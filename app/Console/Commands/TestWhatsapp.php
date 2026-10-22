<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test {phone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending a WhatsApp message';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\WhatsAppService $whatsapp)
    {
        $phone = $this->argument('phone');
        $this->info("Queuing test message to $phone...");

        $result = $whatsapp->sendMessage($phone, "مرحباً! هذه رسالة تجريبية من النظام للتأكد من عمل خدمة الواتساب بشكل صحيح مع Queues. 🚀");

        if ($result['success']) {
            $this->info($result['message']);
            $this->info("Make sure to run 'php artisan queue:work' to process the queue and send the message!");
        } else {
            $this->error($result['message']);
        }
    }
}
