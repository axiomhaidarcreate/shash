@extends('reports.layouts.print')
@section('title','تقرير رواتب موظف')
@section('subtitle')<p class="text-sm text-secondary mt-1">{{ $employee->full_name_ar }} — {{ $employee->department->name_ar ?? '' }}</p>@endsection
@section('content')
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-indigo-600 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">الراتب الأساسي الحالي</p>
        <p class="text-xl font-extrabold" dir="ltr">{{ number_format($employee->base_salary,2) }} ر.س</p>
    </div>
    <div class="bg-emerald-600 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">إجمالي المستلم</p>
        <p class="text-xl font-extrabold" dir="ltr">{{ number_format($payrolls->where('status','paid')->sum('net_salary'),2) }} ر.س</p>
    </div>
    <div class="bg-slate-700 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">عدد الأشهر المصروفة</p>
        <p class="text-xl font-extrabold">{{ $payrolls->where('status','paid')->count() }}</p>
    </div>
</div>
<table class="w-full text-sm text-right">
    <thead class="bg-slate-800 text-white print-bg">
        <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">الشهر/السنة</th>
            <th class="px-4 py-3 text-center">الراتب الأساسي</th>
            <th class="px-4 py-3 text-center">خصم غياب</th>
            <th class="px-4 py-3 text-center">خصم تأخير / أخرى</th>
            <th class="px-4 py-3 text-center">صافي الراتب</th>
            <th class="px-4 py-3 text-center">تاريخ الصرف</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payrolls as $i => $p)
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-4 py-3 text-slate-400">{{ $i+1 }}</td>
            <td class="px-4 py-3 font-bold text-primary">{{ $p->pay_period_month }}/{{ $p->pay_period_year }}</td>
            <td class="px-4 py-3 text-center" dir="ltr">{{ number_format($p->basic_salary,2) }}</td>
            <td class="px-4 py-3 text-center text-red-500" dir="ltr">{{ $p->absent_deduction > 0 ? '-'.number_format($p->absent_deduction,2) : '-' }}</td>
            <td class="px-4 py-3 text-center text-orange-500" dir="ltr">{{ $p->other_deductions > 0 ? '-'.number_format($p->other_deductions,2) : '-' }}</td>
            <td class="px-4 py-3 text-center font-bold text-emerald-700" dir="ltr">{{ number_format($p->net_salary,2) }}</td>
            <td class="px-4 py-3 text-center text-secondary text-xs">{{ $p->paid_at ? \Carbon\Carbon::parse($p->paid_at)->format('Y-m-d') : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">لا توجد رواتب مسجلة.</td></tr>
        @endforelse
        @if($payrolls->count())
        <tr class="bg-slate-800 text-white font-bold print-bg">
            <td colspan="5" class="px-4 py-3 text-right">الإجمالي الكلي المستلم</td>
            <td class="px-4 py-3 text-center" dir="ltr">{{ number_format($payrolls->where('status','paid')->sum('net_salary'),2) }} ر.س</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection


