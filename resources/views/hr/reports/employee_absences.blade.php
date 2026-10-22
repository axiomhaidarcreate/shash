@extends('reports.layouts.print')
@section('title','تقرير غيابات موظف')
@section('subtitle')<p class="text-sm text-secondary mt-1">{{ $employee->full_name_ar }} | من {{ $from }} إلى {{ $to }}</p>@endsection
@section('filters')
<form method="GET" class="flex items-center gap-2">
    <input type="date" name="from" value="{{ $from }}" class="border border-slate-200 rounded-lg text-sm p-2 bg-slate-50">
    <span class="text-slate-400 text-sm">إلى</span>
    <input type="date" name="to" value="{{ $to }}" class="border border-slate-200 rounded-lg text-sm p-2 bg-slate-50">
    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold">تطبيق</button>
</form>
@endsection
@section('content')
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-red-600 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">إجمالي أيام الغياب</p>
        <p class="text-3xl font-extrabold">{{ $absences->count() }}</p>
        <p class="text-xs text-white/70 mt-1">يوم</p>
    </div>
    <div class="bg-slate-700 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">الفترة</p>
        <p class="text-base font-bold mt-2">{{ $from }} <br> {{ $to }}</p>
    </div>
    <div class="bg-red-500 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">إجمالي الخصم (تقديري)</p>
        @php $daily = $employee->base_salary ? ($employee->base_salary / 30) : 0; @endphp
        <p class="text-2xl font-extrabold">{{ number_format($absences->count() * $daily, 2) }}</p>
        <p class="text-xs text-white/70 mt-1">ر.س</p>
    </div>
</div>
<table class="w-full text-sm text-right">
    <thead class="bg-red-700 text-white print-bg">
        <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">التاريخ</th>
            <th class="px-4 py-3">اليوم</th>
            <th class="px-4 py-3 text-center">مبلغ الخصم (تقديري)</th>
            <th class="px-4 py-3 text-center">الملاحظات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($absences as $i => $a)
        <tr class="{{ $i%2==0?'bg-white':'bg-red-50 print-bg' }}">
            <td class="px-4 py-3 text-slate-400">{{ $i+1 }}</td>
            <td class="px-4 py-3 font-bold text-primary">{{ \Carbon\Carbon::parse($a->work_date)->format('Y-m-d') }}</td>
            <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($a->work_date)->locale('ar')->dayName }}</td>
            <td class="px-4 py-3 text-center font-bold text-red-500">{{ number_format($daily, 2) }} ر.س</td>
            <td class="px-4 py-3 text-center text-slate-400 text-xs">{{ $a->notes ?? '-' }}</td>
        </tr>
        @empty<tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">لا توجد غيابات في هذه الفترة ✅</td></tr>@endforelse
        @if($absences->count())
        <tr class="bg-red-700 text-white font-bold print-bg">
            <td colspan="3" class="px-4 py-3 text-right">الإجمالي</td>
            <td class="px-4 py-3 text-center">{{ number_format($absences->count() * $daily, 2) }} ر.س</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection

