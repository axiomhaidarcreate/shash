<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة مطبخ طاولات #{{ $orders->first()->table->number }}</title>
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
        .info { font-size: 1.0rem; margin-bottom: 12px; border-bottom: 2px solid #000; padding-bottom: 8px; font-weight: bold; }
        .details { text-align: right; margin-bottom: 12px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details th, .details td { padding: 6px 0; font-size: 1.0rem; font-weight: bold; border-bottom: 1px dashed #ccc; }
        .details th { text-align: right; border-bottom: 2px solid #000; }

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
            $firstOrder = $orders->first();
            $kitchen = \App\Models\Kitchen::where('slug', $firstOrder->target_kitchen)->first();
            $kitchenName = $kitchen ? $kitchen->name : ($firstOrder->target_kitchen == 'barista' ? 'الباريستا' : ($firstOrder->target_kitchen == 'restaurant' ? 'المطعم' : 'الشيشة'));
        @endphp
        <h2>فاتورة مطبخ {{ $kitchenName }}</h2>
        <div class="info">
            <div>التاريخ والوقت: {{ now()->format('Y-m-d h:i A') }}</div>
            <div>الموظف: {{ $orders->first()->user->name }}</div>
            @if($orders->first()->ticket_id && $orders->first()->ticket)
            <div style="font-size: 1.1rem; border: 1px dashed #000; padding: 2px; margin: 4px 0;">
                رقم التذكرة: {{ $orders->first()->ticket->barcode }}
            </div>
            @elseif($orders->first()->guest_name)
            <div style="font-size: 1.1rem; font-weight: bold; border: 2px solid #000; padding: 4px; margin-top: 5px;">طلب ضيافة باسم: {{ $orders->first()->guest_name }}</div>
            @endif
            @if($orders->first()->ticket)
            <div style="font-size: 1.4rem; font-weight: 900; border: 2px dashed #000; padding: 4px; margin-top: 5px;">تذكرة: {{ $orders->first()->ticket->barcode }}</div>
            @endif
            <div style="font-size: 1.4rem; font-weight: 900; border: 2px solid #000; padding: 5px; margin-top: 5px;">طاولة: {{ $orders->first()->table->number }} @if($orders->first()->table && $orders->first()->table->section)({{ $orders->first()->table->section }})@endif</div>
        </div>

        <div class="details">
            <table>
                <thead>
                    <tr>
                        <th>رقم</th>
                        <th>الصنف</th>
                        <th style="text-align: left;">الكمية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td style="font-size: 0.9rem;">#{{ $order->id }}</td>
                        <td>{{ $order->shisha_type }}</td>
                        <td style="text-align: left; font-size: 1.4rem;">{{ $order->quantity }}</td>
                    </tr>
                    @if($order->notes)
                    <tr>
                        <td colspan="3" style="font-size: 1.0rem; font-weight: 900; color: #000; padding: 6px; border: 3px solid #000; text-align: right;">
                            ★ تفاصيل:
                            @foreach(explode(' | ', $order->notes) as $note)
                                <div style="padding: 1px 0;">• {{ $note }}</div>
                            @endforeach
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
