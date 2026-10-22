<?php

namespace App\Services;

use App\Models\WorkShift;
use App\Models\Setting;
use App\Models\User;
use App\Models\Order;
use App\Models\GuestGreeting;
use App\Models\GreetingQuota;
use App\Models\GreetingDailyReport;
use App\Models\InventoryDailyReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    /**
     * Start a new work shift globally.
     */
    public function startShift($userId = null, $isAuto = false)
    {
        $activeShift = WorkShift::where('status', 'open')->first();
        if ($activeShift) {
            return false; // Shift already active
        }

        DB::transaction(function () use ($userId, $isAuto) {
            $shift = WorkShift::create([
                'status' => 'open',
                'opened_by' => $userId,
                'opened_at' => now(),
                'is_auto_opened' => $isAuto,
            ]);

            $businessDate = now()->subHours(8)->toDateString();
            Setting::updateOrCreate(
                ['key' => 'last_shift_business_date'],
                ['value' => $businessDate]
            );

            // For Inventory, we can optionally snapshot opening balances here
            // For now, inventory is based on consumption when closed
        });

        return true;
    }

    /**
     * Close the current active work shift globally.
     */
    public function closeShift($userId = null, $isAuto = false)
    {
        $activeShift = WorkShift::where('status', 'open')->first();
        if (!$activeShift) {
            return false; // No active shift to close
        }

        DB::transaction(function () use ($activeShift, $userId, $isAuto) {
            // 1. Close Greetings for all staff
            try {
                $greetingStaff = \Spatie\Permission\Models\Role::where('name', 'greeting_staff')->exists()
                    ? User::role('greeting_staff')->get()
                    : collect();
            } catch (\Exception $e) {
                $greetingStaff = collect();
            }

            foreach ($greetingStaff as $staff) {
                // Determine opening balance (what they had at shift start + what was added)
                $openingBalance = GreetingQuota::where('staff_id', $staff->id)
                    ->where('is_daily_closed', false)
                    ->sum('quantity');

                $usedBalance = GuestGreeting::where('staff_id', $staff->id)
                    ->where('is_daily_closed', false)
                    ->sum('total_greetings');

                $totalAmount = GuestGreeting::where('staff_id', $staff->id)
                    ->where('is_daily_closed', false)
                    ->sum('total_amount');

                if ($openingBalance > 0 || $usedBalance > 0) {
                    // Close the open greeting accounts for this staff
                    GuestGreeting::where('staff_id', $staff->id)
                        ->where('is_daily_closed', false)
                        ->update([
                            'is_closed' => true,
                            'closed_at' => now(),
                            'is_daily_closed' => true,
                        ]);

                    GreetingQuota::where('staff_id', $staff->id)
                        ->where('is_daily_closed', false)
                        ->update(['is_daily_closed' => true]);

                    GreetingDailyReport::create([
                        'manager_id' => $userId, // or null if auto
                        'staff_id' => $staff->id,
                        'opening_balance' => $openingBalance,
                        'used_balance' => $usedBalance,
                        'remaining_balance' => max(0, $openingBalance - $usedBalance),
                        'total_amount' => $totalAmount,
                        'guest_details' => [], // Keeping it simple for automated closure
                        'opened_at' => $activeShift->opened_at,
                        'closed_at' => now(),
                    ]);
                }
            }

            // 2. Close Reception (Tickets) - Reset counters and generate report
            $unclosedTickets = \App\Models\Ticket::where('is_daily_closed', false)->get();
            $ticketsByUser = $unclosedTickets->groupBy('user_id');

            foreach ($ticketsByUser as $userId => $tickets) {
                $totalTickets = $tickets->count();
                $cancelledTickets = $tickets->where('status', 'cancelled')->count();
                $soldTickets = $totalTickets - $cancelledTickets;

                \App\Models\TicketDailyReport::create([
                    'user_id' => $userId,
                    'total_tickets' => $totalTickets,
                    'sold_tickets' => $soldTickets,
                    'cancelled_tickets' => $cancelledTickets,
                    'opened_at' => $activeShift->opened_at,
                    'closed_at' => now(),
                ]);
            }

            if ($unclosedTickets->isNotEmpty()) {
                // تعليم كل التذاكر كمغلقة يومياً لتصفير العدادات في الشفت الجديد
                \App\Models\Ticket::whereIn('id', $unclosedTickets->pluck('id'))
                    ->update(['is_daily_closed' => true]);
            }
            // 3. Close Inventory - Create one daily snapshot report
            // Calculate actual numbers from today's data
            $today = now()->toDateString();
            $totalStockIn = \App\Models\StockEntry::where('type', 'addition')
                ->where('created_at', '>=', $activeShift->opened_at)
                ->sum('quantity_grams');
            $totalWaste = \App\Models\StockEntry::where('type', 'waste')
                ->where('status', 'approved')
                ->where('created_at', '>=', $activeShift->opened_at)
                ->sum('quantity_grams');
            $totalConsumed = \App\Models\Order::whereNotIn('status', ['cancelled'])
                ->where('created_at', '>=', $activeShift->opened_at)
                ->sum('quantity');

            // Use the manager (closer) user id for the report
            InventoryDailyReport::create([
                'user_id' => $userId ?? User::first()?->id ?? 1,
                'total_stock_in' => $totalStockIn,
                'total_consumed' => $totalConsumed,
                'total_waste' => $totalWaste,
                'opened_at' => $activeShift->opened_at,
                'closed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Finally, close the shift
            $activeShift->update([
                'status'       => 'closed',
                'closed_by'    => $userId,
                'closed_at'    => now(),
                'is_auto_closed' => $isAuto,
            ]);
        });

        return true;
    }
}
