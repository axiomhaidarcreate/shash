<div>
    {{-- ============================================ --}}
    {{-- PREMIUM REPORTS PAGE STYLES                  --}}
    {{-- ============================================ --}}
    <style>
        /* === Page Header with Gradient Accent === */
        .reports-header {
            position: relative;
            padding: 2rem 2.5rem;
            border-radius: var(--radius-lg);
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.08) 0%, rgba(0,0,0,0) 60%);
            border: 1px solid rgba(212, 175, 55, 0.15);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .reports-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .reports-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .reports-header h1 {
            font-size: clamp(1.4rem, 4vw, 2rem);
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.3rem;
            position: relative;
            z-index: 1;
        }
        .reports-header h1 i {
            background: linear-gradient(135deg, var(--primary-color), #f0d060);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-left: 8px;
        }
        .reports-header p {
            color: var(--text-secondary);
            font-size: clamp(0.85rem, 2.5vw, 1rem);
            position: relative;
            z-index: 1;
        }

        /* === Filters Section === */
        .reports-filters {
            display: flex;
            gap: 1.2rem;
            align-items: flex-end;
            flex-wrap: wrap;
            padding: 1.5rem;
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            position: relative;
        }
        .reports-filters::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
            border-radius: var(--radius-md) var(--radius-md) 0 0;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .filter-group label {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .filter-group label i {
            color: var(--primary-color);
            font-size: 0.8rem;
        }

        /* === Category Cards Grid === */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 2rem;
        }
        .category-card {
            position: relative;
            padding: 1.2rem 1rem;
            border-radius: var(--radius-md);
            border: 2px solid var(--border-color);
            background: var(--surface-color);
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            overflow: hidden;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        .category-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.06), transparent);
            opacity: 0;
            transition: opacity 0.35s ease;
        }
        .category-card:hover {
            border-color: rgba(212, 175, 55, 0.4);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.1);
        }
        .category-card:hover::before {
            opacity: 1;
        }
        .category-card.active {
            border-color: var(--primary-color);
            background: rgba(212, 175, 55, 0.08);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.15), inset 0 0 30px rgba(212, 175, 55, 0.04);
        }
        .category-card.active::before {
            opacity: 1;
        }
        .category-card .cat-icon {
            font-size: 1.6rem;
            margin-bottom: 0.6rem;
            display: block;
            color: var(--text-secondary);
            transition: all 0.35s ease;
        }
        .category-card.active .cat-icon {
            color: var(--primary-color);
            transform: scale(1.15);
        }
        .category-card .cat-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-secondary);
            transition: color 0.35s ease;
            position: relative;
            z-index: 1;
        }
        .category-card.active .cat-name {
            color: var(--primary-color);
        }
        .category-card .cat-indicator {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 40%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), #f0d060);
            border-radius: 3px 3px 0 0;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .category-card.active .cat-indicator {
            transform: translateX(-50%) scaleX(1);
        }

        /* === Summary Stats Row === */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            padding: 1.5rem;
            border-radius: var(--radius-md);
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .stat-card .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 0.6rem;
            display: block;
        }
        .stat-card .stat-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        .stat-card .stat-value {
            font-size: clamp(1.4rem, 4vw, 2rem);
            font-weight: 900;
            line-height: 1.2;
        }
        .stat-card .stat-unit {
            font-size: 0.85rem;
            font-weight: 600;
            opacity: 0.7;
            margin-right: 4px;
        }
        .stat-card.sold {
            border-top: 3px solid var(--warning-color);
        }
        .stat-card.sold .stat-icon { color: var(--warning-color); }
        .stat-card.sold .stat-value { color: var(--warning-color); }
        .stat-card.damaged {
            border-top: 3px solid var(--danger-color);
        }
        .stat-card.damaged .stat-icon { color: var(--danger-color); }
        .stat-card.damaged .stat-value { color: var(--danger-color); }
        .stat-card.remaining {
            border-top: 3px solid var(--primary-color);
        }
        .stat-card.remaining .stat-icon { color: var(--primary-color); }
        .stat-card.remaining .stat-value { color: var(--primary-color); }
        .stat-card.remaining .stat-value.negative { color: var(--danger-color); }

        /* === Glass Card for Report Table === */
        .report-table-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            position: relative;
        }
        .report-table-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), #f0d060, var(--primary-color));
            background-size: 200% 100%;
            animation: shimmer 3s ease infinite;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .report-table-header {
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .report-table-header h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .report-table-header h3 i {
            color: var(--primary-color);
        }
        .report-table-header .selected-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(212, 175, 55, 0.12);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: var(--primary-color);
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* === Report Table === */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }
        .report-table thead {
            background: rgba(212, 175, 55, 0.06);
        }
        .report-table th {
            padding: 14px 18px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
        }
        .report-table td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
            transition: background 0.2s ease;
        }
        .report-table tbody tr {
            transition: all 0.2s ease;
        }
        .report-table tbody tr:hover td {
            background: rgba(212, 175, 55, 0.04);
        }
        .report-table tbody tr:last-child td {
            border-bottom: none;
        }
        .report-table .product-name {
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .report-table .product-name .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary-color);
            flex-shrink: 0;
        }
        .report-table .value-sold {
            color: var(--warning-color);
            font-weight: 700;
        }
        .report-table .value-damaged {
            color: var(--danger-color);
            font-weight: 700;
        }
        .report-table .value-remaining {
            font-weight: 800;
        }
        .report-table .value-remaining.positive { color: var(--success-color); }
        .report-table .value-remaining.negative { color: var(--danger-color); }

        /* === Totals Footer Row === */
        .report-table tfoot td {
            padding: 16px 18px;
            font-weight: 800;
            font-size: 1.05rem;
            border-top: 2px solid var(--primary-color);
            background: rgba(212, 175, 55, 0.04);
        }

        /* === Print Buttons Group === */
        .print-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: var(--font-family);
            min-height: 42px;
            touch-action: manipulation;
        }
        .print-btn:hover {
            transform: translateY(-2px);
        }
        .print-btn.a4 {
            background: linear-gradient(135deg, #1976D2, #1565C0);
            color: white;
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }
        .print-btn.a4:hover {
            box-shadow: 0 6px 20px rgba(25, 118, 210, 0.4);
        }
        .print-btn.thermal {
            background: linear-gradient(135deg, #7B1FA2, #6A1B9A);
            color: white;
            box-shadow: 0 4px 12px rgba(123, 31, 162, 0.3);
        }
        .print-btn.thermal:hover {
            box-shadow: 0 6px 20px rgba(123, 31, 162, 0.4);
        }

        /* === Empty State === */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }
        .empty-state i {
            font-size: 4rem;
            opacity: 0.15;
            margin-bottom: 1.5rem;
            display: block;
            color: var(--primary-color);
        }
        .empty-state p {
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* === Animations === */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-in {
            animation: fadeInUp 0.5s ease forwards;
        }
        .animate-in-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .animate-in-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .animate-in-delay-3 { animation-delay: 0.3s; opacity: 0; }

        /* === Light Theme Overrides === */
        body.light-theme .reports-header {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.06) 0%, rgba(255,255,255,0) 60%);
        }
        body.light-theme .category-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        body.light-theme .category-card.active {
            background: rgba(212, 175, 55, 0.06);
        }
        body.light-theme .report-table thead {
            background: rgba(212, 175, 55, 0.05);
        }
        body.light-theme .report-table tbody tr:hover td {
            background: rgba(212, 175, 55, 0.03);
        }
        body.light-theme .stat-card {
            background: #fff;
        }

        /* === Responsive === */
        @media (max-width: 768px) {
            .reports-header { padding: 1.2rem 1.5rem; }
            .reports-filters { padding: 1rem; gap: 0.8rem; }
            .categories-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; }
            .category-card { padding: 0.9rem 0.7rem; }
            .category-card .cat-icon { font-size: 1.3rem; margin-bottom: 0.4rem; }
            .category-card .cat-name { font-size: 0.85rem; }
            .stats-row { grid-template-columns: repeat(3, 1fr); gap: 0.6rem; }
            .stat-card { padding: 1rem 0.6rem; }
            .stat-card .stat-value { font-size: clamp(1.1rem, 3.5vw, 1.5rem); }
            .report-table-header { padding: 1rem 1.2rem; }
            .report-table th { padding: 10px 12px; font-size: 0.78rem; }
            .report-table td { padding: 10px 12px; font-size: 0.88rem; }
            .print-actions { gap: 6px; }
            .print-btn { padding: 0.5rem 0.9rem; font-size: 0.85rem; }
        }
        @media (max-width: 480px) {
            .stats-row { grid-template-columns: 1fr; }
            .report-table-header { flex-direction: column; align-items: flex-start; }
        }

        /* === Print Overrides === */
        @media print {
            .reports-header, .reports-filters, .categories-grid, .print-actions, .no-print { display: none !important; }
            .report-table-card { border: 1px solid #ccc !important; box-shadow: none !important; }
            .report-table-card::before { display: none; }
            .report-table th { color: #333 !important; background: #f5f5f5 !important; }
            .report-table td { color: #333 !important; }
            .stat-card { border: 1px solid #ddd !important; box-shadow: none !important; }
        }
    </style>

    <div style="margin-bottom: 1.5rem;" class="no-print">
        <a href="{{ route('admin.purchases') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 0.5rem 1rem; border-radius: var(--radius-sm);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة المشتريات والمخزون
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- PAGE HEADER                                  --}}
    {{-- ============================================ --}}
    <div class="reports-header animate-in">
        <h1><i class="fa-solid fa-chart-pie"></i> التقارير</h1>
        <p>تقارير مفصّلة عن المبيعات والمخزون حسب التصنيف والتاريخ</p>
    </div>

    {{-- ============================================ --}}
    {{-- FILTERS ROW                                  --}}
    {{-- ============================================ --}}
    <div class="reports-filters no-print animate-in animate-in-delay-1">
        <div class="filter-group">
            <label><i class="fa-regular fa-calendar"></i> من تاريخ</label>
            <input type="date" wire:model.live="startDate" class="form-control" style="width: auto; min-width: 160px;">
        </div>
        <div class="filter-group">
            <label><i class="fa-regular fa-calendar"></i> إلى تاريخ</label>
            <input type="date" wire:model.live="endDate" class="form-control" style="width: auto; min-width: 160px;">
        </div>
        
        <div class="filter-group">
            <label><i class="fa-solid fa-clock"></i> من ساعة</label>
            <input type="time" wire:model.live="startTime" class="form-control" style="width: auto; min-width: 120px;">
        </div>
        <div class="filter-group">
            <label><i class="fa-solid fa-clock"></i> إلى ساعة</label>
            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="time" wire:model.live="endTime" class="form-control" style="width: auto; min-width: 120px;">
                @if($startTime || $endTime)
                    <button wire:click="$set('startTime', '')" onclick="document.querySelector('[wire\\:model\\.live=endTime]').dispatchEvent(new Event('change'))" style="background: none; border: none; color: var(--danger-color); cursor: pointer; padding: 4px;" title="مسح الساعات">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                @endif
            </div>
        </div>

        <div class="filter-group" style="align-self: flex-end;">
            <span style="background: rgba(212,175,55,0.12); border: 1px solid rgba(212,175,55,0.3); color: var(--primary-color); padding: 0.45rem 1rem; border-radius: 20px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; min-height: 44px;">
                <i class="fa-solid fa-clock"></i>
                الفترة: من {{ $startDate }}{{ $startTime ? ' ' . $startTime : '' }} إلى {{ $endDate }}{{ $endTime ? ' ' . $endTime : '' }}
            </span>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- CATEGORY SELECTION CARDS                     --}}
    {{-- ============================================ --}}
    <div class="no-print animate-in animate-in-delay-2">
        <div style="margin-bottom: 0.8rem; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-layer-group" style="color: var(--primary-color);"></i>
            <span style="font-weight: 700; font-size: 0.95rem;">اختر التصنيف:</span>
        </div>
        <div class="categories-grid">
            @foreach($categories as $cat)
                @php
                    $catIcon = match($cat->name) {
                        'الشيشة', 'معسل', 'الشيش' => 'fa-solid fa-wind',
                        'رؤوس الشيشة', 'رأس شيشة', 'رؤوس شيشة' => 'fa-solid fa-fire-burner',
                        'طبقات إضافية' => 'fa-solid fa-layer-group',
                        'مشروبات' => 'fa-solid fa-mug-hot',
                        'مستلزمات' => 'fa-solid fa-screwdriver-wrench',
                        default => 'fa-solid fa-cube',
                    };
                    $displayName = in_array($cat->name, ['معسل', 'الشيش', 'الشيشة']) ? 'الشيشة' : $cat->name;
                @endphp
                <button wire:click="$set('selectedCategory', '{{ $cat->name }}')"
                        class="category-card {{ $selectedCategory === $cat->name ? 'active' : '' }}">
                    <i class="{{ $catIcon }} cat-icon"></i>
                    <span class="cat-name">{{ $displayName }}</span>
                    <div class="cat-indicator"></div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SUMMARY STATISTICS                           --}}
    {{-- ============================================ --}}
    @if(!$ticketData)
    <div class="stats-row animate-in animate-in-delay-3" wire:key="stats-{{ $selectedCategory }}-{{ $startDate }}-{{ $endDate }}">
        <div class="stat-card sold">
            <i class="fa-solid fa-fire stat-icon"></i>
            <div class="stat-label">إجمالي المُباع</div>
            <div class="stat-value">
                {{ number_format($totalSold) }}
                <span class="stat-unit">{{ $unit }}</span>
            </div>
        </div>
        <div class="stat-card damaged">
            <i class="fa-solid fa-trash-can stat-icon"></i>
            <div class="stat-label">إجمالي التالف</div>
            <div class="stat-value">
                {{ number_format($totalDamaged) }}
                <span class="stat-unit">{{ $unit }}</span>
            </div>
        </div>
        <div class="stat-card remaining">
            <i class="fa-solid fa-boxes-stacked stat-icon"></i>
            <div class="stat-label">المتبقي الكُلي</div>
            <div class="stat-value {{ $totalRemaining < 0 ? 'negative' : '' }}">
                {{ number_format($totalRemaining) }}
                <span class="stat-unit">{{ $unit }}</span>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DETAILED REPORT TABLE                        --}}
    {{-- ============================================ --}}
    <div class="report-table-card" wire:key="table-{{ $selectedCategory }}-{{ $startDate }}-{{ $endDate }}">
        <div class="report-table-header">
            <h3>
                <i class="fa-solid fa-table-list"></i>
                تقرير المبيعات المفصل
            </h3>
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="selected-badge">
                    <i class="fa-solid fa-tag"></i>
                    {{ in_array($selectedCategory, ['معسل', 'الشيش', 'الشيشة']) ? 'الشيشة' : $selectedCategory }}
                </span>

                <div class="print-actions">
                    <a href="/invoice/category-print?category={{ urlencode($selectedCategory) }}&period=custom&start_date={{ $startDate }}&end_date={{ $endDate }}{{ $startTime ? '&start_time='.$startTime : '' }}{{ $endTime ? '&end_time='.$endTime : '' }}"
                       target="_blank"
                       class="print-btn a4"
                       title="طباعة تقرير A4">
                        <i class="fa-solid fa-file-lines"></i> طباعة A4
                    </a>
                    <a href="/invoice/category-print-thermal?category={{ urlencode($selectedCategory) }}&period=custom&start_date={{ $startDate }}&end_date={{ $endDate }}{{ $startTime ? '&start_time='.$startTime : '' }}{{ $endTime ? '&end_time='.$endTime : '' }}"
                       target="_blank"
                       class="print-btn thermal"
                       title="طباعة حرارية">
                        <i class="fa-solid fa-receipt"></i> حراري
                    </a>
                </div>
            </div>
        </div>

        @if($reportRows->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-clipboard-list"></i>
                <p>لا توجد بيانات لهذا التصنيف في الفترة المحددة</p>
            </div>
        @else
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>المنتج</th>
                            <th>المُباع ({{ $unit }})</th>
                            <th>التالف ({{ $unit }})</th>
                            <th>المتبقي ({{ $unit }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportRows as $index => $row)
                            <tr>
                                <td style="color: var(--text-secondary); font-weight: 600;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="product-name">
                                        <span class="dot"></span>
                                        {{ $row['name'] }}
                                    </div>
                                </td>
                                <td class="value-sold">{{ number_format($row['sold']) }}</td>
                                <td class="value-damaged">{{ number_format($row['damaged']) }}</td>
                                <td>
                                    <span class="value-remaining {{ $row['remaining'] >= 0 ? 'positive' : 'negative' }}">
                                        {{ number_format($row['remaining']) }}
                                        @if($row['remaining'] < 0) <i class="fa-solid fa-triangle-exclamation" style="font-size: 0.8rem;"></i> @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="color: var(--primary-color);">
                                <i class="fa-solid fa-calculator"></i> الإجمالي
                            </td>
                            <td class="value-sold">{{ number_format($totalSold) }}</td>
                            <td class="value-damaged">{{ number_format($totalDamaged) }}</td>
                            <td>
                                <span class="value-remaining {{ $totalRemaining >= 0 ? 'positive' : 'negative' }}">
                                    {{ number_format($totalRemaining) }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
    @else
    {{-- ============================================ --}}
    {{-- TICKET RECEPTION STATS (WHEN TICKETS CATEGORY IS SELECTED) --}}
    {{-- ============================================ --}}
    <div class="animate-in animate-in-delay-3" wire:key="ticket-stats-{{ $selectedCategory }}-{{ $startDate }}-{{ $endDate }}">
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="font-size: clamp(0.95rem, 3vw, 1.15rem); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                    <i class="fa-solid fa-ticket" style="color: #9C27B0;"></i> إحصائيات تذاكر الدخول للمرحلة المحددة
                </h2>
                
                <div class="print-actions">
                    <a href="/invoice/ticket-report-print?start_date={{ $startDate }}&end_date={{ $endDate }}{{ $startTime ? '&start_time='.$startTime : '' }}{{ $endTime ? '&end_time='.$endTime : '' }}" target="_blank" class="print-btn a4" style="background: linear-gradient(135deg, #9C27B0, #7B1FA2); box-shadow: 0 4px 12px rgba(156, 39, 176, 0.3);" title="تقرير تذاكر الدخول A4">
                        <i class="fa-solid fa-file-lines"></i> طباعة تقرير (A4)
                    </a>
                    <a href="/invoice/ticket-report-print-thermal?start_date={{ $startDate }}&end_date={{ $endDate }}{{ $startTime ? '&start_time='.$startTime : '' }}{{ $endTime ? '&end_time='.$endTime : '' }}" target="_blank" class="print-btn thermal" title="تقرير تذاكر الدخول حراري">
                        <i class="fa-solid fa-receipt"></i> طباعة تقرير (حراري)
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-4" style="gap: 1rem;">
                {{-- Total Tickets --}}
                <div class="card" style="text-align: center; border-top: 3px solid #9C27B0; padding: clamp(0.7rem, 2vw, 1rem);">
                    <i class="fa-solid fa-ticket" style="color: #9C27B0; margin-bottom: 0.5rem; font-size: clamp(1rem, 3vw, 1.5rem);"></i>
                    <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">إجمالي التذاكر</h3>
                    <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: #9C27B0;">{{ $ticketData['stats']['total'] }}</p>
                </div>
                {{-- Active Tickets --}}
                <div class="card" style="text-align: center; border-top: 3px solid var(--warning-color); padding: clamp(0.7rem, 2vw, 1rem);">
                    <i class="fa-solid fa-hourglass-half" style="color: var(--warning-color); margin-bottom: 0.5rem; font-size: clamp(1rem, 3vw, 1.5rem);"></i>
                    <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">في الانتظار (نشطة)</h3>
                    <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: var(--warning-color);">{{ $ticketData['stats']['active'] }}</p>
                </div>
                {{-- Used Tickets --}}
                <div class="card" style="text-align: center; border-top: 3px solid var(--success-color); padding: clamp(0.7rem, 2vw, 1rem);">
                    <i class="fa-solid fa-circle-check" style="color: var(--success-color); margin-bottom: 0.5rem; font-size: clamp(1rem, 3vw, 1.5rem);"></i>
                    <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">مُستخدمة</h3>
                    <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: var(--success-color);">{{ $ticketData['stats']['used'] }}</p>
                </div>
                {{-- Cancelled Tickets --}}
                <div class="card" style="text-align: center; border-top: 3px solid var(--danger-color); padding: clamp(0.7rem, 2vw, 1rem);">
                    <i class="fa-solid fa-ban" style="color: var(--danger-color); margin-bottom: 0.5rem; font-size: clamp(1rem, 3vw, 1.5rem);"></i>
                    <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">مُلغاة</h3>
                    <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: var(--danger-color);">{{ $ticketData['stats']['cancelled'] }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2" style="gap: 1.5rem; margin-bottom: 2rem;">
            {{-- Template Stats --}}
            <div class="report-table-card">
                <div class="report-table-header" style="background: rgba(156, 39, 176, 0.05);">
                    <h3 style="color: #9C27B0;"><i class="fa-solid fa-list-alt"></i> تفصيل حسب القالب</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>اسم القالب</th>
                                <th style="width: 100px;">العدد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ticketData['templateStats'] as $name => $count)
                            <tr>
                                <td class="product-name"><div class="dot" style="background: #9C27B0;"></div>{{ $name }}</td>
                                <td class="value-sold" style="color: #9C27B0;">{{ $count }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 20px; color: var(--text-secondary);">لا توجد بيانات للقوالب</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Product Stats --}}
            <div class="report-table-card">
                <div class="report-table-header" style="background: rgba(33, 150, 243, 0.05);">
                    <h3 style="color: #9C27B0;"><i class="fa-solid fa-box"></i> المنتجات المحجوزة</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th style="width: 100px;">العدد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ticketData['productStats'] as $name => $count)
                            <tr>
                                <td class="product-name"><div class="dot" style="background: var(--info-color);"></div>{{ $name }}</td>
                                <td class="value-sold" style="color: var(--info-color);">{{ $count }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 20px; color: var(--text-secondary);">لا توجد منتجات محجوزة</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
