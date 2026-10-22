<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير التالف المعتمد - {{ $targetDate->format('Y-m-d') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: #fff;
            color: #000;
            margin: 0;
            padding: 20px 30px;
            font-size: 12px;
            direction: rtl;
        }

        /* ───── Header ───── */
        .report-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .report-header h1 {
            margin: 0 0 4px;
            font-size: 20px;
            font-weight: 800;
        }
        .report-header .sub {
            font-size: 11px;
            color: #444;
        }
        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 6px;
        }

        /* ───── Summary row ───── */
        .summary-box {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .summary-item {
            border: 1px solid #999;
            border-radius: 4px;
            padding: 4px 10px;
            text-align: center;
            flex: 1;
            min-width: 80px;
        }
        .summary-item .label { font-size: 10px; color: #555; }
        .summary-item .value { font-size: 16px; font-weight: 800; }

        /* ───── Section title ───── */
        .section-title {
            font-size: 13px;
            font-weight: 800;
            border-right: 4px solid #000;
            padding-right: 8px;
            margin: 14px 0 8px;
        }

        /* ───── Main compact table ───── */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        table.main-table thead tr {
            background: #222;
            color: #fff;
        }
        table.main-table th {
            padding: 6px 8px;
            border: 1px solid #555;
            font-weight: 800;
            text-align: center;
            font-size: 11px;
        }
        table.main-table td {
            padding: 5px 8px;
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
        }
        table.main-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* ───── Product summary table ───── */
        table.summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 12px;
        }
        table.summary-table th {
            background: #333;
            color: #fff;
            padding: 5px 10px;
            border: 1px solid #555;
            text-align: center;
        }
        table.summary-table td {
            padding: 5px 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        table.summary-table tbody tr:nth-child(even) { background: #f9f9f9; }

        /* ───── Footer ───── */
        .print-footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #666;
        }

        /* ───── Screen Only (preview bar) ───── */
        .screen-only {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .screen-only button {
            font-family: 'Tajawal', sans-serif;
            padding: 10px 28px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-print { background: #1a1a1a; color: #d4af37; }
        .btn-close { background: #555; color: #fff; }

        @media print {
            .screen-only { display: none !important; }
            body { padding: 10px 15px; }
            @page { size: A4; margin: 10mm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="screen-only">
        <button class="btn-print" onclick="window.print()">🖨️ طباعة A4</button>
        <button class="btn-close" onclick="window.close()">✕ إغلاق</button>
    </div>

    <div class="report-header">
        @if(isset($customTitle) && $customTitle)
            <h1>{{ $customTitle }}</h1>
        @else
            <h1>{{ $loungeName }}</h1>
        @endif
        <div class="sub">تقرير التالف المعتمد</div>
        <div class="sub" style="font-size: 14px; color: #555;">التاريخ: {{ $targetDate->format('Y/m/d') }}</div>
        <div class="sub" style="font-size: 12px; color: #777;">وقت الطباعة: {{ now()->format('Y/m/d h:i A') }}</div>
    </div>

    <div class="report-meta">
        <span><strong>التاريخ:</strong> {{ $targetDate->format('Y/m/d') }}</span>
        <span><strong>وقت الطباعة:</strong> {{ now()->format('h:i A') }}</span>
        <span><strong>إجمالي الحركات:</strong> {{ $wasteEntries->count() }}</span>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="label">إجمالي تالف المعسل</div>
            <div class="value">{{ number_format($totalWasteShisha) }} ج</div>
        </div>
        <div class="summary-item">
            <div class="label">إجمالي تالف المستلزمات</div>
            <div class="value">{{ number_format($totalWasteAcc) }} حبة</div>
        </div>
        <div class="summary-item">
            <div class="label">إجمالي العمليات</div>
            <div class="value">{{ $wasteEntries->count() }}</div>
        </div>
    </div>

    <div class="section-title">📋 تفصيل حركات الإتلاف</div>

    @if($wasteEntries->isEmpty())
        <p style="text-align:center; color:#999; padding: 20px 0;">لا توجد حركات إتلاف مسجلة لهذا اليوم.</p>
    @else
    <table class="main-table">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:25%">الصنف</th>
                <th style="width:15%">الفئة</th>
                <th style="width:15%">الكمية</th>
                <th style="width:15%">الموظف (المدخل)</th>
                <th style="width:15%">الوقت</th>
                <th style="width:10%">ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wasteEntries as $index => $entry)
                @php
                    $isShisha = $entry->product->category === 'الشيش';
                    $unit = $isShisha ? 'ج' : 'حبة';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight:bold;">{{ $entry->product->name }}</td>
                    <td>{{ $entry->product->category }}</td>
                    <td style="font-weight:bold; color:#000; font-size:13px;">{{ number_format($entry->quantity_grams) }} {{ $unit }}</td>
                    <td>{{ $entry->creator->name ?? 'غير معروف' }}</td>
                    <td>{{ $entry->created_at->format('h:i A') }}</td>
                    <td style="font-size:10px;">{{ $entry->notes ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(!empty($wasteByProduct))
    <div class="section-title" style="margin-top: 18px;">📦 إجمالي التالف لكل صنف</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>#</th>
                <th>الصنف</th>
                <th>الفئة</th>
                <th>إجمالي التالف</th>
            </tr>
        </thead>
        <tbody>
            @php $idx = 1; @endphp
            @foreach($wasteByProduct as $productName => $data)
                @php
                    $isShisha = $data['category'] === 'الشيش';
                    $unit = $isShisha ? 'ج' : 'حبة';
                @endphp
                <tr>
                    <td>{{ $idx++ }}</td>
                    <td style="text-align:right; font-weight:700;">{{ $productName }}</td>
                    <td>{{ $data['category'] }}</td>
                    <td style="font-weight:800; font-size:13px; color:#d32f2f;">{{ number_format($data['quantity']) }} {{ $unit }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="print-footer">
        <span>{{ $loungeName }} — نظام إدارة المخزون</span>
        <span>تاريخ الطباعة: {{ now()->format('Y/m/d h:i A') }}</span>
        <span>توقيع المدير: _______________</span>
    </div>

</body>
</html>
