<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جرد حراري - {{ $targetDate->format('Y-m-d') }}{{ $shiftLabel ? ' - '.$shiftLabel : '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            background: #fff;
            color: #000;
            margin: 0;
            padding: 2mm 4mm;
            text-align: center;
            width: 80mm;
            box-sizing: border-box;
            font-size: 13px;
            font-weight: bold !important;
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
            font-size: 12px;
            font-weight: bold;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .meta div { display: flex; justify-content: space-between; margin-bottom: 2px; }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            font-weight: bold;
        }
        th {
            background: #000;
            color: #fff;
            padding: 3px 1px;
            font-weight: 800;
            font-size: 11px;
            border: 1px solid #000;
        }
        td {
            padding: 3px 1px;
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
        }
        .total-row td {
            background: #000;
            color: #fff;
            font-weight: 800;
            font-size: 13px;
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
            font-size: 13px;
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
            body { padding: 2mm 4mm; width: 80mm; }
            .receipt { border: none; padding: 0; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt">
        <div class="header">
            @if(isset($customTitle) && $customTitle)
                <h2>{{ $customTitle }}</h2>
            @else
                <h2>{{ $loungeName }}</h2>
            @endif
            @if($shiftLabel)
                <div class="sub" style="font-size:12px; font-weight:800; color:#000;">{{ $shiftLabel }}</div>
                <div class="sub">جرد فاتورة الفترة المحاسبية</div>
            @else
                <div class="sub">تقرير جرد الطاولات والآلات</div>
            @endif
        </div>

        <div class="meta">
            <div><span>التاريخ:</span> <span>{{ $targetDate->format('Y/m/d') }}</span></div>
            @if($shiftLabel)
                <div><span>الفترة:</span> <span style="font-weight:800;">{{ $shiftLabel }}</span></div>
                <div><span>من:</span> <span>{{ $startAt->format('h:i A') }}</span></div>
                <div><span>حتى:</span> <span>{{ $endAt->format('h:i A') }}</span></div>
            @endif
            <div><span>وقت الطباعة:</span> <span>{{ now()->format('h:i A') }}</span></div>
            <div><span>الطاولات:</span> <span>{{ $totalTables }}</span></div>
            <div><span>الطلبيات:</span> <span>{{ $totalOrders }}</span></div>
            <div><span>الرؤوس:</span> <span><strong>{{ $totalQty }}</strong></span></div>
        </div>

        @if(empty($tableReport))
            <p style="text-align:center; padding:10px; color:#999;">لا توجد طلبات لليوم</p>
        @else
        <table>
            <thead>
                <tr>
                    <th style="width:15%;">الصالة</th>
                    <th style="width:18%;">الطاولة</th>
                    <th style="width:37%;">النوع (الكمية)</th>
                    <th style="width:30%;">الموظف/الوقت</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($tableReport as $sectionName => $report)
                    @php $sectionTotal = 0; @endphp
                    @foreach($report['orders'] as $ord)
                    @php 
                        $grandTotal += $ord['quantity']; 
                        $sectionTotal += $ord['quantity'];
                    @endphp
                    <tr>
                        @if($loop->first)
                        <td rowspan="{{ count($report['orders']) }}" style="font-weight:bold; font-size:9px; vertical-align:middle; background:#f9f9f9; border-left: 1px solid #000;">
                            {{ $sectionName }}
                        </td>
                        @endif
                        <td>طا{{ $ord['table_number'] }}</td>
                        <td style="text-align:right;">
                            <span style="font-weight:600; font-size:9px;">{{ $ord['shisha_type'] }}</span> 
                            <span style="font-weight:800; font-size:10px; color:#000;">({{ $ord['quantity'] }})</span>
                        </td>
                        <td style="font-size:8px; text-align:right; direction:rtl;">
                            {{ $ord['waiter'] }}<br>
                            <span style="font-size:7px; color:#444;">{{ $ord['datetime'] }}</span>
                            @if($ord['ticket_barcode'])
                            <br><span style="font-size:8px; font-weight:bold; color:#000;">تذكرة: {{ $ord['ticket_barcode'] }}</span>
                            @endif
                            @if(!empty($ord['guest_name']))
                            <br><span style="font-size:8px; font-weight:bold; color:var(--primary-color);">ضيف: {{ $ord['guest_name'] }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e8e8e8; border-top: 1px solid #999; border-bottom: 2px solid #000;">
                        <td colspan="2" style="text-align:right; padding-right:6px; font-weight:800; font-size:10px;">إجمالي {{ $sectionName }}</td>
                        <td colspan="2" style="text-align:center; font-weight:800; font-size:11px;">{{ $sectionTotal }} رأس</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" style="text-align:right; padding-right:6px;">الإجمالي الكلي</td>
                    <td colspan="2" style="text-align:center;">{{ $grandTotal }} رأس</td>
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
