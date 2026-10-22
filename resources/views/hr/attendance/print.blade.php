<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير سجل الحضور</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background: white;
                color: black;
                font-size: 12pt;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: right;
            }
            th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }
            .text-red-500 {
                color: #ef4444 !important;
                -webkit-print-color-adjust: exact;
            }
        }
        body {
            background-color: #f8fafc;
            padding: 40px;
        }
        .page-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            border-radius: 16px;
        }
    </style>
</head>
<body class="font-cairo text-primary">

    <div class="page-container">
        
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-bold mb-1">تقرير سجل الحضور</h1>
                <p class="text-secondary">الفترة: {{ $dateFrom }} إلى {{ $dateTo }}</p>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.728 15.683A3.001 3.001 0 008 21h8a3.001 3.001 0 001.272-5.317M12 21v-4.5m0 0a1.5 1.5 0 00-1.5-1.5H9.75m4.5 0h-1.5a1.5 1.5 0 00-1.5 1.5m-3-12h9m-9 3h9m-9 3h9M5.25 6h13.5c.828 0 1.5.672 1.5 1.5v7.5c0 .828-.672 1.5-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-7.5C3.75 6.672 4.422 6 5.25 6z" /></svg>
                    طباعة
                </button>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-sm text-right text-primary">
            <thead>
                <tr>
                    <th class="font-bold">الموظف</th>
                    <th class="font-bold">التاريخ</th>
                    <th class="font-bold">تسجيل الدخول</th>
                    <th class="font-bold">تسجيل الخروج</th>
                    <th class="font-bold">الحالة</th>
                    <th class="font-bold text-center">الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                <tr>
                    <td class="font-bold text-primary">{{ $attendance->employee->full_name_ar ?? 'غير محدد' }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}</td>
                    
                    <td dir="ltr" class="text-right">
                        {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '--:--' }}
                        @if($attendance->delay_minutes > 0)
                            <div class="text-xs text-red-500 font-bold mt-0.5">+{{ $attendance->delay_minutes }} دقيقة</div>
                        @endif
                    </td>
                    
                    <td dir="ltr" class="text-right">
                        {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '--:--' }}
                        @if($attendance->early_leave_minutes > 0)
                            <div class="text-xs text-red-500 font-bold mt-0.5">-{{ $attendance->early_leave_minutes }} دقيقة</div>
                        @endif
                    </td>

                    <td>
                        @if($attendance->status === 'present')
                            حاضر
                        @elseif($attendance->status === 'late')
                            متأخر
                        @else
                            غائب
                        @endif
                    </td>

                    <td class="text-center">
                        @if($attendance->is_deducted)
                            تم خصم ({{ $attendance->deduction_amount }} ر.س)
                        @elseif($attendance->delay_minutes > 15)
                            يحتاج إجراء
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-secondary">لا يوجد سجلات حضور لهذه الفترة.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-12 text-sm text-secondary text-center print:block">
            طُبع بواسطة نظام إدارة الموارد البشرية بتاريخ {{ \Carbon\Carbon::now()->format('Y/m/d h:i A') }}
        </div>
    </div>

</body>
</html>


