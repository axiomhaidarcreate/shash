<?php

namespace App\Livewire\Admin\Loyalty;

use Livewire\Component;
use App\Models\Customer;
use App\Models\LoyaltyOffer;
use App\Models\SubscriptionPackage;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Customers extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    
    public $customer_id, $name, $phone, $subscription_number, $loyalty_offer_id;
    public $subscription_package_id, $subscription_start_date;
    
    public $viewCustomer = null;
    public $isViewModalOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetFields();
        $this->subscription_number = $this->generateSubscriptionNumber();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }
    
    public function openViewModal($id)
    {
        $this->viewCustomer = Customer::with('loyaltyOffer')->findOrFail($id);
        $this->isViewModalOpen = true;
    }
    
    public function closeViewModal()
    {
        $this->isViewModalOpen = false;
        $this->viewCustomer = null;
    }

    public function resetFields()
    {
        $this->customer_id = null;
        $this->name = '';
        $this->phone = '';
        $this->subscription_number = '';
        $this->loyalty_offer_id = null;
        $this->subscription_package_id = null;
        $this->subscription_start_date = null;
    }

    public function generateSubscriptionNumber()
    {
        do {
            $number = mt_rand(100000, 999999);
        } while (Customer::where('subscription_number', $number)->exists());
        return (string) $number;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'subscription_number' => 'required|string|unique:customers,subscription_number,' . $this->customer_id,
            'loyalty_offer_id' => 'nullable|exists:loyalty_offers,id',
            'subscription_package_id' => 'nullable|exists:subscription_packages,id',
            'subscription_start_date' => 'nullable|date',
        ]);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'subscription_number' => $this->subscription_number,
            'loyalty_offer_id' => $this->loyalty_offer_id ?: null,
            'subscription_package_id' => $this->subscription_package_id ?: null,
        ];

        // If a new subscription package is assigned, update start and end dates
        if ($this->subscription_package_id) {
            $package = SubscriptionPackage::find($this->subscription_package_id);
            if ($package) {
                $startDate = $this->subscription_start_date ? \Carbon\Carbon::parse($this->subscription_start_date) : now();
                $data['subscription_start_date'] = $startDate->format('Y-m-d');
                $data['subscription_end_date'] = $startDate->copy()->addDays($package->duration_days)->format('Y-m-d');
            }
        }

        Customer::updateOrCreate(['id' => $this->customer_id], $data);

        $this->dispatch('notify', ['message' => 'تم حفظ بيانات العميل بنجاح', 'type' => 'success']);
        $this->closeModal();
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customer_id = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->subscription_number = $customer->subscription_number;
        $this->loyalty_offer_id = $customer->loyalty_offer_id;
        $this->subscription_package_id = $customer->subscription_package_id;
        $this->subscription_start_date = $customer->subscription_start_date;
        
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        Customer::find($id)->delete();
        $this->dispatch('notify', ['message' => 'تم حذف العميل', 'type' => 'success']);
    }

    public function render()
    {
        $customers = Customer::with(['loyaltyOffer', 'subscriptionPackage'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%')
            ->orWhere('subscription_number', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(15);
            
        $offers = LoyaltyOffer::all();
        $packages = SubscriptionPackage::where('is_active', true)->get();

        return view('livewire.admin.loyalty.customers', [
            'customers' => $customers,
            'offers' => $offers,
            'packages' => $packages
        ]);
    }
}
