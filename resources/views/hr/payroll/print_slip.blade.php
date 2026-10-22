<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مسير راتب - {{ $payroll->employee->full_name_ar }} - {{ $payroll->pay_period_month }}/{{ $payroll->pay_period_year }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f1f5f9;
        }

        @media print {
            body {
                background-color: #ffffff;
                margin: 0;
                padding: 0;
            }
            * { 
                color: #000 !important; 
                background-color: transparent !important; 
                border-color: #000 !important; 
            }
            .print-bg, div[class*="bg-"] {
                border: 1px solid #000 !important;
            }
            th, td { border: 1px solid #000 !important; }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body class="py-10">

    <!-- Print Actions -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-end gap-4 no-print">
        <a href="javascript:history.back()" class="px-6 py-2 bg-white border border-slate-300 text-primary rounded-lg hover:bg-slate-50 font-bold">عودة</a>
        <button onclick="window.print()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0v2.796c0 1.136.879 2.073 2.006 2.152a48.24 48.24 0 006.488 0c1.127-.08 2.006-1.016 2.006-2.152V10.25z" /></svg>
            طباعة مسير الراتب
        </button>
    </div>

    <!-- Document Container -->
    <div class="max-w-4xl mx-auto bg-white p-10 lg:p-12 shadow-xl border border-slate-200 rounded-xl print-container">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-slate-800 pb-6 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-primary tracking-tight">مسير راتب (إقرار استلام)</h1>
                <p class="text-secondary mt-2 text-sm font-bold">تاريخ الإصدار: {{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
            </div>
            <div class="text-left">
                <img src="{{ \App\Models\Setting::get('app_logo') ? asset('storage/' . \App\Models\Setting::get('app_logo')) : 'https://placehold.co/150x50/1e293b/ffffff?text=Company+Logo' }}" alt="Logo" class="h-12 mb-2 ml-auto">
                <h2 class="text-lg font-bold text-primary">{{ \App\Models\Setting::get('app_name', 'مؤسسة إنجاز') }}</h2>
                <p class="text-sm text-secondary">قسم الموارد البشرية والرواتب</p>
            </div>
        </div>

        <div class="text-center mb-8">
            <h3 class="text-xl font-bold bg-slate-100 text-primary py-2 px-6 rounded-lg inline-block print-bg border border-slate-200">
                رواتب شهر ( {{ date('F', mktime(0, 0, 0, $payroll->pay_period_month, 10)) }} - {{ $payroll->pay_period_year }} )
            </h3>
        </div>

        <!-- Employee Info -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 mb-8 print-bg">
            <h3 class="text-lg font-bold text-primary mb-4 border-b border-slate-200 pb-2">بيانات الموظف</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="block text-secondary mb-1">الرقم الوظيفي:</span>
                    <strong class="text-primary">{{ $payroll->employee->employee_code }}</strong>
                </div>
                <div>
                    <span class="block text-secondary mb-1">اسم الموظف:</span>
                    <strong class="text-primary">{{ $payroll->employee->full_name_ar ?? $payroll->employee->full_name }}</strong>
                </div>
                <div>
                    <span class="block text-secondary mb-1">القسم:</span>
                    <strong class="text-primary">{{ $payroll->employee->department->name_ar ?? 'غير محدد' }}</strong>
                </div>
                <div>
                    <span class="block text-secondary mb-1">المسمى الوظيفي:</span>
                    <strong class="text-primary">{{ $payroll->employee->job_title_ar ?? $payroll->employee->job_title ?? 'غير محدد' }}</strong>
                </div>
            </div>
        </div>

        <!-- Financial Breakdown -->
        <div class="mb-10">
            <h3 class="text-lg font-bold text-primary mb-4 border-b border-slate-200 pb-2">التفاصيل المالية للراتب</h3>
            <table class="w-full text-sm text-right text-primary">
                <thead class="bg-slate-800 text-white print-bg">
                    <tr>
                        <th class="px-4 py-3 font-bold border border-slate-700 w-2/3">البيان</th>
                        <th class="px-4 py-3 font-bold border border-slate-700 text-center">المبلغ (ر.س)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Income -->
                    <tr class="bg-emerald-50 print-bg">
                        <td class="px-4 py-3 border border-slate-200 font-bold text-emerald-800" colspan="2">الاستحقاقات (الإيرادات)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 border border-slate-200">الراتب الأساسي</td>
                        <td class="px-4 py-3 border border-slate-200 text-center font-bold" dir="ltr">{{ number_format($payroll->basic_salary, 2) }}</td>
                    </tr>
                    @if($payroll->housing_allowance > 0)
                    <tr>
                        <td class="px-4 py-3 border border-slate-200">بدل السكن</td>
                        <td class="px-4 py-3 border border-slate-200 text-center font-bold" dir="ltr">{{ number_format($payroll->housing_allowance, 2) }}</td>
                    </tr>
                    @endif
                    @if($payroll->transport_allowance > 0)
                    <tr>
                        <td class="px-4 py-3 border border-slate-200">بدل المواصلات</td>
                        <td class="px-4 py-3 border border-slate-200 text-center font-bold" dir="ltr">{{ number_format($payroll->transport_allowance, 2) }}</td>
                    </tr>
                    @endif
                    @if($payroll->other_allowances > 0)
                    <tr>
                        <td class="px-4 py-3 border border-slate-200">بدلات أخرى</td>
                        <td class="px-4 py-3 border border-slate-200 text-center font-bold" dir="ltr">{{ number_format($payroll->other_allowances, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="bg-slate-50 print-bg">
                        <td class="px-4 py-3 border border-slate-200 font-bold">إجمالي الاستحقاقات</td>
                        <td class="px-4 py-3 border border-slate-200 text-center font-bold" dir="ltr">{{ number_format($payroll->gross_salary, 2) }}</td>
                    </tr>

                    <!-- Deductions -->
                    <tr class="bg-red-50 print-bg">
                        <td class="px-4 py-3 border border-slate-200 font-bold text-red-800" colspan="2">الاستقطاعات (الخصومات)</td>
                    </tr>
                    @if($payroll->social_insurance > 0)
                    <tr>
                        <td class="px-4 py-3 border border-slate-200">التأمينات الاجتماعية</td>
                        <td class="px-4 py-3 border border-slate-200 text-center font-bold text-red-600" dir="ltr">-{{ number_format($payroll->social_insurance, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="px-4 py-3 border border-slate-200">خصم الغياب والتأخير <span class="text-xs text-slate-400">({{ $payroll->absent_days }} يوم/أيام)</span></td>
                        <td class="px-4 py-3 border border-slate-200 text-center font-bold text-red-600" dir="ltr">-{{ number_format($payroll->absent_deduction, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 border border-slate-200">خصم السلف والقروض (أخرى)</td>
                        <td class="px-4 py-3 border border-slate-200 text-center font-bold text-red-600" dir="ltr">-{{ number_format($payroll->other_deductions, 2) }}</td>
                    </tr>
                    <tr class="bg-slate-50 print-bg">
                        <td class="px-4 py-3 border border-slate-200 font-bold">إجمالي الاستقطاعات</td>
                        <td class="px-4 py-3 border border-slate-200 text-center font-bold text-red-600" dir="ltr">-{{ number_format($payroll->social_insurance + $payroll->absent_deduction + $payroll->other_deductions, 2) }}</td>
                    </tr>

                    <!-- Net Salary -->
                    <tr class="bg-slate-800 text-white print-bg text-lg">
                        <td class="px-4 py-4 border border-slate-700 font-extrabold text-left">صافي الراتب المستحق للاستلام:</td>
                        <td class="px-4 py-4 border border-slate-700 text-center font-extrabold" dir="ltr">{{ number_format($payroll->net_salary, 2) }} ر.س</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Declaration & Signatures -->
        <div class="border border-slate-200 rounded-xl p-6 mt-12 page-break-inside-avoid">
            <h4 class="font-bold text-primary mb-3">إقرار بالاستلام</h4>
            <p class="text-sm text-slate-600 leading-relaxed text-justify mb-8">
                أقر أنا الموقع أدناه / <strong class="text-primary">{{ $payroll->employee->full_name_ar }}</strong> (رقم الهوية/الإقامة: <span dir="ltr">{{ $payroll->employee->national_id ?? '___________' }}</span>)، بأنني قد استلمت كافة مستحقاتي ورواتبي عن شهر ({{ $payroll->pay_period_month }}/{{ $payroll->pay_period_year }}) بمبلغ وقدره (<strong dir="ltr">{{ number_format($payroll->net_salary, 2) }} ر.س</strong>)، وأن هذا الراتب يشمل كافة حقوقي وبدلاتي المتعلقة بهذا الشهر، وبناءً عليه أوقع وأبصم.
            </p>

            {{-- Status Banner --}}
            @if($payroll->status === 'approved' && !$payroll->employee_signature_url)
                <div class="bg-amber-50 border border-amber-300 rounded-lg px-4 py-3 mb-6 flex items-center gap-2 text-amber-800 text-sm font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    بانتظار توقيع الموظف وبصمة وجهه من خلال تطبيقه
                </div>
            @elseif($payroll->employee_signature_url && $payroll->is_face_id_verified)
                <div class="bg-emerald-50 border border-emerald-300 rounded-lg px-4 py-3 mb-6 flex items-center gap-2 text-emerald-800 text-sm font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    تم التوقيع والتحقق من بصمة الوجه بتاريخ: {{ $payroll->employee_received_at ? \Carbon\Carbon::parse($payroll->employee_received_at)->format('Y-m-d H:i') : '—' }}
                </div>
            @endif

            <div class="flex justify-between items-end mt-8 gap-4">

                {{-- Digital Signature Box --}}
                <div class="w-1/3 text-center">
                    @if($payroll->employee_signature_url && !str_starts_with($payroll->employee_signature_url, 'local_'))
                        <div class="border-2 border-emerald-400 rounded-xl overflow-hidden mb-2 bg-white mx-auto" style="width:180px; height:100px;">
                            <img src="{{ $payroll->employee_signature_url }}" alt="توقيع الموظف" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xs font-bold text-emerald-700 flex items-center justify-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            توقيع رقمي مُوثَّق
                        </span>
                    @else
                        <div class="border-2 border-dashed border-slate-300 rounded-lg mb-2 bg-slate-50 flex items-center justify-center mx-auto" style="width:180px; height:100px;">
                            <span class="text-slate-300 text-xs font-bold">مكان توقيع الموظف</span>
                        </div>
                        <span class="text-sm font-bold text-secondary">توقيع الموظف المستلم</span>
                    @endif
                </div>

                {{-- Face Verification Badge --}}
                <div class="w-1/3 text-center flex flex-col items-center">
                    @if($payroll->is_face_id_verified)
                        <div class="w-24 h-24 border-2 border-emerald-400 rounded-full mb-2 flex items-center justify-center bg-emerald-50 print-bg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-emerald-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-emerald-700">✅ تم التحقق بصمة الوجه</span>
                    @else
                        <div class="w-24 h-24 border-2 border-dashed border-slate-300 rounded-full mb-2 flex items-center justify-center bg-slate-50 print-bg">
                            <span class="text-slate-300 text-xs font-bold">بصمة الوجه</span>
                        </div>
                        <span class="text-sm font-bold text-secondary">بصمة الموظف المستلم</span>
                    @endif
                </div>

                {{-- Date & Manager Approval --}}
                <div class="w-1/3 text-center">
                    @if($payroll->paid_at)
                        <div class="text-primary font-bold pb-1 mb-2 text-sm">
                            {{ \Carbon\Carbon::parse($payroll->paid_at)->format('Y-m-d') }}
                        </div>
                        <div class="border-b-2 border-dashed border-slate-300 w-3/4 mx-auto mb-2"></div>
                    @else
                        <div class="border-b-2 border-dashed border-slate-300 w-3/4 mx-auto mb-2">&nbsp;</div>
                    @endif
                    <span class="text-sm font-bold text-secondary">تاريخ الصرف</span>
                </div>
            </div>
            
            <div class="mt-12 text-center text-xs text-slate-400 border-t border-slate-100 pt-4">
                توقيع مدير الشؤون الإدارية / المحاسب: ........................................
            </div>
        </div>


    </div>

</body>
</html>


