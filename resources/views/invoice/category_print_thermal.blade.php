<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير تصنيف - حراري</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #fff;
            color: #000;
            width: 72mm; /* Leave a small margin */
            margin: 0 auto;
            padding: 5px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 3px;
            font-size: 16px;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 11px;
        }
        th, td {
            padding: 3px 1px;
            text-align: center;
            border-bottom: 1px dotted #000;
        }
        th {
            font-weight: bold;
            border-bottom: 1px solid #000;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 15px;
            border-top: 1px dashed #000;
            padding-top: 5px;
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
        <p>تقرير تصنيف: {{ $categoryParam }}</p>
        <p>{{ $printDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40%;">المنتج</th>
                <th style="width: 20%;">مباع</th>
                <th style="width: 20%;">تالف</th>
                <th style="width: 20%;">متبقي</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
                <tr>
                    <td style="text-align: right;">{{ Str::limit($row['name'], 15) }}</td>
                    <td>{{ $row['sold'] }}</td>
                    <td>{{ $row['damaged'] }}</td>
                    <td style="font-weight: bold;">{{ $row['remaining'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">لا توجد بيانات</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>تم إصدار هذا التقرير من النظام</p>
    </div>
</body>
</html>
