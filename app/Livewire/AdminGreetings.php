<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GuestGreeting;
use App\Models\GreetingQuota;
use App\Models\GreetingDailyReport;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class AdminGreetings extends Component
{
    public $staffId = null;
    public $productId = null;
    public $quantity = 10;
    
    public $newGreetingName = '';

    public $editingQuotaId = null;
    public $editQuantity = 0;

    public $filterStartDate;
    public $filterEndDate;

    // For Daily report print
    public $showDailyPrintModal = false;
    public $dailyPrintReport = null;

    public function mount()
    {
        $this->filterStartDate = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->filterEndDate = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    protected function rules()
    {
        return [
            'staffId' => 'nullable|exists:users,id',
            'productId' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:1000',
            'newGreetingName' => 'nullable|string|max:255',
        ];
    }

    public function addQuota()
    {
        $this->validate([
            'staffId' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        // Auto-select first greeting product if none selected
        $productId = $this->productId;
        if (!$productId) {
            $product = Product::where('category', 'التحايا')->first();
            if ($product) {
                $productId = $product->id;
            }
        }

        GreetingQuota::create([
            'staff_id' => $this->staffId,
            'manager_id' => Auth::id(),
            'product_id' => $productId ?? 1,
            'quantity' => $this->quantity,
        ]);

        $this->reset(['quantity']);
        $this->quantity = 10;
        session()->flash('message', 'تم شحن رصيد التحايا للموظف بنجاح.');
    }

    public function addGreetingProduct()
    {
        $this->validate([
            'newGreetingName' => 'required|string|max:255|unique:products,name',
        ], [
            'newGreetingName.required' => 'يرجى إدخال اسم التحية',
            'newGreetingName.unique' => 'اسم التحية موجود مسبقاً',
        ]);

        Product::create([
            'name' => $this->newGreetingName,
            'category' => 'التحايا',
            'is_active' => true,
            'grams_per_order' => 0,
            'sort_order' => 0,
        ]);

        $this->reset('newGreetingName');
        session()->flash('message', 'تمت إضافة منتج التحية الجديد بنجاح.');
    }

    public function editQuota($id)
    {
        $quota = GreetingQuota::where('id', $id)->where('is_daily_closed', false)->first();
        if ($quota) {
            $this->editingQuotaId = $quota->id;
            $this->editQuantity = $quota->quantity;
        }
    }

    public function updateQuota()
    {
        $this->validate([
            'editQuantity' => 'required|integer|min:1|max:1000'
        ]);
        
        $quota = GreetingQuota::where('id', $this->editingQuotaId)->where('is_daily_closed', false)->first();
        if ($quota) {
            $quota->quantity = $this->editQuantity;
            $quota->save();
            session()->flash('message', 'تم تعديل الرصيد بنجاح.');
        }
        $this->cancelEditQuota();
    }

    public function cancelEditQuota()
    {
        $this->editingQuotaId = null;
        $this->editQuantity = 0;
    }

    public function deleteQuota($id)
    {
        $quota = GreetingQuota::where('id', $id)->where('is_daily_closed', false)->first();
        if ($quota) {
            $quota->delete();
            session()->flash('message', 'تم حذف الرصيد بنجاح.');
        }
    }

    /**
     * Get balance details for a specific staff member (only active, non-daily-closed records)
     */
    public function getStaffBalance($staffId)
    {
        $openingBalance = GreetingQuota::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->sum('quantity');

        $usedBalance = GuestGreeting::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->sum('total_greetings');

        $remainingBalance = $openingBalance - $usedBalance;

        return [
            'opening' => $openingBalance,
            'used' => $usedBalance,
            'remaining' => max(0, $remainingBalance),
        ];
    }

    /**
     * Close daily balance for a specific staff member
     */
    public function closeDailyBalance($staffId)
    {
        $balance = $this->getStaffBalance($staffId);
        
        // Get guest details for the report
        $guestDetails = GuestGreeting::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->with(['table', 'product'])
            ->get()
            ->map(function ($greeting) {
                return [
                    'id' => $greeting->id,
                    'guest_name' => $greeting->guest_name,
                    'table' => $greeting->table ? 'طاولة ' . $greeting->table->number : 'بدون طاولة',
                    'product' => $greeting->product->name ?? 'غير محدد',
                    'total_greetings' => $greeting->total_greetings,
                    'price' => $greeting->price,
                    'total_amount' => $greeting->total_amount,
                    'is_closed' => $greeting->is_closed,
                    'created_at' => $greeting->created_at->format('Y-m-d H:i'),
                    'closed_at' => $greeting->closed_at ? $greeting->closed_at->format('Y-m-d H:i') : null,
                ];
            })
            ->toArray();

        $totalAmount = GuestGreeting::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->sum('total_amount');

        // Create the daily report
        GreetingDailyReport::create([
            'manager_id' => Auth::id(),
            'staff_id' => $staffId,
            'opening_balance' => $balance['opening'],
            'used_balance' => $balance['used'],
            'remaining_balance' => $balance['remaining'],
            'total_amount' => $totalAmount,
            'guest_details' => $guestDetails,
            'closed_at' => now(),
        ]);

        // Mark all active quotas as daily closed
        GreetingQuota::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->update(['is_daily_closed' => true]);

        // Mark all guest greetings as daily closed (and close any open ones)
        GuestGreeting::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->update([
                'is_daily_closed' => true,
                'is_closed' => true,
                'closed_at' => now(),
            ]);

        session()->flash('message', 'تم إغلاق الرصيد اليومي للموظف بنجاح وتم إنشاء التقرير.');
    }

    public function printDailyReport($reportId)
    {
        $report = GreetingDailyReport::with(['staff', 'manager'])->findOrFail($reportId);
        
        $this->dailyPrintReport = [
            'id' => $report->id,
            'staff_name' => $report->staff->name ?? 'غير معروف',
            'manager_name' => $report->manager->name ?? 'غير معروف',
            'opening_balance' => $report->opening_balance,
            'used_balance' => $report->used_balance,
            'remaining_balance' => $report->remaining_balance,
            'total_amount' => $report->total_amount,
            'opened_at' => $report->opened_at ? $report->opened_at->format('Y-m-d h:i A') : $report->created_at->format('Y-m-d h:i A'),
            'closed_at' => $report->closed_at ? $report->closed_at->format('Y-m-d h:i A') : $report->created_at->format('Y-m-d h:i A'),
        ];

        $this->showDailyPrintModal = true;
    }

    public function closeDailyPrintModal()
    {
        $this->showDailyPrintModal = false;
        $this->dailyPrintReport = null;
    }

    public function render()
    {
        $staffUsers = User::role('greeting_staff')->get();
        if ($staffUsers->isEmpty()) {
            $staffUsers = User::all();
        }

        // Compute per-staff balances
        $staffBalances = [];
        foreach ($staffUsers as $staff) {
            $staffBalances[$staff->id] = $this->getStaffBalance($staff->id);
        }

        // Admin's own overall balance view (Active Shift / Today)
        $adminOpeningBalance = GreetingQuota::where('is_daily_closed', false)->sum('quantity');
        $adminUsedBalance = GuestGreeting::where('is_daily_closed', false)->sum('total_greetings');
        $adminRemainingBalance = max(0, $adminOpeningBalance - $adminUsedBalance);
        
        $totalRevenueToday = GuestGreeting::where('is_daily_closed', false)->sum('total_amount');

        // Daily reports
        $dailyReportsQuery = GreetingDailyReport::with(['staff', 'manager']);
        if ($this->filterStartDate && $this->filterEndDate) {
            $startDate = \Carbon\Carbon::parse($this->filterStartDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->filterEndDate)->endOfDay();
            $dailyReportsQuery->whereBetween('closed_at', [$startDate, $endDate]);
        }
        $dailyReports = $dailyReportsQuery->latest()->get();

        $todayQuotas = GreetingQuota::with('staff')
            ->where('is_daily_closed', false)
            ->latest()
            ->get();

        return view('livewire.admin-greetings', [
            'staffUsers' => $staffUsers,
            'staffBalances' => $staffBalances,
            'products' => Product::where('category', 'التحايا')->orWhere('name', 'like', '%تحية%')->orWhere('name', 'like', '%Greeting%')->get(),
            'openAccounts' => GuestGreeting::where('is_closed', false)
                ->where('is_daily_closed', false)
                ->where('created_at', '>=', now()->subHours(12))
                ->latest()
                ->get(),
            'totalRevenueToday' => $totalRevenueToday,
            'adminOpeningBalance' => $adminOpeningBalance,
            'adminUsedBalance' => $adminUsedBalance,
            'adminRemainingBalance' => $adminRemainingBalance,
            'dailyReports' => $dailyReports,
            'todayQuotas' => $todayQuotas,
        ])->layout('layouts.app');
    }
}
