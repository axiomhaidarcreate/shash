@extends('reports.layouts.print')
@section('title','قائمة بيانات الموظفين')
@section('subtitle')<p class="text-sm text-secondary mt-1">إجمالي الموظفين: <strong>{{ $employees->count() }}</strong></p>@endsection
@section('filters')
<form method="GET" class="flex items-center gap-2">
    <select name="department_id" class="border border-slate-200 rounded-lg text-sm p-2 bg-slate-50">
        <option value="">جميع الأقسام</option>
        @foreach($departments as $d)<option value="{{ $d->id }}" {{ $dept==$d->id?'selected':'' }}>{{ $d->name_ar }}</option>@endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold">تطبيق</button>
</form>
@endsection
@section('content')
<table class="w-full text-sm text-right">
    <thead class="bg-slate-800 text-white print-bg">
        <tr>
            <th class="px-3 py-3">#</th>
            <th class="px-3 py-3">الرقم الوظيفي</th>
            <th class="px-3 py-3">الاسم الكامل</th>
            <th class="px-3 py-3">القسم</th>
            <th class="px-3 py-3">المسمى الوظيفي</th>
            <th class="px-3 py-3 text-center">الراتب الأساسي</th>
            <th class="px-3 py-3 text-center">الجنسية</th>
            <th class="px-3 py-3 text-center">الحالة</th>
            <th class="px-3 py-3">تاريخ التعيين</th>
        </tr>
    </thead>
    <tbody>
        @forelse($employees as $i => $emp)
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-3 py-2.5 text-slate-400">{{ $i+1 }}</td>
            <td class="px-3 py-2.5 font-mono text-slate-600">{{ $emp->employee_code }}</td>
            <td class="px-3 py-2.5 font-bold text-primary">{{ $emp->full_name_ar }}</td>
            <td class="px-3 py-2.5">{{ $emp->department->name_ar ?? '-' }}</td>
            <td class="px-3 py-2.5 text-slate-600">{{ $emp->job_title_ar ?? $emp->job_title ?? '-' }}</td>
            <td class="px-3 py-2.5 text-center font-bold" dir="ltr">{{ number_format($emp->base_salary,2) }}</td>
            <td class="px-3 py-2.5 text-center">{{ $emp->nationality ?? '-' }}</td>
            <td class="px-3 py-2.5 text-center">
                @if($emp->is_active)<span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full print-bg">نشط</span>
                @else<span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full print-bg">غير نشط</span>@endif
            </td>
            <td class="px-3 py-2.5">{{ $emp->hire_date ? \Carbon\Carbon::parse($emp->hire_date)->format('Y-m-d') : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">لا يوجد موظفين.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection


