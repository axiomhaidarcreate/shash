@extends('reports.layouts.print')
@section('title','تقرير سحوبات موظف')
@section('subtitle')<p class="text-sm text-secondary mt-1">{{ $employee->full_name_ar }} — {{ $employee->employee_code }}</p>@endsection
@section('content')
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-orange-500 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">إجمالي السحوبات</p>
        <p class="text-xl font-extrabold" dir="ltr">{{ number_format($withdrawals->sum('amount'),2) }} ر.س</p>
    </div>
    <div class="bg-emerald-600 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">سحوبات معتمدة</p>
        <p class="text-xl font-extrabold">{{ $withdrawals->where('status','approved')->count() }}</p>
    </div>
    <div class="bg-red-500 text-white rounded-xl p-4 text-center print-bg">
        <p class="text-xs text-white/80 mb-1">سحوبات مرفوضة</p>
        <p class="text-xl font-extrabold">{{ $withdrawals->where('status','rejected')->count() }}</p>
    </div>
</div>
<table class="w-full text-sm text-right">
    <thead class="bg-orange-600 text-white print-bg">
        <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">تاريخ الطلب</th>
            <th class="px-4 py-3">السبب</th>
            <th class="px-4 py-3 text-center">المبلغ</th>
            <th class="px-4 py-3 text-center">الحالة</th>
            <th class="px-4 py-3 text-center">تاريخ الاعتماد</th>
        </tr>
    </thead>
    <tbody>
        @forelse($withdrawals as $i => $w)
        <tr class="{{ $i%2==0?'bg-white':'bg-orange-50 print-bg' }}">
            <td class="px-4 py-3 text-slate-400">{{ $i+1 }}</td>
            <td class="px-4 py-3 font-bold">{{ $w->created_at->format('Y-m-d') }}</td>
            <td class="px-4 py-3 text-slate-600 text-xs">{{ Str::limit($w->reason, 80) }}</td>
            <td class="px-4 py-3 text-center font-bold text-orange-700" dir="ltr">{{ number_format($w->amount,2) }} ر.س</td>
            <td class="px-4 py-3 text-center">
                @if($w->status==='approved')<span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full print-bg">معتمد</span>
                @elseif($w->status==='rejected')<span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full print-bg">مرفوض</span>
                @else<span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full print-bg">معلق</span>@endif
            </td>
            <td class="px-4 py-3 text-center text-slate-400 text-xs">{{ $w->status==='approved' ? $w->updated_at->format('Y-m-d') : '-' }}</td>
        </tr>
        @empty<tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">لا توجد سحوبات مسجلة.</td></tr>@endforelse
        @if($withdrawals->where('status','approved')->count())
        <tr class="bg-orange-600 text-white font-bold print-bg">
            <td colspan="3" class="px-4 py-3 text-right">إجمالي المعتمد والمخصوم</td>
            <td class="px-4 py-3 text-center" dir="ltr">{{ number_format($withdrawals->where('status','approved')->sum('amount'),2) }} ر.س</td>
            <td colspan="2"></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection


