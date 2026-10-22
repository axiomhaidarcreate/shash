<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Table;
use Illuminate\Support\Str;
use App\Models\WorkShift;
use App\Models\Chair;
use App\Services\ShiftService;
use Carbon\Carbon;
use App\Models\Setting;

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
    public $app_theme = 'dark';
    public $manager_pin = '7925';

    // Messaging Settings
    public $messaging_enabled = false;
    public $messaging_type = 'whatsapp';
    public $messaging_api_url = '';

    public $whatsapp_welcome_template = '';
    public $whatsapp_points_updated_template = '';
    public $whatsapp_reward_reached_template = '';

    public function mount()
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        $this->loungeName = $settings['lounge_name'] ?? 'لاونج الشيشة';
        $this->working_hours_start = $settings['shift_start_time'] ?? '16:00';
        $this->working_hours_end = $settings['shift_end_time'] ?? '04:00';
        $this->notifications_enabled = ($settings['notifications_enabled'] ?? '1') == '1';
        $this->lounge_phone = $settings['lounge_phone'] ?? '';
        $this->lounge_address = $settings['lounge_address'] ?? '';
        $this->app_theme = $settings['app_theme'] ?? 'dark';
        $this->manager_pin = $settings['manager_pin'] ?? '7925';
        $this->messaging_enabled = ($settings['messaging_enabled'] ?? '0') == '1';
        $this->messaging_type = $settings['messaging_type'] ?? 'whatsapp';
        $this->messaging_api_url = $settings['messaging_api_url'] ?? '';

        $this->whatsapp_welcome_template = $settings['whatsapp_welcome_template'] ?? '';
        $this->whatsapp_points_updated_template = $settings['whatsapp_points_updated_template'] ?? '';
        $this->whatsapp_reward_reached_template = $settings['whatsapp_reward_reached_template'] ?? '';

    }

    public function addTable()
    {
        $this->validate([
            'newTableNumber' => 'required|string|max:20',
            'newTableCapacity' => 'required|integer|min:1|max:20',
            'newTableSection' => 'required|string|exists:sections,name',
        ]);

        $table = Table::create([
            'number' => $this->newTableNumber,
            'capacity' => $this->newTableCapacity,
            'section' => $this->newTableSection,
            'sort_order' => Table::where('section', $this->newTableSection)->max('sort_order') + 1,
        ]);

        for ($i = 1; $i <= $this->newTableCapacity; $i++) {
            $table->chairs()->create([
                'number' => (string)$i,
                'status' => 'empty',
                'sort_order' => $i
            ]);
        }

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
            
            // Adjust chairs if capacity changed
            $currentChairsCount = $table->chairs()->count();
            if ($this->editTableCapacity > $currentChairsCount) {
                for ($i = $currentChairsCount + 1; $i <= $this->editTableCapacity; $i++) {
                    $table->chairs()->create([
                        'number' => (string)$i,
                        'status' => 'empty',
                        'sort_order' => $i
                    ]);
                }
            } elseif ($this->editTableCapacity < $currentChairsCount) {
                // Delete extra empty chairs if possible, or just delete the last ones
                $chairsToDelete = $table->chairs()->orderBy('sort_order', 'desc')->take($currentChairsCount - $this->editTableCapacity)->get();
                foreach($chairsToDelete as $chair) {
                    $chair->delete();
                }
            }
            
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
        session()->flash('success', 'تم حذف الطاولة وكافة كراسيها.');
    }

    public function addChair($tableId)
    {
        $table = Table::find($tableId);
        if ($table) {
            $count = $table->chairs()->count();
            $table->chairs()->create([
                'number' => (string)($count + 1),
                'status' => 'empty',
                'sort_order' => $count + 1
            ]);
            $table->increment('capacity');
            session()->flash('success', 'تم إضافة الكرسي بنجاح.');
        }
    }

    public function removeChair($chairId)
    {
        $chair = Chair::find($chairId);
        if ($chair) {
            $table = $chair->table;
            $chair->delete();
            if ($table) {
                $table->decrement('capacity');
            }
            session()->flash('success', 'تم حذف الكرسي بنجاح.');
        }
    }

    public function resetChair($chairId)
    {
        $chair = Chair::find($chairId);
        if ($chair) {
            $chair->update(['status' => 'empty']);
            
            // Check if all chairs are empty now
            $table = $chair->table;
            if ($table && !$table->chairs()->where('status', '!=', 'empty')->exists()) {
                $table->update(['status' => 'empty']);
            }
            session()->flash('success', 'تم تحرير الكرسي بنجاح.');
        }
    }

    public function renameChair($chairId, $newName)
    {
        $chair = Chair::find($chairId);
        if ($chair && !empty(trim($newName))) {
            $chair->update(['number' => trim($newName)]);
            session()->flash('success', 'تم إعادة تسمية الكرسي بنجاح.');
        }
    }

    public function resetTable($id)
    {
        $table = Table::find($id);
        if ($table) {
            $table->status = 'empty';
            $table->save();
            $table->chairs()->update(['status' => 'empty']);
        }
    }

    public function resetAllTables()
    {
        Table::query()->update(['status' => 'empty']);
        Chair::query()->update(['status' => 'empty']);
        session()->flash('success', 'تم إعادة تعيين جميع الطاولات والكراسي.');
    }

    public function moveTableUp($tableId)
    {
        $table = Table::find($tableId);
        if (!$table) return;

        // Find the previous table in the same section (lower sort_order)
        $prevTable = Table::where('section', $table->section)
            ->where('sort_order', '<', $table->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($prevTable) {
            $tempOrder = $table->sort_order;
            $table->sort_order = $prevTable->sort_order;
            $prevTable->sort_order = $tempOrder;
            $table->save();
            $prevTable->save();
        }
    }

    public function moveTableDown($tableId)
    {
        $table = Table::find($tableId);
        if (!$table) return;

        // Find the next table in the same section (higher sort_order)
        $nextTable = Table::where('section', $table->section)
            ->where('sort_order', '>', $table->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($nextTable) {
            $tempOrder = $table->sort_order;
            $table->sort_order = $nextTable->sort_order;
            $nextTable->sort_order = $tempOrder;
            $table->save();
            $nextTable->save();
        }
    }

    public function useDefaultWelcomeTemplate()
    {
        $this->whatsapp_welcome_template = "مرحباً بك *{name}* في برنامج الولاء! 🎉\n\n🔑 رقم اشتراكك: *{subscription_number}*\n📱 احتفظ بهذا الرقم لتتبع نقاطك.{offer_details}\n\nنسعد بخدمتك دائماً! 💛";
    }

    public function useDefaultPointsTemplate()
    {
        $this->whatsapp_points_updated_template = "مرحباً *{name}* 👋\n\n✅ تم إضافة *{points_added}* نقطة إلى حسابك{reason}\n\n📊 رصيدك الحالي: *{remaining_points}* نقطة\n{next_reward}\nشكراً لزيارتك! 💛";
    }

    public function useDefaultRedemptionTemplate()
    {
        $this->whatsapp_reward_reached_template = "مرحباً *{name}* 🎉\n\n🎁 تم استبدال *{points_used}* نقطة بنجاح!\n📦 المكافأة: *{reward_name}*\n\n📊 رصيدك المتبقي: *{remaining_points}* نقطة\n\nاستمر في جمع النقاط للحصول على مكافآت أكثر! 💛";
    }

    public function saveGeneralSettings()
    {
        $keys = [
            'lounge_name' => $this->loungeName,
            'shift_start_time' => $this->working_hours_start,
            'shift_end_time' => $this->working_hours_end,
            'notifications_enabled' => $this->notifications_enabled ? '1' : '0',
            'lounge_phone' => $this->lounge_phone,
            'lounge_address' => $this->lounge_address,
            'app_theme' => $this->app_theme,
            'manager_pin' => $this->manager_pin,
            'messaging_enabled' => $this->messaging_enabled ? '1' : '0',
            'messaging_type' => $this->messaging_type,
            'messaging_api_url' => $this->messaging_api_url,
            'whatsapp_welcome_template' => $this->whatsapp_welcome_template,
            'whatsapp_points_updated_template' => $this->whatsapp_points_updated_template,
            'whatsapp_reward_reached_template' => $this->whatsapp_reward_reached_template,
        ];

        foreach ($keys as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        session()->flash('success', 'تم حفظ الإعدادات العامة بنجاح.');
    }

    public function startShiftManual(ShiftService $shiftService)
    {
        $activeShift = WorkShift::where('status', 'open')->first();
        if ($activeShift) {
            session()->flash('error', 'الفترة المحاسبية مفتوحة بالفعل.');
            return;
        }

        $shiftService->startShift(auth()->id());
        session()->flash('success', 'تم فتح الفترة المحاسبية يدوياً بنجاح.');
    }

    public function closeShiftManual(ShiftService $shiftService)
    {
        $activeShift = WorkShift::where('status', 'open')->first();
        if (!$activeShift) {
            session()->flash('error', 'لا توجد فترة محاسبية نشطة لإغلاقها.');
            return;
        }

        $shiftService->closeShift(auth()->id());
        session()->flash('success', 'تم إغلاق الفترة المحاسبية يدوياً بنجاح.');
    }


    public function render()
    {
        $tables = Table::with('chairs')->orderBy('sort_order')->orderBy('id')->get();
        $sections = \App\Models\Section::orderBy('name')->get();
        $activeShift = WorkShift::where('status', 'open')->first();

        return view('livewire.admin.settings', [
            'tables' => $tables,
            'sections' => $sections,
            'activeShift' => $activeShift,
        ])->layout('components.layouts.admin');
    }
}
