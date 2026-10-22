<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الجرد الفعلي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #fff; color: #000; margin: 0; padding: 20px; font-size: 14px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 15px; }
        .header h1 { margin: 0 0 10px 0; font-size: 24px; }
        .header h2 { margin: 0 0 5px 0; font-size: 18px; color: #555; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
        .info-box { border: 1px solid #ccc; padding: 10px; border-radius: 5px; min-width: 200px; }
        .info-box div { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: right; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-danger { color: #d32f2f; }
        .text-success { color: #388e3c; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
        @media print {
            body { padding: 0; }
            button { display: none; }
        }
        .stats-summary { display: flex; gap: 15px; margin-bottom: 20px; }
        .stat-card { border: 1px solid #000; padding: 15px; flex: 1; text-align: center; border-radius: 5px; }
        .stat-card .value { font-size: 20px; font-weight: bold; margin-top: 5px; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>{{ $loungeName }}</h1>
        <h2>تقرير الجرد الفعلي (تفصيلي)</h2>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <div><strong>رقم الجرد:</strong> INV-{{ str_pad($inventory->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div><strong>تاريخ الجرد:</strong> {{ $inventory->date->format('Y-m-d') }}</div>
            <div><strong>حالة الجرد:</strong> {{ $inventory->status === 'approved' ? 'معتمد (تم تحديث المخزون)' : 'مسودة (قيد المراجعة)' }}</div>
        </div>
        <div class="info-box">
            <div><strong>موظف الجرد:</strong> {{ $inventory->user->name ?? 'غير معروف' }}</div>
            <div><strong>تاريخ الطباعة:</strong> {{ $printDate }}</div>
            @if($inventory->notes)
            <div><strong>ملاحظات:</strong> {{ $inventory->notes }}</div>
            @endif
        </div>
    </div>

    @php
        $totalSystem = 0;
        $totalPhysical = 0;
        $totalShortage = 0;
        $totalSurplus = 0;
        $totalMatched = 0;
        foreach($inventory->items as $item) {
            $prev = is_numeric($item->previous_quantity) ? (float)$item->previous_quantity : 0;
            $sysDaily = is_numeric($item->system_daily_consumed) ? (float)$item->system_daily_consumed : 0;
            $actDaily = is_numeric($item->actual_daily_consumed) ? (float)$item->actual_daily_consumed : 0;
            
            $curr = ($prev + $sysDaily) - $actDaily;
            $diff = $sysDaily - $actDaily;
            $totalSystem += $prev;
            $totalPhysical += $curr;
            if ($diff < 0) $totalShortage += abs($diff);
            elseif ($diff > 0) $totalSurplus += $diff;
            else $totalMatched++;
        }
    @endphp

    <div class="stats-summary">
        <div class="stat-card">
            <div>إجمالي الأصناف المجردة</div>
            <div class="value">{{ $inventory->items->count() }}</div>
        </div>
        <div class="stat-card">
            <div>إجمالي المخزون الكلي (النظام)</div>
            <div class="value">{{ $totalSystem }}</div>
        </div>
        <div class="stat-card">
            <div>إجمالي المتبقي الفعلي</div>
            <div class="value">{{ $totalPhysical }}</div>
        </div>
        <div class="stat-card" style="border-color: #d32f2f;">
            <div style="color: #d32f2f;">الفارق (عجز)</div>
            <div class="value text-danger">{{ $totalShortage }}</div>
        </div>
        <div class="stat-card" style="border-color: #388e3c;">
            <div style="color: #388e3c;">الفارق (زيادة)</div>
            <div class="value text-success">{{ $totalSurplus }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم الصنف / المنتج</th>
                <th class="text-center">الوحدة</th>
                <th class="text-center">المخزون الكلي (نظام)</th>
                <th class="text-center">استهلاك النظام (اليوم)</th>
                <th class="text-center">الاستهلاك الفعلي (اليوم)</th>
                <th class="text-center">المتبقي في المخزون</th>
                <th class="text-center">الفارق</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventory->items as $index => $item)
            @php
                $prev = is_numeric($item->previous_quantity) ? (float)$item->previous_quantity : 0;
                $sysDailyConsumed = is_numeric($item->system_daily_consumed) ? (float)$item->system_daily_consumed : 0;
                $actDailyConsumed = is_numeric($item->actual_daily_consumed) ? (float)$item->actual_daily_consumed : 0;
                
                $curr = ($prev + $sysDailyConsumed) - $actDailyConsumed;
                $diff = $sysDailyConsumed - $actDailyConsumed;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td class="text-center">{{ $item->unit }}</td>
                <td class="text-center">{{ $prev }}</td>
                <td class="text-center" style="color: #e65100;">{{ $sysDailyConsumed }}</td>
                <td class="text-center" style="color: #e65100; font-weight: bold;">{{ $actDailyConsumed }}</td>
                <td class="text-center"><strong>{{ $curr }}</strong></td>
                <td class="text-center" style="font-weight: bold; {{ $diff < 0 ? 'color: #d32f2f;' : ($diff > 0 ? 'color: #388e3c;' : '') }}">
                    {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px; display: flex; justify-content: space-around; padding: 20px;">
        <div style="text-align: center;">
            <div>.......................................................</div>
            <div style="margin-top: 5px; font-weight: bold;">توقيع موظف الجرد</div>
        </div>
        <div style="text-align: center;">
            <div>.......................................................</div>
            <div style="margin-top: 5px; font-weight: bold;">توقيع مدير النظام</div>
        </div>
    </div>

    <div class="footer">
        تم إنشاء هذا التقرير من نظام {{ $loungeName }} - جميع الحقوق محفوظة
    </div>

</body>
</html>
