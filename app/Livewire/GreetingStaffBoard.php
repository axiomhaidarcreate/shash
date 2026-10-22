<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GuestGreeting;
use App\Models\GreetingQuota;
use App\Models\Table;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class GreetingStaffBoard extends Component
{
    public $guestName = '';
    public $tableId = null;
    public $initialQuantity = 1;
    public $greetingPrice = null; // Custom price, null = use default product price
    public $tableSearch = '';

    // For adding greetings to existing accounts
    public $addQuantity = [];
    public $addPrice = [];

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
            'guestName' => 'required|string|max:255',
            'tableId' => 'nullable|exists:tables,id',
            'initialQuantity' => 'required|integer|min:1|max:100',
            'greetingPrice' => 'nullable|numeric|min:0',
        ];
    }

    public function openAccount()
    {
        $this->validate();

        if ($this->getRemainingBalance() <= 0) {
            $this->addError('balance', 'عذراً، لا يوجد لديك رصيد تحايا كافٍ. يرجى طلب رصيد من المدير.');
            return;
        }

        // Auto-select the first greeting product
        $product = Product::where('category', 'التحايا')->first();
        if (!$product) {
            $product = Product::where('name', 'like', '%تحية%')->orWhere('name', 'like', '%Greeting%')->first();
        }

        if (!$product) {
            $this->addError('balance', 'لا يوجد منتج تحايا مسجل. يرجى التواصل مع المدير.');
            return;
        }

        // Use custom price or default product price
        $price = $this->greetingPrice ?? ($product->price ?? 10.00);
        $totalAmount = $this->initialQuantity * $price;

        GuestGreeting::create([
            'staff_id' => Auth::id(),
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
        if ($this->getRemainingBalance() <= 0) {
            session()->flash('error', 'لا يوجد رصيد كافٍ.');
            return;
        }

        $greeting = GuestGreeting::findOrFail($greetingId);
        
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

            // Reset the add inputs
            $this->addQuantity[$greetingId] = null;
            $this->addPrice[$greetingId] = null;
        }
    }

    public function printDailyReport($reportId)
    {
        $report = \App\Models\GreetingDailyReport::with(['staff', 'manager'])->findOrFail($reportId);
        
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

    public function getOpeningBalance()
    {
        $userId = Auth::id();
        return GreetingQuota::where('staff_id', $userId)
            ->where('is_daily_closed', false)
            ->sum('quantity');
    }

    public function getUsedBalance()
    {
        $userId = Auth::id();
        return GuestGreeting::where('staff_id', $userId)
            ->where('is_daily_closed', false)
            ->sum('total_greetings');
    }

    public function getRemainingBalance()
    {
        return max(0, $this->getOpeningBalance() - $this->getUsedBalance());
    }

    public function render()
    {
        // Filter tables by search
        $tablesQuery = Table::query();
        if (!empty($this->tableSearch)) {
            $tablesQuery->where(function ($q) {
                $q->where('number', 'like', '%' . $this->tableSearch . '%')
                  ->orWhere('section', 'like', '%' . $this->tableSearch . '%');
            });
        }

        // Fetch Daily Reports History
        $dailyReportsQuery = \App\Models\GreetingDailyReport::where('staff_id', Auth::id());
        if ($this->filterStartDate && $this->filterEndDate) {
            $startDate = \Carbon\Carbon::parse($this->filterStartDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->filterEndDate)->endOfDay();
            $dailyReportsQuery->whereBetween('closed_at', [$startDate, $endDate]);
        }
        $dailyReports = $dailyReportsQuery->latest('closed_at')->get();

        return view('livewire.greeting-staff-board', [
            'tables' => $tablesQuery->get(),
            'openAccounts' => GuestGreeting::where('staff_id', Auth::id())
                ->where('is_closed', false)
                ->where('is_daily_closed', false)
                ->latest()
                ->get(),
            'openingBalance' => $this->getOpeningBalance(),
            'usedBalance' => $this->getUsedBalance(),
            'remainingBalance' => $this->getRemainingBalance(),
            'dailyReports' => $dailyReports,
        ])->layout('layouts.app');
    }
}
