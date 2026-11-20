<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('employees')->orderBy('name_ar')->get();
        return view('hr.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Department::create([
            'name_ar' => trim($validated['name_ar']),
            'name' => !empty($validated['name']) ? trim($validated['name']) : null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        Cache::forget('departments_all');

        return redirect()->route('hr.departments.index')->with('success', 'تم إضافة القسم بنجاح');
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        $dept = Department::create([
            'name_ar' => trim($validated['name_ar']),
            'name' => !empty($validated['name']) ? trim($validated['name']) : null,
            'is_active' => true,
        ]);

        Cache::forget('departments_all');

        return response()->json([
            'success' => true,
            'department' => $dept,
            'message' => 'تم إضافة القسم بنجاح'
        ]);
    }

    public function destroy(Department $department)
    {
        $department->delete();
        Cache::forget('departments_all');

        return redirect()->route('hr.departments.index')->with('success', 'تم حذف القسم بنجاح');
    }
}
