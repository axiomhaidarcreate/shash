<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class Products extends Component
{
    public $name = '';
    public $category = '';
    public $editingId = null;
    public $editName = '';
    public $editCategory = '';
    public $showForm = false;

    // Categories management state
    public $newCategoryName = '';
    public $editingCategoryId = null;
    public $editCategoryName = '';
    public $showCategoryForm = false;

    public function mount()
    {
        $firstCat = Category::first();
        if ($firstCat) {
            $this->category = $firstCat->name;
        } else {
            $this->category = 'معسل';
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
        ]);

        Product::create([
            'name' => $this->name,
            'category' => $this->category,
            'is_active' => true,
            'grams_per_order' => ($this->category === 'معسل') ? 20 : 1,
        ]);

        $this->reset(['name', 'showForm']);
        $firstCat = Category::first();
        $this->category = $firstCat ? $firstCat->name : 'معسل';
        session()->flash('success', 'تم إضافة المنتج بنجاح!');
    }

    public function startEdit($id)
    {
        $product = Product::find($id);
        $this->editingId = $id;
        $this->editName = $product->name;
        $this->editCategory = $product->category;
    }

    public function updateProduct()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editCategory' => 'required|string',
        ]);

        $product = Product::find($this->editingId);
        $data = [
            'name' => $this->editName,
            'category' => $this->editCategory,
        ];
        
        if ($this->editCategory !== 'معسل') {
            $data['grams_per_order'] = 1;
        }

        $product->update($data);

        $this->editingId = null;
        session()->flash('success', 'تم تحديث المنتج بنجاح!');
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

            // Update all products in this category to the new category name
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
            // Find another category to move products to, or default to 'أخرى'
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

    public function toggleCategoryVisibility($id)
    {
        $cat = Category::find($id);
        if ($cat) {
            $cat->is_visible = !$cat->is_visible;
            $cat->save();
            session()->flash('success', 'تم تعديل حالة ظهور التصنيف بنجاح!');
        }
    }

    public function render()
    {
        $products = Product::orderBy('category')->orderBy('sort_order')->orderBy('name')->get();
        $dbCategories = Category::orderBy('name')->get();

        return view('livewire.admin.products', [
            'products' => $products,
            'dbCategories' => $dbCategories,
        ])->layout('components.layouts.admin');
    }
}
