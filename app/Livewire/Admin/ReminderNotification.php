<?php

namespace App\Livewire\Admin;

use App\Models\Reminder;
use Livewire\Component;

class ReminderNotification extends Component
{
    public function getExpiredRemindersProperty()
    {
        $nowDate = now()->format('Y-m-d');
        $nowTime = now()->format('H:i');

        return Reminder::where('is_done', false)
            ->where(function ($query) use ($nowDate, $nowTime) {
                $query->where('reminder_date', '<', $nowDate)
                      ->orWhere(function ($q) use ($nowDate, $nowTime) {
                          $q->where('reminder_date', $nowDate)
                            ->where(function ($sq) use ($nowTime) {
                                $sq->whereNull('reminder_time')
                                   ->orWhere('reminder_time', '<=', $nowTime);
                            });
                      });
            })
            ->get();
    }

    public function markAsDone($id)
    {
        Reminder::where('id', $id)->update([
            'is_done' => true, 
            'paid_at' => now()
        ]);
        
        $this->dispatch('reminder-done');
    }

    public function render()
    {
        return view('livewire.admin.reminder-notification');
    }
}
