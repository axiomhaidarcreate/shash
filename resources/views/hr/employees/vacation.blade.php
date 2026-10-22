<x-layouts.admin>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-primary">الموظفين في إجازة</h2>
            <p class="text-secondary mt-1">تتبع الموظفين المتواجدين في إجازة حالياً</p>
        </div>
        <a href="{{ route('hr.employees.index') }}" class="btn btn-outline">رجوع للموظفين</a>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-secondary whitespace-nowrap">
                    <thead class="text-xs text-secondary" style="background: rgba(0,0,0,0.2);">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">الموظف</th>
                            <th scope="col" class="px-6 py-4 font-semibold">المسمى الوظيفي</th>
                            <th scope="col" class="px-6 py-4 font-semibold">تاريخ بداية الإجازة</th>
                            <th scope="col" class="px-6 py-4 font-semibold">تاريخ نهاية الإجازة</th>
                            <th scope="col" class="px-6 py-4 font-semibold">المتبقي</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-center">إجراءات</th>
                        </tr>
                    </thead>
                <tbody class="divide-y divide-gray-800">
                    @php $today = now()->toDateString(); @endphp
                    @forelse($employees as $employee)
                        @php
                            $today = now()->toDateString();
                            $activeLeave = $employee->leaveRequests->first(); // already filtered by controller
                            // Use string comparison after converting Carbon to date string
                            $leaveStart = null;
                            $leaveEnd   = null;
                            $daysLeft   = null;
                            if ($activeLeave) {
                                $d1 = $activeLeave->start_date instanceof \Carbon\Carbon
                                    ? $activeLeave->start_date->toDateString()
                                    : (string) $activeLeave->start_date;
                                $d2 = $activeLeave->end_date instanceof \Carbon\Carbon
                                    ? $activeLeave->end_date->toDateString()
                                    : (string) $activeLeave->end_date;
                                $leaveStart = min($d1, $d2);
                                $leaveEnd   = max($d1, $d2);
                                $daysLeft   = \Carbon\Carbon::parse($today)->diffInDays(\Carbon\Carbon::parse($leaveEnd), false);
                            }
                        @endphp
                    <tr class="transition hover:bg-gray-800/30">
                        <td class="px-6 py-4 flex items-center gap-3">
                            <img class="w-10 h-10 rounded-full object-cover shrink-0 border border-blue-200 shadow-sm" src="{{ $employee->avatar_url ? asset($employee->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode($employee->full_name).'&background=115e59&color=fff' }}" alt="{{ $employee->full_name }}">
                            <div class="text-left" style="direction: ltr;">
                                <div class="text-base font-bold text-primary text-right" style="direction: rtl;">{{ $employee->full_name_ar }}</div>
                                <div class="font-normal text-secondary text-xs">{{ $employee->email }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-primary">{{ $employee->job_title_ar ?? $employee->department->name_ar ?? 'غير محدد' }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-blue-600">
                            {{ $leaveStart ? \Carbon\Carbon::parse($leaveStart)->format('Y-m-d') : '-' }}
                        </td>
                        <td class="px-6 py-4 font-bold text-blue-600">
                            {{ $leaveEnd ? \Carbon\Carbon::parse($leaveEnd)->format('Y-m-d') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($daysLeft !== null)
                                <span class="{{ $daysLeft <= 1 ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }} text-xs font-bold px-3 py-1 rounded-full">
                                    {{ $daysLeft == 0 ? 'آخر يوم' : $daysLeft . ' يوم' }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('hr.employees.edit', $employee) }}" class="text-secondary hover:text-primary transition font-bold text-xs bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg border border-slate-200">
                                فتح ملف الموظف
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-secondary">
                            لا يوجد موظفين في إجازة حالياً.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t" style="border-color: var(--border-color);">{{ $employees->links() }}</div>
    </div>
</x-layouts.admin>


