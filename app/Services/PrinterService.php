<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Order;
use App\Models\PrintJob;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;

class PrinterService
{
    private $settings;

    public function __construct()
    {
        $this->settings = Setting::all()->pluck('value', 'key')->toArray();
    }

    private function getSetting($key, $default = '')
    {
        return $this->settings[$key] ?? $default;
    }

    public function printOrder(Order $order, $type = 'kitchen')
    {
        $this->printBatch([$order], $type);
    }

    public function printBatch($orders, $type = 'kitchen')
    {
        try {
            if (!class_exists(Printer::class)) {
                return false;
            }

            $connector = new DummyPrintConnector();
            $printer = new Printer($connector);
            
            if ($this->getSetting('printer_encoding') === 'CP864') {
                $printer->selectCharacterTable(22);
            }

            $loungeName = $this->getSetting('lounge_name', 'لاونج الشيشة');
            $loungePhone = $this->getSetting('lounge_phone', '');

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text($loungeName . "\n");
            
            $printer->setTextSize(1, 1);
            if (!empty($loungePhone)) {
                $printer->text("رقم الهاتف: " . $loungePhone . "\n");
            }
            $printer->text("--------------------------------\n");
            
            // تحديد نوع الفاتورة
            $title = match($type) {
                'kitchen' => "--- طلب مطبخ ---",
                'shisha' => "--- طلب شيشة ---",
                default => "--- فاتورة كاشير ---",
            };
            
            $printer->text($title . "\n");
            $printer->text("تاريخ الطلب: " . now()->format('Y-m-d H:i') . "\n");
            
            if (count($orders) > 0) {
                $firstOrder = $orders[0];
                $tableText = $firstOrder->table ? $firstOrder->table->number : 'أخرى';
                $printer->text("رقم الطاولة: " . $tableText . "\n");
                $printer->text("الكابتن: " . ($firstOrder->user ? $firstOrder->user->name : '') . "\n");
                if ($firstOrder->batch_id) {
                    $printer->text("رقم الطلبية: " . $firstOrder->batch_id . "\n");
                }
            }
            
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);

            foreach ($orders as $order) {
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("- الصنف: " . $order->shisha_type . "\n");
                $printer->text("  الكمية: " . $order->quantity);
                if ($order->grams_per_unit > 0) {
                    $printer->text(" | " . $order->grams_per_unit . " جرام");
                }
                $printer->text("\n");
                
                if (!empty($order->notes)) {
                    $printer->text("  ملاحظات: " . $order->notes . "\n");
                }
                $printer->text("--------------------------------\n");
            }
            
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("شكراً لزيارتكم!\n");
            $printer->text("\n\n\n\n");

            if ($this->getSetting('printer_auto_cut', '1') == '1') {
                $printer->cut();
            }
            if ($this->getSetting('printer_cash_drawer', '0') == '1' && $type === 'cashier') {
                $printer->pulse();
            }

            $payload = $connector->getData();
            $printer->close();
            $base64Payload = base64_encode($payload);

            PrintJob::create([
                'receipt_data' => $base64Payload,
                'status' => 'pending',
                'printer_type' => $type
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('PrinterService error: ' . $e->getMessage());
            return false;
        }
    }
}
