<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير تصنيف - {{ $categoryParam }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            color: #000;
            padding: 20px;
            margin: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 5px;
            font-size: 24px;
        }
        .header p {
            margin: 2px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 30px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        @media print {
            body { padding: 0; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>{{ $loungeName }}</h1>
        <p>تقرير مبيعات وجرد التصنيف: <strong>{{ $categoryParam }}</strong></p>
        <p>تاريخ ووقت الطباعة: {{ $printDate }}</p>
        <p>تاريخ التقرير المعتمد: {{ $targetDate->format('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>اسم المنتج</th>
                <th>المباع</th>
                <th>التالف</th>
                <th>المتبقي</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['sold'] }} {{ $row['unit'] }}</td>
                    <td>{{ $row['damaged'] }} {{ $row['unit'] }}</td>
                    <td style="font-weight: bold;">{{ $row['remaining'] }} {{ $row['unit'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">لا توجد بيانات لهذا التصنيف</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>تم إصدار هذا التقرير من النظام</p>
    </div>
</body>
</html>
