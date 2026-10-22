<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند سحب - {{ $leaveRequest->employee->full_name_ar }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f1f5f9; }
        
        @media print {
            body { background-color: #fff; margin: 0; padding: 0; }
            * { 
                color: #000 !important; 
                background-color: transparent !important; 
                border-color: #000 !important; 
            }
            .print-bg, div[class*="bg-"] {
                border: 1px solid #000 !important;
            }
            th, td { border: 1px solid #000 !important; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; border: none !important; max-width: 100% !important; }
            @page { size: A4; margin: 1.5cm; }
        }
    </style>
</head>
<body class="py-10">

    <!-- Print Actions -->
    <div class="max-w-3xl mx-auto mb-6 flex justify-end gap-4 no-print">
        <a href="javascript:history.back()" class="px-6 py-2 bg-white border border-slate-300 text-primary rounded-lg hover:bg-slate-50 font-bold">عودة</a>
        <button onclick="window.print()" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-2.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
            طباعة السند
        </button>
    </div>

    <!-- Document -->
    <div class="max-w-3xl mx-auto bg-white p-10 shadow-xl border border-slate-200 rounded-xl print-container">

        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-orange-500 pb-6 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-primary">سند صرف مبلغ نقدي</h1>
                <p class="text-secondary mt-1 text-sm font-bold">تاريخ الإصدار: {{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
                <p class="text-secondary text-sm">رقم السند: <strong class="text-primary">#{{ substr($leaveRequest->id, 0, 8) }}</strong></p>
            </div>
            <div class="text-left">
                <img src="{{ \App\Models\Setting::get('app_logo') ? asset('storage/' . \App\Models\Setting::get('app_logo')) : 'https://placehold.co/150x50/f97316/ffffff?text=Company+Logo' }}" alt="Logo" class="h-12 mb-2 ml-auto">
                <h2 class="text-lg font-bold text-primary">{{ \App\Models\Setting::get('app_name', 'مؤسسة إنجاز') }}</h2>
                <p class="text-sm text-secondary">قسم الشؤون الإدارية</p>
            </div>
        </div>

        <!-- Preamble -->
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-8 print-bg">
            <p class="text-sm text-primary leading-loose">
                بناءً على طلب السحب المقدم بتاريخ <strong>{{ \Carbon\Carbon::parse($leaveRequest->created_at)->format('Y-m-d') }}</strong>
                واعتماده بتاريخ <strong>{{ \Carbon\Carbon::parse($leaveRequest->updated_at)->format('Y-m-d') }}</strong>،
                تم صرف المبلغ المبين أدناه للموظف المذكور وفقاً لأحكام وسياسات المؤسسة.
            </p>
        </div>

        <!-- Employee & Amount Info -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                <h3 class="text-sm font-bold text-slate-600 mb-4 border-b border-slate-200 pb-2">بيانات الموظف المستلم</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-secondary">الاسم:</span>
                        <strong class="text-primary">{{ $leaveRequest->employee->full_name_ar }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-secondary">الرقم الوظيفي:</span>
                        <strong class="text-primary">{{ $leaveRequest->employee->employee_code }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-secondary">القسم:</span>
                        <strong class="text-primary">{{ $leaveRequest->employee->department->name_ar ?? 'غير محدد' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-secondary">رقم الهوية:</span>
                        <strong class="text-primary">{{ $leaveRequest->employee->national_id ?? '___________' }}</strong>
                    </div>
                </div>
            </div>

            <div class="bg-orange-500 rounded-xl p-5 flex flex-col items-center justify-center text-center print-bg">
                <p class="text-white/80 text-sm font-bold mb-1">المبلغ المصروف</p>
                <div class="text-4xl font-extrabold text-white" dir="ltr">
                    {{ number_format($leaveRequest->amount, 2) }}
                </div>
                <p class="text-white font-bold mt-1">ريال سعودي</p>
            </div>
        </div>

        <!-- Reason -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-600 mb-2">سبب / ملاحظات السحب</h3>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-primary leading-relaxed min-h-16">
                {{ $leaveRequest->reason ?: 'لم يتم تحديد سبب.' }}
            </div>
        </div>

        <!-- Deduction Note -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-10 print-bg">
            <p class="text-xs text-red-700 font-bold">📋 ملاحظة محاسبية:</p>
            <p class="text-xs text-red-600 mt-1">
                سيتم خصم المبلغ المصروف (<strong dir="ltr">{{ number_format($leaveRequest->amount, 2) }} ر.س</strong>) 
                تلقائياً من راتب الموظف في شهر اعتماد الطلب 
                (<strong>{{ \Carbon\Carbon::parse($leaveRequest->updated_at)->format('m/Y') }}</strong>).
            </p>
        </div>

        <!-- Signatures -->
        <div class="border border-slate-200 rounded-xl p-6">
            <h3 class="text-sm font-bold text-primary mb-6 text-center">التوقيعات والإقرار بالاستلام</h3>
            <div class="flex justify-between items-end">
                <!-- Employee Signature -->
                <div class="text-center w-1/3">
                    <div class="border-b-2 border-dashed border-slate-300 w-3/4 mx-auto mb-2 pb-8"></div>
                    <span class="text-sm font-bold text-slate-600">توقيع الموظف المستلم</span>
                    <p class="text-xs text-slate-400 mt-1">{{ $leaveRequest->employee->full_name_ar }}</p>
                </div>

                <!-- Fingerprint -->
                <div class="text-center flex flex-col items-center w-1/3">
                    <div class="w-24 h-32 border-2 border-slate-300 rounded-lg flex items-center justify-center bg-slate-50 print-bg mb-2">
                        <span class="text-slate-300 text-xs font-bold transform -rotate-45">مكان البصمة</span>
                    </div>
                    <span class="text-sm font-bold text-slate-600">بصمة الإصبع</span>
                </div>

                <!-- Manager Signature -->
                <div class="text-center w-1/3">
                    <div class="border-b-2 border-dashed border-slate-300 w-3/4 mx-auto mb-2 pb-8"></div>
                    <span class="text-sm font-bold text-slate-600">توقيع المدير / المفوض</span>
                    <p class="text-xs text-slate-400 mt-1">{{ \App\Models\Setting::get('app_name', 'الإدارة') }}</p>
                </div>
            </div>

            <div class="mt-8 text-center text-xs text-slate-400 border-t border-slate-100 pt-4">
                تم إصدار هذا السند إلكترونياً من نظام {{ \App\Models\Setting::get('app_name', 'إدارة الموارد البشرية') }}
                — بتاريخ {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}
            </div>
        </div>

    </div>
</body>
</html>


