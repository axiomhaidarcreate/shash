<x-layouts.admin>
    <div class="flex items-center justify-between gap-4 mb-8 print:hidden">
        <div>
            <h2 class="text-2xl font-bold text-primary">قالب العقد: {{ $contractTemplate->title }}</h2>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0v2.796c0 1.136.879 2.073 2.006 2.152a48.24 48.24 0 006.488 0c1.127-.08 2.006-1.016 2.006-2.152V10.25z" /></svg>
                طباعة العقد
            </button>
            <a href="{{ route('hr.contract_templates.index') }}" class="bg-white hover:bg-slate-50 text-primary px-4 py-2.5 rounded-xl font-semibold border border-slate-200">رجوع</a>
        </div>
    </div>

    <!-- Contract Content -->
    <div class="bg-white rounded-xl shadow border border-slate-100 p-10 md:p-14 mb-8 print:shadow-none print:border-none print:p-0">
        <h1 class="text-2xl font-bold text-center mb-8 pb-4 border-b-2 border-slate-800">{{ $contractTemplate->title }}</h1>
        <div class="whitespace-pre-wrap text-lg leading-loose text-primary">
{{ $contractTemplate->content }}
        </div>
        
        <div class="mt-24 w-full flex justify-between px-10">
            <div class="text-center font-bold text-lg">
                <p>الطرف الأول (الشركة)</p>
                <div class="border-b-2 border-slate-800 w-48 mx-auto mt-16"></div>
            </div>
            <div class="text-center font-bold text-lg">
                <p>الطرف الثاني (الموظف)</p>
                <div class="border-b-2 border-slate-800 w-48 mx-auto mt-16"></div>
            </div>
        </div>
    </div>
</x-layouts.admin>


