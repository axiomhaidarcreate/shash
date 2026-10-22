<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SiteLink;
use App\Models\Setting;
use App\Models\Event;
use App\Models\EventBooking;
use App\Models\Customer;
use App\Models\SubscriptionPackage;
use App\Models\LoyaltyOffer;
use App\Models\SubscriptionRequest;
use App\Models\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.public')]
class PublicSite extends Component
{
    public $site_name;
    public $site_bio;
    public $site_logo;
    public $site_cover_image;
    public $site_location_name;
    public $site_location_address;
    public $site_location_lat;
    public $site_location_lng;
    public $site_location_url;
    
    // Custom Texts
    public $site_announcements_title;
    public $site_location_title;
    public $site_footer_text;

    public $site_show_events;
    public $site_show_packages;

    // Customer Auth
    public $loggedInCustomer = null;
    public $isAuthModalOpen = false;
    public $authMode = 'login'; // 'login' or 'register'
    public $auth_name = '';
    public $auth_phone = '';
    public $auth_password = '';
    public $auth_gender = '';

    // Events Booking
    public $isEventsListModalOpen = false;
    public $isEventModalOpen = false;
    public $selectedEvent = null;
    public $booking_name = '';
    public $booking_phone = '';
    public $booking_gender = '';
    public $booking_table = ''; // The table number they search for
    public $booking_table_id = null;
    public $booking_chair_id = null;
    public $isTableSelectorModalOpen = false;

    // Subscriptions
    public $isSubListModalOpen = false;
    public $isSubModalOpen = false;
    public $isLoyaltySub = false;
    public $selectedPackageId = null;
    public $selectedLoyaltyId = null;
    public $sub_name = '';
    public $sub_phone = '';
    
    public function mount()
    {
        $this->site_name = Setting::where('key', 'site_name')->value('value') ?? 'لاونج الشيشة';
        $this->site_bio = Setting::where('key', 'site_bio')->value('value') ?? '';
        $this->site_logo = Setting::where('key', 'site_logo')->value('value') ?? '';
        $this->site_cover_image = Setting::where('key', 'site_cover_image')->value('value') ?? '';
        
        $this->site_location_name = Setting::where('key', 'site_location_name')->value('value') ?? '';
        $this->site_location_address = Setting::where('key', 'site_location_address')->value('value') ?? '';
        $this->site_location_lat = Setting::where('key', 'site_location_lat')->value('value') ?? '';
        $this->site_location_lng = Setting::where('key', 'site_location_lng')->value('value') ?? '';
        $this->site_location_url = Setting::where('key', 'site_location_url')->value('value') ?? '';

        $this->site_announcements_title = Setting::where('key', 'site_announcements_title')->value('value') ?? 'إعلانات وحفلات';
        $this->site_location_title = Setting::where('key', 'site_location_title')->value('value') ?? 'موقعنا';
        $this->site_footer_text = Setting::where('key', 'site_footer_text')->value('value') ?? 'تم تطوير النظام بواسطة م. مازن أحمد حيدر';

        $this->site_show_events = filter_var(Setting::where('key', 'site_show_events')->value('value') ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $this->site_show_packages = filter_var(Setting::where('key', 'site_show_packages')->value('value') ?? 'true', FILTER_VALIDATE_BOOLEAN);

        // Restore logged-in customer from session
        $customerId = session('public_customer_id');
        if ($customerId) {
            $this->loggedInCustomer = Customer::find($customerId);
        }
    }

    // ========== Customer Auth Methods ==========

    public function openAuthModal()
    {
        $this->authMode = 'login';
        $this->auth_name = '';
        $this->auth_phone = '';
        $this->auth_password = '';
        $this->auth_gender = '';
        $this->resetErrorBag();
        $this->isAuthModalOpen = true;
    }

    public function switchAuthMode($mode)
    {
        $this->authMode = $mode;
        $this->resetErrorBag();
    }

    public function customerLogin()
    {
        $this->validate([
            'auth_phone' => 'required|string',
            'auth_password' => 'required|string|min:4',
        ], [
            'auth_phone.required' => 'رقم الجوال مطلوب',
            'auth_password.required' => 'كلمة المرور مطلوبة',
            'auth_password.min' => 'كلمة المرور يجب أن تكون 4 أحرف على الأقل',
        ]);

        $customer = Customer::where('phone', $this->auth_phone)->first();

        if (!$customer || !$customer->password || !Hash::check($this->auth_password, $customer->password)) {
            $this->addError('auth_phone', 'رقم الجوال أو كلمة المرور غير صحيحة');
            return;
        }

        $this->loggedInCustomer = $customer;
        session(['public_customer_id' => $customer->id]);
        $this->isAuthModalOpen = false;
        $this->dispatch('notify', ['message' => 'مرحباً بك ' . $customer->name . '!', 'type' => 'success']);
    }

    public function customerRegister()
    {
        $this->validate([
            'auth_name' => 'required|string|max:255',
            'auth_phone' => 'required|string|max:50|unique:customers,phone',
            'auth_password' => 'required|string|min:4',
            'auth_gender' => 'required|in:male,female',
        ], [
            'auth_name.required' => 'الاسم مطلوب',
            'auth_phone.required' => 'رقم الجوال مطلوب',
            'auth_phone.unique' => 'رقم الجوال مسجل مسبقاً، يرجى تسجيل الدخول',
            'auth_password.required' => 'كلمة المرور مطلوبة',
            'auth_password.min' => 'كلمة المرور يجب أن تكون 4 أحرف على الأقل',
            'auth_gender.required' => 'يرجى تحديد الجنس',
        ]);

        $customer = Customer::create([
            'name' => $this->auth_name,
            'phone' => $this->auth_phone,
            'password' => $this->auth_password,
            'gender' => $this->auth_gender,
            'subscription_number' => 'SUB-' . strtoupper(uniqid()), // Default required value
        ]);

        $this->loggedInCustomer = $customer;
        session(['public_customer_id' => $customer->id]);
        $this->isAuthModalOpen = false;
        $this->dispatch('notify', ['message' => 'تم إنشاء حسابك بنجاح! مرحباً ' . $customer->name, 'type' => 'success']);
    }

    public function customerLogout()
    {
        $this->loggedInCustomer = null;
        session()->forget('public_customer_id');
        $this->dispatch('notify', ['message' => 'تم تسجيل الخروج بنجاح', 'type' => 'success']);
    }

    public function openEventModal($eventId)
    {
        // If not logged in, show auth modal first
        if (!$this->loggedInCustomer) {
            $this->openAuthModal();
            return;
        }

        $this->selectedEvent = Event::find($eventId);
        if ($this->selectedEvent) {
            $this->booking_name = $this->loggedInCustomer->name;
            $this->booking_phone = $this->loggedInCustomer->phone;
            $this->booking_gender = $this->loggedInCustomer->gender ?? '';
            $this->booking_table = '';
            $this->booking_table_id = null;
            $this->booking_chair_id = null;
            $this->isEventModalOpen = true;
        }
    }

    public function submitEventBooking()
    {
        $this->validate([
            'booking_name' => 'required|string|max:255',
            'booking_phone' => 'required|string|max:50',
            'booking_gender' => 'required|in:male,female',
            'booking_table' => 'nullable|string|max:50',
        ], [
            'booking_gender.required' => 'يرجى تحديد الجنس (ذكر أو أنثى)',
        ]);

        $tableId = $this->booking_table_id;
        if ($this->booking_table && !$tableId) {
            $table = Table::where('number', $this->booking_table)->first();
            if ($table) {
                $tableId = $table->id;
            }
        }

        $chairId = $this->booking_chair_id;
        $chairNumber = null;
        if ($chairId) {
            $chair = \App\Models\Chair::find($chairId);
            if ($chair) {
                $chairNumber = $chair->number;
            }
        }

        EventBooking::create([
            'event_id' => $this->selectedEvent->id,
            'customer_id' => $this->loggedInCustomer?->id,
            'customer_name' => $this->booking_name,
            'customer_phone' => $this->booking_phone,
            'gender' => $this->booking_gender,
            'table_number' => $this->booking_table,
            'table_id' => $tableId,
            'chair_id' => $chairId,
            'chair_number' => $chairNumber,
            'status' => 'approved',
        ]);

        $this->isEventModalOpen = false;
        $this->dispatch('notify', ['message' => 'تم إرسال طلب الحجز بنجاح! سنتواصل معك قريباً.', 'type' => 'success']);
    }

    public function openTableSelector()
    {
        $this->isTableSelectorModalOpen = true;
    }

    #[On('tableSelected')]
    public function tableSelected($tableId, $tableNumber, $chairId = null)
    {
        $this->booking_table_id = $tableId;
        $this->booking_table = $tableNumber;
        $this->booking_chair_id = $chairId;
        $this->isTableSelectorModalOpen = false;
    }

    #[On('tableSelectionCancelled')]
    public function tableSelectionCancelled()
    {
        $this->isTableSelectorModalOpen = false;
    }

    public function openSubModal($type, $id)
    {
        $this->sub_name = '';
        $this->sub_phone = '';
        $this->isLoyaltySub = ($type === 'loyalty');
        $this->selectedLoyaltyId = $this->isLoyaltySub ? $id : null;
        $this->selectedPackageId = !$this->isLoyaltySub ? $id : null;
        $this->isSubModalOpen = true;
    }

    public function submitSubscriptionRequest()
    {
        $this->validate([
            'sub_name' => 'required|string|max:255',
            'sub_phone' => 'required|string|max:50',
        ]);

        SubscriptionRequest::create([
            'customer_name' => $this->sub_name,
            'customer_phone' => $this->sub_phone,
            'request_type' => $this->isLoyaltySub ? 'loyalty' : 'subscription',
            'subscription_package_id' => $this->selectedPackageId,
            'loyalty_offer_id' => $this->selectedLoyaltyId,
            'status' => 'pending',
        ]);

        $this->isSubModalOpen = false;
        $this->dispatch('notify', ['message' => 'تم استلام طلب الاشتراك الخاص بك بنجاح!', 'type' => 'success']);
    }

    public function render()
    {
        $links = SiteLink::active()->get();

        $socialLinks = $links->where('type', 'social');
        $announcements = $links->where('type', 'announcement');
        $regularLinks = $links->whereIn('type', ['link', 'menu', 'whatsapp']);
        $locationLinks = $links->where('type', 'location');

        $events = $this->site_show_events ? Event::where('is_active', true)->orderBy('event_date', 'desc')->get() : collect();
        $packages = $this->site_show_packages ? SubscriptionPackage::where('is_active', true)->get() : collect();
        $loyaltyOffers = $this->site_show_packages ? LoyaltyOffer::all() : collect();
        $tablesBySection = Table::all()->groupBy('section');

        return view('livewire.public-site', [
            'socialLinks' => $socialLinks,
            'announcements' => $announcements,
            'regularLinks' => $regularLinks,
            'locationLinks' => $locationLinks,
            'events' => $events,
            'packages' => $packages,
            'loyaltyOffers' => $loyaltyOffers,
            'tablesBySection' => $tablesBySection,
        ]);
    }
}
