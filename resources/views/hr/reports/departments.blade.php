@extends('reports.layouts.print')
@section('title','تقرير الأقسام')
@section('subtitle')<p class="text-sm text-secondary mt-1">إجمالي الأقسام: <strong>{{ $departments->count() }}</strong></p>@endsection
@section('content')
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white print-bg">
        <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">اسم القسم</th>
            <th class="px-4 py-3 text-center">عدد الموظفين</th>
            <th class="px-4 py-3 text-center">إجمالي الرواتب</th>
            <th class="px-4 py-3 text-center">متوسط الراتب</th>
        </tr>
    </thead>
    <tbody>
        @forelse($departments as $i => $dept)
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-4 py-3 text-slate-400">{{ $i+1 }}</td>
            <td class="px-4 py-3 font-bold text-primary">{{ $dept->name_ar }}</td>
            <td class="px-4 py-3 text-center font-bold text-indigo-600">{{ $dept->employees_count }}</td>
            <td class="px-4 py-3 text-center font-bold text-emerald-700" dir="ltr">{{ number_format($dept->total_salary,2) }} ر.س</td>
            <td class="px-4 py-3 text-center text-slate-600" dir="ltr">{{ $dept->employees_count>0 ? number_format($dept->total_salary/$dept->employees_count,2) : '0.00' }} ر.س</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">لا توجد أقسام.</td></tr>
        @endforelse
        <tr class="bg-slate-800 text-white font-bold print-bg">
            <td colspan="2" class="px-4 py-3">الإجمالي</td>
            <td class="px-4 py-3 text-center">{{ $departments->sum('employees_count') }}</td>
            <td class="px-4 py-3 text-center" dir="ltr">{{ number_format($departments->sum('total_salary'),2) }} ر.س</td>
            <td></td>
        </tr>
    </tbody>
</table>
@endsection


