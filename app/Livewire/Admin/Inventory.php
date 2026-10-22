<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Inventory extends Component
{
    // For adding stock/waste
    public $selectedProductId = '';
    public $quantityGrams = '';
    public $stockNotes = '';
    public $entryType = 'addition'; // 'addition' or 'waste'
    public $entryUnit = ''; // 'gram' (Moassel) or 'piece' (Accessories)

    // For filtering reports
    public $filterPeriod = 'all'; // daily, weekly, monthly, yearly, all, shift1, shift2
    public $filterDate = ''; // تاريخ مخصص للتقارير YYYY-MM-DD
    public $filterStartTime = ''; // ساعة البداية للجرد HH:MM
    public $filterEndTime = '';   // ساعة النهاية للجرد HH:MM

    // For new UI layout
    public $activeCategory = '';
    public $activeProductId = null;

    public $tableReportDate = '';
    public $tableReportSearch = '';

    // For Daily Closures
    public $showDailyReportModal = false;
    public $filterReportDate = '';

    public function closeDay()
    {
        // Get today's total stats (from start of today until now)
        $today = \Carbon\Carbon::today();

        // Total added stock today
        $totalStockIn = \App\Models\StockEntry::where('type', 'addition')
            ->whereDate('created_at', $today)
            ->count(); // Or we can sum quantity, but maybe count of entries is simpler. Wait, let's sum quantity
            
        $totalStockIn = \App\Models\StockEntry::where('type', 'addition')
            ->whereDate('created_at', $today)
            ->sum('quantity_grams');

        // Total consumed today
        $totalConsumed = \App\Models\Order::whereNotIn('status', ['cancelled'])
            ->whereDate('created_at', $today)
            ->sum('quantity');

        // Total waste today
        $totalWaste = \App\Models\StockEntry::where('type', 'waste')
            ->where('status', 'approved')
            ->whereDate('created_at', $today)
            ->sum('quantity_grams');

        $activeShift = \App\Models\WorkShift::where('status', 'open')->first();

        // Save report
        \App\Models\InventoryDailyReport::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'total_stock_in' => $totalStockIn,
            'total_consumed' => $totalConsumed,
            'total_waste' => $totalWaste,
            'opened_at' => $activeShift ? $activeShift->opened_at : $today,
            'closed_at' => now(),
        ]);

        session()->flash('success', 'تم تسجيل إغلاق اليوم للجرد وإصدار التقرير بنجاح (بدون تصفير العدادات).');
    }

    public function toggleReportModal()
    {
        $this->showDailyReportModal = !$this->showDailyReportModal;
    }

    // For updating grams_per_order
    public $editingGramsId = null;
    public $editGramsValue = 20;

    public function isGramCategory($categoryName)
    {
        return in_array($categoryName, ['الشيشة', 'معسل', 'الشيش', 'رؤوس الشيشة', 'رؤوس شيشة', 'رأس شيشة']);
    }

    /**
     * منطق الفترتين:
     * الفترة الأولى (مسائية): 6 مساءً → 3 صباحاً (اليوم التالي)
     * الفترة الثانية (صباحية): 3 صباحاً → 6 مساءً
     *
     * عند اختيار تاريخ مثل 2026-05-31:
     * - shift1: 2026-05-31 18:00 → 2026-06-01 03:00
     * - shift2: 2026-05-31 03:00 → 2026-05-31 18:00
     */
    private function getShiftRange(string $shift, ?Carbon $baseDate = null): array
    {
        $date = $baseDate ?? Carbon::today();

        if ($shift === 'shift1') {
            // الفترة الأولى: 6م اليوم → 3ص اليوم التالي
            return [
                $date->copy()->setTime(18, 0, 0),
                $date->copy()->addDay()->setTime(3, 0, 0),
            ];
        }

        // الفترة الثانية: 3ص → 6م نفس اليوم
        return [
            $date->copy()->setTime(3, 0, 0),
            $date->copy()->setTime(18, 0, 0),
        ];
    }

    public function mount()
    {
        if (Auth::check() && !Auth::user()->hasRole('admin')) {
            $this->entryType = 'waste';
        }
        $this->filterDate = now()->format('Y-m-d');
        $this->tableReportDate = now()->format('Y-m-d');

        // Dynamic active category initialization
        $firstShishaCategory = Category::where('name', 'الشيشة')
            ->orWhere('name', 'معسل')
            ->orWhere('name', 'الشيش')
            ->first();
        if (!$firstShishaCategory) {
            $firstShishaCategory = Category::first();
        }
        $this->activeCategory = $firstShishaCategory ? $firstShishaCategory->name : '';
    }

    public function addStock()
    {
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'quantityGrams'     => 'required|integer|min:1',
            'entryType'         => 'required|in:addition,waste',
        ]);

        if ($this->entryType === 'addition' && !Auth::user()->hasRole('admin')) {
            session()->flash('error', 'غير مصرح لك بإضافة كميات.');
            return;
        }

        $status = 'approved';
        if ($this->entryType === 'waste') {
            $status = 'pending';
        }

        if ($status === 'pending') {
            $msg = 'تم تسجيل التالف وإرساله للمدير للموافقة.';
        } else {
            $msg = 'تم إضافة الكمية للمخزون بنجاح.';
        }

        $product = Product::find($this->selectedProductId);
        $finalGrams = $this->quantityGrams;
        
        if ($this->entryUnit === 'head') {
            $finalGrams = $this->quantityGrams * ($product->grams_per_order ?: 20);
        }

        // If it's a refill product name, map it to the base product so it deducts from main moassel
        $baseProd = $product;
        if (str_starts_with($product->name, 'رأس ') || str_ends_with($product->name, ' (رأس فقط)')) {
            $baseName = str_replace(['رأس ', ' (رأس فقط)'], '', $product->name);
            $mainProd = Product::where('name', $baseName)->first();
            if ($mainProd) {
                $baseProd = $mainProd;
            }
        }

        StockEntry::create([
            'product_id'     => $baseProd->id,
            'quantity_grams' => $finalGrams,
            'type'           => $this->entryType,
            'status'         => $status,
            'notes'          => $this->stockNotes ?: null,
            'created_by'     => Auth::id(),
        ]);

        $this->reset(['selectedProductId', 'quantityGrams', 'stockNotes', 'entryUnit']);
        if (!Auth::user()->hasRole('admin')) {
            $this->entryType = 'waste';
        } else {
            $this->entryType = 'addition';
        }
        
        session()->flash('success', $msg);
    }

    public function startEditGrams($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $this->editingGramsId = $productId;
            $this->editGramsValue = $product->grams_per_order;
        }
    }

    public function saveGrams()
    {
        if (!Auth::user()->hasRole('admin')) return;

        $this->validate([
            'editGramsValue' => 'required|integer|min:1|max:1000',
        ]);

        Product::where('id', $this->editingGramsId)->update([
            'grams_per_order' => $this->editGramsValue,
        ]);

        $this->reset(['editingGramsId', 'editGramsValue']);
        session()->flash('success', 'تم تحديث الجرامات لكل طلب.');
    }

    public function cancelEditGrams()
    {
        $this->reset(['editingGramsId', 'editGramsValue']);
    }

    public function switchCategory($category)
    {
        $this->activeCategory = $category;
        $this->activeProductId = null; // Reset selection
    }

    public function selectProduct($productId)
    {
        $this->activeProductId = $productId;
    }

    public function approveWaste($id)
    {
        if (!Auth::user()->hasRole('admin')) return;
        StockEntry::where('id', $id)->update(['status' => 'approved']);
        session()->flash('success', 'تم اعتماد طلب الإتلاف.');
    }

    public function rejectWaste($id)
    {
        if (!Auth::user()->hasRole('admin')) return;
        StockEntry::where('id', $id)->update(['status' => 'rejected']);
        session()->flash('success', 'تم رفض وإلغاء طلب الإتلاف.');
    }


    public function resetAllInventory()
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            session()->flash('error', 'غير مصرح لك بتصفير المخزون.');
            return;
        }

        StockEntry::query()->delete();
        Order::query()->delete();

        session()->flash('success', 'تم تصفير جميع عدادات المخزون (المضاف، التالف، والمباع) بنجاح لبدء العمل من جديد.');
    }

    public function render()
    {
        // Get all products
        $products = Product::orderBy('category')->orderBy('name')->get();
        
        // Get all categories for tabs
        $categories = Category::orderBy('name')->get();

        // Filter products for the dropdown based on selected unit
        $filteredProducts = $products;
        if ($this->entryUnit === 'gram' || $this->entryUnit === 'head') {
            $filteredProducts = $products->filter(fn($p) => $this->isGramCategory($p->category));
        } elseif ($this->entryUnit === 'piece') {
            $filteredProducts = $products->filter(fn($p) => !$this->isGramCategory($p->category));
        }

        // ─── تحديد نطاق التاريخ بناءً على الفلتر ───
        $baseDate = $this->filterDate
            ? Carbon::parse($this->filterDate)
            : Carbon::today();

        $isShiftFilter = in_array($this->filterPeriod, ['shift1', 'shift2']);
        $hasCustomTime = $this->filterStartTime && $this->filterEndTime;

        if ($isShiftFilter) {
            [$startDate, $endDate] = $this->getShiftRange($this->filterPeriod, $baseDate);
            $shiftLabel = $this->filterPeriod === 'shift1'
                ? 'الفترة الأولى (6م – 3ص)'
                : 'الفترة الثانية (3ص – 6م)';
        } elseif ($hasCustomTime) {
            $startDate = Carbon::parse($this->filterDate . ' ' . $this->filterStartTime);
            $endDate   = Carbon::parse($this->filterDate . ' ' . $this->filterEndTime);
            // إذا كان وقت النهاية أقل من وقت البداية → يمتد لليوم التالي
            if ($endDate->lte($startDate)) {
                $endDate->addDay();
            }
            $shiftLabel = 'من ' . $this->filterStartTime . ' إلى ' . $this->filterEndTime;
        } else {
            $endDate = null;
            $startDate = match($this->filterPeriod) {
                'daily'   => $baseDate->copy()->startOfDay(),
                'weekly'  => Carbon::now()->startOfWeek(),
                'monthly' => Carbon::now()->startOfMonth(),
                'yearly'  => Carbon::now()->startOfYear(),
                default   => Carbon::create(2000, 1, 1),
            };
            $shiftLabel = null;
        }

        // ─── بناء بيانات المخزون لكل منتج ───
        $inventoryData = $products->map(function ($product) use ($startDate, $endDate) {
            // الإجماليات الكلية (للمخزون المتبقي الفعلي - لا يتأثر بالفلتر)
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
                
            // Add composite product consumption for overallOut
            $compositeLinks = \App\Models\ProductIngredient::where('ingredient_id', $product->id)->with('product')->get();
            foreach ($compositeLinks as $link) {
                if ($link->product) {
                    $parentOverallOut = Order::where('shisha_type', $link->product->name)
                        ->whereNotIn('status', ['cancelled'])
                        ->sum('quantity');
                    $overallOut += ($parentOverallOut * $link->quantity);
                }
            }
            
            $remaining = $overallIn - $overallOut - $overallWaste;

            // الإجماليات المفلترة (للتقرير)
            $stockQ = StockEntry::where('product_id', $product->id)
                ->where('type', 'addition')
                ->where('created_at', '>=', $startDate);
            if ($endDate) $stockQ->where('created_at', '<', $endDate);
            $filteredIn = $stockQ->sum('quantity_grams');

            $wasteQ = StockEntry::where('product_id', $product->id)
                ->where('type', 'waste')
                ->where('status', 'approved')
                ->where('created_at', '>=', $startDate);
            if ($endDate) $wasteQ->where('created_at', '<', $endDate);
            $filteredWaste = $wasteQ->sum('quantity_grams');

            $ordersQ = Order::where(function($q) use ($product) {
                    $q->where('shisha_type', $product->name)
                      ->orWhere('shisha_type', $product->name . ' (رأس فقط)')
                      ->orWhere('shisha_type', 'رأس ' . $product->name);
                })
                ->whereNotIn('status', ['cancelled'])
                ->where('created_at', '>=', $startDate);
            if ($endDate) $ordersQ->where('created_at', '<', $endDate);
            $filteredOut = (clone $ordersQ)->selectRaw('SUM(quantity * grams_per_unit) as total_grams')->value('total_grams') ?? 0;
            
            // Add composite product consumption for filteredOut
            foreach ($compositeLinks as $link) {
                if ($link->product) {
                    $parentFilteredOut = Order::where('shisha_type', $link->product->name)
                        ->whereNotIn('status', ['cancelled'])
                        ->where('created_at', '>=', $startDate);
                    if ($endDate) $parentFilteredOut->where('created_at', '<', $endDate);
                    $filteredOut += ($parentFilteredOut->sum('quantity') * $link->quantity);
                }
            }
            
            $totalOrders = (clone $ordersQ)->sum('quantity');

            // آخر حركات خلال الفترة
            $histQ = StockEntry::where('product_id', $product->id)
                ->where('created_at', '>=', $startDate);
            if ($endDate) $histQ->where('created_at', '<', $endDate);
            $history = $histQ->latest()->take(5)->get();

            return [
                'product'        => $product,
                'overall_in'     => $overallIn,
                'remaining'      => $remaining,
                'filtered_in'    => $filteredIn,
                'filtered_out'   => $filteredOut,
                'filtered_waste' => $filteredWaste,
                'total_orders'   => $totalOrders,
                'history'        => $history,
            ];
        });

        // ─── إحصائيات المعسل / الشيشة (بالجرامات) ───
        $shishaData = $inventoryData->filter(fn($d) => $this->isGramCategory($d['product']->category));
        $totalStockInShisha   = $shishaData->sum('filtered_in');
        $totalConsumedShisha  = $shishaData->sum('filtered_out');
        $totalWasteShisha     = $shishaData->sum('filtered_waste');
        $totalRemainingShisha = $shishaData->sum('remaining');

        // ─── إحصائيات المستلزمات (بالعدد/الحبة) ───
        $accData = $inventoryData->filter(fn($d) => !$this->isGramCategory($d['product']->category));
        $totalStockInAcc   = $accData->sum('filtered_in');
        $totalConsumedAcc  = $accData->sum('filtered_out');
        $totalWasteAcc     = $accData->sum('filtered_waste');
        $totalRemainingAcc = $accData->sum('remaining');

        // ─── طلبات الإتلاف المعلقة ───
        $pendingWastesShisha = collect([]);
        $pendingWastesAcc = collect([]);
        if (Auth::user()->hasRole('admin')) {
            $pendingWastes = StockEntry::with(['product'])
                ->where('type', 'waste')
                ->where('status', 'pending')
                ->latest()
                ->get();
            $pendingWastesShisha = $pendingWastes->filter(fn($w) => $this->isGramCategory($w->product->category));
            $pendingWastesAcc = $pendingWastes->filter(fn($w) => !$this->isGramCategory($w->product->category));
        }


        // ─── تقرير الطاولات المفصّل بناءً على الفترة/التاريخ ───
        $tableDate = $this->tableReportDate ? Carbon::parse($this->tableReportDate) : Carbon::today();
        
        $ordersQuery = Order::with(['table', 'user'])
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $tableDate->copy()->startOfDay())
            ->where('created_at', '<=', $tableDate->copy()->endOfDay())
            ->orderBy('created_at', 'asc');

        if (!empty($this->tableReportSearch)) {
            $ordersQuery->whereHas('table', function($q) {
                $q->where('number', 'like', '%' . $this->tableReportSearch . '%')
                  ->orWhere('section', 'like', '%' . $this->tableReportSearch . '%');
            });
        }

        $shiftOrders = $ordersQuery->get();

        $tableReport = [];
        $todayProductTotals = [];

        foreach ($shiftOrders as $order) {
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
                'quantity'    => $order->quantity,
                'waiter'      => $order->user ? $order->user->name : 'غير معروف',
                'time'        => $order->created_at->format('h:i A'),
            ];

            if (!isset($todayProductTotals[$order->shisha_type])) {
                $todayProductTotals[$order->shisha_type] = 0;
            }
            $todayProductTotals[$order->shisha_type] += $order->quantity;
        }

        $reportsQuery = \App\Models\InventoryDailyReport::with('user')->orderBy('created_at', 'desc');
        if ($this->filterReportDate) {
            $reportsQuery->whereDate('created_at', $this->filterReportDate);
        }
        $dailyReports = $reportsQuery->get();

        return view('livewire.admin.inventory', [
            'categories'     => $categories,
            'products'       => $products,
            'filteredProducts' => $filteredProducts,
            'inventoryData'  => $inventoryData,
            'totalStockInShisha'   => $totalStockInShisha,
            'totalConsumedShisha'  => $totalConsumedShisha,
            'totalWasteShisha'     => $totalWasteShisha,
            'totalRemainingShisha' => $totalRemainingShisha,
            'totalStockInAcc'      => $totalStockInAcc,
            'totalConsumedAcc'     => $totalConsumedAcc,
            'totalWasteAcc'        => $totalWasteAcc,
            'totalRemainingAcc'    => $totalRemainingAcc,
            'pendingWastesShisha'  => $pendingWastesShisha,
            'pendingWastesAcc'     => $pendingWastesAcc,
            'tableReport'          => $tableReport,
            'todayProductTotals'   => $todayProductTotals,
            'filterDate'           => $this->filterDate,
            'filterStartTime'      => $this->filterStartTime,
            'filterEndTime'        => $this->filterEndTime,
            'hasCustomTime'        => $hasCustomTime,
            'shiftLabel'           => $shiftLabel,
            'baseDate'             => $baseDate,
            'isShiftFilter'        => $isShiftFilter,
            'dailyReports'         => $dailyReports,
        ])->layout('components.layouts.admin');
    }
}
