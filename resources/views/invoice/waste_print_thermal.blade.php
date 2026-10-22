<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جرد تالف حراري - {{ $targetDate->format('Y-m-d') }}</title>
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
            padding: 3px 1px;
            font-weight: 800;
            font-size: 10px;
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
            <div class="sub">تقرير التالف المعتمد</div>
        </div>

        <div class="meta">
            <div><span>التاريخ:</span> <span>{{ $targetDate->format('Y/m/d') }}</span></div>
            <div><span>وقت الطباعة:</span> <span>{{ now()->format('h:i A') }}</span></div>
            <div><span>العمليات:</span> <span>{{ $wasteEntries->count() }}</span></div>
            <div><span>تالف المعسل:</span> <span><strong>{{ number_format($totalWasteShisha) }} ج</strong></span></div>
            <div><span>تالف المستلزمات:</span> <span><strong>{{ number_format($totalWasteAcc) }} حبة</strong></span></div>
        </div>

        @if($wasteEntries->isEmpty())
            <p style="text-align:center; padding:10px; color:#999;">لا توجد حركات لليوم</p>
        @else
        <table>
            <thead>
                <tr>
                    <th style="width:30%;">الصنف</th>
                    <th style="width:20%;">الكمية</th>
                    <th style="width:50%;">الموظف/الوقت</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wasteEntries as $entry)
                @php
                    $isShisha = $entry->product->category === 'الشيش';
                    $unit = $isShisha ? 'ج' : 'حبة';
                @endphp
                <tr>
                    <td style="font-weight:600; font-size:9px;">{{ $entry->product->name }}</td>
                    <td style="font-weight:800; font-size:10px; color:#000;">{{ number_format($entry->quantity_grams) }} {{ $unit }}</td>
                    <td style="font-size:8px; text-align:right; direction:rtl;">
                        {{ $entry->creator->name ?? 'غير معروف' }}<br>
                        <span style="font-size:7px; color:#444;">{{ $entry->created_at->format('h:i A') }}</span>
                        @if($entry->notes)
                        <br><span style="font-size:8px; font-weight:bold; color:var(--primary-color);">ملاحظة: {{ $entry->notes }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(!empty($wasteByProduct))
        <div class="product-section">
            <div class="product-title">إجمالي التالف للأصناف</div>
            <table>
                <thead>
                    <tr>
                        <th>الصنف</th>
                        <th>الكمية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wasteByProduct as $name => $data)
                    @php
                        $isShisha = $data['category'] === 'الشيش';
                        $unit = $isShisha ? 'ج' : 'حبة';
                    @endphp
                    <tr>
                        <td style="text-align:right; font-weight:700;">{{ $name }}</td>
                        <td style="font-weight:800; font-size:12px;">{{ number_format($data['quantity']) }} {{ $unit }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="footer">
            {{ $loungeName }} - التالف المعتمد<br>
            {{ now()->format('Y/m/d h:i A') }}
        </div>
    </div>

</body>
</html>
