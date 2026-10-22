<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiToken;
    protected string $instanceId;
    protected bool $isEnabled;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', '');
        $this->apiToken = config('services.whatsapp.api_token', '');
        $this->instanceId = config('services.whatsapp.instance_id', '');
        $this->isEnabled = !empty($this->apiUrl) && !empty($this->apiToken);
    }

    /**
     * Check if the WhatsApp service is configured and ready.
     */
    public function isConfigured(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Send a generic text message via WhatsApp API.
     *
     * @param string $phone   The recipient phone number (with country code, e.g. 966501234567)
     * @param string $message The text message to send
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendMessage(string $phone, string $message): array
    {
        if (!$this->isConfigured()) {
            Log::warning("WhatsApp: Service not configured. Message NOT sent to {$phone}.");
            return ['success' => false, 'message' => 'خدمة الواتساب غير مفعلة. يرجى إعداد مفاتيح الربط في ملف .env'];
        }

        // Normalize phone number: remove leading 0, add 966 if needed
        $phone = $this->normalizePhone($phone);

        try {
            $log = \App\Models\WhatsappLog::create([
                'phone'   => $phone,
                'message' => $message,
                'status'  => 'pending',
            ]);

            \App\Jobs\SendWhatsappMessage::dispatch($log);

            return ['success' => true, 'message' => 'تم إضافة الرسالة إلى قائمة الانتظار لل إرسال'];
        } catch (\Exception $e) {
            Log::error("WhatsApp: Exception queuing message to {$phone}: " . $e->getMessage());
            return ['success' => false, 'message' => 'خطأ في إضافة الرسالة لقائمة الانتظار: ' . $e->getMessage()];
        }
    }

    /**
     * Generate and send an OTP code to verify a customer's phone number.
     *
     * @param string $phone The phone number to verify
     * @return array ['success' => bool, 'message' => string, 'otp' => string|null]
     */
    public function sendOTP(string $phone): array
    {
        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Store OTP in cache for 5 minutes
        Cache::put("whatsapp_otp_{$this->normalizePhone($phone)}", $otp, now()->addMinutes(5));

        $message = "مرحباً بك في برنامج الولاء 🎉\n\nرمز التحقق الخاص بك هو: *{$otp}*\n\nصالح لمدة 5 دقائق فقط.";

        $result = $this->sendMessage($phone, $message);
        $result['otp'] = $otp; // Return OTP for logging/testing (remove in production if needed)

        return $result;
    }

    /**
     * Verify an OTP code entered by the customer.
     *
     * @param string $phone The phone number that received the OTP
     * @param string $enteredOtp The OTP code entered by the user
     * @return bool Whether the OTP is valid
     */
    public function verifyOTP(string $phone, string $enteredOtp): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);
        $storedOtp = Cache::get("whatsapp_otp_{$normalizedPhone}");

        if ($storedOtp && $storedOtp === $enteredOtp) {
            Cache::forget("whatsapp_otp_{$normalizedPhone}");
            return true;
        }

        return false;
    }

    /**
     * Send a welcome message to a newly registered loyalty customer.
     */
    public function sendWelcomeMessage(Customer $customer): array
    {
        $customer->loadMissing('loyaltyOffer.rewardTiers');

        $offerDetails = '';
        if ($customer->loyaltyOffer) {
            $offer = $customer->loyaltyOffer;
            $offerDetails = "\n\n📋 *تفاصيل عرض الولاء:*\n"
                . "▪️ اسم العرض: {$offer->name}\n"
                . "▪️ كل زيارة = {$offer->points_per_visit} نقطة\n"
                . "▪️ كل طلب = {$offer->points_per_order} نقطة\n"
                . "▪️ كل ريال = {$offer->points_per_riyal} نقطة\n";

            // Show reward tiers
            if ($offer->rewardTiers && $offer->rewardTiers->count() > 0) {
                $offerDetails .= "\n🎁 *مستويات المكافآت:*\n";
                foreach ($offer->rewardTiers as $tier) {
                    $offerDetails .= "🏆 {$tier->required_points} نقطة → {$tier->reward_description}\n";
                }
            } elseif ($offer->required_points > 0 && $offer->reward_description) {
                $offerDetails .= "▪️ عند تجميع {$offer->required_points} نقطة تحصل على: {$offer->reward_description}";
            }
        }

        $template = \App\Models\Setting::where('key', 'whatsapp_welcome_template')->value('value');

        if ($template) {
            $message = str_replace(
                ['{name}', '{subscription_number}', '{offer_details}'],
                [$customer->name, $customer->subscription_number, $offerDetails],
                $template
            );
        } else {
            $message = "مرحباً بك *{$customer->name}* في برنامج الولاء! 🎉\n\n"
                . "🔑 رقم اشتراكك: *{$customer->subscription_number}*\n"
                . "📱 احتفظ بهذا الرقم لتتبع نقاطك."
                . $offerDetails
                . "\n\nنسعد بخدمتك دائماً! 💛";
        }

        return $this->sendMessage($customer->phone, $message);
    }

    /**
     * Send a notification when points are added to a customer's account.
     */
    public function sendPointsUpdate(Customer $customer, int $pointsAdded, string $reason = ''): array
    {
        $customer->loadMissing('loyaltyOffer.rewardTiers');
        $remaining = $customer->total_points - $customer->used_points;

        $nextRewardText = "";
        
        if ($customer->loyaltyOffer) {
            $offer = $customer->loyaltyOffer;

            if ($offer->rewardTiers && $offer->rewardTiers->count() > 0) {
                // Find next redeemable tier
                $nextTier = $offer->rewardTiers->where('required_points', '>', $remaining)->first();
                $availableTiers = $offer->rewardTiers->where('required_points', '<=', $remaining);

                if ($availableTiers->count() > 0) {
                    $nextRewardText .= "\n🎁 *مكافآت متاحة للاستبدال:*\n";
                    foreach ($availableTiers as $tier) {
                        $nextRewardText .= "✅ {$tier->reward_description} ({$tier->required_points} نقطة)\n";
                    }
                    $nextRewardText .= "تواصل مع الاستقبال لاستبدال مكافأتك!\n";
                }

                if ($nextTier) {
                    $needed = $nextTier->required_points - $remaining;
                    $nextRewardText .= "🎯 باقي لك *{$needed}* نقطة للحصول على: {$nextTier->reward_description}\n";
                }
            } elseif ($offer->required_points > 0 && $offer->reward_description) {
                // Legacy fallback
                $needed = $offer->required_points - $remaining;
                if ($needed > 0) {
                    $nextRewardText .= "🎯 باقي لك *{$needed}* نقطة للحصول على: {$offer->reward_description}\n";
                } else {
                    $nextRewardText .= "🎁 *تهانينا!* لديك نقاط كافية للحصول على مكافأتك. تواصل مع الاستقبال!\n";
                }
            }
        }

        $template = \App\Models\Setting::where('key', 'whatsapp_points_updated_template')->value('value');

        if ($template) {
            $message = str_replace(
                ['{name}', '{points_added}', '{reason}', '{remaining_points}', '{next_reward}'],
                [$customer->name, $pointsAdded, $reason ? " ({$reason})" : '', $remaining, trim($nextRewardText)],
                $template
            );
        } else {
            $message = "مرحباً *{$customer->name}* 👋\n\n"
                . "✅ تم إضافة *{$pointsAdded}* نقطة إلى حسابك"
                . ($reason ? " ({$reason})" : '') . "\n\n"
                . "📊 رصيدك الحالي: *{$remaining}* نقطة\n"
                . $nextRewardText
                . "\nشكراً لزيارتك! 💛";
        }

        // Insert in-app notification
        \App\Models\CustomerNotification::create([
            'customer_id' => $customer->id,
            'title' => 'نقاط ولاء جديدة! 🌟',
            'message' => "تم إضافة $pointsAdded نقطة إلى حسابك." . ($reason ? " ($reason)" : ""),
            'type' => 'loyalty',
        ]);

        return $this->sendMessage($customer->phone, $message);
    }

    /**
     * Send a notification when a customer redeems a reward.
     */
    public function sendRedemptionNotification(Customer $customer, string $rewardDescription, int $pointsUsed): array
    {
        $remaining = $customer->total_points - $customer->used_points;

        $template = \App\Models\Setting::where('key', 'whatsapp_reward_reached_template')->value('value');

        if ($template) {
            $message = str_replace(
                ['{name}', '{points_used}', '{reward_name}', '{remaining_points}'],
                [$customer->name, $pointsUsed, $rewardDescription, $remaining],
                $template
            );
        } else {
            $message = "مرحباً *{$customer->name}* 🎉\n\n"
                . "🎁 تم استبدال *{$pointsUsed}* نقطة بنجاح!\n"
                . "📦 المكافأة: *{$rewardDescription}*\n\n"
                . "📊 رصيدك المتبقي: *{$remaining}* نقطة\n\n"
                . "استمر في جمع النقاط للحصول على مكافآت أكثر! 💛";
        }

        return $this->sendMessage($customer->phone, $message);
    }

    /**
     * Normalize a Saudi phone number to international format (966XXXXXXXXX).
     */
    protected function normalizePhone(string $phone): string
    {
        // Remove spaces, dashes, and plus sign
        $phone = preg_replace('/[\s\-\+]/', '', $phone);

        // If starts with 0, replace with 966
        if (str_starts_with($phone, '05')) {
            $phone = '966' . substr($phone, 1);
        } elseif (str_starts_with($phone, '5') && strlen($phone) === 9) {
            $phone = '966' . $phone;
        }

        return $phone;
    }
}
