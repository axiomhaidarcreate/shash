<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الاستحقاقات والخصومات</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #fff;
            direction: rtl;
        }

        /* ─── Header ─── */
        .print-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 28px 16px;
            border-bottom: 2px solid #0d9488;
        }
        .print-header .brand { display: flex; align-items: center; gap: 10px; }
        .brand-icon {
            width: 44px; height: 44px;
            background: #0d9488;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .brand-icon svg { width: 26px; height: 26px; stroke: white; }
        .brand-name { font-size: 18px; font-weight: 800; color: #0f4c47; }
        .brand-sub  { font-size: 11px; color: #64748b; margin-top: 2px; }
        .report-title { text-align: left; }
        .report-title h1 { font-size: 16px; font-weight: 700; color: #0d9488; }
        .report-title p  { font-size: 11px; color: #64748b; margin-top: 4px; }

        /* ─── Meta ─── */
        .print-meta {
            display: flex;
            gap: 24px;
            padding: 12px 28px;
            background: #f0fdfa;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            color: #475569;
        }
        .print-meta strong { color: #1e293b; }

        /* ─── Summary Boxes ─── */
        .summary-row {
            display: flex;
            gap: 16px;
            padding: 14px 28px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-box {
            flex: 1;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid;
        }
        .summary-box.green { background: #f0fdf4; border-color: #bbf7d0; }
        .summary-box.red   { background: #fff1f2; border-color: #fecdd3; }
        .summary-box.blue  { background: #eff6ff; border-color: #bfdbfe; }
        .summary-box .label { font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; }
        .summary-box .value { font-size: 18px; font-weight: 800; }
        .summary-box.green .value { color: #16a34a; }
        .summary-box.red .value   { color: #dc2626; }
        .summary-box.blue .value  { color: #2563eb; }
        .summary-box .unit { font-size: 11px; font-weight: 600; color: #94a3b8; margin-right: 3px; }

        /* ─── Table ─── */
        .print-body { padding: 16px 28px; }
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
        }
        thead th {
            background: #0d9488;
            color: white;
            font-weight: 700;
            font-size: 12px;
            padding: 10px 12px;
            text-align: right;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 9px 12px; font-size: 12px; color: #334155; vertical-align: middle; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red   { background: #fee2e2; color: #991b1b; }

        .amount-incentive { color: #16a34a; font-weight: 800; }
        .amount-deduction { color: #dc2626; font-weight: 800; }

        /* ─── Footer ─── */
        .print-footer {
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #94a3b8;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-size: 14px;
        }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            @page { margin: 8mm 10mm; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="print-header">
        <div class="brand">
            <div class="brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                </svg>
            </div>
            <div>
                <div class="brand-name">المكتب التنفيذي</div>
                <div class="brand-sub">نظام إدارة الموارد البشرية</div>
            </div>
        </div>
        <div class="report-title">
            <h1>تقرير الاستحقاقات والخصومات</h1>
            <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <!-- Meta -->
    <div class="print-meta">
        @if(request('employee_id') && $entitlements->first())
            <span>الموظف: <strong>{{ $entitlements->first()->employee?->full_name_ar ?? $entitlements->first()->employee?->full_name ?? 'جميع الموظفين' }}</strong></span>
        @else
            <span>الموظف: <strong>جميع الموظفين</strong></span>
        @endif
        @if(request('from_date'))
            <span>من: <strong>{{ request('from_date') }}</strong></span>
        @endif
        @if(request('to_date'))
            <span>إلى: <strong>{{ request('to_date') }}</strong></span>
        @endif
        <span>عدد السجلات: <strong>{{ $entitlements->count() }}</strong></span>
    </div>

    <!-- Summary -->
    @php
        $totalIncentives = $entitlements->where('type','incentive')->sum('amount');
        $totalDeductions = $entitlements->where('type','deduction')->sum('amount');
        $net = $totalIncentives - $totalDeductions;
    @endphp
    <div class="summary-row">
        <div class="summary-box green">
            <div class="label">إجمالي الحوافز</div>
            <div class="value">{{ number_format($totalIncentives, 2) }}<span class="unit">ر.س</span></div>
        </div>
        <div class="summary-box red">
            <div class="label">إجمالي الخصومات</div>
            <div class="value">{{ number_format($totalDeductions, 2) }}<span class="unit">ر.س</span></div>
        </div>
        <div class="summary-box blue">
            <div class="label">الصافي</div>
            <div class="value">{{ number_format($net, 2) }}<span class="unit">ر.س</span></div>
        </div>
    </div>

    <!-- Table -->
    <div class="print-body">
        @if($entitlements->isEmpty())
            <div class="no-data">لا توجد بيانات لعرضها</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الموظف</th>
                    <th>نوع الاستحقاق</th>
                    <th>المسمى</th>
                    <th>السبب</th>
                    <th>التاريخ</th>
                    <th>الوقت</th>
                    <th>المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entitlements as $i => $ent)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $ent->employee?->full_name_ar ?? $ent->employee?->full_name }}</strong></td>
                    <td>
                        @if($ent->type == 'incentive')
                            <span class="badge badge-green">حافز</span>
                        @else
                            <span class="badge badge-red">خصم</span>
                        @endif
                    </td>
                    <td>{{ $ent->title }}</td>
                    <td>{{ $ent->reason ?? '-' }}</td>
                    <td dir="ltr">{{ $ent->date->format('Y-m-d') }}</td>
                    <td dir="ltr">{{ $ent->time ? $ent->time->format('H:i') : '-' }}</td>
                    <td class="{{ $ent->type == 'incentive' ? 'amount-incentive' : 'amount-deduction' }}" dir="ltr">
                        {{ $ent->type == 'incentive' ? '+' : '-' }}{{ number_format($ent->amount, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Footer -->
    <div class="print-footer">
        <span>تقرير آلي من نظام HR Dashboard &copy; {{ now()->year }}</span>
        <span>طُبع بواسطة: {{ auth()->user()->name ?? '-' }}</span>
    </div>

    <!-- Auto Print -->
    <script>
        window.addEventListener('load', function () {
            setTimeout(() => window.print(), 300);
        });
    </script>
</body>
</html>


