<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير التحايا - {{ $targetDate->format('Y/m/d') }}</title>
    <style>
        body { font-family: 'Tahoma', 'Arial', sans-serif; margin: 0; padding: 20px; color: #000; background: #fff; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px 0; font-size: 24px; }
        .header p { margin: 0; font-size: 14px; color: #555; }
        .info-box { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px; }
        .summary-boxes { display: flex; gap: 15px; margin-bottom: 20px; }
        .summary-box { flex: 1; border: 1px solid #000; padding: 15px; text-align: center; border-radius: 4px; }
        .summary-box h3 { margin: 0 0 10px 0; font-size: 16px; }
        .summary-box .val { font-size: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .footer { text-align: center; font-size: 12px; margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 10px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: left; margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 15px; cursor: pointer; font-family: inherit; background: #2196F3; color: white; border: none; border-radius: 4px;">طباعة التقرير</button>
        <button onclick="window.close()" style="padding: 8px 15px; cursor: pointer; font-family: inherit; background: #f44336; color: white; border: none; border-radius: 4px;">إغلاق</button>
    </div>

    <div class="header">
        <h1>{{ $loungeName }}</h1>
        <h2>تقرير التحايا</h2>
        <p>تاريخ التقرير المخصص: {{ $targetDate->format('Y-m-d') }}</p>
    </div>

    <div class="info-box">
        <div><strong>تاريخ ووقت الطباعة:</strong> {{ $printDate }}</div>
    </div>

    <div class="summary-boxes">
        <div class="summary-box">
            <h3>إجمالي الإيرادات</h3>
            <div class="val">{{ number_format($totalRevenue, 2) }} ريال</div>
        </div>
        <div class="summary-box">
            <h3>إجمالي التحايا</h3>
            <div class="val">{{ $totalGreetings }}</div>
        </div>
        <div class="summary-box">
            <h3>عدد الحسابات (الضيوف)</h3>
            <div class="val">{{ $reports->count() }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>م</th>
                <th>اسم الضيف</th>
                <th>رقم الطاولة</th>
                <th>المنتج</th>
                <th>الموظف</th>
                <th>العدد</th>
                <th>الإجمالي (ريال)</th>
                <th>وقت الطلب (الساعة)</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $report)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $report->guest_name }}</td>
                    <td>{{ $report->table ? $report->table->number : 'بدون' }}</td>
                    <td>{{ $report->product ? $report->product->name : '-' }}</td>
                    <td>{{ $report->staff ? $report->staff->name : '-' }}</td>
                    <td style="font-weight: bold;">{{ $report->total_greetings }}</td>
                    <td>{{ number_format($report->total_amount, 2) }}</td>
                    <td>{{ $report->created_at->format('h:i A') }}</td>
                    <td>{{ $report->is_closed ? 'مغلق' : 'نشط' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">لا توجد بيانات لهذا اليوم</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        طبع بواسطة نظام الشيشة
    </div>

</body>
</html>
