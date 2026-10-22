<div>
    @if(session()->has('success'))
        <div class="toast-container">
            <div class="toast toast-success">{{ session('success') }}</div>
        </div>
        <script>setTimeout(() => document.querySelector('.toast-container')?.remove(), 4000);</script>
    @endif

    <div class="page-header">
        <h1>
            <i class="fa-solid fa-bell" style="color: var(--primary-color);"></i> التذكيرات
            @if($this->pendingCount > 0)
                <span class="badge badge-pending" style="font-size: 0.85rem; margin-right: 10px;">
                    {{ $this->pendingCount }} قيد الانتظار
                </span>
            @endif
            @if($this->overdueCount > 0)
                <span class="badge badge-danger" style="font-size: 0.85rem; background: rgba(244,67,54,0.15); color: var(--danger-color); border: 1px solid rgba(244,67,54,0.3);">
                    {{ $this->overdueCount }} متأخر!
                </span>
            @endif
        </h1>
        <p>إدارة التذكيرات الخاصة بك — الالتزامات، الدفعات، والمهام الدورية</p>
    </div>

    <!-- Toggle Button -->
    <div style="margin-bottom: 1.5rem;">
        <button class="btn {{ $showForm ? 'btn-outline' : '' }}" wire:click="$toggle('showForm')">
            <i class="fa-solid fa-{{ $showForm ? 'eye-slash' : 'plus' }}"></i>
            {{ $showForm ? 'إخفاء النموذج' : 'إضافة تذكير جديد' }}
        </button>
    </div>

    <!-- Add / Edit Form -->
    @if($showForm)
    <div class="card" style="margin-bottom: 2rem; border-top: 3px solid var(--primary-color);">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">
            <i class="fa-solid fa-{{ $isEditing ? 'pen' : 'bell-plus' }}"></i>
            {{ $isEditing ? 'تعديل التذكير' : 'إضافة تذكير جديد' }}
        </h3>

        <form wire:submit.prevent="save">
            <!-- Row 1 -->
            <div class="grid grid-cols-2" style="gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">اسم التذكير <span style="color: var(--danger-color);">*</span></label>
                    <input type="text" class="form-control" wire:model="title"
                           placeholder="مثال: سداد الإيجار، فاتورة الكهرباء...">
                    @error('title') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">المبلغ (اختياري)</label>
                    <input type="number" step="0.01" class="form-control" wire:model="amount" placeholder="0.00">
                </div>
            </div>

            <!-- Row 2 -->
            <div class="grid grid-cols-2" style="gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">تاريخ التذكير <span style="color: var(--danger-color);">*</span></label>
                    <input type="date" class="form-control" wire:model="reminder_date">
                    @error('reminder_date') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">الساعة</label>
                    <input type="time" class="form-control" wire:model="reminder_time">
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label">الوصف / الملاحظات</label>
                <textarea class="form-control" wire:model="description" rows="2"
                          placeholder="تفاصيل إضافية عن هذا التذكير..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                @if($isEditing)
                    <button type="button" class="btn btn-outline" wire:click="resetForm">
                        <i class="fa-solid fa-xmark"></i> إلغاء
                    </button>
                @endif
                <button type="submit" class="btn btn-success" style="padding: 10px 30px;">
                    <i class="fa-solid fa-bell"></i>
                    {{ $isEditing ? 'حفظ التعديلات' : 'إضافة التذكير' }}
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Filter Tabs -->
    <div style="display: flex; gap: 8px; margin-bottom: 1.5rem;">
        <button wire:click="$set('filterStatus','all')"
                class="btn btn-sm {{ $filterStatus === 'all' ? '' : 'btn-outline' }}">
            <i class="fa-solid fa-list"></i> الكل ({{ $this->reminders->count() }})
        </button>
        <button wire:click="$set('filterStatus','pending')"
                class="btn btn-sm {{ $filterStatus === 'pending' ? '' : 'btn-outline' }}"
                style="{{ $filterStatus === 'pending' ? '' : 'color: var(--warning-color); border-color: rgba(255,152,0,0.4);' }}">
            <i class="fa-solid fa-clock"></i> قيد الانتظار ({{ $this->pendingCount }})
        </button>
        <button wire:click="$set('filterStatus','done')"
                class="btn btn-sm {{ $filterStatus === 'done' ? '' : 'btn-outline' }}"
                style="{{ $filterStatus === 'done' ? '' : 'color: var(--success-color); border-color: rgba(76,175,80,0.4);' }}">
            <i class="fa-solid fa-check-circle"></i> مكتمل
        </button>
    </div>

    <!-- Reminders Table -->
    <div class="card">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الحالة</th>
                        <th>اسم التذكير</th>
                        <th>التاريخ</th>
                        <th>الساعة</th>
                        <th>المبلغ</th>
                        <th>الوصف</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->reminders as $reminder)
                    @php
                        $isOverdue = !$reminder->is_done && $reminder->reminder_date->lt(now()->startOfDay());
                        $isToday   = $reminder->reminder_date->isToday();
                    @endphp
                    <tr style="{{ $reminder->is_done ? 'opacity: 0.55;' : ($isOverdue ? 'background: rgba(244,67,54,0.05);' : ($isToday ? 'background: rgba(212,175,55,0.05);' : '')) }}">
                        <td>
                            @if($reminder->is_done)
                                {{-- DONE - Green clear indicator --}}
                                <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                    <button wire:click="toggleDone({{ $reminder->id }})"
                                            class="btn btn-sm"
                                            style="background: rgba(76,175,80,0.2); border: 1.5px solid var(--success-color); color: var(--success-color); font-weight: 800; gap: 6px;"
                                            title="انقر لإلغاء الإكمال">
                                        <i class="fa-solid fa-circle-check" style="font-size: 1rem;"></i> تم السداد ✓
                                    </button>
                                    @if($reminder->paid_at)
                                        <span style="font-size: 0.72rem; color: var(--text-secondary); padding-right: 4px;">
                                            <i class="fa-solid fa-calendar-check"></i>
                                            {{ \Carbon\Carbon::parse($reminder->paid_at)->format('Y-m-d') }}
                                            الساعة {{ \Carbon\Carbon::parse($reminder->paid_at)->format('g:i A') }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                {{-- PENDING - Orange waiting indicator --}}
                                <button wire:click="toggleDone({{ $reminder->id }})"
                                        class="btn btn-sm"
                                        style="background: rgba(255,152,0,0.12); border: 1.5px solid var(--warning-color); color: var(--warning-color); font-weight: 700; gap: 6px;"
                                        title="اضغط لتعيين كمسدد / مكتمل">
                                    <i class="fa-regular fa-circle"></i> لم يُسدد بعد
                                </button>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 700; {{ $reminder->is_done ? 'text-decoration: line-through;' : '' }}">
                                {{ $reminder->title }}
                            </span>
                            @if($isOverdue)
                                <span class="badge" style="background: rgba(244,67,54,0.15); color: var(--danger-color); border: 1px solid rgba(244,67,54,0.3); margin-right: 6px; font-size: 0.7rem;">متأخر!</span>
                            @elseif($isToday)
                                <span class="badge badge-pending" style="margin-right: 6px; font-size: 0.7rem;">اليوم</span>
                            @endif
                        </td>
                        <td>
                            <span style="{{ $isOverdue ? 'color: var(--danger-color); font-weight: 700;' : '' }}">
                                {{ $reminder->reminder_date->format('Y-m-d') }}
                            </span>
                        </td>
                        <td>
                            @if($reminder->reminder_time)
                                @php
                                    $t = \Carbon\Carbon::createFromFormat('H:i:s', $reminder->reminder_time);
                                    $hour = (int)$t->format('g');
                                    $min  = $t->format('i');
                                    $ampm = $t->format('A') === 'AM' ? 'ص' : 'م';
                                @endphp
                                <span style="font-weight: 600; direction: ltr; display: inline-block;">
                                    {{ $hour }}:{{ $min }} {{ $ampm }}
                                </span>
                            @else
                                <span style="color: var(--text-secondary);">-</span>
                            @endif
                        </td>
                        <td>
                            @if($reminder->amount)
                                <span style="font-weight: 700; color: var(--warning-color);">
                                    {{ number_format($reminder->amount, 2) }} ريال
                                </span>
                            @else
                                <span style="color: var(--text-secondary);">-</span>
                            @endif
                        </td>
                        <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--text-secondary);" title="{{ $reminder->description }}">
                            {{ $reminder->description ?: '-' }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <button wire:click="edit({{ $reminder->id }})" class="btn btn-sm btn-outline" title="تعديل">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:click="delete({{ $reminder->id }})"
                                        wire:confirm="هل أنت متأكد من حذف هذا التذكير؟"
                                        class="btn btn-sm btn-danger" title="حذف">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <i class="fa-solid fa-bell-slash" style="font-size: 2.5rem; margin-bottom: 10px; display: block; opacity: 0.4;"></i>
                            لا توجد تذكيرات بعد. أضف تذكيرك الأول!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
