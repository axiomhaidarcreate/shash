<x-slot name="title">عملاء الولاء</x-slot>

<div>
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>عملاء الولاء</h1>
            <p>إدارة المشتركين في برامج الولاء</p>
        </div>
        <button wire:click="openModal" class="btn btn-success">
            <i class="fa-solid fa-user-plus"></i> إضافة عميل جديد
        </button>
    </div>

    <div class="card mb-4">
        <div class="mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="بحث بالاسم، رقم الجوال أو رقم الاشتراك..." style="max-width: 400px;">
        </div>
        <div class="table-responsive">
            <table class="data-table w-100 text-right">
                <thead>
                    <tr>
                        <th>رقم الاشتراك</th>
                        <th>اسم العميل</th>
                        <th>رقم الجوال</th>
                        <th>العرض المشترك به</th>
                        <th>الباقة</th>
                        <th>النقاط المتوفرة (المتبقية)</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td><span class="badge badge-active" style="font-family: monospace; font-size:1rem;">{{ $customer->subscription_number }}</span></td>
                            <td>
                                <a href="#" wire:click.prevent="openViewModal({{ $customer->id }})" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td><span dir="ltr">{{ $customer->phone ?: '---' }}</span></td>
                            <td>{{ $customer->loyaltyOffer ? $customer->loyaltyOffer->name : 'بدون عرض' }}</td>
                            <td>
                                @if($customer->subscriptionPackage)
                                    <span class="badge badge-active">{{ $customer->subscriptionPackage->name }}</span>
                                    @if($customer->hasActiveSubscription())
                                        <br><span style="font-size:0.75rem; color: var(--success-color);">ساري حتى {{ $customer->subscription_end_date }}</span>
                                    @else
                                        <br><span style="font-size:0.75rem; color: var(--danger-color);">منتهي</span>
                                    @endif
                                @else
                                    <span style="color: var(--text-secondary);">---</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $remaining = $customer->total_points - $customer->used_points;
                                @endphp
                                <span class="badge {{ $remaining > 0 ? 'badge-success' : 'badge-inactive' }}">{{ $remaining }} نقطة</span>
                            </td>
                            <td>
                                <button wire:click="edit({{ $customer->id }})" class="btn btn-sm btn-outline-info" style="border:1px solid #17a2b8; color:#17a2b8; background:transparent;">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:click="delete({{ $customer->id }})" wire:confirm="هل أنت متأكد من حذف هذا العميل؟" class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">لا يوجد عملاء مطابقين للبحث.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    @if($isModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1050; display:flex; justify-content:center; align-items:center;">
            <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); width:90%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <div class="modal-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between">
                        <h5 class="modal-title">{{ $customer_id ? 'تعديل بيانات العميل' : 'تسجيل عميل جديد' }}</h5>
                        <button type="button" class="btn-close btn-ghost" wire:click="closeModal" style="font-size:1.5rem; line-height:1;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="store">
                            <div class="form-group mb-3">
                                <label class="form-label">رقم الاشتراك</label>
                                <input type="text" wire:model="subscription_number" class="form-control" readonly style="background-color: rgba(0,0,0,0.1); font-family: monospace; font-size:1.1rem; color:var(--primary-color); text-align:center;">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">اسم العميل</label>
                                <input type="text" wire:model="name" class="form-control" required>
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">رقم الجوال</label>
                                <input type="text" wire:model="phone" class="form-control" dir="ltr">
                                @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">عرض الولاء</label>
                                <select wire:model="loyalty_offer_id" class="form-control">
                                    <option value="">-- بدون عرض --</option>
                                    @foreach($offers as $offer)
                                        @php
                                            $tiersDesc = '';
                                            if ($offer->rewardTiers && $offer->rewardTiers->count() > 0) {
                                                $tiersDesc = ' (' . $offer->rewardTiers->map(fn($t) => $t->required_points . ':' . $t->reward_description)->implode('، ') . ')';
                                            } elseif ($offer->required_points > 0 && $offer->reward_description) {
                                                $tiersDesc = ' (' . $offer->required_points . ':' . $offer->reward_description . ')';
                                            }
                                        @endphp
                                        <option value="{{ $offer->id }}">{{ $offer->name }}{{ $tiersDesc }}</option>
                                    @endforeach
                                </select>
                                @error('loyalty_offer_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div style="border-top: 1px dashed var(--border-color); padding-top: 1rem; margin-top: 0.5rem;">
                                <label class="form-label" style="color: var(--primary-color); font-weight: bold;"><i class="fa-solid fa-crown"></i> باقة الاشتراك</label>
                                <select wire:model="subscription_package_id" class="form-control">
                                    <option value="">-- بدون اشتراك --</option>
                                    @foreach($packages as $pkg)
                                        <option value="{{ $pkg->id }}">{{ $pkg->name }} ({{ $pkg->duration_days }} يوم — {{ number_format($pkg->price, 0) }} ريال)</option>
                                    @endforeach
                                </select>
                                @error('subscription_package_id') <span class="text-danger small">{{ $message }}</span> @enderror

                                @if($subscription_package_id)
                                    <div class="form-group mt-3">
                                        <label class="form-label">تاريخ بداية الاشتراك</label>
                                        <input type="date" wire:model="subscription_start_date" class="form-control">
                                        @error('subscription_start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                        <small style="color: var(--text-secondary);">اتركه فارغاً لتبدأ من اليوم.</small>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-secondary">
                                <button type="button" wire:click="closeModal" class="btn btn-ghost">إلغاء</button>
                                <button type="submit" class="btn btn-success">حفظ العميل</button>
                            </div>
                        </form>
                    </div>
        </div>
    @endif

    <!-- View Customer Details Modal -->
    @if($isViewModalOpen && $viewCustomer)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1050; display:flex; justify-content:center; align-items:center;">
            <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); width:90%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <div class="modal-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between">
                        <h5 class="modal-title" style="color:var(--primary-color);">تفاصيل نقاط العميل: {{ $viewCustomer->name }}</h5>
                        <button type="button" class="btn-close btn-ghost" wire:click="closeViewModal" style="font-size:1.5rem; line-height:1;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-3 gap-3 mb-4 text-center">
                            <div style="background:rgba(255,255,255,0.03); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
                                <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:5px;">إجمالي النقاط المكتسبة</div>
                                <div style="font-size:1.5rem; font-weight:bold; color:var(--info-color);">{{ $viewCustomer->total_points }}</div>
                            </div>
                            <div style="background:rgba(255,255,255,0.03); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
                                <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:5px;">النقاط المستخدمة</div>
                                <div style="font-size:1.5rem; font-weight:bold; color:var(--warning-color);">{{ $viewCustomer->used_points }}</div>
                            </div>
                            <div style="background:rgba(255,255,255,0.03); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--primary-color); box-shadow: 0 0 10px rgba(212, 175, 55, 0.2);">
                                <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:5px;">النقاط المتبقية</div>
                                <div style="font-size:1.5rem; font-weight:bold; color:var(--primary-color);">{{ $viewCustomer->total_points - $viewCustomer->used_points }}</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 style="color:var(--text-secondary); font-size:0.9rem; border-bottom:1px solid var(--border-color); padding-bottom:5px; margin-bottom:10px;">طريقة الحساب بناءً على العرض</h6>
                            @if($viewCustomer->loyaltyOffer)
                                <ul style="list-style-type:none; padding:0; margin:0; line-height:1.8; font-size:0.9rem;">
                                    <li><i class="fa-solid fa-check text-success ml-2"></i> اسم العرض: <strong>{{ $viewCustomer->loyaltyOffer->name }}</strong></li>
                                    <li><i class="fa-solid fa-check text-success ml-2"></i> كل زيارة بـ: <strong>{{ $viewCustomer->loyaltyOffer->points_per_visit }}</strong> نقطة</li>
                                    <li><i class="fa-solid fa-check text-success ml-2"></i> كل طلب بـ: <strong>{{ $viewCustomer->loyaltyOffer->points_per_order }}</strong> نقطة</li>
                                    <li><i class="fa-solid fa-check text-success ml-2"></i> كل ريال بـ: <strong>{{ $viewCustomer->loyaltyOffer->points_per_riyal }}</strong> نقطة</li>
                                </ul>
                                
                                <div class="mt-3">
                                    <h6 style="color:var(--primary-color); font-size:0.9rem; margin-bottom:10px;"><i class="fa-solid fa-trophy"></i> مستويات المكافآت المتاحة في هذا العرض:</h6>
                                    @if($viewCustomer->loyaltyOffer->rewardTiers && $viewCustomer->loyaltyOffer->rewardTiers->count() > 0)
                                        <div style="display: flex; flex-direction: column; gap: 8px;">
                                            @foreach($viewCustomer->loyaltyOffer->rewardTiers as $tier)
                                                <div style="background:var(--primary-glow); border-radius:var(--radius-sm); padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(212,175,55,0.2);">
                                                    <span style="font-weight:bold; color:var(--text-primary);">{{ $tier->reward_description }}</span>
                                                    <span class="badge badge-prep">{{ $tier->required_points }} نقطة</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($viewCustomer->loyaltyOffer->required_points > 0 && $viewCustomer->loyaltyOffer->reward_description)
                                        <div class="p-2 text-center" style="background:var(--primary-glow); border-radius:var(--radius-sm); color:var(--primary-color); font-weight:bold;">
                                            عند تجميع ({{ $viewCustomer->loyaltyOffer->required_points }}) نقطة يتم الحصول على ({{ $viewCustomer->loyaltyOffer->reward_description }})
                                        </div>
                                    @else
                                        <div class="text-secondary small">لا توجد مستويات مكافآت مضافة لهذا العرض.</div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center text-secondary py-3">هذا العميل غير مشترك بأي عرض ولاء حالياً.</div>
                            @endif
                        </div>

                        {{-- Subscription Info --}}
                        <div class="mb-4">
                            <h6 style="color:var(--text-secondary); font-size:0.9rem; border-bottom:1px solid var(--border-color); padding-bottom:5px; margin-bottom:10px;"><i class="fa-solid fa-crown"></i> معلومات الاشتراك</h6>
                            @if($viewCustomer->subscriptionPackage)
                                <ul style="list-style-type:none; padding:0; margin:0; line-height:1.8; font-size:0.9rem;">
                                    <li><i class="fa-solid fa-tag text-success ml-2"></i> الباقة: <strong>{{ $viewCustomer->subscriptionPackage->name }}</strong></li>
                                    <li><i class="fa-solid fa-calendar-check text-success ml-2"></i> بداية الاشتراك: <strong>{{ $viewCustomer->subscription_start_date }}</strong></li>
                                    <li><i class="fa-solid fa-calendar-xmark ml-2" style="color: var(--warning-color);"></i> نهاية الاشتراك: <strong>{{ $viewCustomer->subscription_end_date }}</strong></li>
                                    <li><i class="fa-solid fa-fire ml-2" style="color: var(--info-color);"></i> الحد اليومي: <strong>{{ $viewCustomer->subscriptionPackage->daily_order_limit }} طلب</strong></li>
                                    <li>
                                        @if($viewCustomer->hasActiveSubscription())
                                            <span style="color: var(--success-color); font-weight: bold;"><i class="fa-solid fa-check-circle"></i> الاشتراك ساري المفعول</span>
                                        @else
                                            <span style="color: var(--danger-color); font-weight: bold;"><i class="fa-solid fa-times-circle"></i> الاشتراك منتهي</span>
                                        @endif
                                    </li>
                                </ul>
                            @else
                                <div class="text-center text-secondary py-3">هذا العميل غير مشترك بأي باقة حالياً.</div>
                            @endif
                        </div>
                    </div>
        </div>
    @endif
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                showToast(event[0].message, event[0].type);
            });
        });
    </script>
</div>
