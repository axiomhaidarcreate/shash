<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Table;
use App\Models\Product;
use App\Models\StockEntry;

class AdminDashboard extends Component
{
    public function approveCancellation($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->cancel_requested) {
            $order->status = 'cancelled';
            $order->cancel_requested = false;
            $order->save();

            // Free the table only if no other active orders exist
            if ($order->table) {
                $hasActiveOrders = Order::where('table_id', $order->table_id)
                    ->whereIn('status', ['pending', 'prep', 'ready'])
                    ->exists();

                if (!$hasActiveOrders) {
                    $order->table->status = 'empty';
                    $order->table->save();
                    session()->flash('success', 'تم قبول طلب الإلغاء وتحرير الطاولة.');
                } else {
                    session()->flash('success', 'تم قبول طلب الإلغاء. (يوجد طلبات أخرى نشطة لنفس الطاولة)');
                }
            }
        }
    }

    public function rejectCancellation($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->cancel_requested = false;
            $order->save();
            session()->flash('success', 'تم رفض طلب الإلغاء، الطلب لا يزال نشطاً.');
        }
    }

    public function render()
    {
        $ordersToday = Order::whereDate('created_at', today())->count();
        $activeOrders = Order::whereIn('status', ['pending', 'prep'])->count();
        $completedOrders = Order::where('status', 'delivered')->whereDate('created_at', today())->count();
        
        $mostRequestedShisha = Order::select('shisha_type')
            ->selectRaw('count(*) as total')
            ->groupBy('shisha_type')
            ->orderBy('total', 'desc')
            ->first();

        $mostActiveTable = Order::select('table_id')
            ->selectRaw('count(*) as total')
            ->groupBy('table_id')
            ->orderBy('total', 'desc')
            ->first();

        $activeTableNumber = $mostActiveTable ? Table::find($mostActiveTable->table_id)?->number : 'لا يوجد';

        $tables = Table::all();
        $orders = Order::with('table', 'user')->orderBy('created_at', 'desc')->take(20)->get();
        $cancelRequests = Order::with('table', 'user')
            ->where('cancel_requested', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // --- Inventory Summary ---
        $totalStockIn = StockEntry::where('type', 'addition')->sum('quantity_grams');
        $totalWaste = StockEntry::where('type', 'waste')->sum('quantity_grams');
        $totalConsumed = Order::whereNotIn('status', ['cancelled'])
            ->selectRaw('SUM(quantity * grams_per_unit) as total_grams')
            ->value('total_grams') ?? 0;
        $totalRemaining = $totalStockIn - $totalConsumed - $totalWaste;

        // --- Detailed Today's Table Inventory Report ---
        $todayOrders = Order::with(['table', 'user'])
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'asc')
            ->get();

        $tableReport = [];
        $todayProductTotals = [];

        foreach ($todayOrders as $order) {
            $tableNumber = $order->table ? $order->table->number : 'أخرى';
            $sectionName = ($order->table && $order->table->section) ? $order->table->section : '';
            
            $key = $tableNumber . ($sectionName ? " ($sectionName)" : "");
            if (!isset($tableReport[$key])) {
                $tableReport[$key] = [
                    'table_number' => $tableNumber,
                    'section' => $sectionName,
                    'orders' => [],
                ];
            }
            
            $tableReport[$key]['orders'][] = [
                'shisha_type' => $order->shisha_type,
                'quantity' => $order->quantity,
                'waiter' => $order->user ? $order->user->name : 'غير معروف',
                'time' => $order->created_at->format('h:i A'),
            ];

            // Summarize total items served today
            if (!isset($todayProductTotals[$order->shisha_type])) {
                $todayProductTotals[$order->shisha_type] = 0;
            }
            $todayProductTotals[$order->shisha_type] += $order->quantity;
        }

        return view('livewire.admin-dashboard', [
            'ordersToday' => $ordersToday,
            'activeOrders' => $activeOrders,
            'completedOrders' => $completedOrders,
            'mostRequestedShisha' => $mostRequestedShisha ? $mostRequestedShisha->shisha_type : 'لا يوجد',
            'activeTableNumber' => $activeTableNumber,
            'tables' => $tables,
            'orders' => $orders,
            'cancelRequests' => $cancelRequests,
            'totalStockIn' => $totalStockIn,
            'totalConsumed' => $totalConsumed,
            'totalWaste' => $totalWaste,
            'totalRemaining' => $totalRemaining,
            
            // Passing detailed report variables
            'tableReport' => $tableReport,
            'todayProductTotals' => $todayProductTotals,
        ])->layout('components.layouts.admin');
    }
}
