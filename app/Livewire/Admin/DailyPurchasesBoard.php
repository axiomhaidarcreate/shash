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

    public function paginationView()
    {
        return 'livewire.custom-pagination';
    }

    // Role flag
    public $isAdmin = false;

    // Table Filters
    public $search = '';
    public $filterStatus = '';
    public $filterKitchen = '';

    // Employee: Invoice upload & inquiry modal
    public $showInvoiceModal = false;
    public $purchaseRequestId = null;
    public $purchasePrice = null;
    public $purchaseNotes = '';
    public $existingAdminReply = null;
    public $existingInvoiceImage = null;
    public $invoiceImage;

    // Admin: Confirmation & Review modal
    public $showAdminConfirmModal = false;
    public $adminConfirmId = null;
    public $adminConfirmItemName = '';
    public $adminConfirmKitchen = '';
    public $adminConfirmQuantity = '';
    public $adminConfirmPrice = null;
    public $adminConfirmNotes = '';
    public $adminConfirmInvoiceImage = null;
    public $newInvoiceImage;

    // Admin: Reply to Employee's Note Modal
    public $showAdminReplyModal = false;
    public $adminReplyId = null;
    public $adminReplyItemName = '';
    public $adminReplyKitchen = '';
    public $adminReplyQuantity = '';
    public $adminReplyPrice = null;
    public $adminReplyNotes = '';
    public $adminReplyInvoiceImage = null;
    public $adminReplyText = '';

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
    public $notes = '';
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
        $this->reset(['item_name', 'quantity', 'weight_or_ml', 'importance', 'kitchen_source', 'notes', 'attachment', 'isEditing', 'editId', 'existing_attachment', 'showForm']);
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
            'notes' => $this->notes ? trim($this->notes) : null,
            'attachment_path' => $path,
            'status' => 'معلق',
        ];

        if ($this->isEditing) {
            $req = DailyPurchaseRequest::find($this->editId);
            if ($req) $req->update($data);
            $this->dispatch('show-toast', message: 'تم تعديل الطلب بنجاح', type: 'success');
        } else {
            $data['requested_by'] = \Illuminate\Support\Facades\Auth::id() ?? 1;
            $data['sort_order'] = (DailyPurchaseRequest::min('sort_order') ?? 1) - 1;
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
            $this->notes = $req->notes ?? '';
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

    // Manual Reordering: Drag and Drop
    public function updateOrder($orderedIds)
    {
        if (!is_array($orderedIds)) return;

        foreach ($orderedIds as $index => $id) {
            DailyPurchaseRequest::where('id', $id)->update([
                'sort_order' => $index + 1
            ]);
        }

        $this->dispatch('show-toast', message: 'تم حفظ ترتيب الطلبات بنجاح', type: 'success');
    }

    // Manual Reordering: Move Up (Higher Priority)
    public function moveUp($id)
    {
        $current = DailyPurchaseRequest::find($id);
        if (!$current) return;

        $query = DailyPurchaseRequest::query();
        if (!$this->isAdmin) {
            $query->where(function ($q) {
                $q->where('status', '!=', 'تم الشراء')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'تم الشراء')
                          ->whereDate('updated_at', Carbon::today());
                  });
            });
        }

        $prev = $query->where('sort_order', '<', $current->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($prev) {
            $temp = $current->sort_order;
            $current->sort_order = $prev->sort_order;
            $prev->sort_order = $temp;
            $current->save();
            $prev->save();
            $this->dispatch('show-toast', message: 'تم تقديم الطلب لأعلى', type: 'success');
        }
    }

    // Manual Reordering: Move Down (Lower Priority)
    public function moveDown($id)
    {
        $current = DailyPurchaseRequest::find($id);
        if (!$current) return;

        $query = DailyPurchaseRequest::query();
        if (!$this->isAdmin) {
            $query->where(function ($q) {
                $q->where('status', '!=', 'تم الشراء')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'تم الشراء')
                          ->whereDate('updated_at', Carbon::today());
                  });
            });
        }

        $next = $query->where('sort_order', '>', $current->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($next) {
            $temp = $current->sort_order;
            $current->sort_order = $next->sort_order;
            $next->sort_order = $temp;
            $current->save();
            $next->save();
            $this->dispatch('show-toast', message: 'تم تأخير الطلب لأسفل', type: 'success');
        }
    }

    // Employee: open invoice / inquiry modal
    public function openInvoiceModal($id)
    {
        $this->purchaseRequestId = $id;
        $this->invoiceImage = null;
        $this->purchasePrice = null;
        $this->purchaseNotes = '';
        $this->existingAdminReply = null;
        $this->existingInvoiceImage = null;
        $req = DailyPurchaseRequest::find($id);
        if ($req) {
            if ($req->price !== null) {
                $this->purchasePrice = $req->price;
            }
            if ($req->notes !== null) {
                $this->purchaseNotes = $req->notes;
            }
            $this->existingAdminReply = $req->admin_reply;
            $this->existingInvoiceImage = $req->invoice_image;
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
        $this->purchaseNotes = '';
        $this->existingAdminReply = null;
        $this->existingInvoiceImage = null;
        $this->resetValidation();
    }

    // Employee: Save note / inquiry for manager without confirming purchase yet
    public function saveNoteOnly()
    {
        $this->validate([
            'purchasePrice' => 'nullable|numeric|min:0',
            'purchaseNotes' => 'required|string|max:1000',
            'invoiceImage' => 'nullable|image|max:5120',
        ], [
            'purchasePrice.numeric' => 'يجب أن يكون السعر قيمة عددية.',
            'purchasePrice.min' => 'يجب ألا يقل السعر عن 0.',
            'purchaseNotes.required' => 'يرجى كتابة الملاحظة أو الاستفسار أولاً قبل الإرسال.',
            'purchaseNotes.max' => 'يجب ألا تتجاوز الملاحظات 1000 حرف.',
            'invoiceImage.image' => 'يجب أن يكون المرفق صورة.',
            'invoiceImage.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
        ]);

        $req = DailyPurchaseRequest::find($this->purchaseRequestId);
        if ($req && $req->status === 'معلق') {
            $path = $this->invoiceImage ? $this->invoiceImage->store('daily_purchases_invoices', 'public') : $req->invoice_image;
            $minOrder = DailyPurchaseRequest::min('sort_order') ?? 0;
            $req->update([
                'price' => ($this->purchasePrice !== null && $this->purchasePrice !== '') ? $this->purchasePrice : $req->price,
                'notes' => trim($this->purchaseNotes),
                'invoice_image' => $path,
                'admin_reply' => null, // Reset previous reply so manager sees new note
                'admin_replied_at' => null,
                'sort_order' => $minOrder - 1, // Put at the very top (first row)
            ]);
            $this->dispatch('show-toast', message: 'تم إرسال الملاحظة للمدير العام وتثبيت الطلب في الصف الأول', type: 'info');
        }

        $this->closeInvoiceModal();
    }

    // Employee: confirm purchase with invoice image, price & notes
    public function confirmPurchase()
    {
        $this->validate([
            'purchasePrice' => 'nullable|numeric|min:0',
            'purchaseNotes' => 'nullable|string|max:1000',
            'invoiceImage' => 'nullable|image|max:5120',
        ], [
            'purchasePrice.numeric' => 'يجب أن يكون السعر قيمة عددية.',
            'purchasePrice.min' => 'يجب ألا يقل السعر عن 0.',
            'purchaseNotes.max' => 'يجب ألا تتجاوز الملاحظات 1000 حرف.',
            'invoiceImage.image' => 'يجب أن يكون المرفق صورة.',
            'invoiceImage.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
        ]);

        $req = DailyPurchaseRequest::find($this->purchaseRequestId);
        if ($req && $req->status === 'معلق') {
            $path = $this->invoiceImage ? $this->invoiceImage->store('daily_purchases_invoices', 'public') : $req->invoice_image;
            $maxSort = DailyPurchaseRequest::max('sort_order') ?? 0;
            $req->update([
                'status' => 'تم الشراء',
                'price' => ($this->purchasePrice !== null && $this->purchasePrice !== '') ? $this->purchasePrice : null,
                'notes' => $this->purchaseNotes ? trim($this->purchaseNotes) : null,
                'invoice_image' => $path,
                'is_admin_confirmed' => false,
                'admin_confirmed_at' => null,
                'sort_order' => $maxSort + 1,
            ]);
            $this->dispatch('show-toast', message: 'تم تأكيد الشراء بنجاح ونقله للأسفل', type: 'success');
        }

        $this->closeInvoiceModal();
    }

    // Admin: Open modal to reply to employee's note
    public function openAdminReplyModal($id)
    {
        if (!$this->isAdmin) return;

        $req = DailyPurchaseRequest::find($id);
        if (!$req) return;

        $this->adminReplyId = $req->id;
        $this->adminReplyItemName = $req->item_name;
        $this->adminReplyKitchen = $req->kitchen_source;
        $this->adminReplyQuantity = $req->quantity . ($req->weight_or_ml ? ' (' . $req->weight_or_ml . ')' : '');
        $this->adminReplyPrice = $req->price;
        $this->adminReplyNotes = $req->notes ?? '';
        $this->adminReplyInvoiceImage = $req->invoice_image;
        $this->adminReplyText = $req->admin_reply ?? 'نعم، تمام اشترها';
        $this->resetValidation();
        $this->showAdminReplyModal = true;
    }

    public function closeAdminReplyModal()
    {
        $this->showAdminReplyModal = false;
        $this->adminReplyId = null;
        $this->adminReplyItemName = '';
        $this->adminReplyKitchen = '';
        $this->adminReplyQuantity = '';
        $this->adminReplyPrice = null;
        $this->adminReplyNotes = '';
        $this->adminReplyInvoiceImage = null;
        $this->adminReplyText = '';
        $this->resetValidation();
    }

    public function submitAdminReply()
    {
        if (!$this->isAdmin || !$this->adminReplyId) return;

        $this->validate([
            'adminReplyText' => 'required|string|max:1000',
        ], [
            'adminReplyText.required' => 'يرجى كتابة نص الرد أولاً.',
            'adminReplyText.max' => 'يجب ألا يتجاوز الرد 1000 حرف.',
        ]);

        $req = DailyPurchaseRequest::find($this->adminReplyId);
        if ($req) {
            $req->update([
                'admin_reply' => trim($this->adminReplyText),
                'admin_replied_at' => now(),
            ]);
            $this->dispatch('show-toast', message: 'تم إرسال الرد لموظف المشتريات بنجاح', type: 'success');
        }

        $this->closeAdminReplyModal();
    }

    // Admin: Open confirmation modal to review/edit price, notes & invoice image
    public function openAdminConfirmModal($id)
    {
        if (!$this->isAdmin) return;

        $req = DailyPurchaseRequest::find($id);
        if (!$req) return;

        $this->adminConfirmId = $req->id;
        $this->adminConfirmItemName = $req->item_name;
        $this->adminConfirmKitchen = $req->kitchen_source;
        $this->adminConfirmQuantity = $req->quantity . ($req->weight_or_ml ? ' (' . $req->weight_or_ml . ')' : '');
        $this->adminConfirmPrice = $req->price;
        $this->adminConfirmNotes = $req->notes ?? '';
        $this->adminConfirmInvoiceImage = $req->invoice_image;
        $this->newInvoiceImage = null;
        $this->resetValidation();
        $this->showAdminConfirmModal = true;
    }

    public function closeAdminConfirmModal()
    {
        $this->showAdminConfirmModal = false;
        $this->adminConfirmId = null;
        $this->adminConfirmItemName = '';
        $this->adminConfirmKitchen = '';
        $this->adminConfirmQuantity = '';
        $this->adminConfirmPrice = null;
        $this->adminConfirmNotes = '';
        $this->adminConfirmInvoiceImage = null;
        $this->newInvoiceImage = null;
        $this->resetValidation();
    }

    // Admin: Save review edits, confirm request, and move it down
    public function saveAdminConfirmation()
    {
        if (!$this->isAdmin || !$this->adminConfirmId) return;

        $this->validate([
            'adminConfirmPrice' => 'nullable|numeric|min:0',
            'adminConfirmNotes' => 'nullable|string|max:1000',
            'newInvoiceImage' => 'nullable|image|max:5120',
        ], [
            'adminConfirmPrice.numeric' => 'يجب أن يكون السعر قيمة عددية.',
            'adminConfirmPrice.min' => 'يجب ألا يقل السعر عن 0.',
            'adminConfirmNotes.max' => 'يجب ألا تتجاوز الملاحظات 1000 حرف.',
            'newInvoiceImage.image' => 'يجب أن يكون المرفق صورة.',
            'newInvoiceImage.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
        ]);

        $req = DailyPurchaseRequest::find($this->adminConfirmId);
        if ($req) {
            $invoicePath = $req->invoice_image;
            if ($this->newInvoiceImage) {
                $invoicePath = $this->newInvoiceImage->store('daily_purchases_invoices', 'public');
            }

            $maxSort = DailyPurchaseRequest::max('sort_order') ?? 0;
            $req->update([
                'price' => ($this->adminConfirmPrice !== null && $this->adminConfirmPrice !== '') ? $this->adminConfirmPrice : null,
                'notes' => $this->adminConfirmNotes ? trim($this->adminConfirmNotes) : null,
                'invoice_image' => $invoicePath,
                'is_admin_confirmed' => true,
                'admin_confirmed_at' => now(),
                'sort_order' => $maxSort + 1,
            ]);

            $this->dispatch('show-toast', message: 'تم تأكيد واعتماد الطلب بنجاح ونقله للأسفل', type: 'success');
        }

        $this->closeAdminConfirmModal();
    }

    // Admin: Unconfirm purchased request and move it back to top
    public function unconfirmAdmin($id)
    {
        if (!$this->isAdmin) return;

        $req = DailyPurchaseRequest::find($id);
        if ($req) {
            $minSort = DailyPurchaseRequest::min('sort_order') ?? 0;
            $req->update([
                'is_admin_confirmed' => false,
                'admin_confirmed_at' => null,
                'sort_order' => $minSort - 1,
            ]);
            $this->dispatch('show-toast', message: 'تم إلغاء تأكيد الطلب وإعادته للأعلى', type: 'info');
        }
    }

    public function render()
    {
        $query = DailyPurchaseRequest::query()
            ->with('user');

        // For daily purchases employee:
        // - Orders not purchased yet (معلق / لم يتم الشراء) STAY visible until purchased.
        // - Orders that were purchased (تم الشراء) disappear at the end of the day (only show if purchased today).
        // For admin: show all requests without hiding anything.
        if (!$this->isAdmin) {
            $query->where(function ($q) {
                $q->where('status', '!=', 'تم الشراء')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'تم الشراء')
                          ->whereDate('updated_at', Carbon::today());
                  });
            });
        }

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

        // Ordering:
        // For Admin:
        // 1. Orders with notes from employee waiting for manager reply -> Top row! (الصف الأول)
        // 2. Orders with notes from employee that manager replied to -> Top rows of unpurchased
        // 3. Regular unpurchased orders (لم يتم الشراء) -> On top! (تكون فوق)
        // 4. Purchased orders waiting for Admin confirmation -> Down at bottom (تروح تحت عادي حتى لو لم يتم التأكيد بعد)
        // 5. Purchased orders confirmed by Admin -> Down at bottom
        if ($this->isAdmin) {
            $query->orderByRaw("
                CASE 
                    WHEN status = 'معلق' AND notes IS NOT NULL AND TRIM(notes) != '' AND (admin_reply IS NULL OR admin_reply = '') THEN 1
                    WHEN status = 'معلق' AND notes IS NOT NULL AND TRIM(notes) != '' THEN 2
                    WHEN status != 'تم الشراء' THEN 3
                    WHEN status = 'تم الشراء' AND (is_admin_confirmed = 0 OR is_admin_confirmed IS NULL) THEN 4
                    ELSE 5
                END ASC
            ");
        } else {
            $query->orderByRaw("
                CASE 
                    WHEN status != 'تم الشراء' THEN 1
                    ELSE 2
                END ASC
            ");
        }

        $requests = $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc')->paginate(30);

        return view('livewire.admin.daily-purchases-board', [
            'requests' => $requests,
            'totalPurchases' => $totalPurchases,
        ])->layout('components.layouts.admin', ['title' => 'مشتريات الطلبات اليومية']);
    }
}
