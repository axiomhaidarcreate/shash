<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Table;

class KitchenBoard extends Component
{
    public $lastOrderCount = 0;
    public $lastRushedCount = 0;

    public function mount()
    {
        $this->lastOrderCount = Order::where('status', 'pending')->count();
        $this->lastRushedCount = $this->getRushedOrdersCount();
    }

    public function getRushedOrdersCount()
    {
        return Order::whereIn('status', ['pending', 'prep'])
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

            // Print the receipt (Customer & Cashier) automatically
            $this->dispatch('print-receipt-silent', orderId: $order->id);

            $this->dispatch('order-ready-sound');
        }
    }

    public function reprintKitchen($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            // Find all orders in the same batch to print together
            $batchOrders = Order::where('batch_id', $order->batch_id)->get();
            $orderIds = $batchOrders->pluck('id')->implode(',');
            $this->dispatch('print-kitchen-silent', orderIds: $orderIds);
            session()->flash('success', 'تم إرسال أمر إعادة طباعة كرت المطبخ.');
        }
    }

    public function reprintReceipt($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $this->dispatch('print-receipt-silent', orderId: $order->id);
            session()->flash('success', 'تم إرسال أمر إعادة طباعة الفاتورة.');
        }
    }

    public function render()
    {
        $pendingOrders = Order::with('table', 'user')->where('status', 'pending')->orderBy('created_at', 'asc')->get();
        $prepOrders = Order::with('table', 'user')->where('status', 'prep')->orderBy('created_at', 'asc')->get();

        // Auto print pending orders that have not been printed for the kitchen yet
        $unprintedKitchenOrders = Order::where('status', 'pending')->where('printed_kitchen', false)->get();
        if ($unprintedKitchenOrders->isNotEmpty()) {
            $grouped = $unprintedKitchenOrders->groupBy('batch_id');
            foreach ($grouped as $batchId => $orders) {
                $orderIds = $orders->pluck('id')->implode(',');
                $this->dispatch('print-kitchen-silent', orderIds: $orderIds);
                // Mark as printed immediately to avoid duplicate print dispatches
                Order::whereIn('id', $orders->pluck('id'))->update(['printed_kitchen' => true]);
            }
            // Refresh relation to update the local variables
            $pendingOrders = Order::with('table', 'user')->where('status', 'pending')->orderBy('created_at', 'asc')->get();
        }

        // Detect new orders arriving
        $currentPendingCount = $pendingOrders->count();
        if ($currentPendingCount > $this->lastOrderCount) {
            $this->dispatch('new-order-arrived');
        }
        $this->lastOrderCount = $currentPendingCount;

        // Detect new rushed orders
        $currentRushedCount = $this->getRushedOrdersCount();
        if ($currentRushedCount > $this->lastRushedCount) {
            $this->dispatch('rush-order-alert');
        }
        $this->lastRushedCount = $currentRushedCount;

        return view('livewire.kitchen-board', [
            'pendingOrders' => $pendingOrders,
            'prepOrders' => $prepOrders,
        ])->layout('components.layouts.app');
    }
}
