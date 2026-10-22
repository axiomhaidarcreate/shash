<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GuestGreeting;
use App\Models\GreetingQuota;
use App\Models\GreetingDailyReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GreetingStaffReports extends Component
{
    use WithPagination;

    public $filterStartDate;
    public $filterEndDate;

    // For Daily report print
    public $showDailyPrintModal = false;
    public $dailyPrintReport = null;

    public function mount()
    {
        $this->filterStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->filterEndDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedFilterStartDate()
    {
        $this->resetPage();
    }

    public function updatedFilterEndDate()
    {
        $this->resetPage();
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
        $userId = Auth::id();

        // 1. Current Active Shift Data
        $openingBalance = GreetingQuota::where('staff_id', $userId)
            ->where('is_daily_closed', false)
            ->sum('quantity');
            
        $usedBalance = GuestGreeting::where('staff_id', $userId)
            ->where('is_daily_closed', false)
            ->sum('total_greetings');

        $totalRevenue = GuestGreeting::where('staff_id', $userId)
            ->where('is_daily_closed', false)
            ->sum('total_amount');

        $activeReport = null;
        if ($openingBalance > 0 || $usedBalance > 0) {
            $firstAction = GuestGreeting::where('staff_id', $userId)
                ->where('is_daily_closed', false)
                ->oldest()
                ->first();
                
            $activeReport = [
                'opened_at' => $firstAction ? $firstAction->created_at : now(),
                'opening_balance' => $openingBalance,
                'used_balance' => $usedBalance,
                'remaining_balance' => max(0, $openingBalance - $usedBalance),
                'total_amount' => $totalRevenue,
                'status' => 'active'
            ];
        }

        // 2. Closed Daily Reports History
        $query = GreetingDailyReport::where('staff_id', $userId);

        if ($this->filterStartDate && $this->filterEndDate) {
            $startDate = Carbon::parse($this->filterStartDate)->startOfDay();
            $endDate = Carbon::parse($this->filterEndDate)->endOfDay();
            $query->whereBetween('closed_at', [$startDate, $endDate]);
        }

        $reports = $query->latest('closed_at')->get();

        return view('livewire.greeting-staff-reports', [
            'activeReport' => $activeReport,
            'reports' => $reports,
        ])->layout('layouts.app');
    }
}

