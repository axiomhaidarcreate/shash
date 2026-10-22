<x-layouts.admin>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-top: 1.5rem; margin-bottom: 2.5rem;">
        <div>
            <h1 style="font-size: clamp(1.4rem, 4vw, 2rem); font-weight: 800; margin-bottom: 0.3rem;">
                <i class="fa-solid fa-kitchen-set" style="color: var(--primary-color);"></i> بوابة المطابخ
            </h1>
            <p style="font-size: 1rem; color: var(--text-secondary); margin: 0;">اختر المطبخ المطلوب للانتقال إلى الشاشة التشغيلية ومتابعة الطلبات المباشرة</p>
        </div>
        @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('admin.kitchens') }}" class="btn btn-outline" style="border-radius: var(--radius-sm);">
                <i class="fa-solid fa-sliders"></i> إدارة وتخصيص المطابخ
            </a>
        @endif
    </div>

    @php
        $kitchenMeta = [
            'shisha' => [
                'icon'  => 'fa-solid fa-wind',
                'color' => 'var(--primary-color)',
                'bg'    => 'rgba(212, 175, 55, 0.15)',
                'desc'  => 'شاشة متابعة وتجهيز طلبات المعسل والشيشة',
                'route' => route('kitchen'),
            ],
            'barista' => [
                'icon'  => 'fa-solid fa-mug-hot',
                'color' => '#2196F3',
                'bg'    => 'rgba(33, 150, 243, 0.15)',
                'desc'  => 'شاشة تجهيز المشروبات الباردة والساخنة والقهوة',
                'route' => route('barista'),
            ],
            'restaurant' => [
                'icon'  => 'fa-solid fa-bowl-food',
                'color' => '#4CAF50',
                'bg'    => 'rgba(76, 175, 80, 0.15)',
                'desc'  => 'شاشة تجهيز الوجبات والأطباق الرئيسية والمقبلات',
                'route' => route('restaurant'),
            ],
            'hloyat' => [
                'icon'  => 'fa-solid fa-cake-candles',
                'color' => '#E91E63',
                'bg'    => 'rgba(233, 30, 99, 0.15)',
                'desc'  => 'شاشة متابعة وتجهيز الحلويات والمخبوزات',
                'route' => route('dynamic.kitchen', ['slug' => 'hloyat']),
            ],
            'ksm_almndfat' => [
                'icon'  => 'fa-solid fa-spray-can-sparkles',
                'color' => '#00BCD4',
                'bg'    => 'rgba(0, 188, 212, 0.15)',
                'desc'  => 'قسم المنظفات والتجهيز والمستلزمات',
                'route' => route('dynamic.kitchen', ['slug' => 'ksm_almndfat']),
            ],
        ];

        // Ensure default 3 primary kitchens are always represented if not yet seeded
        $kitchenList = collect($kitchens);
        $standardSlugs = ['shisha' => 'مطبخ الشيشة', 'barista' => 'مطبخ الباريستا', 'restaurant' => 'مطبخ المطعم'];
        foreach ($standardSlugs as $sSlug => $sName) {
            if (!$kitchenList->contains('slug', $sSlug)) {
                $kitchenList->push((object)[
                    'name' => $sName,
                    'slug' => $sSlug,
                    'is_active' => true,
                ]);
            }
        }
    @endphp

    <!-- Kitchens Grid (Responsive Cards) -->
    <div class="grid grid-cols-3 mb-8" style="gap: 1.5rem; max-width: 1100px; margin: 0 auto 3rem auto;">
        @foreach($kitchenList as $k)
            @php
                $meta = $kitchenMeta[$k->slug] ?? [
                    'icon'  => 'fa-solid fa-kitchen-set',
                    'color' => '#FF9800',
                    'bg'    => 'rgba(255, 152, 0, 0.15)',
                    'desc'  => 'شاشة تشغيلية خاصة بقسم ' . $k->name,
                    'route' => route('dynamic.kitchen', ['slug' => $k->slug]),
                ];
                $pending = $pendingCounts[$k->slug] ?? 0;
            @endphp
            <a href="{{ $meta['route'] }}" class="card kitchen-portal-card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: space-between; gap: 16px; padding: 2.2rem 1.5rem; text-align: center; border-radius: var(--radius-md); position: relative; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                <!-- Top Accent Line -->
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: {{ $meta['color'] }};"></div>

                <!-- Icon -->
                <div style="width: 76px; height: 76px; border-radius: 50%; background: {{ $meta['bg'] }}; color: {{ $meta['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15); transition: transform 0.3s ease;">
                    <i class="{{ $meta['icon'] }}"></i>
                </div>

                <!-- Title & Description -->
                <div>
                    <h3 style="color: var(--text-primary); font-size: 1.35rem; font-weight: 800; margin: 0 0 6px 0;">
                        {{ $k->name }}
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5; margin: 0; min-height: 2.6rem;">
                        {{ $meta['desc'] }}
                    </p>
                </div>

                <!-- Live Status Badge -->
                <div style="width: 100%; padding-top: 10px; border-top: 1px solid var(--border-color); display: flex; justify-content: center; align-items: center; gap: 8px;">
                    @if($pending > 0)
                        <span class="badge badge-pending" style="font-size: 0.8rem; padding: 0.35rem 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-bell fa-shake"></i> {{ $pending }} طلبات قيد الانتظار
                        </span>
                    @else
                        <span class="badge badge-active" style="font-size: 0.8rem; padding: 0.35rem 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> مباشر وشغال
                        </span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>

    <style>
        .kitchen-portal-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary-color) !important;
            box-shadow: 0 12px 30px rgba(212, 175, 55, 0.25) !important;
        }
        .kitchen-portal-card:hover > div:first-of-type {
            transform: scale(1.08);
        }
    </style>
</x-layouts.admin>
