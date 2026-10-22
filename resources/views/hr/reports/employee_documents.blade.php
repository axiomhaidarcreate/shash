@extends('reports.layouts.print')
@section('title','تقرير صلاحية وثائق موظف')
@section('subtitle')<p class="text-sm text-secondary mt-1">{{ $employee->full_name_ar }} — {{ $employee->employee_code }}</p>@endsection
@section('content')
<div class="bg-slate-50 rounded-xl p-6 mb-8 flex items-center gap-5 border border-slate-200 print-bg">
    <img src="{{ $employee->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($employee->full_name_ar).'&background=1e293b&color=fff&size=100' }}" class="w-16 h-16 rounded-full object-cover border-4 border-white shadow flex-shrink-0">
    <div class="grid grid-cols-3 gap-4 text-sm flex-1">
        <div><span class="block text-slate-400 text-xs mb-0.5">الاسم</span><strong class="text-primary">{{ $employee->full_name_ar }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">الرقم الوظيفي</span><strong>{{ $employee->employee_code }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">القسم</span><strong>{{ $employee->department->name_ar ?? '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">رقم الهوية</span><strong>{{ $employee->national_id ?? '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">الجنسية</span><strong>{{ $employee->nationality ?? '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">تاريخ الإصدار</span><strong>{{ now()->format('Y-m-d') }}</strong></div>
    </div>
</div>

@if(count($docs))
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white print-bg">
        <tr>
            <th class="px-4 py-3">الوثيقة</th>
            <th class="px-4 py-3 text-center">تاريخ الانتهاء</th>
            <th class="px-4 py-3 text-center">الأيام المتبقية</th>
            <th class="px-4 py-3 text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($docs as $i => $doc)
        @php
            $bg = match($doc['status']){
                'ok'      => $i%2==0?'bg-white':'bg-slate-50',
                'warning' => 'bg-amber-50',
                'expired' => 'bg-red-50',
            };
            $badgeBg = match($doc['status']){
                'ok'      => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'warning' => 'bg-amber-100 text-amber-700 border-amber-200',
                'expired' => 'bg-red-100 text-red-700 border-red-200',
            };
            $statusLabel = match($doc['status']){
                'ok'      => 'سارية',
                'warning' => 'قريبة الانتهاء',
                'expired' => 'منتهية الصلاحية',
            };
            $daysText = $doc['days'] < 0 ? 'منذ '.abs($doc['days']).' يوم' : 'متبقي '.$doc['days'].' يوم';
        @endphp
        <tr class="{{ $bg }} print-bg">
            <td class="px-4 py-3 font-bold text-primary">{{ $doc['label'] }}</td>
            <td class="px-4 py-3 text-center font-bold">{{ $doc['date'] }}</td>
            <td class="px-4 py-3 text-center {{ $doc['days'] < 0 ? 'text-red-600 font-bold' : ($doc['days'] <= 30 ? 'text-amber-600 font-bold' : 'text-slate-600') }}">
                {{ $daysText }}
            </td>
            <td class="px-4 py-3 text-center">
                <span class="border text-[11px] font-bold px-3 py-1 rounded-full {{ $badgeBg }} print-bg">{{ $statusLabel }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Status Summary --}}
<div class="grid grid-cols-3 gap-4">
    @php
        $okCount      = collect($docs)->where('status','ok')->count();
        $warnCount    = collect($docs)->where('status','warning')->count();
        $expiredCount = collect($docs)->where('status','expired')->count();
    @endphp
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-emerald-700 font-bold mb-1">وثائق سارية</p>
        <p class="text-3xl font-extrabold text-emerald-700">{{ $okCount }}</p>
    </div>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-amber-700 font-bold mb-1">قريبة الانتهاء</p>
        <p class="text-3xl font-extrabold text-amber-700">{{ $warnCount }}</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-red-700 font-bold mb-1">منتهية الصلاحية</p>
        <p class="text-3xl font-extrabold text-red-700">{{ $expiredCount }}</p>
    </div>
</div>

@else
<div class="text-center py-16 text-slate-400">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto mb-4 text-slate-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
    <p class="text-lg font-bold">لا توجد وثائق مسجلة لهذا الموظف</p>
    <p class="text-sm mt-1">يُرجى إضافة تواريخ الانتهاء من صفحة تعديل الموظف.</p>
</div>
@endif
@endsection


