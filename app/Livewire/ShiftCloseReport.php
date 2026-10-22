<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\WorkShift;
use App\Models\Order;
use App\Models\StockEntry;
use App\Models\GuestGreeting;
use App\Models\GreetingDailyReport;
use App\Models\TicketDailyReport;
use App\Models\InventoryDailyReport;
use App\Models\Product;
use Carbon\Carbon;

class ShiftCloseReport extends Component
{
    public bool $show = false;
    public $shift = null;

    // 'all' | 'reception' | 'greetings' | 'inventory'
    public string $section = 'all';

    // Reception data
    public $ticketReports = [];
    public $totalTickets = 0;
    public $totalSold = 0;
    public $totalCancelled = 0;
    public $productsSold = [];

    // Greetings data
    public $greetingReports = [];
    public $totalGreetingsUsed = 0;
    public $totalGreetingsAmount = 0;

    // Inventory data
    public $inventoryReport = null;
    public $wasteByProduct = [];

    public function mount(string $section = 'all')
    {
        $this->section = $section;

        $lastShift = WorkShift::where('status', 'closed')
            ->whereNotNull('closed_at')
            ->latest('closed_at')
            ->first();

        if ($lastShift && $lastShift->closed_at && $lastShift->closed_at->diffInMinutes(now()) <= 15) {
            $this->loadReport($lastShift);
            $this->show = true;
        }
    }

    #[On('shiftWasClosed')]
    public function shiftWasClosed()
    {
        $lastShift = WorkShift::where('status', 'closed')
            ->whereNotNull('closed_at')
            ->latest('closed_at')
            ->first();

        if ($lastShift) {
            $this->loadReport($lastShift);
            $this->show = true;
        }
    }

    public function loadReport($shift)
    {
        $this->shift = $shift;
        $openedAt = $shift->opened_at;
        $closedAt = $shift->closed_at;

        if (in_array($this->section, ['all', 'reception'])) {
            $this->ticketReports = TicketDailyReport::whereBetween('opened_at', [$openedAt, $closedAt])
                ->orWhereBetween('closed_at', [$openedAt, $closedAt])
                ->with('user')
                ->get();

            $this->totalTickets   = $this->ticketReports->sum('total_tickets');
            $this->totalSold      = $this->ticketReports->sum('sold_tickets');
            $this->totalCancelled = $this->ticketReports->sum('cancelled_tickets');

            $ordersInShift = Order::whereNotIn('status', ['cancelled'])
                ->where('created_at', '>=', $openedAt)
                ->where('created_at', '<=', $closedAt)
                ->select('shisha_type', 'quantity')
                ->get();

            $this->productsSold = $ordersInShift
                ->groupBy('shisha_type')
                ->map(fn($g) => $g->sum('quantity'))
                ->sortByDesc(fn($v) => $v)
                ->toArray();
        }

        if (in_array($this->section, ['all', 'greetings'])) {
            $this->greetingReports = GreetingDailyReport::where('opened_at', '>=', $openedAt)
                ->where('closed_at', '<=', $closedAt)
                ->with('staff')
                ->get();

            $this->totalGreetingsUsed   = $this->greetingReports->sum('used_balance');
            $this->totalGreetingsAmount = $this->greetingReports->sum('total_amount');
        }

        if (in_array($this->section, ['all', 'inventory'])) {
            $this->inventoryReport = InventoryDailyReport::where('opened_at', '>=', $openedAt)
                ->where('closed_at', '<=', $closedAt)
                ->latest()
                ->first();

            $wasteEntries = StockEntry::where('type', 'waste')
                ->where('status', 'approved')
                ->where('created_at', '>=', $openedAt)
                ->where('created_at', '<=', $closedAt)
                ->with('product')
                ->get();

            $this->wasteByProduct = $wasteEntries
                ->groupBy(fn($e) => $e->product->name ?? 'غير معروف')
                ->map(fn($g) => $g->sum('quantity_grams'))
                ->sortByDesc(fn($v) => $v)
                ->toArray();
        }
    }

    public function showReport()
    {
        $lastShift = WorkShift::where('status', 'closed')
            ->whereNotNull('closed_at')
            ->latest('closed_at')
            ->first();

        if ($lastShift) {
            $this->loadReport($lastShift);
            $this->show = true;
        }
    }

    public function close()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.shift-close-report');
    }
}
