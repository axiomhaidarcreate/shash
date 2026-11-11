<div>
    <div class="page-header" style="margin-top: 1.5rem; margin-bottom: 2.5rem;">
        <h1 style="font-size: clamp(1.4rem, 4vw, 2rem); font-weight: 800; margin-bottom: 0.3rem;">
            <i class="fa-solid fa-cart-shopping" style="color: var(--primary-color);"></i> بوابة المشتريات والمخزون
        </h1>
        <p style="font-size: 1rem; color: var(--text-secondary); margin: 0;">اختر القسم المطلوب للانتقال إلى شاشته التشغيلية ومتابعة العمليات</p>
    </div>

    @php
        $cards = [
            [
                'title' => 'الرئيسية',
                'desc' => 'الانتقال إلى لوحة التحكم الرئيسية ومتابعة الإحصائيات العامة للمنشأة',
                'icon' => 'fa-solid fa-house',
                'color' => 'var(--primary-color)',
                'bg' => 'rgba(212, 175, 55, 0.15)',
                'route' => route('admin'),
                'badge' => 'لوحة التحكم',
                'badge_icon' => 'fa-solid fa-gauge-high',
                'role' => 'admin',
            ],
            [
                'title' => 'فواتير المشتريات',
                'desc' => 'إدارة وتسجيل وعرض فواتير الشراء ومدفوعات المشتريات وتفاصيل الدفع',
                'icon' => 'fa-solid fa-file-invoice-dollar',
                'color' => '#4CAF50',
                'bg' => 'rgba(76, 175, 80, 0.15)',
                'route' => route('admin.purchases.invoices'),
                'badge' => ($invoicesCount > 0 ? $invoicesCount . ' فاتورة مسجلة' : 'إدارة الفواتير'),
                'badge_icon' => 'fa-solid fa-receipt',
                'role' => 'admin',
            ],
            [
                'title' => 'المصروفات',
                'desc' => 'تسجيل ومتابعة المصروفات التشغيلية والنفقات اليومية وتصنيفاتها',
                'icon' => 'fa-solid fa-wallet',
                'color' => '#F44336',
                'bg' => 'rgba(244, 67, 54, 0.15)',
                'route' => route('admin.expenses'),
                'badge' => ($expensesCount > 0 ? $expensesCount . ' عملية صرف' : 'المصروفات والنفقات'),
                'badge_icon' => 'fa-solid fa-money-bill-wave',
                'role' => 'admin',
            ],
            [
                'title' => 'الموردين',
                'desc' => 'إدارة بيانات الموردين وأرقام التواصل ومتابعة كشوفات الحساب والأرصدة',
                'icon' => 'fa-solid fa-users',
                'color' => '#2196F3',
                'bg' => 'rgba(33, 150, 243, 0.15)',
                'route' => route('admin.purchases.suppliers'),
                'badge' => ($suppliersCount > 0 ? $suppliersCount . ' مورد مسجل' : 'سجل الموردين'),
                'badge_icon' => 'fa-solid fa-truck-field',
                'role' => 'admin',
            ],
            [
                'title' => 'المخزون والمنتجات',
                'desc' => 'إدارة قائمة الأصناف والمنتجات والأسعار وتحديد مستويات وتنبيهات المخزون',
                'icon' => 'fa-solid fa-box-open',
                'color' => '#9C27B0',
                'bg' => 'rgba(156, 39, 176, 0.15)',
                'route' => route('admin.products'),
                'badge' => ($productsCount > 0 ? $productsCount . ' صنف ومنتج' : 'قائمة المنتجات'),
                'badge_icon' => 'fa-solid fa-cubes',
                'role' => 'admin',
            ],
            [
                'title' => 'حركة المخزون',
                'desc' => 'متابعة سجل حركات الإدخال والإخراج وتوالف المخزون ورصيد المستودع',
                'icon' => 'fa-solid fa-boxes-stacked',
                'color' => '#FF9800',
                'bg' => 'rgba(255, 152, 0, 0.15)',
                'route' => route('admin.inventory'),
                'badge' => 'سجل الحركات والتوالف',
                'badge_icon' => 'fa-solid fa-dolly',
                'role' => 'admin|inventory_staff',
            ],
            [
                'title' => 'الجرد الفعلي',
                'desc' => 'إجراء عمليات الجرد الدوري الفعلي للمستودع ومطابقتها وتسوية الفروقات',
                'icon' => 'fa-solid fa-clipboard-check',
                'color' => '#00BCD4',
                'bg' => 'rgba(0, 188, 212, 0.15)',
                'route' => route('admin.physical_inventory'),
                'badge' => 'الجرد والتسوية',
                'badge_icon' => 'fa-solid fa-list-check',
                'role' => 'admin|inventory_staff',
            ],
            [
                'title' => 'طلبات المشتريات اليومية',
                'desc' => 'متابعة وتأكيد طلبات النواقص والمشتريات اليومية الواردة من كافة الأقسام',
                'icon' => 'fa-solid fa-cart-arrow-down',
                'color' => '#E91E63',
                'bg' => 'rgba(233, 30, 99, 0.15)',
                'route' => route('admin.daily_purchases_requests'),
                'badge' => ($pendingPurchasesCount > 0 ? $pendingPurchasesCount . ' طلب بانتظار الشراء' : 'جميع الطلبات مكتملة'),
                'badge_icon' => ($pendingPurchasesCount > 0 ? 'fa-solid fa-bell fa-shake' : 'fa-solid fa-check-double'),
                'badge_is_pending' => ($pendingPurchasesCount > 0),
                'role' => 'admin|daily_purchases_staff',
            ],
            [
                'title' => 'التقارير',
                'desc' => 'عرض تقارير شاملة وتحليلات بيانية مفصلة عن حركة المبيعات والمشتريات',
                'icon' => 'fa-solid fa-chart-pie',
                'color' => '#009688',
                'bg' => 'rgba(0, 150, 136, 0.15)',
                'route' => route('admin.reports'),
                'badge' => 'التقارير والإحصائيات',
                'badge_icon' => 'fa-solid fa-chart-line',
                'role' => 'admin',
            ],
        ];
    @endphp

    <!-- Purchases & Inventory Grid (Responsive Cards) -->
    <div class="grid grid-cols-3 mb-8" style="gap: 1.5rem; max-width: 1200px; margin: 0 auto 3rem auto;">
        @foreach($cards as $card)
            @if(auth()->user()->hasAnyRole(explode('|', $card['role'])))
                <a href="{{ $card['route'] }}" class="card purchases-portal-card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: space-between; gap: 16px; padding: 2.2rem 1.5rem; text-align: center; border-radius: var(--radius-md); position: relative; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;">
                    <!-- Top Accent Line -->
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: {{ $card['color'] }};"></div>

                    <!-- Icon -->
                    <div style="width: 76px; height: 76px; border-radius: 50%; background: {{ $card['bg'] }}; color: {{ $card['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15); transition: transform 0.3s ease;">
                        <i class="{{ $card['icon'] }}"></i>
                    </div>

                    <!-- Title & Description -->
                    <div>
                        <h3 style="color: var(--text-primary); font-size: 1.35rem; font-weight: 800; margin: 0 0 6px 0;">
                            {{ $card['title'] }}
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5; margin: 0; min-height: 2.6rem;">
                            {{ $card['desc'] }}
                        </p>
                    </div>

                    <!-- Live Status / Action Badge -->
                    <div style="width: 100%; padding-top: 10px; border-top: 1px solid var(--border-color); display: flex; justify-content: center; align-items: center; gap: 8px;">
                        @if(!empty($card['badge_is_pending']))
                            <span class="badge badge-pending" style="font-size: 0.8rem; padding: 0.35rem 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="{{ $card['badge_icon'] }}"></i> {{ $card['badge'] }}
                            </span>
                        @else
                            <span class="badge badge-active" style="font-size: 0.8rem; padding: 0.35rem 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="{{ $card['badge_icon'] }}"></i> {{ $card['badge'] }}
                            </span>
                        @endif
                    </div>
                </a>
            @endif
        @endforeach
    </div>

    <style>
        .purchases-portal-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary-color) !important;
            box-shadow: 0 12px 30px rgba(212, 175, 55, 0.25) !important;
        }
        .purchases-portal-card:hover > div:first-of-type {
            transform: scale(1.08);
        }
    </style>
</div>
