<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PhysicalInventory;
use App\Models\PhysicalInventoryItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PhysicalInventoryManager extends Component
{
    public $search = '';
    public $filterPeriod = 'all'; 
    public $filterDate = '';
    
    public $inventory_date = '';
    public $notes = '';
    
    // Inventory Items
    public $inventoryItems = [];
    public $searchProduct = '';
    public $selectedProductCategory = '';

    public $isEditing = false;
    public $editId = null;
    
    public $viewMode = 'create'; // create, list, view

    protected $rules = [
        'inventory_date' => 'required|date',
        'notes' => 'nullable|string',
        'inventoryItems' => 'required|array|min:1',
        'inventoryItems.*.product_id' => 'required|exists:products,id',
        'inventoryItems.*.current_quantity' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->filterDate = now()->format('Y-m-d');
        $this->inventory_date = now()->format('Y-m-d');
        $this->viewMode = 'list';
    }

    public function resetFields()
    {
        $this->reset(['notes', 'isEditing', 'editId', 'inventoryItems', 'searchProduct', 'selectedProductCategory']);
        $this->inventory_date = now()->format('Y-m-d');
    }

    public function addProductToInventory($productId, $productName, $productUnit = '')
    {
        // Check if product is already in the list
        foreach ($this->inventoryItems as $item) {
            if ($item['product_id'] == $productId) {
                return; // Already added
            }
        }

        $product = Product::find($productId);
        
        // Calculate previous (system) quantity for this product
        // (Simplified logic: In - Out - Waste)
        $overallIn = \App\Models\StockEntry::where('product_id', $productId)->where('type', 'addition')->sum('quantity_grams');
        $overallWaste = \App\Models\StockEntry::where('product_id', $productId)->where('type', 'waste')->where('status', 'approved')->sum('quantity_grams');
        $overallOut = \App\Models\Order::where(function($q) use ($product) {
                $q->where('shisha_type', $product->name)
                  ->orWhere('shisha_type', $product->name . ' (رأس فقط)')
                  ->orWhere('shisha_type', 'رأس ' . $product->name);
            })
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('SUM(quantity * grams_per_unit) as total_grams')
            ->value('total_grams') ?? 0;
            
        $compositeLinks = \App\Models\ProductIngredient::where('ingredient_id', $productId)->with('product')->get();
        foreach ($compositeLinks as $link) {
            if ($link->product) {
                $parentOverallOut = \App\Models\Order::where('shisha_type', $link->product->name)
                    ->whereNotIn('status', ['cancelled'])
                    ->sum('quantity');
                $overallOut += ($parentOverallOut * $link->quantity);
            }
        }
        
        $systemQty = $overallIn - $overallOut - $overallWaste;

        // Calculate daily consumption for the inventory date
        $inventoryDate = Carbon::parse($this->inventory_date);
        $dayStart = $inventoryDate->copy()->startOfDay();
        $dayEnd = $inventoryDate->copy()->endOfDay();

        $dailyOrdersOut = \App\Models\Order::where(function($q) use ($product) {
                $q->where('shisha_type', $product->name)
                  ->orWhere('shisha_type', $product->name . ' (رأس فقط)')
                  ->orWhere('shisha_type', 'رأس ' . $product->name);
            })
            ->whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->selectRaw('SUM(quantity * grams_per_unit) as total_grams')
            ->value('total_grams') ?? 0;

        $dailyWaste = \App\Models\StockEntry::where('product_id', $productId)
            ->where('type', 'waste')
            ->where('status', 'approved')
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->sum('quantity_grams');

        // Composite links daily consumption
        foreach ($compositeLinks as $link) {
            if ($link->product) {
                $parentDailyOut = \App\Models\Order::where('shisha_type', $link->product->name)
                    ->whereNotIn('status', ['cancelled'])
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->sum('quantity');
                $dailyOrdersOut += ($parentDailyOut * $link->quantity);
            }
        }

        $systemDailyConsumed = $dailyOrdersOut + $dailyWaste;

        $this->inventoryItems[] = [
            'product_id' => $productId,
            'product_name' => $productName,
            'unit' => $productUnit,
            'previous_quantity' => $systemQty,
            'system_daily_consumed' => $systemDailyConsumed,
            'actual_daily_consumed' => 0,
            'current_quantity' => 0,
        ];
        
        $this->searchProduct = ''; // Reset search
    }

    public function updateInventoryItem($index, $field, $value)
    {
        if (isset($this->inventoryItems[$index])) {
            $this->inventoryItems[$index][$field] = $value;
        }
    }

    public function removeInventoryItem($index)
    {
        unset($this->inventoryItems[$index]);
        $this->inventoryItems = array_values($this->inventoryItems);
    }

    public function save()
    {
        $this->validate();

        if (count($this->inventoryItems) === 0) {
            session()->flash('error', 'يجب إضافة منتج واحد على الأقل لجرد.');
            return;
        }

        if ($this->isEditing) {
            $inventory = PhysicalInventory::find($this->editId);
            
            $inventory->update([
                'date' => $this->inventory_date,
                'notes' => $this->notes,
            ]);
            
            // Clear old items and add new ones
            $inventory->items()->delete();
            $this->saveInventoryItems($inventory);
            
            session()->flash('success', 'تم تعديل الجرد بنجاح');
            $this->viewMode = 'list';
        } else {
            $inventory = PhysicalInventory::create([
                'user_id' => Auth::id(),
                'date' => $this->inventory_date,
                'notes' => $this->notes,
                'status' => 'draft',
            ]);

            $this->saveInventoryItems($inventory);
            
            session()->flash('success', 'تم حفظ الجرد بنجاح');
            $this->viewMode = 'list';
        }
    }

    private function saveInventoryItems($inventory)
    {
        foreach ($this->inventoryItems as $item) {
            $prev = is_numeric($item['previous_quantity']) ? (float)$item['previous_quantity'] : 0;
            $sysDaily = is_numeric($item['system_daily_consumed']) ? (float)$item['system_daily_consumed'] : 0;
            $actDaily = is_numeric($item['actual_daily_consumed']) ? (float)$item['actual_daily_consumed'] : 0;
            $curr = ($prev + $sysDaily) - $actDaily;

            PhysicalInventoryItem::create([
                'physical_inventory_id' => $inventory->id,
                'product_id' => $item['product_id'],
                'unit' => $item['unit'] ?? null,
                'previous_quantity' => $prev,
                'system_daily_consumed' => $sysDaily,
                'actual_daily_consumed' => $actDaily,
                'current_quantity' => $curr,
            ]);
        }
    }

    public function edit($id)
    {
        $inventory = PhysicalInventory::with('items.product')->find($id);
        $this->editId = $inventory->id;
        $this->inventory_date = $inventory->date->format('Y-m-d');
        $this->notes = $inventory->notes;
        
        $this->inventoryItems = [];
        foreach ($inventory->items as $item) {
            $this->inventoryItems[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product ? $item->product->name : 'منتج محذوف',
                'unit' => $item->unit ?? ($item->product ? $item->product->unit : ''),
                'previous_quantity' => $item->previous_quantity,
                'system_daily_consumed' => $item->system_daily_consumed ?? 0,
                'actual_daily_consumed' => $item->actual_daily_consumed ?? 0,
                'current_quantity' => $item->current_quantity,
            ];
        }

        $this->isEditing = true;
        $this->viewMode = 'create';
    }

    public function view($id)
    {
        $this->edit($id);
        $this->viewMode = 'view';
    }

    public function approve($id)
    {
        if (!Auth::user()->hasRole('admin')) return;

        $inventory = PhysicalInventory::with('items.product')->find($id);
        if ($inventory && $inventory->status !== 'approved') {
            
            foreach ($inventory->items as $item) {
                $diff = $item->current_quantity - $item->previous_quantity;
                
                if ($diff > 0) {
                    // Added stock (found more than expected)
                    \App\Models\StockEntry::create([
                        'product_id' => $item->product_id,
                        'quantity_grams' => $diff,
                        'type' => 'addition',
                        'status' => 'approved',
                        'notes' => 'تسوية جرد رقم: ' . $inventory->id,
                        'created_by' => Auth::id(),
                    ]);
                } elseif ($diff < 0) {
                    // Wasted/Lost stock (found less than expected)
                    \App\Models\StockEntry::create([
                        'product_id' => $item->product_id,
                        'quantity_grams' => abs($diff),
                        'type' => 'waste',
                        'status' => 'approved',
                        'notes' => 'تسوية جرد رقم: ' . $inventory->id,
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            $inventory->update(['status' => 'approved']);
            session()->flash('success', 'تم اعتماد الجرد وتحديث المخزون بنجاح.');
            $this->viewMode = 'list';
        }
    }

    public function delete($id)
    {
        if (!Auth::user()->hasRole('admin')) return;
        
        $inventory = PhysicalInventory::find($id);
        if ($inventory && $inventory->status !== 'approved') {
            $inventory->delete();
            session()->flash('success', 'تم حذف الجرد بنجاح');
        } else {
            session()->flash('error', 'لا يمكن حذف جرد معتمد.');
        }
    }

    public function render()
    {
        $query = PhysicalInventory::with(['user', 'items'])->orderBy('created_at', 'desc');

        if ($this->filterPeriod !== 'all') {
            $baseDate = $this->filterDate ? Carbon::parse($this->filterDate) : Carbon::today();
            
            if ($this->filterPeriod === 'daily') {
                $query->whereDate('created_at', $baseDate);
            } elseif ($this->filterPeriod === 'weekly') {
                $query->whereBetween('created_at', [$baseDate->copy()->startOfWeek(), $baseDate->copy()->endOfWeek()]);
            } elseif ($this->filterPeriod === 'monthly') {
                $query->whereMonth('created_at', $baseDate->month)->whereYear('created_at', $baseDate->year);
            }
        }

        $inventories = $query->get();
        $categories = \App\Models\Category::all();

        // Product search query
        $productQuery = \App\Models\Product::query();
        if ($this->selectedProductCategory) {
            $productQuery->where('category', $this->selectedProductCategory);
        }
        if ($this->searchProduct) {
            $productQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchProduct . '%')
                  ->orWhere('sku', 'like', '%' . $this->searchProduct . '%');
            });
        }
        $availableProducts = $productQuery->take(20)->get();

        return view('livewire.admin.physical-inventory-manager', [
            'inventories' => $inventories,
            'categories' => $categories,
            'availableProducts' => $availableProducts,
        ])->layout('components.layouts.admin', ['title' => 'الجرد الفعلي']);
    }
}
