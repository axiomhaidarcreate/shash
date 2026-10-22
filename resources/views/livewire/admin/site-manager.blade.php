<x-slot name="title">إدارة الموقع العام</x-slot>

<div>
    <div class="page-header d-flex justify-content-between align-items-center mb-4" style="flex-wrap: wrap; gap: 10px;">
        <div>
            <h1><i class="fa-solid fa-globe" style="color: var(--primary-color);"></i> إدارة الموقع العام</h1>
            <p>إدارة صفحة الموقع العامة وروابط التواصل الاجتماعي والإعلانات</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="/site" target="_blank" class="btn btn-outline" style="font-size: 0.9rem;">
                <i class="fa-solid fa-external-link"></i> معاينة الموقع
            </a>
            <button wire:click="openModal" class="btn btn-success">
                <i class="fa-solid fa-plus"></i> إضافة رابط جديد
            </button>
        </div>
    </div>

    {{-- Site Settings Card --}}
    <div class="card mb-4" style="border-top: 3px solid var(--primary-color);">
        <h3 style="margin-bottom: 1.2rem; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-cog" style="color: var(--primary-color);"></i> إعدادات الموقع الأساسية
        </h3>

        <div class="grid grid-cols-2 gap-3" style="margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label">اسم الموقع / الحساب</label>
                <input type="text" wire:model="site_name" class="form-control" placeholder="مثال: @mars.lounge">
            </div>
            <div class="form-group">
                <label class="form-label">الوصف / البايو</label>
                <textarea wire:model="site_bio" class="form-control" rows="2" placeholder="مثال: أفضل الحفلات اليومية والقهوة والشيشة" style="min-height: 44px;"></textarea>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3" style="margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label"><i class="fa-solid fa-image" style="color: var(--primary-color);"></i> شعار الموقع (صورة دائرية)</label>
                <input type="file" wire:model="newLogo" class="form-control" accept="image/*">
                @if($site_logo)
                    <div style="margin-top: 8px;">
                        <img src="{{ asset('storage/' . $site_logo) }}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-color);">
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fa-solid fa-panorama" style="color: var(--info-color);"></i> صورة الغلاف (اختياري)</label>
                <input type="file" wire:model="newCoverImage" class="form-control" accept="image/*">
                @if($site_cover_image)
                    <div style="margin-top: 8px;">
                        <img src="{{ asset('storage/' . $site_cover_image) }}" style="width: 120px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);">
                    </div>
                @endif
            </div>
        </div>

        {{-- Custom Texts Settings --}}
        <h4 style="font-size: 0.95rem; margin-bottom: 1rem; margin-top: 1.5rem; color: var(--success-color); display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-font"></i> نصوص وعناوين الموقع
        </h4>
        <div class="grid grid-cols-3 gap-3" style="margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label">عنوان قسم الإعلانات والحفلات</label>
                <input type="text" wire:model="site_announcements_title" class="form-control" placeholder="الافتراضي: إعلانات وحفلات">
            </div>
            <div class="form-group">
                <label class="form-label">عنوان قسم الموقع الجغرافي</label>
                <input type="text" wire:model="site_location_title" class="form-control" placeholder="الافتراضي: موقعنا">
            </div>
            <div class="form-group">
                <label class="form-label">نص الفوتر (أسفل الصفحة)</label>
                <input type="text" wire:model="site_footer_text" class="form-control" placeholder="الافتراضي: تم تطوير النظام بواسطة...">
            </div>
        </div>

        {{-- Location Settings --}}
        <h4 style="font-size: 0.95rem; margin-bottom: 1rem; color: var(--info-color); display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-location-dot"></i> إعدادات الموقع الجغرافي
        </h4>
        <div class="grid grid-cols-2 gap-3" style="margin-bottom: 1rem;">
            <div class="form-group">
                <label class="form-label">اسم الموقع الجغرافي</label>
                <input type="text" wire:model="site_location_name" class="form-control" placeholder="مثال: Nala Lounge">
            </div>
            <div class="form-group">
                <label class="form-label">العنوان</label>
                <input type="text" wire:model="site_location_address" class="form-control" placeholder="مثال: الملك عبدالله, الدمام">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3" style="margin-bottom: 1rem;">
            <div class="form-group">
                <label class="form-label">خط العرض (Latitude)</label>
                <input type="text" wire:model="site_location_lat" class="form-control" dir="ltr" placeholder="مثال: 26.4367">
            </div>
            <div class="form-group">
                <label class="form-label">خط الطول (Longitude)</label>
                <input type="text" wire:model="site_location_lng" class="form-control" dir="ltr" placeholder="مثال: 50.1140">
            </div>
            <div class="form-group">
                <label class="form-label">رابط خرائط Google</label>
                <input type="text" wire:model="site_location_url" class="form-control" dir="ltr" placeholder="https://maps.google.com/...">
            </div>
        </div>

        {{-- Feature Toggles --}}
        <h4 style="font-size: 0.95rem; margin-bottom: 1rem; color: var(--warning-color); display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-toggle-on"></i> خصائص الموقع
        </h4>
        <div class="grid grid-cols-2 gap-3" style="margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" wire:model="site_show_events" style="width: 18px; height: 18px;">
                    <span class="form-label" style="margin: 0;">عرض قسم "حفلات هذا الشهر"</span>
                </label>
            </div>
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" wire:model="site_show_packages" style="width: 18px; height: 18px;">
                    <span class="form-label" style="margin: 0;">عرض قسم "باقات الاشتراك والولاء"</span>
                </label>
            </div>
        </div>

        {{-- Self Order Settings --}}
        <h4 style="font-size: 0.95rem; margin-bottom: 1rem; color: var(--primary-color); display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-mobile-screen"></i> إعدادات نظام الطلب الذاتي (الحفلات)
        </h4>
        <div class="grid grid-cols-2 gap-3" style="margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" wire:model="self_order_enabled" style="width: 18px; height: 18px;">
                    <span class="form-label" style="margin: 0;">تفعيل نظام الطلب الذاتي للعملاء</span>
                </label>
            </div>
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" wire:model="self_order_require_geo" style="width: 18px; height: 18px;">
                    <span class="form-label" style="margin: 0;">اشتراط التواجد في الموقع الجغرافي للطلب</span>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3" style="margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label">رسالة الترحيب للعملاء</label>
                <input type="text" wire:model="self_order_welcome_msg" class="form-control" placeholder="مثال: مرحباً بك في نظام الطلب الذاتي">
            </div>
            <div class="form-group">
                <label class="form-label">نطاق القبول الجغرافي (بالمتر)</label>
                <input type="number" wire:model="self_order_geo_radius" class="form-control" placeholder="مثال: 200">
            </div>
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="button" class="btn btn-outline" style="width: 100%; border-color: var(--info-color); color: var(--info-color);" onclick="document.getElementById('qr-modal').style.display = 'flex'">
                    <i class="fa-solid fa-qrcode"></i> عرض وطباعة QR Code الطلب الذاتي
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3" style="margin-bottom: 1.5rem; background: rgba(255,255,255,0.02); padding: 15px; border-radius: 8px; border: 1px dashed var(--border-color);">
            <div class="form-group mb-0">
                <label class="form-label text-warning"><i class="fa-solid fa-location-crosshairs"></i> خط العرض (Latitude) للطلب الذاتي</label>
                <input type="text" wire:model="self_order_geo_lat" class="form-control" dir="ltr" placeholder="مثال: 26.4367">
            </div>
            <div class="form-group mb-0">
                <label class="form-label text-warning"><i class="fa-solid fa-location-crosshairs"></i> خط الطول (Longitude) للطلب الذاتي</label>
                <input type="text" wire:model="self_order_geo_lng" class="form-control" dir="ltr" placeholder="مثال: 50.1140">
            </div>
        </div>

        <button wire:click="saveSiteSettings" class="btn" style="padding: 0.7rem 2rem;" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="saveSiteSettings"><i class="fa-solid fa-save"></i> حفظ الإعدادات</span>
            <span wire:loading wire:target="saveSiteSettings"><i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...</span>
        </button>
    </div>

    {{-- Events Management --}}
    <div class="card mb-4" style="border-top: 3px solid var(--warning-color);">
        <h3 style="margin-bottom: 1.2rem; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; justify-content: space-between;">
            <span><i class="fa-solid fa-calendar-star" style="color: var(--warning-color);"></i> حفلات هذا الشهر ({{ $events->count() }})</span>
            <button wire:click="openEventModal" class="btn btn-sm btn-outline" style="border-color: var(--warning-color); color: var(--warning-color);">
                <i class="fa-solid fa-plus"></i> إضافة حفلة جديدة
            </button>
        </h3>

        @if($events->isEmpty())
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <i class="fa-solid fa-calendar-xmark fa-2x" style="opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                <p>لا توجد حفلات مضافة. اضغط لإضافة حفلة جديدة.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($events as $event)
                    <div style="background: var(--surface-color); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; position: relative; opacity: {{ $event->is_active ? '1' : '0.6' }};">
                        @if($event->artist_image)
                            <div style="height: 120px; background-image: url('{{ asset('storage/' . $event->artist_image) }}'); background-size: cover; background-position: center; border-bottom: 1px solid var(--border-color);"></div>
                        @else
                            <div style="height: 120px; background: rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--border-color);">
                                <i class="fa-solid fa-music fa-2x" style="color: var(--text-secondary); opacity: 0.5;"></i>
                            </div>
                        @endif
                        <div style="padding: 10px;">
                            <h4 style="margin: 0 0 5px 0; font-size: 1rem; color: var(--text-color);">{{ $event->title }}</h4>
                            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 10px;">
                                <i class="fa-regular fa-calendar"></i> {{ $event->event_date }} 
                                @if($event->event_time) | <i class="fa-regular fa-clock"></i> {{ $event->event_time }} @endif
                            </div>
                            <div style="display: flex; gap: 5px; justify-content: space-between; align-items: center;">
                                <div>
                                    <button wire:click="toggleEventActive({{ $event->id }})" class="btn btn-sm btn-ghost" title="{{ $event->is_active ? 'إخفاء' : 'إظهار' }}">
                                        <i class="fa-solid {{ $event->is_active ? 'fa-eye' : 'fa-eye-slash' }}" style="color: {{ $event->is_active ? 'var(--success-color)' : 'var(--danger-color)' }};"></i>
                                    </button>
                                </div>
                                <div style="display: flex; gap: 5px;">
                                    <button wire:click="editEvent({{ $event->id }})" class="btn btn-sm btn-outline" style="border-color: var(--info-color); color: var(--info-color);" title="تعديل">
                                        <i class="fa-solid fa-pen"></i> تعديل
                                    </button>
                                    <button wire:click="deleteEvent({{ $event->id }})" wire:confirm="هل أنت متأكد من حذف هذه الحفلة؟" class="btn btn-sm btn-outline" style="border-color: var(--danger-color); color: var(--danger-color);" title="حذف">
                                        <i class="fa-solid fa-trash"></i> حذف
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Links Management --}}
    <div class="card" style="border-top: 3px solid var(--success-color);">
        <h3 style="margin-bottom: 1.2rem; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; justify-content: space-between;">
            <span><i class="fa-solid fa-link" style="color: var(--success-color);"></i> الروابط ({{ $links->count() }})</span>
        </h3>

        @if($links->isEmpty())
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <i class="fa-solid fa-link-slash fa-3x" style="opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                <p>لا توجد روابط بعد. اضغط "إضافة رابط جديد" لبدء إنشاء صفحة الموقع.</p>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($links as $link)
                    <div style="display: flex; align-items: center; gap: 12px; padding: 0.8rem 1rem; border-radius: var(--radius-sm); border: 1px solid {{ $link->is_active ? 'var(--border-color)' : 'rgba(244,67,54,0.3)' }}; background: {{ $link->is_active ? 'rgba(255,255,255,0.02)' : 'rgba(244,67,54,0.05)' }}; transition: all 0.2s;">
                        {{-- Icon --}}
                        <div style="width: 42px; height: 42px; border-radius: 50%; background: {{ $link->icon_bg_color }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="{{ $link->icon ?? 'fa-solid fa-link' }}" style="color: #fff; font-size: 1rem;"></i>
                        </div>

                        {{-- Info --}}
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: bold; font-size: 0.95rem; {{ !$link->is_active ? 'text-decoration: line-through; opacity: 0.5;' : '' }}">
                                {{ $link->title }}
                            </div>
                            @if($link->subtitle)
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $link->subtitle }}</div>
                            @endif
                            <div style="font-size: 0.75rem; color: var(--info-color); direction: ltr; text-align: right;">{{ Str::limit($link->url, 50) }}</div>
                        </div>

                        {{-- Type Badge --}}
                        <span class="badge {{ $link->type == 'announcement' ? 'badge-pending' : ($link->type == 'social' ? 'badge-prep' : 'badge-active') }}">
                            @switch($link->type)
                                @case('social') تواصل @break
                                @case('announcement') إعلان @break
                                @case('menu') المنيو @break
                                @case('whatsapp') واتساب @break
                                @case('location') الموقع @break
                                @default رابط
                            @endswitch
                        </span>

                        {{-- Sort Order --}}
                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <button wire:click="updateSortOrder({{ $link->id }}, 'up')" class="btn btn-ghost" style="padding: 2px 6px; font-size: 0.7rem;">
                                <i class="fa-solid fa-chevron-up"></i>
                            </button>
                            <span style="font-size: 0.75rem; text-align: center; color: var(--text-secondary);">{{ $link->sort_order }}</span>
                            <button wire:click="updateSortOrder({{ $link->id }}, 'down')" class="btn btn-ghost" style="padding: 2px 6px; font-size: 0.7rem;">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </div>

                        {{-- Actions --}}
                        <div style="display: flex; gap: 6px;">
                            <button wire:click="toggleActive({{ $link->id }})" class="btn btn-sm btn-ghost" title="{{ $link->is_active ? 'إخفاء' : 'إظهار' }}">
                                <i class="fa-solid {{ $link->is_active ? 'fa-eye' : 'fa-eye-slash' }}" style="color: {{ $link->is_active ? 'var(--success-color)' : 'var(--danger-color)' }};"></i>
                            </button>
                            <button wire:click="editLink({{ $link->id }})" class="btn btn-sm btn-ghost" title="تعديل">
                                <i class="fa-solid fa-pen" style="color: var(--info-color);"></i>
                            </button>
                            <button wire:click="deleteLink({{ $link->id }})" wire:confirm="هل أنت متأكد من حذف هذا الرابط؟" class="btn btn-sm btn-ghost" title="حذف">
                                <i class="fa-solid fa-trash" style="color: var(--danger-color);"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Add/Edit Link Modal --}}
    @if($isModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1050; display:flex; justify-content:center; align-items:center;">
            <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); width:90%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between">
                    <h5 class="modal-title">{{ $editingId ? 'تعديل الرابط' : 'إضافة رابط جديد' }}</h5>
                    <button type="button" class="btn-close btn-ghost" wire:click="closeModal" style="font-size:1.5rem; line-height:1;">&times;</button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveLink">
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="form-group">
                                <label class="form-label">العنوان الرئيسي *</label>
                                <input type="text" wire:model="title" class="form-control" placeholder="مثال: سناب شات | snapchat" required>
                                @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">العنوان الفرعي</label>
                                <input type="text" wire:model="subtitle" class="form-control" placeholder="مثال: سناب شات">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">الرابط (URL) *</label>
                            <input type="text" wire:model="url" class="form-control" dir="ltr" placeholder="https://..." required>
                            @error('url') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-3 mb-3">
                            <div class="form-group">
                                <label class="form-label">نوع الرابط</label>
                                <select wire:model="type" class="form-control">
                                    <option value="link">رابط عام</option>
                                    <option value="social">تواصل اجتماعي</option>
                                    <option value="announcement">إعلان / حفلة</option>
                                    <option value="menu">المنيو</option>
                                    <option value="whatsapp">واتساب / خدمة عملاء</option>
                                    <option value="location">الموقع الجغرافي</option>
                                </select>
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label class="form-label">اختر الأيقونة (تلقائياً تحدد اللون)</label>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px;">
                                    @php
                                        $iconsList = [
                                            ['class' => 'fa-brands fa-whatsapp', 'color' => '#25D366', 'title' => 'واتساب'],
                                            ['class' => 'fa-brands fa-snapchat', 'color' => '#FFFC00', 'title' => 'سناب شات'],
                                            ['class' => 'fa-brands fa-instagram', 'color' => '#E1306C', 'title' => 'إنستقرام'],
                                            ['class' => 'fa-brands fa-tiktok', 'color' => '#000000', 'title' => 'تيك توك'],
                                            ['class' => 'fa-brands fa-x-twitter', 'color' => '#000000', 'title' => 'إكس'],
                                            ['class' => 'fa-brands fa-facebook', 'color' => '#1877F2', 'title' => 'فيسبوك'],
                                            ['class' => 'fa-solid fa-phone', 'color' => '#4CAF50', 'title' => 'اتصال'],
                                            ['class' => 'fa-solid fa-location-dot', 'color' => '#E53935', 'title' => 'موقع'],
                                            ['class' => 'fa-solid fa-mug-hot', 'color' => '#8B4513', 'title' => 'قهوة'],
                                            ['class' => 'fa-solid fa-utensils', 'color' => '#D32F2F', 'title' => 'طعام'],
                                            ['class' => 'fa-solid fa-link', 'color' => '#333333', 'title' => 'رابط'],
                                        ];
                                    @endphp
                                    @foreach($iconsList as $ic)
                                        <button type="button" wire:click="selectIcon('{{ $ic['class'] }}', '{{ $ic['color'] }}')" 
                                                class="btn btn-ghost" 
                                                style="width: 40px; height: 40px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; border: 2px solid {{ $icon === $ic['class'] ? 'var(--primary-color)' : 'transparent' }}; background: {{ $icon === $ic['class'] ? 'rgba(212,175,55,0.1)' : 'var(--surface-color)' }};" 
                                                title="{{ $ic['title'] }}">
                                            <i class="{{ $ic['class'] }}" style="color: {{ $ic['color'] }}; font-size: 1.2rem; {{ $ic['class'] == 'fa-brands fa-snapchat' ? 'text-shadow: 0 0 2px #000;' : '' }}"></i>
                                        </button>
                                    @endforeach
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <input type="text" wire:model="icon" class="form-control" dir="ltr" placeholder="أو أدخل كود الأيقونة يدوياً..." style="flex: 1;">
                                    <input type="color" wire:model="icon_bg_color" class="form-control" style="width: 60px; height: 44px; padding: 4px;" title="لون الخلفية">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="form-group">
                                <label class="form-label">ترتيب العرض</label>
                                <input type="number" wire:model="sort_order" class="form-control" min="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">صورة (للإعلانات)</label>
                                <input type="file" wire:model="linkImage" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">وصف إضافي</label>
                            <textarea wire:model="description" class="form-control" rows="2" placeholder="وصف اختياري يظهر تحت الرابط..."></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" wire:model="is_active" style="width: 18px; height: 18px;">
                                <span class="form-label" style="margin: 0;">مفعّل (يظهر في الموقع)</span>
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-secondary">
                            <button type="button" wire:click="closeModal" class="btn btn-ghost">إلغاء</button>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveLink">{{ $editingId ? 'حفظ التعديلات' : 'إضافة الرابط' }}</span>
                                <span wire:loading wire:target="saveLink"><i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Add/Edit Event Modal --}}
    @if($isEventModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1050; display:flex; justify-content:center; align-items:center;">
            <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); width:90%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between">
                    <h5 class="modal-title">{{ $editingEventId ? 'تعديل الحفلة' : 'إضافة حفلة جديدة' }}</h5>
                    <button type="button" class="btn-close btn-ghost" wire:click="closeEventModal" style="font-size:1.5rem; line-height:1;">&times;</button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveEvent">
                        <div class="form-group mb-3">
                            <label class="form-label">عنوان الحفلة / اسم الفنان *</label>
                            <input type="text" wire:model="event_title" class="form-control" placeholder="مثال: حفلة الفنان رابح صقر" required>
                            @error('event_title') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">صورة الفنان أو البوستر</label>
                            <input type="file" wire:model="newEventImage" class="form-control" accept="image/*">
                            <div wire:loading wire:target="newEventImage" class="text-warning small mt-1">جاري رفع الصورة مؤقتاً...</div>
                            @if($newEventImage)
                                <div style="margin-top: 8px;">
                                    <img src="{{ $newEventImage->temporaryUrl() }}" style="width: 100px; border-radius: 8px;">
                                </div>
                            @elseif($existingEventImage)
                                <div style="margin-top: 8px;">
                                    <img src="{{ asset('storage/' . $existingEventImage) }}" style="width: 100px; border-radius: 8px;">
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="form-group">
                                <label class="form-label">تاريخ الحفلة</label>
                                <input type="date" wire:model="event_date" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">وقت الحفلة (اختياري)</label>
                                <input type="time" wire:model="event_time" class="form-control">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">تفاصيل إضافية عن الحفلة</label>
                            <textarea wire:model="event_description" class="form-control" rows="3" placeholder="اكتب أي تفاصيل أخرى عن الحفلة..."></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" wire:model="event_is_active" style="width: 18px; height: 18px;">
                                <span class="form-label" style="margin: 0;">مفعّل (يظهر في الموقع)</span>
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-secondary">
                            <button type="button" wire:click="closeEventModal" class="btn btn-ghost">إلغاء</button>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="saveEvent, newEventImage">
                                <span wire:loading.remove wire:target="saveEvent">{{ $editingEventId ? 'حفظ التعديلات' : 'إضافة الحفلة' }}</span>
                                <span wire:loading wire:target="saveEvent"><i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- QR Code Modal --}}
    <div id="qr-modal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1060; display:none; justify-content:center; align-items:center;">
        <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); width:90%; max-width:400px; padding: 2rem; text-align: center; border-radius: 15px;">
            <div class="d-flex justify-content-between mb-4">
                <h4 style="margin: 0; color: var(--primary-color);">QR Code الطلب الذاتي</h4>
                <button type="button" class="btn-close btn-ghost" onclick="document.getElementById('qr-modal').style.display = 'none'" style="font-size:1.5rem; line-height:1;">&times;</button>
            </div>
            
            <div style="background: white; padding: 20px; border-radius: 10px; display: inline-block; margin-bottom: 1rem;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode(url('/event-order')) }}&margin=10" alt="QR Code" style="width: 250px; height: 250px;">
            </div>
            
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem;" dir="ltr">
                {{ url('/event-order') }}
            </p>

            <button onclick="window.print()" class="btn btn-primary" style="width: 100%;">
                <i class="fa-solid fa-print"></i> طباعة (Ctrl+P)
            </button>
            <style>
                @media print {
                    body * { visibility: hidden; }
                    #qr-modal, #qr-modal * { visibility: visible; }
                    #qr-modal { position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white !important; display: flex !important; justify-content: center; align-items: center; }
                    #qr-modal .btn-close, #qr-modal .btn-primary { display: none !important; }
                    #qr-modal h4 { color: black !important; }
                    #qr-modal p { color: black !important; }
                }
            </style>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                showToast(event[0].message, event[0].type);
            });
        });
    </script>
</div>
