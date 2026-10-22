<x-layouts.admin>
    <!-- Top Breadcrumb / Title -->
        <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1><i class="fa-solid fa-clock" style="color: var(--primary-color);"></i> متابعة الحضور</h1>
            <p>سجل الحضور والانصراف للموظفين · <span style="font-weight: bold;">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span></p>
        </div>
        
        <!-- Filter Toolbar -->
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: flex-end;">
            <form action="{{ route('hr.attendance.index') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: flex-end; flex-wrap: wrap;" id="filterForm">
                <div x-data="{
                        open: false,
                        search: '',
                        selectedId: '{{ request('employee_id', 'all') }}',
                        employees: [
                            { id: 'all', name: 'الكل' },
                            @foreach($employees as $emp)
                            { id: '{{ $emp->id }}', name: '{{ addslashes($emp->full_name_ar) }}' },
                            @endforeach
                        ],
                        get filteredEmployees() {
                            if (this.search === '') return this.employees;
                            return this.employees.filter(e => e.name.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        get selectedName() {
                            let emp = this.employees.find(e => e.id == this.selectedId);
                            return emp ? emp.name : 'الكل';
                        }
                    }" style="position: relative; width: 200px;">
                    <label class="form-label" style="font-size: 0.8rem; margin-bottom: 4px; display: block;">اسم الموظف</label>
                    <input type="hidden" name="employee_id" x-model="selectedId">
                    <button type="button" @click="open = !open" @click.outside="open = false" class="form-control" style="text-align: right; display: flex; justify-content: space-between; align-items: center; min-height: 44px; cursor: pointer;">
                        <span x-text="selectedName" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                        <i class="fa-solid fa-chevron-down" style="font-size: 0.8rem; opacity: 0.5;"></i>
                    </button>
                    <div x-show="open" x-transition style="display: none; position: absolute; z-index: 50; width: 100%; margin-top: 4px; background: var(--surface-color); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="padding: 8px; border-bottom: 1px solid var(--border-color);">
                            <input type="text" x-model="search" placeholder="بحث عن موظف..." class="form-control" style="padding: 8px; font-size: 0.85rem; min-height: auto;" @click.stop>
                        </div>
                        <ul style="overflow-y: auto; flex: 1; padding: 4px; list-style: none; margin: 0;">
                            <template x-for="emp in filteredEmployees" :key="emp.id">
                                <li @click="selectedId = emp.id; open = false; search = ''" style="padding: 8px 12px; cursor: pointer; border-radius: var(--radius-sm); font-size: 0.9rem;" :style="selectedId == emp.id ? 'background: var(--primary-glow); color: var(--primary-color); font-weight: bold;' : ''">
                                    <span x-text="emp.name"></span>
                                </li>
                            </template>
                            <li x-show="filteredEmployees.length === 0" style="padding: 12px; text-align: center; color: var(--text-secondary); font-size: 0.85rem;">لا توجد نتائج</li>
                        </ul>
                    </div>
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.8rem; margin-bottom: 4px; display: block;">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control" style="width: 150px; min-height: 44px;">
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.8rem; margin-bottom: 4px; display: block;">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control" style="width: 150px; min-height: 44px;">
                </div>
                <button type="submit" class="btn" style="min-height: 44px;"><i class="fa-solid fa-search"></i> بحث</button>
                <button type="button" onclick="filterCurrentMonth()" class="btn btn-outline" style="min-height: 44px;">الشهر الحالي</button>
            </form>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        
        <!-- Adherence Rate (Primary Solid) -->
        <div class="card" style="background: linear-gradient(135deg, var(--primary-color), #1565C0); color: white; display: flex; flex-direction: column; justify-content: space-between; border: none; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div>
                <h3 style="font-size: 0.85rem; font-weight: 600; opacity: 0.9; margin-bottom: 0.2rem;">نسبة الالتزام</h3>
                <div style="font-size: 2rem; font-weight: 800;">{{ $adherenceRate }}%</div>
            </div>
        </div>

        <!-- Absents (Red border/accent) -->
        <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; border-right: 4px solid var(--danger-color);">
             <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                 <span class="badge badge-inactive">للفترة المحددة</span>
                 <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(244, 67, 54, 0.15); color: var(--danger-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>
             </div>
             <div>
                <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.2rem;">الغياب</h3>
                <div style="font-size: 2rem; font-weight: 800; color: var(--text-primary);">{{ $absentCount }}</div>
             </div>
        </div>

        <!-- Late (Indigo accent) -->
        <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; border-right: 4px solid var(--warning-color);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                 <span class="badge badge-pending">{{ $lateStatsUnit }}</span>
                 <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(255, 152, 0, 0.15); color: var(--warning-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-clock"></i>
                </div>
             </div>
             <div>
                <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.2rem;">{{ $lateStatsTitle }}</h3>
                <div style="font-size: 2rem; font-weight: 800; color: var(--text-primary);">{{ $lateStatsValue }}</div>
             </div>
        </div>

        <!-- Total Present -->
        <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; border-right: 4px solid var(--info-color);">
             <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                 <span class="badge" style="background: rgba(33, 150, 243, 0.15); color: var(--info-color);">للفترة المحددة</span>
                 <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(33, 150, 243, 0.15); color: var(--info-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
             </div>
             <div>
                <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.2rem;">إجمالي الحضور</h3>
                <div style="font-size: 2rem; font-weight: 800; color: var(--text-primary);" dir="ltr">
                    {{ $presentCount }}<span style="font-size: 1rem; color: var(--text-secondary);">/{{ $totalEmployees }}</span>
                </div>
             </div>
        </div>

    </div>

    <!-- Deduction Stats Row -->
    <div class="grid grid-cols-2 gap-4 mb-8">
        <!-- Total Delay Deductions -->
        <div class="card" style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(255, 152, 0, 0.3); background: rgba(255, 152, 0, 0.05);">
            <div>
                 <div style="font-size: 0.8rem; font-weight: 700; color: var(--warning-color); margin-bottom: 0.5rem;">خصومات التأخير للفترة</div>
                 <div style="display: flex; align-items: baseline; gap: 8px;">
                     <div style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ number_format($totalDelayDeduction, 2) }} <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);">ر.س</span></div>
                 </div>
            </div>
            <div style="text-align: left;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255, 152, 0, 0.15); color: var(--warning-color); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.5rem; margin-left: auto;">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--warning-color);">{{ $totalDelayMinutes }} دقيقة تأخير</div>
            </div>
        </div>

        <!-- Absent Deduction Estimate -->
        <div class="card" style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(244, 67, 54, 0.3); background: rgba(244, 67, 54, 0.05);">
            <div>
                 <div style="font-size: 0.8rem; font-weight: 700; color: var(--danger-color); margin-bottom: 0.5rem;">خصومات الغياب للفترة (أيام غياب × نسبة الخصم)</div>
                 <div style="display: flex; align-items: baseline; gap: 8px;">
                     <div style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ $absentCount }} <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);">أيام غياب</span></div>
                 </div>
            </div>
            <div style="text-align: left;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(244, 67, 54, 0.15); color: var(--danger-color); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.5rem; margin-left: auto;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="toast toast-success" style="position: relative; top: 0; left: 0; transform: none; width: 100%; margin-bottom: 1rem; text-align: right; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="toast toast-error" style="position: relative; top: 0; left: 0; transform: none; width: 100%; margin-bottom: 1rem; text-align: right; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
    </div>
    @endif
    @if(session('info'))
    <div class="toast" style="background: rgba(33,150,243,0.9); color: white; position: relative; top: 0; left: 0; transform: none; width: 100%; margin-bottom: 1rem; text-align: right; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-info-circle"></i> {{ session('info') }}
    </div>
    @endif

    <!-- Attendance Table Section -->
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;" id="printArea">
        
        <!-- Table Header Toolbar -->
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 800;">سجل الحضور</h3>
            <a href="{{ route('hr.attendance.print', ['date_from' => request('date_from'), 'date_to' => request('date_to'), 'employee_id' => request('employee_id')]) }}" target="_blank" class="btn-ghost" title="طباعة التقرير">
                <i class="fa-solid fa-print"></i> طباعة التقرير
            </a>
        </div>

        <!-- Table -->
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">الموظف</th>
                        <th scope="col">التاريخ</th>
                        <th scope="col">تسجيل الدخول</th>
                        <th scope="col">تسجيل الخروج</th>
                        <th scope="col" style="text-align: center;">التأخير</th>
                        <th scope="col" style="text-align: center;">الخصم</th>
                        <th scope="col" style="text-align: center;">الحالة</th>
                        <th scope="col" style="text-align: center;" class="print:hidden">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                    <tr>
                        <td style="display: flex; align-items: center; gap: 12px; padding: 12px;">
                            <img style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" src="https://ui-avatars.com/api/?name={{ urlencode($attendance->employee->full_name ?? '') }}&background=d4af37&color=000" alt="Avatar">
                            <div>
                                <div style="font-weight: 800; font-size: 0.95rem;">{{ $attendance->employee->full_name_ar ?? 'موظف محذوف' }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $attendance->employee->department->name_ar ?? 'غير محدد' }}</div>
                            </div>
                        </td>
                        <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}</td>
                        
                        <!-- Check In -->
                        <td dir="ltr">
                            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                <span style="font-weight: 800; color: var(--text-primary);">{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '--:--' }}</span>
                                @if($attendance->delay_minutes > 0)
                                    <span style="font-size: 0.75rem; color: var(--danger-color); font-weight: 700;">+{{ $attendance->delay_minutes }} دقيقة تأخير</span>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Check Out -->
                        <td dir="ltr">
                            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                <span style="font-weight: 800; color: var(--text-primary);">{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '--:--' }}</span>
                                @if($attendance->early_leave_minutes > 0)
                                    <span style="font-size: 0.75rem; color: var(--danger-color); font-weight: 700;">-{{ $attendance->early_leave_minutes }} خروج مبكر</span>
                                @endif
                            </div>
                        </td>

                        <!-- Delay Minutes -->
                        <td style="text-align: center;">
                            @if($attendance->delay_minutes > 0)
                                <span class="badge badge-pending">
                                    <i class="fa-solid fa-clock"></i> {{ $attendance->delay_minutes }} د
                                </span>
                            @else
                                <span style="color: var(--text-secondary); font-size: 0.85rem;">—</span>
                            @endif
                        </td>

                        <!-- Deduction Amount -->
                        <td style="text-align: center;">
                            @if($attendance->deduction_amount > 0)
                                <span class="badge" style="background: rgba(244,67,54,0.1); color: var(--danger-color);">
                                    {{ number_format($attendance->deduction_amount, 2) }} ر.س
                                </span>
                            @else
                                <span style="color: var(--text-secondary); font-size: 0.85rem;">—</span>
                            @endif
                        </td>

                        <td style="text-align: center;">
                            @if($attendance->status === 'present')
                                <span class="badge badge-active">حاضر</span>
                            @elseif($attendance->status === 'late')
                                <span class="badge badge-pending">متأخر</span>
                            @else
                                <span class="badge badge-inactive">غائب</span>
                            @endif
                        </td>

                        <!-- Action -->
                        <td style="text-align: center;" class="print:hidden">
                            @if($attendance->delay_minutes > 15 && !$attendance->is_deducted)
                                <form action="{{ route('hr.attendance.deduct', $attendance) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.75rem; padding: 4px 8px;">
                                        تحويل لخصم
                                    </button>
                                </form>
                            @elseif($attendance->is_deducted)
                                <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary);">تم الخصم ({{ $attendance->deduction_amount }} ر.س)</span>
                            @else
                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">لا إجراء</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <i class="fa-solid fa-clipboard-list" style="font-size: 2rem; opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                            لا يوجد سجلات حضور لهذه الفترة.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); font-family: sans-serif;" class="print:hidden" dir="ltr">
            {{ $attendances->links() }}
        </div>
    </div>
    
    <script>
        function filterCurrentMonth() {
            const date = new Date();
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0); // Or use today's date if you want '1 to today'

            // Format to YYYY-MM-DD
            const formatDate = (d) => {
                let month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2) month = '0' + month;
                if (day.length < 2) day = '0' + day;

                return [year, month, day].join('-');
            };

            const today = formatDate(new Date());
            const firstDayStr = formatDate(firstDay);

            document.querySelector('input[name="date_from"]').value = firstDayStr;
            document.querySelector('input[name="date_to"]').value = today;
            
            document.getElementById('filterForm').submit();
        }
    </script>
</x-layouts.admin>


