<x-layouts.admin>
    <!-- Page Header & Actions -->
        <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1><i class="fa-solid fa-list-check" style="color: var(--primary-color);"></i> إدارة المهام</h1>
            <p>إسناد ومتابعة مهام الموظفين</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('hr.tasks.create') }}" class="btn">
                <i class="fa-solid fa-plus me-2"></i> مهمة جديدة
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="toast toast-success" style="position: relative; top: 0; left: 0; transform: none; width: 100%; margin-bottom: 1rem; text-align: right; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('info'))
    <div class="toast" style="background: rgba(33,150,243,0.9); color: white; position: relative; top: 0; left: 0; transform: none; width: 100%; margin-bottom: 1rem; text-align: right; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-info-circle"></i> {{ session('info') }}
    </div>
    @endif

    <!-- فلاتر البحث -->
    <div class="card mb-4">
        <form action="{{ route('hr.tasks.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-control">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                </select>
            </div>
            
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label">الموظف</label>
                <select name="employee_id" class="form-control">
                    <option value="">الكل</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label class="form-label">طريقة العرض</label>
                <select name="view" class="form-control">
                    <option value="list" {{ request('view', 'list') == 'list' ? 'selected' : '' }}>قائمة</option>
                    <option value="kanban" {{ request('view') == 'kanban' ? 'selected' : '' }}>لوحة (Kanban)</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label class="form-label">اسم الموظف</label>
                <input type="text" name="employee_name" value="{{ request('employee_name') }}" placeholder="ابحث..." class="form-control">
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label class="form-label">التاريخ</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control">
            </div>

            <button type="submit" class="btn" style="min-height: 44px; padding: 0 1.5rem;">
                <i class="fa-solid fa-filter me-2"></i> تصفية
            </button>
        </form>
    </div>

    @if($viewMode === 'kanban')
        <!-- Kanban Board -->
        <div style="display: flex; flex-wrap: nowrap; overflow-x: auto; gap: 1.5rem; padding-bottom: 1.5rem; min-height: 500px;">
            @php
                $columns = [
                    'pending' => ['title' => 'قيد الانتظار', 'color' => 'var(--warning-color)', 'bg' => 'rgba(255,152,0,0.1)'],
                    'in_progress' => ['title' => 'قيد التنفيذ', 'color' => 'var(--info-color)', 'bg' => 'rgba(33,150,243,0.1)'],
                    'completed' => ['title' => 'مكتملة', 'color' => 'var(--success-color)', 'bg' => 'rgba(76,175,80,0.1)'],
                ];
            @endphp

            @foreach($columns as $status => $col)
                <div class="card" style="width: 320px; flex-shrink: 0; background: rgba(0,0,0,0.1); border: 1px dashed var(--border-color); padding: 1rem; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3 style="font-weight: 800; color: {{ $col['color'] }}; font-size: 1.1rem;">{{ $col['title'] }}</h3>
                        <span class="badge" style="background: {{ $col['bg'] }}; color: {{ $col['color'] }}; border: 1px solid {{ $col['color'] }};">{{ isset($tasks[$status]) ? $tasks[$status]->count() : 0 }}</span>
                    </div>

                    <div style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 0.8rem;">
                        @if(isset($tasks[$status]))
                            @foreach($tasks[$status] as $task)
                                <a href="{{ route('hr.tasks.show', $task) }}" class="card" style="display: block; padding: 1rem; text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='{{ $col['color'] }}'" onmouseout="this.style.borderColor='var(--border-color)'">
                                    <h4 style="font-weight: 800; color: var(--text-primary); margin-bottom: 0.8rem;">{{ $task->title }}</h4>
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: var(--text-secondary);">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <img src="{{ $task->employee->avatar_url ?? asset('images/default-avatar.png') }}" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                                            <span>{{ $task->employee->full_name }}</span>
                                        </div>
                                        <div class="btn-ghost" onclick="event.preventDefault(); window.location.href='{{ route('hr.tasks.edit', $task) }}';" style="padding: 4px 8px; font-size: 1rem;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- List View -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ \App\Models\Setting::get('task_label_title', 'المهمة') }}</th>
                            <th>{{ \App\Models\Setting::get('task_label_employee', 'الموظف') }}</th>
                            <th>المعني في المهمة</th>
                            <th>الوقت</th>
                            <th>الحالة</th>
                            <th>تاريخ الخدمة</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr style="cursor: pointer;" onclick="window.location='{{ route('hr.tasks.show', $task) }}'">
                            <td>
                                <span style="font-weight: 800; color: var(--text-primary);">{{ $task->title }}</span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img src="{{ $task->employee->avatar_url ?? asset('images/default-avatar.png') }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                                    <span style="font-weight: 700;">{{ $task->employee->full_name }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                    <span style="font-weight: 700;">إلى الموظف:</span> {{ $task->employee->full_name ?? 'غير محدد' }}
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);" dir="ltr">
                                    {{ $task->created_at ? $task->created_at->format('h:i A') : '--:--' }}
                                </span>
                            </td>
                            <td>
                                @if($task->status == 'completed')
                                    <span class="badge badge-active">مكتملة</span>
                                @elseif($task->status == 'in_progress')
                                    <span class="badge badge-prep">قيد التنفيذ</span>
                                @elseif($task->status == 'cancelled')
                                    <span class="badge badge-inactive">ملغية</span>
                                @else
                                    <span class="badge badge-pending">قيد الانتظار</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $serviceDateCustomField = $task->customFieldValues->first(function($cv) {
                                        if (!$cv->field) return false;
                                        $normalizedLabel = str_replace([' ', 'ة'], ['', 'ه'], $cv->field->label);
                                        return $normalizedLabel === 'تاريخالخدمه';
                                    });
                                    $serviceDate = $serviceDateCustomField ? $serviceDateCustomField->value : null;
                                @endphp
                                <span style="font-size: 0.85rem; color: var(--text-secondary);" dir="ltr">
                                    {{ $serviceDate ? $serviceDate : 'غير محدد' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; justify-content: center; gap: 8px;">
                                    @if($task->status !== 'cancelled')
                                    <!-- Cancel Button -->
                                    <button class="btn-ghost" style="color: var(--warning-color);" onclick="event.stopPropagation(); document.getElementById('cancelModal-{{ $task->id }}').style.display='flex'" title="إلغاء المهمة">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    @endif
                                    <!-- Reassign Button -->
                                    <button class="btn-ghost" style="color: var(--success-color);" onclick="event.stopPropagation(); document.getElementById('reassignModal-{{ $task->id }}').style.display='flex'" title="تحويل المهمة">
                                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                    </button>
                                    <a href="{{ route('hr.tasks.edit', $task) }}" class="btn-ghost" style="color: var(--info-color);" onclick="event.stopPropagation()" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('hr.tasks.destroy', $task) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost" style="color: var(--danger-color);" onclick="event.stopPropagation()" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Cancel Modal for Task {{ $task->id }} -->
                        <div id="cancelModal-{{ $task->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1050; align-items: center; justify-content: center; backdrop-filter: blur(4px);" onclick="event.stopPropagation()">
                            <div class="card" style="width: 100%; max-width: 450px; padding: 0; overflow: hidden;" onclick="event.stopPropagation()">
                                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); background: rgba(255,152,0,0.05); display: flex; justify-content: space-between; align-items: center;">
                                    <h3 style="font-weight: 800; color: var(--warning-color);"><i class="fa-solid fa-ban me-2"></i> إلغاء المهمة</h3>
                                    <button onclick="document.getElementById('cancelModal-{{ $task->id }}').style.display='none'" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.2rem;">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                <form action="{{ route('hr.tasks.cancel', $task) }}" method="POST" style="padding: 1.5rem;">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">سبب الإلغاء <span style="color: var(--danger-color);">*</span></label>
                                        <textarea name="cancellation_reason" rows="3" required placeholder="اكتب سبب إلغاء المهمة..." class="form-control" style="resize: none;"></textarea>
                                    </div>
                                    <div style="display: flex; gap: 10px; margin-top: 1.5rem;">
                                        <button type="submit" class="btn" style="flex: 1; background: var(--warning-color); color: white;">تأكيد الإلغاء</button>
                                        <button type="button" onclick="document.getElementById('cancelModal-{{ $task->id }}').style.display='none'" class="btn btn-outline" style="flex: 1;">تراجع</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Reassign Modal for Task {{ $task->id }} -->
                        <div id="reassignModal-{{ $task->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1050; align-items: center; justify-content: center; backdrop-filter: blur(4px);" onclick="event.stopPropagation()">
                            <div class="card" style="width: 100%; max-width: 450px; padding: 0; overflow: hidden;" onclick="event.stopPropagation()">
                                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); background: rgba(33,150,243,0.05); display: flex; justify-content: space-between; align-items: center;">
                                    <h3 style="font-weight: 800; color: var(--info-color);"><i class="fa-solid fa-arrow-right-arrow-left me-2"></i> تحويل المهمة</h3>
                                    <button onclick="document.getElementById('reassignModal-{{ $task->id }}').style.display='none'" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.2rem;">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                <form action="{{ route('hr.tasks.reassign', $task) }}" method="POST" style="padding: 1.5rem;">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">اختر الموظف الجديد <span style="color: var(--danger-color);">*</span></label>
                                        <select name="employee_id" required class="form-control">
                                            <option value="">اختر موظفاً...</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ $task->employee_id == $employee->id ? 'disabled' : '' }}>
                                                    {{ $employee->full_name }} {{ $task->employee_id == $employee->id ? '(الموظف الحالي)' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="display: flex; gap: 10px; margin-top: 1.5rem;">
                                        <button type="submit" class="btn" style="flex: 1; background: var(--info-color); color: white;">تأكيد التحويل</button>
                                        <button type="button" onclick="document.getElementById('reassignModal-{{ $task->id }}').style.display='none'" class="btn btn-outline" style="flex: 1;">تراجع</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                <i class="fa-solid fa-clipboard-list" style="font-size: 2.5rem; opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                                لا توجد مهام حالياً
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); font-family: sans-serif;" dir="ltr">
                {{ $tasks->links() }}
            </div>
        </div>
    @endif
</x-layouts.admin>
