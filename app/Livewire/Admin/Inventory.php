<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Inventory extends Component
{
    // For adding stock/waste
    public $selectedProductId = '';
    public $quantityGrams = '';
    public $stockNotes = '';
    public $entryType = 'addition'; // 'addition' or 'waste'

    // For filtering reports
    public $filterPeriod = 'all'; // daily, weekly, monthly, yearly, all

    // For updating grams_per_order
    public $editingGramsId = null;
    public $editGramsValue = 20;

    public function mount()
    {
        if (Auth::check() && !Auth::user()->hasRole('admin')) {
            $this->entryType = 'waste';
        }
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
        if ($this->entryType === 'waste' && !Auth::user()->hasRole('admin')) {
            $status = 'pending';
        }

        if ($status === 'pending') {
            $msg = 'تم تسجيل التالف وإرساله للمدير للموافقة.';
        } else {
            $msg = $this->entryType === 'waste' ? 'تم تسجيل التالف واعتماده بنجاح.' : 'تم إضافة الكمية للمخزون بنجاح.';
        }

        StockEntry::create([
            'product_id'     => $this->selectedProductId,
            'quantity_grams' => $this->quantityGrams,
            'type'           => $this->entryType,
            'status'         => $status,
            'notes'          => $this->stockNotes ?: null,
            'created_by'     => Auth::id(),
        ]);

        $this->reset(['selectedProductId', 'quantityGrams', 'stockNotes']);
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

    public function render()
    {
        // Get all shisha products and accessories
        $products = Product::whereIn('category', ['معسل', 'أخرى'])->orderBy('category')->orderBy('name')->get();

        $startDate = match($this->filterPeriod) {
            'daily'   => Carbon::today(),
            'weekly'  => Carbon::now()->startOfWeek(),
            'monthly' => Carbon::now()->startOfMonth(),
            'yearly'  => Carbon::now()->startOfYear(),
            default   => Carbon::create(2000, 1, 1),
        };

        // For each product calculate based on filter for IN/OUT, but overall remaining is ALWAYS total
        $inventoryData = $products->map(function ($product) use ($startDate) {
            // -- OVERALL TOTALS (For current stock) --
            $overallIn = StockEntry::where('product_id', $product->id)->where('type', 'addition')->sum('quantity_grams');
            $overallWaste = StockEntry::where('product_id', $product->id)->where('type', 'waste')->where('status', 'approved')->sum('quantity_grams');
            $overallOut = Order::where('shisha_type', $product->name)
                ->whereNotIn('status', ['cancelled'])
                ->selectRaw('SUM(quantity * grams_per_unit) as total_grams')
                ->value('total_grams') ?? 0;
            
            $remaining = $overallIn - $overallOut - $overallWaste;

            // -- FILTERED TOTALS (For reports) --
            $filteredIn = StockEntry::where('product_id', $product->id)
                ->where('type', 'addition')
                ->where('created_at', '>=', $startDate)
                ->sum('quantity_grams');

            $filteredWaste = StockEntry::where('product_id', $product->id)
                ->where('type', 'waste')
                ->where('status', 'approved')
                ->where('created_at', '>=', $startDate)
                ->sum('quantity_grams');

            $filteredOut = Order::where('shisha_type', $product->name)
                ->whereNotIn('status', ['cancelled'])
                ->where('created_at', '>=', $startDate)
                ->selectRaw('SUM(quantity * grams_per_unit) as total_grams')
                ->value('total_grams') ?? 0;

            $totalOrders = Order::where('shisha_type', $product->name)
                ->whereNotIn('status', ['cancelled'])
                ->where('created_at', '>=', $startDate)
                ->sum('quantity');

            // Stock history (last 5 entries for this period)
            $history = StockEntry::where('product_id', $product->id)
                ->where('created_at', '>=', $startDate)
                ->latest()
                ->take(5)
                ->get();

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

        // Summary totals for Shisha
        $shishaData = $inventoryData->filter(fn($d) => $d['product']->category === 'معسل');
        $totalStockInShisha   = $shishaData->sum('filtered_in');
        $totalConsumedShisha  = $shishaData->sum('filtered_out');
        $totalWasteShisha     = $shishaData->sum('filtered_waste');
        $totalRemainingShisha = $shishaData->sum('remaining');

        // Summary totals for Accessories
        $accData = $inventoryData->filter(fn($d) => $d['product']->category !== 'معسل');
        $totalStockInAcc   = $accData->sum('filtered_in');
        $totalConsumedAcc  = $accData->sum('filtered_out');
        $totalWasteAcc     = $accData->sum('filtered_waste');
        $totalRemainingAcc = $accData->sum('remaining');

        $pendingWastes = collect([]);
        if (Auth::user()->hasRole('admin')) {
            $pendingWastes = StockEntry::with(['product'])
                ->where('type', 'waste')
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        // --- Detailed Today's Table Inventory Report for Inventory Staff and Admin ---
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

        return view('livewire.admin.inventory', [
            'products'       => $products,
            'inventoryData'  => $inventoryData,
            'totalStockInShisha'   => $totalStockInShisha,
            'totalConsumedShisha'  => $totalConsumedShisha,
            'totalWasteShisha'     => $totalWasteShisha,
            'totalRemainingShisha' => $totalRemainingShisha,
            'totalStockInAcc'      => $totalStockInAcc,
            'totalConsumedAcc'     => $totalConsumedAcc,
            'totalWasteAcc'        => $totalWasteAcc,
            'totalRemainingAcc'    => $totalRemainingAcc,
            'pendingWastes'  => $pendingWastes,
            
            // Pass table wise theft prevention report
            'tableReport' => $tableReport,
            'todayProductTotals' => $todayProductTotals,
        ])->layout('components.layouts.admin');
    }
}
