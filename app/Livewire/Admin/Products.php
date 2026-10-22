<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;

class Products extends Component
{
    use WithFileUploads;

    // Form fields
    public $productId = null;
    public $name = '';
    public $name_en = '';
    public $price = 0;
    public $category = '';
    public $image;
    public $existing_image = null;
    public $unit = 'حبة';
    public $deduct_from_inventory = true;
    public $is_composite = false;
    public $sku = '';
    public $tax_percentage = 15;
    public $target_kitchen = 'shisha';

    public $composite_ingredients = []; // [ ['ingredient_id' => '', 'quantity' => 1] ]
    public $modifier_groups = []; // For dynamic options (flavors, extras)

    public $showForm = false;

    // Categories management state
    public $newCategoryName = '';
    public $editingCategoryId = null;
    public $editCategoryName = '';
    public $showCategoryForm = false;
    public $selectedCategory = null;

    public function mount()
    {
        $firstCat = Category::first();
        $this->category = $firstCat ? $firstCat->name : 'معسل';
    }

    public function selectCategory($categoryName)
    {
        if ($this->selectedCategory === $categoryName) {
            $this->selectedCategory = null;
        } else {
            $this->selectedCategory = $categoryName;
        }
    }

    public function addIngredientRow()
    {
        $this->composite_ingredients[] = ['ingredient_id' => '', 'quantity' => 1];
    }

    public function removeIngredientRow($index)
    {
        unset($this->composite_ingredients[$index]);
        $this->composite_ingredients = array_values($this->composite_ingredients);
    }

    public function addModifierGroup()
    {
        $this->modifier_groups[] = [
            'id' => null,
            'name' => '',
            'is_required' => false,
            'is_multiple' => false,
            'modifiers' => []
        ];
    }

    public function removeModifierGroup($index)
    {
        unset($this->modifier_groups[$index]);
        $this->modifier_groups = array_values($this->modifier_groups);
    }

    public function addModifier($groupIndex)
    {
        $this->modifier_groups[$groupIndex]['modifiers'][] = [
            'id' => null,
            'name' => '',
            'price' => 0
        ];
    }

    public function removeModifier($groupIndex, $modifierIndex)
    {
        unset($this->modifier_groups[$groupIndex]['modifiers'][$modifierIndex]);
        $this->modifier_groups[$groupIndex]['modifiers'] = array_values($this->modifier_groups[$groupIndex]['modifiers']);
    }

    public function resetForm()
    {
        $this->reset([
            'productId', 'name', 'name_en', 'price', 'image', 'existing_image',
            'unit', 'deduct_from_inventory', 'is_composite', 'sku', 'tax_percentage',
            'target_kitchen', 'composite_ingredients', 'modifier_groups'
        ]);
        $firstCat = Category::first();
        $this->category = $firstCat ? $firstCat->name : 'معسل';
    }

    public function openAddForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editProduct($id)
    {
        $this->resetForm();
        $product = Product::with('ingredients')->find($id);
        if ($product) {
            $this->productId = $product->id;
            $this->name = $product->name;
            $this->name_en = $product->name_en;
            $this->price = $product->price;
            $this->category = $product->category;
            $this->existing_image = $product->image;
            $this->unit = $product->unit;
            $this->deduct_from_inventory = $product->deduct_from_inventory;
            $this->is_composite = $product->is_composite;
            $this->sku = $product->sku;
            $this->tax_percentage = $product->tax_percentage;
            $this->target_kitchen = $product->target_kitchen;

            $this->composite_ingredients = [];
            foreach ($product->ingredients as $ing) {
                $this->composite_ingredients[] = [
                    'ingredient_id' => $ing->id,
                    'quantity' => $ing->pivot->quantity
                ];
            }

            $this->modifier_groups = [];
            foreach ($product->modifierGroups()->with('modifiers')->get() as $group) {
                $modifiers = [];
                foreach ($group->modifiers as $mod) {
                    $modifiers[] = [
                        'id' => $mod->id,
                        'name' => $mod->name,
                        'price' => $mod->price
                    ];
                }
                $this->modifier_groups[] = [
                    'id' => $group->id,
                    'name' => $group->name,
                    'is_required' => (bool)$group->is_required,
                    'is_multiple' => (bool)$group->is_multiple,
                    'modifiers' => $modifiers
                ];
            }

            $this->showForm = true;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $this->productId,
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = $this->existing_image;
        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        $data = [
            'name' => $this->name,
            'name_en' => $this->name_en,
            'price' => $this->price,
            'category' => $this->category,
            'image' => $imagePath,
            'unit' => $this->unit,
            'deduct_from_inventory' => $this->deduct_from_inventory,
            'is_composite' => $this->is_composite,
            'sku' => $this->sku,
            'tax_percentage' => $this->tax_percentage,
            'target_kitchen' => $this->target_kitchen,
        ];

        // Grams per order logic (backward compatibility)
        if (in_array($this->category, ['معسل', 'الشيشة', 'الشيش', 'رؤوس الشيشة'])) {
            $data['grams_per_order'] = 20;
        } else {
            $data['grams_per_order'] = 1;
        }

        if ($this->productId) {
            $product = Product::find($this->productId);
            $product->update($data);
            $msg = 'تم تحديث المنتج بنجاح!';
        } else {
            $data['is_active'] = true;
            $product = Product::create($data);
            $msg = 'تم إضافة المنتج بنجاح!';
        }

        if ($this->is_composite) {
            $syncData = [];
            foreach ($this->composite_ingredients as $ing) {
                if (!empty($ing['ingredient_id']) && $ing['quantity'] > 0) {
                    $syncData[$ing['ingredient_id']] = ['quantity' => $ing['quantity']];
                }
            }
            $product->ingredients()->sync($syncData);
        } else {
            $product->ingredients()->sync([]);
        }

        // Save modifiers
        $existingGroupIds = [];
        foreach ($this->modifier_groups as $groupData) {
            if (empty(trim($groupData['name']))) continue;

            if (!empty($groupData['id'])) {
                $group = \App\Models\ModifierGroup::find($groupData['id']);
                if ($group) {
                    $group->update([
                        'name' => $groupData['name'],
                        'is_required' => $groupData['is_required'] ?? false,
                        'is_multiple' => $groupData['is_multiple'] ?? false
                    ]);
                }
            } else {
                $group = $product->modifierGroups()->create([
                    'name' => $groupData['name'],
                    'is_required' => $groupData['is_required'] ?? false,
                    'is_multiple' => $groupData['is_multiple'] ?? false
                ]);
            }
            if (isset($group)) {
                $existingGroupIds[] = $group->id;

                $existingModIds = [];
                if (isset($groupData['modifiers']) && is_array($groupData['modifiers'])) {
                    foreach ($groupData['modifiers'] as $modData) {
                        if (empty(trim($modData['name']))) continue;
                        
                        if (!empty($modData['id'])) {
                            $mod = \App\Models\ProductModifier::find($modData['id']);
                            if ($mod) {
                                $mod->update([
                                    'name' => $modData['name'],
                                    'price' => $modData['price'] ?: 0
                                ]);
                                $existingModIds[] = $mod->id;
                            }
                        } else {
                            $mod = $group->modifiers()->create([
                                'name' => $modData['name'],
                                'price' => $modData['price'] ?: 0
                            ]);
                            $existingModIds[] = $mod->id;
                        }
                    }
                }
                $group->modifiers()->whereNotIn('id', $existingModIds)->delete();
            }
        }
        $product->modifierGroups()->whereNotIn('id', $existingGroupIds)->delete();

        $this->resetForm();
        $this->showForm = false;
        session()->flash('success', $msg);
    }

    public function toggleActive($id)
    {
        $product = Product::find($id);
        $product->is_active = !$product->is_active;
        $product->save();
    }

    public function deleteProduct($id)
    {
        Product::destroy($id);
        session()->flash('success', 'تم حذف المنتج.');
    }

    // --- Dynamic Category Management ---

    public function addCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $this->newCategoryName,
            'is_visible' => true,
            'is_visible_in_products' => true,
        ]);

        $this->reset(['newCategoryName', 'showCategoryForm']);
        session()->flash('success', 'تم إضافة التصنيف بنجاح!');
    }

    public function startEditCategory($id)
    {
        $cat = Category::find($id);
        if ($cat) {
            $this->editingCategoryId = $id;
            $this->editCategoryName = $cat->name;
        }
    }

    public function updateCategory()
    {
        $this->validate([
            'editCategoryName' => 'required|string|max:255|unique:categories,name,' . $this->editingCategoryId,
        ]);

        $cat = Category::find($this->editingCategoryId);
        if ($cat) {
            $oldName = $cat->name;
            $cat->update([
                'name' => $this->editCategoryName,
            ]);

            Product::where('category', $oldName)->update([
                'category' => $this->editCategoryName,
            ]);

            $this->editingCategoryId = null;
            session()->flash('success', 'تم تحديث التصنيف بنجاح!');
        }
    }

    public function deleteCategory($id)
    {
        $cat = Category::find($id);
        if ($cat) {
            $otherCat = Category::where('id', '!=', $id)->first();
            if (!$otherCat) {
                $otherCat = Category::create(['name' => 'أخرى', 'is_visible' => true]);
            }
            
            Product::where('category', $cat->name)->update([
                'category' => $otherCat->name,
            ]);

            $cat->delete();
            $this->reset(['editingCategoryId']);
            session()->flash('success', 'تم حذف التصنيف بنجاح ونقل جميع منتجاته إلى ' . $otherCat->name);
        }
    }

    public function toggleCategoryVisibilityProducts($id)
    {
        $cat = Category::find($id);
        if ($cat) {
            $cat->is_visible_in_products = !$cat->is_visible_in_products;
            $cat->save();
            session()->flash('success', 'تم تعديل حالة ظهور التصنيف في المنتجات بنجاح!');
        }
    }

    public function toggleCategoryVisibility($id)
    {
        $cat = Category::find($id);
        if ($cat) {
            $cat->is_visible = !$cat->is_visible;
            $cat->save();
            session()->flash('success', 'تم تعديل حالة ظهور التصنيف بالطلبات بنجاح!');
        }
    }

    public function render()
    {
        $visibleProductCategories = Category::where('is_visible_in_products', true)->pluck('name')->toArray();
        $products = Product::whereIn('category', $visibleProductCategories)
                           ->orderBy('category')->orderBy('sort_order')->orderBy('name')->get();
        $dbCategories = Category::orderBy('name')->get();
        
        $allProducts = Product::orderBy('name')->get();

        $stockStats = [];
        if ($this->selectedCategory) {
            $catProducts = $products->where('category', $this->selectedCategory);
            foreach ($catProducts as $product) {
                $in = \App\Models\StockEntry::where('product_id', $product->id)->where('type', 'addition')->sum('quantity_grams');
                $waste = \App\Models\StockEntry::where('product_id', $product->id)->where('type', 'waste')->where('status', 'approved')->sum('quantity_grams');
                $outQuery = \App\Models\Order::where(function($q) use ($product) {
                    $q->where('shisha_type', $product->name)
                      ->orWhere('shisha_type', $product->name . ' (رأس فقط)')
                      ->orWhere('shisha_type', 'رأس ' . $product->name);
                })->whereNotIn('status', ['cancelled']);
                
                $out = (clone $outQuery)->selectRaw('SUM(quantity * grams_per_unit) as total_grams')->value('total_grams') ?? 0;
                $soldCount = (clone $outQuery)->sum('quantity');
                
                // Composite
                $compositeLinks = \App\Models\ProductIngredient::where('ingredient_id', $product->id)->with('product')->get();
                foreach ($compositeLinks as $link) {
                    if ($link->product) {
                        $parentOut = \App\Models\Order::where('shisha_type', $link->product->name)->whereNotIn('status', ['cancelled'])->sum('quantity');
                        $out += ($parentOut * $link->quantity);
                        $soldCount += $parentOut; // Approximation
                    }
                }
                
                $remaining = $in - $out - $waste;

                $stockStats[$product->id] = [
                    'remaining' => $remaining,
                    'soldCount' => $soldCount,
                ];
            }
        }

        return view('livewire.admin.products', [
            'products' => $products,
            'dbCategories' => $dbCategories,
            'allProducts' => $allProducts,
            'stockStats' => $stockStats,
        ])->layout('components.layouts.admin');
    }
}
