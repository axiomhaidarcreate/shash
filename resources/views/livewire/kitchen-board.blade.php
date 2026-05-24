<div wire:poll.3s>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);"><i class="fa-solid fa-fire" style="color: var(--warning-color);"></i> مطبخ الشيشة</h2>
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
                            <div style="background: linear-gradient(90deg, #d32f2f, #ff9800); color: #fff; padding: 6px 12px; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: bold; margin-bottom: 0.6rem; display: inline-flex; align-items: center; gap: 8px; animation: pulse 1s infinite; width: 100%; box-sizing: border-box; box-shadow: 0 0 10px rgba(244,67,54,0.3);">
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
                                طاولة {{ $order->table->number }} @if($order->table && $order->table->section)<span style="font-size: 0.7em; color: inherit;">({{ $order->table->section }})</span>@endif
                            </span>
                            <span style="color: {{ $isRushed ? 'var(--danger-color)' : 'var(--text-secondary)' }}; font-size: clamp(0.7rem, 2vw, 0.8rem); font-weight: {{ $isRushed ? 'bold' : 'normal' }};"><i class="fa-regular fa-clock"></i> {{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size: clamp(1.1rem, 3.5vw, 1.3rem); font-weight: bold; margin-bottom: 0.6rem;">
                            {{ $order->quantity }}x {{ $order->shisha_type }}
                        </div>
                        @if($order->notes)
                            <div style="background: rgba(255,255,255,0.04); padding: 0.5rem 0.7rem; border-radius: var(--radius-sm); margin-bottom: 0.6rem; border-right: 3px solid var(--primary-color); font-size: clamp(0.78rem, 2.5vw, 0.85rem); color: var(--text-secondary);">
                                <i class="fa-solid fa-circle-info" style="color: var(--primary-color);"></i> {{ $order->notes }}
                            </div>
                        @endif
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.6rem;">
                            <i class="fa-solid fa-user"></i> {{ $order->user->name }}
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button wire:click="startPrep({{ $order->id }})" class="btn btn-warning" style="flex: 1; padding: 0.8rem; font-size: clamp(0.9rem, 2.5vw, 1rem); min-height: 44px;">
                                <i class="fa-solid fa-play"></i> بدء التحضير
                            </button>
                            <button wire:click="reprintKitchen({{ $order->id }})" class="btn btn-outline" style="width: 44px; min-width: 44px; height: 44px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="إعادة طباعة كرت المطبخ">
                                <i class="fa-solid fa-print"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-mug-hot fa-2x" style="margin-bottom: 0.8rem; opacity: 0.3;"></i>
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
                            <div style="background: linear-gradient(90deg, #d32f2f, #ff9800); color: #fff; padding: 6px 12px; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: bold; margin-bottom: 0.6rem; display: inline-flex; align-items: center; gap: 8px; animation: pulse 1s infinite; width: 100%; box-sizing: border-box; box-shadow: 0 0 10px rgba(244,67,54,0.3);">
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
                            <span style="font-size: clamp(1.1rem, 3.5vw, 1.3rem); font-weight: 900; color: {{ $isRushed ? 'var(--danger-color)' : 'var(--info-color)' }};">
                                طاولة {{ $order->table->number }} @if($order->table && $order->table->section)<span style="font-size: 0.7em; color: inherit;">({{ $order->table->section }})</span>@endif
                            </span>
                            <span style="color: {{ $isRushed ? 'var(--danger-color)' : 'var(--text-secondary)' }}; font-size: clamp(0.7rem, 2vw, 0.8rem); font-weight: {{ $isRushed ? 'bold' : 'normal' }};"><i class="fa-regular fa-clock"></i> {{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size: clamp(1.1rem, 3.5vw, 1.3rem); font-weight: bold; margin-bottom: 0.6rem;">
                            {{ $order->quantity }}x {{ $order->shisha_type }}
                        </div>
                        @if($order->notes)
                            <div style="background: rgba(255,255,255,0.04); padding: 0.5rem 0.7rem; border-radius: var(--radius-sm); margin-bottom: 0.6rem; border-right: 3px solid var(--info-color); font-size: clamp(0.78rem, 2.5vw, 0.85rem); color: var(--text-secondary);">
                                <i class="fa-solid fa-circle-info" style="color: var(--info-color);"></i> {{ $order->notes }}
                            </div>
                        @endif
                        <div style="display: flex; gap: 8px;">
                            <button wire:click="markAsReady({{ $order->id }})" class="btn btn-success" style="flex: 1; padding: 0.8rem; font-size: clamp(0.9rem, 2.5vw, 1rem); min-height: 44px;">
                                <i class="fa-solid fa-check"></i> تم التحضير ✅
                            </button>
                            <button wire:click="reprintKitchen({{ $order->id }})" class="btn btn-outline" style="width: 44px; min-width: 44px; height: 44px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="إعادة طباعة كرت المطبخ">
                                <i class="fa-solid fa-print"></i>
                            </button>
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

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('new-order-arrived', () => { playSound('newOrder'); });
            Livewire.on('order-ready-sound', () => { playSound('success'); });
            Livewire.on('rush-order-alert', () => { playSound('error'); });

            if (!window.playedAlerts) {
                window.playedAlerts = {};
            }

            function checkAlerts() {
                const orderElements = document.querySelectorAll('[data-alert-count]');
                orderElements.forEach(el => {
                    const orderId = el.getAttribute('data-order-id');
                    const currentCount = parseInt(el.getAttribute('data-alert-count') || '0', 10);
                    
                    if (window.playedAlerts[orderId] === undefined) {
                        window.playedAlerts[orderId] = currentCount;
                    } else if (currentCount > window.playedAlerts[orderId]) {
                        window.playedAlerts[orderId] = currentCount;
                        playUrgentAlert(5);
                    }
                });
            }

            function playUrgentAlert(timesLeft) {
                if (timesLeft <= 0) return;
                playSound('error');
                setTimeout(() => {
                    playUrgentAlert(timesLeft - 1);
                }, 1200);
            }

            // Run check on livewire update and periodically
            setInterval(checkAlerts, 1500);
            setTimeout(checkAlerts, 500);
        });
    </script>

    <style>
        @keyframes slideIn { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
    </style>
</div>
