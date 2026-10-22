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
                                    $expected = \App\Http\Controllers\HR\PayrollController::calculateExpectedSalary($emp, $periodMonth, $periodYear);
                                }
                            @endphp
                        <tr>
                            <td style="display: flex; align-items: center; gap: 12px; padding: 12px;">
                                <img style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);" 
                                     src="{{ $emp->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($emp->full_name_ar ?? $emp->full_name ?? '').'&background=d4af37&color=000' }}" 
                                     alt="Avatar">
                                <div>
                                    <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">
                                        {{ $emp->full_name_ar ?? 'موظف غير متوفر' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--primary-color); font-weight: 600;">
                                        {{ $emp->employee_code }}
                                    </div>
                                </div>
                            </td>
                            
                            @if($payroll && $payroll->status === 'paid')
                                <!-- ✅ Paid Employee Row -->
                                <td style="text-align: center; font-weight: 700; color: var(--text-primary);" dir="ltr">
                                    {{ number_format($payroll->basic_salary, 2) }} <span style="font-size: 0.75rem; color: var(--text-secondary);">ر.س</span>
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--danger-color);" dir="ltr" title="غياب: {{ $payroll->absent_deduction }} | سلف وخصومات: {{ $payroll->other_deductions }} | سحوبات: {{ $payroll->withdrawal_deduction }}">
                                    -{{ number_format($payroll->absent_deduction + $payroll->other_deductions + $payroll->withdrawal_deduction, 2) }} <span style="font-size: 0.75rem;">ر.س</span>
                                </td>
                                <td style="text-align: center; font-weight: 800; font-size: 1.05rem; color: var(--success-color);" dir="ltr">
                                    {{ number_format($payroll->net_salary, 2) }} <span style="font-size: 0.75rem;">ر.س</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-active"><i class="fa-solid fa-check-double"></i> تم الصرف</span>
                                </td>
                                <td style="text-align: center;">
                                    @if($payroll->employee_signature_url)
                                        <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.8rem; color: var(--success-color); font-weight: 700;">
                                            <i class="fa-solid fa-circle-check"></i> وقّع + بصمة
                                        </span>
                                    @else
                                        <span style="color: var(--text-secondary); font-size: 0.85rem;">—</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('hr.payroll.slip', $payroll->id) }}" target="_blank" class="btn btn-outline btn-sm" style="font-size: 0.8rem; padding: 0.35rem 0.85rem;">
                                        <i class="fa-solid fa-file-invoice"></i> مسير الراتب
                                    </a>
                                </td>

                            @elseif($payroll && $payroll->status === 'approved')
                                <!-- ⏳ Approved — Awaiting Employee Signature & Face -->
                                <td style="text-align: center; font-weight: 700; color: var(--text-primary);" dir="ltr">
                                    {{ number_format($payroll->basic_salary, 2) }} <span style="font-size: 0.75rem; color: var(--text-secondary);">ر.س</span>
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--danger-color);" dir="ltr" title="غياب: {{ $payroll->absent_deduction }} | سلف وخصومات: {{ $payroll->other_deductions }} | سحوبات: {{ $payroll->withdrawal_deduction }}">
                                    -{{ number_format($payroll->absent_deduction + $payroll->other_deductions + $payroll->withdrawal_deduction, 2) }} <span style="font-size: 0.75rem;">ر.س</span>
                                </td>
                                <td style="text-align: center; font-weight: 800; font-size: 1.05rem; color: var(--info-color);" dir="ltr">
                                    {{ number_format($payroll->net_salary, 2) }} <span style="font-size: 0.75rem;">ر.س</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge" style="background: rgba(33, 150, 243, 0.15); color: var(--info-color); border: 1px solid rgba(33, 150, 243, 0.3);">
                                        <i class="fa-solid fa-clock"></i> بانتظار التوقيع
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    @if($payroll->employee_signature_url && $payroll->is_face_id_verified)
                                        <span class="badge badge-ready" style="gap: 4px;">
                                            <i class="fa-solid fa-circle-check"></i> وقّع ✦ بصمة الوجه
                                        </span>
                                    @elseif($payroll->employee_signature_url)
                                        <span class="badge" style="background: rgba(255, 152, 0, 0.15); color: var(--warning-color); border: 1px solid rgba(255, 152, 0, 0.3);">
                                            ✍️ وقّع — بانتظار البصمة
                                        </span>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">
                                            <i class="fa-regular fa-clock"></i> لم يوقع بعد
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($payroll->employee_signature_url && $payroll->is_face_id_verified)
                                        {{-- الموظف وقّع وبصم — يمكن تأكيد الدفع --}}
                                        <form action="{{ route('hr.payroll.confirmPayment', $payroll->id) }}" method="POST" style="display: inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('تأكيد صرف الراتب نهائياً؟ تم التحقق من توقيع الموظف وبصمة وجهه.')"
                                                class="btn btn-success btn-sm" style="font-size: 0.8rem; padding: 0.35rem 0.85rem;">
                                                <i class="fa-solid fa-check"></i> تأكيد الدفع
                                            </button>
                                        </form>
                                    @else
                                        {{-- الموظف لم يوقع — الزر معطل --}}
                                        <button disabled
                                            class="btn btn-ghost btn-sm"
                                            style="font-size: 0.8rem; opacity: 0.45; cursor: not-allowed; border: 1px dashed var(--border-color);"
                                            title="في انتظار توقيع الموظف وبصمة وجهه من التطبيق">
                                            <i class="fa-solid fa-lock"></i> بانتظار التوقيع
                                        </button>
                                    @endif
                                </td>

                            @else
                                <!-- 🔴 Unpaid Employee Row -->
                                @php
                                    $totalDeductions = ($expected['absent_deduction'] ?? 0) + ($expected['loan_deduction'] ?? 0) + ($expected['withdrawal_deduction'] ?? 0) + ($expected['entitlement_deductions'] ?? 0);
                                @endphp
                                <td style="text-align: center; font-weight: 700; color: var(--text-primary);" dir="ltr">
                                    {{ number_format($expected['gross'], 2) }} <span style="font-size: 0.75rem; color: var(--text-secondary);">ر.س</span>
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--danger-color);" dir="ltr" title="غياب: {{ $expected['absent_deduction'] }} | سلف وخصومات: {{ $expected['loan_deduction'] + ($expected['entitlement_deductions'] ?? 0) }} | سحوبات: {{ $expected['withdrawal_deduction'] ?? 0 }}">
                                    -{{ number_format($totalDeductions, 2) }} <span style="font-size: 0.75rem;">ر.س</span>
                                </td>
                                <td style="text-align: center; font-weight: 800; font-size: 1.05rem; color: var(--warning-color);" dir="ltr">
                                    {{ number_format($expected['net'], 2) }} <span style="font-size: 0.75rem;">ر.س</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-pending">غير مدفوع</span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="color: var(--text-secondary); font-size: 0.85rem;">—</span>
                                </td>
                                <td style="text-align: center;">
                                    <form action="{{ route('hr.payroll.close_month', $emp) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="month" value="{{ $periodMonth }}">
                                        <input type="hidden" name="year" value="{{ $periodYear }}">
                                        <button type="submit" class="btn btn-sm" style="font-size: 0.8rem; padding: 0.35rem 0.85rem;" onclick="return confirm('إرسال طلب توقيع الراتب للموظف؟ سيظهر له في تطبيقه ليوقع ويبصم وجهه.')">
                                            <i class="fa-solid fa-paper-plane"></i> إرسال طلب توقيع
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3.5rem 1rem; color: var(--text-secondary);">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.75rem;">
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--text-secondary);">
                                            <i class="fa-solid fa-users-slash"></i>
                                        </div>
                                        <div style="font-weight: 700; font-size: 0.95rem;">لا توجد بيانات حالياً.</div>
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


