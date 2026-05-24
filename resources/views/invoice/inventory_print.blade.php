<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير جرد الطاولات - {{ $targetDate->format('Y-m-d') }}</title>
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
        table.main-table .table-group-row td {
            background: #eee;
            font-weight: 800;
            font-size: 12px;
            border-top: 2px solid #999;
        }
        table.main-table .total-row td {
            background: #222;
            color: #fff;
            font-weight: 800;
            border-top: 2px solid #000;
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

    <!-- Screen preview bar -->
    <div class="screen-only">
        <button class="btn-print" onclick="window.print()">🖨️ طباعة A4</button>
        <button class="btn-close" onclick="window.close()">✕ إغلاق</button>
    </div>

    <!-- Report Header -->
    <div class="report-header">
        <h1>{{ $loungeName }}</h1>
        <div class="sub">تقرير جرد الطاولات اليومي</div>
    </div>

    <div class="report-meta">
        <span><strong>التاريخ:</strong> {{ $targetDate->format('Y/m/d') }} — {{ $targetDate->translatedFormat('l') }}</span>
        <span><strong>وقت الطباعة:</strong> {{ now()->format('h:i A') }}</span>
        <span><strong>إجمالي الطاولات:</strong> {{ count($tableReport) }}</span>
    </div>

    <!-- Quick Summary -->
    <div class="summary-box">
        <div class="summary-item">
            <div class="label">إجمالي الطلبيات</div>
            <div class="value">{{ $totalOrders }}</div>
        </div>
        <div class="summary-item">
            <div class="label">إجمالي الرؤوس</div>
            <div class="value">{{ $totalQty }}</div>
        </div>
        <div class="summary-item">
            <div class="label">عدد الطاولات</div>
            <div class="value">{{ count($tableReport) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">عدد الأصناف</div>
            <div class="value">{{ count($todayProductTotals) }}</div>
        </div>
    </div>

    <!-- ═══ TABLE DETAIL SECTION ═══ -->
    <div class="section-title">📋 تفصيل جرد الطاولات</div>

    @if(empty($tableReport))
        <p style="text-align:center; color:#999; padding: 20px 0;">لا توجد طلبات مسجلة لهذا اليوم.</p>
    @else
    <table class="main-table">
        <thead>
            <tr>
                <th style="width:12%">الطاولة</th>
                <th style="width:10%">القسم</th>
                <th style="width:30%">النوع</th>
                <th style="width:10%">الكمية</th>
                <th style="width:22%">الموظف</th>
                <th style="width:16%">الوقت</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($tableReport as $key => $report)
                @php
                    $tableTotal = array_sum(array_column($report['orders'], 'quantity'));
                    $orderCount = count($report['orders']);
                    $grandTotal += $tableTotal;
                @endphp

                {{-- Table group header row --}}
                <tr class="table-group-row">
                    <td colspan="3" style="text-align:right; padding-right: 12px;">
                        🪑 طاولة {{ $report['table_number'] }}
                        @if($report['section']) <span style="font-weight:400; font-size:10px;">({{ $report['section'] }})</span> @endif
                        — {{ $orderCount }} طلبية
                    </td>
                    <td><strong>{{ $tableTotal }}</strong> رأس</td>
                    <td colspan="2"></td>
                </tr>

                {{-- Individual orders for this table --}}
                @foreach($report['orders'] as $ord)
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align:right; font-weight:600;">{{ $ord['shisha_type'] }}</td>
                    <td style="font-weight:800; font-size:13px;">{{ $ord['quantity'] }}</td>
                    <td style="font-size:10px; color:#555;">{{ $ord['waiter'] }}</td>
                    <td style="font-size:10px;">{{ $ord['time'] }}</td>
                </tr>
                @endforeach
            @endforeach

            {{-- Grand total row --}}
            <tr class="total-row">
                <td colspan="3" style="text-align:right; padding-right:12px;">الإجمالي الكلي</td>
                <td>{{ $grandTotal }} رأس</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- ═══ PRODUCT TOTALS SECTION ═══ -->
    @if(!empty($todayProductTotals))
    <div class="section-title" style="margin-top: 18px;">📦 إجمالي الأصناف لليوم</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>#</th>
                <th>الصنف</th>
                <th>إجمالي الرؤوس</th>
            </tr>
        </thead>
        <tbody>
            @php $idx = 1; @endphp
            @foreach($todayProductTotals as $productName => $qty)
            <tr>
                <td>{{ $idx++ }}</td>
                <td style="text-align:right; font-weight:700;">{{ $productName }}</td>
                <td style="font-weight:800; font-size:13px;">{{ $qty }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="print-footer">
        <span>{{ $loungeName }} — نظام إدارة الطلبات</span>
        <span>تاريخ الطباعة: {{ now()->format('Y/m/d h:i A') }}</span>
        <span>توقيع المسؤول: _______________</span>
    </div>

</body>
</html>
