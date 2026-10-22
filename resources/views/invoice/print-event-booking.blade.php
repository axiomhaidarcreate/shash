<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تذكرة حجز حفلة</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Tajawal', 'Arial', sans-serif;
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            background: #fff;
            color: #000;
            font-size: 12px;
            direction: rtl;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 900;
            margin-bottom: 4px;
        }
        .header .sub {
            font-size: 11px;
            color: #555;
        }
        .booking-id {
            text-align: center;
            font-size: 20px;
            font-weight: 900;
            padding: 8px 0;
            border-bottom: 1px dashed #000;
            margin-bottom: 8px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dotted #ccc;
            font-size: 12px;
        }
        .info-row .label {
            font-weight: bold;
            color: #333;
        }
        .info-row .value {
            font-weight: 600;
        }
        .gender-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }
        .gender-male {
            background: #E3F2FD;
            color: #1565C0;
            border: 1px solid #90CAF9;
        }
        .gender-female {
            background: #FCE4EC;
            color: #C2185B;
            border: 1px solid #F48FB1;
        }
        .table-info {
            text-align: center;
            background: #f5f5f5;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
        }
        .table-info .number {
            font-size: 24px;
            font-weight: 900;
        }
        .table-info .label {
            font-size: 10px;
            color: #555;
        }
        .status-approved {
            text-align: center;
            background: #E8F5E9;
            border: 2px solid #4CAF50;
            border-radius: 6px;
            padding: 6px;
            margin: 8px 0;
            font-weight: bold;
            color: #2E7D32;
            font-size: 13px;
        }
        .footer {
            text-align: center;
            border-top: 2px dashed #000;
            padding-top: 8px;
            margin-top: 10px;
            font-size: 10px;
            color: #888;
        }
        @media print {
            body {
                width: 80mm;
                max-width: 80mm;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $siteName }}</h1>
        <div class="sub">تذكرة حجز حفلة</div>
    </div>

    <div class="booking-id">
        حجز #{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
    </div>

    <div class="info-row">
        <span class="label">الحفلة:</span>
        <span class="value">{{ $booking->event->title ?? '—' }}</span>
    </div>

    <div class="info-row">
        <span class="label">التاريخ:</span>
        <span class="value">{{ $booking->event->event_date ?? '—' }}</span>
    </div>

    @if($booking->event && $booking->event->event_time)
    <div class="info-row">
        <span class="label">الوقت:</span>
        <span class="value">{{ $booking->event->event_time }}</span>
    </div>
    @endif

    <div class="info-row">
        <span class="label">الاسم:</span>
        <span class="value">{{ $booking->customer_name }}</span>
    </div>

    <div class="info-row">
        <span class="label">الجوال:</span>
        <span class="value">{{ $booking->customer_phone }}</span>
    </div>

    @if($booking->gender)
    <div class="info-row">
        <span class="label">الجنس:</span>
        <span class="gender-badge {{ $booking->gender === 'male' ? 'gender-male' : 'gender-female' }}">
            {{ $booking->gender === 'male' ? 'ذكر' : 'أنثى' }}
        </span>
    </div>
    @endif

    @if($booking->table_number)
    <div class="table-info">
        <div class="label">الطاولة / الكرسي</div>
        <div class="number">{{ $booking->table_number }}</div>
    </div>
    @endif

    <div class="status-approved">
        ✓ تم تأكيد الحجز
    </div>

    <div class="info-row">
        <span class="label">تاريخ الحجز:</span>
        <span class="value">{{ $booking->created_at->format('Y-m-d h:i A') }}</span>
    </div>

    <div class="footer">
        <p>شكراً لزيارتكم</p>
        <p>{{ now()->format('Y-m-d h:i A') }}</p>
    </div>

<script>
    window.onload = function() {
        window.print();
    };
</script>
</body>
</html>
