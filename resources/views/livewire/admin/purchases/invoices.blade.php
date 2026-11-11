<div>
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.purchases') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 0.5rem 1rem; border-radius: var(--radius-sm);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة المشتريات والمخزون
        </a>
    </div>

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>فواتير المشتريات</h1>
            <p>إدارة وتتبع فواتير الشراء من الموردين والأقساط</p>
        </div>
    </div>

    <!-- Add Form -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem; color: var(--primary-color);">
            <i class="fa-solid fa-{{ $isEditing ? 'pen' : 'file-circle-plus' }}"></i> {{ $isEditing ? 'تعديل الفاتورة' : 'إضافة فاتورة جديدة' }}
        </h3>
        
        <form wire:submit.prevent="save" style="gap: 1rem; align-items: start;">
            <div class="grid grid-cols-3" style="gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">المورد</label>
                    <select class="form-control" wire:model="supplier_id" required>
                        <option value="">-- اختر المورد --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">رقم الفاتورة (المرجعي)</label>
                    <input type="text" class="form-control" wire:model="invoice_number" placeholder="مثال: INV-10023">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">حالة الاستلام</label>
                    <select class="form-control" wire:model="status">
                        <option value="received">تم الاستلام (يضاف للمخزون)</option>
                        <option value="pending">معلقة / قيد الانتظار</option>
                    </select>
                </div>
            </div>

            <!-- Invoice Items Section -->
            <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem;">
                <h4 style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                    <span><i class="fa-solid fa-boxes-stacked" style="color: var(--info-color);"></i> أصناف الفاتورة</span>
                    <button type="button" wire:click="openAddProductModal" class="btn btn-sm btn-outline">
                        <i class="fa-solid fa-plus"></i> إضافة منتج جديد غير موجود
                    </button>
                </h4>
                
                <!-- Product Search & Filter -->
                <div class="grid grid-cols-2" style="gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="searchProduct" placeholder="البحث باسم المنتج أو رقم الصنف SKU...">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <select class="form-control" wire:model.live="selectedProductCategory">
                            <option value="">-- تصفية حسب التصنيف --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Available Products Grid -->
                @if($searchProduct || $selectedProductCategory)
                    <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 15px; margin-bottom: 1rem; border-bottom: 1px dashed rgba(255,255,255,0.1);">
                        @forelse($availableProducts as $prod)
                            <div class="card" wire:click="addProductToInvoice({{ $prod->id }}, '{{ addslashes($prod->name) }}', '{{ $prod->unit ?? '' }}')" style="min-width: 150px; padding: 10px; cursor: pointer; text-align: center; background: rgba(255,255,255,0.05); margin-bottom: 0; transition: 0.2s;">
                                <div style="font-weight: bold; font-size: 0.9rem;">{{ $prod->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $prod->category }}</div>
                                <div style="font-size: 0.8rem; margin-top: 5px; color: var(--success-color);"><i class="fa-solid fa-plus"></i> إضافة</div>
                            </div>
                        @empty
                            <div style="color: var(--text-secondary); width: 100%; text-align: center; padding: 10px;">لا يوجد منتجات مطابقة للبحث.</div>
                        @endforelse
                    </div>
                @endif

                <!-- Selected Items Table -->
                <table class="data-table" style="width: 100%; margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th style="width: 120px;">الوحدة</th>
                            <th style="width: 120px;">سعر الوحدة</th>
                            <th style="width: 100px;">الكمية (فاتورة)</th>
                            <th style="width: 120px;">الكمية (مخزون)</th>
                            <th style="width: 120px;">الإجمالي الفرعي</th>
                            <th style="width: 80px; text-align: center;">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoiceItems as $index => $item)
                        <tr>
                            <td>{{ $item['product_name'] }}</td>
                            <td>
                                <input type="text" class="form-control" wire:model.live.debounce.500ms="invoiceItems.{{ $index }}.unit" style="padding: 5px; height: 35px;" placeholder="حبة، كرتون...">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control" wire:model.live.debounce.500ms="invoiceItems.{{ $index }}.unit_price" wire:change="updateInvoiceItem({{ $index }}, 'unit_price', $event.target.value)" style="padding: 5px; height: 35px;">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control" wire:model.live.debounce.500ms="invoiceItems.{{ $index }}.quantity" wire:change="updateInvoiceItem({{ $index }}, 'quantity', $event.target.value)" style="padding: 5px; height: 35px;">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control" wire:model.live.debounce.500ms="invoiceItems.{{ $index }}.stock_quantity" style="padding: 5px; height: 35px; border-color: var(--info-color);" title="الكمية التي ستضاف إلى المخزون">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control" wire:model.live.debounce.500ms="invoiceItems.{{ $index }}.total_price" wire:change="updateInvoiceItem({{ $index }}, 'total_price', $event.target.value)" style="padding: 5px; height: 35px; font-weight: bold; color: var(--primary-color);">
                            </td>
                            <td style="text-align: center;">
                                <button type="button" wire:click="removeInvoiceItem({{ $index }})" class="btn btn-sm btn-danger" style="padding: 5px 10px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                                يرجى البحث وإضافة منتجات للفاتورة
                                @error('invoiceItems') <div style="color: var(--danger-color); margin-top: 5px;">{{ $message }}</div> @enderror
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- الدفع والتقسيط -->
            <div style="background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.2); padding: 1.5rem; border-radius: 8px; margin-top: 1rem; margin-bottom: 1rem;">
                <h4 style="margin-bottom: 1rem;"><i class="fa-solid fa-money-bill-wave" style="color: var(--success-color);"></i> تفاصيل الدفع</h4>
                <div class="grid grid-cols-3" style="gap: 1rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">طريقة الدفع</label>
                        <select class="form-control" wire:model.live="payment_type" style="height: 100%;">
                            <option value="cash">نقداً (كاش / تحويل)</option>
                            <option value="installment">دفع بالتقسيط (آجل)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <div style="display: flex; gap: 10px; height: 100%;">
                            <div style="flex: 1;">
                                <label class="form-label">الإجمالي الكلي</label>
                                <div style="display: flex; align-items: center; border: 1px solid var(--primary-color); border-radius: 4px; background: rgba(var(--primary-color-rgb), 0.1); padding: 0 5px;">
                                    <input type="number" step="0.01" class="form-control" wire:model.live.debounce.500ms="total_amount" style="border: none; background: transparent; padding: 5px; color: var(--primary-color); font-weight: bold; height: 35px;">
                                </div>
                            </div>
                            <div style="flex: 2;">
                                <label class="form-label">المبلغ المدفوع (مقدم / كاش)</label>
                                <input type="number" step="0.01" class="form-control" wire:model.live="paid_amount" placeholder="يترك فارغاً إذا دفع بالكامل كاش" style="height: 37px;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">المبلغ المتبقي</label>
                        <input type="text" class="form-control" value="{{ max(0, (float)$total_amount - (float)($paid_amount ?: 0)) }}" disabled style="background: rgba(0,0,0,0.2); font-weight: bold; color: var(--danger-color); height: 100%;">
                    </div>
                </div>

                @if($payment_type === 'installment')
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); animation: slideDown 0.3s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h5 style="color: var(--primary-color); margin: 0;">جدولة الأقساط</h5>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem; color: var(--text-secondary);">
                            <input type="checkbox" wire:model="addToReminders" style="width: 16px; height: 16px;">
                            <span>إضافة الأقساط إلى صفحة التذكيرات تلقائياً</span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">طريقة جدولة الأقساط</label>
                        <div style="display: flex; gap: 20px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" wire:model.live="installment_method" value="auto">
                                <span>حساب تلقائي</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" wire:model.live="installment_method" value="manual">
                                <span>إدخال يدوي</span>
                            </label>
                        </div>
                    </div>

                    @if($installment_method === 'auto')
                    <div class="grid grid-cols-3" style="gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">عدد الأقساط</label>
                            <input type="number" class="form-control" wire:model="installments_count" min="1">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">المدة بين كل قسط (بالأيام)</label>
                            <input type="number" class="form-control" wire:model="installments_interval_days" min="1">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">تاريخ استحقاق القسط الأول</label>
                            <input type="date" class="form-control" wire:model="first_due_date">
                        </div>
                    </div>
                    @else
                    <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px;">
                        @foreach($manual_installments as $index => $inst)
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: flex-end;">
                            <div style="flex: 1;">
                                <label class="form-label">مبلغ القسط</label>
                                <input type="number" step="0.01" class="form-control" wire:model="manual_installments.{{ $index }}.amount">
                            </div>
                            <div style="flex: 1;">
                                <label class="form-label">تاريخ الاستحقاق</label>
                                <input type="date" class="form-control" wire:model="manual_installments.{{ $index }}.due_date">
                            </div>
                            <button type="button" wire:click="removeManualInstallment({{ $index }})" class="btn btn-danger" style="padding: 0 1rem; height: 44px;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                        <button type="button" wire:click="addManualInstallment" class="btn btn-sm btn-outline">
                            <i class="fa-solid fa-plus"></i> إضافة قسط
                        </button>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <div class="grid grid-cols-2" style="gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">ملاحظات</label>
                    <input type="text" class="form-control" wire:model="notes" placeholder="ملاحظات تفصيلية عن الفاتورة...">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">إرفاق صورة الفاتورة</label>
                    <input type="file" class="form-control" wire:model="attachment" accept="image/*,application/pdf">
                    <div wire:loading wire:target="attachment" style="font-size: 0.8rem; color: var(--warning-color); margin-top: 5px;">جاري الرفع...</div>
                    @if($existing_attachment)
                        <div style="margin-top: 5px; font-size: 0.85rem;">
                            <a href="{{ Storage::url($existing_attachment) }}" target="_blank" style="color: var(--primary-color);">
                                <i class="fa-solid fa-paperclip"></i> عرض المرفق الحالي
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                @if($isEditing)
                    <button type="button" class="btn btn-outline" wire:click="resetFields">إلغاء التعديل</button>
                @endif
                <button type="submit" class="btn btn-success" style="padding: 10px 30px; font-size: 1.1em;">
                    <i class="fa-solid fa-save"></i> {{ $isEditing ? 'حفظ التعديلات' : 'حفظ الفاتورة' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Filters and Table -->
    <div class="card">
        <div style="display: flex; gap: 15px; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="بحث برقم الفاتورة أو المورد...">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <select class="form-control" wire:model.live="filterPeriod">
                    <option value="all">كل الأوقات</option>
                    <option value="daily">يومي</option>
                    <option value="weekly">أسبوعي</option>
                    <option value="monthly">شهري</option>
                </select>
            </div>
            @if($filterPeriod !== 'all')
            <div style="flex: 1; min-width: 150px;">
                <input type="date" class="form-control" wire:model.live="filterDate">
            </div>
            @endif
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>المورد</th>
                        <th>الإجمالي</th>
                        <th>المدفوع / المتبقي</th>
                        <th>حالة الدفع</th>
                        <th>المرفقات</th>
                        <th>تاريخ الفاتورة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td><span class="badge badge-inactive">{{ $invoice->invoice_number ?? 'بدون رقم' }}</span></td>
                        <td><strong>{{ $invoice->supplier->name ?? 'غير معروف' }}</strong></td>
                        <td style="font-weight: bold; color: var(--primary-color);">
                            {{ number_format($invoice->total_amount, 2) }}
                        </td>
                        <td>
                            @if($invoice->payment_type === 'cash' || $invoice->payment_status === 'paid')
                                <span style="color: var(--success-color);">الكل مدفوع</span>
                            @else
                                <div style="color: var(--success-color);">مدفوع: {{ number_format($invoice->paid_amount, 2) }}</div>
                                <div style="color: var(--danger-color);">متبقي: {{ number_format($invoice->remaining_amount, 2) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($invoice->payment_status === 'paid')
                                <span class="badge" style="background: rgba(76,175,80,0.1); color: #4CAF50; border: 1px solid rgba(76,175,80,0.3);">مكتملة الدفع</span>
                            @elseif($invoice->payment_status === 'partial')
                                <span class="badge" style="background: rgba(33,150,243,0.1); color: #2196F3; border: 1px solid rgba(33,150,243,0.3);">مدفوعة جزئياً</span>
                            @else
                                <span class="badge" style="background: rgba(244,67,54,0.1); color: #F44336; border: 1px solid rgba(244,67,54,0.3);">غير مدفوعة</span>
                            @endif
                        </td>
                        <td>
                            @if($invoice->attachment)
                                <a href="{{ Storage::url($invoice->attachment) }}" target="_blank" class="btn btn-sm btn-outline" title="عرض المرفق">
                                    <i class="fa-solid fa-paperclip"></i>
                                </a>
                            @else
                                <span style="color: var(--text-secondary); font-size: 0.85em;">-</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;">{{ $invoice->created_at->format('Y-m-d') }}</div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                @if($invoice->payment_type === 'installment')
                                    <button wire:click="viewInstallments({{ $invoice->id }})" class="btn btn-sm" style="background: var(--warning-color); color: #000;" title="إدارة الأقساط">
                                        <i class="fa-solid fa-list-ol"></i>
                                    </button>
                                @endif
                                <button wire:click="edit({{ $invoice->id }})" class="btn btn-sm btn-outline" title="تعديل">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="{{ route('invoice.purchasePrint', $invoice->id) }}" target="_blank" class="btn btn-sm" style="background: #4CAF50; color: white; text-decoration: none;" title="طباعة">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <button wire:click="delete({{ $invoice->id }})" wire:confirm="هل أنت متأكد من حذف هذه الفاتورة؟ سيتم إلغاء تأثيرها على المورد والأقساط" class="btn btn-sm btn-danger" title="حذف">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            لا توجد فواتير مطابقة لخيارات البحث أو الفلتر.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal للأقساط -->
    @if($showInstallmentsModal)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div class="card" style="width: 600px; max-width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
            <button wire:click="closeInstallmentsModal" class="btn btn-outline" style="position: absolute; top: 15px; left: 15px; padding: 5px 10px;">
                <i class="fa-solid fa-times"></i> إغلاق
            </button>
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                <i class="fa-solid fa-file-invoice-dollar"></i> تفاصيل الأقساط
            </h3>

            @if(count($currentInvoiceInstallments) > 0)
                <table class="data-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>رقم القسط</th>
                            <th>المبلغ</th>
                            <th>تاريخ الاستحقاق</th>
                            <th>الحالة</th>
                            <th>إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currentInvoiceInstallments as $index => $inst)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: bold; color: var(--primary-color);">{{ number_format($inst->amount, 2) }}</td>
                            <td style="color: {{ \Carbon\Carbon::parse($inst->due_date)->isPast() && $inst->status === 'pending' ? 'var(--danger-color)' : 'inherit' }}">
                                {{ $inst->due_date }}
                            </td>
                            <td>
                                @if($inst->status === 'paid')
                                    <span class="badge badge-success">مدفوع</span>
                                @else
                                    <span class="badge badge-inactive">قيد الانتظار</span>
                                @endif
                            </td>
                            <td>
                                @if($inst->status === 'pending')
                                    <button wire:click="markInstallmentPaid({{ $inst->id }})" class="btn btn-sm btn-success" title="تأكيد الدفع للمورد">
                                        <i class="fa-solid fa-check"></i> تسديد
                                    </button>
                                @else
                                    <span style="font-size: 0.8em; color: var(--text-secondary);">{{ \Carbon\Carbon::parse($inst->paid_at)->format('Y-m-d') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: var(--text-secondary); margin: 2rem 0;">لا توجد أقساط مسجلة لهذه الفاتورة.</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Modal إضافة منتج جديد -->
    @if($showAddProductModal)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div class="card" style="width: 500px; max-width: 90%; position: relative;">
            <button wire:click="$set('showAddProductModal', false)" class="btn btn-outline" style="position: absolute; top: 15px; left: 15px; padding: 5px 10px;">
                <i class="fa-solid fa-times"></i> إغلاق
            </button>
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">
                <i class="fa-solid fa-box-open"></i> إضافة منتج جديد
            </h3>

            <div class="grid grid-cols-2" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">اسم المنتج</label>
                    <input type="text" class="form-control" wire:model="newProductName" placeholder="مثال: معسل تفاحتين فاخر">
                    @error('newProductName') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزية (اختياري)</label>
                    <input type="text" class="form-control" wire:model="newProductNameEn" placeholder="مثال: Double Apple">
                    @error('newProductNameEn') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">التصنيف</label>
                    <select class="form-control" wire:model="newProductCategory">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('newProductCategory') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">الوحدة</label>
                    <select class="form-control" wire:model="newProductUnit">
                        <option value="حبة">حبة / عدد</option>
                        <option value="جرام">جرام</option>
                        <option value="كيلو">كيلو</option>
                        <option value="لتر">لتر</option>
                        <option value="مل">مل</option>
                        <option value="كرتون">كرتون</option>
                    </select>
                    @error('newProductUnit') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">سعر البيع (للزائر)</label>
                    <input type="number" step="0.01" class="form-control" wire:model="newProductPrice">
                    @error('newProductPrice') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">نسبة الضريبة (%)</label>
                    <input type="number" step="0.01" class="form-control" wire:model="newProductTax">
                    @error('newProductTax') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">رقم الصنف (SKU) اختياري</label>
                    <input type="text" class="form-control" wire:model="newProductSku">
                </div>
                
                <div class="form-group">
                    <label class="form-label">صورة المرفق (اختياري)</label>
                    <input type="file" class="form-control" wire:model="newProductImage" accept="image/*">
                    @error('newProductImage') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group" style="margin-top: 0.5rem;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" wire:model="newProductDeduct" style="width: 18px; height: 18px;">
                    <span style="font-weight: 500;">يخصم من المخزون (إضافة إلى المخزون)</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 1rem;">
                <button wire:click="quickAddProduct" class="btn btn-success">
                    <i class="fa-solid fa-save"></i> حفظ وإضافة للفاتورة
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
