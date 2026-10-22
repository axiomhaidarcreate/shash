<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير موافقات المدير - {{ $targetDate->format('Y-m-d') }}</title>
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
        .header .sub { font-size: 10px; color: #444; font-weight: 700; }

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
            margin-bottom: 10px;
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
            padding: 4px 3px;
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
            <div class="sub">موافقات المدير للطلبات المجانية</div>
            <div style="font-size: 9px; color: #666;">(الطلبات بدون فاتورة كاشير)</div>
        </div>

        <div class="meta">
            <div><span>التاريخ:</span> <span>{{ $targetDate->format('Y/m/d') }}</span></div>
            <div><span>وقت الطباعة:</span> <span>{{ now()->format('h:i A') }}</span></div>
            <div><span>إجمالي الطلبات:</span> <span>{{ $approvedOrders->count() }}</span></div>
        </div>

        @if($approvedOrders->isEmpty())
            <p style="text-align:center; padding:10px; color:#999; font-weight: bold;">لا توجد طلبات موافقة لليوم</p>
        @else
        <table>
            <thead>
                <tr>
                    <th style="width:15%;">الطاولة</th>
                    <th style="width:30%;">الضيف</th>
                    <th style="width:30%;">الطلب (الكمية)</th>
                    <th style="width:25%;">بواسطة</th>
                </tr>
            </thead>
            <tbody>
                @php $totalQty = 0; @endphp
                @foreach($approvedOrders as $ord)
                @php $totalQty += $ord->quantity; @endphp
                <tr>
                    <td style="font-weight: bold;">{{ $ord->table->number ?? '-' }}</td>
                    <td style="font-size: 9px;">{{ $ord->guest_name ?: 'غير محدد' }}</td>
                    <td style="text-align:right;">
                        <span style="font-weight:600; font-size:9px;">{{ $ord->shisha_type }}</span> 
                        <span style="font-weight:800; font-size:10px; color:#000;">({{ $ord->quantity }})</span>
                        @if($ord->notes)
                            <div style="font-size:8px; color:#555; margin-top:2px;">ملاحظة: {{ $ord->notes }}</div>
                        @endif
                    </td>
                    <td style="font-size:8px; text-align:right; direction:rtl;">
                        {{ $ord->user->name ?? '-' }}
                        <div style="font-size:7px; color:#333; margin-top:4px; line-height:1.2; font-weight:bold;">
                            تم الموافقة بواسطة: {{ $ord->approver ? $ord->approver->name : 'المدير' }}
                            <br>
                            <span style="font-size:6px; font-weight:normal; color:#555;">({{ $ord->updated_at ? $ord->updated_at->format('Y-m-d h:i A') : '' }})</span>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" style="text-align:right; padding-right:6px;">إجمالي الرؤوس</td>
                    <td colspan="2" style="text-align:center;">{{ $totalQty }} رأس</td>
                </tr>
            </tbody>
        </table>
        @endif

        <div class="footer">
            تمت الطباعة بواسطة النظام<br>
            {{ now()->format('Y/m/d h:i A') }}
        </div>
    </div>

</body>
</html>
