<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المطبخ (حراري)</title>
    <style>
        @page { margin: 0; padding: 0; }
        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            font-size: 11px; /* Optimal for 80mm thermal printers */
            line-height: 1.4;
            color: #000;
            width: 72mm; /* ~80mm width minus margins */
            margin: 0 auto;
            padding: 5px;
            background: #fff;
        }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .header h1 { font-size: 16px; margin: 0 0 5px 0; }
        .header h2 { font-size: 13px; margin: 0 0 5px 0; }
        .header p { font-size: 11px; margin: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; table-layout: fixed; }
        th, td { border-bottom: 1px dashed #999; padding: 4px 2px; text-align: center; font-size: 10px; }
        th { font-weight: bold; }
        td.item-name { text-align: right; font-weight: bold; width: 45%; }
        
        .total-section { border-top: 1px solid #000; margin-top: 5px; padding-top: 5px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 3px; font-size: 12px; font-weight: bold; }
        
        .footer { text-align: center; font-size: 10px; margin-top: 15px; border-top: 1px dashed #000; padding-top: 5px; }
        
        @media print {
            body { width: 100%; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>{{ $loungeName }}</h1>
        <h2>تقرير مبيعات المطبخ ({{ $type === 'daily' ? 'يومي' : 'مخصص' }})</h2>
        <p>الفترة: {{ $periodLabel }}</p>
        <p>تاريخ الطباعة: {{ $printDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="text-align: right; width: 45%;">الصنف</th>
                <th>مباع</th>
                <th>تالف</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats as $categoryName => $catData)
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold; background: #eee; padding-right: 5px;">{{ $categoryName }}</td>
                </tr>
                @foreach($catData['products'] as $productName => $prodData)
                    <tr>
                        <td class="item-name">- {{ $productName }}</td>
                        <td>{{ $prodData['sold'] }}</td>
                        <td>{{ $prodData['cancelled'] }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">لا توجد مبيعات في هذه الفترة.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>إجمالي المباع:</span>
            <span>{{ $totalSold }}</span>
        </div>
        <div class="total-row">
            <span>إجمالي التالف:</span>
            <span>{{ $totalCancelled }}</span>
        </div>
        <div class="total-row" style="font-size: 14px; margin-top: 5px;">
            <span>الإجمالي العام:</span>
            <span>{{ $totalSold + $totalCancelled }}</span>
        </div>
    </div>

    <div class="footer">
        برمجة شركة مارس سوفت &copy; {{ date('Y') }}
        <br>
        شكراً لكم
    </div>
</body>
</html>
