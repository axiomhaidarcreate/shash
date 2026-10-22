<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Entitlement;
use App\Models\Employee;
use App\Models\Setting;
use Illuminate\Http\Request;

class EntitlementController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::active()->get();
        
        $query = Entitlement::with('employee')->latest('date')->latest('time');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->has('print')) {
            $entitlements = $query->get();
            return view('hr.entitlements.print', compact('entitlements'));
        }

        $typeFilter = $request->get('type_filter', 'all');
        $isUnified = false;

        $deductionTypesArray = json_decode(Setting::get('deduction_types'), true);
        $deductionTypes = is_array($deductionTypesArray) && !empty($deductionTypesArray) ? $deductionTypesArray : ["غياب","تأخير","سلفة","سحب","أخرى"];

        $incentiveTypesArray = json_decode(Setting::get('incentive_types'), true);
        $incentiveTypes = is_array($incentiveTypesArray) && !empty($incentiveTypesArray) ? $incentiveTypesArray : ["مكافأة أداء","عمل إضافي","بدل","أخرى"];

        $salaryCounters = null;
        if ($request->filled('employee_id')) {
            $empId = $request->employee_id;
            
            $withdrawals = \App\Models\LeaveRequest::where('employee_id', $empId)
                ->where('status', 'approved')
                ->where(function($q) {
                    $q->where('leave_type', 'withdrawal')
                      ->orWhere('leave_type', 'like', '%سحب%');
                })->get()->map->amount->sum();
                
            $vouchersWithdrawals = \App\Models\Voucher::where('employee_id', $empId)
                ->whereIn('type', ['deduction', 'withdrawal', 'سحب'])
                ->where('status', 'approved')
                ->sum('amount');
                
            $totalWithdrawals = $withdrawals + $vouchersWithdrawals;

            $settlements = \App\Models\Voucher::where('employee_id', $empId)
                ->where('type', 'withdrawal_offset')
                ->sum('amount'); // negative
            $totalWithdrawals += $settlements;

            $loans = \App\Models\LeaveRequest::where('employee_id', $empId)
                ->where('status', 'approved')
                ->whereIn('leave_type', ['loan', 'advance', 'قرض'])
                ->get()->map->amount->sum();
                
            $vouchersLoans = \App\Models\Voucher::where('employee_id', $empId)
                ->whereIn('type', ['loan', 'advance'])
                ->where('status', 'approved')
                ->sum('amount');
                
            $totalLoans = $loans + $vouchersLoans;

            $totalAbsenceDeductions = \App\Models\Attendance::where('employee_id', $empId)
                ->where('status', 'absent')
                ->where('is_deducted', false)
                ->sum('deduction_amount');

            $totalLatenessDeductions = \App\Models\Attendance::where('employee_id', $empId)
                ->where('delay_minutes', '>', 0)
                ->where('is_deducted', false)
                ->sum('deduction_amount');

            $allEntitlements = Entitlement::where('employee_id', $empId)
                ->where('type', 'deduction')
                ->get();

            $entitlementLoans = $allEntitlements->filter(function ($e) {
                $title = mb_strtolower($e->title ?? '');
                return str_contains($title, 'سلفة') || str_contains($title, 'سلف') ||
                       str_contains($title, 'قرض')  || str_contains($title, 'loan') ||
                       str_contains($title, 'advance');
            })->sum('amount');

            $entitlementLateness = $allEntitlements->filter(function ($e) {
                $title = mb_strtolower($e->title ?? '');
                return str_contains($title, 'تأخير') || str_contains($title, 'تأخر') ||
                       str_contains($title, 'late')  || str_contains($title, 'lateness');
            })->sum('amount');

            $entitlementAbsence = $allEntitlements->filter(function ($e) {
                $title = mb_strtolower($e->title ?? '');
                return str_contains($title, 'غياب') || str_contains($title, 'absence');
            })->sum('amount');

            $entitlementOther = $allEntitlements->filter(function ($e) {
                $title = mb_strtolower($e->title ?? '');
                $isLoan = str_contains($title, 'سلفة') || str_contains($title, 'سلف') || str_contains($title, 'قرض') || str_contains($title, 'loan') || str_contains($title, 'advance');
                $isLate = str_contains($title, 'تأخير') || str_contains($title, 'تأخر') || str_contains($title, 'late') || str_contains($title, 'lateness');
                $isAbsence = str_contains($title, 'غياب') || str_contains($title, 'absence');
                return !$isLoan && !$isLate && !$isAbsence;
            })->sum('amount');

            $totalLoans              += (float) $entitlementLoans;
            $totalLatenessDeductions += (float) $entitlementLateness;
            $totalAbsenceDeductions  += (float) $entitlementAbsence;
            $totalWithdrawals        += (float) $entitlementOther;

            $entitlementIncentives = Entitlement::where('employee_id', $empId)
                ->where('type', 'incentive')
                ->sum('amount');
            $totalWithdrawals -= (float) $entitlementIncentives;

            $salaryCounters = [
                'withdrawals'        => round(max(0, $totalWithdrawals), 2),
                'loans'              => round(max(0, $totalLoans), 2),
                'absences_deduction' => round($totalAbsenceDeductions, 2),
                'lateness_deduction' => round($totalLatenessDeductions, 2),
            ];

            // ─── UNIFIED LIST FOR TABLE ───
            $isUnified = true;
            $unifiedList = collect();

            // 1. Entitlements
            foreach ($query->get() as $e) {
                $isLoan = false; $isLate = false; $isAbsence = false;
                if ($e->type == 'deduction') {
                    $title = mb_strtolower($e->title ?? '');
                    $isLoan = str_contains($title, 'سلفة') || str_contains($title, 'سلف') || str_contains($title, 'قرض') || str_contains($title, 'loan') || str_contains($title, 'advance');
                    $isLate = str_contains($title, 'تأخير') || str_contains($title, 'تأخر') || str_contains($title, 'late') || str_contains($title, 'lateness');
                    $isAbsence = str_contains($title, 'غياب') || str_contains($title, 'absence');
                }
                $unifiedType = 'entitlement';
                if ($e->type == 'deduction') {
                    if ($isLoan) $unifiedType = 'loans';
                    elseif ($isLate) $unifiedType = 'lateness';
                    elseif ($isAbsence) $unifiedType = 'absences';
                    else $unifiedType = 'withdrawals';
                }
                if ($typeFilter != 'all' && $unifiedType != $typeFilter && !($typeFilter == 'entitlement' && $e->type == 'incentive')) continue;

                $unifiedList->push((object)[
                    'id' => $e->id,
                    'original_model' => $e,
                    'employee' => $e->employee,
                    'type_badge' => $e->type == 'incentive' ? 'حافز مباشر' : 'خصم مباشر',
                    'title' => $e->title,
                    'reason' => $e->reason,
                    'date' => $e->date,
                    'time' => $e->time,
                    'amount' => $e->amount,
                    'is_editable' => true,
                    'sort_date' => $e->date->format('Y-m-d') . ' ' . ($e->time ? $e->time->format('H:i:s') : '00:00:00'),
                ]);
            }

            // 2. Vouchers
            $vouchers = \App\Models\Voucher::with('employee')->where('employee_id', $empId)
                ->whereIn('type', ['deduction', 'withdrawal', 'سحب', 'loan', 'advance'])
                ->where('status', 'approved');
            if ($request->filled('from_date')) $vouchers->whereDate('date', '>=', $request->from_date);
            if ($request->filled('to_date')) $vouchers->whereDate('date', '<=', $request->to_date);
            
            foreach ($vouchers->get() as $v) {
                $isLoan = in_array($v->type, ['loan', 'advance']);
                $unifiedType = $isLoan ? 'loans' : 'withdrawals';
                if ($typeFilter != 'all' && $unifiedType != $typeFilter) continue;

                $unifiedList->push((object)[
                    'id' => $v->id,
                    'original_model' => $v,
                    'employee' => $v->employee,
                    'type_badge' => 'سند مالي',
                    'title' => $v->typeLabelAr(),
                    'reason' => $v->reason,
                    'date' => $v->date,
                    'time' => null,
                    'amount' => $v->amount,
                    'is_editable' => false,
                    'sort_date' => $v->date->format('Y-m-d') . ' ' . $v->created_at->format('H:i:s'),
                ]);
            }

            // 3. Leave Requests
            $lrs = \App\Models\LeaveRequest::with('employee')->where('employee_id', $empId)
                ->where('status', 'approved')
                ->where(function($q) {
                    $q->whereIn('leave_type', ['loan', 'advance', 'قرض', 'withdrawal'])
                      ->orWhere('leave_type', 'like', '%سحب%');
                });
            if ($request->filled('from_date')) $lrs->whereDate('created_at', '>=', $request->from_date);
            if ($request->filled('to_date')) $lrs->whereDate('created_at', '<=', $request->to_date);
            
            foreach ($lrs->get() as $lr) {
                $isLoan = in_array($lr->leave_type, ['loan', 'advance', 'قرض']);
                $unifiedType = $isLoan ? 'loans' : 'withdrawals';
                if ($typeFilter != 'all' && $unifiedType != $typeFilter) continue;

                $unifiedList->push((object)[
                    'id' => $lr->id,
                    'original_model' => $lr,
                    'employee' => $lr->employee,
                    'type_badge' => 'طلب موظف',
                    'title' => $lr->leave_type,
                    'reason' => $lr->reason,
                    'date' => \Carbon\Carbon::parse($lr->created_at),
                    'time' => \Carbon\Carbon::parse($lr->created_at),
                    'amount' => $lr->amount,
                    'is_editable' => false,
                    'sort_date' => $lr->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            // 4. Attendance
            $atts = \App\Models\Attendance::with('employee')->where('employee_id', $empId)
                ->where(function($q) {
                    $q->where(function($sq) {
                        $sq->where('status', 'absent')->where('is_deducted', false);
                    })->orWhere(function($sq) {
                        $sq->where('delay_minutes', '>', 0)->where('is_deducted', false)->where('deduction_amount', '>', 0);
                    });
                });
            if ($request->filled('from_date')) $atts->whereDate('work_date', '>=', $request->from_date);
            if ($request->filled('to_date')) $atts->whereDate('work_date', '<=', $request->to_date);
                
            foreach ($atts->get() as $att) {
                $isAbsent = $att->status == 'absent';
                $checkType = $isAbsent ? 'absences' : 'lateness';
                if ($typeFilter != 'all' && $checkType != $typeFilter) continue;

                $unifiedList->push((object)[
                    'id' => $att->id,
                    'original_model' => $att,
                    'employee' => $att->employee,
                    'type_badge' => 'سجل حضور',
                    'title' => $isAbsent ? 'غياب' : 'تأخير',
                    'reason' => $isAbsent ? 'غياب يوم كامل' : "تأخير ({$att->delay_minutes} دقيقة)",
                    'date' => \Carbon\Carbon::parse($att->work_date),
                    'time' => null,
                    'amount' => $att->deduction_amount,
                    'is_editable' => false,
                    'sort_date' => \Carbon\Carbon::parse($att->work_date)->format('Y-m-d') . ' 00:00:00',
                ]);
            }

            $unifiedList = $unifiedList->sortByDesc('sort_date')->values();
            $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
            $perPage = 15;
            $entitlements = new \Illuminate\Pagination\LengthAwarePaginator(
                $unifiedList->forPage($page, $perPage),
                $unifiedList->count(),
                $perPage,
                $page,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => request()->query()]
            );

        } else {
            $entitlements = $query->paginate(15)->withQueryString();
        }

        return view('hr.entitlements.index', compact('entitlements', 'employees', 'deductionTypes', 'incentiveTypes', 'salaryCounters', 'isUnified'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type'        => 'required|in:deduction,incentive',
            'title'       => 'required|string|max:255',
            'reason'      => 'nullable|string',
            'date'        => 'required|date',
            'time'        => 'nullable|date_format:H:i',
            'amount'      => 'required|numeric|min:0',
            'receipt_number' => 'nullable|string|max:255',
            'attachment'     => 'nullable|file|max:5120',
        ]);

        $time = $request->input('time');
        if (!$time) {
            $time = now()->format('H:i');
        }

        $data = [
            'employee_id'    => $request->employee_id,
            'type'           => $request->type,
            'title'          => $request->title,
            'reason'         => $request->reason,
            'date'           => $request->date,
            'time'           => $time,
            'amount'         => $request->amount,
            'receipt_number' => $request->receipt_number,
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = \App\Services\SupabaseStorageService::upload('attachments', $request->file('attachment'), 'entitlements');
        }

        Entitlement::create($data);

        $message = $request->type == 'deduction' ? 'تم إضافة الخصم بنجاح.' : 'تم إضافة الحافز بنجاح.';
        return back()->with('success', $message);
    }

    public function update(Request $request, Entitlement $entitlement)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'reason'         => 'nullable|string',
            'date'           => 'required|date',
            'time'           => 'nullable|date_format:H:i',
            'amount'         => 'required|numeric|min:0',
            'receipt_number' => 'nullable|string|max:255',
            'attachment'     => 'nullable|file|max:5120',
        ]);

        $data = $request->only('title', 'reason', 'date', 'amount', 'time', 'receipt_number');

        if ($request->hasFile('attachment')) {
            $data['attachment'] = \App\Services\SupabaseStorageService::upload('attachments', $request->file('attachment'), 'entitlements');
        }

        $entitlement->update($data);

        return back()->with('success', 'تم التعديل بنجاح.');
    }

    public function destroy(Entitlement $entitlement)
    {
        $entitlement->delete();
        return back()->with('success', 'تم الحذف بنجاح.');
    }
}

