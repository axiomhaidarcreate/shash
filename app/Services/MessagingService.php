<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessagingService
{
    /**
     * Send a message to a customer.
     * 
     * @param string $phone Customer's phone number
     * @param string $message The message text to send
     * @return array ['success' => bool, 'message' => string]
     */
    public static function send($phone, $message)
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $isEnabled = ($settings['messaging_enabled'] ?? '0') == '1';

        if (!$isEnabled) {
            return ['success' => false, 'message' => 'نظام المراسلة غير مفعل.'];
        }

        $type = $settings['messaging_type'] ?? 'sms'; // 'sms' or 'whatsapp'
        $apiUrl = $settings['messaging_api_url'] ?? '';
        
        if (empty($apiUrl)) {
            return ['success' => false, 'message' => 'رابط الـ API غير مهيأ.'];
        }

        // Clean phone number (remove leading zeros, spaces, etc.)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ensure Yemeni country code for local numbers if missing
        if (strlen($phone) == 9 && (str_starts_with($phone, '7') || str_starts_with($phone, '7'))) {
            $phone = '967' . $phone;
        }

        // Replace placeholders in URL if the user provides a GET URL with placeholders
        // e.g. http://api.sms.com/send?token=xyz&phone={{phone}}&message={{message}}
        if (str_contains($apiUrl, '{{phone}}') || str_contains($apiUrl, '{{message}}')) {
            $finalUrl = str_replace(
                ['{{phone}}', '{{message}}'],
                [urlencode($phone), urlencode($message)],
                $apiUrl
            );
            
            try {
                $response = Http::get($finalUrl);
                if ($response->successful()) {
                    return ['success' => true, 'message' => 'تم الإرسال بنجاح.'];
                }
                Log::error('MessagingService API Error: ' . $response->body());
                return ['success' => false, 'message' => 'فشل الإرسال: خطأ من مزود الخدمة.'];
            } catch (\Exception $e) {
                Log::error('MessagingService Exception: ' . $e->getMessage());
                return ['success' => false, 'message' => 'فشل الاتصال بمزود الخدمة.'];
            }
        }

        // If it's a POST payload or specific provider, we could add specific logic here.
        // For now, we assume GET URL with placeholders is the most flexible approach 
        // that works for many Yemeni gateways (Jawaly, etc.) and WhatsApp APIs (UltraMsg, etc.).
        
        return ['success' => false, 'message' => 'تأكد من وضع {{phone}} و {{message}} في رابط الـ API.'];
    }
}
