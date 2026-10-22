<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    /** GET /vouchers */
    public function index(Request $request)
    {
        $query = Voucher::with(['employee:id,full_name,full_name_ar,employee_code'])
            ->orderBy('date', 'desc');

        if ($request->filled('employee_id') && $request->employee_id !== 'all') {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $vouchers    = $query->paginate(25)->withQueryString();
        $employees   = Employee::active()->orderBy('full_name_ar')->get(['id', 'full_name', 'full_name_ar', 'employee_code']);
        $typeTotals  = $this->getTypeTotals($request);

        return view('vouchers.index', compact('vouchers', 'employees', 'typeTotals'));
    }

    /** POST /vouchers */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|uuid|exists:employees,id',
            'amount'      => 'required|numeric|min:0.01',
            'type'        => 'required|in:reward,loan,deduction,other',
            'date'        => 'required|date',
            'reason'      => 'nullable|string|max:500',
        ]);

        Voucher::create([
            ...$validated,
            'status'     => 'approved',
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم إضافة السند بنجاح');
    }

    /** DELETE /vouchers/{voucher} */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'تم حذف السند');
    }

    private function getTypeTotals(Request $request): array
    {
        $query = Voucher::query();
        if ($request->filled('employee_id') && $request->employee_id !== 'all') {
            $query->where('employee_id', $request->employee_id);
        }
        $all = $query->get();
        return [
            'reward'    => $all->where('type', 'reward')->sum('amount'),
            'loan'      => $all->where('type', 'loan')->sum('amount'),
            'deduction' => $all->where('type', 'deduction')->sum('amount'),
        ];
    }
}

