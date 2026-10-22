<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GuestGreeting;
use App\Models\GreetingQuota;
use App\Models\GreetingDailyReport;
use App\Models\Table;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReceptionGreetingBoard extends Component
{
    public $guestName = '';
    public $tableId = null;
    public $initialQuantity = 1;
    public $greetingPrice = null;
    public $tableSearch = '';
    public $staffId = null; // Reception can act on behalf of a staff

    // For adding greetings to existing accounts
    public $addQuantity = [];
    public $addPrice = [];

    // Add Quota
    public $quotaStaffId = '';
    public $quotaQuantity = '';

    // Close account report modal
    public $showCloseReport = false;
    public $closedAccountReport = null;

    // PIN Verification
    public $showPinModal = false;
    public $enteredPin = '';

    protected function rules()
    {
        return [
            'guestName' => 'required|string|max:255',
            'tableId' => 'nullable|exists:tables,id',
            'initialQuantity' => 'required|integer|min:1|max:100',
            'greetingPrice' => 'nullable|numeric|min:0',
            'quotaStaffId' => 'nullable|exists:users,id',
            'quotaQuantity' => 'nullable|integer|min:1',
        ];
    }

    public function openPinModalForQuota()
    {
        $this->validate([
            'quotaStaffId' => 'required|exists:users,id',
            'quotaQuantity' => 'required|integer|min:1',
        ]);
        $this->enteredPin = '';
        $this->showPinModal = true;
    }

    public function closePinModal()
    {
        $this->showPinModal = false;
        $this->enteredPin = '';
    }

    public function verifyPinAndAddQuota()
    {
        $managerPin = \App\Models\Setting::where('key', 'manager_pin')->value('value') ?? env('MANAGER_PIN', '7925');

        if ($this->enteredPin === $managerPin) {
            $this->executeAddQuota();
            $this->closePinModal();
        } else {
            $this->enteredPin = '';
            session()->flash('error', 'رمز الدخول غير صحيح!');
        }
    }

    public function executeAddQuota()
    {
        $product = Product::where('category', 'التحايا')->first();
        if (!$product) {
            $product = Product::where('name', 'like', '%تحية%')->orWhere('name', 'like', '%Greeting%')->first();
        }

        GreetingQuota::create([
            'staff_id' => $this->quotaStaffId,
            'manager_id' => Auth::id(), // Receptionist adding the quota
            'product_id' => $product ? $product->id : 1,
            'quantity' => $this->quotaQuantity,
            'is_daily_closed' => false,
        ]);

        $this->reset(['quotaStaffId', 'quotaQuantity']);
        session()->flash('message', 'تم شحن الرصيد للموظف بنجاح.');
    }

    public function openAccount()
    {
        $this->validate();

        // Get the acting staff ID (could be the receptionist acting as greeting staff)
        $actingStaffId = $this->staffId ?? Auth::id();

        // Check remaining balance for the acting staff
        if ($this->getRemainingBalanceFor($actingStaffId) <= 0) {
            $this->addError('balance', 'عذراً، لا يوجد رصيد تحايا كافٍ.');
            return;
        }

        $product = Product::where('category', 'التحايا')->first();
        if (!$product) {
            $product = Product::where('name', 'like', '%تحية%')->orWhere('name', 'like', '%Greeting%')->first();
        }
        if (!$product) {
            $this->addError('balance', 'لا يوجد منتج تحايا مسجل.');
            return;
        }

        $price = $this->greetingPrice ?? ($product->price ?? 10.00);
        $totalAmount = $this->initialQuantity * $price;

        GuestGreeting::create([
            'staff_id' => $actingStaffId,
            'guest_name' => $this->guestName,
            'table_id' => $this->tableId,
            'product_id' => $product->id,
            'total_greetings' => $this->initialQuantity,
            'price' => $price,
            'total_amount' => $totalAmount,
            'is_closed' => false,
        ]);

        $this->reset(['guestName', 'tableId', 'initialQuantity', 'greetingPrice', 'tableSearch']);
        $this->initialQuantity = 1;
        session()->flash('message', 'تم فتح حساب جديد وإضافة تحية بنجاح.');
    }

    public function addGreeting($greetingId)
    {
        $greeting = GuestGreeting::findOrFail($greetingId);

        if ($this->getRemainingBalanceFor($greeting->staff_id) <= 0) {
            session()->flash('error', 'لا يوجد رصيد كافٍ.');
            return;
        }

        if (!$greeting->is_closed) {
            $qty = isset($this->addQuantity[$greetingId]) && $this->addQuantity[$greetingId] > 0
                ? (int) $this->addQuantity[$greetingId]
                : 1;

            $customPrice = isset($this->addPrice[$greetingId]) && $this->addPrice[$greetingId] > 0
                ? (float) $this->addPrice[$greetingId]
                : null;

            if ($customPrice !== null) {
                $greeting->price = $customPrice;
            }

            $greeting->total_greetings += $qty;
            $pricePerUnit = $greeting->price ?? 10.00;
            $greeting->total_amount = $greeting->total_greetings * $pricePerUnit;
            $greeting->save();

            $this->addQuantity[$greetingId] = null;
            $this->addPrice[$greetingId] = null;
        }
    }

    /**
     * Close an account and generate printable report
     */
    public function closeAccount($greetingId)
    {
        $greeting = GuestGreeting::with(['table', 'product', 'staff'])->findOrFail($greetingId);
        
        $greeting->update([
            'is_closed' => true,
            'closed_at' => now(),
        ]);

        // Build the close report for printing
        $this->closedAccountReport = [
            'id' => $greeting->id,
            'guest_name' => $greeting->guest_name,
            'staff_name' => $greeting->staff->name ?? 'غير معروف',
            'table' => $greeting->table ? 'طاولة ' . $greeting->table->number : 'بدون طاولة',
            'product' => $greeting->product->name ?? 'تحية',
            'total_greetings' => $greeting->total_greetings,
            'price' => $greeting->price ?? 10.00,
            'total_amount' => $greeting->total_amount,
            'created_at' => $greeting->created_at->format('Y-m-d H:i'),
            'closed_at' => now()->format('Y-m-d H:i'),
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i:s'),
        ];

        $this->showCloseReport = true;
        session()->flash('message', 'تم إغلاق حساب الضيف بنجاح.');
        $this->dispatch('trigger-print');
    }

    public function closeReportModal()
    {
        $this->showCloseReport = false;
        $this->closedAccountReport = null;
    }

    public function getRemainingBalanceFor($staffId)
    {
        $totalGiven = GreetingQuota::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->sum('quantity');
        $totalUsed = GuestGreeting::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->sum('total_greetings');
        return max(0, $totalGiven - $totalUsed);
    }

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

    public function closeDailyBalance($staffId)
    {
        $balance = $this->getStaffBalance($staffId);
        
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

        GreetingQuota::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->update(['is_daily_closed' => true]);

        GuestGreeting::where('staff_id', $staffId)
            ->where('is_daily_closed', false)
            ->update([
                'is_daily_closed' => true,
                'is_closed' => true,
                'closed_at' => now(),
            ]);

        session()->flash('message', 'تم إغلاق الرصيد اليومي للموظف بنجاح وتم إنشاء التقرير.');
    }

    public function render()
    {
        $tablesQuery = Table::query();
        if (!empty($this->tableSearch)) {
            $tablesQuery->where(function ($q) {
                $q->where('number', 'like', '%' . $this->tableSearch . '%')
                  ->orWhere('section', 'like', '%' . $this->tableSearch . '%');
            });
        }

        // Get all greeting staff users
        $greetingStaff = User::role('greeting_staff')->get();
        if ($greetingStaff->isEmpty()) {
            $greetingStaff = User::all();
        }

        // Get all open accounts across all staff (reception can see all)
        $openAccounts = GuestGreeting::where('is_closed', false)
            ->where('is_daily_closed', false)
            ->with(['table', 'product', 'staff'])
            ->latest()
            ->get();

        $staffBalances = [];
        foreach ($greetingStaff as $staff) {
            $staffBalances[$staff->id] = $this->getStaffBalance($staff->id);
        }

        // Global Balances
        $openingBalance = GreetingQuota::where('is_daily_closed', false)->sum('quantity');
        $usedBalance = GuestGreeting::where('is_daily_closed', false)->sum('total_greetings');
        $remainingBalance = max(0, $openingBalance - $usedBalance);

        $activeShift = \App\Models\WorkShift::where('status', 'open')->first();
        $shiftStart = $activeShift ? $activeShift->opened_at : now()->startOfDay();

        // Get closed accounts for current shift
        $closedAccounts = GuestGreeting::where('is_closed', true)
            ->where('closed_at', '>=', $shiftStart)
            ->with(['table', 'product', 'staff'])
            ->latest('closed_at')
            ->get();

        // Get daily closure reports for current shift
        $dailyReports = GreetingDailyReport::with(['staff', 'manager'])
            ->where('closed_at', '>=', $shiftStart)
            ->latest()
            ->get();

        return view('livewire.reception-greeting-board', [
            'tables' => $tablesQuery->get(),
            'greetingStaff' => $greetingStaff,
            'openAccounts' => $openAccounts,
            'staffBalances' => $staffBalances,
            'openingBalance' => $openingBalance,
            'usedBalance' => $usedBalance,
            'remainingBalance' => $remainingBalance,
            'closedAccounts' => $closedAccounts,
            'dailyReports' => $dailyReports,
        ])->layout('components.layouts.app');
    }
}
