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
            
            $table = Table::find($order->table_id);
            if ($table) {
                $hasActiveOrders = Order::where('table_id', $table->id)
                    ->whereIn('status', ['pending', 'prep', 'ready'])
                    ->exists();
                if (!$hasActiveOrders) {
                    $table->status = 'empty';
                }
                $table->save();
            }

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
            $this->dispatch('print-receipt-silent', orderId: $order->id);
            session()->flash('success', 'تم إرسال أمر طباعة الفاتورة للطلب #' . $order->id);
        }
    }

    public function render()
    {
        $readyOrders = Order::with('table', 'user')->where('status', 'ready')->orderBy('updated_at', 'asc')->get();
        
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
            'occupiedTables' => $occupiedTables,
        ])->layout('components.layouts.app');
    }
}
