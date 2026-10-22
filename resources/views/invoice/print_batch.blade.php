<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة فاتورة الطاولة #{{ $orders->first()->table->number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
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
            font-weight: 900;
        }
        .receipt {
            width: 100%;
            margin: 0 auto;
            border: 1px dashed #ccc;
            padding: 8px;
            box-sizing: border-box;
            word-wrap: break-word;
        }
        h2 { margin: 0 0 5px 0; font-size: 1.3rem; font-weight: 800; }
        .info { font-size: 0.85rem; margin-bottom: 12px; border-bottom: 1px dashed #000; padding-bottom: 8px; }
        .details { text-align: right; margin-bottom: 12px; border-bottom: 1px dashed #000; padding-bottom: 8px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details th, .details td { padding: 4px 0; font-size: 0.85rem; }
        .details th { text-align: right; border-bottom: 1px solid #000; }
        .footer { font-size: 0.75rem; margin-top: 15px; }
        
        @media print {
            body { 
                padding: 2mm 4mm; 
                width: 80mm;
            }
            .receipt { 
                border: none; 
                padding: 0; 
                width: 100%;
            }
        }
    </style>
</head>
<body onload="window.print() {{ request('auto') ? '; setTimeout(window.close, 1000);' : '' }}">
    <div class="receipt">
        <h2>{{ $loungeName }}</h2>
        <div style="font-weight: bold; margin-bottom: 10px; border: 1px solid #000; display: inline-block; padding: 2px 8px;">نسخة العميل</div>
        
        <div class="info">
            @if($loungePhone) <div>هاتف: {{ $loungePhone }}</div> @endif
            <div>التاريخ والوقت: {{ now()->format('Y-m-d h:i A') }}</div>
            <div>طاولة رقم: {{ $orders->first()->table->number }} @if($orders->first()->table && $orders->first()->table->section)({{ $orders->first()->table->section }})@endif</div>
            <div>الموظف: {{ $orders->first()->user->name }}</div>
        </div>

        <div class="details">
            <table>
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>الصنف</th>
                        <th style="text-align: left;">الكمية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td style="font-size: 0.8rem;">#{{ $order->id }}</td>
                        <td>{{ $order->shisha_type }}</td>
                        <td style="text-align: left;">{{ $order->quantity }}</td>
                    </tr>
                    @if($order->notes)
                    <tr>
                        <td colspan="3" style="font-size: 0.8rem; color: #555; padding-bottom: 8px;">
                            ملاحظات: {{ $order->notes }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            شكراً لزيارتكم
        </div>
    </div>
</body>
</html>
