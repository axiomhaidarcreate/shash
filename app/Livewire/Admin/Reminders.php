<?php

namespace App\Livewire\Admin;

use App\Models\Reminder;
use Livewire\Component;
use Carbon\Carbon;

class Reminders extends Component
{
    // Form fields
    public $title = '';
    public $reminder_date = '';
    public $reminder_time = '';
    public $amount = '';
    public $description = '';

    // State
    public $isEditing = false;
    public $editId = null;
    public $showForm = true;
    public $filterStatus = 'all'; // all, pending, done

    protected $rules = [
        'title'         => 'required|string|max:255',
        'reminder_date' => 'required|date',
        'reminder_time' => 'nullable|string',
        'amount'        => 'nullable|numeric|min:0',
        'description'   => 'nullable|string',
    ];

    public function mount()
    {
        $this->reminder_date = now()->format('Y-m-d');
        $this->reminder_time = now()->format('H:i');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title'         => $this->title,
            'reminder_date' => $this->reminder_date,
            'reminder_time' => $this->reminder_time ?: null,
            'amount'        => $this->amount ?: null,
            'description'   => $this->description,
        ];

        if ($this->isEditing) {
            Reminder::find($this->editId)->update($data);
            session()->flash('success', 'تم تعديل التذكير بنجاح');
        } else {
            Reminder::create($data);
            session()->flash('success', 'تم إضافة التذكير بنجاح');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $reminder = Reminder::findOrFail($id);
        $this->editId         = $reminder->id;
        $this->title          = $reminder->title;
        $this->reminder_date  = $reminder->reminder_date->format('Y-m-d');
        $this->reminder_time  = $reminder->reminder_time;
        $this->amount         = $reminder->amount;
        $this->description    = $reminder->description;
        $this->isEditing      = true;
        $this->showForm       = true;
    }

    public function toggleDone($id)
    {
        $reminder = Reminder::findOrFail($id);
        $isDone = !$reminder->is_done;
        $reminder->update([
            'is_done' => $isDone,
            'paid_at' => $isDone ? now() : null,
        ]);
    }

    public function delete($id)
    {
        Reminder::findOrFail($id)->delete();
        session()->flash('success', 'تم حذف التذكير');
    }

    public function resetForm()
    {
        $this->reset(['title', 'amount', 'description', 'isEditing', 'editId']);
        $this->reminder_date = now()->format('Y-m-d');
        $this->reminder_time = now()->format('H:i');
    }

    public function getRemindersProperty()
    {
        return Reminder::query()
            ->when($this->filterStatus === 'pending', fn($q) => $q->where('is_done', false))
            ->when($this->filterStatus === 'done',    fn($q) => $q->where('is_done', true))
            ->orderBy('reminder_date')
            ->orderBy('reminder_time')
            ->get();
    }

    public function getPendingCountProperty()
    {
        return Reminder::where('is_done', false)->count();
    }

    public function getOverdueCountProperty()
    {
        return Reminder::where('is_done', false)
            ->where('reminder_date', '<', now()->format('Y-m-d'))
            ->count();
    }

    public function render()
    {
        return view('livewire.admin.reminders')
            ->layout('components.layouts.admin', ['title' => 'التذكيرات']);
    }
}
