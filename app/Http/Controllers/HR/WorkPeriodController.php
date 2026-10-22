<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\WorkPeriod;
use App\Models\Employee;
use Illuminate\Http\Request;

class WorkPeriodController extends Controller
{
    /** GET /work-periods */
    public function index()
    {
        $periods = WorkPeriod::withCount('employees')->orderBy('created_at', 'desc')->get();
        return view('work_periods.index', compact('periods'));
    }

    /** POST /work-periods */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar'          => 'required|string|max:100',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i',
            'grace_period_mins'=> 'required|integer|min:0|max:60',
        ]);

        WorkPeriod::create([
            ...$validated,
            'name'      => $validated['name_ar'],
            'is_active' => true,
        ]);

        return back()->with('success', 'تمت إضافة فترة العمل بنجاح');
    }

    /** PUT /work-periods/{period} */
    public function update(Request $request, WorkPeriod $period)
    {
        $validated = $request->validate([
            'name_ar'          => 'required|string|max:100',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i',
            'grace_period_mins'=> 'required|integer|min:0|max:60',
            'is_active'        => 'boolean',
        ]);

        $period->update([
            ...$validated,
            'name' => $validated['name_ar'],
        ]);

        return back()->with('success', 'تم تحديث فترة العمل');
    }

    /** DELETE /work-periods/{period} */
    public function destroy(WorkPeriod $period)
    {
        if ($period->employees()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف فترة العمل لأنها مرتبطة بموظفين');
        }
        $period->delete();
        return back()->with('success', 'تم حذف فترة العمل');
    }
}

