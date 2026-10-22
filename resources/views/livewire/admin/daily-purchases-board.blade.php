<div>
    <div class="page-header">
        <h1><i class="fa-solid fa-cart-arrow-down" style="color: var(--primary-color);"></i> مشتريات الطلبات اليومية</h1>
        <p>إدارة طلبات النواقص الواردة من المطابخ ومتابعة عمليات الشراء الخاصة بها.</p>
    </div>

    {{-- Admin Only: Toggle Form Button --}}
    @if($isAdmin)
    <div style="margin-bottom: 1.5rem; display: flex; gap: 10px;">
        <button class="btn {{ $showForm ? 'btn-outline' : '' }}" wire:click="$toggle('showForm')">
            <i class="fa-solid fa-{{ $showForm ? 'eye-slash' : 'plus' }}"></i>
            {{ $showForm ? 'إخفاء النموذج' : 'إضافة طلب شراء جديد' }}
        </button>
    </div>

    {{-- Admin Only: Add / Edit Form --}}
    @if($showForm)
    <div class="card" style="margin-bottom: 2rem; border-top: 3px solid var(--primary-color);">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">
            <i class="fa-solid fa-{{ $isEditing ? 'pen' : 'plus-circle' }}"></i>
            {{ $isEditing ? 'تعديل الطلب' : 'إضافة طلب شراء جديد' }}
        </h3>

        <form wire:submit.prevent="save">
            <!-- Row 1 -->
            <div class="grid grid-cols-3" style="gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">المطبخ المستفيد <span style="color: var(--danger-color);">*</span></label>
                    <select class="form-control" wire:model="kitchen_source" required>
                        <option value="شيشة">شيشة</option>
                        <option value="باريستا">باريستا</option>
                        <option value="مطعم">مطعم</option>
                        <option value="استقبال">استقبال</option>
                        <option value="تحايا">تحايا</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">اسم الصنف المطلوب <span style="color: var(--danger-color);">*</span></label>
                    <input type="text" class="form-control" wire:model="item_name" required placeholder="اسم الصنف">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">الأهمية <span style="color: var(--danger-color);">*</span></label>
                    <select class="form-control" wire:model="importance" required>
                        <option value="عادي">عادي</option>
                        <option value="متوسط">متوسط</option>
                        <option value="عاجل">عاجل</option>
                    </select>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="grid grid-cols-4" style="gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">الكمية (العدد) <span style="color: var(--danger-color);">*</span></label>
                    <input type="number" min="1" class="form-control" wire:model="quantity" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">الوحدة (وزن/مل) اختياري</label>
                    <input type="text" class="form-control" wire:model="weight_or_ml" placeholder="مثال: 500 جرام">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">تاريخ الاحتياج <span style="color: var(--danger-color);">*</span></label>
                    <input type="date" class="form-control" wire:model="needed_date" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">صورة / مرفق توضيحي</label>
                    <input type="file" class="form-control" wire:model="attachment" accept="image/*,application/pdf">
                    <div wire:loading wire:target="attachment" style="font-size: 0.8rem; color: var(--warning-color);">جاري الرفع...</div>
                    @if($existing_attachment)
                        <div style="font-size: 0.8rem; margin-top: 5px;">
                            <a href="{{ route('file.serve', ['path' => $existing_attachment]) }}" target="_blank" style="color: var(--primary-color);"><i class="fa-solid fa-paperclip"></i> عرض المرفق الحالي</a>
                        </div>
                    @endif
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                @if($isEditing)
                    <button type="button" class="btn btn-outline" wire:click="resetForm">إلغاء التعديل</button>
                @endif
                <button type="submit" class="btn btn-success" style="padding: 10px 30px;">
                    <i class="fa-solid fa-save"></i>
                    {{ $isEditing ? 'حفظ التعديلات' : 'إضافة الطلب' }}
                </button>
            </div>
        </form>
    </div>
    @endif
    @endif

    <!-- Filters -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="grid grid-cols-{{ $isAdmin ? '4' : '5' }}">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label"><i class="fa-solid fa-magnifying-glass"></i> بحث باسم الصنف</label>
                <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="ابحث هنا...">
            </div>

            {{-- Employee Only: Status Filter --}}
            @if(!$isAdmin)
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label"><i class="fa-solid fa-filter"></i> الحالة</label>
                <select class="form-control" wire:model.live="filterStatus">
                    <option value="">الكل</option>
                    <option value="معلق">معلق</option>
                    <option value="تم الشراء">تم الشراء</option>
                </select>
            </div>
            @endif

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label"><i class="fa-solid fa-shop"></i> المطبخ</label>
                <select class="form-control" wire:model.live="filterKitchen">
                    <option value="">الكل</option>
                    <option value="شيشة">شيشة</option>
                    <option value="باريستا">باريستا</option>
                    <option value="مطعم">مطعم</option>
                    <option value="استقبال">استقبال</option>
                    <option value="تحايا">تحايا</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label"><i class="fa-regular fa-calendar"></i> من تاريخ</label>
                <input type="date" class="form-control" wire:model.live="filterDateFrom">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label"><i class="fa-regular fa-calendar"></i> إلى تاريخ</label>
                <input type="date" class="form-control" wire:model.live="filterDateTo">
            </div>
        </div>
        <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
            <a href="{{ route('admin.daily_purchases.print', ['from' => $filterDateFrom, 'to' => $filterDateTo, 'search' => $search, 'status' => $filterStatus, 'kitchen' => $filterKitchen]) }}" target="_blank" class="btn" style="background: linear-gradient(135deg, #1976D2, #1565C0); color: white; text-decoration: none;">
                <i class="fa-solid fa-print"></i> طباعة التقرير
            </a>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead style="background-color: rgba(0,0,0,0.2);">
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المطبخ</th>
                        <th>اسم الصنف</th>
                        <th>العدد / الكمية</th>
                        <th>الأهمية</th>
                        <th>تاريخ الاحتياج</th>
                        <th>تاريخ الإضافة</th>
                        <th>فاتورة الشراء</th>
                        <th>الحالة</th>
                        <th class="no-print">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>#{{ $request->id }}</td>
                            <td>
                                @if($request->kitchen_source == 'شيشة')
                                    <span class="badge" style="background: rgba(255, 152, 0, 0.15); color: #FF9800; border: 1px solid rgba(255, 152, 0, 0.3);"><i class="fa-solid fa-fire"></i> شيشة</span>
                                @elseif($request->kitchen_source == 'باريستا')
                                    <span class="badge" style="background: rgba(33, 150, 243, 0.15); color: #2196F3; border: 1px solid rgba(33, 150, 243, 0.3);"><i class="fa-solid fa-mug-hot"></i> باريستا</span>
                                @elseif($request->kitchen_source == 'مطعم')
                                    <span class="badge" style="background: rgba(76, 175, 80, 0.15); color: #4CAF50; border: 1px solid rgba(76, 175, 80, 0.3);"><i class="fa-solid fa-utensils"></i> مطعم</span>
                                @elseif($request->kitchen_source == 'استقبال')
                                    <span class="badge" style="background: rgba(156, 39, 176, 0.15); color: #9C27B0; border: 1px solid rgba(156, 39, 176, 0.3);"><i class="fa-solid fa-ticket"></i> استقبال</span>
                                @elseif($request->kitchen_source == 'تحايا')
                                    <span class="badge" style="background: rgba(233, 30, 99, 0.15); color: #E91E63; border: 1px solid rgba(233, 30, 99, 0.3);"><i class="fa-solid fa-gift"></i> تحايا</span>
                                @else
                                    <span class="badge" style="background: rgba(128, 128, 128, 0.15); color: #888; border: 1px solid rgba(128, 128, 128, 0.3);"><i class="fa-solid fa-box"></i> {{ $request->kitchen_source }}</span>
                                @endif
                            </td>
                            <td style="font-weight: bold;">{{ $request->item_name }}</td>
                            <td>
                                {{ $request->quantity }} 
                                @if($request->weight_or_ml)
                                    <span style="color: var(--text-secondary); font-size: 0.8rem;">({{ $request->weight_or_ml }})</span>
                                @endif
                            </td>
                            <td>
                                @if($request->importance == 'عاجل')
                                    <span style="color: var(--danger-color); font-weight: bold;"><i class="fa-solid fa-triangle-exclamation"></i> عاجل</span>
                                @elseif($request->importance == 'متوسط')
                                    <span style="color: var(--warning-color); font-weight: bold;">متوسط</span>
                                @else
                                    <span style="color: var(--text-secondary);">عادي</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($request->needed_date)->format('Y-m-d') }}</td>
                            <td>
                                <div style="font-size: 0.8rem;">{{ $request->created_at->format('Y-m-d') }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $request->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                @if($request->invoice_image)
                                    <a href="{{ route('file.serve', ['path' => $request->invoice_image]) }}" target="_blank" class="btn btn-sm" style="background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.3); padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                        <i class="fa-solid fa-file-invoice"></i> عرض الفاتورة
                                    </a>
                                @else
                                    <span style="color: var(--text-secondary); font-size: 0.8rem;">-</span>
                                @endif
                            </td>

                            <td>
                                @if($request->status == 'معلق')
                                    <span class="badge badge-pending"><i class="fa-regular fa-clock"></i> معلق</span>
                                @else
                                    <span class="badge badge-ready"><i class="fa-solid fa-check-double"></i> تم الشراء</span>
                                @endif
                            </td>

                            {{-- Actions Column --}}
                            <td class="no-print">
                                @if($isAdmin)
                                    {{-- Admin: Edit + Delete --}}
                                    <div style="display: flex; gap: 6px;">
                                        <button wire:click="edit({{ $request->id }})" class="btn btn-sm btn-outline" title="تعديل">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button wire:click="delete({{ $request->id }})"
                                                wire:confirm="هل أنت متأكد من حذف هذا الطلب؟"
                                                class="btn btn-sm btn-danger" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                @else
                                    {{-- Employee: Mark as Purchased (opens invoice modal) --}}
                                    @if($request->status == 'معلق')
                                        <button wire:click="openInvoiceModal({{ $request->id }})" class="btn btn-success btn-sm">
                                            <i class="fa-solid fa-camera"></i> تم الشراء
                                        </button>
                                    @else
                                        @if($request->updated_at)
                                            <div style="font-size: 0.7rem; color: var(--success-color);">
                                                تم في: {{ $request->updated_at->format('h:i A') }}
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 2rem;">
                                <i class="fa-solid fa-box-open" style="font-size: 3rem; color: var(--border-color); margin-bottom: 1rem; display: block;"></i>
                                <span style="color: var(--text-secondary);">لا توجد طلبات مشتريات مطابقة للبحث</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 15px;">
            {{ $requests->links() }}
        </div>
    </div>

    {{-- Employee Only: Invoice Upload Modal --}}
    @if(!$isAdmin && $showInvoiceModal)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 1000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div class="card" style="width: 480px; max-width: 90%; position: relative;">
            <button wire:click="closeInvoiceModal" class="btn btn-outline" style="position: absolute; top: 15px; left: 15px; padding: 5px 10px;">
                <i class="fa-solid fa-times"></i> إغلاق
            </button>
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                <i class="fa-solid fa-camera"></i> تأكيد الشراء - إرفاق فاتورة
            </h3>

            <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.9rem;">
                <i class="fa-solid fa-circle-info" style="color: var(--info-color);"></i>
                يجب تصوير أو إرفاق صورة فاتورة الشراء قبل تأكيد عملية الشراء.
            </p>

            <div class="form-group">
                <label class="form-label">صورة الفاتورة <span style="color: var(--danger-color);">* (إجباري)</span></label>
                <input type="file" class="form-control" wire:model="invoiceImage" accept="image/*" capture="environment" style="padding: 12px;">
                <div wire:loading wire:target="invoiceImage" style="font-size: 0.85rem; color: var(--warning-color); margin-top: 8px;">
                    <i class="fa-solid fa-spinner fa-spin"></i> جاري رفع الصورة...
                </div>
                @error('invoiceImage')
                    <span style="color: var(--danger-color); font-size: 0.8rem; display: block; margin-top: 5px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 1.5rem;">
                <button wire:click="closeInvoiceModal" class="btn btn-outline">إلغاء</button>
                <button wire:click="confirmPurchase" class="btn btn-success" style="padding: 10px 25px;">
                    <i class="fa-solid fa-check-double"></i> تأكيد الشراء
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
