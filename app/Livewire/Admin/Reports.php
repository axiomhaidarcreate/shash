<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Order;
use App\Models\Category;
use Carbon\Carbon;

class Reports extends Component
{
    public string $selectedCategory = '';
    public string $startDate        = '';
    public string $endDate          = '';
    public string $startTime        = '';
    public string $endTime          = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        // Default to first category
        $first = Category::orderBy('name')->first();
        $this->selectedCategory = $first ? $first->name : '';
    }

    public function isGramCategory($categoryName): bool
    {
        return in_array($categoryName, ['الشيشة', 'معسل', 'الشيش', 'رؤوس الشيشة', 'رؤوس شيشة', 'رأس شيشة']);
    }

    private function getDateRange(): array
    {
        if ($this->startTime && $this->endTime) {
            $startDate = Carbon::parse($this->startDate . ' ' . $this->startTime);
            $endDate   = Carbon::parse($this->endDate . ' ' . $this->endTime);
            if ($endDate->lte($startDate)) {
                $endDate->addDay();
            }
        } else {
            $startDate = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : Carbon::today()->startOfDay();
            if ($this->endDate) {
                $endDate = Carbon::parse($this->endDate)->endOfDay();
            } elseif ($this->startDate) {
                $endDate = Carbon::parse($this->startDate)->endOfDay();
            } else {
                $endDate = Carbon::today()->endOfDay();
            }
        }
        
        return [$startDate, $endDate];
    }

    private function getTicketReportData($startDate, $endDate): array
    {
        $tickets = \App\Models\Ticket::with('ticketTemplate')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $stats = [
            'total'     => $tickets->count(),
            'active'    => $tickets->where('status', 'active')->count(),
            'used'      => $tickets->where('status', 'used')->count(),
            'cancelled' => $tickets->where('status', 'cancelled')->count(),
        ];

        $templateStats = [];
        $productStats  = [];

        foreach ($tickets as $ticket) {
            $templateName = $ticket->ticketTemplate ? $ticket->ticketTemplate->name : 'أخرى';
            $templateStats[$templateName] = ($templateStats[$templateName] ?? 0) + 1;

            if ($ticket->status !== 'cancelled' && is_array($ticket->selected_products)) {
                foreach ($ticket->selected_products as $product) {
                    $productStats[$product] = ($productStats[$product] ?? 0) + 1;
                }
            }
        }

        return [
            'stats' => $stats,
            'templateStats' => $templateStats,
            'productStats' => $productStats,
        ];
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        [$startDate, $endDate] = $this->getDateRange();

        $isGram = $this->isGramCategory($this->selectedCategory);
        $unit   = $isGram ? 'ج' : 'حبة';

        // Build report rows for the selected category
        $products = Product::where('category', $this->selectedCategory)->orderBy('name')->get();

        $reportRows = $products->map(function ($product) use ($startDate, $endDate, $isGram) {
            if ($this->selectedCategory === 'التحايا') {
                // For Greetings, we only calculate metrics for the specific date period
                // because quotas are daily and don't carry over like physical inventory.
                $quotaQ = \App\Models\GreetingQuota::where('product_id', $product->id)
                    ->where('created_at', '>=', $startDate);
                if ($endDate) $quotaQ->where('created_at', '<', $endDate);
                
                $periodIn = (clone $quotaQ)->sum('quantity');

                // --- Period-filtered sold for Greetings ---
                $soldQ = \App\Models\GuestGreeting::where('product_id', $product->id)
                    ->where('created_at', '>=', $startDate);
                if ($endDate) $soldQ->where('created_at', '<', $endDate);

                $soldQty   = (clone $soldQ)->sum('total_greetings');
                $damaged   = 0;
                $soldGrams = 0;

                $remaining = $periodIn - $soldQty; // Remaining from what was added in this period

            } else {
                // --- Overall remaining (not date-filtered) ---
                $overallIn    = StockEntry::where('product_id', $product->id)->where('type', 'addition')->sum('quantity_grams');
                $overallWaste = StockEntry::where('product_id', $product->id)->where('type', 'waste')->where('status', 'approved')->sum('quantity_grams');
                $overallOut   = Order::where(function ($q) use ($product) {
                        $q->where('shisha_type', $product->name)
                          ->orWhere('shisha_type', $product->name . ' (رأس فقط)')
                          ->orWhere('shisha_type', 'رأس ' . $product->name);
                    })
                    ->whereNotIn('status', ['cancelled'])
                    ->selectRaw('SUM(quantity * grams_per_unit) as total_grams')
                    ->value('total_grams') ?? 0;

                $remaining = $overallIn - $overallOut - $overallWaste;

                // --- Period-filtered sold ---
                $ordersQ = Order::where(function ($q) use ($product) {
                        $q->where('shisha_type', $product->name)
                          ->orWhere('shisha_type', $product->name . ' (رأس فقط)')
                          ->orWhere('shisha_type', 'رأس ' . $product->name);
                    })
                    ->whereNotIn('status', ['cancelled'])
                    ->where('created_at', '>=', $startDate);
                if ($endDate) $ordersQ->where('created_at', '<', $endDate);

                $soldGrams = (clone $ordersQ)->selectRaw('SUM(quantity * grams_per_unit) as total_grams')->value('total_grams') ?? 0;
                $soldQty   = (clone $ordersQ)->sum('quantity');

                // --- Period-filtered waste ---
                $wasteQ = StockEntry::where('product_id', $product->id)
                    ->where('type', 'waste')
                    ->where('status', 'approved')
                    ->where('created_at', '>=', $startDate);
                if ($endDate) $wasteQ->where('created_at', '<', $endDate);
                $damaged = $wasteQ->sum('quantity_grams');
            }

            return [
                'name'      => $product->name,
                'sold'      => $isGram ? $soldGrams : $soldQty,
                'damaged'   => $damaged,
                'remaining' => $remaining,
            ];
        })->filter(fn($r) => $r['sold'] > 0 || $r['damaged'] > 0 || $r['remaining'] > 0);

        // Summary totals
        $totalSold      = $reportRows->sum('sold');
        $totalDamaged   = $reportRows->sum('damaged');
        $totalRemaining = $reportRows->sum('remaining');

        $ticketData = null;
        if (trim($this->selectedCategory) === 'تذاكر الدخول' || trim($this->selectedCategory) === 'تذكرات الدخول') {
            $ticketData = $this->getTicketReportData($startDate, $endDate);
        }

        return view('livewire.admin.reports', [
            'categories'     => $categories,
            'reportRows'     => $reportRows,
            'unit'           => $unit,
            'isGram'         => $isGram,
            'totalSold'      => $totalSold,
            'totalDamaged'   => $totalDamaged,
            'totalRemaining' => $totalRemaining,
            'ticketData'     => $ticketData,
            'startTime'      => $this->startTime,
            'endTime'        => $this->endTime,
        ])->layout('components.layouts.admin');
    }
}
