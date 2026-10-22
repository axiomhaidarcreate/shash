<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebasePushService
{
    /**
     * Send a push notification via Firebase Cloud Messaging
     *
     * @param string|array $tokens The FCM token(s) of the user
     * @param string $title The notification title
     * @param string $body The notification body
     * @param array $data Extra data payload (e.g. ['type' => 'event', 'id' => 1])
     * @return bool
     */
    public static function send($tokens, string $title, string $body, array $data = [])
    {
        try {
            $credentialsPath = storage_path('app/firebase_credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::error("Firebase credentials file not found at: {$credentialsPath}");
                return false;
            }

            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $messaging = $factory->createMessaging();

            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            if (is_array($tokens)) {
                $report = $messaging->sendMulticast($message, $tokens);
                return $report->successes()->count() > 0;
            } else {
                $message = $message->withChangedTarget('token', $tokens);
                $messaging->send($message);
                return true;
            }

        } catch (\Exception $e) {
            Log::error("Firebase Push Error: " . $e->getMessage());
            return false;
        }
    }
}
