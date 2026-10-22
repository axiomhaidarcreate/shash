<div wire:poll.3s="checkNewOrders">
    <livewire:shift-status-banner />
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px; position: relative; margin-top: 1rem;">
        <!-- Secret Button -->
        <button onclick="handleSecretClick()" style="position: absolute; top: 0; right: 0; width: 60px; height: 60px; background: transparent; border: 1px dashed rgba(128, 128, 128, 0.3); border-radius: 8px; outline: none; z-index: 999; cursor: default;" title="إظهار أزرار الطباعة (3 ضغطات)"></button>
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem); margin: 0;"><i class="fa-solid fa-fire" style="color: var(--warning-color);"></i> {{ $kitchenName }}</h2>
            <button onclick="Livewire.dispatch('openDailyPurchaseModal')" class="btn" style="background: linear-gradient(135deg, #FF9800, #F57C00); color: #fff; border: none; padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-weight: bold; font-size: 0.9rem; box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3); transition: all 0.3s; margin-right: 10px;">
                <i class="fa-solid fa-cart-plus"></i> طلبات
            </button>
            @if(auth()->user()->hasRole('admin'))
                <button wire:click="openReportModal" class="btn" style="background: linear-gradient(135deg, #1E88E5, #1565C0); color: #fff; border: none; padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-weight: bold; font-size: 0.9rem; box-shadow: 0 4px 15px rgba(30, 136, 229, 0.3); transition: all 0.3s; margin-right: 10px;">
                    <i class="fa-solid fa-chart-pie"></i> تقارير الاغلاق اليومية المباع
                </button>
            @endif
            <div class="secret-print-target" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; align-items: center; background: rgba(0,0,0,0.2); padding: 12px; border-radius: 8px; border: 1px dashed rgba(128,128,128,0.3); width: 100%;">
                {{-- Date Filters --}}
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 5px; grid-column: 1 / -1;">
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <label style="font-size: 0.8rem; color: var(--text-secondary); margin: 0;"><i class="fa-regular fa-calendar"></i> من:</label>
                        <input type="date" wire:model.live="filterDateFrom" class="form-control" style="width: auto; padding: 0.2rem 0.5rem; min-height: 30px; font-size: 0.8rem;">
                        <input type="time" wire:model.live="filterTimeFrom" class="form-control" style="width: auto; padding: 0.2rem 0.5rem; min-height: 30px; font-size: 0.8rem;">
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <label style="font-size: 0.8rem; color: var(--text-secondary); margin: 0;"><i class="fa-regular fa-calendar"></i> إلى:</label>
                        <input type="date" wire:model.live="filterDateTo" class="form-control" style="width: auto; padding: 0.2rem 0.5rem; min-height: 30px; font-size: 0.8rem;">
                        <input type="time" wire:model.live="filterTimeTo" class="form-control" style="width: auto; padding: 0.2rem 0.5rem; min-height: 30px; font-size: 0.8rem;">
                    </div>
                </div>

                {{-- Manager approvals print --}}
                <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                    <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">موافقات المدير:</span>
                    <a href="/invoice/manager-approved-print-thermal?start_date={{ $filterDateFrom }}&end_date={{ $filterDateTo }}&start_time={{ $filterTimeFrom }}&end_time={{ $filterTimeTo }}&title={{ $kitchenName }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn" style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;"><i class="fa-solid fa-receipt"></i> حراري</a>
                    <a href="/invoice/manager-approved-print?start_date={{ $filterDateFrom }}&end_date={{ $filterDateTo }}&start_time={{ $filterTimeFrom }}&end_time={{ $filterTimeTo }}&title={{ $kitchenName }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn" style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;"><i class="fa-solid fa-file-lines"></i> A4</a>
                </div>

                {{-- Waste print --}}
                <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                    <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">تالف معتمد:</span>
                    <a href="/invoice/waste-print-thermal?start_date={{ $filterDateFrom }}&end_date={{ $filterDateTo }}&start_time={{ $filterTimeFrom }}&end_time={{ $filterTimeTo }}&title={{ $kitchenName }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn" style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;"><i class="fa-solid fa-receipt"></i> حراري</a>
                    <a href="/invoice/waste-print?start_date={{ $filterDateFrom }}&end_date={{ $filterDateTo }}&start_time={{ $filterTimeFrom }}&end_time={{ $filterTimeTo }}&title={{ $kitchenName }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn" style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;"><i class="fa-solid fa-file-lines"></i> A4</a>
                </div>

                {{-- Inventory print --}}
                <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                    <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">تقرير المبيعات/الجرد:</span>
                    <a href="/invoice/inventory-print-thermal?start_date={{ $filterDateFrom }}&end_date={{ $filterDateTo }}&start_time={{ $filterTimeFrom }}&end_time={{ $filterTimeTo }}&title={{ $kitchenName }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn" style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;"><i class="fa-solid fa-receipt"></i> حراري</a>
                    <a href="/invoice/inventory-print?start_date={{ $filterDateFrom }}&end_date={{ $filterDateTo }}&start_time={{ $filterTimeFrom }}&end_time={{ $filterTimeTo }}&title={{ $kitchenName }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn" style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;"><i class="fa-solid fa-file-lines"></i> A4</a>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <span class="badge badge-pending">{{ $pendingOrders->count() }} جديد</span>
            <span class="badge badge-prep">{{ $prepOrders->count() }} قيد التحضير</span>
            <span style="color: var(--success-color); font-size: 0.8rem;"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> مباشر</span>
        </div>
    </div>

    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-2">
        <!-- New Orders -->
        <div class="card" style="border-top: 3px solid var(--warning-color);">
            <h3 style="margin-bottom: 1rem; font-size: clamp(0.95rem, 3vw, 1.1rem);"><i class="fa-solid fa-bell"></i> الطلبات الجديدة</h3>
            <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                @forelse($pendingOrders as $order)
                    @php $isRushed = $order->is_rushed || $order->created_at <= now()->subMinutes(7) || $order->kitchen_alert_count > 0; @endphp
                    <div class="order-card" data-order-id="{{ $order->id }}" data-alert-count="{{ $order->kitchen_alert_count }}" style="background: {{ $isRushed ? 'rgba(244, 67, 54, 0.1)' : 'rgba(255, 152, 0, 0.05)' }}; border: 1px solid {{ $isRushed ? 'var(--danger-color)' : 'rgba(255, 152, 0, 0.25)' }}; padding: clamp(0.8rem, 2vw, 1rem); border-radius: var(--radius-sm); animation: slideIn 0.4s ease;">
                        @if($order->is_rushed)
                            <div style="background: linear-gradient(90deg, #d32f2f, #ff9800); color: #fff; padding: 6px 12px; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: bold; margin-bottom: 0.6rem; display: inline-flex; align-items: center; gap: 8px; animation: pulse 1s infinite; width: 100%; box-sizing: border-box;">
                                <i class="fa-solid fa-bolt fa-beat" style="color: #ffeb3b;"></i>
                                <span>🚨 تم استعجال الطلب من قبل الموظف ({{ $order->user->name ?? 'غير معروف' }})!</span>
                            </div>
                        @elseif($order->kitchen_alert_count > 0)
                            <div style="background: var(--danger-color); color: #fff; padding: 4px 8px; border-radius: var(--radius-sm); font-size: 0.8rem; font-weight: bold; margin-bottom: 0.5rem; display: inline-flex; align-items: center; gap: 6px; animation: pulse 1s infinite;">
                                <i class="fa-solid fa-bell fa-shake"></i> استعجال طارئ من الويتر ({{ $order->kitchen_alert_count }})
                            </div>
                        @elseif($order->created_at <= now()->subMinutes(7))
                            <div style="color: var(--danger-color); font-size: 0.8rem; font-weight: bold; margin-bottom: 0.5rem; animation: pulse 1.5s infinite;"><i class="fa-solid fa-triangle-exclamation"></i> طلب متأخر (مرت 7 دقائق)</div>
                        @endif
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.6rem; align-items: center; flex-wrap: wrap; gap: 4px;">
                            <span style="font-size: clamp(1.1rem, 3.5vw, 1.3rem); font-weight: 900; color: {{ $isRushed ? 'var(--danger-color)' : 'var(--primary-color)' }};">
                                @if($order->table)
                                    طاولة {{ $order->table->number }} / كرسي {{ $order->chair->number ?? '-' }} @if($order->table->section)<span style="font-size: 0.7em;">({{ $order->table->section }})</span>@endif
                                @else
                                    <span style="color: #9C27B0;"><i class="fa-solid fa-mobile-screen"></i> طلب ذاتي</span>
                                @endif
                            </span>
                            <span style="color: {{ $isRushed ? 'var(--danger-color)' : 'var(--text-secondary)' }}; font-size: clamp(0.7rem, 2vw, 0.8rem);"><i class="fa-regular fa-clock"></i> {{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size: clamp(1.1rem, 3.5vw, 1.3rem); font-weight: bold; margin-bottom: 0.6rem;">{{ $order->quantity }}x {{ $order->shisha_type }}</div>
                        @if($order->source === 'self_service')
                            <div style="background: rgba(156, 39, 176, 0.1); padding: 0.5rem 0.7rem; border-radius: var(--radius-sm); margin-bottom: 0.6rem; border-right: 3px solid #9C27B0; font-size: clamp(0.85rem, 2.5vw, 0.95rem); color: #9C27B0; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-mobile-screen fa-bounce"></i> <span>طلب ذاتي (الحفلة) 🎉</span>
                            </div>
                        @endif
                        @if($order->guest_name)
                            <div style="background: rgba(212,175,55,0.1); padding: 0.5rem 0.7rem; border-radius: var(--radius-sm); margin-bottom: 0.6rem; border-right: 3px solid var(--primary-color); color: var(--primary-color); font-weight: bold;"><i class="fa-solid fa-user-tag"></i> ضيافة باسم: {{ $order->guest_name }}</div>
                        @endif
                        @if($order->ticket)
                            <div style="padding: 0.5rem 0.7rem; border-radius: var(--radius-sm); margin-bottom: 0.6rem; border-right: 3px solid #2196F3; color: var(--primary-color); font-weight: bold;"><i class="fa-solid fa-ticket" style="color: #2196F3;"></i> تذكرة: {{ $order->ticket->barcode }}</div>
                        @endif
                        @if($order->notes)
                            <div style="padding: 0.5rem 0.7rem; border-radius: var(--radius-sm); margin-bottom: 0.6rem; border-right: 3px solid var(--primary-color); color: var(--primary-color); font-weight: bold;"><i class="fa-solid fa-circle-info"></i> ملاحظة: {{ $order->notes }}</div>
                        @endif
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.6rem;"><i class="fa-solid fa-user"></i> {{ $order->user->name ?? 'طلب ذاتي' }}</div>
                        <div style="display: flex; gap: 8px;">
                            <button wire:click="startPrep({{ $order->id }})" class="btn btn-warning" style="flex: 1; padding: 0.8rem; font-size: clamp(0.9rem, 2.5vw, 1rem); min-height: 44px;"><i class="fa-solid fa-play"></i> بدء التحضير</button>
                            <button wire:click="cancelOrder({{ $order->id }})" onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب نهائياً؟')" class="btn btn-outline" style="width: 44px; min-width: 44px; height: 44px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-color: var(--danger-color); color: var(--danger-color);" title="إلغاء"><i class="fa-solid fa-xmark"></i></button>
                            <button wire:click="reprintKitchen({{ $order->id }})" class="btn btn-outline secret-print-target" style="width: 44px; min-width: 44px; height: 44px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="إعادة طباعة"><i class="fa-solid fa-print"></i></button>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-check-circle fa-2x" style="margin-bottom: 0.8rem; opacity: 0.3;"></i>
                        <p>لا توجد طلبات جديدة</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Orders in Prep -->
        <div class="card" style="border-top: 3px solid var(--info-color);">
            <h3 style="margin-bottom: 1rem; font-size: clamp(0.95rem, 3vw, 1.1rem);"><i class="fa-solid fa-blender"></i> قيد التحضير</h3>
            <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                @forelse($prepOrders as $order)
                    @php $isRushed = $order->is_rushed || $order->created_at <= now()->subMinutes(7) || $order->kitchen_alert_count > 0; @endphp
                    <div data-order-id="{{ $order->id }}" data-alert-count="{{ $order->kitchen_alert_count }}" style="background: {{ $isRushed ? 'rgba(244, 67, 54, 0.1)' : 'rgba(33, 150, 243, 0.05)' }}; border: 1px solid {{ $isRushed ? 'var(--danger-color)' : 'rgba(33, 150, 243, 0.25)' }}; padding: clamp(0.8rem, 2vw, 1rem); border-radius: var(--radius-sm);">
                        @if($order->is_rushed)
                            <div style="background: linear-gradient(90deg, #d32f2f, #ff9800); color: #fff; padding: 6px 12px; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: bold; margin-bottom: 0.6rem; animation: pulse 1s infinite; width: 100%;"><i class="fa-solid fa-bolt fa-beat" style="color: #ffeb3b;"></i> 🚨 تم استعجال الطلب ({{ $order->user->name ?? '' }})!</div>
                        @elseif($order->kitchen_alert_count > 0)
                            <div style="background: var(--danger-color); color: #fff; padding: 4px 8px; border-radius: var(--radius-sm); font-size: 0.8rem; font-weight: bold; margin-bottom: 0.5rem; animation: pulse 1s infinite;"><i class="fa-solid fa-bell fa-shake"></i> استعجال طارئ ({{ $order->kitchen_alert_count }})</div>
                        @elseif($order->created_at <= now()->subMinutes(7))
                            <div style="color: var(--danger-color); font-size: 0.8rem; font-weight: bold; margin-bottom: 0.5rem; animation: pulse 1.5s infinite;"><i class="fa-solid fa-triangle-exclamation"></i> طلب متأخر</div>
                        @endif
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.6rem; flex-wrap: wrap; gap: 4px;">
                            <span style="font-size: clamp(1.1rem, 3.5vw, 1.3rem); font-weight: 900; color: {{ $isRushed ? 'var(--danger-color)' : 'var(--info-color)' }};">@if($order->table)طاولة {{ $order->table->number }} / كرسي {{ $order->chair->number ?? '-' }}@else<span style="color: #9C27B0;"><i class="fa-solid fa-mobile-screen"></i> طلب ذاتي</span>@endif</span>
                            <span style="color: var(--text-secondary); font-size: 0.8rem;"><i class="fa-regular fa-clock"></i> {{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size: clamp(1.1rem, 3.5vw, 1.3rem); font-weight: bold; margin-bottom: 0.6rem;">{{ $order->quantity }}x {{ $order->shisha_type }}</div>
                        @if($order->source === 'self_service')
                            <div style="background: rgba(156, 39, 176, 0.1); padding: 0.5rem 0.7rem; border-radius: var(--radius-sm); margin-bottom: 0.6rem; border-right: 3px solid #9C27B0; font-size: clamp(0.85rem, 2.5vw, 0.95rem); color: #9C27B0; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-mobile-screen fa-bounce"></i> <span>طلب ذاتي (الحفلة) 🎉</span>
                            </div>
                        @endif
                        @if($order->notes)
                            <div style="padding: 0.5rem 0.7rem; border-radius: var(--radius-sm); margin-bottom: 0.6rem; border-right: 3px solid var(--info-color); color: var(--info-color); font-weight: bold;"><i class="fa-solid fa-circle-info"></i> {{ $order->notes }}</div>
                        @endif
                        <div style="display: flex; gap: 8px;">
                            <button wire:click="markAsReady({{ $order->id }})" class="btn btn-success" style="flex: 1; padding: 0.8rem; font-size: clamp(0.9rem, 2.5vw, 1rem); min-height: 44px;"><i class="fa-solid fa-check"></i> تم التحضير ✅</button>
                            <button wire:click="cancelOrder({{ $order->id }})" onclick="return confirm('هل أنت متأكد؟')" class="btn btn-outline" style="width: 44px; min-width: 44px; height: 44px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-color: var(--danger-color); color: var(--danger-color);" title="إلغاء"><i class="fa-solid fa-xmark"></i></button>
                            <button wire:click="reprintKitchen({{ $order->id }})" class="btn btn-outline secret-print-target" style="width: 44px; min-width: 44px; height: 44px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="إعادة طباعة"><i class="fa-solid fa-print"></i></button>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-spinner fa-2x" style="margin-bottom: 0.8rem; opacity: 0.3;"></i>
                        <p>لا توجد طلبات قيد التحضير</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Report Modal --}}
    @if($showReportModal)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; justify-content: center; align-items: center; padding: 20px; backdrop-filter: blur(5px);">
        <div style="background: var(--surface-color); width: 100%; max-width: 900px; max-height: 90vh; border-radius: var(--radius-lg); display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); animation: slideIn 0.3s ease;">
            <div style="padding: 1.5rem; background: rgba(255,255,255,0.03); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: var(--primary-color);"><i class="fa-solid fa-chart-pie"></i> تقارير {{ $kitchenName }}</h3>
                <button wire:click="closeReportModal" style="background: transparent; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                {{-- Daily Summary --}}
                <div style="margin-bottom: 2rem;" x-data="{ expandedCategory: null }">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
                        <h4 style="color: var(--text-primary); margin: 0; border-right: 4px solid var(--info-color); padding-right: 10px;"><i class="fa-solid fa-calendar-day"></i> ملخص مبيعات اليوم</h4>
                        <div style="display: flex; gap: 10px;">
                            <a href="/invoice/kitchen-report-print?type=daily&kitchen={{ $kitchenSlug }}" target="_blank" class="btn btn-sm" style="background: #1976D2; color: #fff; border: none;"><i class="fa-solid fa-print"></i> طباعة يومي A4</a>
                            <a href="/invoice/kitchen-report-print-thermal?type=daily&kitchen={{ $kitchenSlug }}" target="_blank" class="btn btn-sm" style="background: #1565C0; color: #fff; border: none;"><i class="fa-solid fa-receipt"></i> طباعة حراري</a>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                        @forelse($dailyStats as $categoryName => $catData)
                            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s;" @click="expandedCategory = expandedCategory === '{{ $categoryName }}' ? null : '{{ $categoryName }}'">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <h5 style="margin: 0; font-size: 1.2rem; color: #fff;">{{ $categoryName }}</h5>
                                </div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-secondary);">المباع:</span><span style="color: var(--success-color); font-weight: bold;">{{ $catData['total_sold'] }}</span></div>
                                <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-secondary);">التالف:</span><span style="color: var(--danger-color); font-weight: bold;">{{ $catData['total_cancelled'] }}</span></div>
                                <div x-show="expandedCategory === '{{ $categoryName }}'" x-transition style="margin-top: 10px; border-top: 1px dashed var(--border-color); padding-top: 10px;">
                                    @foreach($catData['products'] as $productName => $prodData)
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.9rem; background: rgba(0,0,0,0.2); padding: 6px; border-radius: 6px;">
                                            <span style="color: #ddd;">{{ $productName }}</span>
                                            <div style="display: flex; gap: 12px;">
                                                <span style="color: var(--success-color);">✓ {{ $prodData['sold'] }}</span>
                                                <span style="color: var(--danger-color);">✗ {{ $prodData['cancelled'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1/-1; text-align: center; color: var(--text-secondary); padding: 20px;">لا توجد مبيعات مسجلة لليوم.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Monthly/Range Log --}}
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
                        <h4 style="color: var(--text-primary); margin: 0; border-right: 4px solid var(--warning-color); padding-right: 10px;"><i class="fa-solid fa-calendar-days"></i> السجل الشهري / بحث مخصص</h4>
                        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 5px; background: rgba(0,0,0,0.2); padding: 5px 10px; border-radius: 8px;">
                                <label style="font-size: 0.8rem; color: var(--text-secondary); margin: 0;">من:</label>
                                <input type="date" wire:model.live="monthlyReportStartDate" class="form-control" style="padding: 0.2rem 0.5rem; min-height: 30px; font-size: 0.8rem; width: auto; background: transparent; border: none; color: #fff;">
                                <label style="font-size: 0.8rem; color: var(--text-secondary); margin: 0 0 0 10px;">إلى:</label>
                                <input type="date" wire:model.live="monthlyReportEndDate" class="form-control" style="padding: 0.2rem 0.5rem; min-height: 30px; font-size: 0.8rem; width: auto; background: transparent; border: none; color: #fff;">
                            </div>
                            <a href="/invoice/kitchen-report-print?type=range&start_date={{ $monthlyReportStartDate }}&end_date={{ $monthlyReportEndDate }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn btn-sm" style="background: var(--warning-color); color: #000; border: none;"><i class="fa-solid fa-print"></i> طباعة A4</a>
                            <a href="/invoice/kitchen-report-print-thermal?type=range&start_date={{ $monthlyReportStartDate }}&end_date={{ $monthlyReportEndDate }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn btn-sm" style="background: #1565C0; color: #fff; border: none;"><i class="fa-solid fa-receipt"></i> طباعة حراري</a>
                        </div>
                    </div>
                    <div style="overflow-x: auto; background: rgba(0,0,0,0.2); border-radius: 12px;">
                        <table class="data-table" style="min-width: 600px; width: 100%;">
                            <thead style="background: rgba(255,255,255,0.05);">
                                <tr>
                                    <th style="padding: 12px;">التاريخ</th>
                                    <th style="padding: 12px;">الأصناف والمبيعات</th>
                                    <th style="padding: 12px; text-align: center;">إجمالي اليوم</th>
                                    <th style="padding: 12px; text-align: center;">طباعة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyStats as $date => $dayData)
                                    <tr>
                                        <td style="font-weight: bold; color: var(--primary-color); padding: 12px;">{{ $date }}</td>
                                        <td style="padding: 12px;">
                                            @foreach($dayData['categories'] as $cat => $stats)
                                                <div style="margin-bottom: 6px; font-size: 0.9rem;">
                                                    <span style="font-weight: bold; color: #ddd;">{{ $cat }}:</span>
                                                    <span style="color: var(--success-color); margin-right: 8px;">مباع: {{ $stats['sold'] }}</span>|
                                                    <span style="color: var(--danger-color); margin-right: 8px;">تالف: {{ $stats['cancelled'] }}</span>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <div><span style="color: var(--success-color); font-weight: bold;">مباع: {{ $dayData['total_sold'] }}</span></div>
                                            <div><span style="color: var(--danger-color); font-weight: bold;">تالف: {{ $dayData['total_cancelled'] }}</span></div>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <div style="display: flex; flex-direction: column; gap: 5px; align-items: center;">
                                                <a href="/invoice/kitchen-report-print?type=range&start_date={{ $date }}&end_date={{ $date }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn btn-sm btn-outline" style="padding: 4px 10px; font-size: 0.8rem; width: 100px;"><i class="fa-solid fa-print"></i> A4</a>
                                                <a href="/invoice/kitchen-report-print-thermal?type=range&start_date={{ $date }}&end_date={{ $date }}&kitchen={{ $kitchenSlug }}" target="_blank" class="btn btn-sm" style="background: #1565C0; color: #fff; border: none; padding: 4px 10px; font-size: 0.8rem; width: 100px;"><i class="fa-solid fa-receipt"></i> حراري</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-secondary);">لا توجد مبيعات في هذه الفترة.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="margin-top: 2rem; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">
                    <button wire:click="closeReportModal" class="btn btn-danger" style="padding: 10px 40px; font-size: 1.1rem; border-radius: 8px;"><i class="fa-solid fa-right-from-bracket"></i> إغلاق النافذة والعودة للمطبخ</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('new-order-arrived', () => { playSound('newOrder'); });
            Livewire.on('order-ready-sound', () => { playSound('success'); });
            Livewire.on('rush-order-alert', () => { playSound('urgent'); });
            Livewire.on('print-invoice', (event) => {
                let url = event.url;
                if (!url && typeof event === 'object') { url = event.detail?.url || event[0]?.url; }
                if (url) {
                    let iframe = document.getElementById('hidden-print-frame');
                    if (!iframe) {
                        iframe = document.createElement('iframe');
                        iframe.id = 'hidden-print-frame';
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                    }
                    iframe.src = url + '&t=' + new Date().getTime();
                }
            });
            if (!window.playedAlerts) window.playedAlerts = {};
            function checkAlerts() {
                document.querySelectorAll('[data-alert-count]').forEach(el => {
                    const orderId = el.getAttribute('data-order-id');
                    const count = parseInt(el.getAttribute('data-alert-count') || '0', 10);
                    if (window.playedAlerts[orderId] === undefined) { window.playedAlerts[orderId] = count; }
                    else if (count > window.playedAlerts[orderId]) { window.playedAlerts[orderId] = count; playUrgentAlert(5); }
                });
            }
            function playUrgentAlert(n) { if (n <= 0) return; playSound('urgent'); setTimeout(() => playUrgentAlert(n-1), 1200); }
            setInterval(checkAlerts, 1500);
            setTimeout(checkAlerts, 500);
        });
    </script>
    <style>
        @keyframes slideIn { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        .hide-prints .secret-print-target { display: none !important; }
    </style>
    <script>
        let secretClickCount = 0, secretClickResetTimer, hidePrintTimer;
        document.addEventListener('DOMContentLoaded', () => { document.body.classList.add('hide-prints'); });
        function handleSecretClick() {
            secretClickCount++;
            clearTimeout(secretClickResetTimer);
            secretClickResetTimer = setTimeout(() => { secretClickCount = 0; }, 1000);
            if (secretClickCount >= 3) {
                document.body.classList.remove('hide-prints');
                secretClickCount = 0;
                clearTimeout(hidePrintTimer);
                hidePrintTimer = setTimeout(() => { document.body.classList.add('hide-prints'); }, 30000);
            }
        }
    </script>
    @livewire('create-daily-purchase-request', ['kitchenSource' => $kitchenName])
</div>
