<div>
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.purchases') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 0.5rem 1rem; border-radius: var(--radius-sm);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة المشتريات والمخزون
        </a>
    </div>

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>إدارة الموردين</h1>
            <p>سجل بيانات الموردين وحساباتهم</p>
        </div>
    </div>

    <!-- Add/Edit Form -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem; color: var(--primary-color);">
            <i class="fa-solid fa-{{ $isEditing ? 'pen' : 'plus' }}"></i> 
            {{ $isEditing ? 'تعديل بيانات المورد' : 'إضافة مورد جديد' }}
        </h3>
        
        <form wire:submit.prevent="save" class="grid grid-cols-4" style="gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">اسم المورد <span style="color:var(--danger-color);">*</span></label>
                <input type="text" class="form-control" wire:model="name" placeholder="أدخل اسم المورد..." required>
                @error('name') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الشركة</label>
                <input type="text" class="form-control" wire:model="company" placeholder="اسم الشركة...">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الاسم التجاري</label>
                <input type="text" class="form-control" wire:model="trade_name" placeholder="الاسم التجاري...">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">اسم المسؤول</label>
                <input type="text" class="form-control" wire:model="contact_person" placeholder="اسم المسؤول...">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" class="form-control" wire:model="phone" placeholder="رقم الهاتف...">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الرقم الضريبي</label>
                <input type="text" class="form-control" wire:model="tax_number" placeholder="الرقم الضريبي...">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الرقم الوطني</label>
                <input type="text" class="form-control" wire:model="national_id" placeholder="الرقم الوطني...">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">المدينة</label>
                <input type="text" class="form-control" wire:model="city" placeholder="المدينة...">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">العنوان</label>
                <input type="text" class="form-control" wire:model="address" placeholder="العنوان...">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الموقع</label>
                <input type="text" class="form-control" wire:model="website" placeholder="الموقع...">
            </div>
            <div class="form-group" style="margin-bottom: 0; grid-column: span 2;">
                <label class="form-label">الرصيد الافتتاحي</label>
                <input type="number" step="0.01" class="form-control" wire:model="balance" placeholder="0.00">
            </div>
            
            <div style="grid-column: span 4; display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
                @if($isEditing)
                    <button type="button" class="btn btn-outline" wire:click="resetFields">إلغاء التعديل</button>
                @endif
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-save"></i> حفظ المورد
                </button>
            </div>
        </form>
    </div>

    <!-- Filters and Table -->
    <div class="card">
        <div style="display: flex; gap: 15px; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="بحث باسم المورد أو الشركة...">
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الشركة</th>
                        <th>الهاتف</th>
                        <th>الرصيد</th>
                        <th>تاريخ الإضافة</th>
                        <th>إجراءات التسليم / طباعة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td><strong>{{ $supplier->name }}</strong></td>
                        <td>{{ $supplier->company ?? '-' }}</td>
                        <td dir="ltr" style="text-align: right;">{{ $supplier->phone ?? '-' }}</td>
                        <td style="color: {{ $supplier->balance < 0 ? 'var(--danger-color)' : 'var(--success-color)' }}; font-weight: bold;">
                            {{ number_format($supplier->balance, 2) }}
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;">{{ $supplier->created_at->format('Y-m-d') }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $supplier->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button wire:click="edit({{ $supplier->id }})" class="btn btn-sm btn-outline" title="تعديل">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="{{ route('invoice.supplierPrintThermal', $supplier->id) }}" target="_blank" class="btn btn-sm" style="background: #2196F3; color: white; text-decoration: none;" title="طباعة حراري">
                                    <i class="fa-solid fa-print"></i> حراري
                                </a>
                                <a href="{{ route('invoice.supplierPrint', $supplier->id) }}" target="_blank" class="btn btn-sm" style="background: #4CAF50; color: white; text-decoration: none;" title="طباعة A4">
                                    <i class="fa-solid fa-file-pdf"></i> A4
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            لا يوجد موردين مضافين أو لا توجد نتائج مطابقة للبحث.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
