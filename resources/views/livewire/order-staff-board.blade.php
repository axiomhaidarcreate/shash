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
                                @if($order->table)
                                    طاولة {{ $order->table->number }}
                                    @if($order->table->section)
                                        <span style="font-size: 0.8rem; color: var(--text-secondary);">({{ $order->table->section }})</span>
                                    @endif
                                @else
                                    <span style="color: #9C27B0;"><i class="fa-solid fa-mobile-screen"></i> طلب ذاتي</span>
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

        <!-- Table Selection & Order Mode -->
        <div class="card">
            {{-- Order Mode Toggle --}}
            <div style="display: flex; gap: 8px; margin-bottom: 1.5rem; background: rgba(0,0,0,0.05); padding: 6px; border-radius: var(--radius-sm);">
                <button wire:click="setOrderMode('ticket')" class="btn {{ $orderMode === 'ticket' ? '' : 'btn-ghost' }}" style="flex: 1; padding: 0.8rem; border-radius: var(--radius-sm); font-weight: bold; font-size: clamp(0.85rem, 2vw, 1rem); {{ $orderMode === 'ticket' ? 'background: var(--primary-color); color: #fff; box-shadow: 0 4px 10px rgba(212,175,55,0.3);' : 'color: var(--text-secondary);' }}">
                    <i class="fa-solid fa-ticket"></i> طلب حفلة (بتذكرة)
                </button>
                <button wire:click="setOrderMode('normal')" class="btn {{ $orderMode === 'normal' ? '' : 'btn-ghost' }}" style="flex: 1; padding: 0.8rem; border-radius: var(--radius-sm); font-weight: bold; font-size: clamp(0.85rem, 2vw, 1rem); {{ $orderMode === 'normal' ? 'background: #4CAF50; color: #fff; box-shadow: 0 4px 10px rgba(76,175,80,0.3);' : 'color: var(--text-secondary);' }}">
                    <i class="fa-solid fa-store"></i> طلب عادي (بدون تذكرة)
                </button>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <h3 style="font-size: clamp(0.95rem, 3vw, 1.1rem); margin: 0;"><i class="fa-solid fa-border-all" style="color: var(--primary-color);"></i> 1. اختر الطاولة</h3>
                <input type="text" wire:model.live="tableSearch" class="form-control" placeholder="بحث..." style="max-width: 120px; padding: 0.4rem 0.8rem; font-size: 0.9rem;">
            </div>

            <!-- Lounge Sections Tabs -->
            @if($sections->count() > 0)
            <div style="display: flex; gap: 6px; margin-bottom: 1.2rem; overflow-x: auto; padding-bottom: 6px; -webkit-overflow-scrolling: touch; border-bottom: 1px solid var(--border-color); touch-action: pan-x;">
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
                        $isReserved = $table->eventBookings->isNotEmpty();
                        $reservedFor = $isReserved ? $table->eventBookings->first()->customer_name : null;

                        $borderColor = $selectedTable == $table->id ? 'var(--primary-color)' : 'var(--border-color)';
                        
                        if ($isReserved) {
                            $bgColor = $selectedTable == $table->id ? 'rgba(244, 67, 54, 0.2)' : 'rgba(244, 67, 54, 0.05)';
                            $borderColor = $selectedTable == $table->id ? 'var(--primary-color)' : 'var(--danger-color)';
                        } else {
                            $bgColor = $selectedTable == $table->id ? 'rgba(212, 175, 55, 0.12)' : 'transparent';
                        }

                        $statusDot = match($table->status) {
                            'empty' => '#555',
                            'customer_seated' => 'var(--warning-color)',
                            'order_prep' => '#2196F3',
                            'order_ready' => 'var(--success-color)',
                            'delivered' => '#9e9e9e',
                        };
                        if ($isReserved && $table->status == 'empty') {
                            $statusDot = 'var(--danger-color)';
                        }

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
                        <span style="position: absolute; top: 4px; right: 4px; width: 7px; height: 7px; border-radius: 50%; background: {{ $statusDot }};"></span>
                        @if($isReserved)
                            <span style="position: absolute; top: 2px; left: 4px; font-size: 0.7rem; color: var(--danger-color);" title="محجوزة"><i class="fa-solid fa-lock"></i></span>
                        @endif
                        <span style="font-size: clamp(1rem, 3vw, 1.3rem); font-weight: 800; line-height: 1;">{{ $table->number }}</span>
                        @if($isReserved)
                            <span style="font-size: 0.6rem; color: var(--danger-color); font-weight: bold; margin-top: -2px; width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $reservedFor }}</span>
                        @elseif($table->section)
                            <span style="font-size: clamp(0.7rem, 2vw, 0.8rem); color: var(--text-secondary); margin-top: -2px;">{{ $table->section }}</span>
                        @endif
                        <span style="font-size: clamp(0.65rem, 2vw, 0.75rem); color: var(--text-secondary);"><i class="fa-solid fa-users"></i> {{ $table->capacity }}</span>
                    </div>
                @endforeach
            </div>
            @if($selectedSection === null)
                <div style="text-align: center; color: var(--warning-color); margin-top: 1.5rem; font-size: 1rem; font-weight: bold; border: 1px dashed var(--warning-color); padding: 1rem; border-radius: var(--radius-sm); background: rgba(255, 152, 0, 0.05);">
                    <i class="fa-solid fa-hand-point-up fa-bounce" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block;"></i>
                    يرجى تحديد الصالة أولاً من الأزرار العلوية لعرض الطاولات.
                </div>
            @elseif($tables->isEmpty())
                <div style="text-align: center; color: var(--text-secondary); margin-top: 1rem; font-size: 0.9rem;">لا توجد طاولات في هذا القسم حالياً.</div>
            @endif
            @error('selectedTable') <span style="color: var(--danger-color); display: block; margin-top: 8px; font-size: 0.9rem; text-align: center; font-weight: bold;">{{ $message }}</span> @enderror
        </div>

        <!-- Order Details or Chair Selection -->
        <div class="card">
            @if($selectedTable && !$selectedChair)
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                    <h3 style="font-size: clamp(0.95rem, 3vw, 1.1rem); margin: 0;"><i class="fa-solid fa-chair" style="color: var(--primary-color);"></i> 2. اختر الكرسي لطاولة {{ \App\Models\Table::find($selectedTable)?->number }}</h3>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 0.5rem;">
                    @foreach(\App\Models\Table::find($selectedTable)->chairs->sortBy('sort_order') as $chair)
                        @php
                            $isOccupied = $chair->status !== 'empty' && !$editingOrderId;
                            $borderColor = $selectedChair == $chair->id ? 'var(--primary-color)' : 'var(--border-color)';
                            $bgColor = $selectedChair == $chair->id ? 'rgba(212, 175, 55, 0.12)' : 'transparent';
                            $opacity = $isOccupied ? '0.5' : '1';
                            $cursor = $isOccupied ? 'not-allowed' : 'pointer';
                            $statusDot = $isOccupied ? 'var(--warning-color)' : '#555';
                        @endphp
                        <div wire:click="selectChair({{ $chair->id }})" 
                              style="border: 2px solid {{ $borderColor }}; 
                                     padding: 0.5rem 0.2rem; border-radius: var(--radius-sm); text-align: center; cursor: {{ $cursor }}; opacity: {{ $opacity }};
                                     background-color: {{ $bgColor }};
                                     transition: all 0.15s ease; position: relative; min-height: 55px;
                                     display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;">
                            <span style="position: absolute; top: 4px; right: 4px; width: 7px; height: 7px; border-radius: 50%; background: {{ $statusDot }};"></span>
                            <span style="font-size: clamp(1rem, 3vw, 1.3rem); font-weight: 800; line-height: 1;">{{ $chair->number }}</span>
                            <span style="font-size: clamp(0.65rem, 2vw, 0.75rem); color: var(--text-secondary);"><i class="fa-solid fa-chair"></i> كرسي</span>
                        </div>
                    @endforeach
                </div>
                @error('selectedChair') <span style="color: var(--danger-color); display: block; margin-top: 8px; font-size: 0.9rem; text-align: center; font-weight: bold;">{{ $message }}</span> @enderror
            @endif

            @if($selectedTable && $selectedChair)
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <h3 style="margin: 0; font-size: clamp(0.95rem, 3vw, 1.1rem);"><i class="fa-solid fa-clipboard-list" style="color: var(--primary-color);"></i> 3. تفاصيل الطلب</h3>
                    <button wire:click="backToChairs" class="btn btn-sm btn-outline" style="font-size: 0.8rem; padding: 2px 8px;"><i class="fa-solid fa-arrow-right"></i> رجوع للكراسي</button>
                </div>
                
                {{-- Progress Banner --}}
                @if($submittedTicketsCount > 0 && $submittedTicketsCount < $guestsCount)
                    <div style="background: linear-gradient(135deg, rgba(33,150,243,0.15), rgba(33,150,243,0.05)); border: 1px solid #2196F3; border-radius: var(--radius-sm); padding: 0.8rem 1rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; animation: fadeIn 0.4s ease;">
                        <div>
                            <i class="fa-solid fa-spinner fa-pulse" style="color: #2196F3;"></i>
                            <span style="color: #2196F3; font-weight: bold;">جاري تقديم الطلبات...</span>
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">الشخص {{ $submittedTicketsCount + 1 }} من {{ $guestsCount }}</span>
                        </div>
                        <div style="display: flex; gap: 4px;">
                            @for($i = 0; $i < $guestsCount; $i++)
                                <div style="width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold;
                                    {{ $i < $submittedTicketsCount ? 'background: var(--success-color); color: #fff;' : ($i == $submittedTicketsCount ? 'background: #2196F3; color: #fff; animation: pulse 1.5s infinite;' : 'background: rgba(255,255,255,0.1); color: var(--text-secondary); border: 1px solid var(--border-color);') }}">
                                    @if($i < $submittedTicketsCount)
                                        <i class="fa-solid fa-check" style="font-size: 0.6rem;"></i>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif

                @if(!$editingOrderId && $submittedTicketsCount == 0)
                <div class="form-group" style="margin-bottom: 1.5rem; background: var(--bg-color); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                    <label class="form-label" style="display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-users" style="color: var(--primary-color);"></i> عدد الأشخاص (عدد التذاكر المطلوبة)</label>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <button wire:click="setGuestsCount({{ $guestsCount > 1 ? $guestsCount - 1 : 1 }})" class="btn btn-outline" style="width: 48px; height: 48px; padding: 0; font-size: 1.3rem;">−</button>
                        <span style="font-size: clamp(1.5rem, 5vw, 2rem); font-weight: 800; width: 40px; text-align: center; color: var(--primary-color);">{{ $guestsCount }}</span>
                        <button wire:click="setGuestsCount({{ $guestsCount + 1 }})" class="btn btn-outline" style="width: 48px; height: 48px; padding: 0; font-size: 1.3rem;">+</button>
                    </div>
                    @if($guestsCount > 1)
                        <div style="margin-top: 0.5rem; font-size: 0.82rem; color: var(--text-secondary);">
                            <i class="fa-solid fa-circle-info"></i> ستقوم بإرسال {{ $guestsCount }} طلبات بتذاكر مختلفة لنفس الطاولة، واحداً تلو الآخر.
                        </div>
                    @endif
                </div>
                @elseif($submittedTicketsCount > 0)
                    <div style="background: rgba(212,175,55,0.08); border: 1px solid var(--primary-color); border-radius: var(--radius-sm); padding: 0.6rem 1rem; margin-bottom: 1rem; font-size: 0.9rem; color: var(--primary-color); font-weight: bold;">
                        <i class="fa-solid fa-users"></i> الطاولة: {{ \App\Models\Table::find($selectedTable)?->number }} — الأشخاص: {{ $guestsCount }} — تم إرسال: {{ $submittedTicketsCount }} — متبقي: {{ $guestsCount - $submittedTicketsCount }}
                    </div>
                @endif

                {{-- ===== Selected Items Summary Table (above product list) ===== --}}
                @if(count($orderItems) > 0)
                    <div style="border: 1px solid var(--primary-color); border-radius: var(--radius-md); margin-bottom: 1.2rem; overflow: hidden; animation: fadeIn 0.3s ease;">
                        <div style="background: linear-gradient(135deg, rgba(212,175,55,0.15), rgba(212,175,55,0.05)); padding: 0.7rem 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                            <span style="font-weight: bold; color: var(--primary-color); font-size: 0.95rem;">
                                <i class="fa-solid fa-cart-shopping"></i> الأصناف المختارة ({{ count($orderItems) }})
                            </span>
                            @if($orderMode === 'ticket' && $resolvedTicket)
                                @php
                                    $currentTotal = $this->getTotalItemsCount();
                                    $maxAllowed = $this->getMaxAllowedItems();
                                    $isOverLimit = $currentTotal > $maxAllowed;
                                    $limitColor = $isOverLimit ? 'var(--danger-color)' : 'var(--success-color)';
                                @endphp
                                <span style="font-size: 0.85rem; font-weight: bold; color: {{ $limitColor }}; background: rgba(0,0,0,0.1); padding: 2px 8px; border-radius: 12px;">
                                    العدد: {{ $currentTotal }} / المسموح: {{ $maxAllowed }}
                                </span>
                            @endif
                        </div>
                        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                <thead>
                                    <tr style="background: rgba(255,255,255,0.03);">
                                        <th style="padding: 0.5rem 0.8rem; text-align: right; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color);">الصنف</th>
                                        <th style="padding: 0.5rem 0.6rem; text-align: center; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color);">المطبخ</th>
                                        <th style="padding: 0.5rem 0.6rem; text-align: center; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color);">العدد</th>
                                        <th style="padding: 0.5rem 0.6rem; text-align: center; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color);">السعر</th>
                                        <th style="padding: 0.5rem 0.6rem; text-align: center; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color);">الإجمالي</th>
                                        <th style="padding: 0.5rem 0.4rem; text-align: center; border-bottom: 1px solid var(--border-color);"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @foreach($orderItems as $idx => $item)
                                        @php 
                                            $lineTotal = $item['price'] * $item['quantity'];
                                            $grandTotal += $lineTotal;
                                            $kitchenLabel = match($item['targetKitchen']) {
                                                'shisha' => 'شيشة',
                                                'kitchen' => 'مطبخ',
                                                'bar' => 'بار',
                                                'grill' => 'مشويات',
                                                default => $item['targetKitchen'],
                                            };
                                            $kitchenColor = match($item['targetKitchen']) {
                                                'shisha' => '#D4AF37',
                                                'kitchen' => '#4CAF50',
                                                'bar' => '#2196F3',
                                                'grill' => '#FF5722',
                                                default => '#9e9e9e',
                                            };
                                        @endphp
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                            <td style="padding: 0.6rem 0.8rem; font-weight: bold; color: #fff;">
                                                {{ $item['name'] }}
                                                @if(!empty($item['notes']))
                                                    <div style="font-size: 0.75rem; color: #E040FB; font-weight: 600; margin-top: 2px;">
                                                        <i class="fa-solid fa-tags"></i> {{ $item['notes'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td style="padding: 0.6rem 0.4rem; text-align: center;">
                                                <span style="background: {{ $kitchenColor }}22; color: {{ $kitchenColor }}; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: bold; border: 1px solid {{ $kitchenColor }}44;">
                                                    {{ $kitchenLabel }}
                                                </span>
                                            </td>
                                            <td style="padding: 0.6rem 0.4rem; text-align: center;">
                                                <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                    <button wire:click="decrementProductQty({{ $idx }})" class="btn" style="width: 24px; height: 24px; padding: 0; font-size: 0.8rem; background: rgba(244,67,54,0.15); color: var(--danger-color); border: 1px solid rgba(244,67,54,0.3); border-radius: 4px; line-height: 1;">−</button>
                                                    <span style="font-weight: 800; color: var(--primary-color); min-width: 18px;">{{ $item['quantity'] }}</span>
                                                    <button wire:click="incrementProductQty({{ $idx }})" class="btn" style="width: 24px; height: 24px; padding: 0; font-size: 0.8rem; background: rgba(76,175,80,0.15); color: var(--success-color); border: 1px solid rgba(76,175,80,0.3); border-radius: 4px; line-height: 1;">+</button>
                                                </div>
                                            </td>
                                            <td style="padding: 0.6rem 0.4rem; text-align: center; color: var(--text-secondary);">
                                                {{ number_format($item['price'], 0) }}
                                            </td>
                                            <td style="padding: 0.6rem 0.4rem; text-align: center; font-weight: bold; color: var(--success-color);">
                                                {{ number_format($lineTotal, 0) }}
                                            </td>
                                            <td style="padding: 0.6rem 0.4rem; text-align: center;">
                                                <button wire:click="removeProduct({{ $idx }})" style="background: none; border: none; color: var(--danger-color); cursor: pointer; font-size: 0.9rem; padding: 2px;">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background: rgba(212,175,55,0.08);">
                                        <td colspan="4" style="padding: 0.7rem 0.8rem; text-align: left; font-weight: bold; color: var(--primary-color); font-size: 0.95rem;">
                                            <i class="fa-solid fa-coins"></i> الإجمالي
                                        </td>
                                        <td style="padding: 0.7rem 0.4rem; text-align: center; font-weight: 900; color: var(--primary-color); font-size: 1.1rem;">
                                            {{ number_format($grandTotal, 0) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        {{-- Notes for items --}}
                        <div style="padding: 0.6rem 1rem; border-top: 1px solid var(--border-color);">
                            <label class="form-label" style="font-size: 0.82rem; margin-bottom: 0.3rem;"><i class="fa-solid fa-comment-dots"></i> ملاحظات إضافية</label>
                            @foreach($orderItems as $idx => $item)
                                @if(count($orderItems) > 1)
                                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.3rem;">{{ $item['name'] }}:</div>
                                @endif
                                <textarea wire:model="orderItems.{{ $idx }}.notes" class="form-control" rows="1" placeholder="مثال: رأس إضافي، فحم خفيف..." style="resize: vertical; font-size: 0.85rem; margin-bottom: 0.3rem;"></textarea>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ===== Product Selection (Single List) ===== --}}
                <div style="border: 1px solid var(--border-color); padding: 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1rem; background: var(--card-bg);">
                    <h4 style="margin-bottom: 1rem; color: var(--primary-color); font-size: 1.05rem;">
                        <i class="fa-solid fa-box-open"></i> اختر الأصناف
                    </h4>
                    
                    @if($products->count() > 0)
                        @php $grouped = $products->groupBy('category'); @endphp
                        
                        @if(!$selectedCategory)
                            {{-- Step 1: Show Categories --}}
                            <div class="form-group">
                                <label class="form-label" style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.8rem;">
                                    <i class="fa-solid fa-layer-group" style="color: var(--primary-color);"></i> اختر التصنيف
                                </label>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px;">
                                    @foreach($grouped as $cat => $items)
                                        @php
                                            $displayCatName = in_array($cat, ['معسل', 'الشيش', 'الشيشة']) ? 'الشيشة' : $cat;
                                            $catIcon = match($cat) {
                                                'الشيشة', 'معسل', 'الشيش' => 'fa-solid fa-wind',
                                                'رؤوس الشيشة', 'رأس شيشة' => 'fa-solid fa-fire-burner',
                                                'طبقات إضافية' => 'fa-solid fa-layer-group',
                                                'مشروبات' => 'fa-solid fa-mug-hot',
                                                'مستلزمات' => 'fa-solid fa-screwdriver-wrench',
                                                default => 'fa-solid fa-cube',
                                            };
                                            $catColor = match($cat) {
                                                'الشيشة', 'معسل', 'الشيش' => 'linear-gradient(135deg, rgba(212,175,55,0.15), rgba(212,175,55,0.05))',
                                                'رؤوس الشيشة', 'رأس شيشة' => 'linear-gradient(135deg, rgba(255,152,0,0.15), rgba(255,152,0,0.05))',
                                                'طبقات إضافية' => 'linear-gradient(135deg, rgba(233,30,99,0.15), rgba(233,30,99,0.05))',
                                                'مشروبات' => 'linear-gradient(135deg, rgba(33,150,243,0.15), rgba(33,150,243,0.05))',
                                                'مستلزمات' => 'linear-gradient(135deg, rgba(156,39,176,0.15), rgba(156,39,176,0.05))',
                                                default => 'linear-gradient(135deg, rgba(158,158,158,0.15), rgba(158,158,158,0.05))',
                                            };
                                            $catBorder = match($cat) {
                                                'الشيشة', 'معسل', 'الشيش' => 'var(--primary-color)',
                                                'رؤوس الشيشة', 'رأس شيشة' => '#FF9800',
                                                'طبقات إضافية' => '#E91E63',
                                                'مشروبات' => '#2196F3',
                                                'مستلزمات' => '#9C27B0',
                                                default => 'var(--border-color)',
                                            };
                                        @endphp
                                        <button wire:click="selectCategory('{{ $cat }}')" 
                                                class="btn btn-outline"
                                                style="padding: 1.2rem 0.8rem; border-radius: var(--radius-md); font-weight: bold; width: 100%; 
                                                       display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; 
                                                       text-align: center; white-space: normal; min-height: 90px;
                                                       background: {{ $catColor }}; border-color: {{ $catBorder }};
                                                       transition: all 0.25s ease; position: relative;">
                                            <i class="{{ $catIcon }}" style="font-size: 1.5rem; color: {{ $catBorder }};"></i>
                                            <span style="font-size: clamp(0.85rem, 2.5vw, 1rem);">{{ $displayCatName }}</span>
                                            <span style="font-size: 0.7rem; color: var(--text-secondary);">{{ $items->count() }} صنف</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{-- Step 2: Show Products within selected category --}}
                            @php 
                                $selectedCat = $selectedCategory; 
                                $displayCatName = in_array($selectedCat, ['معسل', 'الشيش', 'الشيشة']) ? 'الشيشة' : $selectedCat;
                                $selectedIcon = match($selectedCat) {
                                    'الشيشة', 'معسل', 'الشيش' => 'fa-solid fa-wind',
                                    'رؤوس الشيشة', 'رأس شيشة' => 'fa-solid fa-fire-burner',
                                    'طبقات إضافية' => 'fa-solid fa-layer-group',
                                    'مشروبات' => 'fa-solid fa-mug-hot',
                                    'مستلزمات' => 'fa-solid fa-screwdriver-wrench',
                                    default => 'fa-solid fa-cube',
                                };
                                $selectedIconColor = match($selectedCat) {
                                    'الشيشة', 'معسل', 'الشيش' => 'var(--primary-color)',
                                    'رؤوس الشيشة', 'رأس شيشة' => '#FF9800',
                                    'طبقات إضافية' => '#E91E63',
                                    'مشروبات' => '#2196F3',
                                    'مستلزمات' => '#9C27B0',
                                    default => 'var(--text-secondary)',
                                };
                            @endphp
                            <div class="form-group">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.8rem; flex-wrap: wrap; gap: 6px;">
                                    <label class="form-label" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                                        <i class="{{ $selectedIcon }}" style="color: {{ $selectedIconColor }};"></i>
                                        اختر من: <strong>{{ $displayCatName }}</strong>
                                    </label>
                                    <button wire:click="backToCategories" 
                                            class="btn btn-sm btn-outline" 
                                            style="padding: 0.35rem 0.8rem; font-size: 0.8rem; border-radius: var(--radius-sm); display: flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-arrow-right"></i> رجوع للتصنيفات
                                    </button>
                                </div>
                                
                                @if(isset($grouped[$selectedCat]))
                                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 8px; animation: fadeIn 0.3s ease;">
                                        @foreach($grouped[$selectedCat] as $product)
                                            @php
                                                $isShishaOrMoassel = in_array($selectedCat, ['معسل', 'الشيشة', 'الشيش']);
                                                $isAlreadyAdded = collect($orderItems)->contains('name', $product->name);
                                            @endphp
                                            <div style="display: flex; gap: 4px; border-radius: var(--radius-sm); overflow: hidden; width: 100%;">
                                                <button wire:click="addProduct('{{ $product->name }}', {{ $product->grams_per_order }}, false)" 
                                                        class="btn {{ $isAlreadyAdded ? '' : 'btn-outline' }}" 
                                                        style="flex: 2; padding: 0.5rem; font-size: clamp(0.85rem, 2vw, 0.95rem); border-radius: 0; font-weight: bold; min-height: 50px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; white-space: normal;">
                                                    @if($product->image)
                                                        <img src="{{ route('file.serve', ['path' => $product->image]) }}" style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">
                                                    @else
                                                        <div style="width: 40px; height: 40px; border-radius: 6px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center;">
                                                            <i class="fa-solid fa-box" style="color: #555; font-size: 1.2rem;"></i>
                                                        </div>
                                                    @endif
                                                    <span>{{ $product->name }}</span>
                                                    @if($product->price > 0)
                                                        <span style="font-size: 0.7rem; color: var(--success-color);">{{ number_format($product->price, 0) }} ر.ي</span>
                                                    @endif
                                                </button>
                                                @if($isShishaOrMoassel)
                                                    @php
                                                        $isHeadAdded = collect($orderItems)->contains('name', $product->name . ' (رأس فقط)');
                                                    @endphp
                                                    <button wire:click="addProduct('{{ $product->name }}', 20, true)" 
                                                            class="btn {{ $isHeadAdded ? '' : 'btn-outline' }}" 
                                                            style="flex: 1.1; padding: 0.8rem 0.3rem; font-size: 0.78rem; border-radius: 0; font-weight: bold; min-height: 50px; text-align: center; display: flex; align-items: center; justify-content: center; white-space: normal; border-right: none; {{ !$isHeadAdded ? 'color: #ff9800; border-color: rgba(255,152,0,0.3); background: rgba(255,152,0,0.05);' : '' }}">
                                                        رأس فقط
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div style="text-align: center; color: var(--text-secondary); padding: 1rem;">لا توجد أصناف في هذا التصنيف.</div>
                                @endif
                            </div>
                        @endif
                    @else
                        <div style="background: rgba(255,152,0,0.08); border: 1px solid rgba(255,152,0,0.2); padding: 0.8rem; border-radius: var(--radius-sm); margin-bottom: 0.8rem; color: var(--warning-color); font-size: 0.85rem;">
                            <i class="fa-solid fa-triangle-exclamation"></i> لم يتم تفعيل أو إيجاد أي تصنيفات ومنتجات للعرض حالياً.
                        </div>
                    @endif
                </div>

                @if($editingOrderId)
                    <button wire:click="submitOrder" class="btn btn-success" style="width: 100%; padding: 0.9rem; font-size: clamp(0.95rem, 3vw, 1.15rem); margin-top: 0.5rem;" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submitOrder"><i class="fa-solid fa-check"></i> حفظ التعديلات</span>
                        <span wire:loading wire:target="submitOrder"><i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...</span>
                    </button>
                @else
                    <div style="background: rgba(212, 175, 55, 0.08); border: 1px solid var(--primary-color); padding: 1.2rem; border-radius: var(--radius-md); margin-top: 1rem;">
                        <h4 style="margin-bottom: 0.8rem; color: var(--primary-color); font-size: 1.05rem;">
                            <i class="fa-solid fa-file-invoice-dollar"></i> الفاتورة وتأكيد الطلب
                            @if($guestsCount > 1)
                                <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: normal;">— الشخص {{ $submittedTicketsCount + 1 }} من {{ $guestsCount }}</span>
                            @endif
                        </h4>

                        {{-- ===== Reception Ticket Section ===== --}}
                        @if($orderMode === 'ticket')
                            <div style="background: rgba(156,39,176,0.07); border: 1px solid rgba(156,39,176,0.35); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                                <label class="form-label" style="color: #9C27B0; font-weight: 700; display: flex; align-items: center; gap: 6px; margin-bottom: 0.6rem;">
                                    <i class="fa-solid fa-ticket"></i> تذكرة الشخص {{ $submittedTicketsCount + 1 }}
                                    <span style="font-size: 0.75rem; font-weight: 400; color: var(--text-secondary);">(مطلوبة — امسح أو أدخل الباركود)</span>
                                </label>

                                @if($resolvedTicket)
                                    {{-- Ticket Found & Valid --}}
                                    <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); border-radius: var(--radius-sm); padding: 0.8rem 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                        <div>
                                            <div style="font-weight: bold; color: var(--success-color); font-size: 0.95rem; margin-bottom: 2px;">
                                                <i class="fa-solid fa-circle-check"></i> تذكرة نشطة: 
                                                <span style="font-family: monospace; letter-spacing: 1px;">{{ $resolvedTicket['barcode'] }}</span>
                                            </div>
                                            <div style="font-size: 0.82rem; color: var(--text-secondary);">
                                                @if($resolvedTicket['guest_name'])
                                                    <i class="fa-solid fa-user"></i> {{ $resolvedTicket['guest_name'] }} &nbsp;|&nbsp;
                                                @endif
                                                <i class="fa-solid fa-users"></i> {{ $resolvedTicket['num_people'] }} شخص
                                            </div>
                                        </div>
                                        <button wire:click="clearTicket" class="btn btn-sm" style="background: rgba(244,67,54,0.15); color: var(--danger-color); border: 1px solid rgba(244,67,54,0.3); padding: 0.3rem 0.7rem;">
                                            <i class="fa-solid fa-xmark"></i> إلغاء الربط
                                        </button>
                                    </div>
                                @else
                                    <div style="display: flex; gap: 8px; align-items: flex-start; flex-wrap: wrap;">
                                        <div style="flex: 1; min-width: 200px;">
                                            <input type="text" id="ticketBarcodeField" wire:model="ticketBarcode" wire:keydown.enter="lookupTicket"
                                                   class="form-control" placeholder="مثال: 0001"
                                                   style="font-family: monospace; letter-spacing: 1px; text-transform: uppercase;">
                                            @error('ticketBarcode')
                                                <span style="color: var(--danger-color); font-size: 0.8rem; margin-top: 4px; display: block;">
                                                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                        <button type="button" onclick="startCameraScanner()" class="btn" style="background: rgba(156,39,176,0.15); color: #9C27B0; border: 1px solid #9C27B0; padding: 0.6rem 0.8rem;">
                                            <i class="fa-solid fa-camera"></i> تصوير
                                        </button>
                                        <button wire:click="lookupTicket" class="btn" style="background: #9C27B0; color: #fff; border: none; padding: 0.6rem 1rem; white-space: nowrap;">
                                            <i class="fa-solid fa-magnifying-glass"></i> تحقق
                                        </button>
                                    </div>

                                    {{-- Camera Scanner Modal --}}
                                    <div id="scannerModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; justify-content: center; align-items: center; padding: 1rem;">
                                        <div style="background: var(--surface-color); padding: 1.5rem; border-radius: var(--radius-md); width: 100%; max-width: 500px; text-align: center; position: relative;">
                                            <button type="button" onclick="stopCameraScanner()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: var(--danger-color); font-size: 1.5rem; cursor: pointer;">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                            <h3 style="margin-bottom: 1rem; color: #9C27B0;"><i class="fa-solid fa-camera"></i> وجّه الكاميرا نحو الباركود</h3>
                                            <div id="reader" style="width: 100%; background: #000; border-radius: 8px; overflow: hidden; min-height: 250px;"></div>
                                        </div>
                                    </div>

                                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 6px;">
                                        <i class="fa-solid fa-circle-info"></i> عند ربط تذكرة، سيتم تلقائياً تعبئة اسم الضيف وتحديد التذكرة كـ "مستخدمة".
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- ===== Loyalty Customer Section (Normal Mode) ===== --}}
                            <div style="background: rgba(255,152,0,0.05); border: 1px solid rgba(255,152,0,0.35); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                                <label class="form-label" style="color: #FF9800; font-weight: 700; display: flex; align-items: center; gap: 6px; margin-bottom: 0.6rem;">
                                    <i class="fa-solid fa-star"></i> ربط بعميل ولاء
                                    <span style="font-size: 0.75rem; font-weight: 400; color: var(--text-secondary);">(اختياري — لإضافة النقاط)</span>
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
                                                    | +{{ $linkedCustomer->loyaltyOffer->points_per_order }} نقطة للطلب
                                                @endif
                                            </span>
                                        </div>
                                        <button wire:click="clearLoyaltyCustomer" class="btn btn-sm" style="background: rgba(244,67,54,0.2); color: var(--danger-color); border: none; padding: 0.3rem 0.6rem;">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
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
                        @endif
                        {{-- ===== End Sections ===== --}}

                        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 1.2rem;">
                            <button wire:click="submitOrder" class="btn" style="width: 100%; padding: 0.9rem; font-size: clamp(0.95rem, 3vw, 1.15rem);" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitOrder">
                                    <i class="fa-solid fa-paper-plane"></i> 
                                    @if($guestsCount > 1)
                                        إرسال طلب الشخص {{ $submittedTicketsCount + 1 }} من {{ $guestsCount }}
                                    @else
                                        إرسال الطلب للمطبخ
                                    @endif
                                </span>
                                <span wire:loading wire:target="submitOrder"><i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...</span>
                            </button>

                            <div style="text-align: center; color: var(--text-secondary); margin: 0.5rem 0;">أو للطلبات الخاصة (بدون كاشير)</div>

                            <div style="background: rgba(255, 152, 0, 0.05); border: 1px dashed rgba(255, 152, 0, 0.5); padding: 1rem; border-radius: var(--radius-sm);">
                                <label class="form-label" style="display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-user-tag" style="color: var(--primary-color);"></i> اسم الضيف <span style="color: var(--danger-color); font-size: 0.8rem;">(مطلوب لطلبات الضيافة)</span></label>
                                <input type="text" wire:model="guestName" class="form-control" placeholder="أدخل اسم الضيف أولاً ثم اطلب الموافقة" style="margin-bottom: 0.5rem; border: 1px solid var(--border-color);">
                                @error('guestName') <span style="color: var(--danger-color); font-size: 0.85rem; margin-bottom: 0.5rem; display: block;">{{ $message }}</span> @enderror

                                <button wire:click="submitOrderWithApproval" class="btn btn-outline" style="width: 100%; padding: 0.9rem; font-size: clamp(0.95rem, 3vw, 1.15rem); border-color: var(--warning-color); color: var(--warning-color);" wire:loading.attr="disabled" onclick="return confirm('هل أنت متأكد من إرسال هذا الطلب بانتظار موافقة المدير؟ لن يتم تحضيره حتى تتم الموافقة عليه.')">
                                    <span wire:loading.remove wire:target="submitOrderWithApproval"><i class="fa-solid fa-user-tie"></i> طلب بموافقة المدير</span>
                                    <span wire:loading wire:target="submitOrderWithApproval"><i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">
                    <i class="fa-solid fa-hand-pointer" style="font-size: 2rem; margin-bottom: 1rem; color: var(--border-color);"></i>
                    <p>يرجى اختيار الطاولة أولاً من القائمة اليمنى</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modifiers Modal --}}
    @if($showModifierModal)
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; justify-content: center; align-items: center; padding: 1rem;">
        <div style="background: var(--surface-color); border: 1px solid var(--primary-color); border-radius: var(--radius-md); width: 100%; max-width: 500px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; animation: slideDown 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            
            <div style="background: linear-gradient(135deg, rgba(212,175,55,0.15), rgba(212,175,55,0.05)); padding: 1rem 1.5rem; border-bottom: 1px solid rgba(212,175,55,0.2); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: var(--primary-color); font-size: 1.2rem;">
                    <i class="fa-solid fa-layer-group"></i> خيارات: {{ $activeProductForModifiers['name'] ?? '' }}
                </h3>
                <button wire:click="cancelModifiers" style="background: none; border: none; color: var(--danger-color); font-size: 1.2rem; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                @foreach($productModifiersData as $group)
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="margin-top: 0; margin-bottom: 0.8rem; color: #fff; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            <span>
                                {{ $group['name'] }}
                                @if($group['is_required'])
                                    <span style="color: var(--danger-color); font-size: 0.8rem;">* (إجباري)</span>
                                @else
                                    <span style="color: var(--text-secondary); font-size: 0.8rem;">(اختياري)</span>
                                @endif
                            </span>
                            @if($group['is_multiple'])
                                <span style="font-size: 0.75rem; background: rgba(33,150,243,0.15); color: #2196F3; padding: 2px 6px; border-radius: 4px;">متعدد</span>
                            @else
                                <span style="font-size: 0.75rem; background: rgba(255,152,0,0.15); color: #FF9800; padding: 2px 6px; border-radius: 4px;">خيار واحد</span>
                            @endif
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px;">
                            @foreach($group['modifiers'] as $mod)
                                <label style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.2s; position: relative; background: rgba(255,255,255,0.02);">
                                    @if($group['is_multiple'])
                                        <input type="checkbox" wire:model="selectedModifiers.{{ $group['id'] }}.{{ $mod['id'] }}" style="position: absolute; top: 8px; right: 8px; accent-color: var(--primary-color);">
                                    @else
                                        <input type="radio" wire:model="selectedModifiers.{{ $group['id'] }}" value="{{ $mod['id'] }}" name="mod_group_{{ $group['id'] }}" style="position: absolute; top: 8px; right: 8px; accent-color: var(--primary-color);">
                                    @endif
                                    
                                    <span style="font-weight: 600; margin-top: 10px; font-size: 0.95rem; text-align: center;">{{ $mod['name'] }}</span>
                                    @if($mod['price'] > 0)
                                        <span style="color: var(--success-color); font-size: 0.8rem; font-weight: bold;">+{{ (float)$mod['price'] }} ر.ي</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        @error('modifier_'.$group['id'])
                            <div style="color: var(--danger-color); font-size: 0.85rem; margin-top: 0.5rem; font-weight: bold;"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.2); display: flex; gap: 1rem;">
                <button wire:click="confirmModifiers" class="btn btn-success" style="flex: 2; padding: 0.8rem; font-size: 1.1rem;">
                    <i class="fa-solid fa-cart-plus"></i> اعتماد وإضافة للطلب
                </button>
                <button wire:click="cancelModifiers" class="btn btn-outline" style="flex: 1;">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Orders -->
    @if($recentOrders->count() > 0)
    <div class="card" style="margin-top: 2rem;">
        <h3 style="margin-bottom: 1rem; font-size: clamp(0.95rem, 3vw, 1.1rem);"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary-color);"></i> طلباتي اليوم</h3>
        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="data-table">
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
                                    <span style="color: var(--warning-color); font-size: 0.8rem; font-weight: bold; white-space: nowrap;"><i class="fa-solid fa-hourglass-half"></i> انتظار إلغاء</span>
                                @elseif($order->cancel_duplicate_requested)
                                    <span style="color: var(--warning-color); font-size: 0.8rem; font-weight: bold; white-space: nowrap;"><i class="fa-solid fa-clone"></i> إلغاء تكرار</span>
                                @else
                                    <div style="display: flex; flex-direction: column; gap: 3px; min-width: 120px; max-width: 150px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.65rem; color: var(--text-secondary); font-weight: bold;">
                                            <span style="color: {{ in_array($order->status, ['pending', 'prep', 'ready', 'delivered']) ? 'var(--warning-color)' : 'inherit' }}">⏳</span>
                                            <span style="color: {{ in_array($order->status, ['prep', 'ready', 'delivered']) ? 'var(--info-color)' : 'inherit' }}">🔧</span>
                                            <span style="color: {{ in_array($order->status, ['ready', 'delivered']) ? 'var(--success-color)' : 'inherit' }}">✅</span>
                                            <span style="color: {{ $order->status === 'delivered' ? '#9e9e9e' : 'inherit' }}">📦</span>
                                        </div>
                                        <div style="display: flex; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                                            <div style="width: 25%; background: {{ in_array($order->status, ['pending', 'prep', 'ready', 'delivered']) ? 'var(--warning-color)' : 'transparent' }}"></div>
                                            <div style="width: 25%; background: {{ in_array($order->status, ['prep', 'ready', 'delivered']) ? 'var(--info-color)' : 'transparent' }}"></div>
                                            <div style="width: 25%; background: {{ in_array($order->status, ['ready', 'delivered']) ? 'var(--success-color)' : 'transparent' }}"></div>
                                            <div style="width: 25%; background: {{ $order->status === 'delivered' ? '#9e9e9e' : 'transparent' }}"></div>
                                        </div>
                                        <div style="font-size: 0.65rem; color: {{ $statusColor }}; font-weight: bold; text-align: center;">{{ $statusLabel }}</div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    @if($order->status === 'ready' && !$order->cancel_requested && !$order->cancel_duplicate_requested)
                                        <button wire:click="deliverOrder({{ $order->id }})" class="btn btn-sm" style="background: var(--success-color); color: #fff; border: none; padding: 0.4rem 0.8rem; font-weight: bold;">
                                            <i class="fa-solid fa-hand-holding-heart fa-beat"></i> تسليم الطلب
                                        </button>
                                    @endif

                                    @if(in_array($order->status, ['pending', 'prep']) && !$order->cancel_requested && !$order->cancel_duplicate_requested)
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
                                        
                                        <button wire:click="requestCancelDuplicate({{ $order->id }})" class="btn btn-sm" style="background: #9C27B0; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من طلب إلغاء هذا الطلب لأنه مكرر؟')">
                                            <i class="fa-solid fa-clone"></i> إلغاء تكرار
                                        </button>
                                        
                                        <button wire:click="requestCancel({{ $order->id }})" class="btn btn-sm" style="background: #F44336; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من طلب إلغاء هذه الطلبية بالكامل وتحرير الطاولة؟')">
                                            <i class="fa-solid fa-ban"></i> إلغاء
                                        </button>
                                    @elseif($order->status !== 'ready' && !$order->cancel_requested && !$order->cancel_duplicate_requested)
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
            Livewire.on('order-sent', () => { 
                if (typeof playSound === 'function') {
                    playSound('success'); 
                }
                // Scroll to top so they can see the success message and next step
                window.scrollTo({ top: 0, behavior: 'smooth' });
                // Force focus on the ticket field if it exists for the next person
                setTimeout(() => {
                    let field = document.getElementById('ticketBarcodeField');
                    if(field) {
                        field.value = '';
                        field.focus();
                    }
                }, 100);
            });

            Livewire.on('print-invoice', (event) => {
                let url = event.url;
                if (!url && typeof event === 'object') {
                    url = event.detail?.url || event[0]?.url;
                }
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
        });
    </script>

    {{-- Camera Scanner Logic --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrCode = null;

        function startCameraScanner() {
            document.getElementById('scannerModal').style.display = 'flex';
            
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            const config = { 
                fps: 10, 
                qrbox: { width: 300, height: 150 },
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            };
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                turnOnFlash();
            }).catch(err => {
                console.warn("Environment camera not found or failed, falling back to any camera", err);
                html5QrCode.start(
                    { facingMode: "user" },
                    config,
                    onScanSuccess,
                    onScanFailure
                ).catch(e => {
                    console.error("Camera start failed completely", e);
                    alert("تعذر تشغيل الكاميرا تلقائياً، يرجى التحقق من الصلاحيات.");
                });
            });

            function turnOnFlash() {
                setTimeout(() => {
                    const track = html5QrCode.getState() === 2 ? html5QrCode.getRunningTrack() : null;
                    if (track) {
                        try {
                            const capabilities = track.getCapabilities();
                            if (capabilities && capabilities.torch) {
                                track.applyConstraints({ advanced: [{ torch: true }] })
                                    .catch(e => console.log('Torch could not be enabled via constraint', e));
                            } else {
                                track.applyConstraints({ advanced: [{ torch: true }] })
                                    .catch(e => console.log('Torch could not be enabled directly', e));
                            }
                        } catch (e) {
                            console.log('Error checking capabilities', e);
                        }
                    }
                }, 500);
            }
        }

        function stopCameraScanner() {
            document.getElementById('scannerModal').style.display = 'none';
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(err => console.error("Failed to stop scanner", err));
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            stopCameraScanner();
            @this.set('ticketBarcode', decodedText);
            @this.call('lookupTicket');
            playSound('success');
        }

        function onScanFailure(error) {
            // handle scan failure silently
        }
    </script>

    <style>
        @keyframes fadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
        @keyframes pulseBorder {
            0% { border-color: rgba(244, 67, 54, 0.4); }
            50% { border-color: rgba(244, 67, 54, 1); box-shadow: 0 0 10px rgba(244, 67, 54, 0.15); }
            100% { border-color: rgba(244, 67, 54, 0.4); }
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
    </style>
</div>
