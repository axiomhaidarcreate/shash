<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Table;

class DeliveryBoard extends Component
{
    public $lastReadyCount = 0;

    public function mount()
    {
        $this->lastReadyCount = Order::where('status', 'ready')->count();
    }

    public function markAsDelivered($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->status = 'delivered';
            $order->save();
            
            $order->releaseTableAndChairIfEmpty();

            $this->dispatch('delivery-done-sound');
        }
    }

    public function freeTable($tableId)
    {
        $table = Table::find($tableId);
        if ($table && in_array($table->status, ['delivered', 'customer_seated', 'order_prep', 'order_ready'])) {
            $table->status = 'empty';
            $table->save();
            session()->flash('success', 'تم تحرير الطاولة رقم ' . $table->number);
        }
    }

    public function reprintReceipt($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $printUrl = route('invoice.print', ['order' => $order->id, 'auto' => 1]);
            $this->dispatch('print-invoice', url: $printUrl);
            session()->flash('success', 'تم فتح صفحة طباعة الفاتورة للطلب #' . $order->id);
        }
    }

    public function render()
    {
        $user = auth()->user();
        $assignedKitchens = $user->assigned_kitchens ?? [];

        $query = Order::with('table', 'user')->where('status', 'ready');
        
        if (!empty($assignedKitchens)) {
            $query->whereIn('target_kitchen', $assignedKitchens);
        }
        
        $readyOrders = $query->orderBy('updated_at', 'asc')->get();
        $groupedOrders = $readyOrders->groupBy('target_kitchen');
        
        // Tables that have all their orders delivered, or are occupied
        $occupiedTables = Table::whereNotIn('status', ['empty'])->get();

        // Detect new ready orders
        $currentReadyCount = $readyOrders->count();
        if ($currentReadyCount > $this->lastReadyCount) {
            $this->dispatch('new-ready-order');
        }
        $this->lastReadyCount = $currentReadyCount;

        return view('livewire.delivery-board', [
            'readyOrders' => $readyOrders,
            'groupedOrders' => $groupedOrders,
            'occupiedTables' => $occupiedTables,
        ])->layout('components.layouts.app');
    }
}
