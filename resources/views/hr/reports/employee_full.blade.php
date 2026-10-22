@extends('reports.layouts.print')
@section('title','تقرير موظف شامل')
@section('subtitle')
<p class="text-sm text-secondary mt-1">{{ $employee->full_name_ar }} — {{ $employee->employee_code }}</p>
@endsection
@section('content')

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 1. Employee Profile Header --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="bg-slate-50 rounded-xl p-6 mb-8 flex items-center gap-6 print-bg border border-slate-200">
    <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-white shadow flex-shrink-0">
        <img src="{{ $employee->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($employee->full_name_ar).'&background=1e293b&color=fff&size=200' }}" class="w-full h-full object-cover">
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 flex-1 text-sm">
        <div><span class="block text-slate-400 text-xs mb-0.5">الاسم الكامل</span><strong class="text-primary">{{ $employee->full_name_ar }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">الرقم الوظيفي</span><strong>{{ $employee->employee_code }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">القسم</span><strong>{{ $employee->department->name_ar ?? '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">المسمى الوظيفي</span><strong>{{ $employee->job_title_ar ?? $employee->job_title ?? '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">الراتب الأساسي</span><strong dir="ltr">{{ number_format($employee->base_salary,2) }} ر.س</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">بدل السكن</span><strong dir="ltr">{{ number_format($employee->housing_allowance ?? 0,2) }} ر.س</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">تاريخ التعيين</span><strong>{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">الجنسية</span><strong>{{ $employee->nationality ?? '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">رقم الهوية</span><strong>{{ $employee->national_id ?? '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">الهاتف</span><strong>{{ $employee->phone ?? '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">البريد الإلكتروني</span><strong>{{ $employee->email ?? '-' }}</strong></div>
        <div><span class="block text-slate-400 text-xs mb-0.5">الحالة</span>
            @if($employee->is_active)<span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-0.5 rounded-full print-bg">نشط</span>
            @else<span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full print-bg">غير نشط</span>@endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 2. Attendance Summary --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<h3 class="font-bold text-primary mb-3 text-base border-b border-slate-200 pb-2">📊 ملخص الحضور {{ $month !== 'all' ? '(شهر '.$month.'/'.$year.')' : '(سنة '.$year.')' }}</h3>
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="border border-slate-200 rounded-xl p-4 text-center print-bg">
        <div class="text-2xl font-extrabold text-primary">{{ $attendanceSummary['total'] }}</div>
        <div class="text-xs text-secondary font-bold mt-1">إجمالي الأيام</div>
    </div>
    <div class="border border-slate-200 rounded-xl p-4 text-center print-bg">
        <div class="text-2xl font-extrabold text-emerald-700">{{ $attendanceSummary['present'] }}</div>
        <div class="text-xs text-secondary font-bold mt-1">حضور</div>
    </div>
    <div class="border border-slate-200 rounded-xl p-4 text-center print-bg">
        <div class="text-2xl font-extrabold text-amber-600">{{ $attendanceSummary['late'] }}</div>
        <div class="text-xs text-secondary font-bold mt-1">تأخير</div>
    </div>
    <div class="border border-slate-200 rounded-xl p-4 text-center print-bg">
        <div class="text-2xl font-extrabold text-red-600">{{ $attendanceSummary['absent'] }}</div>
        <div class="text-xs text-secondary font-bold mt-1">غياب</div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 3. Attendance Details --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<h3 class="font-bold text-primary mb-3 text-base border-b border-slate-200 pb-2">📅 تفاصيل الحضور ({{ $attendances->count() }} يوم)</h3>
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white text-xs print-bg">
        <tr>
            <th class="px-3 py-2">التاريخ</th>
            <th class="px-3 py-2 text-center">اليوم</th>
            <th class="px-3 py-2 text-center">الحالة</th>
            <th class="px-3 py-2 text-center">وقت الحضور</th>
            <th class="px-3 py-2 text-center">وقت الانصراف</th>
        </tr>
    </thead>
    <tbody>
        @forelse($attendances as $i => $att)
        @php
            $label = match($att->status) { 'present' => 'حاضر', 'late' => 'متأخر', 'absent' => 'غائب', default => $att->status };
        @endphp
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-3 py-2 font-bold">{{ \Carbon\Carbon::parse($att->work_date)->format('Y-m-d') }}</td>
            <td class="px-3 py-2 text-center">{{ \Carbon\Carbon::parse($att->work_date)->locale('ar')->dayName }}</td>
            <td class="px-3 py-2 text-center font-bold">{{ $label }}</td>
            <td class="px-3 py-2 text-center" dir="ltr">{{ $att->check_in ?? '-' }}</td>
            <td class="px-3 py-2 text-center" dir="ltr">{{ $att->check_out ?? '-' }}</td>
        </tr>
        @empty<tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">لا يوجد سجل حضور.</td></tr>@endforelse
    </tbody>
</table>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 4. Salary History --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<h3 class="font-bold text-primary mb-3 text-base border-b border-slate-200 pb-2">💰 سجل الرواتب ({{ $payrolls->count() }} شهر)</h3>
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white text-xs print-bg">
        <tr>
            <th class="px-3 py-2">الشهر/السنة</th>
            <th class="px-3 py-2 text-center">الاستحقاق</th>
            <th class="px-3 py-2 text-center">الخصومات</th>
            <th class="px-3 py-2 text-center">الصافي المستلم</th>
            <th class="px-3 py-2 text-center">الحالة</th>
            <th class="px-3 py-2 text-center">تاريخ الصرف</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payrolls as $i => $p)
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-3 py-2 font-bold">{{ $p->pay_period_month }}/{{ $p->pay_period_year }}</td>
            <td class="px-3 py-2 text-center" dir="ltr">{{ number_format($p->gross_salary,2) }}</td>
            <td class="px-3 py-2 text-center" dir="ltr">-{{ number_format($p->absent_deduction+$p->other_deductions+($p->social_insurance ?? 0),2) }}</td>
            <td class="px-3 py-2 text-center font-bold" dir="ltr">{{ number_format($p->net_salary,2) }}</td>
            <td class="px-3 py-2 text-center">
                @if($p->status==='paid') <span class="font-bold">مدفوع</span>
                @else <span class="font-bold">غير مدفوع</span> @endif
            </td>
            <td class="px-3 py-2 text-center">{{ $p->paid_at ? \Carbon\Carbon::parse($p->paid_at)->format('Y-m-d') : '-' }}</td>
        </tr>
        @empty<tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">لا توجد رواتب مسجلة.</td></tr>@endforelse
    </tbody>
    @if($payrolls->count() > 0)
    <tfoot class="bg-slate-100 print-bg font-bold">
        <tr>
            <td class="px-3 py-2">الإجمالي</td>
            <td class="px-3 py-2 text-center" dir="ltr">{{ number_format($payrolls->sum('gross_salary'),2) }}</td>
            <td class="px-3 py-2 text-center" dir="ltr">-{{ number_format($payrolls->sum('absent_deduction')+$payrolls->sum('other_deductions')+$payrolls->sum('social_insurance'),2) }}</td>
            <td class="px-3 py-2 text-center" dir="ltr">{{ number_format($payrolls->sum('net_salary'),2) }}</td>
            <td class="px-3 py-2" colspan="2"></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 5. Withdrawals --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<h3 class="font-bold text-primary mb-3 text-base border-b border-slate-200 pb-2">💵 السحوبات ({{ $withdrawals->count() }})</h3>
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white text-xs print-bg"><tr>
        <th class="px-3 py-2">التاريخ</th><th class="px-3 py-2">السبب</th>
        <th class="px-3 py-2 text-center">المبلغ</th><th class="px-3 py-2 text-center">الحالة</th>
    </tr></thead>
    <tbody>
        @forelse($withdrawals as $i => $w)
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-3 py-2">{{ $w->created_at->format('Y-m-d') }}</td>
            <td class="px-3 py-2 text-xs">{{ Str::limit($w->reason,60) }}</td>
            <td class="px-3 py-2 text-center font-bold" dir="ltr">{{ number_format($w->amount,2) }} ر.س</td>
            <td class="px-3 py-2 text-center">
                @if($w->status==='approved') <span class="font-bold">معتمد</span>
                @elseif($w->status==='rejected') <span class="font-bold">مرفوض</span>
                @else <span class="font-bold">معلق</span> @endif
            </td>
        </tr>
        @empty<tr><td colspan="4" class="px-4 py-4 text-center text-slate-400">لا توجد سحوبات.</td></tr>@endforelse
    </tbody>
    @if($withdrawals->where('status','approved')->count() > 0)
    <tfoot class="bg-slate-100 print-bg font-bold">
        <tr>
            <td class="px-3 py-2" colspan="2">إجمالي السحوبات المعتمدة</td>
            <td class="px-3 py-2 text-center" dir="ltr">{{ number_format($withdrawals->where('status','approved')->sum(fn($w)=>$w->amount),2) }} ر.س</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 6. Loans --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<h3 class="font-bold text-primary mb-3 text-base border-b border-slate-200 pb-2">🏦 القروض والسلف ({{ $loans->count() }})</h3>
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white text-xs print-bg"><tr>
        <th class="px-3 py-2">التاريخ</th><th class="px-3 py-2">التفاصيل</th>
        <th class="px-3 py-2 text-center">المبلغ</th><th class="px-3 py-2 text-center">المدفوع</th>
        <th class="px-3 py-2 text-center">المتبقي</th><th class="px-3 py-2 text-center">الحالة</th>
    </tr></thead>
    <tbody>
        @forelse($loans as $i => $l)
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-3 py-2">{{ $l->created_at->format('Y-m-d') }}</td>
            <td class="px-3 py-2 text-xs">{{ Str::limit($l->reason,60) }}</td>
            <td class="px-3 py-2 text-center font-bold" dir="ltr">{{ number_format($l->amount,2) }} ر.س</td>
            <td class="px-3 py-2 text-center" dir="ltr">{{ number_format($l->paid_amount,2) }}</td>
            <td class="px-3 py-2 text-center font-bold" dir="ltr">{{ number_format($l->remaining_amount,2) }}</td>
            <td class="px-3 py-2 text-center">
                @if($l->status==='approved') <span class="font-bold">معتمد</span>
                @elseif($l->status==='rejected') <span class="font-bold">مرفوض</span>
                @else <span class="font-bold">معلق</span> @endif
            </td>
        </tr>
        @empty<tr><td colspan="6" class="px-4 py-4 text-center text-slate-400">لا توجد قروض أو سلف.</td></tr>@endforelse
    </tbody>
</table>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 7. Entitlements --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<h3 class="font-bold text-primary mb-3 text-base border-b border-slate-200 pb-2">🎁 الاستحقاقات والبدلات ({{ $entitlements->count() }})</h3>
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white text-xs print-bg"><tr>
        <th class="px-3 py-2">التاريخ</th><th class="px-3 py-2">النوع</th>
        <th class="px-3 py-2">العنوان</th><th class="px-3 py-2">السبب</th>
        <th class="px-3 py-2 text-center">المبلغ</th>
    </tr></thead>
    <tbody>
        @forelse($entitlements as $i => $e)
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-3 py-2">{{ $e->date ? $e->date->format('Y-m-d') : '-' }}</td>
            <td class="px-3 py-2 font-bold">{{ $e->type ?? '-' }}</td>
            <td class="px-3 py-2">{{ $e->title ?? '-' }}</td>
            <td class="px-3 py-2 text-xs">{{ Str::limit($e->reason,50) }}</td>
            <td class="px-3 py-2 text-center font-bold" dir="ltr">{{ number_format($e->amount,2) }} ر.س</td>
        </tr>
        @empty<tr><td colspan="5" class="px-4 py-4 text-center text-slate-400">لا توجد استحقاقات مسجلة.</td></tr>@endforelse
    </tbody>
    @if($entitlements->count() > 0)
    <tfoot class="bg-slate-100 print-bg font-bold">
        <tr>
            <td class="px-3 py-2" colspan="4">إجمالي الاستحقاقات</td>
            <td class="px-3 py-2 text-center" dir="ltr">{{ number_format($entitlements->sum('amount'),2) }} ر.س</td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 8. Tasks --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<h3 class="font-bold text-primary mb-3 text-base border-b border-slate-200 pb-2">📋 المهام ({{ $tasks->count() }})</h3>
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white text-xs print-bg"><tr>
        <th class="px-3 py-2">المهمة</th><th class="px-3 py-2 text-center">الحالة</th>
        <th class="px-3 py-2 text-center">الأولوية</th><th class="px-3 py-2 text-center">تاريخ التسليم</th>
        <th class="px-3 py-2 text-center">تاريخ الإنشاء</th>
    </tr></thead>
    <tbody>
        @forelse($tasks as $i => $t)
        @php
            $statusLabel = match($t->status) { 'pending' => 'معلق', 'in_progress' => 'قيد التنفيذ', 'completed' => 'مكتمل', 'cancelled' => 'ملغي', default => $t->status };
            $priorityLabel = match($t->priority ?? 'medium') { 'low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجلة', default => $t->priority ?? '-' };
        @endphp
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-3 py-2 font-bold">{{ Str::limit($t->title,50) }}</td>
            <td class="px-3 py-2 text-center font-bold">{{ $statusLabel }}</td>
            <td class="px-3 py-2 text-center">{{ $priorityLabel }}</td>
            <td class="px-3 py-2 text-center">{{ $t->due_date ? \Carbon\Carbon::parse($t->due_date)->format('Y-m-d') : '-' }}</td>
            <td class="px-3 py-2 text-center">{{ $t->created_at->format('Y-m-d') }}</td>
        </tr>
        @empty<tr><td colspan="5" class="px-4 py-4 text-center text-slate-400">لا توجد مهام مسجلة.</td></tr>@endforelse
    </tbody>
</table>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 9. Documents --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<h3 class="font-bold text-primary mb-3 text-base border-b border-slate-200 pb-2">🪪 الوثائق والمستندات</h3>
<table class="w-full text-sm text-right mb-8">
    <thead class="bg-slate-800 text-white text-xs print-bg"><tr>
        <th class="px-3 py-2">الوثيقة</th>
        <th class="px-3 py-2 text-center">تاريخ الانتهاء</th>
        <th class="px-3 py-2 text-center">الأيام المتبقية</th>
        <th class="px-3 py-2 text-center">الحالة</th>
    </tr></thead>
    <tbody>
        @forelse($docs as $i => $d)
        @php
            $statusLabel = match($d['status']) { 'expired' => 'منتهية', 'warning' => 'قاربت على الانتهاء', 'ok' => 'سارية', default => '-' };
        @endphp
        <tr class="{{ $i%2==0?'bg-white':'bg-slate-50 print-bg' }}">
            <td class="px-3 py-2 font-bold">{{ $d['label'] }}</td>
            <td class="px-3 py-2 text-center">{{ $d['date'] }}</td>
            <td class="px-3 py-2 text-center font-bold">{{ $d['days'] }} يوم</td>
            <td class="px-3 py-2 text-center font-bold">{{ $statusLabel }}</td>
        </tr>
        @empty<tr><td colspan="4" class="px-4 py-4 text-center text-slate-400">لا توجد وثائق مسجلة.</td></tr>@endforelse
    </tbody>
</table>

@endsection


