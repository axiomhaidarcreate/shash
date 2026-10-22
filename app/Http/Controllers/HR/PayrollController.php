<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        // مزامنة بيانات التوقيعات وبصمة الوجه من Firebase RTDB
        self::syncPayrollsFromFirebase();

        $periodMonth  = $request->get('month', now()->month);
        $periodYear   = $request->get('year', now()->year);
        $employeeId   = $request->get('employee_id');
        $statusFilter = $request->get('status', 'all');

        $allEmployees = Employee::where('is_active', true)->get();

        // عرض سجل موظف محدد
        if ($employeeId && $employeeId !== 'all') {
            $selectedEmployee = Employee::find($employeeId);
            $payrolls = Payroll::where('employee_id', $employeeId)
                ->orderBy('pay_period_year', 'desc')
                ->orderBy('pay_period_month', 'desc')
                ->paginate(12)->appends($request->all());

            $selectedMonthPayroll = Payroll::where('employee_id', $employeeId)
                ->where('pay_period_month', $periodMonth)
                ->where('pay_period_year', $periodYear)
                ->first();

            $expectedSalary = null;
            if (!$selectedMonthPayroll || $selectedMonthPayroll->status !== 'paid') {
                $expectedSalary = self::calculateExpectedSalary($selectedEmployee, $periodMonth, $periodYear);
            }

            return view('hr.payroll.index', compact('selectedEmployee', 'payrolls', 'periodMonth', 'periodYear', 'statusFilter', 'allEmployees', 'employeeId', 'selectedMonthPayroll', 'expectedSalary'));
        }

        // العرض العادي للشهر
        $query = Employee::where('is_active', true)->with([
            'department',
            'payrolls',
            'leaveRequests' => fn($q) => $q->where('status', 'approved'),
            'attendance'    => fn($q) => $q->whereYear('work_date', $periodYear)->whereMonth('work_date', $periodMonth),
            'entitlements'  => fn($q) => $q->whereYear('date', $periodYear)->whereMonth('date', $periodMonth),
        ]);

        if ($statusFilter === 'paid') {
            $query->whereHas('payrolls', fn($q) => $q->where('pay_period_month', $periodMonth)->where('pay_period_year', $periodYear)->where('status', 'paid'));
        } elseif ($statusFilter === 'approved') {
            $query->whereHas('payrolls', fn($q) => $q->where('pay_period_month', $periodMonth)->where('pay_period_year', $periodYear)->where('status', 'approved'));
        } elseif ($statusFilter === 'pending') {
            $query->whereDoesntHave('payrolls', fn($sq) => $sq->where('pay_period_month', $periodMonth)->where('pay_period_year', $periodYear)->whereIn('status', ['paid', 'approved']));
        }

        $employeesList = $query->paginate(20)->appends($request->all());

        $expectedTotalBase = Employee::where('is_active', true)->sum('base_salary');
        $totalPaidAmount   = Payroll::where('pay_period_month', $periodMonth)->where('pay_period_year', $periodYear)->where('status', 'paid')->sum('net_salary');
        $approvedCount     = Payroll::where('pay_period_month', $periodMonth)->where('pay_period_year', $periodYear)->where('status', 'approved')->count();
        $totalEmployees    = Employee::where('is_active', true)->count();
        $processedCount    = Payroll::where('pay_period_month', $periodMonth)->where('pay_period_year', $periodYear)->where('status', 'paid')->count();
        $unpaidCount       = max(0, $totalEmployees - $processedCount);

        $unpaidEmployeeIds  = Employee::where('is_active', true)->whereDoesntHave('payrolls', fn($q) => $q->where('pay_period_month', $periodMonth)->where('pay_period_year', $periodYear)->whereIn('status', ['paid', 'approved']))->pluck('id');
        $totalUnpaidAmount  = Employee::whereIn('id', $unpaidEmployeeIds)->sum('base_salary');

        return view('hr.payroll.index', compact(
            'expectedTotalBase', 'totalPaidAmount', 'totalUnpaidAmount', 'approvedCount',
            'processedCount', 'totalEmployees', 'unpaidCount', 'periodMonth', 'periodYear',
            'employeesList', 'statusFilter', 'allEmployees', 'employeeId'
        ));
    }

    public static function calculateExpectedSalary(Employee $employee, $periodMonth, $periodYear)
    {
        $absenceSettingRate = (float) \App\Models\Setting::get('absence_deduction', 100);
        $lateSetting        = (float) \App\Models\Setting::get('late_deduction', 0);

        $periodStart = \Carbon\Carbon::create($periodYear, $periodMonth, 1);
        $periodEnd   = $periodStart->copy()->endOfMonth();
        $now         = now();
        $toDate      = $periodEnd->isAfter($now) ? $now : $periodEnd;

        $latestShiftEndMinutes = 17 * 60;
        for ($i = 1; $i <= 4; $i++) {
            if (\App\Models\Setting::get("shift_{$i}_enabled") == '1') {
                $endStr = \App\Models\Setting::get("shift_{$i}_end", '17:00');
                $parts  = explode(':', $endStr);
                $endMins = (intval($parts[0] ?? 17) * 60) + intval($parts[1] ?? 0);
                if ($endMins > $latestShiftEndMinutes) {
                    $latestShiftEndMinutes = $endMins;
                }
            }
        }

        $attendedDates = \App\Models\Attendance::where('employee_id', $employee->id)
            ->whereYear('work_date', $periodYear)
            ->whereMonth('work_date', $periodMonth)
            ->whereNotNull('check_in')
            ->pluck('work_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        $dynamicAbsentDays = 0;
        for ($i = 0; $i <= $toDate->diffInDays($periodStart); $i++) {
            $date = $periodStart->copy()->addDays($i);
            if ($date->isFriday()) continue;
            $dateStr = $date->format('Y-m-d');
            if (!in_array($dateStr, $attendedDates)) {
                $isAbsent = true;
                if ($date->isSameDay($now)) {
                    $inTotalMinutes = ($now->hour * 60) + $now->minute;
                    if ($inTotalMinutes <= $latestShiftEndMinutes) {
                        $isAbsent = false;
                    }
                }
                if ($isAbsent) $dynamicAbsentDays++;
            }
        }

        if ($employee->relationLoaded('attendance')) {
            $explicitAbsentDays = $employee->attendance->filter(function ($a) use ($periodYear, $periodMonth) {
                $d = \Carbon\Carbon::parse($a->work_date);
                return $d->year == $periodYear && $d->month == $periodMonth && $a->status === 'absent';
            })->count();
            $lateDays = $employee->attendance->filter(function ($a) use ($periodYear, $periodMonth) {
                $d = \Carbon\Carbon::parse($a->work_date);
                return $d->year == $periodYear && $d->month == $periodMonth && $a->status === 'late';
            })->count();
        } else {
            $explicitAbsentDays = \App\Models\Attendance::where('employee_id', $employee->id)->where('status', 'absent')->whereYear('work_date', $periodYear)->whereMonth('work_date', $periodMonth)->count();
            $lateDays           = \App\Models\Attendance::where('employee_id', $employee->id)->where('status', 'late')->whereYear('work_date', $periodYear)->whereMonth('work_date', $periodMonth)->count();
        }

        $absentDays = max($explicitAbsentDays, $dynamicAbsentDays);
        $dailySalary = $employee->base_salary / 30;
        $autoAbsentDeduction = ($absentDays * $dailySalary * ($absenceSettingRate / 100)) + ($lateDays * $lateSetting);

        if ($employee->relationLoaded('leaveRequests')) {
            $legacyLoans = $employee->leaveRequests->filter(fn($r) => $r->status === 'approved' && $r->leave_type === 'loan')->sum(function ($req) {
                preg_match('/بقيمة\s*([\d\.]+)/', $req->reason, $matches);
                return isset($matches[1]) ? (float)$matches[1] : 0;
            });
            $legacyWithdrawals = $employee->leaveRequests->filter(fn($r) => $r->status === 'approved' && (str_contains($r->leave_type, 'سحب') || in_array($r->leave_type, ['withdrawal', 'سحب'])))->sum(function ($req) {
                preg_match('/بقيمة\s*([\d\.]+)/', $req->reason, $matches);
                return isset($matches[1]) ? (float)$matches[1] : 0;
            });
        } else {
            $legacyLoansReqs   = \App\Models\LeaveRequest::where('employee_id', $employee->id)->where('status', 'approved')->where('leave_type', 'loan')->get();
            $legacyLoans       = $legacyLoansReqs->sum(function ($req) { preg_match('/بقيمة\s*([\d\.]+)/', $req->reason, $m); return isset($m[1]) ? (float)$m[1] : 0; });
            $legacyWithdrawalsReqs = \App\Models\LeaveRequest::where('employee_id', $employee->id)->where('status', 'approved')->where(fn($q) => $q->where('leave_type', 'LIKE', '%سحب%')->orWhereIn('leave_type', ['withdrawal', 'سحب']))->get();
            $legacyWithdrawals = $legacyWithdrawalsReqs->sum(function ($req) { preg_match('/بقيمة\s*([\d\.]+)/', $req->reason, $m); return isset($m[1]) ? (float)$m[1] : 0; });
        }

        $vouchers          = \App\Models\Voucher::where('employee_id', $employee->id)->where('status', 'approved')->get();
        $voucherLoans      = $vouchers->where('type', 'loan')->sum('amount');
        $voucherDeductions = $vouchers->where('type', 'deduction')->sum('amount');
        $voucherRewards    = $vouchers->where('type', 'reward')->sum('amount');

        $totalLoanAmount       = $legacyLoans + $voucherLoans;
        $totalWithdrawalAmount = $legacyWithdrawals + $voucherDeductions;

        if ($employee->relationLoaded('entitlements')) {
            $entitlementDeductions = $employee->entitlements->where('type', 'deduction')->sum('amount');
            $entitlementIncentives = $employee->entitlements->where('type', 'incentive')->sum('amount');
        } else {
            $entitlementDeductions = \App\Models\Entitlement::where('employee_id', $employee->id)->where('type', 'deduction')->sum('amount');
            $entitlementIncentives = \App\Models\Entitlement::where('employee_id', $employee->id)->where('type', 'incentive')->sum('amount');
        }

        $totalIncentives = $entitlementIncentives + $voucherRewards;
        $grossSalary     = $employee->base_salary + $totalIncentives;
        $netSalary       = $grossSalary - $autoAbsentDeduction - $totalLoanAmount - $totalWithdrawalAmount - $entitlementDeductions;

        return [
            'gross'                  => $grossSalary,
            'absent_deduction'       => $autoAbsentDeduction,
            'loan_deduction'         => $totalLoanAmount,
            'withdrawal_deduction'   => $totalWithdrawalAmount,
            'entitlement_deductions' => $entitlementDeductions,
            'entitlement_incentives' => $totalIncentives,
            'net'                    => $netSalary,
            'absent_days'            => $absentDays,
        ];
    }

    public function closeMonth(Request $request, Employee $employee)
    {
        $periodMonth = $request->input('month', now()->month);
        $periodYear  = $request->input('year', now()->year);

        // ── التحقق: هل إجمالي الخصومات يساوي أو يتجاوز الراتب الأساسي؟ ──
        $baseSalary = (float) $employee->base_salary;
        $expected = self::calculateExpectedSalary($employee, $periodMonth, $periodYear);
        $totalDeductions = ($expected['absent_deduction'] ?? 0)
                         + ($expected['loan_deduction'] ?? 0)
                         + ($expected['withdrawal_deduction'] ?? 0)
                         + ($expected['entitlement_deductions'] ?? 0);

        if ($totalDeductions < $baseSalary) {
            $remaining = number_format($baseSalary - $totalDeductions, 2);
            return redirect()->route('payroll.index', ['month' => $periodMonth, 'year' => $periodYear])
                ->with('error', "لا يمكن إرسال التوقيع! إجمالي الخصومات ({$totalDeductions}) أقل من الراتب الأساسي ({$baseSalary}). المتبقي: {$remaining} ر.س. يجب أن تتساوى الخصومات مع الراتب الأساسي أولاً.");
        }
        // ────────────────────────────────────────────────────────────────

        $payroll = Payroll::where('employee_id', $employee->id)
            ->where('pay_period_month', $periodMonth)
            ->where('pay_period_year', $periodYear)
            ->first();

        if (!$payroll) {
            $payroll = Payroll::create([
                'employee_id'         => $employee->id,
                'pay_period_month'    => $periodMonth,
                'pay_period_year'     => $periodYear,
                'basic_salary'        => $employee->base_salary,
                'housing_allowance'   => 0,
                'transport_allowance' => 0,
                'other_allowances'    => $expected['entitlement_incentives'] ?? 0,
                'gross_salary'        => $expected['gross'],
                'social_insurance'    => 0,
                'tax_deduction'       => 0,
                'absent_deduction'    => $expected['absent_deduction'],
                'withdrawal_deduction'=> $expected['withdrawal_deduction'] ?? 0,
                'other_deductions'    => $expected['loan_deduction'] + ($expected['entitlement_deductions'] ?? 0),
                'net_salary'          => $expected['net'],
                'working_days'        => 30,
                'absent_days'         => $expected['absent_days'],
                'status'              => 'approved',
                'approved_at'         => now(),
                'approved_by'         => auth()->id(),
            ]);

            self::syncPayrollToFirebase($payroll);
        } elseif ($payroll->status === 'draft') {
            $payroll->update([
                'status'      => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);
            self::syncPayrollToFirebase($payroll);
        }

        // إشعار الموظف عبر Firebase RTDB
        self::notifyEmployeeForSignature($employee, $payroll, $periodMonth, $periodYear);

        return redirect()->route('payroll.index', ['month' => $periodMonth, 'year' => $periodYear])
            ->with('success', 'تم اعتماد الراتب وإرسال إشعار للموظف للتوقيع وبصمة الوجه. ⏳');
    }

    /**
     * إرسال إشعار عبر Firebase RTDB للموظف ليفتح التطبيق ويوقع
     */
    protected static function notifyEmployeeForSignature(\App\Models\Employee $employee, Payroll $payroll, $month, $year): void
    {
        if (!$employee->user_id) return;

        $monthNames = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];
        $monthName = $monthNames[(int)$month] ?? $month;
        $title     = "راتب شهر {$monthName} {$year} جاهز للاستلام";
        $body      = 'تم اعتماد راتبك. يرجى فتح عقد الاستلام والتوقيع وبصمة الوجه لإتمام الصرف.';

        try {
            $firebase = app(FirebaseService::class);
            $firebase->sendNotification($employee->user_id, $title, $body, 'salary', $payroll->id);
        } catch (\Throwable $e) {
            Log::warning('Could not send salary notification via Firebase: ' . $e->getMessage());
        }
    }

    public function printSlip(Payroll $payroll)
    {
        $payroll->load('employee.department');
        return view('hr.payroll.print_slip', compact('payroll'));
    }

    public function process(Request $request)
    {
        $periodMonth = $request->input('month', now()->month);
        $periodYear  = $request->input('year', now()->year);

        $employees = Employee::where('is_active', true)->whereDoesntHave('payrolls', fn($q) => $q->where('pay_period_month', $periodMonth)->where('pay_period_year', $periodYear)->where('status', 'paid'))->get();

        $processed = 0;
        foreach ($employees as $employee) {
            $expected = self::calculateExpectedSalary($employee, $periodMonth, $periodYear);

            $payroll = Payroll::create([
                'employee_id'          => $employee->id,
                'pay_period_month'     => $periodMonth,
                'pay_period_year'      => $periodYear,
                'basic_salary'         => $employee->base_salary,
                'housing_allowance'    => 0,
                'transport_allowance'  => 0,
                'other_allowances'     => $expected['entitlement_incentives'] ?? 0,
                'gross_salary'         => $expected['gross'],
                'social_insurance'     => 0,
                'tax_deduction'        => 0,
                'absent_deduction'     => $expected['absent_deduction'],
                'withdrawal_deduction' => $expected['withdrawal_deduction'] ?? 0,
                'other_deductions'     => $expected['loan_deduction'] + ($expected['entitlement_deductions'] ?? 0),
                'net_salary'           => $expected['net'],
                'working_days'         => 30,
                'absent_days'          => $expected['absent_days'],
                'status'               => 'approved',
                'approved_at'          => now(),
                'approved_by'          => auth()->id(),
            ]);

            self::syncPayrollToFirebase($payroll);
            $processed++;
        }

        return redirect()->back()->with('success', "تم اعتماد رواتب $processed موظف بنجاح. بانتظار توقيع الموظفين وبصمة الوجه.");
    }

    public function processCustom(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'month'       => 'required|integer',
            'year'        => 'required|integer',
        ]);

        if ($request->input('employee_id') === 'all') {
            return $this->process($request);
        } else {
            $employee = Employee::findOrFail($request->input('employee_id'));
            return $this->closeMonth($request, $employee);
        }
    }

    public function markAsPaid(Payroll $payroll)
    {
        $payroll->update(['status' => 'paid', 'paid_at' => now()]);
        self::syncPayrollToFirebase($payroll);
        self::zeroOutDeductions($payroll->employee_id);

        return redirect()->back()->with('success', 'تم صرف الراتب وتغيير حالته إلى مدفوع بنجاح.');
    }

    public function confirmPayment(Payroll $payroll)
    {
        if (empty($payroll->employee_signature_url)) {
            return redirect()->back()->with('error', 'لا يمكن تأكيد الدفع: الموظف لم يوقع بعد على عقد الاستلام.');
        }

        if (!$payroll->is_face_id_verified) {
            return redirect()->back()->with('error', 'لا يمكن تأكيد الدفع: لم يتم التحقق من بصمة وجه الموظف بعد.');
        }

        $payroll->update([
            'status'                      => 'paid',
            'paid_at'                     => now(),
            'manager_receipt_approved_at' => now(),
        ]);

        self::syncPayrollToFirebase($payroll);
        self::zeroOutDeductions($payroll->employee_id);

        return redirect()->back()->with('success', '✅ تم تأكيد صرف الراتب بنجاح بعد التحقق من التوقيع وبصمة الوجه.');
    }

    protected static function zeroOutDeductions($employeeId): void
    {
        \App\Models\Voucher::where('employee_id', $employeeId)->where('status', 'approved')->update(['status' => 'paid', 'updated_at' => now()]);
        \App\Models\LeaveRequest::where('employee_id', $employeeId)->where('status', 'approved')
            ->where(fn($q) => $q->where('leave_type', 'loan')->orWhere('leave_type', 'LIKE', '%سحب%')->orWhereIn('leave_type', ['withdrawal', 'سحب']))
            ->update(['status' => 'paid', 'updated_at' => now()]);
    }

    /**
     * مزامنة الراتب إلى Firebase RTDB
     */
    protected static function syncPayrollToFirebase(Payroll $payroll): void
    {
        try {
            $firebase = app(FirebaseService::class);
            $firebase->syncPayroll($payroll);
        } catch (\Throwable $e) {
            Log::error('Firebase Payroll Sync Error: ' . $e->getMessage());
        }
    }

    /**
     * جلب التوقيعات وبصمة الوجه من Firebase RTDB وتحديثها في MySQL
     */
    protected static function syncPayrollsFromFirebase(): void
    {
        try {
            $firebase = app(FirebaseService::class);
            $allPayrolls = $firebase->getData('payroll');

            if (!$allPayrolls) return;

            foreach ($allPayrolls as $id => $sp) {
                if (($sp['status'] ?? '') !== 'approved') continue;
                if (empty($sp['employee_signature_url'])) continue;

                $mysqlPayroll = Payroll::find($id);
                if ($mysqlPayroll && empty($mysqlPayroll->employee_signature_url)) {
                    $mysqlPayroll->update([
                        'employee_signature_url' => $sp['employee_signature_url'],
                        'is_face_id_verified'    => !empty($sp['is_face_id_verified']) ? 1 : 0,
                        'employee_received_at'   => !empty($sp['employee_received_at'])
                            ? date('Y-m-d H:i:s', strtotime($sp['employee_received_at']))
                            : null,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Firebase → MySQL Payroll Sync Error: ' . $e->getMessage());
        }
    }
}

