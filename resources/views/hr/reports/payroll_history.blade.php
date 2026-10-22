@extends('reports.layouts.print')
@section('title','مسير الرواتب - جميع الشهور')
@section('subtitle')<p class="text-sm text-secondary mt-1">إجمالي سجلات الرواتب: <strong>{{ collect($grouped)->flatten()->count() }}</strong></p>@endsection
@section('filters')
<form method="GET" class="flex items-center gap-2">
    <select name="employee_id" class="border border-slate-200 rounded-lg text-sm p-2 bg-slate-50">
        <option value="">جميع الموظفين</option>
        @foreach($allEmployees as $emp)<option value="{{ $emp->id }}" {{ $employeeId==$emp->id?'selected':'' }}>{{ $emp->full_name_ar }}</option>@endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-bold">تطبيق</button>
</form>
@endsection
@section('content')
@forelse($grouped as $period => $payrolls)
@php [$yr,$mo] = explode('-',$period); @endphp
<div class="mb-8">
    <h3 class="font-bold text-primary text-base mb-2 flex items-center gap-2">
        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full print-bg">{{ $mo }}/{{ $yr }}</span>
        <span class="text-sm text-secondary">— {{ $payrolls->count() }} موظف — إجمالي: <strong dir="ltr">{{ number_format($payrolls->sum('net_salary'),2) }} ر.س</strong></span>
    </h3>
    <table class="w-full text-sm text-right">
        <thead class="bg-slate-700 text-white text-xs print-bg">
            <tr>
                <th class="px-3 py-2">الموظف</th>
                <th class="px-3 py-2">القسم</th>
                <th class="px-3 py-2 text-center">الاستحقاق</th>
                <th class="px-3 py-2 text-center">خصم التأخير</th>
                <th class="px-3 py-2 text-center">خصم الغياب</th>
                <th class="px-3 py-2 text-center">الصافي</th>
                <th class="px-3 py-2 text-center">تاريخ الصرف</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $i => $p)
            <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
                <td class="px-3 py-2 font-bold text-primary">{{ $p->employee->full_name_ar ?? '-' }}</td>
                <td class="px-3 py-2 text-secondary text-xs">{{ $p->employee->department->name_ar ?? '-' }}</td>
                <td class="px-3 py-2 text-center" dir="ltr">{{ number_format($p->gross_salary,2) }}</td>
                <td class="px-3 py-2 text-center text-orange-500" dir="ltr">{{ $p->other_deductions > 0 ? '-'.number_format($p->other_deductions,2) : '-' }}</td>
                <td class="px-3 py-2 text-center text-red-500" dir="ltr">{{ $p->absent_deduction > 0 ? '-'.number_format($p->absent_deduction,2) : '-' }}</td>
                <td class="px-3 py-2 text-center font-bold text-emerald-700" dir="ltr">{{ number_format($p->net_salary,2) }}</td>
                <td class="px-3 py-2 text-center text-slate-400 text-xs">{{ $p->paid_at ? \Carbon\Carbon::parse($p->paid_at)->format('Y-m-d') : '-' }}</td>
            </tr>
            @endforeach
            <tr class="bg-purple-50 font-bold print-bg">
                <td colspan="5" class="px-3 py-2 text-right text-purple-800">إجمالي الشهر</td>
                <td class="px-3 py-2 text-center text-purple-800" dir="ltr">{{ number_format($payrolls->sum('net_salary'),2) }} ر.س</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
@empty
<div class="text-center py-16 text-slate-400">لا توجد سجلات رواتب.</div>
@endforelse
@endsection


