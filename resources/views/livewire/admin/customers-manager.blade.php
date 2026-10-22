<x-slot name="title">إدارة العملاء والرسائل</x-slot>

<div>
    <!-- Page Header & Action Buttons -->
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="m-0" style="font-size:1.8rem; font-weight:800; color:var(--text-primary);">
                <i class="fa-solid fa-users text-primary me-2"></i> إدارة العملاء والرسائل
            </h1>
            <p class="text-secondary m-0 mt-1">الربط المباشر مع تطبيق العملاء والفلترة المتطورة وإدارة الرسائل الجماعية والمجدولة</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button wire:click="openCustomerModal" class="btn btn-success">
                <i class="fa-solid fa-user-plus me-1"></i> إضافة عميل جديد
            </button>
            <button wire:click="openTemplateModal" class="btn btn-info" style="background:#17a2b8; color:#fff; border:none;">
                <i class="fa-solid fa-file-lines me-1"></i> قوالب الرسائل
            </button>
            <button wire:click="openCustomMessageModal" class="btn btn-secondary" style="background:#6c757d; border:none;">
                <i class="fa-solid fa-envelope-open-text me-1"></i> الرسائل المخصصة
            </button>
            <button wire:click="openBroadcastModal" class="btn btn-primary" style="background:var(--primary-color); border:none;">
                <i class="fa-solid fa-paper-plane me-1"></i> إرسال رسالة جماعية
            </button>
            <button wire:click="openScheduledModal" class="btn btn-warning" style="background:#ffc107; color:#111; border:none; font-weight:bold;">
                <i class="fa-solid fa-clock me-1"></i> رسائل مجدولة
            </button>
        </div>
    </div>

    <!-- Active Template Banner -->
    @if($active_template_id)
    <div class="alert alert-info d-flex align-items-center mb-4" style="background: rgba(23, 162, 184, 0.1); border: 1px solid #17a2b8; color: #17a2b8; border-radius: 12px;">
        <i class="fa-solid fa-circle-info me-2 fs-5"></i>
        <div>
            <strong>القالب المستخدم حالياً:</strong> {{ $active_template_title }}
        </div>
    </div>
    @endif

    <!-- Multi-Filter Bar -->
    <div class="card mb-4 p-3" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:14px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <div class="filter-grid-row">
            <!-- Search -->
            <div class="filter-item">
                <label class="form-label text-secondary small fw-bold mb-1" style="font-size:0.82rem; white-space:nowrap;">
                    <i class="fa-solid fa-magnifying-glass text-primary me-1"></i> البحث بالاسم / الجوال / الاشتراك
                </label>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control filter-input" placeholder="ابحث هنا...">
            </div>

            <!-- Nationality Filter -->
            <div class="filter-item">
                <label class="form-label text-secondary small fw-bold mb-1" style="font-size:0.82rem; white-space:nowrap;">
                    <i class="fa-solid fa-flag text-warning me-1"></i> التصنيف حسب الجنسية
                </label>
                <select wire:model.live="nationalityFilter" class="form-select form-control filter-input">
                    <option value="" style="background:#1a1a1a;">جميع الجنسيات</option>
                    @foreach($nationalitiesList as $nat)
                        <option value="{{ $nat }}" style="background:#1a1a1a;">{{ $nat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Subscription Filter -->
            <div class="filter-item">
                <label class="form-label text-secondary small fw-bold mb-1" style="font-size:0.82rem; white-space:nowrap;">
                    <i class="fa-solid fa-crown text-success me-1"></i> حالة الاشتراك
                </label>
                <select wire:model.live="subscriptionFilter" class="form-select form-control filter-input">
                    <option value="" style="background:#1a1a1a;">الكل</option>
                    <option value="active" style="background:#1a1a1a;">مشتركين بسارية</option>
                    <option value="inactive" style="background:#1a1a1a;">غير مشتركين / منتهية</option>
                </select>
            </div>

            <!-- Loyalty Filter -->
            <div class="filter-item">
                <label class="form-label text-secondary small fw-bold mb-1" style="font-size:0.82rem; white-space:nowrap;">
                    <i class="fa-solid fa-star text-info me-1"></i> نقاط الولاء
                </label>
                <select wire:model.live="loyaltyFilter" class="form-select form-control filter-input">
                    <option value="" style="background:#1a1a1a;">الكل</option>
                    <option value="has_points" style="background:#1a1a1a;">لديه نقاط ولاء (> 0)</option>
                    <option value="has_offer" style="background:#1a1a1a;">مشترك بعرض ولاء</option>
                </select>
            </div>

            <!-- Event / Meet & Greet Filter -->
            <div class="filter-item">
                <label class="form-label text-secondary small fw-bold mb-1" style="font-size:0.82rem; white-space:nowrap;">
                    <i class="fa-solid fa-champagne-glasses text-primary me-1"></i> عملاء الحفلة / الميت آند قريت
                </label>
                <select wire:model.live="eventFilter" class="form-select form-control filter-input">
                    <option value="" style="background:#1a1a1a;">جميع الحفلات والفعاليات</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" style="background:#1a1a1a;">{{ $ev->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <style>
        .filter-grid-row {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr 1fr 1.2fr;
            gap: 12px;
            align-items: flex-end;
        }
        .filter-input {
            height: 44px !important;
            background: rgba(0, 0, 0, 0.25) !important;
            border: 1px solid var(--border-color) !important;
            color: #fff !important;
            border-radius: 8px !important;
            font-size: 0.9rem !important;
            transition: all 0.25s ease;
        }
        .filter-input:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.25) !important;
        }
        @media (max-width: 1200px) {
            .filter-grid-row {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 768px) {
            .filter-grid-row {
                grid-template-columns: repeat(1, 1fr);
            }
        }
    </style>

    <!-- Data Table Card -->
    <div class="card mb-4 p-3" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:12px;">
        <div class="table-responsive">
            <table class="data-table w-100 text-right align-middle" style="color:var(--text-primary);">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); font-weight:bold;">
                        <th class="py-3">رقم الاشتراك</th>
                        <th class="py-3">اسم العميل</th>
                        <th class="py-3">رقم الجوال</th>
                        <th class="py-3">الجنسية</th>
                        <th class="py-3">الجنس</th>
                        <th class="py-3">باقة الاشتراك</th>
                        <th class="py-3">نقاط الولاء</th>
                        <th class="py-3">الحفلات المحجوزة</th>
                        <th class="py-3 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td>
                                <span class="badge badge-active" style="font-family: monospace; font-size:0.95rem; background:rgba(212,175,55,0.15); color:var(--primary-color); border:1px solid var(--primary-color);">
                                    {{ $customer->subscription_number }}
                                </span>
                            </td>
                            <td>
                                <a href="#" wire:click.prevent="openViewModal({{ $customer->id }})" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td><span dir="ltr">{{ $customer->phone ?: '---' }}</span></td>
                            <td>
                                @if($customer->nationality)
                                    <span class="badge bg-secondary text-light px-2 py-1"><i class="fa-solid fa-flag me-1"></i> {{ $customer->nationality }}</span>
                                @else
                                    <span class="text-muted">غير حدد</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->gender == 'أنثى')
                                    <span class="badge bg-danger text-light"><i class="fa-solid fa-venus"></i> أنثى</span>
                                @else
                                    <span class="badge bg-primary text-light"><i class="fa-solid fa-mars"></i> ذكر</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->subscriptionPackage)
                                    <span class="badge badge-active fw-bold">{{ $customer->subscriptionPackage->name }}</span>
                                    @if($customer->hasActiveSubscription())
                                        <br><span style="font-size:0.75rem; color: var(--success-color);"><i class="fa-solid fa-check me-1"></i> ساري حتى {{ $customer->subscription_end_date }}</span>
                                    @else
                                        <br><span style="font-size:0.75rem; color: var(--danger-color);"><i class="fa-solid fa-xmark me-1"></i> منتهي</span>
                                    @endif
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td>
                                @php $remaining = $customer->total_points - $customer->used_points; @endphp
                                <span class="badge {{ $remaining > 0 ? 'badge-success' : 'badge-inactive' }}" style="font-size:0.9rem;">
                                    <i class="fa-solid fa-star text-warning me-1"></i> {{ $remaining }} نقطة
                                </span>
                            </td>
                            <td>
                                @if($customer->eventBookings && $customer->eventBookings->count() > 0)
                                    <span class="badge bg-info text-dark font-weight-bold">
                                        <i class="fa-solid fa-ticket me-1"></i> {{ $customer->eventBookings->count() }} حجز
                                    </span>
                                @else
                                    <span class="text-muted">لا يوجد</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button wire:click="openViewModal({{ $customer->id }})" class="btn btn-outline-primary" title="عرض التفاصيل">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button wire:click="editCustomer({{ $customer->id }})" class="btn btn-outline-warning" title="تعديل">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button wire:click="deleteCustomer({{ $customer->id }})" wire:confirm="هل أنت متأكد من حذف هذا العميل؟" class="btn btn-outline-danger" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash display-6 d-block mb-2"></i>
                                لا يوجد عملاء مطابقين للبحث أو الفلترة الحالية.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-end">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- MODAL 1: Create / Edit Customer -->
    @if($isCustomerModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid var(--border-color); width:95%; max-width:650px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="modal-header border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-primary m-0">
                        <i class="fa-solid {{ $customer_id ? 'fa-user-pen' : 'fa-user-plus' }} me-2"></i>
                        {{ $customer_id ? 'تعديل بيانات العميل' : 'تسجيل عميل جديد' }}
                    </h5>
                    <button type="button" class="btn-close btn-ghost text-light" wire:click="closeCustomerModal" style="font-size:1.5rem; line-height:1;">&times;</button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveCustomer">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">رقم الاشتراك</label>
                                <input type="text" wire:model="subscription_number" class="form-control text-center font-monospace fw-bold text-primary" readonly style="background-color: rgba(0,0,0,0.2);">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">اسم العميل بالكامل</label>
                                <input type="text" wire:model="name" class="form-control" required placeholder="أدخل اسم العميل...">
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">رقم الجوال</label>
                                <input type="text" wire:model="phone" class="form-control" dir="ltr" required placeholder="05xxxxxxxx">
                                @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">كلمة المرور {{ $customer_id ? '(اتركها فارغة للإبقاء عليها)' : '' }}</label>
                                <input type="password" wire:model="password" class="form-control" placeholder="••••••••">
                                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">الجنسية</label>
                                <select wire:model="nationality" class="form-select">
                                    <option value="سعودي">سعودي</option>
                                    <option value="يمني">يمني</option>
                                    <option value="مصري">مصري</option>
                                    <option value="كويتي">كويتي</option>
                                    <option value="إماراتي">إماراتي</option>
                                    <option value="قطري">قطري</option>
                                    <option value="عماني">عماني</option>
                                    <option value="عراقي">عراقي</option>
                                    <option value="سوري">سوري</option>
                                    <option value="أردني">أردني</option>
                                    <option value="لبناني">لبناني</option>
                                    <option value="سوداني">سوداني</option>
                                    <option value="مغربي">مغربي</option>
                                    <option value="أخرى">أخرى</option>
                                </select>
                                @error('nationality') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">الجنس</label>
                                <select wire:model="gender" class="form-select">
                                    <option value="ذكر">ذكر</option>
                                    <option value="أنثى">أنثى</option>
                                </select>
                                @error('gender') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">عرض نقاط الولاء</label>
                                <select wire:model="loyalty_offer_id" class="form-select">
                                    <option value="">-- بدون عرض --</option>
                                    @foreach($offers as $offer)
                                        <option value="{{ $offer->id }}">{{ $offer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">باقة الاشتراك</label>
                                <select wire:model="subscription_package_id" class="form-select">
                                    <option value="">-- بدون اشتراك --</option>
                                    @foreach($packages as $pkg)
                                        <option value="{{ $pkg->id }}">{{ $pkg->name }} ({{ $pkg->duration_days }} يوم)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($subscription_package_id)
                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ بدء الاشتراك</label>
                                <input type="date" wire:model="subscription_start_date" class="form-control">
                                <small class="text-muted">اتركه فارغاً ليبدأ تلقائياً من اليوم.</small>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" wire:click="closeCustomerModal" class="btn btn-secondary">إلغاء</button>
                            <button type="submit" class="btn btn-success px-4">حفظ بيانات العميل</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: View Customer Details Drawer -->
    @if($isViewModalOpen && $viewCustomer)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid var(--border-color); width:95%; max-width:700px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="modal-header border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-primary m-0">
                            <i class="fa-solid fa-address-card me-2"></i> ملف العميل الكامل: {{ $viewCustomer->name }}
                        </h5>
                        <span class="text-muted small">رقم الاشتراك: {{ $viewCustomer->subscription_number }}</span>
                    </div>
                    <button type="button" class="btn-close btn-ghost text-light" wire:click="closeViewModal" style="font-size:1.5rem; line-height:1;">&times;</button>
                </div>

                <div class="modal-body">
                    <!-- General Details Grid -->
                    <div class="row g-3 mb-4 text-center">
                        <div class="col-4">
                            <div class="p-3 border rounded bg-dark">
                                <div class="small text-muted mb-1">الجنسية</div>
                                <div class="fw-bold text-light"><i class="fa-solid fa-flag text-warning me-1"></i> {{ $viewCustomer->nationality ?: 'غير محدودة' }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 border rounded bg-dark">
                                <div class="small text-muted mb-1">رقم الجوال</div>
                                <div class="fw-bold text-light" dir="ltr">{{ $viewCustomer->phone }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 border rounded bg-dark">
                                <div class="small text-muted mb-1">الجنس</div>
                                <div class="fw-bold text-light">{{ $viewCustomer->gender }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Loyalty & Subscription Box -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100" style="background:rgba(212,175,55,0.05); border-color:var(--primary-color)!important;">
                                <h6 class="fw-bold text-warning border-bottom pb-2 mb-2"><i class="fa-solid fa-star me-1"></i> نقاط الولاء</h6>
                                <p class="mb-1"><strong>إجمالي النقاط:</strong> {{ $viewCustomer->total_points }}</p>
                                <p class="mb-1"><strong>النقاط المستهلكة:</strong> {{ $viewCustomer->used_points }}</p>
                                <p class="mb-1"><strong>الرصيد المتبقي:</strong> <span class="badge bg-success font-size-1rem">{{ $viewCustomer->total_points - $viewCustomer->used_points }} نقطة</span></p>
                                <p class="mb-0 text-muted small mt-2">العرض الحالي: {{ $viewCustomer->loyaltyOffer ? $viewCustomer->loyaltyOffer->name : 'بدون عرض' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100" style="background:rgba(23,162,184,0.05); border-color:#17a2b8!important;">
                                <h6 class="fw-bold text-info border-bottom pb-2 mb-2"><i class="fa-solid fa-crown me-1"></i> باقة الاشتراك</h6>
                                @if($viewCustomer->subscriptionPackage)
                                    <p class="mb-1"><strong>اسم الباقة:</strong> {{ $viewCustomer->subscriptionPackage->name }}</p>
                                    <p class="mb-1"><strong>تاريخ البدء:</strong> {{ $viewCustomer->subscription_start_date }}</p>
                                    <p class="mb-1"><strong>تاريخ الانتهاء:</strong> {{ $viewCustomer->subscription_end_date }}</p>
                                    <p class="mb-0">
                                        <strong>الحالة:</strong>
                                        @if($viewCustomer->hasActiveSubscription())
                                            <span class="badge bg-success">نشط وساري</span>
                                        @else
                                            <span class="badge bg-danger">منتهي</span>
                                        @endif
                                    </p>
                                @else
                                    <p class="text-muted mb-0">غير مشترك بأي باقة حالياً.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Event Bookings History -->
                    <div class="mb-3">
                        <h6 class="fw-bold text-light border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-champagne-glasses me-1 text-primary"></i> سجل حجوزات الحفلات والميت آند قريت ({{ $viewCustomer->eventBookings->count() }})
                        </h6>
                        @if($viewCustomer->eventBookings && $viewCustomer->eventBookings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-dark table-striped table-sm text-right align-middle">
                                    <thead>
                                        <tr>
                                            <th>الفعالية / الحفلة</th>
                                            <th>الطاولة</th>
                                            <th>الكرسي</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($viewCustomer->eventBookings as $b)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $b->event ? $b->event->title : 'حفلة' }}</td>
                                                <td>طاولة {{ $b->table_number ?: '---' }}</td>
                                                <td>كرسي {{ $b->chair_number ?: '---' }}</td>
                                                <td><span class="badge bg-success">{{ $b->status ?? 'مؤكد' }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted small">لم يقم العميل بحجز أي حفلات حتى الآن.</p>
                        @endif
                    </div>
                </div>

                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-secondary w-100" wire:click="closeViewModal">إغلاق</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: Message Templates Manager -->
    @if($isTemplateModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid var(--border-color); width:95%; max-width:800px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="modal-header border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-info m-0"><i class="fa-solid fa-file-lines me-2"></i> إدارة قوالب الرسائل</h5>
                    <button type="button" class="btn-close btn-ghost text-light" wire:click="$set('isTemplateModalOpen', false)">&times;</button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveTemplate" class="mb-4 p-3 border rounded bg-dark">
                        <h6 class="fw-bold text-light mb-3">{{ $template_id ? 'تعديل القالب' : 'إضافة قالب جديدة' }}</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">عنوان القالب</label>
                            <input type="text" wire:model="template_title" class="form-control" required placeholder="مثال: الترحيب بالعملاء الجديد">
                            @error('template_title') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">نص القالب</label>
                            <textarea wire:model="template_content" class="form-control" rows="3" required placeholder="أدخل نص الرسالة المقترحة..."></textarea>
                            @error('template_content') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">مرفق القالب (اختياري)</label>
                            <input type="file" wire:model="template_attachment" class="form-control" accept="image/*,.pdf,.doc,.docx">
                            <div wire:loading wire:target="template_attachment" class="text-info small mt-1">جاري رفع الملف...</div>
                            @error('template_attachment') <span class="text-danger small">{{ $message }}</span> @enderror
                            @if($template_id && \App\Models\MessageTemplate::find($template_id)->attachment)
                                <div class="mt-2 small text-success"><i class="fa-solid fa-paperclip"></i> يوجد مرفق حالي محفوظ لهذا القالب.</div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            @if($template_id)
                                <button type="button" wire:click="resetTemplateFields" class="btn btn-secondary btn-sm">إلغاء التعديل</button>
                            @endif
                            <button type="submit" class="btn btn-info btn-sm text-white px-3"><i class="fa-solid fa-save me-1"></i> حفظ القالب</button>
                        </div>
                    </form>

                    <h6 class="fw-bold text-light mb-3">القوالب المحفوظة ({{ $templates->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-sm text-right align-middle">
                            <thead>
                                <tr>
                                    <th>العنوان</th>
                                    <th>محتوى القالب</th>
                                    <th class="text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $tpl)
                                    <tr>
                                        <td class="fw-bold text-info">{{ $tpl->title }}</td>
                                        <td class="small">{{ Str::limit($tpl->content, 60) }}</td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" wire:click="applyTemplate({{ $tpl->id }})" class="btn btn-success" title="استخدام الآن">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                                <button type="button" wire:click="editTemplate({{ $tpl->id }})" class="btn btn-warning" title="تعديل">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                                <button type="button" wire:click="deleteTemplate({{ $tpl->id }})" wire:confirm="حذف هذا القالب؟" class="btn btn-danger" title="حذف">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-3 text-muted">لا توجد قوالب رسائل مضافة حتى الآن.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 4: Targeted Broadcast Message -->
    @if($isBroadcastModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid var(--border-color); width:95%; max-width:650px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="modal-header border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-primary m-0"><i class="fa-solid fa-paper-plane me-2"></i> إرسال رسالة جماعية مستهدفة</h5>
                    <button type="button" class="btn-close btn-ghost text-light" wire:click="$set('isBroadcastModalOpen', false)">&times;</button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="sendBroadcast">
                        <div class="mb-3">
                            <label class="form-label fw-bold">الفئة المستهدفة</label>
                            <select wire:model.live="broadcast_target_type" class="form-select">
                                <option value="all">جميع العملاء</option>
                                <option value="nationality">حسب جنسية محددة</option>
                                <option value="event">حسب حضور حفلة / ميت آند قريت معينة</option>
                                <option value="subscribed">العملاء المشتركين فقط</option>
                                <option value="loyalty">عملاء نقاط الولاء فقط</option>
                            </select>
                        </div>

                        @if($broadcast_target_type === 'nationality')
                            <div class="mb-3">
                                <label class="form-label fw-bold">اختر الجنسية المستهدفة</label>
                                <select wire:model="broadcast_target_value" class="form-select" required>
                                    <option value="">-- اختر الجنسية --</option>
                                    @foreach($nationalitiesList as $nat)
                                        <option value="{{ $nat }}">{{ $nat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($broadcast_target_type === 'event')
                            <div class="mb-3">
                                <label class="form-label fw-bold">اختر الحفلة / الفعالية المستهدفة</label>
                                <select wire:model="broadcast_target_value" class="form-select" required>
                                    <option value="">-- اختر الحفلة --</option>
                                    @foreach($events as $ev)
                                        <option value="{{ $ev->id }}">{{ $ev->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if($market_event_id)
                            <div class="alert alert-info mt-2 mb-3 py-2" style="background: rgba(33,150,243,0.1); border: 1px solid rgba(33,150,243,0.3); color: #2196F3; border-radius: 8px;">
                                <i class="fa-solid fa-bullhorn me-2"></i> <strong>تسويق للحفلة:</strong> {{ $market_event_title }}
                                <br><small><i class="fa-solid fa-link me-1"></i> سيتم ربط هذه الحفلة بالإشعار لكي يتمكن العملاء من فتحها مباشرة من التطبيق.</small>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">اختر قالب الرسالة</label>
                            <select wire:model.live="broadcast_template_id" class="form-select" required>
                                <option value="">-- اختر قالباً --</option>
                                @foreach($templates as $tpl)
                                    <option value="{{ $tpl->id }}">{{ $tpl->title }}</option>
                                @endforeach
                            </select>
                            @error('broadcast_template_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        @if($broadcast_title)
                            <div class="p-3 mb-4 rounded border border-info" style="background: rgba(23,162,184,0.05);">
                                <h6 class="fw-bold text-info border-bottom border-info pb-2 mb-2">معاينة القالب: {{ $broadcast_title }}</h6>
                                <p class="mb-0 small text-light">{{ $broadcast_content }}</p>
                                @if($broadcast_attachment)
                                    <div class="mt-2 small text-success"><i class="fa-solid fa-paperclip"></i> يحتوي على مرفق</div>
                                @endif
                            </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" wire:click="$set('isBroadcastModalOpen', false)" class="btn btn-secondary">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-paper-plane me-1"></i> إرسال الآن</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 5: Scheduled Automated Messaging -->
    @if($isScheduledModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid var(--border-color); width:95%; max-width:750px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="modal-header border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-warning m-0"><i class="fa-solid fa-clock me-2"></i> جدولة رسالة تلقائية حسب الوقت</h5>
                    <button type="button" class="btn-close btn-ghost text-light" wire:click="$set('isScheduledModalOpen', false)">&times;</button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveScheduledMessage" class="p-3 border rounded bg-dark mb-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">الفئة المستهدفة</label>
                                <select wire:model.live="scheduled_target_type" class="form-select">
                                    <option value="all">جميع العملاء</option>
                                    <option value="nationality">حسب الجنسية</option>
                                    <option value="event">حسب حضور حفلة</option>
                                    <option value="subscribed">المشتركين فقط</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">تاريخ ووقت الإرسال التلقائي</label>
                                <input type="datetime-local" wire:model="scheduled_datetime" class="form-control" required>
                                @error('scheduled_datetime') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if($scheduled_target_type === 'nationality')
                            <div class="mb-3">
                                <label class="form-label fw-bold">اختر الجنسية</label>
                                <select wire:model="scheduled_target_value" class="form-select" required>
                                    <option value="">-- اختر الجنسية --</option>
                                    @foreach($nationalitiesList as $nat)
                                        <option value="{{ $nat }}">{{ $nat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($scheduled_target_type === 'event')
                            <div class="mb-3">
                                <label class="form-label fw-bold">اختر الحفلة (حفلات اليوم)</label>
                                <select wire:model="scheduled_target_value" class="form-select" required>
                                    <option value="">-- اختر الحفلة --</option>
                                    @foreach($todayEvents as $ev)
                                        <option value="{{ $ev->id }}">{{ $ev->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">عنوان الرسالة</label>
                            <input type="text" wire:model="scheduled_title" class="form-control" required placeholder="عنوان الإشعار...">
                            @error('scheduled_title') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">محتوى الرسالة</label>
                            <textarea wire:model="scheduled_content" class="form-control" rows="3" required placeholder="نص الرسالة..."></textarea>
                            @error('scheduled_content') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning px-4 font-weight-bold"><i class="fa-solid fa-clock me-1"></i> جدولة الرسالة التلقائية</button>
                        </div>
                    </form>

                    <h6 class="fw-bold text-light mb-3">الرسائل المجدولة والتلقائية الحالية</h6>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-sm text-right align-middle">
                            <thead>
                                <tr>
                                    <th>العنوان</th>
                                    <th>الفئة</th>
                                    <th>وقت الإرسال المجدول</th>
                                    <th>الحالة</th>
                                    <th class="text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scheduledMessages as $sm)
                                    <tr>
                                        <td class="fw-bold text-warning">{{ $sm->title }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $sm->target_type }} {{ $sm->target_value ? '('.$sm->target_value.')' : '' }}
                                            </span>
                                        </td>
                                        <td>{{ $sm->scheduled_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($sm->status === 'sent')
                                                <span class="badge bg-success">تم الإرسال</span>
                                            @elseif($sm->status === 'cancelled')
                                                <span class="badge bg-danger">ملغي</span>
                                            @else
                                                <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($sm->status === 'pending')
                                                <button type="button" wire:click="cancelScheduledMessage({{ $sm->id }})" class="btn btn-danger btn-sm" title="إلغاء">
                                                    <i class="fa-solid fa-ban me-1"></i> إلغاء
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-3 text-muted">لا توجد رسائل مجدولة.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 6: Custom Messages (الرسائل المخصصة) -->
    @if($isCustomMessageModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid var(--border-color); width:95%; max-width:700px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="modal-header border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-secondary m-0"><i class="fa-solid fa-envelope-open-text me-2"></i> إرسال رسائل مخصصة</h5>
                    <button type="button" class="btn-close btn-ghost text-light" wire:click="$set('isCustomMessageModalOpen', false)">&times;</button>
                </div>
                <div class="modal-body">
                    
                    @if(!$active_template_id)
                        <div class="alert alert-warning border-warning text-warning" style="background: rgba(255,193,7,0.1);">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> لا يوجد قالب نشط! يرجى اختيار قالب من شاشة "قوالب الرسائل" وتعيينه كقالب نشط أولاً.
                        </div>
                    @else
                        <div class="p-3 mb-4 rounded border border-info" style="background: rgba(23,162,184,0.05);">
                            <h6 class="fw-bold text-info border-bottom border-info pb-2 mb-2">القالب المستخدم: {{ $active_template_title }}</h6>
                            <p class="mb-0 small text-light">{{ $active_template_content }}</p>
                            @if($active_template_attachment)
                                <div class="mt-2 small text-success"><i class="fa-solid fa-paperclip"></i> يحتوي على مرفق</div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-light">اختر الجنسية (للفلترة)</label>
                            <select wire:model.live="custom_message_nationality" class="form-select border-secondary text-light" style="background: rgba(0,0,0,0.3);">
                                <option value="">-- اختر جنسية معينة لعرض عملائها --</option>
                                @foreach($nationalitiesList as $nat)
                                    <option value="{{ $nat }}">{{ $nat }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($custom_message_nationality && count($available_customers_for_custom_message) > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold text-light m-0">العملاء من الجنسية ({{ count($available_customers_for_custom_message) }})</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model.live="selectAllCustomers" id="selectAll">
                                        <label class="form-check-label text-info" for="selectAll">تحديد الكل</label>
                                    </div>
                                </div>
                                <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto; background: rgba(0,0,0,0.2);">
                                    @foreach($available_customers_for_custom_message as $customer)
                                        <div class="form-check mb-2 pb-2 border-bottom border-secondary">
                                            <input class="form-check-input" type="checkbox" wire:model="custom_message_customers" value="{{ $customer['id'] }}" id="cust_{{ $customer['id'] }}">
                                            <label class="form-check-label d-flex align-items-center" for="cust_{{ $customer['id'] }}">
                                                <span class="fw-bold me-2">{{ $customer['name'] }}</span>
                                                <span class="badge bg-secondary ms-auto" dir="ltr">{{ $customer['phone'] }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('custom_message_customers') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-secondary me-2" wire:click="$set('isCustomMessageModalOpen', false)">إلغاء</button>
                                <button type="button" class="btn btn-success px-4" wire:click="sendCustomMessage">
                                    <i class="fa-solid fa-paper-plane me-1"></i> إرسال إلى ({{ count($custom_message_customers) }}) عميل
                                </button>
                            </div>
                        @elseif($custom_message_nationality)
                            <div class="alert alert-secondary text-center">لا يوجد عملاء بهذه الجنسية.</div>
                        @endif

                    @endif
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                if (typeof showToast === 'function') {
                    showToast(event[0].message, event[0].type);
                } else {
                    alert(event[0].message);
                }
            });
        });
    </script>
</div>
