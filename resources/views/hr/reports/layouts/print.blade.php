<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') — {{ \App\Models\Setting::get('app_name', 'نظام الموارد البشرية') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background: #f1f5f9; }
        
        @media print {
            body { background: #fff; }
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
            .print-container { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important; padding: 0.5cm !important; }
            @page { size: A4; margin: 1cm; }
        }
    </style>
    @stack('styles')
</head>
<body class="py-8">
    <!-- Actions Bar -->
    <div class="max-w-5xl mx-auto mb-5 flex justify-between items-center no-print">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-white border border-slate-300 text-primary rounded-lg hover:bg-slate-50 font-bold text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                عودة
            </a>
            @yield('filters')
        </div>
        <button onclick="window.print()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-2.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
            طباعة التقرير
        </button>
    </div>

    <!-- Report Container -->
    <div class="max-w-5xl mx-auto bg-white shadow-xl border border-slate-200 rounded-xl p-10 print-container">
        <!-- Report Header -->
        <div class="flex justify-between items-start border-b-2 border-slate-800 pb-6 mb-8 print-bg">
            <div>
                <h1 class="text-2xl font-extrabold text-primary">@yield('title')</h1>
                <p class="text-secondary text-sm mt-1">تاريخ الإصدار: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>
                @yield('subtitle')
            </div>
            <div class="text-left">
                <img src="{{ \App\Models\Setting::get('app_logo') ? asset('storage/'.\App\Models\Setting::get('app_logo')) : 'https://placehold.co/150x40/1e293b/ffffff?text=HR+System' }}" alt="Logo" class="h-10 mb-2 ml-auto">
                <p class="text-sm font-bold text-primary">{{ \App\Models\Setting::get('app_name','نظام الموارد البشرية') }}</p>
                <p class="text-xs text-slate-400">تقرير رسمي — سري</p>
            </div>
        </div>

        @yield('content')

        <!-- Footer -->
        <div class="mt-10 pt-4 border-t border-slate-100 text-center text-xs text-slate-400">
            صدر هذا التقرير إلكترونياً من {{ \App\Models\Setting::get('app_name','نظام الموارد البشرية') }} — {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}
        </div>
    </div>
</body>
</html>


