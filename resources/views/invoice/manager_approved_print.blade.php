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
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            background: #fff;
            color: #000;
            margin: 0;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .receipt { width: 100%; max-width: 800px; margin: 0 auto; }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h2 { font-size: 24px; font-weight: 800; margin-bottom: 5px; }
        .header .sub { font-size: 16px; color: #444; font-weight: 700; }

        /* Meta info */
        .meta {
            font-size: 14px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }
        .meta div { flex: 1; text-align: right; margin: 0 10px; font-weight: bold; }
        .meta div span:first-child { color: #555; margin-left: 5px; }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 20px;
        }
        th {
            background: #000;
            color: #fff;
            padding: 10px;
            font-weight: 800;
            font-size: 14px;
            border: 1px solid #000;
        }
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
        }
        .total-row td {
            background: #f9f9f9;
            color: #000;
            font-weight: 800;
            font-size: 16px;
            border-top: 2px solid #000;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        @media print {
            body { padding: 0; background: none; }
            .receipt { border: none; max-width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt">
        <!-- Report Header -->
        <div class="report-header" style="margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 15px;">
            @if(isset($customTitle) && $customTitle)
                <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 5px;">{{ $customTitle }}</h1>
            @else
                <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 5px;">{{ $loungeName }}</h1>
            @endif
            <div class="sub" style="font-size: 16px; color: #444; font-weight: 700;">تقرير موافقات المدير للطلبات المجانية</div>
            <div class="sub" style="font-size: 14px; color: #555;">التاريخ: {{ $targetDate->format('Y/m/d') }}</div>
            <div class="sub" style="font-size: 12px; color: #777;">وقت الطباعة: {{ now()->format('Y/m/d h:i A') }}</div>
        </div>

        <div class="meta">
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
                    <td style="font-weight: bold; font-size: 14px;">{{ $ord->table->number ?? '-' }}</td>
                    <td style="font-size: 14px;">{{ $ord->guest_name ?: 'غير محدد' }}</td>
                    <td style="text-align:right;">
                        <span style="font-weight:600; font-size:14px;">{{ $ord->shisha_type }}</span> 
                        <span style="font-weight:800; font-size:16px; color:#000;">({{ $ord->quantity }})</span>
                        @if($ord->notes)
                            <div style="font-size:12px; color:#555; margin-top:5px;">ملاحظة: {{ $ord->notes }}</div>
                        @endif
                    </td>
                    <td style="font-size:12px; text-align:right; direction:rtl;">
                        <span style="font-weight:bold; font-size:14px;">{{ $ord->user->name ?? '-' }}</span>
                        <div style="font-size:11px; color:#333; margin-top:8px; line-height:1.4; font-weight:bold;">
                            تم الموافقة بواسطة: {{ $ord->approver ? $ord->approver->name : 'المدير' }}
                            <br>
                            <span style="font-size:10px; font-weight:normal; color:#555;">({{ $ord->updated_at ? $ord->updated_at->format('Y-m-d h:i A') : '' }})</span>
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
