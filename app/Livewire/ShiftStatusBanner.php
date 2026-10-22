<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WorkShift;
use App\Models\Setting;
use App\Services\ShiftService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftStatusBanner extends Component
{
    public $activeShift = null;
    public $lastClosedShift = null;
    public $shiftStartTime;
    public $shiftEndTime;
    public $formattedStart;
    public $formattedEnd;

    public function mount()
    {
        $this->loadShiftData();
    }

    public function loadShiftData()
    {
        $this->activeShift = WorkShift::where('status', 'open')->first();
        $this->lastClosedShift = WorkShift::where('status', 'closed')
            ->whereNotNull('closed_at')
            ->latest('closed_at')
            ->first();
        $this->shiftStartTime = Setting::where('key', 'shift_start_time')->value('value') ?? '16:00';
        $this->shiftEndTime = Setting::where('key', 'shift_end_time')->value('value') ?? '02:00';
        $this->formattedStart = Carbon::parse($this->shiftStartTime)->format('h:i A');
        $this->formattedEnd = Carbon::parse($this->shiftEndTime)->format('h:i A');
    }

    public function startShift()
    {
        $shiftService = new ShiftService();
        $success = $shiftService->startShift(Auth::id(), false);

        if ($success) {
            $this->loadShiftData();
            $this->dispatch('shift-started');
            session()->flash('message', 'تم بدء العمل وفتح الفترة المحاسبية بنجاح للجميع.');
        } else {
            session()->flash('error', 'توجد فترة محاسبية مفتوحة حالياً.');
        }
    }

    public function closeShift()
    {
        $shiftService = new ShiftService();
        $success = $shiftService->closeShift(Auth::id(), false);

        if ($success) {
            $this->loadShiftData();
            $this->dispatch('shift-closed');
            session()->flash('message', 'تم إغلاق العمل وإنهاء الفترة المحاسبية بنجاح للجميع.');
        } else {
            session()->flash('error', 'لا توجد فترة محاسبية مفتوحة للإغلاق.');
        }
    }

    public function render()
    {
        return view('livewire.shift-status-banner');
    }
}
