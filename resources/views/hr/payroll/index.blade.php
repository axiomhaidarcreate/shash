<x-layouts.admin>
    <!-- Page Header & Actions -->
        <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1><i class="fa-solid fa-money-check-dollar" style="color: var(--primary-color);"></i> معالجة الرواتب</h1>
            <p>كشوف الرواتب الشهرية وإدارة الصرف</p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <button onclick="document.getElementById('disburseModal').classList.remove('hidden')" class="btn btn-success">
                <i class="fa-solid fa-hand-holding-dollar"></i> صرف الراتب
            </button>
            <button class="btn btn-outline">
                تصدير PDF <i class="fa-solid fa-file-pdf"></i>
            </button>
        </div>
    </div>

    <!-- Smart Filter & Search -->
    <div class="card mb-6">
        <form action="{{ route('hr.payroll.index') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 1rem; width: 100%;">
            <div style="flex: 1; min-width: 250px;">
                <label class="form-label" style="font-size: 0.85rem;">بحث وتاريخ موظف معين</label>
                <select name="employee_id" class="form-control">
                    <option value="all">جميع الموظفين</option>
                    @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}" {{ ($employeeId ?? 'all') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name_ar }} - {{ $emp->employee_code }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div style="min-width: 150px;">
                <label class="form-label" style="font-size: 0.85rem;">الشهر</label>
                <select name="month" class="form-control">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }} - {{ $m }}</option>
                    @endfor
                </select>
            </div>

            <div style="min-width: 150px;">
                <label class="form-label" style="font-size: 0.85rem;">السنة</label>
                <select name="year" class="form-control">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <button type="submit" class="btn" style="min-height: 44px; width: 100%;">
                    <i class="fa-solid fa-filter"></i> تطبيق
                </button>
            </div>
        </form>
    </div>

    @if(($employeeId ?? 'all') !== 'all')
        <!-- سجل الموظف -->
        <div class="card mb-6" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <img style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color);" src="{{ $selectedEmployee->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($selectedEmployee->full_name_ar ?? $selectedEmployee->full_name ?? '').'&background=d4af37&color=000' }}" alt="Avatar">
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 800;">{{ $selectedEmployee->full_name_ar }}</h3>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">سجل استلام الرواتب الشهري</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                @php
                    $monthNames = [1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'];
                    $mName = $monthNames[(int)$periodMonth] ?? $periodMonth;
                @endphp
                
                @if(isset($selectedMonthPayroll) && $selectedMonthPayroll->status === 'paid')
                    <div style="background: rgba(76, 175, 80, 0.1); border: 1px solid rgba(76, 175, 80, 0.3); border-radius: var(--radius-md); padding: 1rem; text-align: center; min-width: 160px;">
                        <p style="font-size: 0.8rem; color: var(--success-color); font-weight: 700; margin-bottom: 0.5rem;">راتب شهر {{ $mName }} {{ $periodYear }}</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: var(--success-color);" dir="ltr">{{ number_format($selectedMonthPayroll->net_salary, 2) }} <span style="font-size: 0.85rem;">ر.س</span></p>
                        <p style="font-size: 0.75rem; color: var(--success-color); font-weight: 700; margin-top: 0.25rem;"><i class="fa-solid fa-check"></i> تم الاستلام</p>
                    </div>
                @else
                    <div style="background: rgba(255, 152, 0, 0.1); border: 1px solid rgba(255, 152, 0, 0.3); border-radius: var(--radius-md); padding: 1rem; text-align: center; min-width: 160px;">
                        <p style="font-size: 0.8rem; color: var(--warning-color); font-weight: 700; margin-bottom: 0.5rem;">استلام شهر {{ $mName }} {{ $periodYear }}</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: var(--warning-color);" dir="ltr">0</p>
                        <p style="font-size: 0.75rem; color: var(--warning-color); font-weight: 700; margin-top: 0.25rem;"><i class="fa-solid fa-clock"></i> لم يستلم بعد</p>
                    </div>
                    @if(isset($expectedSalary))
                        <div style="background: rgba(33, 150, 243, 0.1); border: 1px solid rgba(33, 150, 243, 0.3); border-radius: var(--radius-md); padding: 1rem; text-align: center; min-width: 160px;">
                            <p style="font-size: 0.8rem; color: var(--info-color); font-weight: 700; margin-bottom: 0.5rem;">المستحق لشهر {{ $mName }}</p>
                            <p style="font-size: 1.5rem; font-weight: 800; color: var(--info-color);" dir="ltr">{{ number_format($expectedSalary['net'], 2) }} <span style="font-size: 0.85rem;">ر.س</span></p>
                            <p style="font-size: 0.75rem; color: var(--info-color); font-weight: 700; margin-top: 0.25rem;"><i class="fa-solid fa-hourglass-half"></i> قيد الانتظار للصرف</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="card mb-8" style="padding: 0; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">الشهر / السنة</th>
                            <th scope="col" style="text-align: center;">الراتب الأساسي</th>
                            <th scope="col" style="text-align: center;">الخصومات (سلف / غياب)</th>
                            <th scope="col" style="text-align: center;">صافي الراتب المستلم</th>
                            <th scope="col" style="text-align: center;">تاريخ الإقفال</th>
                            <th scope="col" style="text-align: center;">تقرير (مسير الراتب)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $pr)
                        <tr>
                            <td style="font-weight: 800;">{{ $pr->pay_period_month }} / {{ $pr->pay_period_year }}</td>
                            <td style="text-align: center; font-weight: 600;" dir="ltr">{{ number_format($pr->basic_salary, 2) }} ر.س</td>
                            <td style="text-align: center; font-weight: 600; color: var(--danger-color);" dir="ltr">-{{ number_format($pr->absent_deduction + $pr->other_deductions, 2) }} ر.س</td>
                            <td style="text-align: center; font-weight: 800; color: var(--success-color);" dir="ltr">{{ number_format($pr->net_salary, 2) }} ر.س</td>
                            <td style="text-align: center; font-weight: 600;">{{ \Carbon\Carbon::parse($pr->paid_at)->format('Y-m-d') }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('hr.payroll.slip', $pr->id) }}" target="_blank" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                    <i class="fa-solid fa-print"></i> طباعة الاستلام
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 2rem; opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                                لا يوجد سجل رواتب متاح.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color);" dir="ltr">
                {{ $payrolls->links() }}
            </div>
        </div>
    @else
        <!-- Stats Grid (Clickable) -->
        <div class="grid grid-cols-4 gap-4 mb-8">
            
            <!-- Total Expected (Primary Dark) -->
            <a href="{{ route('hr.payroll.index', ['month' => $periodMonth, 'year' => $periodYear, 'status' => 'all']) }}" class="card" style="background: linear-gradient(135deg, var(--primary-color), #1565C0); color: white; display: flex; flex-direction: column; justify-content: space-between; border: none; padding: 1.5rem; text-decoration: none; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: var(--radius-sm); font-size: 0.75rem;">كل الموظفين</span>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div style="text-align: center;">
                    <h3 style="font-size: 0.85rem; font-weight: 600; opacity: 0.9; margin-bottom: 0.2rem;">إجمالي رواتب الشهر الحالي</h3>
                    <div style="font-size: 1.8rem; font-weight: 800; display: flex; justify-content: center; align-items: baseline; gap: 4px;" dir="ltr">
                        <span style="font-size: 1rem; opacity: 0.8;">ر.س</span> {{ number_format($expectedTotalBase, 2) }}
                    </div>
                </div>
            </a>

            <!-- Total Paid -->
            <a href="{{ route('hr.payroll.index', ['month' => $periodMonth, 'year' => $periodYear, 'status' => 'paid']) }}" class="card" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; border: {{ $statusFilter == 'paid' ? '2px solid var(--success-color)' : '1px solid var(--border-color)' }}; text-decoration: none; background: {{ $statusFilter == 'paid' ? 'rgba(76, 175, 80, 0.05)' : 'var(--surface-color)' }};">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <span class="badge badge-active">تم الصرف</span>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(76, 175, 80, 0.15); color: var(--success-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                </div>
                <div style="text-align: center;">
                    <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.2rem;">إجمالي الرواتب المدفوعة</h3>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--success-color); display: flex; justify-content: center; align-items: baseline; gap: 4px;" dir="ltr">
                        <span style="font-size: 0.85rem;">ر.س</span> {{ number_format($totalPaidAmount, 2) }}
                    </div>
                </div>
            </a>

            <!-- Awaiting Signature -->
            <a href="{{ route('hr.payroll.index', ['month' => $periodMonth, 'year' => $periodYear, 'status' => 'approved']) }}" class="card" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; border: {{ $statusFilter == 'approved' ? '2px solid var(--info-color)' : '1px solid var(--border-color)' }}; text-decoration: none; background: {{ $statusFilter == 'approved' ? 'rgba(33, 150, 243, 0.05)' : 'var(--surface-color)' }};">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <span class="badge" style="background: rgba(33,150,243,0.15); color: var(--info-color);">بانتظار التوقيع</span>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(33, 150, 243, 0.15); color: var(--info-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                </div>
                <div style="text-align: center;">
                    <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.2rem;">بانتظار التوقيع والبصمة</h3>
                    <div style="font-size: 1.8rem; font-weight: 800; color: var(--info-color);" dir="ltr">{{ $approvedCount }}</div>
                </div>
            </a>

            <!-- Total Pending/Unpaid -->
            <a href="{{ route('hr.payroll.index', ['month' => $periodMonth, 'year' => $periodYear, 'status' => 'pending']) }}" class="card" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; border: {{ $statusFilter == 'pending' ? '2px solid var(--warning-color)' : '1px solid var(--border-color)' }}; text-decoration: none; background: {{ $statusFilter == 'pending' ? 'rgba(255, 152, 0, 0.05)' : 'var(--surface-color)' }};">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <span class="badge badge-pending">بانتظار الصرف</span>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(255, 152, 0, 0.15); color: var(--warning-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
                <div style="text-align: center;">
                    <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.2rem;">إجمالي المتبقي (غير مدفوع)</h3>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--warning-color); display: flex; justify-content: center; align-items: baseline; gap: 4px;" dir="ltr">
                        <span style="font-size: 0.85rem;">ر.س</span> {{ number_format($totalUnpaidAmount, 2) }}
                    </div>
                </div>
            </a>
        </div>

        <!-- Payroll Table Section -->
        <div class="card mb-8" style="padding: 0; overflow: hidden;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 800;">
                        {{ $statusFilter == 'paid' ? 'الموظفين (تم صرف رواتبهم)' : ($statusFilter == 'approved' ? 'الموظفين (بانتظار التوقيع والبصمة)' : ($statusFilter == 'pending' ? 'الموظفين (بانتظار الصرف)' : 'كل الموظفين النشطين')) }}
                    </h3>
                </div>
            </div>

            @if(session('success'))
                <div class="toast toast-success" style="position: relative; top: 0; left: 0; transform: none; width: auto; margin: 1rem 1.5rem; text-align: right; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="toast toast-error" style="position: relative; top: 0; left: 0; transform: none; width: auto; margin: 1rem 1.5rem; text-align: right; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Table -->
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">اسم الموظف</th>
                            <th scope="col" style="text-align: center;">الراتب الأساسي (الاستحقاق)</th>
                            <th scope="col" style="text-align: center; color: var(--danger-color);">الخصومات (سلف / غياب)</th>
                            <th scope="col" style="text-align: center; color: var(--success-color);">صافي الراتب المستحق</th>
                            <th scope="col" style="text-align: center;">الحالة</th>
                            <th scope="col" style="text-align: center;">التوقيع / البصمة</th>
                            <th scope="col" style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeesList as $emp)
                            @php 
                                $payroll = $emp->payrolls->first(); 
                                if(!$payroll) {
                                    $expected = \App\Http\Controllers\Web\PayrollController::calculateExpectedSalary($emp, $periodMonth, $periodYear);
                                }
                            @endphp
                        <tr class="bg-white hover:bg-slate-50 transition whitespace-nowrap">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img class="w-8 h-8 rounded-full object-cover" src="{{ $emp->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($emp->full_name_ar ?? $emp->full_name ?? '').'&background=random' }}" alt="Avatar">
                                <div class="text-left" style="direction: ltr;">
                                    <div class="text-sm font-bold text-primary text-right" style="direction: rtl;">{{ $emp->full_name_ar ?? 'موظف غير متوفر' }}</div>
                                    <div class="font-normal text-slate-400 text-[10px] text-right" style="direction: rtl;">{{ $emp->employee_code }}</div>
                                </div>
                            </td>
                            
                            @if($payroll && $payroll->status === 'paid')
                                <!-- ✅ Paid Employee Row -->
                                <td class="px-6 py-4 text-center text-primary font-bold" dir="ltr">{{ number_format($payroll->basic_salary, 2) }} ر.س</td>
                                <td class="px-6 py-4 text-center text-red-500 font-bold" dir="ltr" title="غياب: {{ $payroll->absent_deduction }} | سلف وخصومات: {{ $payroll->other_deductions }} | سحوبات: {{ $payroll->withdrawal_deduction }}">
                                    -{{ number_format($payroll->absent_deduction + $payroll->other_deductions + $payroll->withdrawal_deduction, 2) }} ر.س
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-emerald-600 text-lg" dir="ltr">{{ number_format($payroll->net_salary, 2) }} ر.س</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-3 py-1 rounded-full inline-block text-center border border-emerald-200">✅ تم الصرف</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($payroll->employee_signature_url)
                                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 font-bold">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            وقّع + بصمة
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('hr.payroll.slip', $payroll->id) }}" target="_blank" class="inline-flex items-center justify-center gap-2 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition border border-blue-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0v2.796c0 1.136.879 2.073 2.006 2.152a48.24 48.24 0 006.488 0c1.127-.08 2.006-1.016 2.006-2.152V10.25z" /></svg>
                                        مسير الراتب
                                    </a>
                                </td>

                            @elseif($payroll && $payroll->status === 'approved')
                                <!-- ⏳ Approved — Awaiting Employee Signature & Face -->
                                <td class="px-6 py-4 text-center text-primary font-bold" dir="ltr">{{ number_format($payroll->basic_salary, 2) }} ر.س</td>
                                <td class="px-6 py-4 text-center text-red-500 font-bold" dir="ltr" title="غياب: {{ $payroll->absent_deduction }} | سلف وخصومات: {{ $payroll->other_deductions }} | سحوبات: {{ $payroll->withdrawal_deduction }}">
                                    -{{ number_format($payroll->absent_deduction + $payroll->other_deductions + $payroll->withdrawal_deduction, 2) }} ر.س
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-blue-600 text-lg" dir="ltr">{{ number_format($payroll->net_salary, 2) }} ر.س</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-3 py-1 rounded-full inline-block text-center border border-blue-200">⏳ بانتظار التوقيع</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($payroll->employee_signature_url && $payroll->is_face_id_verified)
                                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 font-bold bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            وقّع ✦ بصمة الوجه
                                        </span>
                                    @elseif($payroll->employee_signature_url)
                                        <span class="inline-flex items-center gap-1 text-xs text-amber-700 font-bold bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                                            ✍️ وقّع — بانتظار البصمة
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-secondary font-bold bg-slate-50 px-2 py-1 rounded-lg border border-slate-200">
                                            🕐 لم يوقع بعد
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($payroll->employee_signature_url && $payroll->is_face_id_verified)
                                        {{-- الموظف وقّع وبصم — يمكن تأكيد الدفع --}}
                                        <form action="{{ route('hr.payroll.confirmPayment', $payroll->id) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('تأكيد صرف الراتب نهائياً؟ تم التحقق من توقيع الموظف وبصمة وجهه.')"
                                                class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                تأكيد الدفع ✅
                                            </button>
                                        </form>
                                    @else
                                        {{-- الموظف لم يوقع — الزر معطل --}}
                                        <button disabled
                                            class="inline-flex items-center justify-center gap-2 bg-slate-100 text-slate-400 px-4 py-2 rounded-xl text-xs font-bold cursor-not-allowed"
                                            title="في انتظار توقيع الموظف وبصمة وجهه من التطبيق">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                            بانتظار التوقيع
                                        </button>
                                    @endif
                                </td>

                            @else
                                <!-- 🔴 Unpaid Employee Row -->
                                <td class="px-6 py-4 text-center text-primary font-bold" dir="ltr">{{ number_format($expected['gross'], 2) }} ر.س</td>
                                <td class="px-6 py-4 text-center text-red-500 font-bold" dir="ltr" title="غياب: {{ $expected['absent_deduction'] }} | سلف وخصومات: {{ $expected['loan_deduction'] + ($expected['entitlement_deductions'] ?? 0) }} | سحوبات: {{ $expected['withdrawal_deduction'] ?? 0 }}">
                                    -{{ number_format($expected['absent_deduction'] + $expected['loan_deduction'] + ($expected['withdrawal_deduction'] ?? 0) + ($expected['entitlement_deductions'] ?? 0), 2) }} ر.س
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-amber-600 text-lg" dir="ltr">{{ number_format($expected['net'], 2) }} ر.س</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-3 py-1 rounded-full inline-block text-center border border-amber-200">غير مدفوع</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs text-slate-400">—</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('hr.payroll.close_month', $emp) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="month" value="{{ $periodMonth }}">
                                        <input type="hidden" name="year" value="{{ $periodYear }}">
                                        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm" onclick="return confirm('إرسال طلب توقيع الراتب للموظف؟ سيظهر له في تطبيقه ليوقع ويبصم وجهه.')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                                            إرسال طلب توقيع
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <div class="text-slate-400 font-bold">لا توجد بيانات حالياً.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($employeesList, 'links'))
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color);" dir="ltr">
                    {{ $employeesList->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Disburse Salary Modal -->
    <div id="disburseModal" style="display: none; position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center; overflow-y: auto;" class="modal-backdrop">
        <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; padding: 0; overflow: hidden; animation: slideDown 0.3s ease-out;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; color: var(--primary-color);">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(76,175,80,0.1); color: var(--success-color); display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    صرف راتب مخصص
                </h3>
                <button type="button" onclick="document.getElementById('disburseModal').classList.add('hidden')" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-secondary);">&times;</button>
            </div>
            <form action="{{ route('hr.payroll.process_custom') }}" method="POST">
                @csrf
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                    <div style="padding: 1rem; background: rgba(33, 150, 243, 0.1); border: 1px solid rgba(33, 150, 243, 0.3); border-radius: var(--radius-md); display: flex; gap: 0.75rem; align-items: flex-start;">
                        <i class="fa-solid fa-circle-info" style="color: var(--info-color); margin-top: 0.25rem;"></i>
                        <p style="font-size: 0.85rem; color: var(--info-color); line-height: 1.6; margin: 0;">
                            سيتم احتساب الراتب وإضافة الخصومات (الغياب، التأخير، السحوبات، وغيرها) بشكل متكامل وآلي من قاعدة البيانات.
                        </p>
                    </div>

                    <div>
                        <label class="form-label" style="font-size: 0.9rem;">اسم الموظف</label>
                        <select name="employee_id" required class="form-control" style="width: 100%;">
                            <option value="all">صرف لجميع الموظفين (المتبقين)</option>
                            @foreach($allEmployees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->full_name_ar }} - {{ $emp->employee_code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" style="font-size: 0.9rem;">الشهر</label>
                            <select name="month" required class="form-control" style="width: 100%;">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }} - {{ $m }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 0.9rem;">السنة</label>
                            <select name="year" required class="form-control" style="width: 100%;">
                                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div style="padding: 1.25rem 1.5rem; background: var(--surface-color); border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 1rem;">
                    <button type="button" onclick="document.getElementById('disburseModal').classList.add('hidden')" class="btn btn-outline">
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-check"></i> صرف واعتماد
                    </button>
                </div>
            </form>
        </div>
    </div>
    <style>
        /* Fix hidden class on modal */
        #disburseModal:not(.hidden) {
            display: flex !important;
        }
    </style>
</x-layouts.admin>


