<x-layouts.admin>
    <!-- Page Header & Actions -->
        <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1><i class="fa-solid fa-users-viewfinder" style="color: var(--primary-color);"></i> إدارة الموظفين</h1>
            <p>تتبع وإدارة جميع أعضاء الفريق في مكان واحد</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->hasRole('admin')))
            <a href="{{ route('hr.contract_templates.index') }}" class="btn btn-outline">
                <i class="fa-solid fa-file-contract"></i> قوالب العقود
            </a>
            @endif
            <button onclick="document.getElementById('addLeaveModal').classList.remove('hidden')" class="btn" style="background: rgba(33, 150, 243, 0.15); color: var(--info-color); border: 1px solid rgba(33, 150, 243, 0.3);">
                <i class="fa-solid fa-calendar-plus"></i> إضافة إجازة
            </button>
            <button onclick="document.getElementById('addResignationModal').classList.remove('hidden')" class="btn btn-danger">
                <i class="fa-solid fa-user-minus"></i> استقالة
            </button>
            <a href="{{ route('hr.employees.create') }}" class="btn">
                <i class="fa-solid fa-user-plus"></i> إضافة موظف
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="toast toast-success" style="position: relative; top: 0; left: 0; transform: none; width: 100%; margin-bottom: 1rem; text-align: right; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('info'))
    <div class="toast" style="background: rgba(33,150,243,0.9); color: white; position: relative; top: 0; left: 0; transform: none; width: 100%; margin-bottom: 1rem; text-align: right; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-info-circle"></i> {{ session('info') }}
    </div>
    @endif

    {{-- Pending Employees Section (Admin Only) --}}
    @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->hasRole('admin')) && $pendingEmployees->count() > 0)
    <div class="card" style="margin-bottom: 1.5rem; border-color: rgba(255, 152, 0, 0.3); padding: 0;">
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px; background: rgba(255,152,0,0.05);">
            <i class="fa-solid fa-user-clock" style="color: var(--warning-color);"></i>
            <h3 style="font-weight: 800; color: var(--warning-color);">موظفون في انتظار موافقتك ({{ $pendingEmployees->count() }})</h3>
        </div>
        <div>
            @foreach($pendingEmployees as $pending)
            <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,152,0,0.15); color: var(--warning-color); display: flex; align-items: center; justify-content: center; font-weight: 800;">
                        {{ mb_substr($pending->full_name_ar, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--text-primary);">{{ $pending->full_name_ar }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $pending->department->name_ar ?? 'غير محدد' }} · {{ $pending->employee_code }}</div>
                    </div>
                </div>
                <form action="{{ route('hr.employees.approve', $pending) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-check"></i> تفعيل وقبول
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-3 mb-8">
        <!-- Dashboard Stat -->
        <div class="card" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: var(--primary-glow); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.2rem;">إجمالي الموظفين</p>
                <h3 style="font-size: 1.5rem; font-weight: 800;">{{ number_format($totalEmployees) }}</h3>
            </div>
        </div>

        <!-- Vacation Stat Link -->
        <a href="{{ route('hr.employees.vacation') }}" class="card" style="display: flex; align-items: center; gap: 1rem; text-decoration: none; color: inherit; transition: all 0.3s ease;">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: rgba(33, 150, 243, 0.15); color: var(--info-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fa-solid fa-plane-departure"></i>
            </div>
            <div>
                <p style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.2rem;">الموظفين في الإجازات</p>
                <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--info-color);">{{ $onVacationCount }}</h3>
            </div>
        </a>

        <!-- Dismissed Stat Link -->
        <a href="{{ route('hr.employees.dismissed') }}" class="card" style="display: flex; align-items: center; gap: 1rem; text-decoration: none; color: inherit; transition: all 0.3s ease;">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: rgba(244, 67, 54, 0.15); color: var(--danger-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fa-solid fa-user-large-slash"></i>
            </div>
            <div>
                <p style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.2rem;">الموظفين غير النشطين</p>
                <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--danger-color);">{{ $dismissedCount }}</h3>
            </div>
        </a>
    </div>

    <!-- Employee List Section -->
    <div class="card" style="padding: 0; overflow: hidden;">
        
        <!-- Table Toolbar -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color);">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                
                <!-- Search Group -->
                <form method="GET" action="{{ route('hr.employees.index') }}" style="display:flex; align-items:center; gap:0.5rem;">
                    @if($departmentId)
                        <input type="hidden" name="department_id" value="{{ $departmentId }}">
                    @endif
                    <div>
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="بحث بالاسم أو رقم الموظف..." style="width: 300px;">
                    </div>
                    <button type="submit" class="btn" style="min-height: 44px; padding: 0 1.5rem;">بحث</button>
                    @if($search)
                    <a href="{{ route('hr.employees.index', $departmentId ? ['department_id' => $departmentId] : []) }}" class="btn btn-outline" style="min-height: 44px; padding: 0 1rem;">✕</a>
                    @endif
                </form>

                <!-- Filter Group -->
                <form method="GET" action="{{ route('hr.employees.index') }}" style="display:flex; align-items:center; gap:0.5rem;">
                    @if($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    <select name="department_id" class="form-control" style="min-width: 200px;">
                        <option value="">كل الأقسام</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name_ar }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline" style="min-height: 44px;">تصفية <i class="fa-solid fa-filter"></i></button>
                    @if($departmentId)
                    <a href="{{ route('hr.employees.index', $search ? ['search' => $search] : []) }}" class="btn btn-outline" style="min-height: 44px; padding: 0 1rem;">✕</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Employees Table -->
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">الموظف بالكامل</th>
                        <th scope="col">المسمى الوظيفي</th>
                        <th scope="col">الراتب</th>
                        <th scope="col">أيام العمل</th>
                        <th scope="col">الحالة</th>
                        <th scope="col" style="text-align: center; min-width: 170px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td style="display: flex; align-items: center; gap: 12px; padding: 12px;">
                            <img style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);" src="{{ $employee->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($employee->full_name_ar ?? $employee->full_name).'&background=d4af37&color=000' }}" alt="{{ $employee->full_name_ar }}">
                            <div>
                                <div style="font-weight: 800; font-size: 0.95rem;">{{ $employee->full_name_ar }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $employee->email }}</div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-primary);">{{ $employee->job_title_ar ?? $employee->department->name_ar ?? 'غير مدخل' }}</div>
                            <div style="font-size: 0.75rem; color: var(--primary-color); margin-top: 2px;">{{ $employee->employee_code }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 800;">SAR {{ number_format($employee->base_salary) }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">شهرياً</div>
                        </td>
                        <td style="font-weight: 700;">
                            {{ $employee->working_days ?? '26' }} يوم
                        </td>
                        <td>
                            @if($employee->leaveRequests->isNotEmpty())
                                <span class="badge badge-prep">في إجازة</span>
                            @else
                                <span class="badge badge-active">نشط</span>
                            @endif
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            <div style="display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                <!-- Edit Button -->
                                <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-sm" title="تعديل" style="background: #2196F3; color: #fff; border: none; padding: 0.35rem 0.75rem; font-size: 0.82rem; min-height: unset; border-radius: var(--radius-sm); display: inline-flex; align-items: center; gap: 5px; text-decoration: none;">
                                    <i class="fa-solid fa-pen-to-square"></i> تعديل
                                </a>

                                <!-- Delete Button -->
                                <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('هل أنت متأكد من حذف الموظف ({{ $employee->full_name_ar }})؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" title="حذف" style="background: #F44336; color: #fff; border: none; padding: 0.35rem 0.75rem; font-size: 0.82rem; min-height: unset; border-radius: var(--radius-sm); cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                                        <i class="fa-solid fa-trash"></i> حذف
                                    </button>
                                </form>

                                <!-- Print Contract (if linked) -->
                                @if($employee->contract_template_id)
                                <a href="{{ route('hr.employees.printContract', $employee) }}" target="_blank" class="btn btn-sm btn-outline" title="طباعة العقد" style="padding: 0.35rem 0.6rem; font-size: 0.82rem; min-height: unset; color: var(--success-color); border-color: rgba(76, 175, 80, 0.4);">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <i class="fa-solid fa-users-slash" style="font-size: 2rem; opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                            لا يوجد موظفين لعرضهم.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); font-family: sans-serif;" dir="ltr">
            {{ $employees->links() }}
        </div>
    </div>

    @php
        $allActiveEmployees = \Illuminate\Support\Facades\Cache::remember('active_employees_list', 60, fn() => \App\Models\Employee::select('id', 'full_name_ar', 'employee_code')->where('is_active', true)->get());
    @endphp

    <!-- Add Leave Modal -->
    <div id="addLeaveModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('addLeaveModal').classList.add('hidden')"></div>
        <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="card relative w-full max-w-lg shadow-2xl p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                            </div>
                            <h3 class="text-xl font-bold text-primary">إضافة إجازة لموظف</h3>
                        </div>
                        <button onclick="document.getElementById('addLeaveModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form action="{{ route('hr.requests.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-primary mb-2">الموظف <span class="text-red-500">*</span></label>
                                <select name="employee_id" required class="form-control">
                                    <option value="">اختر الموظف...</option>
                                    @foreach($allActiveEmployees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name_ar }} - {{ $emp->employee_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-primary mb-2">نوع الإجازة <span class="text-red-500">*</span></label>
                                <select name="leave_type" required class="form-control">
                                    <option value="annual">إجازة سنوية</option>
                                    <option value="sick">إجازة مرضية</option>
                                    <option value="emergency">إجازة طارئة</option>
                                    <option value="unpaid">إجازة غير مدفوعة</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">تاريخ البداية <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">تاريخ الانتهاء <span class="text-red-500">*</span></label>
                                    <input type="date" name="end_date" required value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-primary mb-2">سبب / ملاحظات</label>
                                <textarea name="reason" rows="2" placeholder="سبب الإجازة (اختياري)" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-3">
                            <button type="submit" class="btn flex-1">حفظ الإجازة</button>
                            <button type="button" onclick="document.getElementById('addLeaveModal').classList.add('hidden')" class="btn btn-outline px-6">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Resignation Modal -->
    <div id="addResignationModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('addResignationModal').classList.add('hidden')"></div>
        <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="card relative w-full max-w-lg shadow-2xl p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                            </div>
                            <h3 class="text-xl font-bold text-primary">تسجيل استقالة</h3>
                        </div>
                        <button onclick="document.getElementById('addResignationModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form action="{{ route('hr.requests.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="leave_type" value="resignation">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-primary mb-2">الموظف <span class="text-red-500">*</span></label>
                                <select name="employee_id" required class="form-control">
                                    <option value="">اختر الموظف...</option>
                                    @foreach($allActiveEmployees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name_ar }} - {{ $emp->employee_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">تاريخ الاستقالة <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">آخر يوم عمل <span class="text-red-500">*</span></label>
                                    <input type="date" name="end_date" required value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-primary mb-2">سبب الاستقالة (مطلوب)</label>
                                <textarea name="reason" rows="3" required placeholder="يرجى توضيح سبب الاستقالة هنا..." class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-3">
                            <button type="submit" class="btn flex-1">رفع طلب الاستقالة</button>
                            <button type="button" onclick="document.getElementById('addResignationModal').classList.add('hidden')" class="btn btn-outline px-6">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>


