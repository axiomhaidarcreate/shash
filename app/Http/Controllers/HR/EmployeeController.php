<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $search       = request('search');
        $departmentId = request('department_id');

        $query = Employee::with(['department', 'leaveRequests' => function ($q) use ($today) {
            $q->where('status', 'approved')
              ->where('start_date', '<=', $today)
              ->where('end_date', '>=', $today);
        }])->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name_ar', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        $totalEmployees = Cache::remember('employees_active_count', 60, fn() => Employee::where('is_active', true)->count());
        $dismissedCount = Cache::remember('employees_dismissed_count', 60, fn() => Employee::where('is_active', false)->count());
        $departments    = Cache::remember('departments_all', 3600, fn() => \App\Models\Department::orderBy('name_ar')->get());

        $onVacationCount = Cache::remember('on_vacation_count', 60, function () use ($today) {
            return Employee::where('is_active', true)->whereHas('leaveRequests', function ($q) use ($today) {
                $q->where('status', 'approved')
                  ->where('start_date', '<=', $today)
                  ->where('end_date', '>=', $today);
            })->count();
        });

        $pendingEmployees = (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->hasRole('admin')))
            ? Employee::with('department')->where('is_active', false)->where('notes', 'like', '%في انتظار موافقة المدير%')->latest()->get()
            : collect();

        return view('hr.employees.index', compact(
            'employees', 'totalEmployees', 'dismissedCount',
            'onVacationCount', 'departments', 'search', 'departmentId', 'pendingEmployees'
        ));
    }

    public function create()
    {
        $departments       = \App\Models\Department::orderBy('name_ar')->get();
        $contractTemplates = \App\Models\ContractTemplate::all();
        return view('hr.employees.create', compact('departments', 'contractTemplates'));
    }

    public function dismissed()
    {
        $employees = Employee::with('department')->where('is_active', false)->latest()->paginate(10);
        return view('hr.employees.dismissed', compact('employees'));
    }

    public function vacation()
    {
        $today = now()->toDateString();
        $employees = Employee::with(['department', 'leaveRequests' => function ($q) use ($today) {
            $q->where('status', 'approved')
              ->whereRaw('LEAST(start_date, end_date) <= ?', [$today])
              ->whereRaw('GREATEST(start_date, end_date) >= ?', [$today]);
        }])->whereHas('leaveRequests', function ($query) use ($today) {
            $query->where('status', 'approved')
                  ->whereRaw('LEAST(start_date, end_date) <= ?', [$today])
                  ->whereRaw('GREATEST(start_date, end_date) >= ?', [$today]);
        })->latest()->paginate(10);

        return view('hr.employees.vacation', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name_ar'           => 'required|string|max:255',
            'full_name'              => 'nullable|string|max:255',
            'employee_code'          => 'required|string|min:6|unique:employees,employee_code',
            'password'               => 'required|string|min:6',
            'email'                  => 'required|email|unique:employees,email',
            'phone'                  => 'required|string',
            'national_id'            => 'nullable|string',
            'hire_date'              => 'required|date',
            'base_salary'            => 'required|numeric',
            'department_id'          => 'nullable|exists:departments,id',
            'employment_type'        => 'nullable|string',
            'is_active'              => 'required|boolean',
            'avatar'                 => 'nullable|image|max:2048',
            'job_title_ar'           => 'nullable|string|max:255',
            'national_id_expiry'     => 'nullable|date',
            'passport_number'        => 'nullable|string',
            'passport_expiry'        => 'nullable|date',
            'health_cert_number'     => 'nullable|string',
            'previous_sponsor'       => 'nullable|string',
            'phone_2'                => 'nullable|string',
            'nationality'            => 'nullable|string|max:255',
            'percentage'             => 'nullable|numeric|between:0,100',
            'housing_allowance'      => 'nullable|numeric',
            'address'                => 'nullable|string',
            'address_link'           => 'nullable|string',
            'insurance_name'         => 'nullable|string',
            'insurance_start_date'   => 'nullable|date',
            'insurance_expiry_date'  => 'nullable|date',
            'date_of_birth'          => 'nullable|date',
            'working_days'           => 'nullable|integer',
            'daily_work_hours'       => 'nullable|integer',
            'contract_template_id'   => 'nullable|exists:contract_templates,id',
            'health_cert_expiry'     => 'nullable|date',
            'contract_expiry_date'   => 'nullable|date',
            'national_id_attachment' => 'nullable|file|max:5120',
            'passport_attachment'    => 'nullable|file|max:5120',
            'health_cert_attachment' => 'nullable|file|max:5120',
            'contract_attachment'    => 'nullable|file|max:5120',
        ]);

        // تحويل الحقول الفارغة إلى null لتجنب أخطاء قاعدة البيانات
        foreach ($validated as $key => $value) {
            if ($value === '') {
                $validated[$key] = null;
            }
        }

        // ── تم إزالة التحقق من حد الموظفين (SaaS Limits) ────────────────

        $userPassword = $validated['password'];

        // ── رفع الصورة الشخصية إلى التخزين السحابي ──────────────────────────
        if ($request->hasFile('avatar')) {
            $validated['avatar_url'] = \App\Services\SupabaseStorageService::upload('avatars', $request->file('avatar'), 'employees');
        }

        // ── رفع المرفقات ────────────────────────────────────────────────────
        $attachmentFields = ['national_id_attachment', 'passport_attachment', 'health_cert_attachment', 'contract_attachment'];
        foreach ($attachmentFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = \App\Services\SupabaseStorageService::upload('attachments', $request->file($field), 'employees');
            }
        }

        // ── إنشاء حساب في Firebase Auth ─────────────────────────────────────
        $firebaseUserId = null;
        try {
            $firebase       = app(FirebaseService::class);
            $firebaseUserId = $firebase->createUser(
                $validated['email'],
                $userPassword,
                $validated['full_name_ar']
            );
            if ($firebaseUserId) {
                $validated['user_id'] = $firebaseUserId;
            }
        } catch (\Throwable $e) {
            Log::error('Firebase Auth create failed: ' . $e->getMessage());
        }

        if (empty($validated['full_name'])) {
            $validated['full_name'] = $validated['full_name_ar'];
        }

        $employee = Employee::create($validated);

        // ── إنشاء حساب مستخدم في النظام (اختياري) ────────────────────────
        if ($request->input('create_system_account')) {
            $systemRole = $request->input('system_role', 'order_staff');
            $isDynamic  = $systemRole === 'dynamic_kitchen_staff';

            $user = \App\Models\User::create([
                'name'              => $validated['full_name_ar'],
                'email'             => $validated['email'],
                'password'          => \Illuminate\Support\Facades\Hash::make($userPassword),
                'assigned_kitchens' => $systemRole === 'delivery_staff' ? $request->input('assigned_kitchens', []) : null,
                'kitchen_slug'      => $isDynamic ? $request->input('kitchen_slug') : null,
            ]);

            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $systemRole, 'guard_name' => 'web']);
            $user->assignRole($role);
        }

        // ── مزامنة القسم والموظف إلى Firebase RTDB ─────────────────────────
        try {
            $firebase = app(FirebaseService::class);
            $employee->load('department');

            if ($employee->department) {
                $firebase->syncDepartment($employee->department);
            }

            $firebase->syncEmployee($employee);
            Log::info("✅ Employee synced to Firebase RTDB: {$employee->id}");
        } catch (\Throwable $e) {
            Log::error('❌ Firebase RTDB sync failed (store): ' . $e->getMessage());
        }
        // ────────────────────────────────────────────────────────────────────

        $this->clearEmployeeCache();

        if (auth()->user()->role === 'accountant') {
            return redirect()->route('hr.employees.index')->with('info', 'تم إرسال بيانات الموظف بنجاح. في انتظار موافقة المدير لتفعيل الحساب.');
        }

        return redirect()->route('hr.employees.index')->with('success', 'تم إضافة الموظف بنجاح.');
    }

    public function approve(Employee $employee)
    {
        $updatedNotes = str_replace('[في انتظار موافقة المدير]', '', $employee->notes ?? '');
        $employee->update([
            'is_active' => true,
            'notes'     => trim($updatedNotes),
        ]);

        // مزامنة حالة التفعيل إلى Firebase
        try {
            $firebase = app(FirebaseService::class);
            $employee->load('department');
            $firebase->syncEmployee($employee);
        } catch (\Throwable $e) {
            Log::warning('Firebase sync failed on approve: ' . $e->getMessage());
        }

        $this->clearEmployeeCache();
        return redirect()->route('hr.employees.index')->with('success', 'تم تفعيل الموظف "' . $employee->full_name_ar . '" بنجاح.');
    }

    public function edit(Employee $employee)
    {
        $departments       = \App\Models\Department::orderBy('name_ar')->get();
        $contractTemplates = \App\Models\ContractTemplate::all();
        return view('hr.employees.edit', compact('employee', 'departments', 'contractTemplates'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'full_name_ar'           => 'required|string|max:255',
            'full_name'              => 'nullable|string|max:255',
            'employee_code'          => 'required|string|unique:employees,employee_code,' . $employee->id,
            'password'               => 'nullable|string|min:6',
            'email'                  => 'required|email|unique:employees,email,' . $employee->id,
            'phone'                  => 'required|string',
            'national_id'            => 'nullable|string',
            'hire_date'              => 'required|date',
            'base_salary'            => 'required|numeric',
            'department_id'          => 'nullable|exists:departments,id',
            'employment_type'        => 'nullable|string',
            'is_active'              => 'required|boolean',
            'avatar'                 => 'nullable|image|max:2048',
            'job_title_ar'           => 'nullable|string|max:255',
            'national_id_expiry'     => 'nullable|date',
            'passport_number'        => 'nullable|string',
            'passport_expiry'        => 'nullable|date',
            'previous_sponsor'       => 'nullable|string',
            'phone_2'                => 'nullable|string',
            'nationality'            => 'nullable|string|max:255',
            'percentage'             => 'nullable|numeric|between:0,100',
            'housing_allowance'      => 'nullable|numeric',
            'address'                => 'nullable|string',
            'address_link'           => 'nullable|string',
            'insurance_name'         => 'nullable|string',
            'insurance_start_date'   => 'nullable|date',
            'insurance_expiry_date'  => 'nullable|date',
            'date_of_birth'          => 'nullable|date',
            'working_days'           => 'nullable|integer',
            'daily_work_hours'       => 'nullable|integer',
            'contract_template_id'   => 'nullable|exists:contract_templates,id',
            'health_cert_expiry'     => 'nullable|date',
            'contract_expiry_date'   => 'nullable|date',
            'national_id_attachment' => 'nullable|file|max:5120',
            'passport_attachment'    => 'nullable|file|max:5120',
            'health_cert_attachment' => 'nullable|file|max:5120',
            'contract_attachment'    => 'nullable|file|max:5120',
            'resignation_attachment' => 'nullable|file|max:5120',
        ]);

        // تحويل الحقول الفارغة إلى null لتجنب أخطاء قاعدة البيانات
        foreach ($validated as $key => $value) {
            if ($value === '') {
                $validated[$key] = null;
            }
        }

        $newPassword = $validated['password'] ?? null;
        if (!$newPassword) {
            unset($validated['password']);
        }

        // ── تحديث كلمة المرور في Firebase Auth ──────────────────────────────
        if ($newPassword && $employee->user_id) {
            try {
                $firebase = app(FirebaseService::class);
                $firebase->updateUserPassword($employee->user_id, $newPassword);
            } catch (\Throwable $e) {
                Log::warning('Firebase password update failed: ' . $e->getMessage());
            }
        }

        // ── رفع الصورة الشخصية ───────────────────────────────────────────────
        if ($request->hasFile('avatar')) {
            $validated['avatar_url'] = \App\Services\SupabaseStorageService::upload('avatars', $request->file('avatar'), 'employees');
        }

        // ── رفع المرفقات ────────────────────────────────────────────────────
        $attachmentFields = ['national_id_attachment', 'passport_attachment', 'health_cert_attachment', 'contract_attachment', 'resignation_attachment'];
        foreach ($attachmentFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = \App\Services\SupabaseStorageService::upload('attachments', $request->file($field), 'employees');
            }
        }

        if (empty($validated['full_name'])) {
            $validated['full_name'] = $validated['full_name_ar'];
        }

        $employee->update($validated);

        // ── مزامنة القسم والموظف إلى Firebase RTDB ─────────────────────────
        try {
            $firebase = app(FirebaseService::class);
            $employee->load('department');

            if ($employee->department) {
                $firebase->syncDepartment($employee->department);
            }

            $firebase->syncEmployee($employee);
            Log::info("✅ Employee updated in Firebase RTDB: {$employee->id}");
        } catch (\Throwable $e) {
            Log::error('❌ Firebase RTDB sync failed (update): ' . $e->getMessage());
        }
        // ────────────────────────────────────────────────────────────────────

        $this->clearEmployeeCache();
        return redirect()->route('hr.employees.index')->with('success', 'تم تحديث بيانات الموظف بنجاح.');
    }

    public function destroy(Employee $employee)
    {
        $employee->is_active        = false;
        $employee->termination_date = now()->toDateString();
        $employee->save();

        // مزامنة تعطيل الموظف إلى Firebase
        try {
            $firebase = app(FirebaseService::class);
            $employee->load('department');
            $firebase->syncEmployee($employee);
        } catch (\Throwable $e) {
            Log::warning('Firebase sync failed on destroy: ' . $e->getMessage());
        }

        $this->clearEmployeeCache();
        return redirect()->route('hr.employees.index')->with('success', 'تم حذف الموظف ونقله لقسم المفصولين بنجاح.');
    }

    public function toggleStatus(Employee $employee)
    {
        $employee->is_active = !$employee->is_active;
        $employee->save();

        // مزامنة الحالة إلى Firebase
        try {
            $firebase = app(FirebaseService::class);
            $employee->load('department');
            $firebase->syncEmployee($employee);
        } catch (\Throwable $e) {
            Log::warning('Firebase sync failed on toggleStatus: ' . $e->getMessage());
        }

        $this->clearEmployeeCache();
        return back()->with('success', 'تم تحديث حالة الموظف بنجاح.');
    }

    public function printContract(Employee $employee)
    {
        if (!$employee->contract_template_id || !$employee->contractTemplate) {
            return back()->with('error', 'هذا الموظف غير مرتبط بأي قالب عقد بعد.');
        }

        $template = $employee->contractTemplate;
        $content  = $template->content;

        $content = str_replace('[اسم الموظف]', $employee->full_name_ar ?? $employee->full_name, $content);
        $content = str_replace('[الراتب]', number_format($employee->base_salary, 2), $content);
        $content = str_replace('[رقم الهوية]', $employee->national_id ?? '___________', $content);
        $content = str_replace('[تاريخ التعيين]', $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : '___________', $content);
        $content = str_replace('[رقم الجواز]', $employee->passport_number ?? '___________', $content);
        $content = str_replace('[الجنسية]', $employee->nationality ?? '___________', $content);
        $content = str_replace('[المسمى الوظيفي]', $employee->job_title_ar ?? ($employee->department->name_ar ?? '___________'), $content);

        return view('hr.employees.print_contract', compact('employee', 'template', 'content'));
    }

    private function clearEmployeeCache(): void
    {
        Cache::forget('employees_active_count');
        Cache::forget('employees_dismissed_count');
        Cache::forget('active_employees_list');
        Cache::forget('dashboard_total_employees');
        Cache::forget('on_vacation_count');
    }
}

