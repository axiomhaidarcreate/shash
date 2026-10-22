<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\DailyPurchaseRequest;
use Carbon\Carbon;

class CreateDailyPurchaseRequest extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $kitchenSource;

    public $itemName;
    public $quantity;
    public $weightOrMl;
    public $importance = 'عادي';
    public $attachment;
    public $neededDate;

    protected $listeners = ['openDailyPurchaseModal' => 'openModal'];

    protected $rules = [
        'itemName' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
        'weightOrMl' => 'nullable|string|max:255',
        'importance' => 'required|in:عاجل,متوسط,عادي',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'neededDate' => 'required|date',
        'kitchenSource' => 'required|in:شيشة,باريستا,مطعم,استقبال,تحايا',
    ];

    public function mount($kitchenSource)
    {
        $this->kitchenSource = $kitchenSource;
        $this->neededDate = Carbon::today()->format('Y-m-d');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->neededDate = Carbon::today()->format('Y-m-d');
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['itemName', 'quantity', 'weightOrMl', 'importance', 'attachment']);
    }

    public function submit()
    {
        $this->validate();

        $attachmentPath = null;
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('daily_purchases', 'public');
        }

        DailyPurchaseRequest::create([
            'item_name' => $this->itemName,
            'quantity' => $this->quantity,
            'weight_or_ml' => $this->weightOrMl,
            'importance' => $this->importance,
            'attachment_path' => $attachmentPath,
            'needed_date' => $this->neededDate,
            'kitchen_source' => $this->kitchenSource,
            'requested_by' => auth()->id(),
            'status' => 'معلق'
        ]);

        $this->closeModal();
        $this->dispatch('show-toast', message: 'تم إرسال الطلب بنجاح', type: 'success');
    }

    public function render()
    {
        return view('livewire.create-daily-purchase-request');
    }
}
