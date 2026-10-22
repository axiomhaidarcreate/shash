<div>
    <div class="page-header">
        <h1 style="font-size: clamp(1.2rem, 4vw, 1.6rem);"><i class="fa-solid fa-user-check" style="color: var(--primary-color);"></i> إدارة العملاء المشتركين</h1>
        <p>متابعة اشتراكات العملاء وإرسال التنبيهات وإدارة الباقات.</p>
    </div>

    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div style="background: rgba(244,67,54,0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="grid grid-cols-2" style="gap: 1rem;">
            <div class="form-group">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="بحث بالاسم، الجوال، أو رقم الاشتراك...">
            </div>
            <div class="form-group">
                <select wire:model.live="filterPackage" class="form-control">
                    <option value="">جميع الباقات</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}">{{ $package->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.05); margin: 0 -0.5rem; padding: 0 0.5rem;">
            <table class="data-table" style="min-width: 800px; background-color: #222;">
                <thead style="background-color: #2a2a2a;">
                    <tr>
                        <th>العميل</th>
                        <th>الباقة</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الأيام المتبقية</th>
                        <th>الاستخدام اليومي</th>
                        <th style="width: 250px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        @php
                            $endDate = \Carbon\Carbon::parse($customer->subscription_end_date);
                            $daysLeft = now()->startOfDay()->diffInDays($endDate, false);
                            
                            $daysClass = 'color: var(--success-color);';
                            if ($daysLeft <= 3 && $daysLeft > 0) $daysClass = 'color: var(--warning-color);';
                            if ($daysLeft <= 0) $daysClass = 'color: var(--danger-color); font-weight: bold;';
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight: bold; color: var(--text-primary);">{{ $customer->name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);" dir="ltr">{{ $customer->phone }}</div>
                            </td>
                            <td>
                                <span class="badge badge-active">{{ $customer->subscriptionPackage->name ?? 'غير محدد' }}</span>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 4px;">رقم: {{ $customer->subscription_number }}</div>
                            </td>
                            <td dir="ltr" style="text-align: right;">{{ $endDate->format('Y-m-d') }}</td>
                            <td style="{{ $daysClass }}">
                                @if($daysLeft > 0)
                                    {{ $daysLeft }} يوم
                                @elseif($daysLeft == 0)
                                    ينتهي اليوم
                                @else
                                    منتهي منذ {{ abs($daysLeft) }} يوم
                                @endif
                            </td>
                            <td>
                                @php
                                    $limit = $customer->subscriptionPackage->daily_order_limit ?? 1;
                                    $used = $customer->canUseSubscriptionToday() ? $customer->daily_subscription_usage_count : $limit;
                                @endphp
                                {{ $used }} / {{ $limit }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <button wire:click="openMessageModal({{ $customer->id }})" class="btn btn-sm" style="background: #25D366; color: #fff; padding: 0.4rem 0.6rem;" title="إرسال رسالة">
                                        <i class="fa-brands fa-whatsapp"></i> مراسلة
                                    </button>
                                    <button wire:click="extendSubscription({{ $customer->id }})" class="btn btn-sm" style="background: #2196F3; color: #fff; padding: 0.4rem 0.6rem;" title="تمديد الاشتراك (إضافة 30 يوم)" onclick="return confirm('هل تريد إضافة 30 يوماً إضافية لاشتراك هذا العميل؟')">
                                        <i class="fa-solid fa-calendar-plus"></i> تمديد
                                    </button>
                                    <button wire:click="cancelSubscription({{ $customer->id }})" class="btn btn-sm" style="background: #F44336; color: #fff; padding: 0.4rem 0.6rem;" title="إلغاء الاشتراك" onclick="return confirm('هل أنت متأكد من إلغاء اشتراك العميل نهائياً؟')">
                                        <i class="fa-solid fa-ban"></i> إلغاء
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                <i class="fa-solid fa-users-slash" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                                لا يوجد عملاء مشتركين حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 1rem;">
            {{ $customers->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

    <!-- Message Modal Overlay -->
    @if($selectedCustomerId)
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card" style="width: 90%; max-width: 500px; background: var(--surface-color); border: 1px solid var(--border-color); box-shadow: var(--shadow-md);">
            <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-regular fa-message" style="color: var(--primary-color);"></i> إرسال رسالة للعميل
            </h3>
            
            <div class="form-group">
                <label class="form-label">نص الرسالة</label>
                <textarea wire:model="messageText" class="form-control" rows="4"></textarea>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 1.5rem; justify-content: flex-end;">
                <button wire:click="$set('selectedCustomerId', null)" class="btn btn-sm" style="background: #444; color: #fff;">إلغاء</button>
                <button wire:click="sendMessage" class="btn btn-sm btn-success">
                    <i class="fa-solid fa-paper-plane"></i> إرسال
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
