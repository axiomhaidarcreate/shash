<x-layouts.admin>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-primary">تعديل المهمة</h2>
            <p class="text-secondary mt-1">تعديل تفاصيل المهمة الحالية</p>
        </div>
        <a href="{{ route('hr.tasks.index') }}" class="text-secondary hover:text-primary font-bold transition-colors">
            العودة للمهام
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6">
            <ul class="list-disc list-inside font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('hr.tasks.update', $task) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


            <!-- Title -->
            <div class="md:col-span-2">
                <label class="form-label">{{ \App\Models\Setting::get('task_label_title', 'عنوان المهمة') }} <span class="text-red-500">*</span></label>
                <input list="task-titles" type="text" name="title" value="{{ old('title', $task->title) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="مثال: صيانة التكييف أو اختر من القائمة" autocomplete="off">
                <datalist id="task-titles">
                    <option value="غسيل">
                    <option value="تنظيف">
                    <option value="صيانة">
                    <option value="برمجة">
                </datalist>
            </div>


            


            <!-- Assign to -->
            <div>
                <label class="form-label">{{ \App\Models\Setting::get('task_label_employee', 'إسناد إلى موظف') }} <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    <option value="">-- اختر الموظف --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id', $task->employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }} ({{ $employee->department->name ?? 'بدون قسم' }})</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="form-label">اسم الموظف (اختياري)</label>
                <input type="text" name="manual_employee_name" value="{{ old('manual_employee_name', $task->manual_employee_name) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="اكتب اسم الموظف">
            </div>


            @if(isset($customFields) && $customFields->count() > 0)
            <!-- Custom Fields Section -->
            <div class="md:col-span-2 mt-4 pt-4 border-t border-slate-100">
                <h3 class="font-bold text-primary mb-4 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    تفاصيل إضافية للمهمة
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($customFields as $field)
                        <div class="{{ $field->type === 'textarea' ? 'md:col-span-2' : '' }}">
                            <label class="form-label">
                                {{ $field->label }} @if($field->is_required)<span class="text-red-500">*</span>@endif
                            </label>
                            
                            @if($field->type === 'textarea')
                                <textarea name="custom_fields[{{ $field->id }}]" rows="3" {{ $field->is_required ? 'required' : '' }} class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">{{ old('custom_fields.'.$field->id) }}</textarea>
                            @elseif($field->type === 'select')
                                <select name="custom_fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }} class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                    <option value="">— اختر —</option>
                                    @if(is_array($field->options))
                                        @foreach($field->options as $option)
                                            <option value="{{ trim($option) }}" {{ old('custom_fields.'.$field->id) == trim($option) ? 'selected' : '' }}>{{ trim($option) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @elseif($field->type === 'file')
                                <input type="file" name="custom_fields[{{ $field->id }}]" {{ $field->is_required ? 'required' : '' }} class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            @else
                                <input type="{{ $field->type === 'date' ? 'date' : ($field->type === 'time' ? 'time' : ($field->type === 'number' ? 'number' : 'text')) }}" name="custom_fields[{{ $field->id }}]" value="{{ old('custom_fields.'.$field->id) }}" {{ $field->is_required ? 'required' : '' }} class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif



            <!-- Status -->
            <div class="md:col-span-2 mt-4">
                <label class="form-label">حالة المهمة</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                    <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                </select>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex gap-4">
            <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-primary-dark transition-colors">
                حفظ التعديلات
            </button>
            <a href="{{ route('hr.tasks.index') }}" class="bg-slate-100 text-slate-600 px-8 py-3 rounded-xl font-bold hover:bg-slate-200 transition-colors">
                إلغاء
            </a>
        </div>
    </form>
</x-layouts.admin>


