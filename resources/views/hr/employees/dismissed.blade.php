<x-layouts.admin>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-primary">الموظفين المفصولين</h2>
            <p class="text-secondary mt-1">سجل الموظفين غير النشطين والمفصولين مع مرفق الاستقالة</p>
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
                        <th scope="col" class="px-6 py-4 font-semibold">رقم الموبايل 1</th>
                        <th scope="col" class="px-6 py-4 font-semibold">مرفق الاستقالة</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($employees as $employee)
                    <tr class="transition hover:bg-gray-800/30">
                        <td class="px-6 py-4 flex items-center gap-3">
                            <img class="w-10 h-10 rounded-full object-cover shrink-0 border border-slate-100 shadow-sm opacity-60 grayscale" src="{{ $employee->avatar_url ? asset($employee->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode($employee->full_name).'&background=115e59&color=fff' }}" alt="{{ $employee->full_name }}">
                            <div class="text-left" style="direction: ltr;">
                                <div class="text-base font-bold text-primary text-right" style="direction: rtl;">{{ $employee->full_name_ar }}</div>
                                <div class="font-normal text-secondary text-xs">{{ $employee->email }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-primary">{{ $employee->job_title_ar ?? $employee->department->name_ar ?? 'غير محدد' }}</div>
                            <div class="text-xs text-primary mt-0.5">{{ $employee->employee_code }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-primary" dir="ltr">
                            {{ $employee->phone }}
                        </td>
                        <td class="px-6 py-4">
                            @if($employee->resignation_attachment)
                                <a href="{{ $employee->resignation_attachment }}" target="_blank" class="flex items-center gap-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 px-3 py-1.5 rounded-lg font-bold text-xs transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                    عرض المرفق
                                </a>
                            @else
                                <span class="text-slate-400 text-xs italic">لا يوجد مرفق</span>
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
                        <td colspan="5" class="px-6 py-12 text-center text-secondary">
                            لا يوجد موظفين مفصولين.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t" style="border-color: var(--border-color);">{{ $employees->links() }}</div>
    </div>
</x-layouts.admin>


