<x-layouts.admin>

        <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="text-2xl font-bold text-primary">التقارير والإحصائيات</h2>
            <p class="text-secondary mt-1">اختر التقرير المطلوب لعرضه أو طباعته.</p>
        </div>
    </div>

    {{-- Employee Selector for per-employee reports --}}
    <div class="card mb-8">
        <h3 class="text-lg font-bold text-primary mb-4">التقارير الفردية</h3>
        <div class="flex flex-col gap-4 md:flex-row md:items-end w-full">
            <!-- Select Employee -->
            <div class="flex-1">
                <label class="form-label">اختر موظفاً</label>
                <select id="empSelect" class="form-control">
                    <option value="">— اختر موظفاً أولاً —</option>
                    @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name_ar }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Select Report Type -->
            <div class="flex-1">
                <label class="form-label">اختر نوع التقرير</label>
                <select id="reportTypeSelect" class="form-control">
                    <option value="full">📄 بيانات شاملة</option>
                    <option value="salary">💰 رواتبه</option>
                    <option value="withdrawals">💵 سحوباته</option>
                    <option value="absences">🚫 غياباته</option>
                    <option value="late">⏰ تأخيراته</option>
                    <option value="attendance">📅 حضور شهري</option>
                    <option value="documents">🪪 الوثائق</option>
                </select>
            </div>

            <!-- Select Month -->
            <div class="flex-1">
                <label class="form-label">الشهر</label>
                <select id="filterMonth" class="form-control">
                    <option value="all">كل الشهور</option>
                    @foreach(['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'] as $i => $m)
                        <option value="{{ $i+1 }}" {{ ($i+1)==now()->month ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Select Year -->
            <div class="flex-1">
                <label class="form-label">السنة</label>
                <select id="filterYear" class="form-control">
                    @for($y = now()->year + 1; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ $y==now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- View Button -->
            <div>
                <button onclick="openReport()" class="btn w-full md:w-auto flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    عرض التقرير
                </button>
            </div>
        </div>
    </div>

    <script>
    function openReport() {
        const empId  = document.getElementById('empSelect').value;
        const type   = document.getElementById('reportTypeSelect').value;
        const month  = document.getElementById('filterMonth').value;
        const year   = document.getElementById('filterYear').value;

        if (!empId) {
            alert('يرجى اختيار موظف أولاً من القائمة.');
            return;
        }

        const base = '/reports/employee/' + empId;
        let url = '';
        
        if (type === 'attendance') {
            url = base + '/attendance?month=' + month + '&year=' + year;
        } else if (type === 'absences' || type === 'late') {
            if (month === 'all') {
                const from = `${year}-01-01`;
                const to = `${year}-12-31`;
                url = base + '/' + type + '?from=' + from + '&to=' + to;
            } else {
                const from = `${year}-${month.padStart(2, '0')}-01`;
                const lastDay = new Date(year, month, 0).getDate();
                const to = `${year}-${month.padStart(2, '0')}-${lastDay.toString().padStart(2, '0')}`;
                url = base + '/' + type + '?from=' + from + '&to=' + to;
            }
        } else {
            url = base + '/' + type + '?month=' + month + '&year=' + year;
        }

        window.open(url, '_blank');
    }
    </script>


    {{-- General Reports Grid --}}
    <h3 class="text-base font-bold text-primary mb-4">التقارير العامة</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-10">

        {{-- Payroll Summary --}}
        <div class="card hover:border-indigo-200 hover:shadow-md transition group">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
            </div>
            <h4 class="font-bold text-primary mb-1">تقارير الرواتب الشهرية</h4>
            <p class="text-xs text-secondary mb-4">المدفوع والمتبقي للشهر المحدد مع تفاصيل كل موظف.</p>
            <a href="{{ route('hr.reports.payroll_summary') }}" target="_blank" class="inline-flex items-center gap-2 text-indigo-600 font-bold text-sm hover:text-indigo-800 transition">
                عرض التقرير
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
            </a>
        </div>

        {{-- Employees List --}}
        <div class="card hover:border-emerald-200 hover:shadow-md transition group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
            </div>
            <h4 class="font-bold text-primary mb-1">قائمة بيانات الموظفين</h4>
            <p class="text-xs text-secondary mb-4">جميع الموظفين مع بياناتهم الأساسية ورواتبهم وأقسامهم.</p>
            <a href="{{ route('hr.reports.employees_list') }}" target="_blank" class="inline-flex items-center gap-2 text-emerald-600 font-bold text-sm hover:text-emerald-800 transition">
                عرض التقرير
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
            </a>
        </div>

        {{-- Departments --}}
        <div class="card hover:border-teal-200 hover:shadow-md transition group">
            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
            </div>
            <h4 class="font-bold text-primary mb-1">تقرير الأقسام</h4>
            <p class="text-xs text-secondary mb-4">عدد الموظفين وإجمالي الرواتب لكل قسم.</p>
            <a href="{{ route('hr.reports.departments') }}" target="_blank" class="inline-flex items-center gap-2 text-teal-600 font-bold text-sm hover:text-teal-800 transition">
                عرض التقرير
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
            </a>
        </div>

        {{-- Payroll History --}}
        <div class="card hover:border-purple-200 hover:shadow-md transition group">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" /></svg>
            </div>
            <h4 class="font-bold text-primary mb-1">مسير رواتب لجميع الشهور</h4>
            <p class="text-xs text-secondary mb-4">سجل كامل للرواتب المصروفة مجمعة حسب كل شهر وسنة.</p>
            <a href="{{ route('hr.reports.payroll_history') }}" target="_blank" class="inline-flex items-center gap-2 text-purple-600 font-bold text-sm hover:text-purple-800 transition">
                عرض التقرير
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
            </a>
        </div>

    </div>

    

</x-layouts.admin>


