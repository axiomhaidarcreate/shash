<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Set default filter to today if no dates are provided
        $dateFrom = $request->get('date_from', now()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $employeeId = $request->get('employee_id');

        // Main Query for attendances
        $query = Attendance::with('employee.department')
            ->whereBetween('work_date', [$dateFrom, $dateTo]);

        if ($employeeId && $employeeId !== 'all') {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->latest('work_date')->latest('created_at')->paginate(20)->withQueryString();

        // Calculate total active employees for the adherence rate base
        $totalEmployees = \Illuminate\Support\Facades\Cache::remember('employees_active_count', 60, fn() => Employee::where('is_active', true)->count());
        
        // Stats calculations
        // If it's a date range > 1 day, the "total expected presences" is (totalEmployees * days)
        // For simplicity and standard dashboard metrics, we usually calculate stats based on distinct employees present in the period, or averages.
        // But since the user wants specific adherence rate for the filtered period:
        
        $presentCount = Attendance::whereBetween('work_date', [$dateFrom, $dateTo])
            ->whereIn('status', ['present', 'late'])
            ->select('employee_id')
            ->distinct()
            ->count();
            
        // If a specific employee is selected over a date range, the "Late" card shows total delay minutes
        $lateStatsTitle = 'المتأخرون';
        $lateStatsValue = 0;
        $lateStatsUnit = 'موظفين';
        
        if ($employeeId && $employeeId !== 'all') {
            $lateStatsTitle = 'إجمالي التأخير';
            $lateStatsValue = Attendance::whereBetween('work_date', [$dateFrom, $dateTo])
                ->where('employee_id', $employeeId)
                ->sum('delay_minutes');
            $lateStatsUnit = 'دقيقة';
        } else {
            $lateStatsValue = Attendance::whereBetween('work_date', [$dateFrom, $dateTo])
                ->where('status', 'late')
                ->select('employee_id')
                ->distinct()
                ->count();
        }

        // Absent calculation: if 1 day, it's total_employees - present. If multiple days, it's an average or sum.
        // For standard HR, if single day:
        $daysDiff = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;
        $totalExpected = $totalEmployees * $daysDiff;
        
        $totalPresentRecords = Attendance::whereBetween('work_date', [$dateFrom, $dateTo])
            ->whereIn('status', ['present', 'late'])
            ->count();
            
        $absentCount = max(0, $totalExpected - $totalPresentRecords);
        $adherenceRate = $totalExpected > 0 ? round(($totalPresentRecords / $totalExpected) * 100, 1) : 0;

        // Employees list for the dropdown filter
        $employees = \Illuminate\Support\Facades\Cache::remember('active_employees_list_attendance', 60, fn() => Employee::select('id', 'full_name_ar')->where('is_active', true)->orderBy('full_name_ar')->get());

        // Total delay deductions for the period
        $totalDelayDeduction = Attendance::whereBetween('work_date', [$dateFrom, $dateTo])
            ->where('is_deducted', true)
            ->when($employeeId && $employeeId !== 'all', fn($q) => $q->where('employee_id', $employeeId))
            ->sum('deduction_amount');

        // Total delay minutes
        $totalDelayMinutes = Attendance::whereBetween('work_date', [$dateFrom, $dateTo])
            ->where('delay_minutes', '>', 0)
            ->when($employeeId && $employeeId !== 'all', fn($q) => $q->where('employee_id', $employeeId))
            ->sum('delay_minutes');

        return view('hr.attendance.index', compact(
            'attendances',
            'presentCount',
            'totalEmployees',
            'lateStatsTitle',
            'lateStatsValue',
            'lateStatsUnit',
            'absentCount',
            'adherenceRate',
            'dateFrom',
            'dateTo',
            'employeeId',
            'employees',
            'totalDelayDeduction',
            'totalDelayMinutes'
        ));
    }

    public function print(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $employeeId = $request->get('employee_id');

        $query = Attendance::with('employee.department')
            ->whereBetween('work_date', [$dateFrom, $dateTo]);

        if ($employeeId && $employeeId !== 'all') {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->latest('work_date')->latest('created_at')->get();

        return view('hr.attendance.print', compact('attendances', 'dateFrom', 'dateTo'));
    }

    public function convertToDeduction(Request $request, Attendance $attendance)
    {
        // Prevent double deduction
        if ($attendance->is_deducted || $attendance->delay_minutes <= 0) {
            return back()->with('error', 'هذا السجل ليس به تأخير أو تم خصمه مسبقاً.');
        }

        $employee = $attendance->employee;
        if (!$employee || !$employee->base_salary) {
            return back()->with('error', 'بيانات الموظف أو الراتب غير مكتملة لحساب الخصم.');
        }

        // Grace period from settings (default 15 minutes)
        $gracePeriod = (int) Setting::get('grace_period_minutes', 15);
        $chargeableMinutes = max(0, $attendance->delay_minutes - $gracePeriod);

        if ($chargeableMinutes <= 0) {
            return back()->with('info', 'تأخير الموظف ضمن فترة السماح المسموحة.');
        }

        // Work hours per day from settings (default 8)
        $workHoursPerDay = (int) Setting::get('work_hours_per_day', 8);

        // Calculate minute value: Base Salary / 30 days / work hours / 60 mins
        $minuteValue = $employee->base_salary / 30 / $workHoursPerDay / 60;
        $deductionAmount = round($chargeableMinutes * $minuteValue, 2);

        // Current month bounds
        $startOfMonth = Carbon::parse($attendance->work_date)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::parse($attendance->work_date)->endOfMonth()->toDateString();

        // Find or create payroll for the month
        $payroll = Payroll::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'period_start' => $startOfMonth,
                'period_end' => $endOfMonth,
            ],
            [
                'basic_salary' => $employee->base_salary,
                'housing_allowance' => $employee->housing_allowance ?? 0,
                'transportation_allowance' => 0, // add logic if needed
                'other_allowances' => 0,
                'tax_deduction' => 0,
                'absent_deduction' => 0,
                'other_deductions' => 0,
                'net_salary' => $employee->base_salary + ($employee->housing_allowance ?? 0),
                'status' => 'draft',
            ]
        );

        // Update Payroll Deduction
        $payroll->other_deductions += $deductionAmount;
        $payroll->net_salary -= $deductionAmount;
        $payroll->save();

        // Mark attendance as deducted
        $attendance->update([
            'is_deducted' => true,
            'deduction_amount' => $deductionAmount,
            'notes' => trim(($attendance->notes ?? '') . "\nتم خصم {$deductionAmount} ريال مقابل تأخير {$chargeableMinutes} دقيقة.")
        ]);

        return back()->with('success', "تم تحويل {$chargeableMinutes} دقيقة تأخير إلى خصم بقيمة {$deductionAmount} ريال بنجاح.");
    }
}


