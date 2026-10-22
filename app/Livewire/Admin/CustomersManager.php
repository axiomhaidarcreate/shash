<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Customer;
use App\Models\LoyaltyOffer;
use App\Models\SubscriptionPackage;
use App\Models\Event;
use App\Models\EventBooking;
use App\Models\MessageTemplate;
use App\Models\ScheduledMessage;
use App\Models\CustomerNotification;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
class CustomersManager extends Component
{
    use WithPagination, WithFileUploads;

    public function mount()
    {
        if (request()->has('market_event')) {
            $this->broadcast_target_type = 'all';
            $this->market_event_id = request('market_event');
            $event = \App\Models\Event::find($this->market_event_id);
            if ($event) {
                $this->market_event_title = $event->title;
            }
            $this->isBroadcastModalOpen = true;
        }
    }

    // Filters
    public $search = '';
    public $subscriptionFilter = '';
    public $loyaltyFilter = '';
    public $nationalityFilter = '';
    public $eventFilter = '';

    // Customer Form Modal State
    public $isCustomerModalOpen = false;
    public $customer_id = null;
    public $name = '';
    public $phone = '';
    public $password = '';
    public $gender = 'ذكر';
    public $nationality = 'سعودي';
    public $subscription_number = '';
    public $loyalty_offer_id = null;
    public $subscription_package_id = null;
    public $subscription_start_date = null;

    // Customer View Drawer State
    public $isViewModalOpen = false;
    public $viewCustomer = null;

    // Message Templates State
    public $isTemplateModalOpen = false;
    public $template_id = null;
    public $template_title = '';
    public $template_content = '';
    public $template_attachment = null;

    // Active Template State
    public $active_template_id = null;
    public $active_template_title = '';
    public $active_template_content = '';
    public $active_template_attachment = null;

    // Custom Messages State
    public $isCustomMessageModalOpen = false;
    public $custom_message_nationality = '';
    public $custom_message_customers = []; // Array of selected customer IDs
    public $available_customers_for_custom_message = [];
    public $selectAllCustomers = false;

    // Broadcast Messaging State
    public $isBroadcastModalOpen = false;
    public $broadcast_target_type = 'all'; // all, nationality, event, subscribed, loyalty
    public $broadcast_target_value = '';
    public $broadcast_template_id = '';
    public $broadcast_title = '';
    public $broadcast_content = '';
    public $broadcast_attachment = null;
    public $market_event_id = null;
    public $market_event_title = '';

    // Scheduled Messaging State
    public $isScheduledModalOpen = false;
    public $scheduled_target_type = 'all';
    public $scheduled_target_value = '';
    public $scheduled_title = '';
    public $scheduled_content = '';
    public $scheduled_datetime = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSubscriptionFilter() { $this->resetPage(); }
    public function updatingLoyaltyFilter() { $this->resetPage(); }
    public function updatingNationalityFilter() { $this->resetPage(); }
    public function updatingEventFilter() { $this->resetPage(); }

    // Reset Forms
    public function resetCustomerFields()
    {
        $this->customer_id = null;
        $this->name = '';
        $this->phone = '';
        $this->password = '';
        $this->gender = 'ذكر';
        $this->nationality = 'سعودي';
        $this->subscription_number = $this->generateSubscriptionNumber();
        $this->loyalty_offer_id = null;
        $this->subscription_package_id = null;
        $this->subscription_start_date = null;
    }

    public function generateSubscriptionNumber()
    {
        do {
            $number = 'CUST-' . mt_rand(10000, 99999);
        } while (Customer::where('subscription_number', $number)->exists());
        return $number;
    }

    public function openCustomerModal()
    {
        $this->resetCustomerFields();
        $this->isCustomerModalOpen = true;
    }

    public function closeCustomerModal()
    {
        $this->isCustomerModalOpen = false;
        $this->resetCustomerFields();
    }

    public function editCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customer_id = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->gender = $customer->gender ?? 'ذكر';
        $this->nationality = $customer->nationality ?? 'سعودي';
        $this->subscription_number = $customer->subscription_number;
        $this->loyalty_offer_id = $customer->loyalty_offer_id;
        $this->subscription_package_id = $customer->subscription_package_id;
        $this->subscription_start_date = $customer->subscription_start_date;
        $this->password = '';

        $this->isCustomerModalOpen = true;
    }

    public function saveCustomer()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone,' . $this->customer_id,
            'gender' => 'required|string',
            'nationality' => 'nullable|string|max:255',
            'subscription_number' => 'required|string|unique:customers,subscription_number,' . $this->customer_id,
            'loyalty_offer_id' => 'nullable|exists:loyalty_offers,id',
            'subscription_package_id' => 'nullable|exists:subscription_packages,id',
            'subscription_start_date' => 'nullable|date',
            'password' => $this->customer_id ? 'nullable|min:6' : 'required|min:6',
        ]);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'nationality' => $this->nationality,
            'subscription_number' => $this->subscription_number,
            'loyalty_offer_id' => $this->loyalty_offer_id ?: null,
            'subscription_package_id' => $this->subscription_package_id ?: null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->subscription_package_id) {
            $package = SubscriptionPackage::find($this->subscription_package_id);
            if ($package) {
                $startDate = $this->subscription_start_date ? Carbon::parse($this->subscription_start_date) : now();
                $data['subscription_start_date'] = $startDate->format('Y-m-d');
                $data['subscription_end_date'] = $startDate->copy()->addDays($package->duration_days)->format('Y-m-d');
            }
        }

        Customer::updateOrCreate(['id' => $this->customer_id], $data);

        $this->dispatch('notify', ['message' => 'تم حفظ بيانات العميل بنجاح', 'type' => 'success']);
        $this->closeCustomerModal();
    }

    public function deleteCustomer($id)
    {
        Customer::find($id)?->delete();
        $this->dispatch('notify', ['message' => 'تم حذف العميل بنجاح', 'type' => 'success']);
    }

    public function openViewModal($id)
    {
        $this->viewCustomer = Customer::with(['loyaltyOffer', 'subscriptionPackage', 'eventBookings.event'])->findOrFail($id);
        $this->isViewModalOpen = true;
    }

    public function closeViewModal()
    {
        $this->isViewModalOpen = false;
        $this->viewCustomer = null;
    }

    // Message Templates Functions
    public function openTemplateModal()
    {
        $this->resetTemplateFields();
        $this->isTemplateModalOpen = true;
    }

    public function resetTemplateFields()
    {
        $this->template_id = null;
        $this->template_title = '';
        $this->template_content = '';
        $this->template_attachment = null;
    }

    public function saveTemplate()
    {
        $this->validate([
            'template_title' => 'required|string|max:255',
            'template_content' => 'required|string',
            'template_attachment' => 'nullable|file|max:5120', // 5MB max
        ]);

        $data = [
            'title' => $this->template_title, 
            'content' => $this->template_content
        ];

        if ($this->template_attachment) {
            $path = $this->template_attachment->store('templates', 'public');
            $data['attachment'] = $path;
        }

        MessageTemplate::updateOrCreate(
            ['id' => $this->template_id],
            $data
        );

        $this->resetTemplateFields();
        $this->dispatch('notify', ['message' => 'تم حفظ قالب الرسالة بنجاح', 'type' => 'success']);
    }

    public function editTemplate($id)
    {
        $tpl = MessageTemplate::findOrFail($id);
        $this->template_id = $tpl->id;
        $this->template_title = $tpl->title;
        $this->template_content = $tpl->content;
        // Don't bind the old attachment to the file input to avoid validation issues, 
        // users can upload a new one if they want to replace it.
        $this->template_attachment = null;
    }

    public function deleteTemplate($id)
    {
        MessageTemplate::find($id)?->delete();
        $this->dispatch('notify', ['message' => 'تم حذف القالب بنجاح', 'type' => 'success']);
    }

    public function applyTemplate($id)
    {
        $tpl = MessageTemplate::find($id);
        if ($tpl) {
            $this->active_template_id = $tpl->id;
            $this->active_template_title = $tpl->title;
            $this->active_template_content = $tpl->content;
            $this->active_template_attachment = $tpl->attachment;
            
            $this->dispatch('notify', ['message' => 'تم تعيين القالب كقالب نشط', 'type' => 'info']);
        }
    }

    // Custom Messages Functions
    public function openCustomMessageModal()
    {
        $this->isCustomMessageModalOpen = true;
        $this->custom_message_nationality = '';
        $this->custom_message_customers = [];
        $this->available_customers_for_custom_message = [];
        $this->selectAllCustomers = false;
    }

    public function updatedCustomMessageNationality()
    {
        if ($this->custom_message_nationality) {
            $this->available_customers_for_custom_message = Customer::where('nationality', $this->custom_message_nationality)
                ->orderBy('name')
                ->get()
                ->toArray();
        } else {
            $this->available_customers_for_custom_message = [];
        }
        $this->custom_message_customers = [];
        $this->selectAllCustomers = false;
    }

    public function updatedSelectAllCustomers($value)
    {
        if ($value) {
            $this->custom_message_customers = array_column($this->available_customers_for_custom_message, 'id');
        } else {
            $this->custom_message_customers = [];
        }
    }

    public function sendCustomMessage()
    {
        if (!$this->active_template_id) {
            $this->dispatch('notify', ['message' => 'يرجى اختيار قالب نشط أولاً', 'type' => 'warning']);
            return;
        }

        if (empty($this->custom_message_customers)) {
            $this->dispatch('notify', ['message' => 'يرجى تحديد عميل واحد على الأقل', 'type' => 'warning']);
            return;
        }

        $count = 0;
        $fcmTokens = [];
        foreach ($this->custom_message_customers as $customerId) {
            CustomerNotification::create([
                'customer_id' => $customerId,
                'title' => $this->active_template_title,
                'message' => $this->active_template_content,
                'type' => 'general',
                'is_read' => false,
                'attachment' => $this->active_template_attachment,
            ]);
            
            $customer = Customer::find($customerId);
            if ($customer && !empty($customer->fcm_token)) {
                $fcmTokens[] = $customer->fcm_token;
            }
            
            $count++;
        }

        if (count($fcmTokens) > 0) {
            \App\Services\FirebasePushService::send(
                $fcmTokens,
                $this->active_template_title,
                $this->active_template_content,
                ['type' => 'general']
            );
        }

        $this->isCustomMessageModalOpen = false;
        $this->dispatch('notify', ['message' => "تم إرسال الرسالة بنجاح إلى {$count} عميل", 'type' => 'success']);
    }

    // Broadcast Messaging Functions
    public function openBroadcastModal()
    {
        $this->broadcast_template_id = '';
        $this->broadcast_title = '';
        $this->broadcast_content = '';
        $this->broadcast_attachment = null;
        $this->broadcast_target_type = 'all';
        $this->broadcast_target_value = '';
        $this->market_event_id = null;
        $this->market_event_title = '';
        $this->isBroadcastModalOpen = true;
    }

    public function updatedBroadcastTemplateId($value)
    {
        if ($value) {
            $tpl = MessageTemplate::find($value);
            if ($tpl) {
                $this->broadcast_title = $tpl->title;
                $this->broadcast_content = $tpl->content;
                $this->broadcast_attachment = $tpl->attachment;
            }
        } else {
            $this->broadcast_title = '';
            $this->broadcast_content = '';
            $this->broadcast_attachment = null;
        }
    }

    public function sendBroadcast()
    {
        $this->validate([
            'broadcast_template_id' => 'required',
            'broadcast_title' => 'required|string|max:255',
            'broadcast_content' => 'required|string',
            'broadcast_target_type' => 'required|string',
        ], [
            'broadcast_template_id.required' => 'يرجى اختيار قالب رسالة.'
        ]);

        $query = Customer::query();

        if ($this->broadcast_target_type === 'nationality' && $this->broadcast_target_value) {
            $query->where('nationality', $this->broadcast_target_value);
        } elseif ($this->broadcast_target_type === 'event' && $this->broadcast_target_value) {
            $query->whereHas('eventBookings', function($q) {
                $q->where('event_id', $this->broadcast_target_value);
            });
        } elseif ($this->broadcast_target_type === 'subscribed') {
            $query->whereNotNull('subscription_package_id')
                  ->where('subscription_end_date', '>=', now()->format('Y-m-d'));
        } elseif ($this->broadcast_target_type === 'loyalty') {
            $query->where('total_points', '>', 0);
        }

        $customers = $query->get();

        if ($customers->isEmpty()) {
            $this->dispatch('notify', ['message' => 'لم يتم العثور على عملاء ضمن الفئة المحددة', 'type' => 'warning']);
            return;
        }

        $count = 0;
        $fcmTokens = [];
        
        $notifType = $this->market_event_id ? 'event' : 'general';
        $pushData = ['type' => $notifType];
        if ($this->market_event_id) {
            $pushData['event_id'] = (string)$this->market_event_id;
        }

        foreach ($customers as $customer) {
            CustomerNotification::create([
                'customer_id' => $customer->id,
                'title' => $this->broadcast_title,
                'message' => $this->broadcast_content,
                'type' => $notifType,
                'is_read' => false,
                'attachment' => $this->broadcast_attachment,
            ]);
            
            if (!empty($customer->fcm_token)) {
                $fcmTokens[] = $customer->fcm_token;
            }
            
            $count++;
        }

        if (count($fcmTokens) > 0) {
            \App\Services\FirebasePushService::send(
                $fcmTokens,
                $this->broadcast_title,
                $this->broadcast_content,
                $pushData
            );
        }

        $this->isBroadcastModalOpen = false;
        $this->dispatch('notify', ['message' => "تم إرسال الرسالة الجماعية بنجاح إلى {$count} عميل", 'type' => 'success']);
    }

    // Scheduled Messaging Functions
    public function openScheduledModal()
    {
        $this->scheduled_title = '';
        $this->scheduled_content = '';
        $this->scheduled_target_type = 'all';
        $this->scheduled_target_value = '';
        $this->scheduled_datetime = now()->addHour()->format('Y-m-d\TH:i');
        $this->isScheduledModalOpen = true;
    }

    public function saveScheduledMessage()
    {
        $this->validate([
            'scheduled_title' => 'required|string|max:255',
            'scheduled_content' => 'required|string',
            'scheduled_target_type' => 'required|string',
            'scheduled_datetime' => 'required|date|after:now',
        ]);

        ScheduledMessage::create([
            'title' => $this->scheduled_title,
            'content' => $this->scheduled_content,
            'target_type' => $this->scheduled_target_type,
            'target_value' => $this->scheduled_target_value,
            'scheduled_at' => Carbon::parse($this->scheduled_datetime),
            'status' => 'pending',
        ]);

        $this->isScheduledModalOpen = false;
        $this->dispatch('notify', ['message' => 'تم جدولة الرسالة التلقائية بنجاح', 'type' => 'success']);
    }

    public function cancelScheduledMessage($id)
    {
        $msg = ScheduledMessage::find($id);
        if ($msg && $msg->status === 'pending') {
            $msg->update(['status' => 'cancelled']);
            $this->dispatch('notify', ['message' => 'تم إلغاء الرسالة المجدولة', 'type' => 'info']);
        }
    }

    public function render()
    {
        $query = Customer::with(['loyaltyOffer', 'subscriptionPackage', 'eventBookings']);

        // Search Filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('subscription_number', 'like', '%' . $this->search . '%');
            });
        }

        // Subscription Filter
        if ($this->subscriptionFilter === 'active') {
            $query->whereNotNull('subscription_package_id')
                  ->where('subscription_end_date', '>=', now()->format('Y-m-d'));
        } elseif ($this->subscriptionFilter === 'inactive') {
            $query->where(function($q) {
                $q->whereNull('subscription_package_id')
                  ->orWhere('subscription_end_date', '<', now()->format('Y-m-d'));
            });
        }

        // Loyalty Filter
        if ($this->loyaltyFilter === 'has_points') {
            $query->where('total_points', '>', 0);
        } elseif ($this->loyaltyFilter === 'has_offer') {
            $query->whereNotNull('loyalty_offer_id');
        }

        // Nationality Filter
        if ($this->nationalityFilter) {
            $query->where('nationality', $this->nationalityFilter);
        }

        // Event Attendance Filter
        if ($this->eventFilter) {
            $query->whereHas('eventBookings', function($q) {
                $q->where('event_id', $this->eventFilter);
            });
        }

        $customers = $query->orderBy('id', 'desc')->paginate(15);

        // Fetch distinct nationalities for filter dropdown
        $nationalitiesList = Customer::whereNotNull('nationality')
            ->select('nationality')
            ->distinct()
            ->pluck('nationality');

        $offers = LoyaltyOffer::all();
        $packages = SubscriptionPackage::where('is_active', true)->get();
        $events = Event::where('is_active', true)->get();
        $todayEvents = Event::where('is_active', true)->whereDate('event_date', Carbon::today())->get();
        $templates = MessageTemplate::orderBy('id', 'desc')->get();
        $scheduledMessages = ScheduledMessage::orderBy('id', 'desc')->take(10)->get();

        return view('livewire.admin.customers-manager', [
            'customers' => $customers,
            'nationalitiesList' => $nationalitiesList,
            'offers' => $offers,
            'packages' => $packages,
            'events' => $events,
            'todayEvents' => $todayEvents,
            'templates' => $templates,
            'scheduledMessages' => $scheduledMessages,
        ]);
    }
}
