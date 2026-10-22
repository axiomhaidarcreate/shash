<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class Expenses extends Component
{
    use WithFileUploads;

    // Form fields
    public $name = '';
    public $invoice_number = '';
    public $amount = '';
    public $payment_date = '';
    public $invoice_date = '';
    public $attachment;
    public $notes = '';
    public $addToReminders = true;

    // State
    public $isEditing = false;
    public $editId = null;
    public $existing_attachment = null;

    // Filters
    public $search = '';
    public $filterFrom = '';
    public $filterTo = '';
    public $showForm = true;

    protected $rules = [
        'name'           => 'required|string|max:255',
        'invoice_number' => 'nullable|string|max:255',
        'amount'         => 'required|numeric|min:0',
        'payment_date'   => 'required|date',
        'invoice_date'   => 'required|date',
        'attachment'     => 'nullable|file|max:5120',
        'notes'          => 'nullable|string',
    ];

    public function mount()
    {
        $this->invoice_date  = now()->format('Y-m-d');
        $this->payment_date  = now()->format('Y-m-d');
        $this->filterFrom    = now()->startOfMonth()->format('Y-m-d');
        $this->filterTo      = now()->endOfMonth()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        $attachmentPath = $this->existing_attachment;
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('expenses', 'public');
        }

        $data = [
            'name'           => $this->name,
            'invoice_number' => $this->invoice_number,
            'amount'         => (float) $this->amount,
            'payment_date'   => $this->payment_date,
            'invoice_date'   => $this->invoice_date,
            'notes'          => $this->notes,
            'attachment'     => $attachmentPath,
        ];

        if ($this->isEditing) {
            Expense::find($this->editId)->update($data);
            session()->flash('success', 'تم تعديل المصروف بنجاح');
        } else {
            Expense::create($data);
            session()->flash('success', 'تم إضافة المصروف بنجاح');
        }

        if ($this->addToReminders && $this->payment_date) {
            \App\Models\Reminder::create([
                'title' => 'سداد مصروف: ' . $this->name,
                'reminder_date' => $this->payment_date,
                'amount' => (float) $this->amount,
                'description' => 'تذكير بسداد مصروف مضاف من شاشة المصروفات. رقم الفاتورة: ' . ($this->invoice_number ?: 'غير محدد'),
            ]);
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $this->editId              = $expense->id;
        $this->name                = $expense->name;
        $this->invoice_number      = $expense->invoice_number;
        $this->amount              = $expense->amount;
        $this->payment_date        = $expense->payment_date;
        $this->invoice_date        = $expense->invoice_date;
        $this->notes               = $expense->notes;
        $this->existing_attachment = $expense->attachment;
        $this->isEditing           = true;
        $this->showForm            = true;
    }

    public function delete($id)
    {
        Expense::findOrFail($id)->delete();
        session()->flash('success', 'تم حذف المصروف بنجاح');
    }

    public function resetForm()
    {
        $this->reset(['name', 'invoice_number', 'amount', 'notes', 'attachment', 'isEditing', 'editId', 'existing_attachment']);
        $this->invoice_date = now()->format('Y-m-d');
        $this->payment_date = now()->format('Y-m-d');
    }

    public function getExpensesProperty()
    {
        return Expense::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('invoice_number', 'like', "%{$this->search}%"))
            ->when($this->filterFrom, fn($q) => $q->where('invoice_date', '>=', $this->filterFrom))
            ->when($this->filterTo,   fn($q) => $q->where('invoice_date', '<=', $this->filterTo))
            ->orderByDesc('invoice_date')
            ->get();
    }

    public function getTotalProperty()
    {
        return $this->expenses->sum('amount');
    }

    public function render()
    {
        return view('livewire.admin.expenses')
            ->layout('components.layouts.admin', ['title' => 'المصروفات']);
    }
}
