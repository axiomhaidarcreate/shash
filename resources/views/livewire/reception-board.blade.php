<div wire:poll.10s>
    <livewire:shift-status-banner />
    <livewire:admin-ticket-report-viewer />
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem; position: relative;">
        <!-- Secret Button -->
        <button onclick="handleSecretClick()" style="position: absolute; top: 0; right: 0; width: 60px; height: 60px; background: transparent; border: 1px dashed rgba(128, 128, 128, 0.3); border-radius: 8px; outline: none; z-index: 999; cursor: default;" title="إظهار أزرار الطباعة (3 ضغطات)"></button>
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);">
            <i class="fa-solid fa-ticket" style="color: var(--primary-color);"></i> موظف الاستقبال - إصدار التذاكر
        </h2>
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <div class="secret-print-target" style="display: flex; gap: 10px; align-items: stretch; flex-wrap: wrap; margin-left: 10px;">
                
                {{-- Date & Time Filters --}}
                <div style="display: flex; align-items: center; gap: 5px; background: rgba(0, 0, 0, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(0, 0, 0, 0.1);">
                    <input type="date" wire:model.live="filterStartDate" class="form-control" style="width: 120px; padding: 0.2rem 0.4rem; font-size: 0.8rem;">
                    <input type="time" wire:model.live="filterStartTime" class="form-control" style="width: 100px; padding: 0.2rem 0.4rem; font-size: 0.8rem;">
                    <span style="font-size: 0.8rem; font-weight: bold; color: #555;">إلى</span>
                    <input type="date" wire:model.live="filterEndDate" class="form-control" style="width: 120px; padding: 0.2rem 0.4rem; font-size: 0.8rem;">
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <input type="time" wire:model.live="filterEndTime" class="form-control" style="width: 100px; padding: 0.2rem 0.4rem; font-size: 0.8rem;">
                        @if($filterStartTime || $filterEndTime)
                            <button wire:click="$set('filterStartTime', ''); $set('filterEndTime', '')" style="background: none; border: none; color: var(--danger-color); cursor: pointer; padding: 2px;" title="مسح الساعات">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Manager approvals print --}}
                <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                    <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">موافقات المدير:</span>
                    <a href="/invoice/manager-approved-print-thermal?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                        <i class="fa-solid fa-receipt"></i> حراري
                    </a>
                    <a href="/invoice/manager-approved-print?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                        <i class="fa-solid fa-file-lines"></i> A4
                    </a>
                </div>

                {{-- Waste print --}}
                <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                    <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">تالف معتمد:</span>
                    <a href="/invoice/waste-print-thermal?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                        <i class="fa-solid fa-receipt"></i> حراري
                    </a>
                    <a href="/invoice/waste-print?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                        <i class="fa-solid fa-file-lines"></i> A4
                    </a>
                </div>

                {{-- Inventory print --}}
                <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                    <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">المبيعات/الجرد:</span>
                    <a href="/invoice/inventory-print-thermal?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                        <i class="fa-solid fa-receipt"></i> حراري
                    </a>
                    <a href="/invoice/inventory-print?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                        <i class="fa-solid fa-file-lines"></i> A4
                    </a>
                </div>
                {{-- Print Report Buttons --}}
                <div style="display: flex; align-items: center; background: rgba(156, 39, 176, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(156, 39, 176, 0.3);">
                    <span style="font-size: 0.85rem; color: var(--primary-color); padding: 0 10px; font-weight: bold; flex: 1;">تقرير التذاكر:</span>
                    <a href="/invoice/ticket-report-print-thermal?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: var(--primary-color); color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 4px; margin-left: 4px;">
                        <i class="fa-solid fa-receipt"></i> حراري
                    </a>
                    <a href="/invoice/ticket-report-print?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: #D4AF37; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 4px;">
                        <i class="fa-solid fa-file-lines"></i> A4
                    </a>
                </div>
            </div>
            <a href="{{ route('reception.loyalty') }}" class="btn" style="background: linear-gradient(135deg, #9C27B0, #7B1FA2); color: #fff; padding: 0.6rem 1.2rem; border: none; border-radius: var(--radius-sm); font-weight: bold; font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-star"></i> الولاء
            </a>
            <button wire:click="openSubscriptionModal" class="btn" style="background: linear-gradient(135deg, #9C27B0, #7B1FA2); color: #fff; padding: 0.6rem 1.2rem; border: none; border-radius: var(--radius-sm); font-weight: bold; font-size: 0.9rem; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-crown"></i> تفعيل اشتراك
            </button>
            <a href="{{ route('reception.greetings') }}" class="btn" style="background: linear-gradient(135deg, #9C27B0, #7B1FA2); color: #fff; padding: 0.6rem 1.2rem; border: none; border-radius: var(--radius-sm); font-weight: bold; font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-gift"></i> التحايا
            </a>
            <a href="{{ route('reception.customer-requests') }}" class="btn" style="background: linear-gradient(135deg, #9C27B0, #7B1FA2); color: #fff; padding: 0.6rem 1.2rem; border: none; border-radius: var(--radius-sm); font-weight: bold; font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-bell"></i> الحجوزات
            </a>
            <span style="color: var(--success-color); font-size: 0.8rem;"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> مباشر</span>
        </div>
    </div>

    @if(session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div style="background: rgba(244,67,54,0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Manager Approval Requests --}}
    @if(isset($approvalRequests) && $approvalRequests->count() > 0)
    <div class="card" style="margin-bottom: 1.5rem; border: 2px solid var(--info-color); background: rgba(33, 150, 243, 0.03); box-shadow: 0 0 15px rgba(33, 150, 243, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
            <h3 style="color: var(--info-color); display: flex; align-items: center; gap: 8px; margin: 0;">
                <i class="fa-solid fa-user-tie fa-bounce"></i>
                طلبات ضيافة/خاصة بانتظار موافقة المدير
                <span style="background: var(--info-color); color: #fff; border-radius: 50%; padding: 2px 8px; font-size: 0.9rem;">{{ $approvalRequests->count() }}</span>
            </h3>
        </div>
        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="data-table" style="min-width: 600px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الضيف</th>
                        <th>الطاولة</th>
                        <th>الطلب</th>
                        <th>بواسطة</th>
                        <th style="width: 220px;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvalRequests as $req)
                    <tr wire:key="approval-req-{{ $req->id }}" style="background: rgba(33, 150, 243, 0.05);">
                        <td style="font-weight: bold; color: var(--info-color);">#{{ $req->id }}</td>
                        <td style="font-weight: bold; font-size: 1.05rem; color: #fff;">
                            <i class="fa-solid fa-user-tag" style="color: var(--primary-color);"></i> {{ $req->guest_name ?: 'غير محدد' }}
                        </td>
                        <td>طاولة {{ $req->table->number ?? '-' }} / كرسي {{ $req->chair->number ?? '-' }} @if($req->table && $req->table->section)<span style="font-size: 0.8rem; color: #777;">({{ $req->table->section }})</span>@endif</td>
                        <td>
                            {{ $req->quantity }}x {{ $req->shisha_type }}
                            @if($req->notes)
                                <div style="font-size: 0.85rem; color: var(--warning-color); margin-top: 5px;">
                                    <i class="fa-solid fa-note-sticky"></i> ملاحظة: {{ $req->notes }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $req->user->name ?? '-' }}</td>
                        <td>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <button wire:click="openPinModal({{ $req->id }})" class="btn btn-sm" style="background: #4CAF50; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                    <i class="fa-solid fa-check"></i> موافقة
                                </button>
                                <button wire:click="rejectSpecialOrder({{ $req->id }})" class="btn btn-sm" style="background: #F44336; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من الرفض؟ سيتم إلغاء الطلب.')">
                                    <i class="fa-solid fa-xmark"></i> رفض
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Audio Notification Script -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('play-approval-sound', () => {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    oscillator.type = 'triangle';
                    oscillator.frequency.setValueAtTime(800, audioCtx.currentTime);
                    oscillator.frequency.setValueAtTime(1200, audioCtx.currentTime + 0.15);
                    gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
                    gainNode.gain.linearRampToValueAtTime(0.5, audioCtx.currentTime + 0.05);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
                    oscillator.start(audioCtx.currentTime);
                    oscillator.stop(audioCtx.currentTime + 0.5);
                } catch(e) {
                    console.log('Audio notification blocked by browser.');
                }
            });
        });
    </script>

    <div class="layout-grid">

        {{-- Issue Ticket Card --}}
        <div class="card" style="border-top: 3px solid var(--primary-color);">
            <h3 style="margin-bottom: 1.2rem; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-plus-circle" style="color: var(--primary-color);"></i> إصدار تذكرة جديدة
            </h3>


            {{-- Template Buttons --}}
            @if($templates->count() > 0)
                <label class="form-label" style="margin-bottom: 0.8rem;">
                    <i class="fa-solid fa-hand-pointer" style="color: var(--primary-color);"></i> اختر نوع التذكرة:
                </label>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 1.5rem;">
                    @foreach($templates as $template)
                        <button wire:click="selectTemplate({{ $template->id }})" class="btn"
                                style="flex: 1; min-width: 100px; padding: 1.2rem 1rem; font-size: 1.8rem; font-weight: 900;
                                       background: {{ $selectedTemplateId == $template->id ? 'var(--primary-color)' : 'rgba(212,175,55,0.08)' }};
                                       color: {{ $selectedTemplateId == $template->id ? '#fff' : 'var(--primary-color)' }};
                                       border: 2px solid {{ $selectedTemplateId == $template->id ? 'var(--primary-color)' : 'rgba(212,175,55,0.3)' }};
                                       border-radius: 12px; transition: all 0.2s;">
                            {{ $template->name }}
                            <div style="font-size: 0.75rem; font-weight: 600; margin-top: 4px; opacity: 0.85;">
                                {{ $template->max_items == 1 ? 'طلب واحد فقط' : $template->max_items . ' طلبات' }}
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 2rem; color: #777; background: rgba(212,175,55,0.04); border-radius: 8px; border: 1px dashed rgba(212,175,55,0.2); margin-bottom: 1rem;">
                    <i class="fa-solid fa-info-circle" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block; color: var(--primary-color);"></i>
                    لا توجد قوالب تذاكر بعد. يرجى التواصل مع المدير لإنشاء قوالب.
                </div>
            @endif

            {{-- Product Selection --}}
            @if($selectedTemplateId)
                @php
                    $activeTemplate = $templates->firstWhere('id', $selectedTemplateId);
                @endphp
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="margin-bottom: 0.6rem;">
                        <i class="fa-solid fa-box-open" style="color: var(--primary-color);"></i>
                        اختر المنتجات
                        <span style="font-weight: 400; color: var(--text-secondary);">
                            ({{ count($selectedProducts) }} / {{ $activeTemplate->max_items }})
                        </span>
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 8px; max-height: 250px; overflow-y: auto; padding: 4px;">
                        @if($activeTemplate->products && count($activeTemplate->products) > 0)
                            @foreach($activeTemplate->products as $productName)
                                @php
                                    $isSelected = in_array($productName, $selectedProducts);
                                @endphp
                                <div wire:click="toggleProduct('{{ addslashes($productName) }}')"
                                     style="padding: 10px 8px; text-align: center; border-radius: 10px; cursor: pointer; transition: all 0.2s; font-size: 0.9rem;
                                            background: {{ $isSelected ? 'rgba(212,175,55,0.12)' : 'rgba(255,255,255,0.05)' }};
                                            border: 2px solid {{ $isSelected ? 'var(--primary-color)' : 'rgba(255,255,255,0.1)' }};
                                            color: {{ $isSelected ? 'var(--primary-color)' : 'var(--text-primary)' }};
                                            font-weight: {{ $isSelected ? 'bold' : 'normal' }};">
                                    @if($isSelected)
                                        <i class="fa-solid fa-circle-check" style="color: var(--primary-color); margin-left: 4px;"></i>
                                    @endif
                                    {{ $productName }}
                                </div>
                            @endforeach
                        @else
                            <div style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary); padding: 10px;">
                                لا توجد منتجات مخصصة لهذا القالب
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Loyalty Customer Link (optional) --}}
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(255,152,0,0.05); border: 1px solid rgba(255,152,0,0.2); border-radius: var(--radius-sm);">
                    <label class="form-label" style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-star" style="color: #FF9800;"></i>
                        ربط بعميل ولاء (اختياري)
                    </label>
                    @if($linkedCustomer)
                        <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); border-radius: var(--radius-sm); padding: 0.6rem 1rem;">
                            <div>
                                <span style="color: var(--success-color); font-weight: bold;">
                                    <i class="fa-solid fa-user-check"></i> {{ $linkedCustomer->name }}
                                </span>
                                <span style="color: var(--text-secondary); font-size: 0.85rem; margin-right: 8px;">
                                    | رقم: {{ $linkedCustomer->subscription_number }}
                                    | النقاط: {{ $linkedCustomer->total_points - $linkedCustomer->used_points }}
                                    @if($linkedCustomer->loyaltyOffer)
                                        | +{{ $linkedCustomer->loyaltyOffer->points_per_visit + $linkedCustomer->loyaltyOffer->points_per_order }} نقطة عند الإصدار
                                    @endif
                                </span>
                            </div>
                            <button wire:click="clearLoyaltyCustomer" class="btn btn-sm" style="background: rgba(244,67,54,0.2); color: var(--danger-color); border: none; padding: 0.3rem 0.6rem;">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        @if($linkedCustomer->subscriptionPackage && $linkedCustomer->hasActiveSubscription())
                            <div style="margin-top: 0.5rem; background: rgba(0,188,212,0.1); border: 1px solid #00BCD4; padding: 0.5rem 0.8rem; border-radius: var(--radius-sm); font-size: 0.85rem;">
                                <i class="fa-solid fa-crown" style="color: #00BCD4;"></i>
                                <strong style="color: #00BCD4;">اشتراك ساري:</strong> {{ $linkedCustomer->subscriptionPackage->name }}
                                — حتى {{ $linkedCustomer->subscription_end_date }}
                                @if($linkedCustomer->canUseSubscriptionToday())
                                    <span style="color: var(--success-color);">(يمكن استخدامه اليوم)</span>
                                @else
                                    <span style="color: var(--danger-color);">(تم استيفاء الحد اليومي)</span>
                                @endif
                            </div>
                        @endif
                    @else
                        <div style="position: relative;">
                            <div style="display: flex; gap: 6px; align-items: flex-start; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 200px; position: relative;">
                                    <i class="fa-solid fa-search" style="position: absolute; right: 10px; top: 10px; color: #FF9800;"></i>
                                    <input type="text" wire:model.live.debounce.300ms="loyaltySearch" class="form-control" placeholder="ابحث بالاسم، أو رقم الجوال، أو رقم الاشتراك..." style="font-size: 0.9rem; padding: 0.5rem 2rem 0.5rem 0.5rem;">
                                </div>
                            </div>
                            
                            @if(count($loyaltySearchResults) > 0)
                                <div style="position: absolute; width: 100%; max-width: 400px; background: var(--surface-color); border: 1px solid var(--border-color); border-radius: var(--radius-sm); margin-top: 4px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 10;">
                                    @foreach($loyaltySearchResults as $res)
                                        <div wire:click="selectLoyaltyCustomer({{ $res->id }})" style="padding: 0.8rem 1rem; border-bottom: 1px solid var(--border-color); cursor: pointer; display: flex; flex-direction: column; gap: 4px; transition: background 0.2s;">
                                            <div style="font-weight: bold; color: var(--primary-color);">
                                                <i class="fa-solid fa-user"></i> {{ $res->name }}
                                            </div>
                                            <div style="font-size: 0.8rem; color: var(--text-secondary); display: flex; gap: 10px;">
                                                <span><i class="fa-solid fa-phone"></i> {{ $res->phone }}</span>
                                                <span><i class="fa-solid fa-id-card"></i> {{ $res->subscription_number }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(strlen(trim($loyaltySearch)) >= 2)
                                <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--warning-color);">
                                    <i class="fa-solid fa-circle-info"></i> لا يوجد عملاء مطابقين للبحث.
                                </div>
                            @endif
                        </div>
                        @if($loyaltyMessage)
                            <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--success-color); font-weight: bold;">
                                {{ $loyaltyMessage }}
                            </div>
                        @endif
                    @endif
                </div>

                <button wire:click="issueTicket" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: bold;" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="issueTicket">
                        <i class="fa-solid fa-ticket"></i> إنشاء وطباعة التذكرة
                        @if($linkedCustomer && $linkedCustomer->loyaltyOffer)
                            <span style="font-size: 0.8rem; opacity: 0.9;">(+ {{ $linkedCustomer->loyaltyOffer->points_per_visit + $linkedCustomer->loyaltyOffer->points_per_order }} نقطة)</span>
                        @endif
                    </span>
                    <span wire:loading wire:target="issueTicket">
                        <i class="fa-solid fa-spinner fa-spin"></i> جاري الإصدار...
                    </span>
                </button>

                @if($linkedCustomer && $linkedCustomer->subscriptionPackage && $linkedCustomer->hasActiveSubscription() && $linkedCustomer->canUseSubscriptionToday())
                    <button wire:click="issueTicketViaSubscription" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: bold; margin-top: 0.5rem; background: linear-gradient(135deg, #00BCD4, #0097A7); color: #fff;" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="issueTicketViaSubscription">
                            <i class="fa-solid fa-crown"></i> إصدار عبر الاشتراك (مجاني)
                        </span>
                        <span wire:loading wire:target="issueTicketViaSubscription">
                            <i class="fa-solid fa-spinner fa-spin"></i> جاري الإصدار...
                        </span>
                    </button>
                @endif
            @elseif($templates->count() > 0)
                <div style="text-align: center; padding: 2rem; color: #777; background: rgba(212,175,55,0.04); border-radius: 8px; border: 1px dashed rgba(212,175,55,0.2);">
                    <i class="fa-solid fa-arrow-up" style="font-size: 1.2rem; margin-bottom: 0.5rem; display: block; color: var(--primary-color);"></i>
                    اختر نوع التذكرة من الأزرار أعلاه
                </div>
            @endif

            {{-- Preview Last Ticket --}}
            @if($lastTicket)
                <div style="margin-top: 1.5rem; background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.3); border-radius: var(--radius-sm); padding: 1.2rem; text-align: center;">
                    <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">آخر تذكرة صدرت</div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: var(--primary-color); margin-bottom: 0.5rem;">#{{ str_pad($lastTicket->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-family: monospace; font-size: 1.1rem; background: rgba(0,0,0,0.3); padding: 0.5rem 1rem; border-radius: var(--radius-sm); margin-bottom: 0.8rem; letter-spacing: 2px;">{{ $lastTicket->barcode }}</div>
                    <button wire:click="$dispatch('print-ticket', { ticketId: {{ $lastTicket->id }}, productsCount: {{ is_array($lastTicket->selected_products) && count($lastTicket->selected_products) > 0 ? count($lastTicket->selected_products) : 1 }} })" class="btn btn-sm" style="margin-top: 0.8rem; background: rgba(212,175,55,0.2); color: var(--primary-color); border: 1px solid var(--primary-color);">
                        <i class="fa-solid fa-print"></i> إعادة طباعة
                    </button>
                </div>
            @endif
        </div>

        {{-- Today's Tickets --}}
        <div class="card" style="border-top: 3px solid var(--info-color);">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; justify-content: space-between;">
                <span><i class="fa-solid fa-list" style="color: var(--info-color);"></i> تذاكر اليوم</span>
                <span style="background: var(--info-color); color: #fff; padding: 2px 10px; border-radius: 50%; font-size: 0.9rem;">{{ $todayTickets->count() }}</span>
            </h3>

            @if($todayTickets->isEmpty())
                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-ticket fa-2x" style="opacity: 0.3; margin-bottom: 0.8rem; display: block;"></i>
                    <p>لا توجد تذاكر لليوم بعد.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="data-table" style="min-width: 480px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الباركود</th>
                                <th>الوقت</th>
                                <th>القالب</th>
                                <th>عميل الولاء</th>
                                <th>المنتجات</th>
                                <th>الحالة</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayTickets as $ticket)
                            <tr wire:key="ticket-{{ $ticket->id }}">
                                <td style="font-weight: bold; color: var(--info-color);">#{{ $ticket->id }}</td>
                                <td style="font-family: monospace; font-size: 0.85rem; letter-spacing: 1px;">{{ $ticket->barcode }}</td>
                                <td style="font-size: 0.82rem; color: var(--text-secondary); white-space: nowrap;">
                                    <i class="fa-regular fa-clock" style="margin-left: 3px;"></i>{{ $ticket->created_at->format('h:i A') }}
                                </td>
                                <td>{{ $ticket->ticketTemplate->name ?? '—' }}</td>
                                <td style="font-size: 0.85rem; color: var(--success-color);">
                                    @if($ticket->customer_id && $ticket->customer)
                                        <button type="button" wire:click="openCustomerDetailsModal({{ $ticket->customer_id }})" style="background: none; border: none; padding: 0; color: var(--success-color); font-weight: bold; cursor: pointer; text-decoration: underline;">
                                            <i class="fa-solid fa-star"></i> {{ $ticket->customer->name }}
                                        </button>
                                    @else
                                        <span style="color: var(--text-secondary);">-</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.82rem;">
                                    @if($ticket->selected_products && is_array($ticket->selected_products))
                                        {{ implode('، ', $ticket->selected_products) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->status === 'active')
                                        <span class="badge" style="background: rgba(76,175,80,0.2); color: var(--success-color); border: 1px solid var(--success-color);">نشطة</span>
                                    @elseif($ticket->status === 'used')
                                        <span class="badge" style="background: rgba(33,150,243,0.2); color: var(--info-color); border: 1px solid var(--info-color);">مستخدمة</span>
                                    @else
                                        <span class="badge" style="background: rgba(244,67,54,0.2); color: var(--danger-color); border: 1px solid var(--danger-color);">ملغاة</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <button wire:click="$dispatch('print-ticket', { ticketId: {{ $ticket->id }}, productsCount: {{ is_array($ticket->selected_products) && count($ticket->selected_products) > 0 ? count($ticket->selected_products) : 1 }} })" class="btn btn-sm btn-ghost" title="طباعة">
                                            <i class="fa-solid fa-print"></i>
                                        </button>
                                        @if($ticket->status === 'active')

                                            <button wire:click="markTicketAsUsed({{ $ticket->id }})" class="btn btn-sm" style="background: rgba(33,150,243,0.2); color: var(--info-color); border: 1px solid rgba(33,150,243,0.3); padding: 0.3rem 0.7rem;" onclick="return confirm('هل أنت متأكد من استخدام هذه التذكرة يدوياً؟')">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Cancelled Tickets Section --}}
        @if(isset($cancelledTickets) && $cancelledTickets->count() > 0)
        <div x-data="{ showCancelled: false }" class="card" style="border-top: 3px solid var(--danger-color); background: rgba(244,67,54,0.02);">
            <button @click="showCancelled = !showCancelled" style="width: 100%; display: flex; justify-content: space-between; align-items: center; background: none; border: none; cursor: pointer; padding: 0; color: var(--text-primary);">
                <h3 style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; color: var(--danger-color);">
                    <i class="fa-solid fa-ban"></i> تذاكر ملغاة اليوم
                    <span style="background: var(--danger-color); color: #fff; padding: 2px 10px; border-radius: 20px; font-size: 0.88rem; font-weight: bold;">{{ $cancelledTickets->count() }}</span>
                </h3>
                <i class="fa-solid" :class="showCancelled ? 'fa-chevron-up' : 'fa-chevron-down'" style="color: var(--danger-color); font-size: 1rem; transition: transform 0.2s;"></i>
            </button>

            <div x-show="showCancelled" x-collapse style="margin-top: 1rem;">
                <div style="overflow-x: auto;">
                    <table class="data-table" style="min-width: 480px;">
                        <thead>
                            <tr style="background: rgba(244,67,54,0.08);">
                                <th>#</th>
                                <th>الباركود</th>
                                <th>القالب</th>
                                <th>المنتجات</th>
                                <th>وقت الإصدار</th>
                                <th>وقت الإلغاء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cancelledTickets as $cTicket)
                            <tr wire:key="cancelled-ticket-{{ $cTicket->id }}" style="background: rgba(244,67,54,0.03);">
                                <td style="font-weight: bold; color: var(--danger-color);">#{{ $cTicket->id }}</td>
                                <td style="font-family: monospace; font-size: 0.85rem; letter-spacing: 1px; text-decoration: line-through; opacity: 0.7;">{{ $cTicket->barcode }}</td>
                                <td>{{ $cTicket->ticketTemplate->name ?? '—' }}</td>
                                <td style="font-size: 0.82rem;">
                                    @if($cTicket->selected_products && is_array($cTicket->selected_products))
                                        {{ implode('، ', $cTicket->selected_products) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="font-size: 0.82rem; color: var(--text-secondary);">{{ $cTicket->created_at->format('h:i A') }}</td>
                                <td style="font-size: 0.82rem; color: var(--danger-color); font-weight: 600;">{{ $cTicket->updated_at->format('h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('print-ticket', (event) => {
                const ticketId = event[0]?.ticketId || event.ticketId;
                const productsCount = event[0]?.productsCount || event.productsCount || 1;
                
                if (ticketId) {
                    let currentIndex = 0;

                    function printNext() {
                        if (currentIndex >= productsCount) return;

                        const oldIframe = document.getElementById('ticket-silent-print-frame');
                        if (oldIframe) oldIframe.remove();

                        const iframe = document.createElement('iframe');
                        iframe.id = 'ticket-silent-print-frame';
                        iframe.style.position = 'fixed';
                        iframe.style.right = '0';
                        iframe.style.bottom = '0';
                        iframe.style.width = '10px';
                        iframe.style.height = '10px';
                        iframe.style.border = '0';
                        iframe.style.opacity = '0.01';
                        iframe.style.pointerEvents = 'none';
                        iframe.style.zIndex = '-99999';
                        document.body.appendChild(iframe);

                        const printUrl = `/ticket/${ticketId}/print?product_index=${currentIndex}&t=${Date.now()}`;
                        iframe.src = printUrl;
                        
                        iframe.onload = function() {
                            setTimeout(() => {
                                try {
                                    iframe.contentWindow.focus();
                                    
                                    let jobDone = false;
                                    function nextTicket() {
                                        if (jobDone) return;
                                        jobDone = true;
                                        currentIndex++;
                                        if (currentIndex < productsCount) {
                                            setTimeout(printNext, 800);
                                        }
                                    }

                                    iframe.contentWindow.addEventListener('afterprint', nextTicket, { once: true });
                                    iframe.contentWindow.print();

                                    // Fallback for kiosk/silent printing
                                    setTimeout(nextTicket, 2000);
                                } catch (e) {
                                    console.error("Print error:", e);
                                }
                            }, 150);
                        };
                    }

                    printNext(); // Start the queue
                }
            });
        });
    </script>
    <style>
        .hide-prints .secret-print-target {
            display: none !important;
        }
    </style>
    <script>
        let secretClickCount = 0;
        let secretClickResetTimer;
        let hidePrintTimer;

        // Hide print buttons by default on load
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('hide-prints');
        });

        function handleSecretClick() {
            secretClickCount++;
            
            // Reset click count after 1 second if not clicked again rapidly
            clearTimeout(secretClickResetTimer);
            secretClickResetTimer = setTimeout(() => {
                secretClickCount = 0;
            }, 1000);
            
            // If clicked 3 times rapidly
            if (secretClickCount >= 3) {
                // Show print buttons
                document.body.classList.remove('hide-prints');
                secretClickCount = 0; // Reset counter
                
                // Clear any existing hide timer
                clearTimeout(hidePrintTimer);
                
                // Automatically hide after 30 seconds
                hidePrintTimer = setTimeout(() => {
                    document.body.classList.add('hide-prints');
                }, 30000);
            }
        }
    </script>

    {{-- PIN Modal (Alpine.js) --}}
    <div x-data="{ pin: @entangle('enteredPin') }" 
         x-show="$wire.showPinModal" 
         style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; justify-content: center; align-items: center;"
         x-init="$watch('$wire.showPinModal', value => { if(value) pin = ''; })"
         :style="{ display: $wire.showPinModal ? 'flex' : 'none' }">
        
        <div style="background: var(--surface-color); padding: 2rem; border-radius: var(--radius-lg); width: 90%; max-width: 350px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid var(--border-color);">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem; font-size: 1.2rem;">
                <i class="fa-solid fa-lock"></i> أدخل رمز المدير للموافقة
            </h3>
            
            {{-- PIN Display --}}
            <div style="background: rgba(0,0,0,0.1); border: 2px solid var(--border-color); border-radius: 8px; height: 50px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; letter-spacing: 10px; font-weight: bold; color: var(--text-primary);">
                <span x-text="pin.replace(/./g, '*') || '----'" style="opacity: pin.length > 0 ? 1 : 0.3;"></span>
            </div>

            {{-- Numpad Grid --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 1.5rem;">
                @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $num)
                    <button type="button" @click="if(pin.length < 4) pin += '{{ $num }}'" style="padding: 15px; font-size: 1.5rem; font-weight: bold; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); cursor: pointer; transition: background 0.2s;" onmousedown="this.style.background='rgba(212,175,55,0.2)'" onmouseup="this.style.background='rgba(255,255,255,0.05)'" onmouseleave="this.style.background='rgba(255,255,255,0.05)'" ontouchstart="this.style.background='rgba(212,175,55,0.2)'" ontouchend="this.style.background='rgba(255,255,255,0.05)'">
                        {{ $num }}
                    </button>
                @endforeach
                <button type="button" @click="pin = pin.slice(0, -1)" style="padding: 15px; font-size: 1.5rem; font-weight: bold; background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); border-radius: 8px; color: var(--danger-color); cursor: pointer;" ontouchstart="this.style.background='rgba(244,67,54,0.3)'" ontouchend="this.style.background='rgba(244,67,54,0.1)'">
                    <i class="fa-solid fa-delete-left"></i>
                </button>
                <button type="button" @click="if(pin.length < 4) pin += '0'" style="padding: 15px; font-size: 1.5rem; font-weight: bold; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); cursor: pointer;" ontouchstart="this.style.background='rgba(212,175,55,0.2)'" ontouchend="this.style.background='rgba(255,255,255,0.05)'">
                    0
                </button>
                <button type="button" @click="$wire.verifyPinAndApprove()" style="padding: 15px; font-size: 1.2rem; font-weight: bold; background: #4CAF50; border: none; border-radius: 8px; color: #fff; cursor: pointer;" ontouchstart="this.style.opacity='0.7'" ontouchend="this.style.opacity='1'">
                    <i class="fa-solid fa-check"></i>
                </button>
            </div>

            <button type="button" @click="$wire.closePinModal()" style="width: 100%; padding: 12px; background: transparent; border: 1px solid var(--text-secondary); border-radius: 8px; color: var(--text-secondary); font-size: 1rem; cursor: pointer;">
                إلغاء
            </button>
        </div>
    </div>

    {{-- Subscription Activation Modal --}}
    @if($showSubscriptionModal)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1050; display: flex; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
            <div class="card" style="background: var(--surface-color); border: 2px solid #00BCD4; width: 90%; max-width: 550px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,188,212,0.3);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
                    <h3 style="margin: 0; color: #00BCD4; font-size: 1.1rem;">
                        <i class="fa-solid fa-crown"></i> تفعيل اشتراك لعميل
                    </h3>
                    <button wire:click="closeSubscriptionModal" style="background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer;">&times;</button>
                </div>

                {{-- Search Customer --}}
                @if(!$subscriptionCustomer)
                    @if(!$isCreatingNewCustomer)
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <label class="form-label" style="margin: 0;"><i class="fa-solid fa-search"></i> ابحث عن العميل</label>
                                <button wire:click="toggleCreateCustomer" class="btn btn-sm" style="background: rgba(0, 188, 212, 0.1); color: #00BCD4; border: 1px solid #00BCD4; padding: 0.2rem 0.5rem; border-radius: 4px; cursor: pointer;">
                                    <i class="fa-solid fa-plus"></i> عميل جديد
                                </button>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="subscriptionSearch" class="form-control" placeholder="ابحث بالاسم، رقم الجوال أو رقم الاشتراك...">
                        </div>

                        @if(count($subscriptionSearchResults) > 0)
                            <div style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); margin-bottom: 1rem; max-height: 200px; overflow-y: auto;">
                                @foreach($subscriptionSearchResults as $res)
                                    <div wire:click="selectSubscriptionCustomer({{ $res->id }})" style="padding: 0.8rem 1rem; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.2s;">
                                        <div style="font-weight: bold; color: var(--primary-color);">
                                            <i class="fa-solid fa-user"></i> {{ $res->name }}
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary); display: flex; gap: 10px; flex-wrap: wrap;">
                                            <span><i class="fa-solid fa-phone"></i> {{ $res->phone ?: '---' }}</span>
                                            <span><i class="fa-solid fa-id-card"></i> {{ $res->subscription_number }}</span>
                                            @if($res->subscriptionPackage && $res->hasActiveSubscription())
                                                <span style="color: var(--success-color);"><i class="fa-solid fa-crown"></i> {{ $res->subscriptionPackage->name }} (ساري)</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(strlen(trim($subscriptionSearch)) >= 2)
                            <div style="text-align: center; padding: 1rem; color: var(--warning-color); font-size: 0.9rem;">
                                <i class="fa-solid fa-circle-info"></i> لا يوجد عملاء مطابقين.
                            </div>
                        @endif
                    @else
                        {{-- Create New Customer Form --}}
                        <div style="margin-bottom: 1rem; background: rgba(0, 188, 212, 0.05); border: 1px solid rgba(0, 188, 212, 0.3); border-radius: var(--radius-sm); padding: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h4 style="margin: 0; color: #00BCD4;"><i class="fa-solid fa-user-plus"></i> إضافة عميل جديد</h4>
                                <button wire:click="toggleCreateCustomer" class="btn btn-sm" style="background: none; border: none; color: var(--text-secondary); cursor: pointer;"><i class="fa-solid fa-arrow-left"></i> عودة للبحث</button>
                            </div>
                            <div style="margin-bottom: 0.8rem;">
                                <label class="form-label">اسم العميل <span style="color: var(--danger-color);">*</span></label>
                                <input type="text" wire:model="newCustomerName" class="form-control" placeholder="الاسم الكامل">
                                @error('newCustomerName') <div style="color: var(--danger-color); font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label class="form-label">رقم الجوال (اختياري)</label>
                                <input type="text" wire:model="newCustomerPhone" class="form-control" placeholder="رقم الجوال">
                                @error('newCustomerPhone') <div style="color: var(--danger-color); font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
                            </div>
                            <button wire:click="saveNewCustomer" class="btn" style="width: 100%; background: #00BCD4; color: #fff; font-weight: bold; border: none; padding: 0.8rem; border-radius: var(--radius-sm); cursor: pointer;">
                                <i class="fa-solid fa-save"></i> حفظ ومتابعة الاشتراك
                            </button>
                        </div>
                    @endif
                @else
                    {{-- Selected Customer --}}
                    <div style="background: rgba(0,188,212,0.08); border: 1px solid #00BCD4; border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: bold; font-size: 1.1rem; color: #00BCD4;">
                                    <i class="fa-solid fa-user-check"></i> {{ $subscriptionCustomer->name }}
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px;">
                                    رقم: {{ $subscriptionCustomer->subscription_number }} | جوال: {{ $subscriptionCustomer->phone ?: '---' }}
                                </div>
                            </div>
                            <button wire:click="$set('subscriptionCustomer', null)" class="btn btn-sm" style="background: rgba(244,67,54,0.2); color: var(--danger-color); border: none;">
                                <i class="fa-solid fa-xmark"></i> تغيير
                            </button>
                        </div>

                        @if($subscriptionCustomer->subscriptionPackage)
                            <div style="margin-top: 0.8rem; padding: 0.5rem 0.8rem; border-radius: var(--radius-sm); font-size: 0.85rem;
                                {{ $subscriptionCustomer->hasActiveSubscription() ? 'background: rgba(76,175,80,0.1); border: 1px solid var(--success-color);' : 'background: rgba(244,67,54,0.1); border: 1px solid var(--danger-color);' }}">
                                @if($subscriptionCustomer->hasActiveSubscription())
                                    <i class="fa-solid fa-check-circle" style="color: var(--success-color);"></i>
                                    <strong style="color: var(--success-color);">اشتراك ساري:</strong> {{ $subscriptionCustomer->subscriptionPackage->name }}
                                    — حتى {{ $subscriptionCustomer->subscription_end_date }}
                                @else
                                    <i class="fa-solid fa-times-circle" style="color: var(--danger-color);"></i>
                                    <strong style="color: var(--danger-color);">اشتراك منتهي:</strong> {{ $subscriptionCustomer->subscriptionPackage->name }}
                                    — انتهى {{ $subscriptionCustomer->subscription_end_date }}
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Select Package --}}
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="font-weight: bold;"><i class="fa-solid fa-box-open" style="color: #00BCD4;"></i> اختر الباقة</label>
                        <div style="display: grid; gap: 8px;">
                            @foreach($packages as $pkg)
                                <div wire:click="$set('selectedSubscriptionPackageId', {{ $pkg->id }})"
                                     style="padding: 1rem; border-radius: var(--radius-sm); cursor: pointer; transition: all 0.2s;
                                            border: 2px solid {{ $selectedSubscriptionPackageId == $pkg->id ? '#00BCD4' : 'var(--border-color)' }};
                                            background: {{ $selectedSubscriptionPackageId == $pkg->id ? 'rgba(0,188,212,0.1)' : 'rgba(0,0,0,0.02)' }};">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <strong style="color: {{ $selectedSubscriptionPackageId == $pkg->id ? '#00BCD4' : 'var(--text-primary)' }}; font-size: 1.05rem;">
                                                @if($selectedSubscriptionPackageId == $pkg->id) <i class="fa-solid fa-circle-check"></i> @endif
                                                {{ $pkg->name }}
                                            </strong>
                                            <div style="font-size: 0.82rem; color: var(--text-secondary); margin-top: 4px;">
                                                {{ $pkg->duration_days }} يوم — {{ $pkg->daily_order_limit }} طلب/يوم
                                            </div>
                                        </div>
                                        <span style="font-size: 1.2rem; font-weight: 900; color: var(--success-color);">{{ number_format($pkg->price, 0) }} ر.ي</span>
                                    </div>
                                    @if(is_array($pkg->included_products) && count($pkg->included_products) > 0)
                                        <div style="margin-top: 6px; display: flex; flex-wrap: wrap; gap: 4px;">
                                            @foreach($pkg->included_products as $p)
                                                <span style="background: rgba(0,188,212,0.1); color: #00BCD4; padding: 1px 6px; border-radius: 4px; font-size: 0.75rem;">{{ $p }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button wire:click="activateSubscription" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: bold; background: linear-gradient(135deg, #00BCD4, #0097A7); color: #fff;" wire:loading.attr="disabled"
                            {{ !$selectedSubscriptionPackageId ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="activateSubscription">
                            <i class="fa-solid fa-crown"></i> تفعيل الاشتراك الآن
                        </span>
                        <span wire:loading wire:target="activateSubscription">
                            <i class="fa-solid fa-spinner fa-spin"></i> جاري التفعيل...
                        </span>
                    </button>
                @endif

                <button wire:click="closeSubscriptionModal" style="width: 100%; margin-top: 0.5rem; padding: 0.7rem; background: transparent; border: 1px solid var(--text-secondary); border-radius: var(--radius-sm); color: var(--text-secondary); cursor: pointer; font-family: var(--font-family); font-size: 0.95rem;">
                    إلغاء
                </button>
            </div>
        </div>
    @endif

    {{-- Customer Details Popup Modal After Ticket Print / Click --}}
    @if($showCustomerDetailsModal && $printedCustomerDetails)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1060; display:flex; justify-content:center; align-items:center; animation: fadeIn 0.3s ease;">
            <div class="card" style="background:var(--surface-color); border:2px solid var(--primary-color); width:95%; max-width:650px; max-height:92vh; overflow-y:auto; box-shadow: 0 15px 40px rgba(0,0,0,0.6); border-radius: 16px;">
                <div class="modal-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="modal-title" style="color:var(--primary-color); display: flex; align-items: center; gap: 8px; margin: 0; font-size: 1.25rem;">
                        <i class="fa-solid fa-star fa-bounce" style="color: #FF9800;"></i>
                        تفاصيل عميل الولاء والاشتراك
                    </h4>
                    <button type="button" class="btn-close btn-ghost" wire:click="closeCustomerDetailsModal" style="font-size:1.8rem; line-height:1; color: var(--text-secondary); cursor: pointer;">&times;</button>
                </div>

                <div class="modal-body" style="padding: 0.5rem 0.2rem;">
                    {{-- Customer Profile Header --}}
                    <div style="background: linear-gradient(135deg, rgba(212,175,55,0.15), rgba(255,152,0,0.05)); border: 1px solid var(--primary-color); padding: 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <h3 style="margin-bottom: 0.3rem; font-size: 1.3rem; color: #fff; font-weight: bold;">
                                    <i class="fa-solid fa-user-tag" style="color: var(--primary-color);"></i>
                                    {{ $printedCustomerDetails->name }}
                                    @if($printedCustomerDetails->is_phone_verified)
                                        <i class="fa-solid fa-circle-check" style="color: #25D366; font-size: 0.9rem;" title="موثق"></i>
                                    @endif
                                </h3>
                                <div style="color: var(--text-secondary); font-size: 0.95rem; display: flex; gap: 15px; flex-wrap: wrap;">
                                    <span><i class="fa-solid fa-phone" style="color: var(--primary-color);"></i> {{ $printedCustomerDetails->phone ?: 'بدون رقم' }}</span>
                                    <span><i class="fa-solid fa-id-card" style="color: var(--primary-color);"></i> الاشتراك: <strong>{{ $printedCustomerDetails->subscription_number }}</strong></span>
                                </div>
                            </div>
                            <div>
                                <span class="badge badge-active" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                                    {{ $printedCustomerDetails->loyaltyOffer ? $printedCustomerDetails->loyaltyOffer->name : 'عميل عام' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @php
                        $rem = $printedCustomerDetails->total_points - $printedCustomerDetails->used_points;
                        $off = $printedCustomerDetails->loyaltyOffer;
                        $tiersList = $off ? $off->rewardTiers : collect();
                    @endphp

                    {{-- Points Grid --}}
                    <div class="grid grid-cols-3 gap-3 mb-4 text-center">
                        <div style="background:rgba(255,255,255,0.03); padding:0.9rem; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
                            <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:4px;">إجمالي النقاط</div>
                            <div style="font-size:1.4rem; font-weight:bold; color:var(--info-color);">{{ $printedCustomerDetails->total_points }}</div>
                        </div>
                        <div style="background:rgba(255,255,255,0.03); padding:0.9rem; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
                            <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:4px;">النقاط المستهلكة</div>
                            <div style="font-size:1.4rem; font-weight:bold; color:var(--warning-color);">{{ $printedCustomerDetails->used_points }}</div>
                        </div>
                        <div style="background:rgba(212,175,55,0.12); padding:0.9rem; border-radius:var(--radius-sm); border:1.5px solid var(--primary-color); box-shadow: 0 0 12px rgba(212,175,55,0.25);">
                            <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:4px;">الرصيد المتاح</div>
                            <div style="font-size:1.6rem; font-weight:bold; color:var(--primary-color);">{{ $rem }} نقطة</div>
                        </div>
                    </div>

                    {{-- Reward Tiers Section --}}
                    @if($tiersList && $tiersList->count() > 0)
                        <div style="margin-bottom: 1.2rem; background: rgba(0,0,0,0.15); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                            <h5 style="font-size: 0.95rem; margin-bottom: 0.8rem; color: var(--primary-color); display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-trophy"></i> مستويات المكافآت المستحقة والمتاحة:
                            </h5>
                            @foreach($tiersList as $t)
                                @php
                                    $hasEnough = $rem >= $t->required_points;
                                @endphp
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; margin-bottom: 6px; border-radius: 6px; background: {{ $hasEnough ? 'rgba(76,175,80,0.12)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $hasEnough ? 'var(--success-color)' : 'var(--border-color)' }};">
                                    <span style="font-weight: bold; font-size: 0.9rem; color: {{ $hasEnough ? 'var(--success-color)' : 'var(--text-primary)' }};">
                                        @if($hasEnough) <i class="fa-solid fa-gift text-success"></i> @else <i class="fa-solid fa-lock text-secondary"></i> @endif
                                        {{ $t->reward_description }}
                                    </span>
                                    <span class="badge {{ $hasEnough ? 'badge-success' : 'badge-prep' }}" style="font-size: 0.8rem;">
                                        {{ $t->required_points }} نقطة {{ $hasEnough ? '✅ متاح!' : '' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Subscription Info --}}
                    @if($printedCustomerDetails->subscriptionPackage)
                        <div style="margin-bottom: 1.2rem; padding: 1rem; background: rgba(0,188,212,0.08); border: 1px solid #00BCD4; border-radius: var(--radius-sm);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: #00BCD4; font-size: 1rem;"><i class="fa-solid fa-crown"></i> الباقة: {{ $printedCustomerDetails->subscriptionPackage->name }}</strong>
                                    <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 3px;">
                                        ينتهي في: {{ $printedCustomerDetails->subscription_end_date }}
                                    </div>
                                </div>
                                <div>
                                    @if($printedCustomerDetails->hasActiveSubscription())
                                        <span class="badge badge-success">اشتراك ساري ✅</span>
                                    @else
                                        <span class="badge badge-inactive">منتهي ❌</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div style="display: flex; gap: 10px; margin-top: 1.5rem;">
                        <a href="{{ route('reception.loyalty') }}" class="btn" style="flex: 1; text-align: center; background: linear-gradient(135deg, #FF9800, #F57C00); color: #fff; padding: 0.8rem; font-weight: bold; border-radius: var(--radius-sm); text-decoration: none;">
                            <i class="fa-solid fa-star"></i> إدارة ولاء العميل بالكامل
                        </a>
                        <button wire:click="closeCustomerDetailsModal" class="btn" style="flex: 1; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); padding: 0.8rem; font-weight: bold; border-radius: var(--radius-sm);">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

