<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'leave');

        // Single query to get ALL requests with counts (instead of 8+ separate count queries)
        $allRequests = LeaveRequest::with('employee.department')->latest()->get();

        // Calculate counts from the collection (0 DB queries for counts)
        $pendingCount  = $allRequests->where('status', 'pending')->count();
        $approvedCount = $allRequests->where('status', 'approved')->count();
        $rejectedCount = $allRequests->where('status', 'rejected')->count();

        // Type filtering helper
        $isLeave = fn($r) => in_array(strtolower($r->leave_type ?? ''), ['annual', 'sick', 'emergency', 'unpaid', 'leave']) || str_contains(strtolower($r->leave_type ?? ''), 'إجازة');
        $isLoan = fn($r) => in_array(strtolower($r->leave_type ?? ''), ['advance', 'loan', 'قرض']) || str_contains(strtolower($r->leave_type ?? ''), 'قرض');
        $isWithdrawal = fn($r) => in_array(strtolower($r->leave_type ?? ''), ['withdrawal', 'سحب']) || str_contains(strtolower($r->leave_type ?? ''), 'سحب');
        $isResignation = fn($r) => in_array(strtolower($r->leave_type ?? ''), ['resignation', 'استقالة']) || str_contains(strtolower($r->leave_type ?? ''), 'استقالة');

        $leavesCount       = $allRequests->filter($isLeave)->count();
        $loansCount        = $allRequests->filter($isLoan)->count();
        $withdrawalsCount  = $allRequests->filter($isWithdrawal)->count();
        $resignationsCount = $allRequests->filter($isResignation)->count();

        // Filter for current tab
        $requests = match($type) {
            'loan'        => $allRequests->filter($isLoan)->values(),
            'withdrawal'  => $allRequests->filter($isWithdrawal)->values(),
            'resignation' => $allRequests->filter($isResignation)->values(),
            default       => $allRequests->filter($isLeave)->values(),
        };

        // Selected request
        $selectedId = $request->get('selected');
        $selectedRequest = null;
        if ($selectedId) {
            $selectedRequest = $allRequests->firstWhere('id', $selectedId);
        }
        if (!$selectedRequest) {
            $selectedRequest = $allRequests->firstWhere('status', 'pending') ?? $allRequests->first();
        }

        $employees = \Illuminate\Support\Facades\Cache::remember('active_employees_list_requests', 60, fn() => Employee::select('id', 'full_name_ar')->where('is_active', true)->orderBy('full_name_ar')->get());

        return view('hr.requests.index', compact(
            'requests',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'leavesCount',
            'loansCount',
            'withdrawalsCount',
            'resignationsCount',
            'type',
            'selectedRequest',
            'employees'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'nullable|string|max:1000',
        ]);

        LeaveRequest::create([
            'employee_id' => $request->employee_id,
            'leave_type'  => $request->leave_type,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => $request->reason ?? 'أضيفت من قِبل الإدارة',
            'status'      => 'approved',
        ]);

        return back()->with('success', 'تم إضافة الإجازة بنجاح وتم اعتمادها تلقائياً.');
    }

    public function updateStatus(Request $request, LeaveRequest $leaveRequest)
    {
        $status = $request->input('status');
        if (in_array($status, ['approved', 'rejected'])) {
            $leaveRequest->status = $status;
            $leaveRequest->save();

            \Illuminate\Support\Facades\Cache::forget('header_pending_requests');
            \Illuminate\Support\Facades\Cache::forget('header_pending_count');

            // Create notification for the employee
            $employee = $leaveRequest->employee;
            if ($employee && $employee->user_id) {
                $statusAr = $status === 'approved' ? 'مقبول' : 'مرفوض';
                $title = "تم تحديث حالة طلبك";
                $body = "تم تحديث حالة طلب الإجازة/السلفة الخاص بك إلى: $statusAr";

                \App\Models\Notification::create([
                    'user_id' => $employee->user_id,
                    'title' => $title,
                    'title_ar' => $title,
                    'body' => $body,
                    'body_ar' => $body,
                    'type' => 'leave',
                    'is_read' => false,
                    'related_id' => $leaveRequest->id,
                    'related_table' => 'leave_requests',
                ]);
            }

            return back()->with('success', 'تم تحديث حالة الطلب بنجاح وتم إرسال إشعار للموظف.');
        }
        return back()->with('error', 'حالة غير صالحة.');
    }

    public function printVoucher(LeaveRequest $leaveRequest)
    {
        return view('hr.requests.voucher', compact('leaveRequest'));
    }
}

