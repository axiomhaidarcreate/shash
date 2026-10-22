<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة الفاتورة #{{ $order->id }}</title>
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
        .total { font-weight: bold; font-size: 1.1rem; margin-top: 8px; text-align: center; }
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
        @php
            $kitchen = \App\Models\Kitchen::where('slug', $order->target_kitchen)->first();
            $kitchenName = $kitchen ? $kitchen->name : ($order->target_kitchen == 'barista' ? 'الباريستا' : ($order->target_kitchen == 'restaurant' ? 'المطعم' : 'الشيشة'));
        @endphp
        @if($order->status === 'prep')
            <h2>فاتورة مطبخ {{ $kitchenName }}</h2>
        @elseif($order->status === 'ready' || $order->status === 'pending')
            <h2>فاتورة العميل لمطبخ {{ $kitchenName }}</h2>
        @else
            <h2>{{ $loungeName }}</h2>
            <div style="font-weight: bold; margin-bottom: 10px; border: 1px solid #000; display: inline-block; padding: 2px 8px;">نسخة العميل</div>
        @endif
        
        <div class="info">
            @if($order->status !== 'prep' && $order->status !== 'ready' && $loungePhone) <div>هاتف: {{ $loungePhone }}</div> @endif
            <div>التاريخ والوقت: {{ $order->created_at->format('Y-m-d h:i A') }}</div>
            <div>رقم الطلب: #{{ $order->id }}</div>
            <div>الموظف: {{ $order->user?->name ?? 'طلب ذاتي' }}</div>
            @if($order->guest_name)
            <div style="font-size: 1.1rem; font-weight: bold; border: 2px solid #000; padding: 4px; margin-top: 5px;">طلب ضيافة باسم: {{ $order->guest_name }}</div>
            @endif
            @if($order->ticket)
            <div style="font-size: 1.4rem; font-weight: 900; border: 2px dashed #000; padding: 4px; margin-top: 5px;">تذكرة: {{ $order->ticket->barcode }}</div>
            @endif
            @if($order->table)
                <div style="font-size: 1.4rem; font-weight: 900; border: 2px solid #000; padding: 4px; margin-top: 5px;">{{ $order->table->number }} @if($order->table->section)({{ $order->table->section }})@endif</div>
            @elseif($order->source === 'self_service')
                <div style="font-size: 1.2rem; font-weight: 900; border: 2px solid #000; padding: 4px; margin-top: 5px;">طلب ذاتي 🎉</div>
            @endif
        </div>

        <div class="details">
            <table>
                <thead>
                    <tr>
                        <th>الصنف</th>
                        <th style="text-align: left;">الكمية</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $order->shisha_type }}</td>
                        <td style="text-align: left; font-size: 2.0rem; font-weight: 900;">{{ $order->quantity }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($order->notes)
        <div style="text-align: right; font-size: 0.9rem; margin-bottom: 10px;">
            <strong>ملاحظات:</strong> {{ $order->notes }}
        </div>
        @endif

        <div class="footer">
            شكراً لزيارتكم
        </div>
    </div>
</body>
</html>
