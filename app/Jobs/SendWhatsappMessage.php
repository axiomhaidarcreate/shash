<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessage implements ShouldQueue
{
    use Queueable;

    protected WhatsappLog $log;

    /**
     * Create a new job instance.
     */
    public function __construct(WhatsappLog $log)
    {
        $this->log = $log;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apiUrl = config('services.whatsapp.api_url', '');
        $apiToken = config('services.whatsapp.api_token', '');
        $instanceId = config('services.whatsapp.instance_id', '');

        if (empty($apiUrl) || empty($apiToken) || empty($instanceId)) {
            $this->log->update([
                'status' => 'failed',
                'response' => 'WhatsApp configuration is missing.',
            ]);
            return;
        }

        $url = rtrim($apiUrl, '/') . '/' . $instanceId . '/messages/chat';

        try {
            $response = Http::asForm()->post($url, [
                'token'    => $apiToken,
                'to'       => $this->log->phone,
                'body'     => $this->log->message,
                'priority' => 10,
            ]);

            if ($response->successful()) {
                $this->log->update([
                    'status' => 'sent',
                    'response' => $response->body(),
                    'sent_at' => now(),
                ]);
            } else {
                $this->log->update([
                    'status' => 'failed',
                    'response' => $response->body(),
                ]);
                Log::error("WhatsApp Job: Failed to send message to {$this->log->phone}. Response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->log->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
            ]);
            Log::error("WhatsApp Job: Exception sending message to {$this->log->phone}: " . $e->getMessage());
        }
    }
}
