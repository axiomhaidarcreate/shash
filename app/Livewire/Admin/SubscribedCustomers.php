<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\SubscriptionPackage;
use App\Services\MessagingService;
use Carbon\Carbon;

class SubscribedCustomers extends Component
{
    use WithPagination;

    public $search = '';
    public $filterPackage = '';

    // Message sending state
    public $messageText = '';
    public $selectedCustomerId = null;

    // Extend subscription state
    public $extendDays = 30;

    protected $queryString = ['search', 'filterPackage'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPackage()
    {
        $this->resetPage();
    }

    public function openMessageModal($customerId)
    {
        $this->selectedCustomerId = $customerId;
        $customer = Customer::find($customerId);
        if ($customer) {
            $this->messageText = "مرحباً {$customer->name}، نود تذكيرك بأن اشتراكك في باقة " . ($customer->subscriptionPackage->name ?? '') . " سينتهي قريباً. نتمنى لك يوماً سعيداً!";
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required|string|min:5',
        ]);

        $customer = Customer::find($this->selectedCustomerId);
        if ($customer && $customer->phone) {
            $result = MessagingService::send($customer->phone, $this->messageText);
            
            if ($result['success']) {
                session()->flash('success', 'تم إرسال الرسالة بنجاح.');
            } else {
                session()->flash('error', $result['message']);
            }
        } else {
            session()->flash('error', 'العميل غير موجود أو لا يملك رقم هاتف.');
        }

        $this->reset(['selectedCustomerId', 'messageText']);
    }

    public function extendSubscription($customerId)
    {
        $customer = Customer::find($customerId);
        if ($customer && $customer->subscription_end_date) {
            $endDate = Carbon::parse($customer->subscription_end_date);
            // If already expired, start from today, else extend from current end date
            if ($endDate->isPast()) {
                $endDate = now();
            }
            $customer->update([
                'subscription_end_date' => $endDate->addDays($this->extendDays)->toDateString()
            ]);
            session()->flash('success', 'تم تمديد الاشتراك للعميل بنجاح.');
        }
    }

    public function cancelSubscription($customerId)
    {
        $customer = Customer::find($customerId);
        if ($customer) {
            $customer->update([
                'subscription_package_id' => null,
                'subscription_start_date' => null,
                'subscription_end_date' => null,
                'daily_subscription_usage_count' => 0
            ]);
            session()->flash('success', 'تم إلغاء اشتراك العميل.');
        }
    }

    public function render()
    {
        $query = Customer::whereNotNull('subscription_package_id')
                    ->with('subscriptionPackage');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('subscription_number', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterPackage)) {
            $query->where('subscription_package_id', $this->filterPackage);
        }

        $customers = $query->orderBy('subscription_end_date', 'asc')->paginate(15);
        $packages = SubscriptionPackage::all();

        return view('livewire.admin.subscribed-customers', [
            'customers' => $customers,
            'packages' => $packages,
        ])->layout('components.layouts.admin');
    }
}
