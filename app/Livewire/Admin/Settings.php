<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Table;
use Illuminate\Support\Str;

class Settings extends Component
{
    public $loungeName = 'لاونج الشيشة';
    public $tableCount = 10;
    public $newTableNumber = '';
    public $newTableCapacity = 4;
    public $newTableSection = '';

    // Edit Table State
    public $editingTableId = null;
    public $editTableNumber = '';
    public $editTableCapacity = 4;
    public $editTableSection = '';

    // Section Management State
    public $newSectionName = '';
    public $editingSectionId = null;
    public $editSectionName = '';

    // General Settings
    public $working_hours_start = '16:00';
    public $working_hours_end = '04:00';
    public $notifications_enabled = true;
    public $lounge_phone = '';
    public $lounge_address = '';

    // Printer Settings
    public $print_api_token = '';
    public $printer_name = 'POSSTORE PS-7';
    public $printer_connection_type = 'Network';
    public $printer_cashier_ip = '';
    public $printer_kitchen_ip = '';
    public $printer_shisha_ip = '';
    public $printer_port = '9100';
    public $printer_protocol = 'RAW';
    public $printer_driver = 'ESC/POS';
    public $printer_paper_size = '80mm';
    public $printer_encoding = 'UTF-8';
    public $printer_auto_cut = true;
    public $printer_cash_drawer = false;
    public $printer_density = 'Medium';

    // Printer Statuses
    public $printer_cashier_status = 'غير معروف';
    public $printer_kitchen_status = 'غير معروف';
    public $printer_shisha_status = 'غير معروف';

    public function mount()
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        $this->loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $this->working_hours_start = $settings['working_hours_start'] ?? '16:00';
        $this->working_hours_end = $settings['working_hours_end'] ?? '04:00';
        $this->notifications_enabled = ($settings['notifications_enabled'] ?? '1') == '1';
        $this->lounge_phone = $settings['lounge_phone'] ?? '';
        $this->lounge_address = $settings['lounge_address'] ?? '';

        // Load Printer Settings
        $this->print_api_token = $settings['print_api_token'] ?? Str::random(32);
        if (!isset($settings['print_api_token'])) {
            \App\Models\Setting::updateOrCreate(['key' => 'print_api_token'], ['value' => $this->print_api_token]);
        }
        $this->printer_name = $settings['printer_name'] ?? 'POSSTORE PS-7';
        $this->printer_connection_type = $settings['printer_connection_type'] ?? 'Network';
        $this->printer_cashier_ip = $settings['printer_cashier_ip'] ?? '192.168.8.189';
        $this->printer_kitchen_ip = $settings['printer_kitchen_ip'] ?? '';
        $this->printer_shisha_ip = $settings['printer_shisha_ip'] ?? '';
        $this->printer_port = $settings['printer_port'] ?? '9100';
        $this->printer_protocol = $settings['printer_protocol'] ?? 'RAW';
        $this->printer_driver = $settings['printer_driver'] ?? 'ESC/POS';
        $this->printer_paper_size = $settings['printer_paper_size'] ?? '80mm';
        $this->printer_encoding = $settings['printer_encoding'] ?? 'UTF-8';
        $this->printer_auto_cut = ($settings['printer_auto_cut'] ?? '1') == '1';
        $this->printer_cash_drawer = ($settings['printer_cash_drawer'] ?? '0') == '1';
        $this->printer_density = $settings['printer_density'] ?? 'Medium';
        
        $this->printer_cashier_status = $settings['printer_cashier_status'] ?? 'غير معروف';
        $this->printer_kitchen_status = $settings['printer_kitchen_status'] ?? 'غير معروف';
        $this->printer_shisha_status = $settings['printer_shisha_status'] ?? 'غير معروف';
    }

    public function addTable()
    {
        $this->validate([
            'newTableNumber' => 'required|string|max:20',
            'newTableCapacity' => 'required|integer|min:1|max:20',
            'newTableSection' => 'required|string|exists:sections,name',
        ]);

        Table::create([
            'number' => $this->newTableNumber,
            'capacity' => $this->newTableCapacity,
            'section' => $this->newTableSection,
        ]);

        $this->reset(['newTableNumber', 'newTableSection']);
        $this->newTableCapacity = 4;
        session()->flash('success', 'تم إضافة الطاولة بنجاح!');
    }

    public function editTable($id)
    {
        $table = Table::find($id);
        if ($table) {
            $this->editingTableId = $table->id;
            $this->editTableNumber = $table->number;
            $this->editTableCapacity = $table->capacity;
            $this->editTableSection = $table->section ?? '';
        }
    }

    public function updateTable()
    {
        $this->validate([
            'editTableNumber' => 'required|string|max:20',
            'editTableCapacity' => 'required|integer|min:1|max:20',
            'editTableSection' => 'required|string|exists:sections,name',
        ]);

        $table = Table::find($this->editingTableId);
        if ($table) {
            $table->update([
                'number' => $this->editTableNumber,
                'capacity' => $this->editTableCapacity,
                'section' => $this->editTableSection,
            ]);
            session()->flash('success', 'تم تحديث الطاولة بنجاح!');
        }

        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->reset(['editingTableId', 'editTableNumber', 'editTableCapacity', 'editTableSection']);
    }

    public function addSection()
    {
        $this->validate([
            'newSectionName' => 'required|string|max:50|unique:sections,name',
        ]);

        \App\Models\Section::create([
            'name' => $this->newSectionName,
        ]);

        $this->newSectionName = '';
        session()->flash('success', 'تم إضافة القسم/الصالة بنجاح!');
    }

    public function editSection($id)
    {
        $section = \App\Models\Section::find($id);
        if ($section) {
            $this->editingSectionId = $section->id;
            $this->editSectionName = $section->name;
        }
    }

    public function updateSection()
    {
        $this->validate([
            'editSectionName' => 'required|string|max:50|unique:sections,name,' . $this->editingSectionId,
        ]);

        $section = \App\Models\Section::find($this->editingSectionId);
        if ($section) {
            $oldName = $section->name;
            $section->update([
                'name' => $this->editSectionName,
            ]);

            // Update all tables in this section to the new name
            Table::where('section', $oldName)->update([
                'section' => $this->editSectionName,
            ]);

            session()->flash('success', 'تم تحديث القسم/الصالة بنجاح!');
        }

        $this->cancelEditSection();
    }

    public function cancelEditSection()
    {
        $this->reset(['editingSectionId', 'editSectionName']);
    }

    public function deleteSection($id)
    {
        $section = \App\Models\Section::find($id);
        if ($section) {
            Table::where('section', $section->name)->update(['section' => null]);
            $section->delete();
            session()->flash('success', 'تم حذف القسم/الصالة بنجاح.');
        }
    }

    public function deleteTable($id)
    {
        Table::destroy($id);
        session()->flash('success', 'تم حذف الطاولة.');
    }

    public function resetTable($id)
    {
        $table = Table::find($id);
        if ($table) {
            $table->status = 'empty';
            $table->save();
        }
    }

    public function resetAllTables()
    {
        Table::query()->update(['status' => 'empty']);
        session()->flash('success', 'تم إعادة تعيين جميع الطاولات.');
    }

    public function saveGeneralSettings()
    {
        $keys = [
            'lounge_name' => $this->loungeName,
            'working_hours_start' => $this->working_hours_start,
            'working_hours_end' => $this->working_hours_end,
            'notifications_enabled' => $this->notifications_enabled ? '1' : '0',
            'lounge_phone' => $this->lounge_phone,
            'lounge_address' => $this->lounge_address,
            'print_api_token' => $this->print_api_token,
            'printer_name' => $this->printer_name,
            'printer_connection_type' => $this->printer_connection_type,
            'printer_cashier_ip' => $this->printer_cashier_ip,
            'printer_kitchen_ip' => $this->printer_kitchen_ip,
            'printer_shisha_ip' => $this->printer_shisha_ip,
            'printer_port' => $this->printer_port,
            'printer_protocol' => $this->printer_protocol,
            'printer_driver' => $this->printer_driver,
            'printer_paper_size' => $this->printer_paper_size,
            'printer_encoding' => $this->printer_encoding,
            'printer_auto_cut' => $this->printer_auto_cut ? '1' : '0',
            'printer_cash_drawer' => $this->printer_cash_drawer ? '1' : '0',
            'printer_density' => $this->printer_density,
        ];

        foreach ($keys as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        session()->flash('success', 'تم حفظ الإعدادات العامة بنجاح.');
    }

    public function printTest($type = 'cashier')
    {
        $ip = match($type) {
            'kitchen' => $this->printer_kitchen_ip,
            'shisha' => $this->printer_shisha_ip,
            default => $this->printer_cashier_ip,
        };

        if (empty($ip)) {
            session()->flash('error', 'يرجى إدخال عنوان IP الخاص بهذه الطابعة أولاً.');
            return;
        }

        try {
            if (class_exists(\Mike42\Escpos\Printer::class)) {
                $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
                $printer = new \Mike42\Escpos\Printer($connector);
                
                if ($this->printer_encoding === 'CP864') {
                    $printer->selectCharacterTable(22);
                }

                $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
                $printer->text("*** اختبار الطباعة ***\n");
                $printer->text("----------------------\n");
                $printer->text("Printer: " . $this->printer_name . "\n");
                $printer->text("IP: " . $ip . "\n");
                $printer->text("Date: " . now()->format('Y-m-d H:i:s') . "\n");
                $printer->text("----------------------\n");
                $printer->text("Success!\n");
                $printer->text("\n\n");

                if ($this->printer_auto_cut) {
                    $printer->cut();
                }
                
                if ($this->printer_cash_drawer && $type === 'cashier') {
                    $printer->pulse();
                }

                $payload = $connector->getData();
                $printer->close();
                $base64Payload = base64_encode($payload);

                \App\Models\PrintJob::create([
                    'receipt_data' => $base64Payload,
                    'status' => 'pending',
                    'printer_type' => $type
                ]);

                session()->flash('success', 'تم إرسال أمر الطباعة التجريبية لـ ' . $type . ' (بانتظار سحبه).');
            } else {
                session()->flash('error', 'مكتبة الطباعة غير مثبتة.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'خطأ أثناء الطباعة: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $tables = Table::orderBy('number')->get();
        $sections = \App\Models\Section::orderBy('name')->get();

        return view('livewire.admin.settings', [
            'tables' => $tables,
            'sections' => $sections,
        ])->layout('components.layouts.admin');
    }
}
