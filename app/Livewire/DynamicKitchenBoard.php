<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Table;
use App\Models\Kitchen;
use Carbon\Carbon;

class DynamicKitchenBoard extends Component
{
    public string $kitchenSlug = '';
    public string $kitchenName = '';

    public $lastOrderCount = 0;
    public $lastRushedCount = 0;

    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterTimeFrom = '';
    public $filterTimeTo = '';

    public $showReportModal = false;
    public $monthlyReportStartDate = '';
    public $monthlyReportEndDate = '';

    public function mount(string $slug)
    {
        $kitchen = Kitchen::where('slug', $slug)->firstOrFail();
        $this->kitchenSlug = $kitchen->slug;
        $this->kitchenName = $kitchen->name;

        $this->filterDateFrom = now()->format('Y-m-d');
        $this->filterDateTo   = now()->format('Y-m-d');
        $this->filterTimeFrom = '00:00';
        $this->filterTimeTo   = '23:59';

        $this->lastOrderCount  = $this->getPendingCount();
        $this->lastRushedCount = $this->getRushedOrdersCount();

        $this->monthlyReportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->monthlyReportEndDate   = now()->endOfMonth()->format('Y-m-d');
    }

    private function getPendingCount(): int
    {
        return Order::where('status', 'pending')
            ->where('target_kitchen', $this->kitchenSlug)
            ->where(function ($q) {
                $q->whereNull('manager_approval_status')->orWhere('manager_approval_status', 'approved');
            })->count();
    }

    public function getRushedOrdersCount(): int
    {
        return Order::whereIn('status', ['pending', 'prep'])
            ->where('target_kitchen', $this->kitchenSlug)
            ->where(function ($q) {
                $q->whereNull('manager_approval_status')->orWhere('manager_approval_status', 'approved');
            })
            ->where(function ($q) {
                $q->where('is_rushed', true)
                  ->orWhere('created_at', '<=', now()->subMinutes(7));
            })->count();
    }

    public function startPrep($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->status = 'prep';
            $order->save();
            $table = Table::find($order->table_id);
            if ($table) { $table->status = 'order_prep'; $table->save(); }
            $this->dispatch('print-invoice', url: route('invoice.print', ['order' => $order->id, 'auto' => 1]));
        }
    }

    public function markAsReady($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->status = 'ready';
            $order->printed_receipt = true;
            $order->save();
            $table = Table::find($order->table_id);
            if ($table) { $table->status = 'order_ready'; $table->save(); }

            // إشعار العميل أن الطلب جاهز
            if ($order->customer_id) {
                \App\Models\CustomerNotification::create([
                    'customer_id' => $order->customer_id,
                    'title' => 'طلبك جاهز! 🎉',
                    'message' => 'طلبك #' . $order->id . ' (' . $order->shisha_type . ') جاهز للتسليم.',
                    'type' => 'order',
                ]);
            }

            $this->dispatch('print-invoice', url: route('invoice.print', ['order' => $order->id, 'auto' => 1]));
            $this->dispatch('order-ready-sound');
        }
    }

    public function reprintKitchen($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $batchOrderIds = Order::where('batch_id', $order->batch_id)->pluck('id')->toArray();
            $printUrl = route('invoice.kitchenPrintBatch', ['ids' => implode(',', $batchOrderIds), 'auto' => 1]);
            $this->dispatch('print-invoice', url: $printUrl);
        }
    }

    public function cancelOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->status = 'cancelled';
            $order->save();
            $order->releaseTableAndChairIfEmpty();          session()->flash('success', 'تم إلغاء الطلب بنجاح.');
        }
    }

    public function checkNewOrders()
    {
        $currentPending = $this->getPendingCount();
        if ($currentPending > $this->lastOrderCount) {
            $this->dispatch('new-order-arrived');
        }
        $this->lastOrderCount = $currentPending;

        $currentRushed = $this->getRushedOrdersCount();
        if ($currentRushed > $this->lastRushedCount) {
            $this->dispatch('rush-order-alert');
        }
        $this->lastRushedCount = $currentRushed;
    }

    public function openReportModal()  { $this->showReportModal = true; }
    public function closeReportModal() { $this->showReportModal = false; }

    public function render()
    {
        $slug = $this->kitchenSlug;

        $pendingOrders = Order::with('table', 'user')
            ->where('status', 'pending')
            ->where('target_kitchen', $slug)
            ->where(function ($q) {
                $q->whereNull('manager_approval_status')->orWhere('manager_approval_status', 'approved');
            })->orderBy('created_at')->get();

        $prepOrders = Order::with('table', 'user')
            ->where('status', 'prep')
            ->where('target_kitchen', $slug)
            ->where(function ($q) {
                $q->whereNull('manager_approval_status')->orWhere('manager_approval_status', 'approved');
            })->orderBy('created_at')->get();

        $dailyStats   = [];
        $monthlyStats = [];

        if ($this->showReportModal) {
            $productCategories = \App\Models\Product::pluck('category', 'name')->toArray();
            $activeShift = \App\Models\WorkShift::where('status', 'open')->first();

            // Daily
            $dailyOrders = $activeShift
                ? Order::whereBetween('created_at', [$activeShift->created_at, now()])
                    ->where('target_kitchen', $slug)->get()
                : collect();

            foreach ($dailyOrders as $order) {
                $cat = $productCategories[$order->shisha_type] ?? 'أخرى';
                if (!isset($dailyStats[$cat])) {
                    $dailyStats[$cat] = ['total_sold' => 0, 'total_cancelled' => 0, 'products' => []];
                }
                if (!isset($dailyStats[$cat]['products'][$order->shisha_type])) {
                    $dailyStats[$cat]['products'][$order->shisha_type] = ['sold' => 0, 'cancelled' => 0];
                }
                if ($order->status === 'cancelled') {
                    $dailyStats[$cat]['total_cancelled'] += $order->quantity;
                    $dailyStats[$cat]['products'][$order->shisha_type]['cancelled'] += $order->quantity;
                } else {
                    $dailyStats[$cat]['total_sold'] += $order->quantity;
                    $dailyStats[$cat]['products'][$order->shisha_type]['sold'] += $order->quantity;
                }
            }

            // Monthly
            $mStart = $this->monthlyReportStartDate ? Carbon::parse($this->monthlyReportStartDate)->startOfDay() : now()->startOfMonth();
            $mEnd   = $this->monthlyReportEndDate   ? Carbon::parse($this->monthlyReportEndDate)->endOfDay()   : now()->endOfMonth();

            $monthlyOrders = Order::whereBetween('created_at', [$mStart, $mEnd])
                ->where('target_kitchen', $slug)->get();

            foreach ($monthlyOrders as $order) {
                $cat  = $productCategories[$order->shisha_type] ?? 'أخرى';
                $date = $order->created_at->format('Y-m-d');
                if (!isset($monthlyStats[$date])) {
                    $monthlyStats[$date] = ['categories' => [], 'total_sold' => 0, 'total_cancelled' => 0];
                }
                if (!isset($monthlyStats[$date]['categories'][$cat])) {
                    $monthlyStats[$date]['categories'][$cat] = ['sold' => 0, 'cancelled' => 0];
                }
                if ($order->status === 'cancelled') {
                    $monthlyStats[$date]['categories'][$cat]['cancelled'] += $order->quantity;
                    $monthlyStats[$date]['total_cancelled'] += $order->quantity;
                } else {
                    $monthlyStats[$date]['categories'][$cat]['sold'] += $order->quantity;
                    $monthlyStats[$date]['total_sold'] += $order->quantity;
                }
            }
            krsort($monthlyStats);
        }

        return view('livewire.dynamic-kitchen-board', [
            'pendingOrders' => $pendingOrders,
            'prepOrders'    => $prepOrders,
            'dailyStats'    => $dailyStats,
            'monthlyStats'  => $monthlyStats,
        ])->layout('components.layouts.app');
    }
}
