<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Table;
use App\Models\Order;
use App\Models\Product;
use App\Models\Section;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class OrderStaffBoard extends Component
{
    public $tableSearch = '';
    public $selectedTable = null;
    public $guestsCount = 1;
    public $orderItems = [];

    public $editingOrderId = null;
    public $printKitchenReceipt = true;

    // Table section filtering state
    public $selectedSection = '';

    public function mount()
    {
        $this->initOrderItems(1);
        
        // Default to all sections
        $this->selectedSection = '';
    }

    public function initOrderItems($count = 1)
    {
        $this->guestsCount = $count;
        $this->orderItems = [];
        for ($i = 0; $i < $count; $i++) {
            $this->orderItems[] = $this->getEmptyItem();
        }
    }

    public function getEmptyItem()
    {
        return [
            'shishaType' => '',
            'quantity' => 1,
            'gramsPerUnit' => 20,
            'notes' => ''
        ];
    }

    public function setGuestsCount($value)
    {
        $value = (int)$value;
        if ($value < 1) $value = 1;
        
        $currentCount = count($this->orderItems);
        if ($value > $currentCount) {
            for ($i = $currentCount; $i < $value; $i++) {
                $this->orderItems[] = $this->getEmptyItem();
            }
        } elseif ($value < $currentCount) {
            $this->orderItems = array_slice($this->orderItems, 0, $value);
        }
        
        $this->guestsCount = count($this->orderItems);
    }

    public function selectTable($id)
    {
        $table = Table::find($id);
        if ($table && $table->status !== 'empty' && !$this->editingOrderId) {
            $this->addError('selectedTable', 'هذه الطاولة مشغولة حالياً، لا يمكن الحجز عليها حتى يتم تحريرها من التوصيل.');
            return;
        }

        $this->resetValidation('selectedTable');
        $this->selectedTable = $id;
        // Default guestsCount to 1, but user can change it
        $this->initOrderItems(1);
    }

    public function selectType($index, $type, $grams = 20)
    {
        $this->orderItems[$index]['shishaType'] = $type;
        $this->orderItems[$index]['gramsPerUnit'] = $grams;
    }

    public function incrementQuantity($index)
    {
        $this->orderItems[$index]['quantity']++;
    }

    public function decrementQuantity($index)
    {
        if ($this->orderItems[$index]['quantity'] > 1) {
            $this->orderItems[$index]['quantity']--;
        }
    }

    public function editOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order && $order->status === 'pending') {
            $this->editingOrderId = $order->id;
            $this->selectedTable = $order->table_id;
            $this->guestsCount = 1;
            
            $notes = $order->notes;
            // Clean up legacy ice level note if present
            if (strpos($notes, 'ثلج: ') === 0) {
                $parts = explode(' | ', $notes);
                if (count($parts) > 1) {
                    $notes = $parts[1];
                } else {
                    $notes = '';
                }
            }

            $this->orderItems = [
                [
                    'shishaType' => $order->shisha_type,
                    'quantity' => $order->quantity,
                    'grams_per_unit' => $order->grams_per_unit,
                    'notes' => $notes
                ]
            ];
        } else {
            session()->flash('error', 'لا يمكن تعديل هذا الطلب (ربما بدأ تحضيره).');
        }
    }

    public function cancelEdit()
    {
        $this->reset(['editingOrderId', 'selectedTable', 'tableSearch']);
        $this->initOrderItems(1);
    }

    public function requestCancel($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order) {
            $order->cancel_requested = true;
            $order->save();
            session()->flash('success', 'تم إرسال طلب إلغاء الطلبية للمدير.');
        }
    }

    public function rushOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order && in_array($order->status, ['pending', 'prep'])) {
            // Check if 10 minutes have passed since creation
            if ($order->created_at->gt(now()->subMinutes(10))) {
                session()->flash('error', 'لا يمكن تسريع الطلب إلا بعد مرور 10 دقائق من إنشائه.');
                return;
            }

            $order->is_rushed = true;
            $order->save();
            $this->dispatch('order-rushed');
            session()->flash('success', 'تم إرسال تنبيه تسريع للمطبخ بنجاح!');
        }
    }

    public function triggerUrgentAlert($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order && in_array($order->status, ['pending', 'prep'])) {
            $order->increment('kitchen_alert_count');
            $this->dispatch('urgent-alert-triggered');
            session()->flash('success', 'تم إرسال نداء استعجال طارئ للمطبخ! 🚨');
        }
    }

    public function selectSection($name)
    {
        $this->selectedSection = $name;
    }

    public function submitOrder()
    {
        if (!$this->selectedTable) {
            $this->addError('selectedTable', 'يرجى اختيار الطاولة');
            return;
        }

        $hasValidItem = false;
        foreach ($this->orderItems as $item) {
            if (!empty($item['shishaType'])) {
                $hasValidItem = true;
                break;
            }
        }

        if (!$hasValidItem) {
            session()->flash('error', 'يرجى اختيار صنف واحد على الأقل.');
            return;
        }

        $batchId = date('YmdHis') . rand(100, 999);
        $lastOrderIds = [];

        foreach ($this->orderItems as $item) {
            if (empty($item['shishaType'])) continue;
            
            $notesStr = trim($item['notes'] ?? '');
            $order = Order::create([
                'table_id'       => $this->selectedTable,
                'shisha_type'    => $item['shishaType'],
                'quantity'       => $item['quantity'],
                'grams_per_unit' => $item['gramsPerUnit'],
                'notes'          => $notesStr,
                'user_id'        => Auth::id(),
                'status'         => 'pending',
                'batch_id'       => $batchId,
            ]);
            $lastOrderIds[] = $order->id;
        }

        $table = Table::find($this->selectedTable);
        $table->status = 'order_prep';
        $table->save();

        // Print automatically
        try {
            $ordersToPrint = Order::with(['table', 'user'])->whereIn('id', $lastOrderIds)->get();
            $printerService = new \App\Services\PrinterService();
            $printerService->printBatch($ordersToPrint);
        } catch (\Exception $e) {
            \Log::error('Auto print failed: ' . $e->getMessage());
        }

        $this->dispatch('order-sent');
        session()->flash('success', 'تم إرسال الطلبات للمطبخ بنجاح!');
        
        $this->cancelEdit();
    }

    public function render()
    {
        $tablesQuery = Table::query();
        if ($this->tableSearch) {
            $tablesQuery->where('number', 'like', '%' . $this->tableSearch . '%');
        }
        if ($this->selectedSection) {
            $tablesQuery->where('section', $this->selectedSection);
        }
        $tables = $tablesQuery->orderBy('number')->get();

        // Show only معسل products in the order form
        $products = Product::where('is_active', true)
            ->where('category', 'معسل')
            ->orderBy('name')->get();
            
        $productGrams = $products->pluck('grams_per_order', 'name');
        
        $recentOrders = Order::with('table')
            ->where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->latest()
            ->take(10)
            ->get();

        $delayedOrders = Order::with('table')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'prep'])
            ->where('created_at', '<=', now()->subMinutes(10))
            ->get();

        $sections = Section::orderBy('name')->get();

        return view('livewire.order-staff-board', [
            'tables'       => $tables,
            'products'     => $products,
            'productGrams' => $productGrams,
            'recentOrders' => $recentOrders,
            'delayedOrders'=> $delayedOrders,
            'sections'     => $sections,
        ])->layout('components.layouts.app');
    }
}
