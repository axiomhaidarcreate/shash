<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;
use App\Models\WorkShift;
use App\Services\ShiftService;
use Illuminate\Support\Facades\Auth;

class AdminShiftSettings extends Component
{
    public $shiftStartTime;
    public $shiftEndTime;
    public $activeShift = null;

    public function mount()
    {
        $this->shiftStartTime = Setting::where('key', 'shift_start_time')->value('value') ?? '16:00';
        $this->shiftEndTime = Setting::where('key', 'shift_end_time')->value('value') ?? '02:00';
        $this->loadActiveShift();
    }

    public function loadActiveShift()
    {
        $this->activeShift = WorkShift::where('status', 'open')->first();
    }

    public function saveSettings()
    {
        $this->validate([
            'shiftStartTime' => 'required|date_format:H:i',
            'shiftEndTime' => 'required|date_format:H:i',
        ]);

        Setting::updateOrCreate(['key' => 'shift_start_time'], ['value' => $this->shiftStartTime]);
        Setting::updateOrCreate(['key' => 'shift_end_time'], ['value' => $this->shiftEndTime]);

        session()->flash('message', 'تم حفظ أوقات الفترة المحاسبية بنجاح.');
    }

    public function startShiftManual()
    {
        $shiftService = new ShiftService();
        $success = $shiftService->startShift(Auth::id(), false);

        if ($success) {
            $this->loadActiveShift();
            session()->flash('message', 'تم بدء العمل وفتح الفترة المحاسبية بنجاح للجميع.');
        } else {
            session()->flash('error', 'توجد فترة محاسبية مفتوحة حالياً.');
        }
    }

    public function closeShiftManual()
    {
        $shiftService = new ShiftService();
        $success = $shiftService->closeShift(Auth::id(), false);

        if ($success) {
            $this->loadActiveShift();
            session()->flash('message', 'تم إغلاق العمل وإنهاء الفترة المحاسبية بنجاح للجميع.');
        } else {
            session()->flash('error', 'لا توجد فترة محاسبية مفتوحة للإغلاق.');
        }
    }

    public function render()
    {
        return view('livewire.admin-shift-settings')->layout('components.layouts.app');
    }
}
