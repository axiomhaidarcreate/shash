<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function print(Order $order)
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $loungePhone = $settings['lounge_phone'] ?? '';

        return view('invoice.print', compact('order', 'loungeName', 'loungePhone'));
    }

    public function printBatch(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $orders = Order::whereIn('id', $ids)->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'الطلبات غير موجودة.');
        }

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $loungePhone = $settings['lounge_phone'] ?? '';

        return view('invoice.print_batch', compact('orders', 'loungeName', 'loungePhone'));
    }

    public function kitchenPrint(Order $order)
    {
        return view('invoice.kitchen_print', compact('order'));
    }

    public function kitchenPrintBatch(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $orders = Order::whereIn('id', $ids)->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'الطلبات غير موجودة.');
        }

        return view('invoice.kitchen_print_batch', compact('orders'));
    }

    /**
     * Build table report data for today's orders
     */
    private function buildTableReport()
    {
        $targetDate = Carbon::today();

        $todayOrders = Order::with(['table', 'user'])
            ->whereDate('created_at', $targetDate)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'asc')
            ->get();

        $tableReport = [];
        $todayProductTotals = [];
        $totalOrders = 0;
        $totalQty = 0;

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

            if (!isset($todayProductTotals[$order->shisha_type])) {
                $todayProductTotals[$order->shisha_type] = 0;
            }
            $todayProductTotals[$order->shisha_type] += $order->quantity;

            $totalOrders++;
            $totalQty += $order->quantity;
        }

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';

        return compact('targetDate', 'tableReport', 'todayProductTotals', 'totalOrders', 'totalQty', 'loungeName');
    }

    /**
     * Print inventory report - A4 format
     */
    public function inventoryPrint()
    {
        $data = $this->buildTableReport();
        return view('invoice.inventory_print', $data);
    }

    /**
     * Print inventory report - Thermal (80mm) format
     */
    public function inventoryPrintThermal()
    {
        $data = $this->buildTableReport();
        return view('invoice.inventory_print_thermal', $data);
    }
}
