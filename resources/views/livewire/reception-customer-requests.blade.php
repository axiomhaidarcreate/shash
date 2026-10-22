<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem); margin: 0; color: var(--text-color);">
            <i class="fa-solid fa-bell" style="color: var(--primary-color);"></i> حجوزات الحفلات
        </h2>
        @if(!$isAdminRoute)
        <a href="{{ route('reception') }}" class="btn btn-outline" style="border-color: var(--text-secondary); color: var(--text-color);">
            <i class="fa-solid fa-arrow-right"></i> العودة للاستقبال
        </a>
        @endif
    </div>

    @if(session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card" style="border-top: 3px solid var(--warning-color);">
        
        {{-- Breadcrumbs / Navigation --}}
        <div style="display: flex; gap: 10px; margin-bottom: 1.5rem; align-items: center; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 10px;">
            <button wire:click="backToEvents" style="background: none; border: none; color: {{ $selectedEventId ? 'var(--text-secondary)' : 'var(--primary-color)' }}; font-weight: bold; cursor: pointer; font-size: 1.1rem;">
                <i class="fa-solid fa-calendar-star"></i> الحفلات
            </button>
            
            @if($selectedEventId)
                <i class="fa-solid fa-chevron-left" style="color: var(--text-secondary); font-size: 0.8rem;"></i>
                <button wire:click="backToTables" style="background: none; border: none; color: {{ $selectedTableId ? 'var(--text-secondary)' : 'var(--primary-color)' }}; font-weight: bold; cursor: pointer; font-size: 1.1rem;">
                    الطاولات المحجوزة
                </button>
            @endif

            @if($selectedTableId)
                <i class="fa-solid fa-chevron-left" style="color: var(--text-secondary); font-size: 0.8rem;"></i>
                <span style="color: var(--primary-color); font-weight: bold; font-size: 1.1rem;">الكراسي المحجوزة</span>
            @endif
        </div>

        {{-- LEVEL 1: EVENTS --}}
        @if(!$selectedEventId)
            @if($events->isEmpty())
                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    لا توجد حفلات بها حجوزات.
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                    @foreach($events as $event)
                        <div wire:click="selectEvent({{ $event->id }})" style="background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 15px; overflow: hidden; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 5px 15px rgba(212,175,55,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            @if($event->artist_image)
                                <div style="width: 100%; height: 140px; background-image: url('{{ asset('storage/' . $event->artist_image) }}'); background-size: cover; background-position: center; border-bottom: 2px solid var(--warning-color);"></div>
                            @else
                                <div style="width: 100%; height: 140px; background: #222; display: flex; align-items: center; justify-content: center; border-bottom: 2px solid var(--warning-color);">
                                    <i class="fa-solid fa-music fa-3x" style="color: rgba(255,255,255,0.1);"></i>
                                </div>
                            @endif
                            <div style="padding: 15px; text-align: center;">
                                <h4 style="margin-bottom: 10px; color: var(--primary-color); font-size: 1.2rem;">{{ $event->title }}</h4>
                                <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 15px;">
                                    <i class="fa-regular fa-calendar"></i> {{ $event->event_date }}
                                </div>
                                <div style="background: rgba(212,175,55,0.1); padding: 10px; border-radius: 10px; display: inline-block; margin-left: 10px;">
                                    <span style="font-size: 1.5rem; font-weight: 900; color: var(--primary-color);">{{ $event->approved_bookings_count }}</span>
                                    <span style="font-size: 0.9rem; color: var(--text-secondary);">معتمدة</span>
                                </div>
                                @if($event->pending_bookings_count > 0)
                                    <div style="background: rgba(255,152,0,0.15); padding: 10px; border-radius: 10px; display: inline-block; animation: pulse 2s infinite;">
                                        <span style="font-size: 1.5rem; font-weight: 900; color: #FF9800;">{{ $event->pending_bookings_count }}</span>
                                        <span style="font-size: 0.9rem; color: #FF9800;">بانتظار الموافقة</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        {{-- LEVEL 2: TABLES --}}
        @elseif($selectedEventId && !$selectedTableId)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="color: var(--primary-color); margin: 0;">الطاولات المحجوزة - {{ $selectedEvent->title ?? '' }}</h3>
                <button wire:click="openAddCustomerModal" class="btn btn-sm" style="background: var(--success-color); color: #fff; border: none;">
                    <i class="fa-solid fa-user-plus"></i> إضافة عميل للحفلة
                </button>
            </div>

            @if($tables->isEmpty())
                 <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    لا توجد طاولات محجوزة لهذه الحفلة.
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
                    @foreach($tables as $tableId => $tableBookings)
                        @php
                            $displayTableNumber = $tableBookings->first()->table_number ?: 'بدون رقم';
                            $chairsCount = $tableBookings->count();
                        @endphp
                        <div wire:click="selectTable('{{ $tableId }}')" style="background: linear-gradient(145deg, #2a2a2a, #1a1a1a); border: 2px solid var(--primary-color); border-radius: 15px; padding: 20px 10px; text-align: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 0 15px rgba(212,175,55,0.4)'" onmouseout="this.style.boxShadow='none'">
                            <i class="fa-solid fa-border-all fa-2x" style="color: var(--primary-color); margin-bottom: 10px;"></i>
                            <div style="font-size: 1.4rem; font-weight: 900; color: #fff; margin-bottom: 5px;">T{{ $displayTableNumber }}</div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); background: rgba(255,255,255,0.1); padding: 3px 8px; border-radius: 20px; display: inline-block;">
                                {{ $chairsCount }} كراسي محجوزة
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- PENDING BOOKINGS SECTION --}}
            @if($pendingBookings->isNotEmpty())
                <div style="margin-top: 2rem; margin-bottom: 1.5rem;">
                    <h3 style="color: #FF9800; margin-bottom: 1rem;">
                        <i class="fa-solid fa-hourglass-half" style="animation: pulse 2s infinite;"></i> 
                        طلبات حجز جديدة بانتظار الموافقة ({{ $pendingBookings->count() }})
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($pendingBookings as $pending)
                            <div style="background: linear-gradient(145deg, rgba(255,152,0,0.08), rgba(255,152,0,0.02)); border: 1px solid rgba(255,152,0,0.3); border-radius: 15px; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <div style="flex: 1; min-width: 200px;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                        <i class="fa-solid fa-user-clock" style="color: #FF9800; font-size: 1.3rem;"></i>
                                        <span style="font-weight: bold; color: #fff; font-size: 1.1rem;">{{ $pending->customer_name }}</span>
                                    </div>
                                    <div style="display: flex; gap: 15px; flex-wrap: wrap; color: var(--text-secondary); font-size: 0.9rem;">
                                        <span><i class="fa-solid fa-phone" style="color: var(--primary-color);"></i> {{ $pending->customer_phone }}</span>
                                        <span><i class="fa-solid fa-border-all" style="color: var(--primary-color);"></i> طاولة {{ $pending->table_number ?? '-' }}</span>
                                        <span><i class="fa-solid fa-chair" style="color: var(--primary-color);"></i> كرسي {{ $pending->chair_number ?? '-' }}</span>
                                        <span><i class="fa-solid fa-venus-mars" style="color: var(--primary-color);"></i> {{ $pending->gender == 'male' || $pending->gender == 'ذكر' ? 'ذكر' : 'أنثى' }}</span>
                                        <span><i class="fa-regular fa-clock" style="color: var(--primary-color);"></i> {{ $pending->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button wire:click="approveBooking({{ $pending->id }})" 
                                        style="background: var(--success-color); color: #fff; border: none; padding: 8px 20px; border-radius: 10px; cursor: pointer; font-weight: bold; font-size: 0.95rem; transition: all 0.2s;"
                                        onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                        <i class="fa-solid fa-check"></i> قبول
                                    </button>
                                    <button wire:click="rejectBooking({{ $pending->id }})" 
                                        style="background: var(--danger-color, #e74c3c); color: #fff; border: none; padding: 8px 20px; border-radius: 10px; cursor: pointer; font-weight: bold; font-size: 0.95rem; transition: all 0.2s;"
                                        onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                        <i class="fa-solid fa-xmark"></i> رفض
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        {{-- LEVEL 3: CHAIRS --}}
        @elseif($selectedEventId && $selectedTableId)
            <h3 style="color: var(--primary-color); margin-bottom: 1rem; text-align: center;">الكراسي المحجوزة (طاولة {{ $chairs->first()->table_number ?? $selectedTableId }})</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px;">
                @foreach($chairs as $chairBooking)
                    <div wire:click="selectChairBooking({{ $chairBooking->id }})" style="background: rgba(33, 150, 243, 0.1); border: 2px solid #2196F3; border-radius: 15px; padding: 15px 10px; text-align: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 0 15px rgba(33, 150, 243, 0.4)'" onmouseout="this.style.boxShadow='none'">
                        <i class="fa-solid fa-chair fa-2x" style="color: #2196F3; margin-bottom: 10px;"></i>
                        <div style="font-size: 1.2rem; font-weight: bold; color: #fff; margin-bottom: 5px;">C{{ $chairBooking->chair_number ?: '?' }}</div>
                        <div style="font-size: 0.8rem; color: #aaa;">عرض العميل</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- LEVEL 4: CUSTOMER DETAILS MODAL --}}
    @if($selectedBookingForChair)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 3000; display: flex; justify-content: center; align-items: center; padding: 15px;">
            <div style="background: var(--surface-color); width: 100%; max-width: 450px; border-radius: 20px; border-top: 4px solid var(--success-color); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div style="padding: 20px; text-align: center; border-bottom: 1px solid var(--border-color); background: rgba(76,175,80,0.1);">
                    <i class="fa-solid fa-user-circle fa-4x" style="color: var(--success-color); margin-bottom: 10px;"></i>
                    <h3 style="margin: 0; color: #fff; font-size: 1.5rem;">{{ $selectedBookingForChair->customer_name }}</h3>
                </div>
                <div style="padding: 20px;">
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px dashed var(--border-color);">
                            <span style="color: var(--text-secondary);"><i class="fa-solid fa-phone"></i> رقم الجوال:</span>
                            <a href="tel:{{ $selectedBookingForChair->customer_phone }}" style="color: var(--primary-color); font-weight: bold; font-size: 1.1rem; text-decoration: none;">{{ $selectedBookingForChair->customer_phone }}</a>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px dashed var(--border-color);">
                            <span style="color: var(--text-secondary);"><i class="fa-solid fa-venus-mars"></i> الجنس:</span>
                            <span style="font-weight: bold; color: {{ $selectedBookingForChair->gender == 'male' ? '#2196F3' : '#E91E63' }};">
                                {{ $selectedBookingForChair->gender == 'male' ? 'ذكر' : 'أنثى' }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px dashed var(--border-color);">
                            <span style="color: var(--text-secondary);"><i class="fa-solid fa-border-all"></i> الطاولة:</span>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <select wire:model.live="editTableNumber" class="form-control" style="width: 120px; padding: 2px 5px; text-align: center;">
                                    <option value="">اختر طاولة</option>
                                    @foreach($eventTables as $t)
                                        <option value="{{ $t->number }}">T{{ $t->number }} (كراسي: {{ $t->chairs->count() }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px dashed var(--border-color);">
                            <span style="color: var(--text-secondary);"><i class="fa-solid fa-chair"></i> الكرسي:</span>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <select wire:model="editChairNumber" class="form-control" style="width: 120px; padding: 2px 5px; text-align: center;">
                                    <option value="">اختر كرسي</option>
                                    @foreach($editAvailableChairs as $c)
                                        <option value="{{ $c['number'] }}" {{ $c['is_booked'] ? 'disabled' : '' }}>
                                            C{{ $c['number'] }} {{ $c['is_booked'] ? '(محجوز)' : '(متاح)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--text-secondary);"><i class="fa-regular fa-clock"></i> وقت الحجز:</span>
                            <span style="font-size: 0.9rem; color: #aaa;">{{ $selectedBookingForChair->created_at->format('Y-m-d h:i A') }}</span>
                        </div>
                    </div>
                </div>
                <div style="padding: 15px; text-align: center; background: rgba(0,0,0,0.2); display: flex; gap: 10px;">
                    <button wire:click="updateBookingLocation" class="btn btn-success" style="flex: 1;">
                        حفظ التعديل
                    </button>
                    <button wire:click="closeCustomerDetails" class="btn" style="flex: 1; background: var(--surface-hover); color: var(--text-primary); border: 1px solid var(--border-color);">
                        إغلاق
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ADD CUSTOMER MODAL --}}
    @if($showAddCustomerModal)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 3000; display: flex; justify-content: center; align-items: center; padding: 15px;">
            <div style="background: var(--surface-color); width: 100%; max-width: 500px; border-radius: 20px; border-top: 4px solid var(--primary-color); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div style="padding: 20px; text-align: center; border-bottom: 1px solid var(--border-color); background: rgba(212,175,55,0.1);">
                    <h3 style="margin: 0; color: var(--primary-color); font-size: 1.5rem;"><i class="fa-solid fa-user-plus"></i> إضافة عميل للحفلة</h3>
                </div>
                <div style="padding: 20px;">
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div>
                            <label style="color: var(--text-secondary); margin-bottom: 5px; display: block;">الاسم</label>
                            <input type="text" wire:model="newCustomerName" class="form-control" placeholder="اسم العميل">
                            @error('newCustomerName') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="color: var(--text-secondary); margin-bottom: 5px; display: block;">رقم الجوال</label>
                            <input type="text" wire:model="newCustomerPhone" class="form-control" placeholder="رقم الجوال">
                            @error('newCustomerPhone') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </div>
                        <div style="display: flex; gap: 15px;">
                            <div style="flex: 1;">
                                <label style="color: var(--text-secondary); margin-bottom: 5px; display: block;">رقم الطاولة</label>
                                <select wire:model.live="newCustomerTable" class="form-control">
                                    <option value="">اختر طاولة</option>
                                    @foreach($eventTables as $t)
                                        <option value="{{ $t->number }}">T{{ $t->number }} (كراسي: {{ $t->chairs->count() }})</option>
                                    @endforeach
                                </select>
                                @error('newCustomerTable') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                            </div>
                            <div style="flex: 1;">
                                <label style="color: var(--text-secondary); margin-bottom: 5px; display: block;">رقم الكرسي</label>
                                <select wire:model="newCustomerChair" class="form-control">
                                    <option value="">اختر كرسي</option>
                                    @foreach($newAvailableChairs as $c)
                                        <option value="{{ $c['number'] }}" {{ $c['is_booked'] ? 'disabled' : '' }}>
                                            C{{ $c['number'] }} {{ $c['is_booked'] ? '(محجوز)' : '(متاح)' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('newCustomerChair') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label style="color: var(--text-secondary); margin-bottom: 5px; display: block;">الجنس</label>
                            <select wire:model="newCustomerGender" class="form-control">
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div style="padding: 15px; text-align: center; background: rgba(0,0,0,0.2); display: flex; gap: 10px;">
                    <button wire:click="addCustomerToEvent" class="btn btn-primary" style="flex: 2;">
                        إضافة للحفلة
                    </button>
                    <button wire:click="closeAddCustomerModal" class="btn" style="flex: 1; background: var(--surface-hover); color: var(--text-primary); border: 1px solid var(--border-color);">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
