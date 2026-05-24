<div wire:poll.3s>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);"><i class="fa-solid fa-person-walking-arrow-right" style="color: var(--success-color);"></i> الطلبات الجاهزة</h2>
        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <span class="badge badge-ready">{{ $readyOrders->count() }} جاهز للتوصيل</span>
            <span style="color: var(--success-color); font-size: 0.8rem;"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> مباشر</span>
        </div>
    </div>

    <div class="card" style="border-top: 3px solid var(--success-color);">
        <h3 style="margin-bottom: 1rem; font-size: clamp(0.95rem, 3vw, 1.1rem);"><i class="fa-solid fa-truck-fast"></i> الطلبات الجاهزة للتوصيل</h3>
        
        <div class="grid grid-cols-3" style="gap: 1rem;">
            @forelse($readyOrders as $order)
                <div style="background: rgba(76, 175, 80, 0.05); border: 2px solid rgba(76, 175, 80, 0.3); padding: clamp(0.8rem, 2vw, 1rem); border-radius: var(--radius-md); text-align: center; animation: bounceIn 0.5s ease;">
                    <div style="font-size: clamp(1.6rem, 6vw, 2.2rem); font-weight: 900; color: var(--success-color); margin-bottom: 0.2rem;">
                        {{ $order->table->number }}
                        @if($order->table && $order->table->section)
                            <div style="font-size: clamp(0.8rem, 3vw, 1rem); font-weight: normal; color: var(--text-secondary); margin-top: -5px;">({{ $order->table->section }})</div>
                        @endif
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.8rem;">طاولة</div>
                    
                    <div style="background: rgba(255,255,255,0.04); padding: 0.8rem; border-radius: var(--radius-sm); margin-bottom: 0.8rem; text-align: center;">
                        <div style="font-size: clamp(1rem, 3vw, 1.2rem); font-weight: bold; margin-bottom: 0.3rem;">
                            {{ $order->quantity }}x {{ $order->shisha_type }}
                        </div>
                        @if($order->notes)
                            <div style="font-size: clamp(0.78rem, 2vw, 0.85rem); color: var(--primary-color); border-top: 1px solid var(--border-color); padding-top: 0.4rem; margin-top: 0.4rem;">
                                <i class="fa-solid fa-circle-info"></i> {{ $order->notes }}
                            </div>
                        @endif
                    </div>

                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.8rem;">
                        <i class="fa-regular fa-clock"></i> {{ $order->created_at->diffForHumans() }}
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button wire:click="markAsDelivered({{ $order->id }})" class="btn btn-success" style="flex: 1; font-size: clamp(0.9rem, 2.5vw, 1.1rem); padding: 0.8rem; min-height: 44px;">
                            <i class="fa-solid fa-check-double"></i> تم التسليم
                        </button>
                        <button wire:click="reprintReceipt({{ $order->id }})" class="btn btn-outline" style="width: 44px; min-width: 44px; height: 44px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="إعادة طباعة الفاتورة">
                            <i class="fa-solid fa-print"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-mug-hot fa-2x" style="margin-bottom: 0.8rem; opacity: 0.3;"></i><br>
                    لا توجد طلبات جاهزة للتوصيل حالياً
                </div>
            @endforelse
        </div>
    </div>
    
    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; margin-top: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif



    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('new-ready-order', () => { playSound('ready'); });
            Livewire.on('delivery-done-sound', () => { playSound('success'); });
        });
    </script>

    <style>
        @keyframes bounceIn {
            0% { opacity:0; transform:scale(0.9); }
            60% { transform:scale(1.02); }
            100% { opacity:1; transform:scale(1); }
        }
    </style>
</div>
