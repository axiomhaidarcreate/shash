<div>
    @if(session()->has('success'))
        <div class="toast-container">
            <div class="toast toast-success">{{ session('success') }}</div>
        </div>
        <script>setTimeout(() => document.querySelector('.toast-container')?.remove(), 4000);</script>
    @endif

    <div class="page-header">
        <h1><i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary-color);"></i> إدارة المصروفات</h1>
        <p>تسجيل ومتابعة فواتير المصروفات والالتزامات التشغيلية</p>
    </div>

    <!-- Toggle Form Button -->
    <div style="margin-bottom: 1.5rem; display: flex; gap: 10px;">
        <button class="btn {{ $showForm ? 'btn-outline' : '' }}" wire:click="$toggle('showForm')">
            <i class="fa-solid fa-{{ $showForm ? 'eye-slash' : 'plus' }}"></i>
            {{ $showForm ? 'إخفاء نموذج الإضافة' : 'إضافة مصروف جديد' }}
        </button>
    </div>

    <!-- Add / Edit Form -->
    @if($showForm)
    <div class="card" style="margin-bottom: 2rem; border-top: 3px solid var(--primary-color);">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">
            <i class="fa-solid fa-{{ $isEditing ? 'pen' : 'plus-circle' }}"></i>
            {{ $isEditing ? 'تعديل المصروف' : 'إضافة فاتورة مصروف جديدة' }}
        </h3>

        <form wire:submit.prevent="save">
            <!-- Row 1 -->
            <div class="grid grid-cols-3" style="gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">اسم الفاتورة / بند الصرف <span style="color: var(--danger-color);">*</span></label>
                    <input type="text" class="form-control" wire:model="name"
                           placeholder="مثال: فاتورة كهرباء، إيجار...">
                    @error('name') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">رقم الفاتورة</label>
                    <input type="text" class="form-control" wire:model="invoice_number" placeholder="INV-001">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">المبلغ (ريال) <span style="color: var(--danger-color);">*</span></label>
                    <input type="number" step="0.01" class="form-control" wire:model="amount" placeholder="0.00">
                    @error('amount') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 2 -->
            <div class="grid grid-cols-3" style="gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">تاريخ الفاتورة <span style="color: var(--danger-color);">*</span></label>
                    <input type="date" class="form-control" wire:model="invoice_date">
                    @error('invoice_date') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">وقت / تاريخ السداد <span style="color: var(--danger-color);">*</span></label>
                    <input type="date" class="form-control" wire:model="payment_date" style="margin-bottom: 5px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.85rem; color: var(--text-secondary);">
                        <input type="checkbox" wire:model="addToReminders" style="width: 16px; height: 16px;">
                        <span>إضافة تنبيه في صفحة التذكيرات</span>
                    </label>
                    @error('payment_date') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">إرفاق الفاتورة (صورة / PDF)</label>
                    <input type="file" class="form-control" wire:model="attachment" accept="image/*,application/pdf">
                    <div wire:loading wire:target="attachment" style="font-size: 0.8rem; color: var(--warning-color); margin-top: 4px;">
                        <i class="fa-solid fa-spinner fa-spin"></i> جاري الرفع...
                    </div>
                    @if($existing_attachment)
                        <div style="margin-top: 5px; font-size: 0.82rem;">
                            <a href="{{ Storage::url($existing_attachment) }}" target="_blank" style="color: var(--primary-color);">
                                <i class="fa-solid fa-paperclip"></i> عرض المرفق الحالي
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Row 3 -->
            <div class="form-group">
                <label class="form-label">ملاحظات</label>
                <textarea class="form-control" wire:model="notes" rows="2"
                          placeholder="أي تفاصيل إضافية عن هذا المصروف..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                @if($isEditing)
                    <button type="button" class="btn btn-outline" wire:click="resetForm">
                        <i class="fa-solid fa-xmark"></i> إلغاء التعديل
                    </button>
                @endif
                <button type="submit" class="btn btn-success" style="padding: 10px 30px;">
                    <i class="fa-solid fa-save"></i>
                    {{ $isEditing ? 'حفظ التعديلات' : 'إضافة المصروف' }}
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Filters & Search -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="grid grid-cols-4" style="gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label"><i class="fa-solid fa-search"></i> بحث بالاسم أو رقم الفاتورة</label>
                <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="ابحث هنا...">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label"><i class="fa-solid fa-calendar-days"></i> من تاريخ</label>
                <input type="date" class="form-control" wire:model.live="filterFrom">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label"><i class="fa-solid fa-calendar-days"></i> إلى تاريخ</label>
                <input type="date" class="form-control" wire:model.live="filterTo">
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('admin.expenses.print') }}?from={{ $filterFrom }}&to={{ $filterTo }}&search={{ $search }}"
                   target="_blank" class="btn btn-outline" style="width: 100%; justify-content: center;">
                    <i class="fa-solid fa-print"></i> طباعة التقرير
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, rgba(212,175,55,0.1), rgba(212,175,55,0.05)); border-color: rgba(212,175,55,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
                <div style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 4px;">إجمالي المصروفات للفترة المحددة</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary-color);">
                    {{ number_format($this->total, 2) }} <span style="font-size: 1rem;">ريال</span>
                </div>
            </div>
            <div style="text-align: center;">
                <div style="color: var(--text-secondary); font-size: 0.85rem;">عدد الفواتير</div>
                <div style="font-size: 1.8rem; font-weight: 800;">{{ $this->expenses->count() }}</div>
            </div>
            <div style="text-align: left; color: var(--text-secondary); font-size: 0.85rem;">
                <i class="fa-solid fa-calendar-range"></i>
                من {{ $filterFrom }} إلى {{ $filterTo }}
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الفاتورة / البند</th>
                        <th>رقم الفاتورة</th>
                        <th>المبلغ</th>
                        <th>تاريخ الفاتورة</th>
                        <th>تاريخ السداد</th>
                        <th>مرفق</th>
                        <th>ملاحظات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->expenses as $index => $expense)
                    <tr>
                        <td style="color: var(--text-secondary);">{{ $index + 1 }}</td>
                        <td>
                            <span style="font-weight: 700;">{{ $expense->name }}</span>
                        </td>
                        <td>
                            <span style="font-family: monospace; font-size: 0.9rem; color: var(--text-secondary);">
                                {{ $expense->invoice_number ?: '-' }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: var(--danger-color);">
                                {{ number_format($expense->amount, 2) }} ريال
                            </span>
                        </td>
                        <td>{{ $expense->invoice_date }}</td>
                        <td>{{ $expense->payment_date }}</td>
                        <td>
                            @if($expense->attachment)
                                <a href="{{ Storage::url($expense->attachment) }}" target="_blank"
                                   class="btn btn-sm btn-outline" title="عرض المرفق">
                                    <i class="fa-solid fa-paperclip"></i>
                                </a>
                            @else
                                <span style="color: var(--text-secondary);">-</span>
                            @endif
                        </td>
                        <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $expense->notes }}">
                            {{ $expense->notes ?: '-' }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <button wire:click="edit({{ $expense->id }})" class="btn btn-sm btn-outline" title="تعديل">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:click="delete({{ $expense->id }})"
                                        wire:confirm="هل أنت متأكد من حذف هذا المصروف؟"
                                        class="btn btn-sm btn-danger" title="حذف">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                            لا توجد مصروفات مسجلة لهذه الفترة
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($this->expenses->count() > 0)
                <tfoot>
                    <tr style="background: rgba(212,175,55,0.08); font-weight: 800;">
                        <td colspan="3" style="text-align: center; padding: 12px;">الإجمالي</td>
                        <td style="color: var(--danger-color); font-size: 1.1rem;">{{ number_format($this->total, 2) }} ريال</td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
