<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مبيعات المطبخ - {{ $periodLabel }}</title>
    <style>
        body { font-family: 'Tahoma', 'Arial', sans-serif; font-size: 14px; margin: 0; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h1 { margin: 5px 0; font-size: 24px; color: #1E88E5; }
        .header h2 { margin: 5px 0; font-size: 18px; color: #555; }
        .header p { margin: 5px 0; font-size: 14px; color: #777; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: right; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .total-row td { font-weight: bold; background-color: #e3f2fd; }
        .text-success { color: #2e7d32; }
        .text-danger { color: #c62828; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
        @media print {
            body { padding: 0; }
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
                <th>الصنف / الشيشة</th>
                <th>إجمالي المباع</th>
                <th>إجمالي الملغى/التالف</th>
                <th>الإجمالي العام</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats as $categoryName => $catData)
                <tr style="background-color: #eee;">
                    <td colspan="4" style="font-weight: bold; font-size: 16px;">{{ $categoryName }}</td>
                </tr>
                @foreach($catData['products'] as $productName => $prodData)
                    <tr>
                        <td>- {{ $productName }}</td>
                        <td class="text-success">{{ $prodData['sold'] }}</td>
                        <td class="text-danger">{{ $prodData['cancelled'] }}</td>
                        <td>{{ $prodData['sold'] + $prodData['cancelled'] }}</td>
                    </tr>
                @endforeach
                <tr class="total-row" style="background-color: #f9f9f9;">
                    <td>إجمالي ({{ $categoryName }})</td>
                    <td class="text-success">{{ $catData['total_sold'] }}</td>
                    <td class="text-danger">{{ $catData['total_cancelled'] }}</td>
                    <td>{{ $catData['total_sold'] + $catData['total_cancelled'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">لا توجد مبيعات في هذه الفترة.</td>
                </tr>
            @endforelse
            @if(count($stats) > 0)
                <tr class="total-row" style="background-color: #d1c4e9;">
                    <td style="font-size: 16px;">الإجمالي العام</td>
                    <td class="text-success" style="font-size: 16px;">{{ $totalSold }}</td>
                    <td class="text-danger" style="font-size: 16px;">{{ $totalCancelled }}</td>
                    <td style="font-size: 16px;">{{ $totalSold + $totalCancelled }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        برمجة شركة مارس سوفت &copy; {{ date('Y') }}
    </div>
</body>
</html>
