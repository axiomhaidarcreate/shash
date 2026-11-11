<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DailyPurchaseRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DailyPurchasesBoard extends Component
{
    use WithPagination, \Livewire\WithFileUploads;

    // Role flag
    public $isAdmin = false;

    // Table Filters
    public $search = '';
    public $filterStatus = '';
    public $filterKitchen = '';

    // Employee: Invoice upload modal
    public $showInvoiceModal = false;
    public $purchaseRequestId = null;
    public $purchasePrice = null;
    public $invoiceImage;

    // Form fields
    public $showForm = false;
    public $isEditing = false;
    public $editId = null;
    
    public $item_name = '';
    public $quantity = 1;
    public $weight_or_ml = '';
    public $importance = 'عادي';
    public $kitchen_source = 'شيشة';
    public $needed_date;
    public $attachment;
    public $existing_attachment = null;

    protected $rules = [
        'item_name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
        'weight_or_ml' => 'nullable|string|max:255',
        'importance' => 'required|in:عاجل,متوسط,عادي',
        'kitchen_source' => 'required|in:شيشة,باريستا,مطعم,استقبال,تحايا',
        'needed_date' => 'required|date',
        'attachment' => 'nullable|file|max:5120',
    ];

    public function mount()
    {
        $this->isAdmin = Auth::user()->hasRole('admin');
        $this->needed_date = Carbon::today()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterKitchen()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset(['item_name', 'quantity', 'weight_or_ml', 'importance', 'kitchen_source', 'attachment', 'isEditing', 'editId', 'existing_attachment', 'showForm']);
        $this->needed_date = Carbon::today()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        $path = $this->existing_attachment;
        if ($this->attachment) {
            $path = $this->attachment->store('daily_purchases', 'public');
        }

        $data = [
            'item_name' => $this->item_name,
            'quantity' => $this->quantity,
            'weight_or_ml' => $this->weight_or_ml,
            'importance' => $this->importance,
            'kitchen_source' => $this->kitchen_source,
            'needed_date' => $this->needed_date,
            'attachment_path' => $path,
            'status' => 'معلق',
        ];

        if ($this->isEditing) {
            $req = DailyPurchaseRequest::find($this->editId);
            if ($req) $req->update($data);
            $this->dispatch('show-toast', message: 'تم تعديل الطلب بنجاح', type: 'success');
        } else {
            $data['requested_by'] = \Illuminate\Support\Facades\Auth::id() ?? 1;
            DailyPurchaseRequest::create($data);
            $this->dispatch('show-toast', message: 'تم إضافة الطلب بنجاح', type: 'success');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $req = DailyPurchaseRequest::find($id);
        if ($req) {
            $this->editId = $req->id;
            $this->item_name = $req->item_name;
            $this->quantity = $req->quantity;
            $this->weight_or_ml = $req->weight_or_ml;
            $this->importance = $req->importance;
            $this->kitchen_source = $req->kitchen_source;
            $this->needed_date = Carbon::parse($req->needed_date)->format('Y-m-d');
            $this->existing_attachment = $req->attachment_path;
            
            $this->isEditing = true;
            $this->showForm = true;
        }
    }

    public function delete($id)
    {
        $req = DailyPurchaseRequest::find($id);
        if ($req) {
            $req->delete();
            $this->dispatch('show-toast', message: 'تم حذف الطلب بنجاح', type: 'success');
        }
    }

    // Employee: open invoice upload modal
    public function openInvoiceModal($id)
    {
        $this->purchaseRequestId = $id;
        $this->invoiceImage = null;
        $this->purchasePrice = null;
        $req = DailyPurchaseRequest::find($id);
        if ($req && $req->price !== null) {
            $this->purchasePrice = $req->price;
        }
        $this->resetValidation();
        $this->showInvoiceModal = true;
    }

    public function closeInvoiceModal()
    {
        $this->showInvoiceModal = false;
        $this->purchaseRequestId = null;
        $this->invoiceImage = null;
        $this->purchasePrice = null;
        $this->resetValidation();
    }

    // Employee: confirm purchase with invoice image & price
    public function confirmPurchase()
    {
        $this->validate([
            'purchasePrice' => 'nullable|numeric|min:0',
            'invoiceImage' => 'nullable|image|max:5120',
        ], [
            'purchasePrice.numeric' => 'يجب أن يكون السعر قيمة عددية.',
            'purchasePrice.min' => 'يجب ألا يقل السعر عن 0.',
            'invoiceImage.image' => 'يجب أن يكون المرفق صورة.',
            'invoiceImage.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
        ]);

        $req = DailyPurchaseRequest::find($this->purchaseRequestId);
        if ($req && $req->status === 'معلق') {
            $path = $this->invoiceImage ? $this->invoiceImage->store('daily_purchases_invoices', 'public') : $req->invoice_image;
            $req->update([
                'status' => 'تم الشراء',
                'price' => ($this->purchasePrice !== null && $this->purchasePrice !== '') ? $this->purchasePrice : null,
                'invoice_image' => $path,
            ]);
            $this->dispatch('show-toast', message: 'تم تأكيد الشراء بنجاح', type: 'success');
        }

        $this->closeInvoiceModal();
    }

    public function render()
    {
        $query = DailyPurchaseRequest::query()
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where('item_name', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterKitchen) {
            $query->where('kitchen_source', $this->filterKitchen);
        }

        // Total purchases sum
        $totalPurchases = (clone $query)->whereNotNull('price')->sum('price');

        $requests = $query->paginate(15);

        return view('livewire.admin.daily-purchases-board', [
            'requests' => $requests,
            'totalPurchases' => $totalPurchases,
        ])->layout('components.layouts.admin', ['title' => 'مشتريات الطلبات اليومية']);
    }
}
