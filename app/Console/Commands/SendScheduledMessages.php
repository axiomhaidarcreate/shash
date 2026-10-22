<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledMessage;
use App\Models\Customer;
use App\Models\CustomerNotification;

class SendScheduledMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال الرسائل المجدولة والتلقائية للعملاء التي حان وقت إرسالها';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingMessages = ScheduledMessage::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($pendingMessages->isEmpty()) {
            $this->info('لا توجد رسائل مجدولة مستحقة للإرسال حالياً.');
            return 0;
        }

        foreach ($pendingMessages as $msg) {
            $query = Customer::query();

            if ($msg->target_type === 'nationality' && $msg->target_value) {
                $query->where('nationality', $msg->target_value);
            } elseif ($msg->target_type === 'event' && $msg->target_value) {
                $query->whereHas('eventBookings', function($q) use ($msg) {
                    $q->where('event_id', $msg->target_value);
                });
            } elseif ($msg->target_type === 'subscribed') {
                $query->whereNotNull('subscription_package_id')
                      ->where('subscription_end_date', '>=', now()->format('Y-m-d'));
            } elseif ($msg->target_type === 'loyalty') {
                $query->where('total_points', '>', 0);
            }

            $customers = $query->get();
            $count = 0;

            foreach ($customers as $customer) {
                CustomerNotification::create([
                    'customer_id' => $customer->id,
                    'title' => $msg->title,
                    'message' => $msg->content,
                    'type' => 'scheduled',
                    'is_read' => false,
                ]);
                $count++;
            }

            $msg->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $this->info("تم إرسال الرسالة المجدولة [{$msg->title}] بنجاح إلى {$count} عميل.");
        }

        return 0;
    }
}
