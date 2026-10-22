<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير التذاكر - {{ $targetDate->format('Y/m/d') }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; background: #eee; padding: 20px; direction: rtl; color: #333; }
        .page { background: #fff; width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20mm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0 0 10px 0; font-size: 24px; color: #222; }
        .header p { margin: 5px 0; font-size: 14px; color: #555; }
        
        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .box { border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: #fafafa; }
        .box h3 { margin-top: 0; color: #9C27B0; border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-bottom: 15px; }
        
        .stat-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 15px; border-bottom: 1px dashed #eee; padding-bottom: 5px; }
        .stat-row:last-child { border-bottom: none; }
        .stat-val { font-weight: bold; color: #000; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: right; }
        th { background: #f5f5f5; color: #333; }
        
        .no-print { text-align: center; margin-top: 20px; }
        @media print {
            body { background: #fff; padding: 0; }
            .page { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>{{ $loungeName }} - تقرير تذاكر الاستقبال</h1>
            <p>التاريخ/الفترة: <strong style="color:#000;">{{ $periodLabel }}</strong></p>
            <p>وقت الطباعة: {{ $printDate }}</p>
        </div>

        <div class="grid-container">
            <div class="box">
                <h3>إحصائيات عامة</h3>
                <div class="stat-row"><span>إجمالي التذاكر المصدرة:</span> <span class="stat-val" style="color: #9C27B0; font-size: 18px;">{{ $stats['total'] }}</span></div>
                <div class="stat-row"><span>تذاكر نشطة (في الانتظار):</span> <span class="stat-val" style="color: #ff9800;">{{ $stats['active'] }}</span></div>
                <div class="stat-row"><span>تذاكر مستخدمة:</span> <span class="stat-val" style="color: #4CAF50;">{{ $stats['used'] }}</span></div>
            </div>

            <div class="box">
                <h3>تفصيل المنتجات المحجوزة</h3>
                @if(count($productStats) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th style="width: 80px;">الكمية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productStats as $productName => $count)
                            <tr>
                                <td>{{ $productName }}</td>
                                <td class="stat-val">{{ $count }}</td>
                            </tr>
                            @endforeach
                            <tr style="background-color: rgba(156, 39, 176, 0.1); border-top: 2px solid #9C27B0;">
                                <td style="font-weight: bold; color: #9C27B0;">المجموع الكلي للمنتجات</td>
                                <td class="stat-val" style="font-weight: bold; color: #9C27B0; font-size: 16px;">{{ $totalProducts }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p style="color: #777;">لا توجد منتجات محجوزة.</p>
                @endif
            </div>
        </div>

        <div class="box" style="margin-bottom: 30px;">
            <h3>تفصيل التذاكر حسب القالب</h3>
            @if(count($templateStats) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>القالب</th>
                            <th style="width: 150px;">عدد التذاكر</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templateStats as $templateName => $count)
                        <tr>
                            <td>{{ $templateName }}</td>
                            <td class="stat-val">{{ $count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: #777;">لا توجد بيانات للقوالب.</p>
            @endif
        </div>

        <div style="text-align: center; color: #777; font-size: 12px; margin-top: 50px; border-top: 1px solid #eee; padding-top: 10px;">
            -- نهاية التقرير --
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 12px 24px; font-size: 16px; background: #9C27B0; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            <i class="fa-solid fa-print"></i> طباعة التقرير
        </button>
    </div>
</body>
</html>
