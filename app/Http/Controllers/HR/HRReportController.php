<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HRReportController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $inactiveEmployees = Employee::where('is_active', false)->count();
        $turnoverRate = $totalEmployees > 0 ? round(($inactiveEmployees / $totalEmployees) * 100, 1) : 0;
        $averageSalary = Employee::where('is_active', true)->avg('base_salary') ?? 0;
        $totalAttendances = Attendance::count();
        $presentAttendances = Attendance::whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100, 1) : 98.2;

        $salaryDistRaw = Employee::whereNotNull('department_id')
            ->where('is_active', true)
            ->selectRaw('department_id, SUM(base_salary) as total')
            ->groupBy('department_id')->with('department')->get();
        $salaryDistColors = ['#115e59', '#6366f1', '#fb923c', '#eab308', '#ec4899', '#8b5cf6'];
        $salaryDistList = $salaryDistRaw->map(function($item, $idx) use ($salaryDistColors) {
            return ['name' => $item->department->name_ar ?? 'غير محدد', 'amount' => $item->total, 'color' => $salaryDistColors[$idx % count($salaryDistColors)]];
        });
        $salaryDistLabels = $salaryDistRaw->map(fn($i) => $i->department->name_ar ?? 'غير محدد');
        $salaryDistValues = $salaryDistRaw->map(fn($i) => $i->total);

        // OPTIMIZED: Single query for 7-day attendance instead of 14 separate queries
        $weekStart = now()->subDays(6)->format('Y-m-d');
        $weekAttendance = Attendance::where('work_date', '>=', $weekStart)
            ->selectRaw("work_date, status, COUNT(*) as cnt")
            ->groupBy('work_date', 'status')
            ->get()
            ->groupBy('work_date');

        $attendanceDays = $attendancePresents = $attendanceAbsents = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $attendanceDays[] = now()->subDays($i)->locale('ar')->dayName;
            $dayData = $weekAttendance->get($date, collect());
            $attendancePresents[] = $dayData->whereIn('status', ['present', 'late'])->sum('cnt');
            $attendanceAbsents[] = $dayData->where('status', 'absent')->sum('cnt');
        }

        // OPTIMIZED: Single query for 12-month growth instead of 12 separate queries
        $growthLabels = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
        $cumSum = Employee::whereYear('created_at', '<', now()->year)->count();
        $monthCounts = Employee::whereYear('created_at', now()->year)
            ->selectRaw("EXTRACT(MONTH FROM created_at) as m, COUNT(*) as cnt")
            ->groupBy('m')
            ->pluck('cnt', 'm');

        $growthData = [];
        for ($month = 1; $month <= 12; $month++) {
            $cumSum += ($monthCounts[$month] ?? 0);
            $growthData[] = $cumSum;
        }

        $allEmployees = Employee::with('department')->where('is_active', true)->orderBy('full_name_ar')->get();

        return view('hr.reports.index', compact(
            'totalEmployees','turnoverRate','averageSalary','attendanceRate',
            'salaryDistLabels','salaryDistValues','salaryDistColors','salaryDistList',
            'attendanceDays','attendancePresents','attendanceAbsents',
            'growthLabels','growthData','allEmployees'
        ));
    }

    // 1. Monthly Payroll Summary (paid & unpaid)
    public function payrollSummary(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $allActive = Employee::where('is_active', true)->with('department')->get();
        $paidIds   = Payroll::where('pay_period_month', $month)->where('pay_period_year', $year)->where('status','paid')->pluck('employee_id');

        $paidPayrolls = Payroll::where('pay_period_month', $month)->where('pay_period_year', $year)->where('status','paid')->with('employee.department')->get();
        $unpaidEmployees = $allActive->whereNotIn('id', $paidIds)->values();

        $totalPaid    = $paidPayrolls->sum('net_salary');
        $totalUnpaid  = $unpaidEmployees->sum('base_salary');
        $totalExpected = $allActive->sum('base_salary');

        return view('hr.reports.payroll_summary', compact('paidPayrolls','unpaidEmployees','totalPaid','totalUnpaid','totalExpected','month','year'));
    }

    // 2. Employees List
    public function employeesList(Request $request)
    {
        $dept = $request->get('department_id');
        $query = Employee::with('department')->orderBy('full_name_ar');
        if ($dept) $query->where('department_id', $dept);
        $employees = $query->get();
        $departments = Department::orderBy('name_ar')->get();
        return view('hr.reports.employees_list', compact('employees','departments','dept'));
    }

    // 3. Departments Report
    public function departments()
    {
        $departments = Department::withCount(['employees' => fn($q) => $q->where('is_active', true)])
            ->with(['employees' => fn($q) => $q->where('is_active', true)->select('id','department_id','base_salary')])
            ->get()
            ->map(function($dept) {
                $dept->total_salary = $dept->employees->sum('base_salary');
                return $dept;
            });
        return view('hr.reports.departments', compact('departments'));
    }

    // 4. Full Employee Profile
    public function employeeFull(Request $request, Employee $employee)
    {
        $employee->load('department');
        $month = $request->get('month', 'all');
        $year  = $request->get('year', now()->year);

        // Payrolls
        $payrollQuery = Payroll::where('employee_id', $employee->id)->whereYear('created_at', $year);
        if ($month !== 'all') {
            $payrollQuery->where('pay_period_month', $month);
        }
        $payrolls = $payrollQuery->orderBy('pay_period_year','desc')->orderBy('pay_period_month','desc')->get();
        
        // All Requests (loans & withdrawals)
        $allRequests = LeaveRequest::where('employee_id', $employee->id)->orderBy('created_at','desc')->get();
        
        $isLoan = fn($r) => in_array(strtolower($r->leave_type ?? ''), ['advance', 'loan', 'قرض']) || str_contains(strtolower($r->leave_type ?? ''), 'قرض');
        $isWithdrawal = fn($r) => in_array(strtolower($r->leave_type ?? ''), ['withdrawal', 'سحب']) || str_contains(strtolower($r->leave_type ?? ''), 'سحب');
        
        $loans = $allRequests->filter($isLoan)->values();
        $withdrawals = $allRequests->filter($isWithdrawal)->values();

        // Attendance
        $attendanceQuery = Attendance::where('employee_id', $employee->id)->whereYear('work_date', $year);
        if ($month !== 'all') {
            $attendanceQuery->whereMonth('work_date', $month);
        }
        $attendances = $attendanceQuery->orderBy('work_date','desc')->get();

        $attendanceSummary = [
            'present' => $attendances->where('status','present')->count(),
            'late'    => $attendances->where('status','late')->count(),
            'absent'  => $attendances->where('status','absent')->count(),
            'total'   => $attendances->count(),
        ];

        // Entitlements (استحقاقات)
        $entitlements = \App\Models\Entitlement::where('employee_id', $employee->id)->orderBy('date','desc')->get();

        // Tasks (المهام)
        $tasks = \App\Models\Task::where('employee_id', $employee->id)->orderBy('created_at','desc')->get();

        // Documents (الوثائق)
        $today = now();
        $docs = [];
        $fields = [
            'national_id_expiry'   => 'الإقامة / الهوية الوطنية',
            'passport_expiry'      => 'جواز السفر',
            'health_cert_expiry'   => 'الشهادة الصحية',
            'contract_expiry_date' => 'العقد',
            'insurance_expiry_date'=> 'التأمين',
        ];
        foreach ($fields as $field => $label) {
            if (!empty($employee->$field)) {
                $expiry  = Carbon::parse($employee->$field);
                $days    = $today->diffInDays($expiry, false);
                $status  = $days < 0 ? 'expired' : ($days <= 30 ? 'warning' : 'ok');
                $docs[]  = ['label' => $label, 'date' => $expiry->format('Y-m-d'), 'days' => (int)$days, 'status' => $status];
            }
        }

        return view('hr.reports.employee_full', compact(
            'employee','payrolls','loans','withdrawals','attendances','attendanceSummary',
            'entitlements','tasks','docs','month','year'
        ));
    }

    // 5. Employee Salary Report
    public function employeeSalary(Request $request, Employee $employee)
    {
        $employee->load('department');
        $payrolls = Payroll::where('employee_id', $employee->id)->orderBy('pay_period_year','desc')->orderBy('pay_period_month','desc')->get();
        return view('hr.reports.employee_salary', compact('employee','payrolls'));
    }

    // 6. Employee Withdrawals Report
    public function employeeWithdrawals(Request $request, Employee $employee)
    {
        $employee->load('department');
        $allRequests = LeaveRequest::where('employee_id', $employee->id)->orderBy('created_at','desc')->get();
        $isWithdrawal = fn($r) => in_array(strtolower($r->leave_type ?? ''), ['withdrawal', 'سحب']) || str_contains(strtolower($r->leave_type ?? ''), 'سحب');
        $withdrawals = $allRequests->filter($isWithdrawal)->values();
        
        return view('hr.reports.employee_withdrawals', compact('employee','withdrawals'));
    }

    // 7. All Months Payroll History
    public function payrollHistory(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $allEmployees = Employee::where('is_active', true)->orderBy('full_name_ar')->get();

        $query = Payroll::with('employee.department')->orderBy('pay_period_year','desc')->orderBy('pay_period_month','desc');
        if ($employeeId) $query->where('employee_id', $employeeId);
        $payrolls = $query->get();

        $grouped = $payrolls->groupBy(fn($p) => $p->pay_period_year . '-' . str_pad($p->pay_period_month, 2, '0', STR_PAD_LEFT));

        return view('hr.reports.payroll_history', compact('grouped','allEmployees','employeeId'));
    }

    // 8. Employee Absences Report
    public function employeeAbsences(Request $request, Employee $employee)
    {
        $employee->load('department');
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to',   now()->format('Y-m-d'));
        $absences = Attendance::where('employee_id', $employee->id)->where('status','absent')
            ->whereBetween('work_date', [$from, $to])->orderBy('work_date')->get();
        return view('hr.reports.employee_absences', compact('employee','absences','from','to'));
    }

    // 9. Employee Late Report
    public function employeeLate(Request $request, Employee $employee)
    {
        $employee->load('department');
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to',   now()->format('Y-m-d'));
        $lates = Attendance::where('employee_id', $employee->id)->where('status','late')
            ->whereBetween('work_date', [$from, $to])->orderBy('work_date')->get();
        return view('hr.reports.employee_late', compact('employee','lates','from','to'));
    }

    // 10. Monthly Attendance Report
    public function employeeAttendance(Request $request, Employee $employee)
    {
        $employee->load('department');
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $query = Attendance::where('employee_id', $employee->id)->whereYear('work_date', $year);
        
        if ($month !== 'all') {
            $query->whereMonth('work_date', $month);
        }

        $attendances = $query->orderBy('work_date')->get();

        $summary = [
            'present' => $attendances->where('status','present')->count(),
            'late'    => $attendances->where('status','late')->count(),
            'absent'  => $attendances->where('status','absent')->count(),
        ];
        return view('hr.reports.employee_attendance', compact('employee','attendances','summary','month','year'));
    }

    // 11. Documents Validity Report
    public function employeeDocuments(Request $request, Employee $employee)
    {
        $employee->load('department');
        $today = now();
        $docs = [];
        $fields = [
            'national_id_expiry'   => 'الإقامة / الهوية الوطنية',
            'passport_expiry'      => 'جواز السفر',
            'health_cert_expiry'   => 'الشهادة الصحية',
            'contract_expiry_date' => 'العقد',
            'insurance_expiry_date'=> 'التأمين',
        ];
        foreach ($fields as $field => $label) {
            if (!empty($employee->$field)) {
                $expiry  = Carbon::parse($employee->$field);
                $days    = $today->diffInDays($expiry, false);
                $status  = $days < 0 ? 'expired' : ($days <= 30 ? 'warning' : 'ok');
                $docs[]  = ['label' => $label, 'date' => $expiry->format('Y-m-d'), 'days' => (int)$days, 'status' => $status];
            }
        }
        return view('hr.reports.employee_documents', compact('employee','docs'));
    }
}

