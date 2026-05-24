<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جرد حراري - {{ $targetDate->format('Y-m-d') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: #fff;
            color: #000;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
            font-size: 11px;
        }
        .receipt { width: 100%; }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header h2 { font-size: 16px; font-weight: 800; margin-bottom: 2px; }
        .header .sub { font-size: 10px; color: #444; }

        /* Meta info */
        .meta {
            font-size: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .meta div { display: flex; justify-content: space-between; margin-bottom: 2px; }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th {
            background: #000;
            color: #fff;
            padding: 4px 3px;
            font-weight: 800;
            font-size: 9px;
            border: 1px solid #000;
        }
        td {
            padding: 3px;
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
        }
        .group-row td {
            background: #ddd;
            font-weight: 800;
            font-size: 11px;
            border-top: 2px solid #000;
            text-align: right;
            padding-right: 6px;
        }
        .total-row td {
            background: #000;
            color: #fff;
            font-weight: 800;
            font-size: 11px;
            border-top: 2px solid #000;
        }

        /* Product summary */
        .product-section {
            margin-top: 10px;
            border-top: 2px dashed #000;
            padding-top: 8px;
        }
        .product-title {
            font-weight: 800;
            font-size: 11px;
            margin-bottom: 6px;
            text-align: center;
        }

        /* Footer */
        .footer {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 6px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        @media print {
            body { padding: 0; max-width: 100%; }
            @page { margin: 2mm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt">
        <div class="header">
            <h2>{{ $loungeName }}</h2>
            <div class="sub">تقرير جرد الطاولات</div>
        </div>

        <div class="meta">
            <div><span>التاريخ:</span> <span>{{ $targetDate->format('Y/m/d') }}</span></div>
            <div><span>وقت الطباعة:</span> <span>{{ now()->format('h:i A') }}</span></div>
            <div><span>الطاولات:</span> <span>{{ count($tableReport) }}</span></div>
            <div><span>الطلبيات:</span> <span>{{ $totalOrders }}</span></div>
            <div><span>الرؤوس:</span> <span><strong>{{ $totalQty }}</strong></span></div>
        </div>

        @if(empty($tableReport))
            <p style="text-align:center; padding:10px; color:#999;">لا توجد طلبات لليوم</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>الطاولة</th>
                    <th>النوع</th>
                    <th>الكمية</th>
                    <th>الوقت</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($tableReport as $key => $report)
                    @php
                        $tableTotal = array_sum(array_column($report['orders'], 'quantity'));
                        $grandTotal += $tableTotal;
                    @endphp
                    <tr class="group-row">
                        <td colspan="2">ط{{ $report['table_number'] }} @if($report['section'])({{ $report['section'] }})@endif</td>
                        <td style="text-align:center;">{{ $tableTotal }}</td>
                        <td></td>
                    </tr>
                    @foreach($report['orders'] as $ord)
                    <tr>
                        <td></td>
                        <td style="text-align:right; font-weight:600; font-size:10px;">{{ $ord['shisha_type'] }}</td>
                        <td style="font-weight:800; font-size:12px;">{{ $ord['quantity'] }}</td>
                        <td style="font-size:8px;">{{ $ord['time'] }}</td>
                    </tr>
                    @endforeach
                @endforeach
                <tr class="total-row">
                    <td colspan="2" style="text-align:right; padding-right:6px;">الإجمالي</td>
                    <td>{{ $grandTotal }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        @endif

        @if(!empty($todayProductTotals))
        <div class="product-section">
            <div class="product-title">إجمالي الأصناف</div>
            <table>
                <thead>
                    <tr>
                        <th>الصنف</th>
                        <th>الرؤوس</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayProductTotals as $name => $qty)
                    <tr>
                        <td style="text-align:right; font-weight:700;">{{ $name }}</td>
                        <td style="font-weight:800; font-size:12px;">{{ $qty }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="footer">
            {{ $loungeName }}<br>
            {{ now()->format('Y/m/d h:i A') }}
        </div>
    </div>

</body>
</html>
