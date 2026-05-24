<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);">
            <i class="fa-solid fa-pen-to-square" style="color: var(--primary-color);"></i> 
            {{ $editingOrderId ? 'تعديل الطلب #' . $editingOrderId : 'إنشاء طلب جديد' }}
        </h2>
        @if($editingOrderId)
            <button wire:click="cancelEdit" class="btn btn-sm" style="background: #444; color: #fff;">
                <i class="fa-solid fa-xmark"></i> إلغاء التعديل
            </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div style="background: linear-gradient(135deg, rgba(76,175,80,0.15), rgba(76,175,80,0.05)); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; text-align: center; font-weight: bold; font-size: clamp(0.9rem, 2.5vw, 1.1rem); animation: fadeIn 0.4s ease; display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <div><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        </div>
    @endif

    @if (session()->has('error'))
        <div style="background: linear-gradient(135deg, rgba(244,67,54,0.15), rgba(244,67,54,0.05)); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 0.8rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; text-align: center; font-weight: bold; font-size: clamp(0.9rem, 2.5vw, 1.1rem); animation: fadeIn 0.4s ease; display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <div><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
        </div>
    @endif

    @if($delayedOrders->count() > 0)
        <div style="background: linear-gradient(135deg, rgba(244,67,54,0.15), rgba(244,67,54,0.05)); border: 2px solid var(--danger-color); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem; animation: pulseBorder 2s infinite;">
            <h3 style="color: var(--danger-color); font-size: 1.1rem; margin-top: 0; margin-bottom: 0.8rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation fa-bounce"></i> طلبيات متأخرة (مرت 10 دقائق أو أكثر)
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 0.8rem;">
                @foreach($delayedOrders as $order)
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(244,67,54,0.3); border-radius: var(--radius-sm); padding: 0.8rem; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                        <div>
                            <div style="font-weight: bold; font-size: 1rem; color: #fff;">
                                طاولة {{ $order->table->number }}
                                @if($order->table && $order->table->section)
                                    <span style="font-size: 0.8rem; color: var(--text-secondary);">({{ $order->table->section }})</span>
                                @endif
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 2px;">
                                {{ $order->quantity }}x {{ $order->shisha_type }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--danger-color); margin-top: 4px; font-weight: bold;">
                                <i class="fa-regular fa-clock"></i> مضى {{ $order->created_at->diffForHumans(null, true) }}
                            </div>
                        </div>
                        <button wire:click="triggerUrgentAlert({{ $order->id }})" class="btn" style="background: var(--danger-color); color: #fff; border: none; padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: var(--radius-sm); font-weight: bold; display: flex; align-items: center; gap: 4px; box-shadow: 0 0 10px rgba(244,67,54,0.4);">
                            <i class="fa-solid fa-bell"></i> استعجال 🚨
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Mobile: stack vertically. Desktop: side by side --}}
    <div class="layout-grid-reverse">

        <!-- Table Selection -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <h3 style="font-size: clamp(0.95rem, 3vw, 1.1rem); margin: 0;"><i class="fa-solid fa-border-all" style="color: var(--primary-color);"></i> 1. اختر الطاولة</h3>
                <input type="text" wire:model.live="tableSearch" class="form-control" placeholder="بحث..." style="max-width: 120px; padding: 0.4rem 0.8rem; font-size: 0.9rem;">
            </div>

            <!-- Lounge Sections Tabs -->
            @if($sections->count() > 0)
            <div style="display: flex; gap: 6px; margin-bottom: 1.2rem; overflow-x: auto; padding-bottom: 6px; -webkit-overflow-scrolling: touch; border-bottom: 1px solid var(--border-color); touch-action: pan-x;">
                <button wire:click="selectSection('')" 
                        class="btn btn-sm {{ $selectedSection === '' ? '' : 'btn-outline' }}" 
                        style="padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); font-weight: bold; white-space: nowrap; font-size: clamp(0.75rem, 2vw, 0.85rem); transition: all 0.2s ease;">
                    <i class="fa-solid fa-border-all"></i> الكل
                </button>
                @foreach($sections as $sec)
                    <button wire:click="selectSection('{{ $sec->name }}')" 
                            class="btn btn-sm {{ $selectedSection === $sec->name ? '' : 'btn-outline' }}" 
                            style="padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); font-weight: bold; white-space: nowrap; font-size: clamp(0.75rem, 2vw, 0.85rem); transition: all 0.2s ease;">
                        <i class="fa-solid fa-map-location-dot"></i> {{ $sec->name }}
                    </button>
                @endforeach
            </div>
            @endif

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 0.5rem;">
                @foreach($tables as $table)
                    @php
                        $borderColor = $selectedTable == $table->id ? 'var(--primary-color)' : 'var(--border-color)';
                        $bgColor = $selectedTable == $table->id ? 'rgba(212, 175, 55, 0.12)' : 'transparent';
                        $statusDot = match($table->status) {
                            'empty' => '#555',
                            'customer_seated' => 'var(--warning-color)',
                            'order_prep' => '#2196F3',
                            'order_ready' => 'var(--success-color)',
                            'delivered' => '#9e9e9e',
                        };
                        $isOccupied = $table->status !== 'empty' && !$editingOrderId;
                        $opacity = $isOccupied ? '0.5' : '1';
                        $cursor = $isOccupied ? 'not-allowed' : 'pointer';
                    @endphp
                    <div wire:click="selectTable({{ $table->id }})" 
                          style="border: 2px solid {{ $borderColor }}; 
                                 padding: 0.5rem 0.2rem; border-radius: var(--radius-sm); text-align: center; cursor: {{ $cursor }}; opacity: {{ $opacity }};
                                 background-color: {{ $bgColor }};
                                 transition: all 0.15s ease; position: relative; min-height: 55px;
                                 display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;
                                 touch-action: manipulation; -webkit-tap-highlight-color: transparent;">
                        <span style="position: absolute; top: 4px; left: 4px; width: 7px; height: 7px; border-radius: 50%; background: {{ $statusDot }};"></span>
                        <span style="font-size: clamp(1rem, 3vw, 1.3rem); font-weight: 800; line-height: 1;">{{ $table->number }}</span>
                        @if($table->section)
                        <span style="font-size: clamp(0.7rem, 2vw, 0.8rem); color: var(--text-secondary); margin-top: -2px;">{{ $table->section }}</span>
                        @endif
                        <span style="font-size: clamp(0.65rem, 2vw, 0.75rem); color: var(--text-secondary);"><i class="fa-solid fa-users"></i> {{ $table->capacity }}</span>
                    </div>
                @endforeach
            </div>
            @if($tables->isEmpty())
                <div style="text-align: center; color: var(--text-secondary); margin-top: 1rem; font-size: 0.9rem;">لا توجد طاولات في هذا القسم حالياً.</div>
            @endif
            @error('selectedTable') <span style="color: var(--danger-color); display: block; margin-top: 8px; font-size: 0.9rem;">{{ $message }}</span> @enderror
        </div>

        <!-- Order Details -->
        <div class="card">
            <h3 style="margin-bottom: 1rem; font-size: clamp(0.95rem, 3vw, 1.1rem);"><i class="fa-solid fa-clipboard-list" style="color: var(--primary-color);"></i> 2. تفاصيل الطلب</h3>
            
            @if($selectedTable)
                @if(!$editingOrderId)
                <div class="form-group" style="margin-bottom: 1.5rem; background: var(--bg-color); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                    <label class="form-label" style="display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-users" style="color: var(--primary-color);"></i> عدد الأشخاص (لتحديد الطلبات)</label>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <button wire:click="setGuestsCount({{ $guestsCount > 1 ? $guestsCount - 1 : 1 }})" class="btn btn-outline" style="width: 48px; height: 48px; padding: 0; font-size: 1.3rem;">−</button>
                        <span style="font-size: clamp(1.5rem, 5vw, 2rem); font-weight: 800; width: 40px; text-align: center; color: var(--primary-color);">{{ $guestsCount }}</span>
                        <button wire:click="setGuestsCount({{ $guestsCount + 1 }})" class="btn btn-outline" style="width: 48px; height: 48px; padding: 0; font-size: 1.3rem;">+</button>
                    </div>
                    <small style="color: var(--text-secondary); margin-top: 8px; display: block;">سيتم إنشاء قائمة اختيارات بعدد الأشخاص.</small>
                </div>
                @endif

                @foreach($orderItems as $index => $item)
                <div style="border: 1px solid var(--border-color); padding: 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1rem; background: var(--card-bg);">
                    <h4 style="margin-bottom: 1rem; color: var(--primary-color); font-size: 1.05rem;">
                        <i class="fa-solid fa-user"></i> الشخص رقم {{ $index + 1 }}
                    </h4>
                    
                    {{-- Products from Database --}}
                    @if($products->count() > 0)
                        @php $grouped = $products->groupBy('category'); @endphp
                        @foreach($grouped as $cat => $items)
                            <div class="form-group">
                                <label class="form-label">
                                    @if($cat === 'معسل') <i class="fa-solid fa-wind"></i>
                                    @elseif($cat === 'مشروبات') <i class="fa-solid fa-mug-hot"></i>
                                    @elseif($cat === 'مستلزمات') <i class="fa-solid fa-screwdriver-wrench"></i>
                                    @else <i class="fa-solid fa-cube"></i>
                                    @endif
                                    {{ $cat }}
                                </label>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                    @foreach($items as $product)
                                        <button wire:click="selectType({{ $index }}, '{{ $product->name }}', {{ $product->grams_per_order }})" 
                                                class="btn {{ $item['shishaType'] == $product->name ? '' : 'btn-outline' }}" 
                                                style="padding: 0.4rem 0.8rem; font-size: clamp(0.78rem, 2vw, 0.85rem);">
                                            {{ $product->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div style="background: rgba(255,152,0,0.08); border: 1px solid rgba(255,152,0,0.2); padding: 0.8rem; border-radius: var(--radius-sm); margin-bottom: 0.8rem; color: var(--warning-color); font-size: 0.85rem;">
                            <i class="fa-solid fa-triangle-exclamation"></i> لم يتم تفعيل أو إيجاد أي تصنيفات ومنتجات للعرض حالياً.
                        </div>
                    @endif

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">الكمية</label>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <button wire:click="decrementQuantity({{ $index }})" class="btn btn-outline" style="width: 40px; height: 40px; padding: 0; font-size: 1.1rem;">−</button>
                            <span style="font-size: 1.3rem; font-weight: 800; width: 30px; text-align: center; color: var(--primary-color);">{{ $item['quantity'] }}</span>
                            <button wire:click="incrementQuantity({{ $index }})" class="btn btn-outline" style="width: 40px; height: 40px; padding: 0; font-size: 1.1rem;">+</button>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label class="form-label"><i class="fa-solid fa-comment-dots"></i> ملاحظات إضافية</label>
                        <textarea wire:model="orderItems.{{ $index }}.notes" class="form-control" rows="1" placeholder="مثال: رأس إضافي، فحم خفيف..." style="resize: vertical;"></textarea>
                    </div>
                </div>
                @endforeach

                @if($editingOrderId)
                    <button wire:click="submitOrder" class="btn btn-success" style="width: 100%; padding: 0.9rem; font-size: clamp(0.95rem, 3vw, 1.15rem); margin-top: 0.5rem;" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submitOrder"><i class="fa-solid fa-check"></i> حفظ التعديلات</span>
                        <span wire:loading wire:target="submitOrder"><i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...</span>
                    </button>
                @else
                    <button wire:click="submitOrder" class="btn" style="width: 100%; padding: 0.9rem; font-size: clamp(0.95rem, 3vw, 1.15rem); margin-top: 1rem;" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submitOrder"><i class="fa-solid fa-paper-plane"></i> إرسال الطلبات للتحضير</span>
                        <span wire:loading wire:target="submitOrder"><i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...</span>
                    </button>
                @endif
            @else
                <div style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">
                    <i class="fa-solid fa-hand-pointer" style="font-size: 2rem; margin-bottom: 1rem; color: var(--border-color);"></i>
                    <p>يرجى اختيار الطاولة أولاً من القائمة اليمنى</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    @if($recentOrders->count() > 0)
    <div class="card" style="margin-top: 2rem;">
        <h3 style="margin-bottom: 1rem; font-size: clamp(0.95rem, 3vw, 1.1rem);"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary-color);"></i> طلباتي اليوم</h3>
        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="data-table" style="min-width: 550px;">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>الطاولة</th>
                        <th>الطلب</th>
                        <th>الحالة</th>
                        <th style="width: 180px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        @php
                            $statusLabel = match($order->status) {
                                'pending' => 'قيد الانتظار',
                                'prep' => 'جاري التحضير',
                                'ready' => 'جاهز',
                                'delivered' => 'تم التسليم',
                                'cancelled' => 'ملغي',
                                default => $order->status
                            };
                            $statusColor = match($order->status) {
                                'pending' => 'var(--warning-color)',
                                'prep' => 'var(--info-color)',
                                'ready' => 'var(--success-color)',
                                'delivered' => '#9e9e9e',
                                'cancelled' => 'var(--danger-color)',
                                default => '#fff'
                            };
                        @endphp
                        <tr wire:key="recent-order-{{ $order->id }}">
                            <td style="font-weight: bold;">#{{ $order->id }}</td>
                            <td>طاولة {{ $order->table->number ?? '-' }} @if($order->table && $order->table->section)<span style="font-size: 0.8rem; color: #777;">({{ $order->table->section }})</span>@endif</td>
                            <td>{{ $order->quantity }}x {{ $order->shisha_type }}</td>
                            <td>
                                @if($order->cancel_requested)
                                    <span style="color: var(--warning-color); font-size: 0.85rem; font-weight: bold;"><i class="fa-solid fa-hourglass-half"></i> بانتظار موافقة الإلغاء</span>
                                @else
                                    <span style="color: {{ $statusColor }}; font-weight: bold;">{{ $statusLabel }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    @if(in_array($order->status, ['pending', 'prep']) && !$order->cancel_requested)
                                        @if($order->status === 'pending')
                                            <button wire:click="editOrder({{ $order->id }})" class="btn btn-sm" style="background: #2196F3; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                                <i class="fa-solid fa-pen"></i> تعديل
                                            </button>
                                        @endif
                                        
                                        {{-- 10 Minute Rush Checking --}}
                                        @php
                                            $minutesElapsed = $order->created_at->diffInMinutes(now());
                                            $canRush = $minutesElapsed >= 10;
                                            $remainingMinutes = 10 - $minutesElapsed;
                                        @endphp
                                        @if($order->is_rushed)
                                            <button class="btn btn-sm" style="background: #555; color: #aaa; border: none; padding: 0.4rem 0.8rem; cursor: not-allowed;" disabled>
                                                <i class="fa-solid fa-bolt"></i> تم التسريع
                                            </button>
                                        @elseif($canRush)
                                            <button wire:click="rushOrder({{ $order->id }})" class="btn btn-sm" style="background: #FF9800; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                                <i class="fa-solid fa-bolt fa-beat"></i> تسريع
                                            </button>
                                        @else
                                            <button class="btn btn-sm" style="background: rgba(255,152,0,0.1); color: #FF9800; border: 1px solid rgba(255,152,0,0.25); padding: 0.4rem 0.8rem; cursor: not-allowed; font-size: 0.8rem; font-weight: bold;" disabled title="متاح بعد {{ $remainingMinutes }} دقيقة">
                                                <i class="fa-solid fa-hourglass-start"></i> تسريع ({{ $remainingMinutes }} د)
                                            </button>
                                        @endif
                                        
                                        <button wire:click="requestCancel({{ $order->id }})" class="btn btn-sm" style="background: #F44336; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من طلب إلغاء هذه الطلبية؟')">
                                            <i class="fa-solid fa-ban"></i> إلغاء
                                        </button>
                                    @elseif(!$order->cancel_requested)
                                        <span style="color: var(--text-secondary); font-size: 0.85rem;">لا يمكن التعديل</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('order-sent', () => { playSound('success'); });
            


            Livewire.on('print-invoice', (event) => {
                let url = event.url;
                if (!url && typeof event === 'object') {
                    url = event.detail?.url || event[0]?.url;
                }
                if (url) {
                    window.open(url, '_blank');
                }
            });
        });
    </script>

    <style>
        @keyframes fadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
        @keyframes pulseBorder {
            0% { border-color: rgba(244, 67, 54, 0.4); }
            50% { border-color: rgba(244, 67, 54, 1); box-shadow: 0 0 10px rgba(244, 67, 54, 0.15); }
            100% { border-color: rgba(244, 67, 54, 0.4); }
        }
    </style>
</div>
