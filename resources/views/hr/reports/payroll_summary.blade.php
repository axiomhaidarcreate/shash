@extends('reports.layouts.print')
@section('title', 'تقرير الرواتب الشهرية')
@section('subtitle')
    <p class="text-sm text-secondary mt-1">
        شهر: <strong>{{ $month }}/{{ $year }}</strong>
    </p>
@endsection
@section('filters')
<form method="GET" class="flex items-center gap-2">
    <select name="month" class="border border-slate-200 rounded-lg text-sm p-2 bg-slate-50">
        @for($m=1;$m<=12;$m++)
        <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ $m }}</option>
        @endfor
    </select>
    <select name="year" class="border border-slate-200 rounded-lg text-sm p-2 bg-slate-50">
        @for($y=date('Y')-2;$y<=date('Y');$y++)
        <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
        @endfor
    </select>
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold">تطبيق</button>
</form>
@endsection
@section('content')
{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-indigo-600 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">إجمالي الرواتب المتوقعة</p>
        <p class="text-xl font-extrabold" dir="ltr">{{ number_format($totalExpected,2) }} ر.س</p>
    </div>
    <div class="bg-emerald-600 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">إجمالي المدفوع</p>
        <p class="text-xl font-extrabold" dir="ltr">{{ number_format($totalPaid,2) }} ر.س</p>
    </div>
    <div class="bg-amber-500 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">إجمالي المتبقي (غير مدفوع)</p>
        <p class="text-xl font-extrabold" dir="ltr">{{ number_format($totalUnpaid,2) }} ر.س</p>
    </div>
</div>

{{-- Paid Table --}}
<h3 class="font-bold text-primary text-base mb-3 flex items-center gap-2">
    <span class="inline-block w-3 h-3 rounded-full bg-emerald-500"></span>
    الموظفون المدفوع لهم ({{ $paidPayrolls->count() }})
</h3>
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white print-bg">
        <tr>
            <th class="px-4 py-3 font-bold">#</th>
            <th class="px-4 py-3 font-bold">اسم الموظف</th>
            <th class="px-4 py-3 font-bold">القسم</th>
            <th class="px-4 py-3 font-bold text-center">الراتب الأساسي</th>
            <th class="px-4 py-3 font-bold text-center">خصم التأخير</th>
            <th class="px-4 py-3 font-bold text-center">خصم الغياب</th>
            <th class="px-4 py-3 font-bold text-center">صافي الراتب</th>
            <th class="px-4 py-3 font-bold text-center">تاريخ الصرف</th>
        </tr>
    </thead>
    <tbody>
        @forelse($paidPayrolls as $i => $p)
        <tr class="{{ $i%2==0 ? 'bg-white' : 'bg-slate-50 print-bg' }}">
            <td class="px-4 py-3 text-slate-400">{{ $i+1 }}</td>
            <td class="px-4 py-3 font-bold text-primary">{{ $p->employee->full_name_ar }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $p->employee->department->name_ar ?? '-' }}</td>
            <td class="px-4 py-3 text-center" dir="ltr">{{ number_format($p->basic_salary,2) }}</td>
            <td class="px-4 py-3 text-center text-orange-500" dir="ltr">{{ $p->other_deductions > 0 ? '-'.number_format($p->other_deductions,2) : '-' }}</td>
            <td class="px-4 py-3 text-center text-red-500" dir="ltr">{{ $p->absent_deduction > 0 ? '-'.number_format($p->absent_deduction,2) : '-' }}</td>
            <td class="px-4 py-3 text-center font-bold text-emerald-700" dir="ltr">{{ number_format($p->net_salary,2) }}</td>
            <td class="px-4 py-3 text-center text-secondary">{{ $p->paid_at ? \Carbon\Carbon::parse($p->paid_at)->format('Y-m-d') : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-6 text-center text-slate-400">لا يوجد رواتب مدفوعة لهذا الشهر.</td></tr>
        @endforelse
        @if($paidPayrolls->count())
        <tr class="bg-indigo-50 font-bold print-bg">
            <td colspan="6" class="px-4 py-3 text-right">الإجمالي المدفوع</td>
            <td class="px-4 py-3 text-center text-indigo-700" dir="ltr">{{ number_format($totalPaid,2) }} ر.س</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>

{{-- Unpaid Table --}}
<h3 class="font-bold text-primary text-base mb-3 flex items-center gap-2">
    <span class="inline-block w-3 h-3 rounded-full bg-amber-400"></span>
    الموظفون غير المدفوع لهم ({{ $unpaidEmployees->count() }})
</h3>
<table class="w-full text-sm text-right">
    <thead class="bg-amber-700 text-white print-bg">
        <tr>
            <th class="px-4 py-3 font-bold">#</th>
            <th class="px-4 py-3 font-bold">اسم الموظف</th>
            <th class="px-4 py-3 font-bold">القسم</th>
            <th class="px-4 py-3 font-bold text-center">الراتب الأساسي (متوقع)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($unpaidEmployees as $i => $emp)
        <tr class="{{ $i%2==0 ? 'bg-white' : 'bg-amber-50 print-bg' }}">
            <td class="px-4 py-3 text-slate-400">{{ $i+1 }}</td>
            <td class="px-4 py-3 font-bold text-primary">{{ $emp->full_name_ar }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $emp->department->name_ar ?? '-' }}</td>
            <td class="px-4 py-3 text-center text-amber-700 font-bold" dir="ltr">{{ number_format($emp->base_salary,2) }} ر.س</td>
        </tr>
        @empty
        <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">جميع الموظفين تم صرف رواتبهم ✅</td></tr>
        @endforelse
        @if($unpaidEmployees->count())
        <tr class="bg-amber-100 font-bold print-bg">
            <td colspan="3" class="px-4 py-3 text-right">إجمالي المتبقي</td>
            <td class="px-4 py-3 text-center text-amber-800" dir="ltr">{{ number_format($totalUnpaid,2) }} ر.س</td>
        </tr>
        @endif
    </tbody>
</table>
@endsection


