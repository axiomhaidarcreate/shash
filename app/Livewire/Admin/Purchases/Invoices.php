<?php

namespace App\Livewire\Admin\Purchases;

use Livewire\Component;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\PurchaseInstallment;
use Carbon\Carbon;

class Invoices extends Component
{
    use \Livewire\WithFileUploads;

    public $search = '';
    public $filterPeriod = 'all'; 
    public $filterDate = '';
    
    public $supplier_id = '';
    public $invoice_number = '';
    public $total_amount = 0;
    public $notes = '';
    public $status = 'received';
    public $attachment;

    // Invoice Items
    public $invoiceItems = [];
    public $searchProduct = '';
    public $selectedProductCategory = '';

    // Quick Add Product
    public $showAddProductModal = false;
    public $newProductName = '';
    public $newProductNameEn = '';
    public $newProductCategory = 'معسل';
    public $newProductUnit = 'حبة';
    public $newProductPrice = 0;
    public $newProductSku = '';
    public $newProductTax = 15;
    public $newProductImage;
    public $newProductDeduct = true;

    public $payment_type = 'cash';
    public $paid_amount = 0;
    
    public $installment_method = 'auto';
    public $installments_count = 1;
    public $installments_interval_days = 30;
    public $first_due_date = '';
    public $addToReminders = true;
    
    public $manual_installments = [];

    public $showInstallmentsModal = false;
    public $currentInvoiceInstallments = [];
    public $currentInvoiceId = null;
    
    public $isEditing = false;
    public $editId = null;
    public $existing_attachment = null;
    
    public $is_total_manual = false;

    protected $rules = [
        'supplier_id' => 'required|exists:suppliers,id',
        'invoice_number' => 'nullable|string|max:255',
        'total_amount' => 'required|numeric|min:0',
        'notes' => 'nullable|string',
        'status' => 'required|in:pending,received',
        'payment_type' => 'required|in:cash,installment',
        'paid_amount' => 'nullable|numeric|min:0',
        'attachment' => 'nullable|file|max:5120', // Max 5MB
        'invoiceItems' => 'required|array|min:1',
        'invoiceItems.*.product_id' => 'required|exists:products,id',
        'invoiceItems.*.quantity' => 'required|numeric|min:0.01',
        'invoiceItems.*.unit_price' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->filterDate = now()->format('Y-m-d');
        $this->first_due_date = now()->addDays(30)->format('Y-m-d');
    }

    public function addManualInstallment()
    {
        $this->manual_installments[] = ['amount' => 0, 'due_date' => now()->addDays(30)->format('Y-m-d')];
    }

    public function removeManualInstallment($index)
    {
        unset($this->manual_installments[$index]);
        $this->manual_installments = array_values($this->manual_installments);
    }

    public function resetFields()
    {
        $this->reset(['supplier_id', 'invoice_number', 'total_amount', 'notes', 'paid_amount', 'payment_type', 'attachment', 'isEditing', 'editId', 'existing_attachment', 'invoiceItems', 'searchProduct', 'selectedProductCategory']);
        $this->payment_type = 'cash';
        $this->manual_installments = [];
    }

    public function addProductToInvoice($productId, $productName, $productUnit = '')
    {
        $this->invoiceItems[] = [
            'product_id' => $productId,
            'product_name' => $productName,
            'unit' => $productUnit,
            'quantity' => 1,
            'stock_quantity' => 1,
            'old_quantity' => 1,
            'unit_price' => 0,
            'total_price' => 0
        ];
        $this->calculateTotal();
        $this->searchProduct = ''; // Reset search
    }

    public function updateInvoiceItem($index, $field, $value)
    {
        if (isset($this->invoiceItems[$index])) {
            $this->invoiceItems[$index][$field] = $value;
            
            if ($field === 'quantity') {
                $oldQty = (float)($this->invoiceItems[$index]['old_quantity'] ?? 1);
                $currStockQty = (float)($this->invoiceItems[$index]['stock_quantity'] ?? 1);
                if ($currStockQty === $oldQty) {
                    $this->invoiceItems[$index]['stock_quantity'] = $value;
                }
                $this->invoiceItems[$index]['old_quantity'] = $value;
            }

            if ($field === 'total_price') {
                $total = (float)$value;
                $qty = (float)($this->invoiceItems[$index]['quantity'] ?? 1);
                if ($qty > 0) {
                    $this->invoiceItems[$index]['unit_price'] = round($total / $qty, 2);
                }
            } else {
                $qty = (float)($this->invoiceItems[$index]['quantity'] ?? 0);
                $price = (float)($this->invoiceItems[$index]['unit_price'] ?? 0);
                $this->invoiceItems[$index]['total_price'] = round($qty * $price, 2);
            }

            $this->calculateTotal();
        }
    }

    public function removeInvoiceItem($index)
    {
        unset($this->invoiceItems[$index]);
        $this->invoiceItems = array_values($this->invoiceItems);
        $this->calculateTotal();
    }

    public function updatedTotalAmount()
    {
        $this->is_total_manual = true;
    }

    public function calculateTotal()
    {
        if ($this->is_total_manual) {
            return;
        }
        
        $total = 0;
        foreach ($this->invoiceItems as $item) {
            $total += (float)($item['total_price'] ?? 0);
        }
        $this->total_amount = round($total, 2);
    }

    public function openAddProductModal()
    {
        $this->reset(['newProductName', 'newProductNameEn', 'newProductCategory', 'newProductUnit', 'newProductPrice', 'newProductSku', 'newProductTax', 'newProductImage', 'newProductDeduct']);
        $this->showAddProductModal = true;
    }

    public function quickAddProduct()
    {
        $this->validate([
            'newProductName' => 'required|string|max:255',
            'newProductNameEn' => 'nullable|string|max:255',
            'newProductCategory' => 'required|string|max:255',
            'newProductUnit' => 'required|string|max:255',
            'newProductPrice' => 'required|numeric|min:0',
            'newProductTax' => 'required|numeric|min:0',
            'newProductImage' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($this->newProductImage) {
            $imagePath = $this->newProductImage->store('products', 'public');
        }

        $product = \App\Models\Product::create([
            'name' => $this->newProductName,
            'name_en' => $this->newProductNameEn,
            'category' => $this->newProductCategory,
            'unit' => $this->newProductUnit,
            'price' => $this->newProductPrice,
            'sku' => $this->newProductSku,
            'tax_percentage' => $this->newProductTax,
            'image' => $imagePath,
            'deduct_from_inventory' => $this->newProductDeduct,
            'is_active' => true,
        ]);

        $this->addProductToInvoice($product->id, $product->name, $product->unit);
        $this->showAddProductModal = false;
        session()->flash('success', 'تم إضافة المنتج بنجاح وإدراجه في الفاتورة');
    }

    public function save()
    {
        $this->validate();

        if (count($this->invoiceItems) === 0) {
            session()->flash('error', 'يجب إضافة منتج واحد على الأقل للفاتورة.');
            return;
        }

        $total = (float)$this->total_amount;
        
        $paid = (float)($this->paid_amount ?: 0);
        // If cash is selected and paid_amount is empty, assume fully paid
        if ($this->payment_type === 'cash' && $this->paid_amount === '') {
            $paid = $total;
        }

        $remaining = max(0, $total - $paid);

        $paymentStatus = 'unpaid';
        if ($paid >= $total && $total > 0) {
            $paymentStatus = 'paid';
        } elseif ($paid > 0) {
            $paymentStatus = 'partial';
        }

        $attachmentPath = $this->existing_attachment;
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('purchase_invoices', 'public');
        }

        if ($this->isEditing) {
            $invoice = PurchaseInvoice::find($this->editId);
            
            // Revert inventory for old items if previously received
            if ($invoice->status === 'received') {
                $this->revertInventoryForInvoice($invoice);
            }

            // Adjust supplier balance (revert old, apply new)
            $old_supplier = Supplier::find($invoice->supplier_id);
            $old_supplier->balance -= $invoice->remaining_amount;
            $old_supplier->save();
            
            $invoice->update([
                'supplier_id' => $this->supplier_id,
                'invoice_number' => $this->invoice_number,
                'total_amount' => $total,
                'notes' => $this->notes,
                'status' => $this->status,
                'payment_type' => $this->payment_type,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
                'payment_status' => $paymentStatus,
                'attachment' => $attachmentPath,
            ]);
            
            $new_supplier = Supplier::find($this->supplier_id);
            $new_supplier->balance += $remaining;
            $new_supplier->save();

            // Clear old items and add new ones
            $invoice->items()->delete();
            $this->saveInvoiceItemsAndInventory($invoice);
            
            session()->flash('success', 'تم تعديل الفاتورة بنجاح');
        } else {
            $invoice = PurchaseInvoice::create([
                'supplier_id' => $this->supplier_id,
                'invoice_number' => $this->invoice_number,
                'total_amount' => $total,
                'notes' => $this->notes,
                'status' => $this->status,
                'payment_type' => $this->payment_type,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
                'payment_status' => $paymentStatus,
                'attachment' => $attachmentPath,
            ]);

            $this->saveInvoiceItemsAndInventory($invoice);

            if ($this->payment_type === 'installment' && $remaining > 0) {
                $supplierName = Supplier::find($this->supplier_id)->name ?? '';
                if ($this->installment_method === 'auto') {
                    $count = (int)$this->installments_count;
                    if ($count > 0) {
                        $installmentAmount = round($remaining / $count, 2);
                        $dueDate = Carbon::parse($this->first_due_date);
                        $days = (int)$this->installments_interval_days;

                        for ($i = 0; $i < $count; $i++) {
                            PurchaseInstallment::create([
                                'purchase_invoice_id' => $invoice->id,
                                'amount' => $installmentAmount,
                                'due_date' => $dueDate->format('Y-m-d'),
                                'status' => 'pending',
                            ]);

                            if ($this->addToReminders) {
                                \App\Models\Reminder::create([
                                    'title' => 'قسط مشتريات: ' . $supplierName,
                                    'reminder_date' => $dueDate->format('Y-m-d'),
                                    'amount' => $installmentAmount,
                                    'description' => 'القسط رقم (' . ($i + 1) . ') لفاتورة المشتريات رقم: ' . ($this->invoice_number ?: 'غير محدد'),
                                ]);
                            }

                            $dueDate->addDays($days);
                        }
                    }
                } else {
                    $i = 0;
                    foreach ($this->manual_installments as $inst) {
                        if ($inst['amount'] > 0) {
                            PurchaseInstallment::create([
                                'purchase_invoice_id' => $invoice->id,
                                'amount' => $inst['amount'],
                                'due_date' => $inst['due_date'],
                                'status' => 'pending',
                            ]);

                            if ($this->addToReminders) {
                                \App\Models\Reminder::create([
                                    'title' => 'قسط مشتريات: ' . $supplierName,
                                    'reminder_date' => $inst['due_date'],
                                    'amount' => $inst['amount'],
                                    'description' => 'قسط (يدوي) لفاتورة المشتريات رقم: ' . ($this->invoice_number ?: 'غير محدد'),
                                ]);
                            }
                        }
                        $i++;
                    }
                }
            }
            
            $supplier = Supplier::find($this->supplier_id);
            $supplier->balance += $remaining;
            $supplier->save();
            
            session()->flash('success', 'تم حفظ فاتورة المشتريات بنجاح');
        }

        $this->resetFields();
    }

    private function saveInvoiceItemsAndInventory($invoice)
    {
        foreach ($this->invoiceItems as $item) {
            \App\Models\PurchaseInvoiceItem::create([
                'purchase_invoice_id' => $invoice->id,
                'product_id' => $item['product_id'],
                'unit' => $item['unit'] ?? null,
                'quantity' => $item['quantity'],
                'stock_quantity' => $item['stock_quantity'] ?? $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
            ]);

            if ($this->status === 'received') {
                $product = \App\Models\Product::find($item['product_id']);
                if ($product && $product->deduct_from_inventory) {
                    \App\Models\StockEntry::create([
                        'product_id' => $product->id,
                        'quantity_grams' => $item['stock_quantity'] ?? $item['quantity'], // Use stock_quantity
                        'type' => 'addition',
                        'status' => 'approved',
                        'notes' => 'فاتورة مشتريات رقم: ' . $invoice->invoice_number,
                        'created_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                    ]);
                }
            }
        }
    }

    private function revertInventoryForInvoice($invoice)
    {
        // This removes previously added stock entries from this invoice.
        // It relies on finding StockEntries by notes which is a bit brittle, but works for our simple logic.
        \App\Models\StockEntry::where('notes', 'فاتورة مشتريات رقم: ' . $invoice->invoice_number)
            ->where('type', 'addition')
            ->delete();
    }

    public function edit($id)
    {
        $invoice = PurchaseInvoice::with('items.product')->find($id);
        $this->editId = $invoice->id;
        $this->supplier_id = $invoice->supplier_id;
        $this->invoice_number = $invoice->invoice_number;
        $this->total_amount = $invoice->total_amount;
        $this->notes = $invoice->notes;
        $this->status = $invoice->status;
        $this->payment_type = $invoice->payment_type;
        $this->paid_amount = $invoice->paid_amount;
        $this->existing_attachment = $invoice->attachment;
        
        $this->invoiceItems = [];
        foreach ($invoice->items as $item) {
            $this->invoiceItems[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product ? $item->product->name : 'منتج محذوف',
                'unit' => $item->unit ?? ($item->product ? $item->product->unit : ''),
                'quantity' => $item->quantity,
                'stock_quantity' => $item->stock_quantity ?? $item->quantity,
                'old_quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ];
        }

        $this->isEditing = true;
    }

    public function delete($id)
    {
        $invoice = PurchaseInvoice::find($id);
        if ($invoice) {
            // Revert balance
            $supplier = $invoice->supplier;
            $supplier->balance -= $invoice->remaining_amount;
            $supplier->save();
            
            $invoice->delete();
            session()->flash('success', 'تم حذف الفاتورة بنجاح');
        }
    }

    public function viewInstallments($id)
    {
        $this->currentInvoiceId = $id;
        $this->loadInstallments();
        $this->showInstallmentsModal = true;
    }

    public function loadInstallments()
    {
        if ($this->currentInvoiceId) {
            $this->currentInvoiceInstallments = PurchaseInstallment::where('purchase_invoice_id', $this->currentInvoiceId)->orderBy('due_date')->get();
        }
    }

    public function closeInstallmentsModal()
    {
        $this->showInstallmentsModal = false;
        $this->currentInvoiceId = null;
        $this->currentInvoiceInstallments = [];
    }

    public function markInstallmentPaid($id)
    {
        $inst = PurchaseInstallment::find($id);
        if ($inst && $inst->status === 'pending') {
            $inst->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);
            
            $invoice = $inst->invoice;
            $invoice->paid_amount += $inst->amount;
            $invoice->remaining_amount -= $inst->amount;
            if ($invoice->remaining_amount <= 0) {
                $invoice->payment_status = 'paid';
            } else {
                $invoice->payment_status = 'partial';
            }
            $invoice->save();

            $supplier = $invoice->supplier;
            $supplier->balance -= $inst->amount;
            $supplier->save();

            $this->loadInstallments();
        }
    }

    public function render()
    {
        $query = PurchaseInvoice::with('supplier')->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->whereHas('supplier', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('invoice_number', 'like', '%' . $this->search . '%');
        }

        if ($this->filterPeriod !== 'all') {
            $baseDate = $this->filterDate ? Carbon::parse($this->filterDate) : Carbon::today();
            
            if ($this->filterPeriod === 'daily') {
                $query->whereDate('created_at', $baseDate);
            } elseif ($this->filterPeriod === 'weekly') {
                $query->whereBetween('created_at', [$baseDate->copy()->startOfWeek(), $baseDate->copy()->endOfWeek()]);
            } elseif ($this->filterPeriod === 'monthly') {
                $query->whereMonth('created_at', $baseDate->month)->whereYear('created_at', $baseDate->year);
            }
        }

        $invoices = $query->get();
        $suppliers = Supplier::all();
        $categories = \App\Models\Category::all();

        // Product search query
        $productQuery = \App\Models\Product::query();
        if ($this->selectedProductCategory) {
            $productQuery->where('category', $this->selectedProductCategory);
        }
        if ($this->searchProduct) {
            $productQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchProduct . '%')
                  ->orWhere('sku', 'like', '%' . $this->searchProduct . '%');
            });
        }
        $availableProducts = $productQuery->take(20)->get();

        return view('livewire.admin.purchases.invoices', [
            'invoices' => $invoices,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'availableProducts' => $availableProducts,
        ])->layout('components.layouts.admin', ['title' => 'فواتير المشتريات']);
    }
}
