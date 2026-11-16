<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use App\Models\StockEntry;
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
     * احسب نطاق الفترة (shift1 أو shift2) لتاريخ معين
     * الفترة الأولى  (shift1): 18:00 اليوم → 03:00 اليوم التالي
     * الفترة الثانية (shift2): 03:00 اليوم → 18:00 اليوم
     */
    private function getShiftRange(string $shift, Carbon $date): array
    {
        if ($shift === 'shift1') {
            return [
                $date->copy()->setTime(18, 0, 0),
                $date->copy()->addDay()->setTime(3, 0, 0),
            ];
        }
        return [
            $date->copy()->setTime(3, 0, 0),
            $date->copy()->setTime(18, 0, 0),
        ];
    }

    /**
     * Build table report data — يدعم التاريخ المخصص والفترتين
     */
    private function buildTableReport(Request $request): array
    {
        $startDateParam = $request->query('start_date');
        $endDateParam = $request->query('end_date');
        $startTimeParam = $request->query('start_time', '00:00');
        $endTimeParam = $request->query('end_time', '23:59');
        $dateParam = $request->query('date');
        $shift     = $request->query('shift'); // shift1 | shift2 | null

        if ($startDateParam && $endDateParam) {
            $startAt = Carbon::parse($startDateParam . ' ' . $startTimeParam);
            $endAt = Carbon::parse($endDateParam . ' ' . $endTimeParam);
            $targetDate = $startAt; // For label purposes
            $shiftLabel = 'من ' . $startAt->format('Y-m-d H:i') . ' إلى ' . $endAt->format('Y-m-d H:i');
        } else {
            $targetDate = $dateParam
                ? Carbon::parse($dateParam)->startOfDay()
                : Carbon::today();

            // نطاق الاستعلام
            if (in_array($shift, ['shift1', 'shift2'])) {
                [$startAt, $endAt] = $this->getShiftRange($shift, $targetDate);
                $shiftLabel = $shift === 'shift1'
                    ? 'الفترة الأولى 🌙 (6م – 3ص)'
                    : 'الفترة الثانية ☀️ (3ص – 6م)';
            } else {
                $startAt    = $targetDate->copy()->startOfDay();
                $endAt      = $targetDate->copy()->endOfDay();
                $shiftLabel = null;
            }
        }

        $kitchen = $request->query('kitchen');
        $ordersQuery = Order::with(['table', 'user', 'approver', 'ticket'])
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $startAt)
            ->where('created_at', '<=', $endAt);

        if ($kitchen) {
            $ordersQuery->where('target_kitchen', $kitchen);
        }
        
        $ordersQuery->orderBy('created_at', 'asc');

        $shiftOrders = $ordersQuery->get();

        $tableReport        = [];
        $todayProductTotals = [];
        $uniqueTables       = [];
        $totalOrders        = 0;
        $totalQty           = 0;

        foreach ($shiftOrders as $order) {
            $sectionName = ($order->table && $order->table->section) ? $order->table->section : 'بدون صالة';
            $tableNumber = $order->table ? $order->table->number : 'أخرى';

            $uniqueTables[$sectionName . '_' . $tableNumber] = true;

            if (!isset($tableReport[$sectionName])) {
                $tableReport[$sectionName] = [
                    'section' => $sectionName,
                    'orders'  => [],
                ];
            }

            $tableReport[$sectionName]['orders'][] = [
                'table_number'           => $tableNumber,
                'shisha_type'            => $order->shisha_type,
                'quantity'               => $order->quantity,
                'waiter'                 => $order->user ? $order->user->name : 'غير معروف',
                'datetime'               => $order->created_at->format('Y-m-d h:i A'),
                'ticket_barcode'         => $order->ticket ? $order->ticket->barcode : null,
                'manager_approval_status'=> $order->manager_approval_status,
                'approver_name'          => $order->approver ? $order->approver->name : null,
                'guest_name'             => $order->guest_name,
            ];

            if (!isset($todayProductTotals[$order->shisha_type])) {
                $todayProductTotals[$order->shisha_type] = 0;
            }
            $todayProductTotals[$order->shisha_type] += $order->quantity;

            $totalOrders++;
            $totalQty += $order->quantity;
        }

        $totalTables = count($uniqueTables);

        $settings   = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $customTitle = $request->query('title');

        return compact(
            'targetDate', 'tableReport', 'todayProductTotals',
            'totalOrders', 'totalQty', 'loungeName', 'totalTables',
            'shiftLabel', 'shift', 'startAt', 'endAt', 'customTitle'
        );
    }

    /**
     * Print inventory report - A4 format
     */
    public function inventoryPrint(Request $request)
    {
        $data = $this->buildTableReport($request);
        return view('invoice.inventory_print', $data);
    }

    /**
     * Print inventory report - Thermal (80mm) format
     */
    public function inventoryPrintThermal(Request $request)
    {
        $data = $this->buildTableReport($request);
        return view('invoice.inventory_print_thermal', $data);
    }

    /**
     * Print Manager Approved Orders (Without Cashier Invoice) - Thermal (80mm) format
     */
    public function managerApprovedPrintThermal(Request $request)
    {
        $startDateParam = $request->query('start_date');
        $endDateParam = $request->query('end_date');
        if ($startDateParam && $endDateParam) {
            $startAt = Carbon::parse($startDateParam)->startOfDay();
            $endAt = Carbon::parse($endDateParam)->endOfDay();
            $targetDate = $startAt; 
        } else {
            $dateParam  = $request->query('date');
            $targetDate = $dateParam
                ? Carbon::parse($dateParam)->startOfDay()
                : Carbon::today();
            $startAt = $targetDate->copy()->startOfDay();
            $endAt = $targetDate->copy()->endOfDay();
        }

        $kitchen = $request->query('kitchen');
        $query = Order::with(['table', 'user'])
            ->whereBetween('created_at', [$startAt, $endAt])
            ->where('manager_approval_status', 'approved')
            ->where(function($q) {
                $q->whereNull('cashier_invoice_number')
                  ->orWhere('cashier_invoice_number', '');
            });
            
        if ($kitchen) {
            $query->where('target_kitchen', $kitchen);
        }

        $approvedOrders = $query->orderBy('created_at', 'asc')->get();

        $settings   = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $customTitle = $request->query('title');

        return view('invoice.manager_approved_print_thermal', compact('approvedOrders', 'loungeName', 'targetDate', 'customTitle'));
    }

    /**
     * Print Manager Approved Orders (Without Cashier Invoice) - A4 format
     */
    public function managerApprovedPrint(Request $request)
    {
        $startDateParam = $request->query('start_date');
        $endDateParam = $request->query('end_date');
        if ($startDateParam && $endDateParam) {
            $startAt = Carbon::parse($startDateParam)->startOfDay();
            $endAt = Carbon::parse($endDateParam)->endOfDay();
            $targetDate = $startAt; 
        } else {
            $dateParam  = $request->query('date');
            $targetDate = $dateParam
                ? Carbon::parse($dateParam)->startOfDay()
                : Carbon::today();
            $startAt = $targetDate->copy()->startOfDay();
            $endAt = $targetDate->copy()->endOfDay();
        }

        $kitchen = $request->query('kitchen');
        $query = Order::with(['table', 'user'])
            ->whereBetween('created_at', [$startAt, $endAt])
            ->where('manager_approval_status', 'approved')
            ->where(function($q) {
                $q->whereNull('cashier_invoice_number')
                  ->orWhere('cashier_invoice_number', '');
            });

        if ($kitchen) {
            $query->where('target_kitchen', $kitchen);
        }
        
        $approvedOrders = $query->orderBy('created_at', 'asc')->get();

        $settings   = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $customTitle = $request->query('title');

        return view('invoice.manager_approved_print', compact('approvedOrders', 'loungeName', 'targetDate', 'customTitle'));
    }

    /**
     * Build waste report data for a given date
     */
    private function buildWasteReport(Request $request): array
    {
        $startDateParam = $request->query('start_date');
        $endDateParam = $request->query('end_date');
        if ($startDateParam && $endDateParam) {
            $startAt = Carbon::parse($startDateParam)->startOfDay();
            $endAt = Carbon::parse($endDateParam)->endOfDay();
            $targetDate = $startAt; 
        } else {
            $dateParam  = $request->query('date');
            $targetDate = $dateParam
                ? Carbon::parse($dateParam)->startOfDay()
                : Carbon::today();
            $startAt = $targetDate->copy()->startOfDay();
            $endAt = $targetDate->copy()->endOfDay();
        }

        $kitchen = $request->query('kitchen');
        $query = StockEntry::with(['product', 'creator'])
            ->where('type', 'waste')
            ->where('status', 'approved')
            ->whereBetween('created_at', [$startAt, $endAt]);

        if ($kitchen) {
            $query->whereHas('product', function($q) use ($kitchen) {
                $q->where('target_kitchen', $kitchen);
            });
        }
        
        $wasteEntries = $query->orderBy('created_at', 'asc')->get();

        $totalWasteShisha = 0;
        $totalWasteAcc = 0;
        $wasteByProduct = [];

        foreach ($wasteEntries as $entry) {
            $isShisha = $entry->product->category === 'الشيش';
            if ($isShisha) {
                $totalWasteShisha += $entry->quantity_grams;
            } else {
                $totalWasteAcc += $entry->quantity_grams;
            }

            $productName = $entry->product->name;
            if (!isset($wasteByProduct[$productName])) {
                $wasteByProduct[$productName] = [
                    'quantity' => 0,
                    'category' => $entry->product->category,
                ];
            }
            $wasteByProduct[$productName]['quantity'] += $entry->quantity_grams;
        }

        $settings   = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $customTitle = $request->query('title');

        return compact(
            'targetDate', 'wasteEntries', 'totalWasteShisha',
            'totalWasteAcc', 'wasteByProduct', 'loungeName', 'customTitle'
        );
    }

    /**
     * Print approved waste report - A4 format
     */
    public function wastePrint(Request $request)
    {
        $data = $this->buildWasteReport($request);
        return view('invoice.waste_print', $data);
    }

    /**
     * Print approved waste report - Thermal (80mm) format
     */
    public function wastePrintThermal(Request $request)
    {
        $data = $this->buildWasteReport($request);
        return view('invoice.waste_print_thermal', $data);
    }

    /**
     * Build category report data for a given date and category
     */
    private function buildCategoryReport(Request $request): array
    {
        $dateParam  = $request->query('date');
        $categoryParam = $request->query('category');
        $shift      = $request->query('shift');
        $period     = $request->query('period', 'daily');
        
        $targetDate = $dateParam
            ? Carbon::parse($dateParam)->startOfDay()
            : Carbon::today();

        if (in_array($shift, ['shift1', 'shift2'])) {
            [$startAt, $endAt] = $this->getShiftRange($shift, $targetDate);
        } else {
            $startAt = match($period) {
                'daily'   => $targetDate->copy()->startOfDay(),
                'weekly'  => Carbon::now()->startOfWeek(),
                'monthly' => Carbon::now()->startOfMonth(),
                'yearly'  => Carbon::now()->startOfYear(),
                'custom'  => $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::today()->startOfDay(),
                'all'     => Carbon::create(2000, 1, 1),
                default   => $targetDate->copy()->startOfDay(),
            };
            if ($period === 'daily') {
                $endAt = $targetDate->copy()->endOfDay();
            } elseif ($period === 'custom') {
                $endAt = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::today()->endOfDay();
            } else {
                $endAt = null;
            }
        }

        // Get products for the selected category
        $products = \App\Models\Product::where('category', $categoryParam)->get();
        $isGramCategory = in_array($categoryParam, ['الشيشة', 'معسل', 'الشيش', 'رؤوس الشيشة', 'رؤوس شيشة', 'رأس شيشة']);
        $catUnit = $isGramCategory ? 'ج' : 'حبة';

        $reportData = [];

        foreach ($products as $product) {
            if ($categoryParam === 'التحايا') {
                $overallIn = \App\Models\GreetingQuota::where('product_id', $product->id)->sum('quantity');
                $overallOut = \App\Models\GuestGreeting::where('product_id', $product->id)->sum('total_greetings');
                $overallWaste = 0;
                
                $remaining = $overallIn - $overallOut - $overallWaste;

                $soldQ = \App\Models\GuestGreeting::where('product_id', $product->id)
                    ->where('created_at', '>=', $startAt);
                if ($endAt) $soldQ->where('created_at', '<', $endAt);
                
                $soldQty = (clone $soldQ)->sum('total_greetings');
                $sold = 0; // Not grams
                $damaged = 0;

            } else {
                $overallIn = StockEntry::where('product_id', $product->id)->where('type', 'addition')->sum('quantity_grams');
                $overallWaste = StockEntry::where('product_id', $product->id)->where('type', 'waste')->where('status', 'approved')->sum('quantity_grams');
                $overallOut = Order::where(function($q) use ($product) {
                        $q->where('shisha_type', $product->name)
                          ->orWhere('shisha_type', $product->name . ' (رأس فقط)')
                          ->orWhere('shisha_type', 'رأس ' . $product->name);
                    })
                    ->whereNotIn('status', ['cancelled'])
                    ->selectRaw('SUM(quantity * grams_per_unit) as total_grams')
                    ->value('total_grams') ?? 0;
                
                $remaining = $overallIn - $overallOut - $overallWaste;

                // Filtered Out (Sold)
                $ordersQ = Order::where(function($q) use ($product) {
                        $q->where('shisha_type', $product->name)
                          ->orWhere('shisha_type', $product->name . ' (رأس فقط)')
                          ->orWhere('shisha_type', 'رأس ' . $product->name);
                    })
                    ->whereNotIn('status', ['cancelled'])
                    ->where('created_at', '>=', $startAt);
                if ($endAt) $ordersQ->where('created_at', '<', $endAt);
                
                $sold = (clone $ordersQ)->selectRaw('SUM(quantity * grams_per_unit) as total_grams')->value('total_grams') ?? 0;
                $soldQty = (clone $ordersQ)->sum('quantity');

                // Filtered Waste (Damaged)
                $wasteQ = StockEntry::where('product_id', $product->id)
                    ->where('type', 'waste')
                    ->where('status', 'approved')
                    ->where('created_at', '>=', $startAt);
                if ($endAt) $wasteQ->where('created_at', '<', $endAt);
                
                $damaged = $wasteQ->sum('quantity_grams');
            }

            if ($sold > 0 || $damaged > 0 || $remaining > 0 || $overallIn > 0) {
                $reportData[] = [
                    'name' => $product->name,
                    'sold' => $isGramCategory ? $sold : $soldQty,
                    'damaged' => $damaged,
                    'remaining' => $remaining,
                    'unit' => $catUnit
                ];
            }
        }

        $settings   = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        
        $printDate = now()->format('Y-m-d h:i A');

        return compact(
            'targetDate', 'reportData', 'categoryParam', 'loungeName', 'printDate'
        );
    }

    public function categoryPrint(Request $request)
    {
        $data = $this->buildCategoryReport($request);
        return view('invoice.category_print', $data);
    }

    public function categoryPrintThermal(Request $request)
    {
        $data = $this->buildCategoryReport($request);
        return view('invoice.category_print_thermal', $data);
    }
    private function buildTicketReport(Request $request): array
    {
        $startDateParam = $request->query('start_date');
        $endDateParam = $request->query('end_date');
        $startTimeParam = $request->query('start_time', '00:00');
        $endTimeParam = $request->query('end_time', '23:59');
        $dateParam  = $request->query('date');

        if ($startDateParam === 'current_shift') {
            $activeShift = \App\Models\WorkShift::where('status', 'open')->first();
            if ($activeShift) {
                $startAt = $activeShift->created_at;
                $endAt = now();
                $targetDate = $startAt;
                $periodLabel = 'الفترة المحاسبية الحالية';
            } else {
                $startAt = now()->startOfDay();
                $endAt = now()->endOfDay();
                $targetDate = $startAt;
                $periodLabel = 'لا توجد فترة محاسبية مفتوحة';
            }
        } elseif (is_string($startDateParam) && str_starts_with($startDateParam, 'shift_')) {
            $reportId = str_replace('shift_', '', $startDateParam);
            $report = \App\Models\TicketDailyReport::find($reportId);
            if ($report) {
                $startAt = $report->opened_at;
                $endAt = $report->closed_at ?? now();
                $targetDate = $startAt;
                $periodLabel = 'الفترة المحاسبية المغلقة رقم ' . $reportId;
            } else {
                $startAt = now()->startOfDay();
                $endAt = now()->endOfDay();
                $targetDate = $startAt;
                $periodLabel = 'فترة محاسبية غير موجودة';
            }
        } elseif ($startDateParam && $endDateParam) {
            $startAt = Carbon::parse($startDateParam . ' ' . $startTimeParam);
            $endAt = Carbon::parse($endDateParam . ' ' . $endTimeParam);
            $targetDate = $startAt; // For label
            $periodLabel = 'من ' . $startAt->format('Y/m/d H:i') . ' إلى ' . $endAt->format('Y/m/d H:i');
        } else {
            $targetDate = $dateParam
                ? Carbon::parse($dateParam)->startOfDay()
                : Carbon::today();
            $startAt = $targetDate->copy()->startOfDay();
            $endAt = $targetDate->copy()->endOfDay();
            $periodLabel = $targetDate->format('Y/m/d');
        }

        $tickets = \App\Models\Ticket::with(['user', 'ticketTemplate'])
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $startAt)
            ->where('created_at', '<=', $endAt)
            ->orderBy('created_at', 'asc')
            ->get();

        $stats = [
            'total'     => $tickets->count(),
            'active'    => $tickets->where('status', 'active')->count(),
            'used'      => $tickets->where('status', 'used')->count(),
            'cancelled' => 0, // Excluded from query
        ];

        $templateStats = [];
        $productStats  = [];
        $totalProducts = 0;

        foreach ($tickets as $ticket) {
            $templateName = $ticket->ticketTemplate ? $ticket->ticketTemplate->name : 'أخرى';
            if (!isset($templateStats[$templateName])) {
                $templateStats[$templateName] = 0;
            }
            $templateStats[$templateName]++;

            if (is_array($ticket->selected_products)) {
                foreach ($ticket->selected_products as $product) {
                    if (!isset($productStats[$product])) {
                        $productStats[$product] = 0;
                    }
                    $productStats[$product]++;
                    $totalProducts++;
                }
            }
        }

        $settings   = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $printDate  = now()->format('Y-m-d h:i A');

        return compact(
            'targetDate', 'periodLabel', 'tickets', 'stats', 'templateStats', 'productStats', 'totalProducts', 'loungeName', 'printDate'
        );
    }

    public function ticketReportPrint(Request $request)
    {
        $data = $this->buildTicketReport($request);
        return view('invoice.ticket_report_print', $data);
    }

    public function ticketReportPrintThermal(Request $request)
    {
        $data = $this->buildTicketReport($request);
        return view('invoice.ticket_report_print_thermal', $data);
    }

    private function buildGreetingsReport(Request $request): array
    {
        $dateParam = $request->query('date');
        $staffId = $request->query('staff_id');

        // If the user is not an admin, they can only view their own reports
        if (!auth()->user()->hasRole('admin')) {
            $staffId = auth()->id();
        }

        $targetDate = $dateParam ? Carbon::parse($dateParam)->startOfDay() : Carbon::today();
        $startAt = $targetDate->copy()->startOfDay();
        $endAt = $targetDate->copy()->endOfDay();

        $query = \App\Models\GuestGreeting::with(['staff', 'table', 'product'])
            ->where('created_at', '>=', $startAt)
            ->where('created_at', '<=', $endAt);

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $reports = $query->orderBy('created_at', 'asc')->get();

        $totalRevenue = $reports->sum('total_amount');
        $totalGreetings = $reports->sum('total_greetings');

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $printDate = now()->format('Y-m-d h:i A');

        return compact('reports', 'targetDate', 'totalRevenue', 'totalGreetings', 'loungeName', 'printDate');
    }

    public function greetingsReportPrint(Request $request)
    {
        $data = $this->buildGreetingsReport($request);
        return view('invoice.greetings_report_print', $data);
    }

    public function greetingsReportPrintThermal(Request $request)
    {
        $data = $this->buildGreetingsReport($request);
        return view('invoice.greetings_report_print_thermal', $data);
    }

    private function buildKitchenReport(Request $request): array
    {
        $type = $request->query('type', 'daily');
        
        $kitchen = $request->query('kitchen');
        
        if ($type === 'range') {
            $startDateParam = $request->query('start_date');
            $endDateParam = $request->query('end_date');
            $startAt = $startDateParam ? Carbon::parse($startDateParam)->startOfDay() : now()->startOfMonth();
            $endAt = $endDateParam ? Carbon::parse($endDateParam)->endOfDay() : now()->endOfMonth();
            $periodLabel = 'من ' . $startAt->format('Y-m-d') . ' إلى ' . $endAt->format('Y-m-d');
            $query = Order::whereBetween('created_at', [$startAt, $endAt]);
            if ($kitchen) {
                $query->where('target_kitchen', $kitchen);
            }
            $orders = $query->get();
        } else {
            $activeShift = \App\Models\WorkShift::where('status', 'open')->first();
            if ($activeShift) {
                $startAt = $activeShift->created_at;
                $endAt = now();
                $periodLabel = 'الفترة المحاسبية الحالية (بدأت ' . $startAt->format('Y-m-d h:i A') . ')';
                $query = Order::whereBetween('created_at', [$startAt, $endAt]);
                if ($kitchen) {
                    $query->where('target_kitchen', $kitchen);
                }
                $orders = $query->get();
            } else {
                $periodLabel = 'لا توجد فترة محاسبية مفتوحة';
                $orders = collect();
            }
        }

        $productCategories = \App\Models\Product::pluck('category', 'name')->toArray();
        $stats = [];
        
        $totalSold = 0;
        $totalCancelled = 0;

        foreach ($orders as $order) {
            $category = $productCategories[$order->shisha_type] ?? 'أخرى';
            
            if (!isset($stats[$category])) {
                $stats[$category] = ['total_sold' => 0, 'total_cancelled' => 0, 'products' => []];
            }
            if (!isset($stats[$category]['products'][$order->shisha_type])) {
                $stats[$category]['products'][$order->shisha_type] = ['sold' => 0, 'cancelled' => 0];
            }
            
            if ($order->status === 'cancelled') {
                $stats[$category]['total_cancelled'] += $order->quantity;
                $stats[$category]['products'][$order->shisha_type]['cancelled'] += $order->quantity;
                $totalCancelled += $order->quantity;
            } else {
                $stats[$category]['total_sold'] += $order->quantity;
                $stats[$category]['products'][$order->shisha_type]['sold'] += $order->quantity;
                $totalSold += $order->quantity;
            }
        }

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $printDate = now()->format('Y-m-d h:i A');

        return compact('stats', 'periodLabel', 'loungeName', 'printDate', 'type', 'totalSold', 'totalCancelled');
    }

    public function kitchenReportPrint(Request $request)
    {
        $data = $this->buildKitchenReport($request);
        return view('invoice.kitchen_report_print', $data);
    }

    public function kitchenReportPrintThermal(Request $request)
    {
        $data = $this->buildKitchenReport($request);
        return view('invoice.kitchen_report_print_thermal', $data);
    }

    public function supplierPrint($id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $printDate = now()->format('Y-m-d h:i A');

        return view('invoice.supplier_print', compact('supplier', 'loungeName', 'printDate'));
    }

    public function supplierPrintThermal($id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $printDate = now()->format('Y-m-d h:i A');

        return view('invoice.supplier_print_thermal', compact('supplier', 'loungeName', 'printDate'));
    }
    public function purchasePrint($id)
    {
        $invoice = \App\Models\PurchaseInvoice::with(['supplier', 'installments'])->findOrFail($id);
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $printDate = now()->format('Y-m-d h:i A');

        return view('invoice.purchase_print', compact('invoice', 'loungeName', 'printDate'));
    }

    public function expensesReport(\Illuminate\Http\Request $request)
    {
        $filterFrom = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $filterTo   = $request->query('to',   now()->endOfMonth()->format('Y-m-d'));
        $search     = $request->query('search', '');

        $expenses = \App\Models\Expense::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('invoice_number', 'like', "%{$search}%"))
            ->when($filterFrom, fn($q) => $q->where('invoice_date', '>=', $filterFrom))
            ->when($filterTo,   fn($q) => $q->where('invoice_date', '<=', $filterTo))
            ->orderBy('invoice_date')
            ->get();

        $total = $expenses->sum('amount');
        $settings   = Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $printDate  = now()->format('Y-m-d h:i A');

        return view('invoice.expenses_report', compact('expenses', 'total', 'filterFrom', 'filterTo', 'loungeName', 'printDate'));
    }

    public function dailyPurchasesReport(\Illuminate\Http\Request $request)
    {
        $filterFrom = $request->query('from');
        $filterTo   = $request->query('to');
        $search     = $request->query('search', '');
        $status     = $request->query('status', '');
        $kitchen    = $request->query('kitchen', '');
        $allDates   = $request->boolean('all_dates', false);

        $query = \App\Models\DailyPurchaseRequest::query()
            ->with('user')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc');

        if ($search) {
            $query->where('item_name', 'like', '%' . $search . '%');
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($kitchen) {
            $query->where('kitchen_source', $kitchen);
        }

        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('admin');

        // If specific date range is provided
        if ($filterFrom && $filterTo) {
            $query->whereDate('created_at', '>=', $filterFrom)
                  ->whereDate('created_at', '<=', $filterTo);
            $reportDateRange = "من {$filterFrom} إلى {$filterTo}";
        } elseif (!$isAdmin) {
            // For employee: show pending orders (not yet purchased) + orders purchased today
            $query->where(function ($q) {
                $q->where('status', '!=', 'تم الشراء')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'تم الشراء')
                          ->whereDate('updated_at', \Carbon\Carbon::today());
                  });
            });
            $today = \Carbon\Carbon::today()->format('Y-m-d');
            $reportDateRange = "مشتريات اليوم ({$today})";
        } elseif ($isAdmin && $allDates) {
            $reportDateRange = 'كافة الفترات';
        } else {
            $reportDateRange = 'كافة الفترات';
        }

        $requests = $query->get();

        $settings   = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $printDate  = now()->format('Y-m-d h:i A');

        return view('invoice.daily_purchases_report', compact(
            'requests', 'filterFrom', 'filterTo', 'search', 'status', 'kitchen', 'loungeName', 'printDate', 'reportDateRange'
        ));
    }

    public function physicalInventoryPrint($id)
    {
        $inventory = \App\Models\PhysicalInventory::with(['user', 'items'])->findOrFail($id);
        $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        $loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $printDate = now()->format('Y-m-d h:i A');

        return view('invoice.physical_inventory_print', compact('inventory', 'loungeName', 'printDate'));
    }
}
