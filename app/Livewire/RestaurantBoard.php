<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Table;
use Carbon\Carbon;

class RestaurantBoard extends Component
{
    public $lastOrderCount = 0;
    public $lastRushedCount = 0;
    
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterTimeFrom = '';
    public $filterTimeTo = '';

    public $showReportModal = false;
    public $monthlyReportStartDate = '';
    public $monthlyReportEndDate = '';

    public function mount()
    {
        $this->filterDateFrom = now()->format('Y-m-d');
        $this->filterDateTo = now()->format('Y-m-d');
        $this->filterTimeFrom = '00:00';
        $this->filterTimeTo = '23:59';
        $this->lastOrderCount = Order::where('status', 'pending')
            ->where('target_kitchen', 'restaurant')
            ->where(function($q) {
                $q->whereNull('manager_approval_status')->orWhere('manager_approval_status', 'approved');
            })->count();
        $this->lastRushedCount = $this->getRushedOrdersCount();
        
        $this->monthlyReportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->monthlyReportEndDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function getRushedOrdersCount()
    {
        return Order::whereIn('status', ['pending', 'prep'])
            ->where('target_kitchen', 'restaurant')
            ->where(function($query) {
                $query->whereNull('manager_approval_status')->orWhere('manager_approval_status', 'approved');
            })
            ->where(function($q) {
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
            if ($table) {
                $table->status = 'order_prep';
                $table->save();
            }

            // Print receipt via Browser Popup
            $printUrl = route('invoice.print', ['order' => $order->id, 'auto' => 1]);
            $this->dispatch('print-invoice', url: $printUrl);

            // Print to thermal printer via Agent
            $printerService = app(\App\Services\PrinterService::class);
            $printerService->printOrder($order, 'kitchen');
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
            if ($table) {
                $table->status = 'order_ready';
                $table->save();
            }

            // Print receipt via Browser Popup
            $printUrl = route('invoice.print', ['order' => $order->id, 'auto' => 1]);
            $this->dispatch('print-invoice', url: $printUrl);

            // Print to thermal printer via Agent
            $printerService = app(\App\Services\PrinterService::class);
            $printerService->printOrder($order, 'kitchen');

            $this->dispatch('order-ready-sound');
        }
    }

    public function reprintKitchen($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            // Print kitchen batch via Browser Popup
            $batchOrderIds = Order::where('batch_id', $order->batch_id)->pluck('id')->toArray();
            $printUrl = route('invoice.kitchenPrintBatch', ['ids' => implode(',', $batchOrderIds), 'auto' => 1]);
            $this->dispatch('print-invoice', url: $printUrl);
            session()->flash('success', 'تم فتح صفحة طباعة كرت المطبخ.');
        }
    }

    public function reprintReceipt($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $printUrl = route('invoice.print', ['order' => $order->id, 'auto' => 1]);
            $this->dispatch('print-invoice', url: $printUrl);
            session()->flash('success', 'تم فتح صفحة طباعة الفاتورة.');
        }
    }

    public function cancelOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->status = 'cancelled';
            $order->save();

            $order->releaseTableAndChairIfEmpty();

            session()->flash('success', 'تم إلغاء الطلب بنجاح ولن يُحسب في التقارير أو المخزون.');
        }
    }

    public function checkNewOrders()
    {
        $currentPendingCount = Order::where('status', 'pending')
            ->where('target_kitchen', 'restaurant')
            ->where(function($q) {
                $q->whereNull('manager_approval_status')->orWhere('manager_approval_status', 'approved');
            })->count();

        if ($currentPendingCount > $this->lastOrderCount) {
            $this->dispatch('new-order-arrived');
        }
        $this->lastOrderCount = $currentPendingCount;

        $currentRushedCount = $this->getRushedOrdersCount();
        if ($currentRushedCount > $this->lastRushedCount) {
            $this->dispatch('rush-order-alert');
        }
        $this->lastRushedCount = $currentRushedCount;
    }

    public function openReportModal()
    {
        $this->showReportModal = true;
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
    }

    public function render()
    {
        $pendingOrders = Order::with('table', 'user')->where('status', 'pending')
            ->where('target_kitchen', 'restaurant')
            ->where(function($q) {
                $q->whereNull('manager_approval_status')->orWhere('manager_approval_status', 'approved');
            })
            ->orderBy('created_at', 'asc')->get();

        $prepOrders = Order::with('table', 'user')->where('status', 'prep')
            ->where('target_kitchen', 'restaurant')
            ->where(function($q) {
                $q->whereNull('manager_approval_status')->orWhere('manager_approval_status', 'approved');
            })
            ->orderBy('created_at', 'asc')->get();
        $dailyStats = [];
        $monthlyStats = [];

        if ($this->showReportModal) {
            $productCategories = \App\Models\Product::pluck('category', 'name')->toArray();
            
            $activeShift = \App\Models\WorkShift::where('status', 'open')->first();
            
            // Daily Stats
            if ($activeShift) {
                $dailyOrders = Order::whereBetween('created_at', [$activeShift->created_at, now()])->where('target_kitchen', 'restaurant')->get();
            } else {
                $dailyOrders = collect();
            }
            foreach ($dailyOrders as $order) {
                $category = $productCategories[$order->shisha_type] ?? 'أخرى';
                
                if (!isset($dailyStats[$category])) {
                    $dailyStats[$category] = ['total_sold' => 0, 'total_cancelled' => 0, 'products' => []];
                }
                if (!isset($dailyStats[$category]['products'][$order->shisha_type])) {
                    $dailyStats[$category]['products'][$order->shisha_type] = ['sold' => 0, 'cancelled' => 0];
                }
                
                if ($order->status === 'cancelled') {
                    $dailyStats[$category]['total_cancelled'] += $order->quantity;
                    $dailyStats[$category]['products'][$order->shisha_type]['cancelled'] += $order->quantity;
                } else {
                    $dailyStats[$category]['total_sold'] += $order->quantity;
                    $dailyStats[$category]['products'][$order->shisha_type]['sold'] += $order->quantity;
                }
            }

            // Monthly/Range Stats
            $mStart = $this->monthlyReportStartDate ? Carbon::parse($this->monthlyReportStartDate)->startOfDay() : now()->startOfMonth();
            $mEnd = $this->monthlyReportEndDate ? Carbon::parse($this->monthlyReportEndDate)->endOfDay() : now()->endOfMonth();
            
            $monthlyOrders = Order::whereBetween('created_at', [$mStart, $mEnd])->where('target_kitchen', 'restaurant')->get();
            foreach ($monthlyOrders as $order) {
                $category = $productCategories[$order->shisha_type] ?? 'أخرى';
                $date = $order->created_at->format('Y-m-d');
                
                if (!isset($monthlyStats[$date])) {
                    $monthlyStats[$date] = ['categories' => [], 'total_sold' => 0, 'total_cancelled' => 0];
                }
                
                if (!isset($monthlyStats[$date]['categories'][$category])) {
                    $monthlyStats[$date]['categories'][$category] = ['sold' => 0, 'cancelled' => 0];
                }
                
                if ($order->status === 'cancelled') {
                    $monthlyStats[$date]['categories'][$category]['cancelled'] += $order->quantity;
                    $monthlyStats[$date]['total_cancelled'] += $order->quantity;
                } else {
                    $monthlyStats[$date]['categories'][$category]['sold'] += $order->quantity;
                    $monthlyStats[$date]['total_sold'] += $order->quantity;
                }
            }
            krsort($monthlyStats);
        }
            
        return view('livewire.restaurant-board', [
            'pendingOrders' => $pendingOrders,
            'prepOrders' => $prepOrders,
            'filterDateFrom' => $this->filterDateFrom,
            'filterDateTo' => $this->filterDateTo,
            'filterTimeFrom' => $this->filterTimeFrom,
            'filterTimeTo' => $this->filterTimeTo,
            'dailyStats' => $dailyStats,
            'monthlyStats' => $monthlyStats,
        ])->layout('components.layouts.app');
    }
}
