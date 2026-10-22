@extends('reports.layouts.print')
@section('title','تقرير الحضور الشهري')
@section('subtitle')<p class="text-sm text-secondary mt-1">{{ $employee->full_name_ar }} — {{ $month }}/{{ $year }}</p>@endsection
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
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold">تطبيق</button>
</form>
@endsection
@section('content')
{{-- Summary --}}
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-emerald-600 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">أيام الحضور</p>
        <p class="text-3xl font-extrabold">{{ $summary['present'] }}</p>
    </div>
    <div class="bg-amber-500 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">أيام التأخير</p>
        <p class="text-3xl font-extrabold">{{ $summary['late'] }}</p>
    </div>
    <div class="bg-red-600 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">أيام الغياب</p>
        <p class="text-3xl font-extrabold">{{ $summary['absent'] }}</p>
    </div>
    <div class="bg-slate-700 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">إجمالي الخصومات</p>
        @php 
            $dailySalary = $employee->base_salary ? ($employee->base_salary / 30) : 0;
            $absenceDeduction = $summary['absent'] * $dailySalary;
            $lateDeduction = $attendances->where('is_deducted', true)->sum('deduction_amount');
            $totalDeduction = $absenceDeduction + $lateDeduction;
        @endphp
        <p class="text-2xl font-extrabold">{{ number_format($totalDeduction, 2) }} <span class="text-xs font-normal">ر.س</span></p>
    </div>
</div>

@php $total = $summary['present'] + $summary['late'] + $summary['absent']; @endphp
@if($total > 0)
<div class="bg-slate-50 rounded-xl p-4 mb-8 border border-slate-200">
    <div class="flex h-4 rounded-full overflow-hidden">
        @if($summary['present']) <div class="bg-emerald-500 print-bg" style="width:{{ round($summary['present']/$total*100) }}%"></div> @endif
        @if($summary['late'])    <div class="bg-amber-400 print-bg"   style="width:{{ round($summary['late']/$total*100) }}%"></div>    @endif
        @if($summary['absent'])  <div class="bg-red-500 print-bg"     style="width:{{ round($summary['absent']/$total*100) }}%"></div>  @endif
    </div>
    <div class="flex justify-between mt-2 text-xs text-secondary">
        <span>حضور {{ round($summary['present']/$total*100) }}%</span>
        <span>تأخير {{ round($summary['late']/$total*100) }}%</span>
        <span>غياب {{ round($summary['absent']/$total*100) }}%</span>
    </div>
</div>
@endif

<table class="w-full text-sm text-right">
    <thead class="bg-blue-700 text-white print-bg">
        <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">التاريخ</th>
            <th class="px-4 py-3">اليوم</th>
            <th class="px-4 py-3 text-center">الحالة</th>
            <th class="px-4 py-3 text-center">وقت الدخول</th>
            <th class="px-4 py-3 text-center">وقت الخروج</th>
            <th class="px-4 py-3 text-center">التأخير (د)</th>
            <th class="px-4 py-3 text-center">الخصم</th>
        </tr>
    </thead>
    <tbody>
        @forelse($attendances as $i => $a)
        @php
            $rowColor = match($a->status) {
                'present' => $i%2==0 ? 'bg-white' : 'bg-emerald-50 print-bg',
                'late'    => 'bg-amber-50 print-bg',
                'absent'  => 'bg-red-50 print-bg',
                default   => 'bg-white'
            };
            $statusLabel = match($a->status){ 'present'=>'حاضر','late'=>'متأخر','absent'=>'غائب',default=>$a->status };
            $statusColor = match($a->status){
                'present'=>'bg-emerald-100 text-emerald-700',
                'late'=>'bg-amber-100 text-amber-700',
                'absent'=>'bg-red-100 text-red-700',
                default=>'bg-slate-100 text-secondary'
            };
            $deduction = 0;
            if ($a->status == 'absent') {
                $deduction = $dailySalary;
            } elseif ($a->is_deducted) {
                $deduction = $a->deduction_amount;
            }
        @endphp
        <tr class="{{ $rowColor }}">
            <td class="px-4 py-2.5 text-slate-400">{{ $i+1 }}</td>
            <td class="px-4 py-2.5 font-bold text-primary">{{ \Carbon\Carbon::parse($a->work_date)->format('Y-m-d') }}</td>
            <td class="px-4 py-2.5 text-slate-600">{{ \Carbon\Carbon::parse($a->work_date)->locale('ar')->dayName }}</td>
            <td class="px-4 py-2.5 text-center">
                <span class="{{ $statusColor }} text-[10px] font-bold px-2 py-0.5 rounded-full print-bg">{{ $statusLabel }}</span>
            </td>
            <td class="px-4 py-2.5 text-center text-slate-600">{{ $a->check_in ?? '-' }}</td>
            <td class="px-4 py-2.5 text-center text-slate-600">{{ $a->check_out ?? '-' }}</td>
            <td class="px-4 py-2.5 text-center {{ $a->delay_minutes ? 'text-amber-600 font-bold' : 'text-slate-300' }}">{{ $a->delay_minutes ?? '-' }}</td>
            <td class="px-4 py-2.5 text-center font-bold text-red-500">{{ $deduction > 0 ? number_format($deduction, 2) . ' ر.س' : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">لا توجد سجلات دوام لهذا الشهر.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection


