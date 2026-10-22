<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GuestGreeting;
use App\Models\GreetingDailyReport;
use App\Models\User;

class ReceptionGreetingReports extends Component
{
    public $filterStaffId = '';
    public $filterDate = '';

    // For individual print
    public $showPrintModal = false;
    public $printReport = null;

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

    public function render()
    {
        $query = GuestGreeting::query();

        if ($this->filterStaffId) {
            $query->where('staff_id', $this->filterStaffId);
        }

        if ($this->filterDate) {
            $startDate = \Carbon\Carbon::parse($this->filterDate)->startOfDay();
            $endDate = $startDate->copy()->addDays(6)->endOfDay();
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
        if ($this->filterDate) {
            $startDate = \Carbon\Carbon::parse($this->filterDate)->startOfDay();
            $endDate = $startDate->copy()->addDays(6)->endOfDay();
            $dailyReportsQuery->whereBetween('closed_at', [$startDate, $endDate]);
        }
        if ($this->filterStaffId) {
            $dailyReportsQuery->where('staff_id', $this->filterStaffId);
        }
        $dailyReports = $dailyReportsQuery->latest()->get();

        return view('livewire.reception-greeting-reports', [
            'reports' => $reports,
            'totalRevenue' => $totalRevenue,
            'totalGreetings' => $totalGreetings,
            'staffUsers' => $staffUsers,
            'dailyReports' => $dailyReports,
        ])->layout('layouts.app');
    }
}
