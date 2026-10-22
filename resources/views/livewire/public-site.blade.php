<x-slot name="title">{{ $site_name }}</x-slot>

<div>
{{-- Customer Auth Bar --}}
<div style="position: fixed; top: 0; left: 0; right: 0; z-index: 1500; background: rgba(13,13,13,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-color); padding: 8px 15px; display: flex; justify-content: space-between; align-items: center;">
    @if($loggedInCustomer)
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; font-weight: bold; color: #000; font-size: 0.8rem;">
                {{ mb_substr($loggedInCustomer->name, 0, 1) }}
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: bold; color: white;">{{ $loggedInCustomer->name }}</div>
                <div style="font-size: 0.7rem; color: var(--text-secondary);">{{ $loggedInCustomer->phone }}</div>
            </div>
        </div>
        <button wire:click="customerLogout" style="background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); color: #ff5252; padding: 5px 12px; border-radius: 8px; font-size: 0.8rem; cursor: pointer;">
            <i class="fa-solid fa-sign-out-alt"></i> خروج
        </button>
    @else
        <div style="font-size: 0.85rem; color: var(--text-secondary);"><i class="fa-solid fa-user-lock"></i> زائر</div>
        <button wire:click="openAuthModal" style="background: var(--primary-color); color: #000; border: none; padding: 5px 15px; border-radius: 8px; font-size: 0.85rem; font-weight: bold; cursor: pointer;">
            <i class="fa-solid fa-sign-in-alt"></i> تسجيل الدخول
        </button>
    @endif
</div>
<div style="height: 50px;"></div> {{-- Spacer for fixed auth bar --}}
@if($site_cover_image)
    <img src="{{ asset('storage/' . $site_cover_image) }}" class="cover-image" alt="Cover">
@endif

<div class="container">
    <div class="profile-section">
        @if($site_logo)
            <img src="{{ asset('storage/' . $site_logo) }}" class="profile-image" alt="Logo">
        @else
            <div class="profile-image" style="display:flex; justify-content:center; align-items:center; background:linear-gradient(135deg, var(--primary-color), #8B6914);">
                <i class="fa-solid fa-store fa-3x" style="color: black;"></i>
            </div>
        @endif
        
        <h1 class="profile-title">{{ $site_name }}</h1>
        @if($site_bio)
            <p class="profile-bio">{{ $site_bio }}</p>
        @endif
    </div>

    <style>
        /* Social Icons matching the image (no background, white outline) */
        .social-icons-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 2.5rem;
            padding: 0;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            text-decoration: none;
            font-size: 1.8rem;
            transition: transform 0.3s ease;
        }
        
        .social-icon:hover {
            transform: translateY(-3px) scale(1.1);
            color: var(--primary-color);
        }

        /* Pill shaped Link Cards matching the image */
        .links-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 2rem;
        }

        .link-card {
            display: flex;
            align-items: center;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 50px;
            padding: 6px;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
            position: relative;
        }

        .link-card:hover {
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.03);
        }

        .link-icon-wrapper {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: white;
            font-size: 1.3rem;
        }

        .link-content {
            flex: 1;
            text-align: center;
            padding: 0 10px;
        }

        .link-title {
            font-weight: 500;
            font-size: 1rem;
        }

        .link-subtitle {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .link-dots {
            width: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.2rem;
        }

        /* Location Card matching the image */
        .location-card {
            display: block;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            overflow: hidden;
            text-decoration: none;
            color: white;
            margin-bottom: 20px;
            transition: border-color 0.3s ease;
        }

        .location-card:hover {
            border-color: var(--primary-color);
        }

        .map-image-container {
            width: 100%;
            height: 200px;
            background: #111;
            position: relative;
        }
        
        .map-image-container iframe {
            width: 100%;
            height: 100%;
            pointer-events: none; /* To allow clicking the card */
        }

        .location-details {
            padding: 15px;
            text-align: center;
        }

        .loc-title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .loc-address {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 15px;
        }

        .loc-directions {
            font-size: 0.9rem;
            font-weight: bold;
            color: #ffffff;
        }
    </style>

    {{-- Social Icons Section --}}
    @if($socialLinks->isNotEmpty())
        <div class="social-icons-container animated-link" style="animation-delay: 0.1s;">
            @foreach($socialLinks as $link)
                <a href="{{ $link->url }}" target="_blank" class="social-icon" title="{{ $link->title }}">
                    <i class="{{ $link->icon ?? 'fa-solid fa-link' }}"></i>
                </a>
            @endforeach
        </div>
    @endif

    {{-- Events Button --}}
    @if($site_show_events && $events->isNotEmpty())
        <div class="links-section" style="margin-bottom: 1rem;">
            <button wire:click="$set('isEventsListModalOpen', true)" class="link-card animated-link" style="width: 100%; text-align: right; animation-delay: 0.15s; border-color: var(--warning-color); background: rgba(212,175,55,0.1);">
                <div class="link-icon-wrapper" style="background: var(--warning-color); color: #000;">
                    <i class="fa-solid fa-calendar-star"></i>
                </div>
                <div class="link-content">
                    <div class="link-title" style="color: var(--warning-color); font-weight: bold;">حفلات هذا الشهر</div>
                    <div class="link-subtitle" style="color: #fff;">احجز طاولتك الآن لأقوى الحفلات</div>
                </div>
                <div class="link-dots">
                    <i class="fa-solid fa-chevron-left"></i>
                </div>
            </button>
        </div>
    @endif

    {{-- Packages Button --}}
    @if($site_show_packages && ($packages->isNotEmpty() || $loyaltyOffers->isNotEmpty()))
        <div class="links-section" style="margin-bottom: 1rem;">
            <button wire:click="$set('isSubListModalOpen', true)" class="link-card animated-link" style="width: 100%; text-align: right; animation-delay: 0.18s; border-color: var(--info-color); background: rgba(33, 150, 243, 0.1);">
                <div class="link-icon-wrapper" style="background: var(--info-color); color: #fff;">
                    <i class="fa-solid fa-crown"></i>
                </div>
                <div class="link-content">
                    <div class="link-title" style="color: var(--info-color); font-weight: bold;">باقات الاشتراك والولاء</div>
                    <div class="link-subtitle" style="color: #fff;">اشترك الآن واستمتع بمميزات حصرية</div>
                </div>
                <div class="link-dots">
                    <i class="fa-solid fa-chevron-left"></i>
                </div>
            </button>
        </div>
    @endif

    {{-- Location Section --}}
    @if($site_location_url)
        <div class="animated-link" style="animation-delay: 0.2s;">
            <a href="{{ $site_location_url }}" target="_blank" class="location-card">
                <div class="map-image-container">
                    @if($site_location_lat && $site_location_lng)
                        <iframe style="border:0;" loading="lazy" allowfullscreen src="https://maps.google.com/maps?q={{$site_location_lat}},{{$site_location_lng}}&z=15&output=embed"></iframe>
                    @else
                        <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-map-location-dot fa-3x" style="color: rgba(255,255,255,0.2);"></i>
                        </div>
                    @endif
                </div>
                <div class="location-details">
                    <div class="loc-title">{{ $site_location_name ?: 'الموقع الجغرافي | Location' }}</div>
                    @if($site_location_address)
                        <div class="loc-address"><i class="fa-solid fa-location-dot"></i> {{ $site_location_address }}</div>
                    @endif
                    <div class="loc-directions">Get directions</div>
                </div>
            </a>
        </div>
    @endif

    {{-- Regular Links Section (Menu, WhatsApp, Links) --}}
    @if($regularLinks->isNotEmpty() || $locationLinks->isNotEmpty())
        <div class="links-section">
            @php
                $allRegularLinks = $regularLinks->concat($locationLinks)->sortBy('sort_order');
            @endphp
            @foreach($allRegularLinks as $index => $link)
                <a href="{{ $link->url }}" target="_blank" class="link-card animated-link" style="animation-delay: {{ 0.3 + ($index * 0.1) }}s;">
                    <div class="link-icon-wrapper" style="background: {{ $link->icon_bg_color }};">
                        <i class="{{ $link->icon ?? 'fa-solid fa-link' }}"></i>
                    </div>
                    <div class="link-content">
                        <div class="link-title">{{ $link->title }}</div>
                        @if($link->subtitle)
                            <div class="link-subtitle">{{ $link->subtitle }}</div>
                        @endif
                    </div>
                    <div class="link-dots">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div style="text-align: center; margin-top: 3rem; padding-bottom: 2rem; color: var(--text-secondary); font-size: 0.8rem;" class="animated-link" style="animation-delay: 0.8s;">
        {!! $site_footer_text !!}
    </div>
</div>

{{-- MODALS SECTION --}}
<style>
    .public-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.8); backdrop-filter: blur(5px);
        z-index: 2000; display: flex; justify-content: center; align-items: center;
        padding: 15px; animation: fadeIn 0.3s ease;
    }
    .public-modal-content {
        background: var(--surface-color); border: 1px solid var(--border-color);
        width: 100%; max-width: 500px; max-height: 85vh; overflow-y: auto;
        border-radius: 20px; padding: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        animation: scaleIn 0.3s ease;
    }
    .public-modal-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;
    }
    .public-modal-title { font-size: 1.2rem; font-weight: bold; color: white; margin: 0; }
    .public-modal-close {
        background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer;
    }
    
    .event-card {
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 15px; overflow: hidden; margin-bottom: 15px; cursor: pointer;
        transition: all 0.2s; display: block; width: 100%; text-align: right;
    }
    .event-card:hover { border-color: var(--warning-color); background: rgba(255,255,255,0.08); }
    .event-image {
        width: 100%; height: 150px; background-size: cover; background-position: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .event-details { padding: 15px; }
    .event-title { font-size: 1.1rem; font-weight: bold; color: white; margin-bottom: 5px; }
    .event-date { font-size: 0.85rem; color: var(--warning-color); margin-bottom: 10px; }
    
    .form-input {
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2);
        color: white; width: 100%; padding: 12px; border-radius: 10px; margin-bottom: 15px;
    }
    .form-input:focus { border-color: var(--primary-color); outline: none; }
    
    .btn-submit {
        background: var(--primary-color); color: black; font-weight: bold;
        width: 100%; padding: 12px; border-radius: 10px; border: none; cursor: pointer;
    }
    
    @keyframes scaleIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

{{-- 1. Events List Modal --}}
@if($isEventsListModalOpen)
    <div class="public-modal-overlay">
        <div class="public-modal-content" style="border-top: 3px solid var(--warning-color);">
            <div class="public-modal-header">
                <h3 class="public-modal-title"><i class="fa-solid fa-calendar-star" style="color: var(--warning-color);"></i> حفلات هذا الشهر</h3>
                <button wire:click="$set('isEventsListModalOpen', false)" class="public-modal-close">&times;</button>
            </div>
            <div>
                @foreach($events as $event)
                    <button wire:click="openEventModal({{ $event->id }})" class="event-card">
                        @if($event->artist_image)
                            <div class="event-image" style="background-image: url('{{ asset('storage/' . $event->artist_image) }}');"></div>
                        @else
                            <div class="event-image" style="background: #222; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-music fa-2x" style="color: rgba(255,255,255,0.2);"></i>
                            </div>
                        @endif
                        <div class="event-details">
                            <div class="event-title">{{ $event->title }}</div>
                            <div class="event-date">
                                <i class="fa-regular fa-calendar"></i> {{ $event->event_date }}
                                @if($event->event_time) | <i class="fa-regular fa-clock"></i> {{ $event->event_time }} @endif
                            </div>
                            @if($event->description)
                                <div style="font-size: 0.85rem; color: #ccc;">{{ Str::limit($event->description, 80) }}</div>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- 2. Event Booking Modal --}}
@if($isEventModalOpen && $selectedEvent)
    <div class="public-modal-overlay">
        <div class="public-modal-content" style="border-top: 3px solid var(--success-color);">
            <div class="public-modal-header" style="margin-bottom: 10px;">
                <h3 class="public-modal-title">حجز طاولة - {{ $selectedEvent->title }}</h3>
                <button wire:click="$set('isEventModalOpen', false)" class="public-modal-close">&times;</button>
            </div>
            
            <div style="margin-bottom: 20px; border-radius: 15px; overflow: hidden; position: relative;">
                @if($selectedEvent->artist_image)
                    <div style="width: 100%; height: 120px; background-image: url('{{ asset('storage/' . $selectedEvent->artist_image) }}'); background-size: cover; background-position: center;"></div>
                @else
                    <div style="width: 100%; height: 120px; background: #222; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-music fa-3x" style="color: rgba(255,255,255,0.1);"></i>
                    </div>
                @endif
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.9)); padding: 10px;">
                    <div style="color: var(--warning-color); font-size: 0.85rem; font-weight: bold;"><i class="fa-regular fa-calendar"></i> {{ $selectedEvent->event_date }} @if($selectedEvent->event_time) | <i class="fa-regular fa-clock"></i> {{ $selectedEvent->event_time }} @endif</div>
                </div>
            </div>

            <form wire:submit.prevent="submitEventBooking">
                <input type="text" wire:model="booking_name" class="form-input" placeholder="الاسم الكريم *" required>
                @error('booking_name') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -10px; margin-bottom: 10px;">{{ $message }}</div> @enderror
                
                <input type="tel" wire:model="booking_phone" class="form-input" placeholder="رقم الجوال *" required>
                @error('booking_phone') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -10px; margin-bottom: 10px;">{{ $message }}</div> @enderror
                
                {{-- Gender Selection --}}
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 8px; display: block;"><i class="fa-solid fa-venus-mars"></i> الجنس *</label>
                    <div style="display: flex; gap: 10px;">
                        <label wire:click="$set('booking_gender', 'male')" style="flex: 1; padding: 10px; border-radius: 10px; text-align: center; cursor: pointer; font-weight: bold; transition: all 0.2s;
                            background: {{ $booking_gender === 'male' ? 'rgba(33, 150, 243, 0.15)' : 'rgba(255,255,255,0.05)' }};
                            border: 2px solid {{ $booking_gender === 'male' ? '#2196F3' : 'rgba(255,255,255,0.2)' }};
                            color: {{ $booking_gender === 'male' ? '#2196F3' : 'white' }};">
                            <i class="fa-solid fa-mars"></i> ذكر
                        </label>
                        <label wire:click="$set('booking_gender', 'female')" style="flex: 1; padding: 10px; border-radius: 10px; text-align: center; cursor: pointer; font-weight: bold; transition: all 0.2s;
                            background: {{ $booking_gender === 'female' ? 'rgba(233, 30, 99, 0.15)' : 'rgba(255,255,255,0.05)' }};
                            border: 2px solid {{ $booking_gender === 'female' ? '#E91E63' : 'rgba(255,255,255,0.2)' }};
                            color: {{ $booking_gender === 'female' ? '#E91E63' : 'white' }};">
                            <i class="fa-solid fa-venus"></i> أنثى
                        </label>
                    </div>
                    @error('booking_gender') <div style="color: #ff5252; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div> @enderror
                </div>

                <div style="margin-bottom: 15px;">
                    <button type="button" wire:click="openTableSelector" class="form-input" style="text-align: right; display: flex; justify-content: space-between; align-items: center; cursor: pointer; {{ $booking_table ? 'border-color: var(--warning-color); color: var(--warning-color); font-weight: bold;' : '' }}">
                        <span>
                            <i class="fa-solid fa-border-all"></i>
                            {{ $booking_table ? 'الطاولة المختارة: ' . $booking_table : 'اختر الطاولة (اختياري)' }}
                        </span>
                        <i class="fa-solid fa-chevron-left" style="font-size: 0.8em; opacity: 0.7;"></i>
                    </button>
                    @error('booking_table') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -5px;">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submitEventBooking">تأكيد طلب الحجز</span>
                    <span wire:loading wire:target="submitEventBooking"><i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...</span>
                </button>
            </form>
        </div>
    </div>
@endif

{{-- 3. Subscriptions List Modal --}}
@if($isSubListModalOpen)
    <div class="public-modal-overlay">
        <div class="public-modal-content" style="border-top: 3px solid var(--info-color);">
            <div class="public-modal-header">
                <h3 class="public-modal-title"><i class="fa-solid fa-crown" style="color: var(--info-color);"></i> الباقات المتاحة</h3>
                <button wire:click="$set('isSubListModalOpen', false)" class="public-modal-close">&times;</button>
            </div>
            
            @if($packages->isNotEmpty())
                <h4 style="color: var(--info-color); margin-bottom: 10px; font-size: 1rem;"><i class="fa-solid fa-box"></i> باقات الاشتراك</h4>
                <div style="display: grid; gap: 10px; margin-bottom: 20px;">
                    @foreach($packages as $pkg)
                        <button wire:click="openSubModal('package', {{ $pkg->id }})" class="event-card" style="margin-bottom: 0;">
                            <div class="event-details">
                                <div class="event-title" style="color: var(--info-color);">{{ $pkg->name }}</div>
                                <div class="event-date" style="color: white; font-size: 1rem;">{{ $pkg->price }} ريال</div>
                                <div style="font-size: 0.85rem; color: #ccc;">المدة: {{ $pkg->duration_days }} يوم | الحد اليومي: {{ $pkg->daily_order_limit }} طلب</div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif

            @if($loyaltyOffers->isNotEmpty())
                <h4 style="color: var(--primary-color); margin-bottom: 10px; font-size: 1rem;"><i class="fa-solid fa-star"></i> عروض الولاء</h4>
                <div style="display: grid; gap: 10px; margin-bottom: 20px;">
                    @foreach($loyaltyOffers as $offer)
                        <button wire:click="openSubModal('loyalty', {{ $offer->id }})" class="event-card" style="margin-bottom: 0;">
                            <div class="event-details">
                                <div class="event-title" style="color: var(--primary-color);">{{ $offer->name }}</div>
                                <div style="font-size: 0.85rem; color: #ccc; margin-top: 5px;">{{ $offer->reward_description }}</div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif

{{-- 4. Subscription Request Modal --}}
@if($isSubModalOpen)
    <div class="public-modal-overlay">
        <div class="public-modal-content" style="border-top: 3px solid {{ $isLoyaltySub ? 'var(--primary-color)' : 'var(--info-color)' }};">
            <div class="public-modal-header">
                <h3 class="public-modal-title">طلب انضمام - {{ $isLoyaltySub ? 'برنامج الولاء' : 'باقة اشتراك' }}</h3>
                <button wire:click="$set('isSubModalOpen', false)" class="public-modal-close">&times;</button>
            </div>
            <form wire:submit.prevent="submitSubscriptionRequest">
                <input type="text" wire:model="sub_name" class="form-input" placeholder="الاسم الكريم *" required>
                @error('sub_name') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -10px; margin-bottom: 10px;">{{ $message }}</div> @enderror
                
                <input type="tel" wire:model="sub_phone" class="form-input" placeholder="رقم الجوال *" required>
                @error('sub_phone') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -10px; margin-bottom: 10px;">{{ $message }}</div> @enderror

                <button type="submit" class="btn-submit" style="background: {{ $isLoyaltySub ? 'var(--primary-color)' : 'var(--info-color)' }}; color: {{ $isLoyaltySub ? 'black' : 'white' }};" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submitSubscriptionRequest">تأكيد الطلب</span>
                    <span wire:loading wire:target="submitSubscriptionRequest"><i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...</span>
                </button>
            </form>
        </div>
    </div>
@endif

{{-- 5. Table Selector Modal --}}
@if($isTableSelectorModalOpen)
    <div class="public-modal-overlay" style="z-index: 2050;">
        <div class="public-modal-content" style="border-top: 3px solid var(--primary-color); padding: 0; background: transparent; box-shadow: none;">
            <div style="background: var(--surface-color); border-radius: 20px; overflow: hidden; padding: 15px;">
                @livewire('table-selector', ['initialTableId' => $booking_table_id, 'excludeOccupied' => false, 'filterSection' => 'صالة الحفلات'])
            </div>
        </div>
    </div>
@endif

{{-- Customer Auth Modal (Login / Register) --}}
@if($isAuthModalOpen)
    <div class="public-modal-overlay">
        <div class="public-modal-content" style="border-top: 3px solid var(--primary-color);">
            <div class="public-modal-header">
                <h3 class="public-modal-title">
                    @if($authMode === 'login')
                        <i class="fa-solid fa-sign-in-alt" style="color: var(--primary-color);"></i> تسجيل الدخول
                    @else
                        <i class="fa-solid fa-user-plus" style="color: var(--primary-color);"></i> إنشاء حساب جديد
                    @endif
                </h3>
                <button wire:click="$set('isAuthModalOpen', false)" class="public-modal-close">&times;</button>
            </div>

            {{-- Mode Toggle Tabs --}}
            <div style="display: flex; gap: 0; margin-bottom: 20px; border-radius: 10px; overflow: hidden; border: 1px solid var(--border-color);">
                <button wire:click="switchAuthMode('login')" style="flex: 1; padding: 10px; border: none; cursor: pointer; font-weight: bold; font-size: 0.9rem; transition: all 0.2s;
                    background: {{ $authMode === 'login' ? 'var(--primary-color)' : 'transparent' }};
                    color: {{ $authMode === 'login' ? '#000' : 'var(--text-secondary)' }};">
                    <i class="fa-solid fa-sign-in-alt"></i> دخول
                </button>
                <button wire:click="switchAuthMode('register')" style="flex: 1; padding: 10px; border: none; cursor: pointer; font-weight: bold; font-size: 0.9rem; transition: all 0.2s;
                    background: {{ $authMode === 'register' ? 'var(--primary-color)' : 'transparent' }};
                    color: {{ $authMode === 'register' ? '#000' : 'var(--text-secondary)' }};">
                    <i class="fa-solid fa-user-plus"></i> حساب جديد
                </button>
            </div>

            @if($authMode === 'login')
                <form wire:submit.prevent="customerLogin" autocomplete="off">
                    <input type="tel" id="login_phone" name="login_phone" wire:model="auth_phone" class="form-input" placeholder="رقم الجوال *" required autocomplete="new-password">
                    @error('auth_phone') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -10px; margin-bottom: 10px;">{{ $message }}</div> @enderror

                    <input type="password" id="login_password" name="login_password" wire:model="auth_password" class="form-input" placeholder="كلمة المرور *" required autocomplete="new-password">
                    @error('auth_password') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -10px; margin-bottom: 10px;">{{ $message }}</div> @enderror

                    <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="customerLogin"><i class="fa-solid fa-sign-in-alt"></i> تسجيل الدخول</span>
                        <span wire:loading wire:target="customerLogin"><i class="fa-solid fa-spinner fa-spin"></i> جاري الدخول...</span>
                    </button>
                </form>
                <div style="text-align: center; margin-top: 15px; font-size: 0.85rem; color: var(--text-secondary);">
                    ليس لديك حساب؟
                    <button wire:click="switchAuthMode('register')" style="background: none; border: none; color: var(--primary-color); cursor: pointer; font-weight: bold; text-decoration: underline;">إنشاء حساب جديد</button>
                </div>
            @else
                <form wire:submit.prevent="customerRegister" autocomplete="off">
                    <input type="text" id="reg_name" name="reg_name" wire:model="auth_name" class="form-input" placeholder="الاسم الكريم *" required autocomplete="new-password">
                    @error('auth_name') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -10px; margin-bottom: 10px;">{{ $message }}</div> @enderror

                    <input type="tel" id="reg_phone" name="reg_phone" wire:model="auth_phone" class="form-input" placeholder="رقم الجوال *" required autocomplete="new-password">
                    @error('auth_phone') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -10px; margin-bottom: 10px;">{{ $message }}</div> @enderror

                    <input type="password" id="reg_password" name="reg_password" wire:model="auth_password" class="form-input" placeholder="كلمة المرور * (4 أحرف على الأقل)" required autocomplete="new-password">
                    @error('auth_password') <div style="color: #ff5252; font-size: 0.8rem; margin-top: -10px; margin-bottom: 10px;">{{ $message }}</div> @enderror

                    {{-- Gender Selection --}}
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 8px; display: block;"><i class="fa-solid fa-venus-mars"></i> الجنس *</label>
                        <div style="display: flex; gap: 10px;">
                            <label wire:click="$set('auth_gender', 'male')" style="flex: 1; padding: 10px; border-radius: 10px; text-align: center; cursor: pointer; font-weight: bold; transition: all 0.2s;
                                background: {{ $auth_gender === 'male' ? 'rgba(33, 150, 243, 0.15)' : 'rgba(255,255,255,0.05)' }};
                                border: 2px solid {{ $auth_gender === 'male' ? '#2196F3' : 'rgba(255,255,255,0.2)' }};
                                color: {{ $auth_gender === 'male' ? '#2196F3' : 'white' }};">
                                <i class="fa-solid fa-mars"></i> ذكر
                            </label>
                            <label wire:click="$set('auth_gender', 'female')" style="flex: 1; padding: 10px; border-radius: 10px; text-align: center; cursor: pointer; font-weight: bold; transition: all 0.2s;
                                background: {{ $auth_gender === 'female' ? 'rgba(233, 30, 99, 0.15)' : 'rgba(255,255,255,0.05)' }};
                                border: 2px solid {{ $auth_gender === 'female' ? '#E91E63' : 'rgba(255,255,255,0.2)' }};
                                color: {{ $auth_gender === 'female' ? '#E91E63' : 'white' }};">
                                <i class="fa-solid fa-venus"></i> أنثى
                            </label>
                        </div>
                        @error('auth_gender') <div style="color: #ff5252; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="customerRegister"><i class="fa-solid fa-user-plus"></i> إنشاء الحساب</span>
                        <span wire:loading wire:target="customerRegister"><i class="fa-solid fa-spinner fa-spin"></i> جاري الإنشاء...</span>
                    </button>
                </form>
                <div style="text-align: center; margin-top: 15px; font-size: 0.85rem; color: var(--text-secondary);">
                    لديك حساب بالفعل؟
                    <button wire:click="switchAuthMode('login')" style="background: none; border: none; color: var(--primary-color); cursor: pointer; font-weight: bold; text-decoration: underline;">تسجيل الدخول</button>
                </div>
            @endif
        </div>
    </div>
@endif

</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('notify', (event) => {
            // Note: Toast might need to be styled for public view if not included
            if(typeof showToast === 'function') {
                showToast(event[0].message, event[0].type);
            } else {
                alert(event[0].message);
            }
        });
    });
</script>
