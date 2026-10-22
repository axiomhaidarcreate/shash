<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\Employee;
use App\Models\Department;
use App\Models\TaskCustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['employee', 'category', 'creator', 'customFieldValues.field'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->employee_name, fn($q) => $q->whereHas('employee', fn($q) => $q->where('full_name', 'like', '%'.$request->employee_name.'%')->orWhere('full_name_ar', 'like', '%'.$request->employee_name.'%')))
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date));


        $viewMode = $request->get('view', 'list'); // 'list' or 'kanban'

        if ($viewMode === 'kanban') {
            $tasks = $query->latest('updated_at')->get()->groupBy('status');
        } else {
            $tasks = $query->latest('updated_at')->paginate(15);
        }

        $employees = Employee::all();
        $categories = TaskCategory::all();

        return view('hr.tasks.index', compact('tasks', 'employees', 'categories', 'viewMode'));
    }

    public function create()
    {
        $employees = Employee::all();
        $departments = Department::all();
        $categories = TaskCategory::all();
        $parentTasks = Task::where('status', '!=', 'completed')->get();
        $customFields = TaskCustomField::activeFields();

        return view('hr.tasks.create', compact('employees', 'departments', 'categories', 'parentTasks', 'customFields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'employee_id' => 'required|exists:employees,id',
            'manual_employee_name' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'cost_amount' => 'nullable|numeric|min:0',
            'execution_date' => 'nullable|date',
            'execution_time' => 'nullable|date_format:H:i',
            'subtasks' => 'nullable|array',
            'subtasks.*.title' => 'required|string|max:255',
            'custom_fields' => 'nullable|array',
        ]);

        $taskData = collect($validated)->except('subtasks')->toArray();
        $taskData['created_by'] = Auth::id();
        $taskData['status'] = 'pending';
        if (!isset($taskData['priority'])) {
            $taskData['priority'] = 'medium';
        }

        // Convert empty strings to null for nullable foreign keys and dates
        $nullableFields = ['parent_id', 'department_id', 'due_date', 'due_time', 'execution_date', 'execution_time'];
        foreach ($nullableFields as $field) {
            if (isset($taskData[$field]) && $taskData[$field] === '') {
                $taskData[$field] = null;
            }
        }

        $taskData = collect($taskData)->except('custom_fields')->toArray();
        $task = Task::create($taskData);

        if (!empty($validated['custom_fields'])) {
            foreach ($validated['custom_fields'] as $fieldId => $value) {
                if ($value !== null && $value !== '') {
                    $storeValue = $value;
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $storeValue = \App\Services\SupabaseStorageService::upload('attachments', $value, 'tasks');
                    } elseif (is_array($value)) {
                        $storeValue = json_encode($value);
                    }
                    
                    $task->customFieldValues()->create([
                        'field_id' => $fieldId,
                        'value' => $storeValue,
                    ]);
                }
            }
        }

        if (!empty($validated['subtasks'])) {
            foreach ($validated['subtasks'] as $subtaskData) {
                if (!empty($subtaskData['title'])) {
                    $task->subtasks()->create([
                        'title' => $subtaskData['title'],
                        'employee_id' => $task->employee_id,
                        'priority' => $task->priority ?? 'medium',
                        'status' => 'pending',
                        'created_by' => Auth::id(),
                    ]);
                }
            }
        }

        // TODO: Send notification to employee

        return redirect()->route('tasks.index')->with('success', 'تم إنشاء المهمة بنجاح');
    }

    public function show(Task $task)
    {
        $task->load(['employee', 'category', 'creator', 'department', 'comments.user', 'comments.employee', 'attachments', 'subtasks', 'customFieldValues.field']);
        return view('hr.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $employees = Employee::all();
        $departments = Department::all();
        $categories = TaskCategory::all();
        $parentTasks = Task::where('id', '!=', $task->id)->where('status', '!=', 'completed')->get();
        $customFields = TaskCustomField::activeFields();

        return view('hr.tasks.edit', compact('task', 'employees', 'departments', 'categories', 'parentTasks', 'customFields'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'employee_id' => 'required|exists:employees,id',
            'manual_employee_name' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'cost_amount' => 'nullable|numeric|min:0',
            'execution_date' => 'nullable|date',
            'execution_time' => 'nullable|date_format:H:i',
            'custom_fields' => 'nullable|array',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        // Convert empty strings to null for nullable foreign keys and dates
        $nullableFields = ['parent_id', 'department_id', 'due_date', 'due_time', 'execution_date', 'execution_time'];
        foreach ($nullableFields as $field) {
            if (isset($validated[$field]) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $taskData = collect($validated)->except('custom_fields')->toArray();
        $task->update($taskData);

        if (isset($validated['custom_fields'])) {
            // Remove existing values to avoid duplicates
            $task->customFieldValues()->delete();
            foreach ($validated['custom_fields'] as $fieldId => $value) {
                if ($value !== null && $value !== '') {
                    $storeValue = $value;
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $storeValue = \App\Services\SupabaseStorageService::upload('attachments', $value, 'tasks');
                    } elseif (is_array($value)) {
                        $storeValue = json_encode($value);
                    }
                    $task->customFieldValues()->create([
                        'field_id' => $fieldId,
                        'value' => $storeValue,
                    ]);
                }
            }
        }

        return redirect()->route('tasks.show', $task)->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'تم حذف المهمة بنجاح');
    }

    public function addComment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $task->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'تم إضافة التعليق');
    }

    public function cancel(Request $request, Task $task)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $task->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_by' => 'manager',
        ]);

        return back()->with('success', 'تم إلغاء المهمة بنجاح');
    }

    public function reassign(Request $request, Task $task)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $task->update([
            'employee_id' => $validated['employee_id'],
            'status' => 'pending',
            'cancellation_reason' => null, // Reset if it was previously cancelled
            'cancelled_by' => null,
        ]);

        return back()->with('success', 'تم تحويل المهمة بنجاح');
    }
}

