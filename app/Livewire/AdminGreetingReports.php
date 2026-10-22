<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GuestGreeting;
use App\Models\GreetingDailyReport;
use App\Models\User;
use Carbon\Carbon;

class AdminGreetingReports extends Component
{
    public $filterStaffId = '';
    public $filterStartDate = '';
    public $filterEndDate = '';

    // For individual print
    public $showPrintModal = false;
    public $printReport = null;

    // For Daily report print
    public $showDailyPrintModal = false;
    public $dailyPrintReport = null;

    public function mount()
    {
        $this->filterStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->filterEndDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function printGuestReport($greetingId)
    {
        $greeting = GuestGreeting::with(['staff', 'table', 'product'])->findOrFail($greetingId);
        
        $this->printReport = [
            'id' => $greeting->id,
            'guest_name' => $greeting->guest_name,
            'staff_name' => $greeting->staff->name ?? 'غير معروف',
            'table' => $greeting->table ? 'طاولة ' . $greeting->table->number : 'بدون طاولة',
            'product' => $greeting->product->name ?? 'تحية',
            'total_greetings' => $greeting->total_greetings,
            'price' => $greeting->price ?? 10.00,
            'total_amount' => $greeting->total_amount,
            'is_closed' => $greeting->is_closed,
            'created_at' => $greeting->created_at->format('Y-m-d H:i'),
            'closed_at' => $greeting->closed_at ? $greeting->closed_at->format('Y-m-d H:i') : '-',
        ];

        $this->showPrintModal = true;
    }

    public function closePrintModal()
    {
        $this->showPrintModal = false;
        $this->printReport = null;
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
        $query = GuestGreeting::query();

        if ($this->filterStaffId) {
            $query->where('staff_id', $this->filterStaffId);
        }

        if ($this->filterStartDate && $this->filterEndDate) {
            $startDate = Carbon::parse($this->filterStartDate)->startOfDay();
            $endDate = Carbon::parse($this->filterEndDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $reports = $query->with(['staff', 'table', 'product'])->latest()->get();

        $totalRevenue = $reports->sum('total_amount');
        $totalGreetings = $reports->sum('total_greetings');

        $staffUsers = User::role('greeting_staff')->get();
        if ($staffUsers->isEmpty()) {
            $staffUsers = User::all();
        }

        // Daily closed reports
        $dailyReportsQuery = GreetingDailyReport::with(['staff', 'manager']);
        if ($this->filterStartDate && $this->filterEndDate) {
            $startDate = Carbon::parse($this->filterStartDate)->startOfDay();
            $endDate = Carbon::parse($this->filterEndDate)->endOfDay();
            $dailyReportsQuery->whereBetween('closed_at', [$startDate, $endDate]);
        }
        if ($this->filterStaffId) {
            $dailyReportsQuery->where('staff_id', $this->filterStaffId);
        }
        $dailyReports = $dailyReportsQuery->latest()->get();

        return view('livewire.admin-greeting-reports', [
            'reports' => $reports,
            'totalRevenue' => $totalRevenue,
            'totalGreetings' => $totalGreetings,
            'staffUsers' => $staffUsers,
            'dailyReports' => $dailyReports,
        ])->layout('layouts.app');
    }
}
