<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير التذاكر (حراري) - {{ $targetDate->format('Y/m/d') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #fff; color: #000; direction: rtl; text-align: center; }
        .thermal-wrapper { width: 80mm; margin: 0 auto; padding: 5px; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 5px; }
        .divider { border-bottom: 1px dashed #000; margin: 8px 0; }
        .bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11px; }
        th, td { text-align: right; padding: 4px; border-bottom: 1px dotted #000; }
        th { font-weight: bold; }
        .summary-box { border: 1px solid #000; padding: 5px; margin-top: 10px; text-align: right; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .no-print { text-align: center; margin: 20px 0; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
    @if(request('auto') || true)
    <script>window.onload = function() { window.print(); }</script>
    @endif
</head>
<body>
    <div class="thermal-wrapper">
        <h1>{{ $loungeName }}</h1>
        <div class="bold">تقرير تذاكر الاستقبال</div>
        <div style="font-size: 11px; margin-top: 2px;">التاريخ/الفترة: {{ $periodLabel }}</div>
        <div>وقت الطباعة: {{ $printDate }}</div>
        <div class="divider"></div>

        <div class="summary-box">
            <div class="bold" style="text-align:center; border-bottom:1px solid #000; margin-bottom:4px; padding-bottom:4px;">إحصائيات عامة</div>
            <div class="summary-item"><span>إجمالي التذاكر المصدرة:</span> <span class="bold">{{ $stats['total'] }}</span></div>
            <div class="summary-item"><span>تذاكر نشطة (في الانتظار):</span> <span class="bold">{{ $stats['active'] }}</span></div>
            <div class="summary-item"><span>تذاكر مستخدمة:</span> <span class="bold">{{ $stats['used'] }}</span></div>
        </div>

        <div class="divider"></div>
        <div class="bold" style="margin-bottom: 5px;">تفصيل التذاكر حسب القالب</div>
        @if(count($templateStats) > 0)
            <table>
                <thead>
                    <tr>
                        <th>القالب</th>
                        <th style="width: 40px; text-align: left;">العدد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templateStats as $templateName => $count)
                    <tr>
                        <td>{{ $templateName }}</td>
                        <td style="text-align: left;" class="bold">{{ $count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div>لا توجد بيانات للقوالب.</div>
        @endif

        <div class="divider"></div>
        <div class="bold" style="margin-bottom: 5px;">تفصيل المنتجات المحجوزة</div>
        @if(count($productStats) > 0)
            <table>
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th style="width: 40px; text-align: left;">الكمية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productStats as $productName => $count)
                    <tr>
                        <td>{{ $productName }}</td>
                        <td style="text-align: left;" class="bold">{{ $count }}</td>
                    </tr>
                    @endforeach
                    <tr style="border-top: 2px dashed #000;">
                        <td class="bold">المجموع الكلي للمنتجات</td>
                        <td style="text-align: left; font-size: 14px;" class="bold">{{ $totalProducts }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div>لا توجد منتجات محجوزة.</div>
        @endif

        <div class="divider"></div>
        <div style="font-size: 10px; margin-top: 10px;">نهاية التقرير</div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">طباعة</button>
    </div>
</body>
</html>
