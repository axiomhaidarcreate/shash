<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مشتريات الطلبات اليومية</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; color: #222; padding: 30px; font-size: 13px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 22px; color: #1a1a2e; margin-bottom: 5px; }
        .header p { color: #555; font-size: 13px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 20px; gap: 15px; flex-wrap: wrap; }
        .meta-box { border: 1px solid #ddd; border-radius: 6px; padding: 12px 18px; flex: 1; background: #f9f9f9; min-width: 120px; }
        .meta-box .label { font-size: 11px; color: #777; margin-bottom: 3px; }
        .meta-box .value { font-size: 14px; font-weight: 700; color: #1a1a2e; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f0f0f0; border: 1px solid #ccc; padding: 9px 10px; text-align: right; font-size: 12px; color: #444; }
        td { border: 1px solid #ddd; padding: 8px 10px; text-align: right; font-size: 12px; }
        tr:nth-child(even) td { background: #fafafa; }
        .footer { margin-top: 35px; text-align: center; color: #888; font-size: 11px; border-top: 1px solid #ddd; padding-top: 15px; }
        .print-btn { margin-bottom: 20px; padding: 8px 20px; background: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; }
        @media print { .print-btn { display: none; } body { padding: 15px; } @page { margin: 1.5cm; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ طباعة التقرير</button>

    <div class="header">
        <h1>{{ $loungeName }}</h1>
        <p>تقرير مشتريات الطلبات اليومية (النواقص)</p>
    </div>

    <div class="meta">
        <div class="meta-box">
            <div class="label">الفترة الزمنية (تاريخ الاحتياج)</div>
            <div class="value">
                @if($filterFrom || $filterTo)
                    {{ $filterFrom ?: 'البداية' }} — {{ $filterTo ?: 'النهاية' }}
                @else
                    الكل
                @endif
            </div>
        </div>
        <div class="meta-box">
            <div class="label">المطبخ</div>
            <div class="value">{{ $kitchen ?: 'الكل' }}</div>
        </div>
        <div class="meta-box">
            <div class="label">الحالة</div>
            <div class="value">{{ $status ?: 'الكل' }}</div>
        </div>
        <div class="meta-box">
            <div class="label">إجمالي الطلبات</div>
            <div class="value" style="color: #c0392b;">{{ $requests->count() }} طلب</div>
        </div>
        <div class="meta-box">
            <div class="label">تاريخ الطباعة</div>
            <div class="value">{{ $printDate }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>المطبخ</th>
                <th>اسم الصنف</th>
                <th>العدد / الكمية</th>
                <th>الأهمية</th>
                <th>تاريخ الاحتياج</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
            <tr>
                <td>#{{ $request->id }}</td>
                <td style="font-weight: 700;">{{ $request->kitchen_source }}</td>
                <td style="font-weight: 700; color: #1a1a2e;">{{ $request->item_name }}</td>
                <td>{{ $request->quantity }} {{ $request->weight_or_ml ? '(' . $request->weight_or_ml . ')' : '' }}</td>
                <td>{{ $request->importance }}</td>
                <td>{{ \Carbon\Carbon::parse($request->needed_date)->format('Y-m-d') }}</td>
                <td style="{{ $request->status == 'معلق' ? 'color: #e67e22;' : 'color: #27ae60;' }} font-weight: bold;">
                    {{ $request->status }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #888;">لا توجد طلبات مشتريات مطابقة للبحث</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>تم إنشاء هذا التقرير بتاريخ {{ $printDate }} — {{ $loungeName }}</p>
    </div>
</body>
</html>
