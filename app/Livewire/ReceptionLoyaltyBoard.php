<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\LoyaltyOffer;
use App\Models\LoyaltyRewardTier;
use App\Models\LoyaltyTransaction;
use App\Services\WhatsAppService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ReceptionLoyaltyBoard extends Component
{
    public $searchQuery = '';
    public $foundCustomer = null;
    public $searchResults = [];
    
    // For registering a new customer
    public $showRegisterModal = false;
    public $newName = '';
    public $newPhone = '';
    public $newOfferId = '';
    
    // OTP verification (optional)
    public $showOtpModal = false;
    public $enteredOtp = '';
    public $otpPhone = '';
    public $pendingCustomerId = null;
    
    // For adding points
    public $showAddPointsModal = false;
    public $pointsToAdd = 0;
    public $pointsSource = 'visit'; // 'visit', 'order', 'custom'
    public $orderAmount = 0; // For 'riyal' calculation if needed
    
    public function generateSubscriptionNumber()
    {
        do {
            $number = mt_rand(100000, 999999);
        } while (Customer::where('subscription_number', $number)->exists());
        return (string) $number;
    }

    public function updatedSearchQuery()
    {
        $query = trim($this->searchQuery);
        if (strlen($query) >= 1) {
            $this->searchResults = Customer::with(['loyaltyOffer.rewardTiers', 'subscriptionPackage'])
                ->where('phone', 'like', $query . '%')
                ->orWhere('phone', 'like', '%' . $query . '%')
                ->orWhere('name', 'like', '%' . $query . '%')
                ->orWhere('subscription_number', 'like', $query . '%')
                ->take(10)
                ->get();
        } else {
            $this->searchResults = [];
            $this->foundCustomer = null;
        }
    }

    public function selectCustomer($customerId)
    {
        $customer = Customer::with(['loyaltyOffer.rewardTiers', 'subscriptionPackage'])->find($customerId);
        if ($customer) {
            $this->foundCustomer = $customer;
            $this->searchQuery = $customer->phone ?: $customer->subscription_number;
            $this->searchResults = [];
            session()->forget('error');
        }
    }

    public function clearSelectedCustomer()
    {
        $this->foundCustomer = null;
        $this->searchQuery = '';
        $this->searchResults = [];
        session()->forget('error');
    }

    public function searchCustomer()
    {
        $query = trim($this->searchQuery);
        if (empty($query)) {
            $this->foundCustomer = null;
            $this->searchResults = [];
            return;
        }
        
        // Check exact match first, then partial
        $customer = Customer::with(['loyaltyOffer.rewardTiers', 'subscriptionPackage'])
            ->where('subscription_number', $query)
            ->orWhere('phone', $query)
            ->first();

        if (!$customer) {
            $customer = Customer::with(['loyaltyOffer.rewardTiers', 'subscriptionPackage'])
                ->where('phone', 'like', $query . '%')
                ->orWhere('name', 'like', '%' . $query . '%')
                ->first();
        }

        if ($customer) {
            $this->foundCustomer = $customer;
            $this->searchResults = [];
            session()->forget('error');
        } else {
            $this->foundCustomer = null;
            session()->flash('error', 'لم يتم العثور على عميل مطابق لهذا الرقم أو الاسم.');
        }
    }

    public function openRegisterModal()
    {
        $this->newName = '';
        $this->newPhone = $this->searchQuery;
        $this->newOfferId = '';
        $this->showRegisterModal = true;
    }

    public function closeRegisterModal()
    {
        $this->showRegisterModal = false;
    }

    public function registerCustomer()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newPhone' => 'required|string|max:20',
            'newOfferId' => 'required|exists:loyalty_offers,id',
        ]);
        
        // Check if phone already exists
        if (Customer::where('phone', $this->newPhone)->exists()) {
            session()->flash('register_error', 'رقم الجوال مسجل مسبقاً.');
            return;
        }

        $customer = Customer::create([
            'name' => $this->newName,
            'phone' => $this->newPhone,
            'subscription_number' => $this->generateSubscriptionNumber(),
            'loyalty_offer_id' => $this->newOfferId,
            'total_points' => 0,
            'used_points' => 0,
            'is_phone_verified' => false,
        ]);

        $this->closeRegisterModal();

        // Send WhatsApp welcome message
        $whatsapp = app(WhatsAppService::class);
        $whatsapp->sendWelcomeMessage($customer);

        // Send OTP for optional verification
        $otpResult = $whatsapp->sendOTP($customer->phone);

        if ($whatsapp->isConfigured()) {
            // WhatsApp is active: show OTP modal for optional verification
            $this->pendingCustomerId = $customer->id;
            $this->otpPhone = $customer->phone;
            $this->enteredOtp = '';
            $this->showOtpModal = true;

            $this->dispatch('notify', ['message' => 'تم تسجيل العميل بنجاح! تم إرسال رسالة ترحيبية ورمز تحقق عبر الواتساب.', 'type' => 'success']);
        } else {
            // WhatsApp not configured: skip OTP, go directly to search
            $this->searchQuery = $customer->subscription_number;
            $this->searchCustomer();

            $this->dispatch('notify', ['message' => 'تم تسجيل العميل بنجاح! (الواتساب غير مفعل حالياً)', 'type' => 'success']);
        }
    }

    // ---- OTP Methods (optional verification) ----

    public function verifyOtp()
    {
        if (empty($this->enteredOtp) || empty($this->otpPhone)) {
            $this->dispatch('notify', ['message' => 'يرجى إدخال رمز التحقق.', 'type' => 'error']);
            return;
        }

        $whatsapp = app(WhatsAppService::class);
        $isValid = $whatsapp->verifyOTP($this->otpPhone, $this->enteredOtp);

        if ($isValid) {
            // Mark phone as verified
            $customer = Customer::find($this->pendingCustomerId);
            if ($customer) {
                $customer->update(['is_phone_verified' => true]);
            }

            $this->closeOtpModal();
            $this->searchQuery = $customer->subscription_number;
            $this->searchCustomer();

            $this->dispatch('notify', ['message' => 'تم التحقق من رقم الجوال بنجاح! ✅', 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.', 'type' => 'error']);
            $this->enteredOtp = '';
        }
    }

    public function resendOtp()
    {
        if (empty($this->otpPhone)) return;

        $whatsapp = app(WhatsAppService::class);
        $result = $whatsapp->sendOTP($this->otpPhone);

        if ($result['success']) {
            $this->dispatch('notify', ['message' => 'تم إعادة إرسال رمز التحقق.', 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => $result['message'], 'type' => 'error']);
        }
    }

    public function skipOtp()
    {
        // Skip verification - customer remains unverified
        $customer = Customer::find($this->pendingCustomerId);
        $this->closeOtpModal();

        if ($customer) {
            $this->searchQuery = $customer->subscription_number;
            $this->searchCustomer();
        }

        $this->dispatch('notify', ['message' => 'تم تخطي التحقق. يمكن التحقق لاحقاً.', 'type' => 'info']);
    }

    public function closeOtpModal()
    {
        $this->showOtpModal = false;
        $this->enteredOtp = '';
        $this->otpPhone = '';
        $this->pendingCustomerId = null;
    }

    // ---- Points Methods ----

    public function openAddPointsModal()
    {
        if (!$this->foundCustomer) return;
        $this->pointsSource = 'visit';
        $this->orderAmount = 0;
        $this->calculateSuggestedPoints();
        $this->showAddPointsModal = true;
    }
    
    public function closeAddPointsModal()
    {
        $this->showAddPointsModal = false;
    }
    
    public function updatedPointsSource()
    {
        $this->calculateSuggestedPoints();
    }
    
    public function updatedOrderAmount()
    {
        if ($this->pointsSource === 'order') {
            $this->calculateSuggestedPoints();
        }
    }
    
    public function calculateSuggestedPoints()
    {
        if (!$this->foundCustomer || !$this->foundCustomer->loyaltyOffer) {
            $this->pointsToAdd = 0;
            return;
        }
        
        $offer = $this->foundCustomer->loyaltyOffer;
        
        if ($this->pointsSource === 'visit') {
            $this->pointsToAdd = $offer->points_per_visit;
        } elseif ($this->pointsSource === 'order') {
            if ($offer->points_per_riyal > 0) {
                $this->pointsToAdd = $offer->points_per_order + ($this->orderAmount * $offer->points_per_riyal);
            } else {
                $this->pointsToAdd = $offer->points_per_order;
            }
        } else {
            $this->pointsToAdd = 0;
        }
    }

    public function addPoints()
    {
        $this->validate([
            'pointsToAdd' => 'required|numeric|min:1',
        ]);
        
        if (!$this->foundCustomer) return;

        $oldRemaining = $this->foundCustomer->total_points - $this->foundCustomer->used_points;

        $this->foundCustomer->increment('total_points', $this->pointsToAdd);
        $this->foundCustomer->refresh();
        
        $newRemaining = $this->foundCustomer->total_points - $this->foundCustomer->used_points;
        $reason = $this->pointsSource == 'visit' ? 'زيارة' : ($this->pointsSource == 'order' ? 'طلب بقيمة '.$this->orderAmount : 'إضافة يدوية');

        LoyaltyTransaction::create([
            'customer_id' => $this->foundCustomer->id,
            'points_added' => $this->pointsToAdd,
            'description' => 'إضافة نقاط: ' . $reason,
        ]);

        // Determine if any new tiers were just reached
        $newlyReachedTiers = [];
        $offer = $this->foundCustomer->loyaltyOffer;
        if ($offer) {
            $offer->loadMissing('rewardTiers');
            if ($offer->rewardTiers && $offer->rewardTiers->count() > 0) {
                foreach ($offer->rewardTiers as $tier) {
                    if ($oldRemaining < $tier->required_points && $newRemaining >= $tier->required_points) {
                        $newlyReachedTiers[] = $tier;
                    }
                }
            } elseif ($offer->required_points > 0 && $offer->reward_description) {
                // Legacy check
                if ($oldRemaining < $offer->required_points && $newRemaining >= $offer->required_points) {
                    $newlyReachedTiers[] = (object)[
                        'required_points' => $offer->required_points,
                        'reward_description' => $offer->reward_description
                    ];
                }
            }
        }

        // Send WhatsApp notification
        $whatsapp = app(WhatsAppService::class);
        $whatsapp->sendPointsUpdate($this->foundCustomer, $this->pointsToAdd, $reason);

        // If a new tier was reached, send explicit alerts
        if (!empty($newlyReachedTiers)) {
            $tierNames = collect($newlyReachedTiers)->pluck('reward_description')->implode('، ');
            
            // 1. Alert for Reception
            $this->dispatch('notify', ['message' => "🎉 تنبيه: العميل استحق للتو مكافأة: {$tierNames}!", 'type' => 'success']);
            
            // 2. Alert for Customer via WhatsApp
            $message = "🎉 *تهانينا {$this->foundCustomer->name}!*\n\nلقد وصلت للتو إلى المكافآت التالية:\n";
            foreach ($newlyReachedTiers as $tier) {
                $message .= "🎁 {$tier->reward_description}\n";
            }
            $message .= "\nيرجى التواصل مع موظف الاستقبال لاستبدال مكافأتك! 💛";
            $whatsapp->sendMessage($this->foundCustomer->phone, $message);
        } else {
            $this->dispatch('notify', ['message' => 'تم إضافة النقاط بنجاح!', 'type' => 'success']);
        }

        $this->closeAddPointsModal();
        $this->searchCustomer(); // refresh customer data
    }
    
    /**
     * Redeem a specific reward tier by its ID.
     */
    public function redeemReward($tierId = null)
    {
        if (!$this->foundCustomer || !$this->foundCustomer->loyaltyOffer) return;
        
        $remaining = $this->foundCustomer->total_points - $this->foundCustomer->used_points;
        
        if ($tierId) {
            // Redeem a specific tier
            $tier = LoyaltyRewardTier::where('id', $tierId)
                ->where('loyalty_offer_id', $this->foundCustomer->loyaltyOffer->id)
                ->first();
            
            if (!$tier) {
                $this->dispatch('notify', ['message' => 'مستوى المكافأة غير موجود.', 'type' => 'error']);
                return;
            }
            
            $requiredPoints = $tier->required_points;
            $rewardDescription = $tier->reward_description;
        } else {
            // Fallback to legacy single reward
            $offer = $this->foundCustomer->loyaltyOffer;
            $requiredPoints = $offer->required_points;
            $rewardDescription = $offer->reward_description;
        }
        
        if ($remaining >= $requiredPoints) {
            $this->foundCustomer->increment('used_points', $requiredPoints);
            
            LoyaltyTransaction::create([
                'customer_id' => $this->foundCustomer->id,
                'points_used' => $requiredPoints,
                'description' => 'استبدال مكافأة: ' . $rewardDescription,
            ]);

            // Send WhatsApp redemption notification
            $whatsapp = app(WhatsAppService::class);
            $this->foundCustomer->refresh();
            $whatsapp->sendRedemptionNotification($this->foundCustomer, $rewardDescription, $requiredPoints);
            
            $this->searchCustomer(); // refresh
            $this->dispatch('notify', ['message' => 'تم استبدال المكافأة بنجاح! (' . $rewardDescription . ')', 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => 'نقاط العميل غير كافية لاستبدال هذه المكافأة.', 'type' => 'error']);
        }
    }

    public function render()
    {
        $offers = LoyaltyOffer::with('rewardTiers')->get();
        
        return view('livewire.reception-loyalty-board', [
            'offers' => $offers
        ]);
    }
}
