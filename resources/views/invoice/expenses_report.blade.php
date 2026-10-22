<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المصروفات</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; color: #222; padding: 30px; font-size: 13px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 22px; color: #1a1a2e; margin-bottom: 5px; }
        .header p { color: #555; font-size: 13px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 20px; gap: 15px; }
        .meta-box { border: 1px solid #ddd; border-radius: 6px; padding: 12px 18px; flex: 1; background: #f9f9f9; }
        .meta-box .label { font-size: 11px; color: #777; margin-bottom: 3px; }
        .meta-box .value { font-size: 15px; font-weight: 700; color: #1a1a2e; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f0f0f0; border: 1px solid #ccc; padding: 9px 10px; text-align: right; font-size: 12px; color: #444; }
        td { border: 1px solid #ddd; padding: 8px 10px; text-align: right; font-size: 12px; }
        tr:nth-child(even) td { background: #fafafa; }
        tfoot td { background: #f0f0f0; font-weight: 800; font-size: 13px; }
        .total-amount { color: #c0392b; }
        .footer { margin-top: 35px; text-align: center; color: #888; font-size: 11px; border-top: 1px solid #ddd; padding-top: 15px; }
        .print-btn { margin-bottom: 20px; padding: 8px 20px; background: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; }
        @media print { .print-btn { display: none; } body { padding: 15px; } @page { margin: 1.5cm; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ طباعة التقرير</button>

    <div class="header">
        <h1>{{ $loungeName }}</h1>
        <p>تقرير المصروفات التشغيلية والالتزامات</p>
    </div>

    <div class="meta">
        <div class="meta-box">
            <div class="label">الفترة الزمنية</div>
            <div class="value">{{ $filterFrom }} — {{ $filterTo }}</div>
        </div>
        <div class="meta-box">
            <div class="label">عدد الفواتير</div>
            <div class="value">{{ $expenses->count() }}</div>
        </div>
        <div class="meta-box">
            <div class="label">إجمالي المصروفات</div>
            <div class="value total-amount">{{ number_format($total, 2) }} ريال</div>
        </div>
        <div class="meta-box">
            <div class="label">تاريخ الطباعة</div>
            <div class="value">{{ $printDate }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم الفاتورة / البند</th>
                <th>رقم الفاتورة</th>
                <th>المبلغ</th>
                <th>تاريخ الفاتورة</th>
                <th>تاريخ السداد</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $expense)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-weight: 700;">{{ $expense->name }}</td>
                <td>{{ $expense->invoice_number ?: '-' }}</td>
                <td style="font-weight: 700; color: #c0392b;">{{ number_format($expense->amount, 2) }} ريال</td>
                <td>{{ $expense->invoice_date }}</td>
                <td>{{ $expense->payment_date }}</td>
                <td>{{ $expense->notes ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #888;">لا توجد مصروفات في هذه الفترة</td>
            </tr>
            @endforelse
        </tbody>
        @if($expenses->count() > 0)
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: center;">الإجمالي</td>
                <td class="total-amount">{{ number_format($total, 2) }} ريال</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>تم إنشاء هذا التقرير بتاريخ {{ $printDate }} — {{ $loungeName }}</p>
    </div>
</body>
</html>
