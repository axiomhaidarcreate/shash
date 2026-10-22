<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HRDashboardController extends Controller
{
    public function index()
    {
        $tenantId = 1;
        $totalEmployees = Cache::remember("dashboard_total_employees_{$tenantId}", 300, function () {
            return Employee::count();
        });
        
        $today = now()->toDateString();
        
        // Single query for today's attendance
        $todayAttendances = Attendance::where('work_date', $today)->get(['status', 'check_in']);
        $todayAttendance = $todayAttendances->count();
        $todayAbsence = max(0, $totalEmployees - $todayAttendance);

        // Calculate delay from collection (no extra DB call)
        $delayMinutes = 0;
        $officialStart = \Carbon\Carbon::parse('08:00:00');
        foreach($todayAttendances as $att) {
            if ($att->status === 'late' && $att->check_in) {
                $checkInTime = \Carbon\Carbon::parse($att->check_in);
                if ($checkInTime->greaterThan($officialStart)) {
                    $delayMinutes += $checkInTime->diffInMinutes($officialStart);
                }
            }
        }
        $delayHours = (int) round($delayMinutes / 60);

        // Single query for payroll (instead of 2 separate queries)
        $monthlyPayrolls = Cache::remember("dashboard_payroll_{$tenantId}_" . now()->format('Y-m'), 300, function () {
            return Payroll::where('pay_period_month', now()->month)
                          ->where('pay_period_year', now()->year)
                          ->get(['net_salary', 'social_insurance', 'tax_deduction', 'absent_deduction', 'other_deductions']);
        });

        $totalPayroll = $monthlyPayrolls->sum('net_salary');
        $totalDeductions = $monthlyPayrolls->sum(function ($p) {
            return $p->social_insurance + $p->tax_deduction + $p->absent_deduction + $p->other_deductions;
        });

        // Notifications
        $recentNotifications = \App\Models\Notification::orderBy('created_at', 'desc')->take(5)->get();

        // Salary Distribution
        $salaryDistribution = [];
        $activeEmployees = Employee::with('department')->where('is_active', true)->get();
        $totalBaseSalary = $activeEmployees->sum('base_salary');
        
        if ($totalBaseSalary > 0) {
            $deptSalaries = $activeEmployees->groupBy('department_id');
            foreach ($deptSalaries as $deptId => $emps) {
                $deptName = $emps->first()->department ? $emps->first()->department->name_ar : 'بدون قسم';
                $deptTotal = $emps->sum('base_salary');
                $percentage = round(($deptTotal / $totalBaseSalary) * 100);
                
                if (!isset($salaryDistribution[$deptName])) {
                    $salaryDistribution[$deptName] = 0;
                }
                $salaryDistribution[$deptName] += $percentage;
            }
        }
        arsort($salaryDistribution);
        $salaryDistribution = array_slice($salaryDistribution, 0, 4);

        $totalAbsentDeductions = $monthlyPayrolls->sum('absent_deduction');
        $totalDelayDeductions = $monthlyPayrolls->sum('other_deductions');

        return view('hr.dashboard', compact(
            'totalEmployees',
            'todayAttendance',
            'todayAbsence',
            'delayHours',
            'totalPayroll',
            'totalDeductions',
            'totalAbsentDeductions',
            'totalDelayDeductions',
            'recentNotifications',
            'salaryDistribution'
        ));
    }
}

