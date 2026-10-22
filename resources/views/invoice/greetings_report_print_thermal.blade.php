<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير التحايا - حراري</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Tahoma', sans-serif; margin: 0; padding: 5px; width: 80mm; background-color: #fff; color: #000; font-size: 12px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        .border-top { border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { text-align: center; padding: 4px 2px; border-bottom: 1px dotted #ccc; font-size: 11px; }
        th { border-bottom: 1px solid #000; font-weight: bold; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .summary-label { font-weight: bold; }
        .print-btn { display: block; width: 100%; padding: 10px; background: #000; color: #fff; text-align: center; border: none; font-size: 16px; cursor: pointer; margin-bottom: 10px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <button class="no-print print-btn" onclick="window.print()">طباعة</button>

    <div class="text-center border-bottom mb-2">
        <h2 style="margin: 0 0 5px 0; font-size: 18px;">{{ $loungeName }}</h2>
        <h3 style="margin: 0 0 5px 0; font-size: 14px;">تقرير التحايا</h3>
        <div>التاريخ: {{ $targetDate->format('Y/m/d') }}</div>
        <div style="font-size: 10px;">وقت الطباعة: {{ $printDate }}</div>
    </div>

    <div class="border-bottom mb-2">
        <div class="summary-row">
            <span class="summary-label">الإيرادات:</span>
            <span>{{ number_format($totalRevenue, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">إجمالي التحايا:</span>
            <span>{{ $totalGreetings }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">عدد الحسابات:</span>
            <span>{{ $reports->count() }}</span>
        </div>
    </div>

    <div class="fw-bold text-center mb-1">تفاصيل الحسابات</div>
    <table>
        <thead>
            <tr>
                <th style="width: 35%;">الضيف</th>
                <th style="width: 25%;">الوقت</th>
                <th style="width: 20%;">العدد</th>
                <th style="width: 20%;">المبلغ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                <tr>
                    <td>{{ \Illuminate\Support\Str::limit($report->guest_name, 10) }}</td>
                    <td>{{ $report->created_at->format('H:i') }}</td>
                    <td>{{ $report->total_greetings }}</td>
                    <td>{{ number_format($report->total_amount, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-center border-top" style="margin-top: 15px; font-size: 10px;">
        تمت الطباعة بواسطة النظام
    </div>
</body>
</html>
