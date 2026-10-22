<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SubscriptionPackage;
use App\Models\Product;

class AdminSubscriptionPackages extends Component
{
    public $name = '';
    public $price = 0;
    public $duration_days = 30;
    public $daily_order_limit = 1;
    public $included_products = [];
    public $is_active = true;
    public $productSearch = '';

    public $editId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'duration_days' => 'required|integer|min:1',
        'daily_order_limit' => 'required|integer|min:1',
        'included_products' => 'required|array|min:1',
    ];

    public function createPackage()
    {
        $this->validate();

        SubscriptionPackage::create([
            'name' => $this->name,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'daily_order_limit' => $this->daily_order_limit,
            'included_products' => $this->included_products,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'تم إنشاء الباقة بنجاح!');
        $this->resetForm();
    }

    public function editPackage($id)
    {
        $package = SubscriptionPackage::find($id);
        if ($package) {
            $this->editId = $package->id;
            $this->name = $package->name;
            $this->price = $package->price;
            $this->duration_days = $package->duration_days;
            $this->daily_order_limit = $package->daily_order_limit;
            $this->included_products = is_array($package->included_products) ? $package->included_products : [];
            $this->is_active = $package->is_active;
        }
    }

    public function updatePackage()
    {
        $this->validate();

        $package = SubscriptionPackage::find($this->editId);
        if ($package) {
            $package->update([
                'name' => $this->name,
                'price' => $this->price,
                'duration_days' => $this->duration_days,
                'daily_order_limit' => $this->daily_order_limit,
                'included_products' => $this->included_products,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'تم تحديث الباقة بنجاح!');
            $this->resetForm();
        }
    }

    public function deletePackage($id)
    {
        $package = SubscriptionPackage::find($id);
        if ($package) {
            // Check if customers are using this? For now just soft disable or delete
            if (\App\Models\Customer::where('subscription_package_id', $id)->exists()) {
                session()->flash('error', 'لا يمكن حذف هذه الباقة لأن هناك عملاء مشتركين بها. يمكنك تعطيلها بدلاً من الحذف.');
                return;
            }
            $package->delete();
            session()->flash('success', 'تم حذف الباقة بنجاح!');
        }
    }

    public function toggleProduct($productName)
    {
        if (in_array($productName, $this->included_products)) {
            $this->included_products = array_diff($this->included_products, [$productName]);
        } else {
            $this->included_products[] = $productName;
        }
    }

    public function resetForm()
    {
        $this->reset(['editId', 'name', 'price', 'duration_days', 'daily_order_limit', 'included_products', 'is_active', 'productSearch']);
    }

    public function render()
    {
        $packages = SubscriptionPackage::orderBy('created_at', 'desc')->get();
        
        $productsQuery = Product::where('is_active', true);
        if (!empty(trim($this->productSearch))) {
            $productsQuery->where('name', 'like', '%' . trim($this->productSearch) . '%');
        }
        $products = $productsQuery->take(30)->get();

        return view('livewire.admin-subscription-packages', [
            'packages' => $packages,
            'products' => $products,
        ])->layout('components.layouts.admin', ['title' => 'باقات الاشتراك']);
    }
}
