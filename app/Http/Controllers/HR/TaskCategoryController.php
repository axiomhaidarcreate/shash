<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function index()
    {
        $categories = TaskCategory::withCount('tasks')->latest()->get();
        return view('hr.tasks.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        TaskCategory::create($validated);

        return redirect()->route('tasks.categories.index')->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function update(Request $request, TaskCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $category->update($validated);

        return redirect()->route('tasks.categories.index')->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(TaskCategory $category)
    {
        if ($category->tasks()->count() > 0) {
            return redirect()->route('tasks.categories.index')->with('error', 'لا يمكن حذف التصنيف لوجود مهام مرتبطة به');
        }

        $category->delete();

        return redirect()->route('tasks.categories.index')->with('success', 'تم حذف التصنيف بنجاح');
    }
}

