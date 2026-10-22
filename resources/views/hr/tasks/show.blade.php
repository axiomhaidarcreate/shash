<x-layouts.admin>
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-2xl font-bold text-primary">{{ $task->title }}</h2>
                @if($task->status == 'completed')
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">مكتملة</span>
                @elseif($task->status == 'in_progress')
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">قيد التنفيذ</span>
                @elseif($task->status == 'cancelled')
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded">ملغية</span>
                @else
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">قيد الانتظار</span>
                @endif
            </div>
            <p class="text-secondary">
                بواسطة <span class="font-bold">{{ $task->creator->name ?? 'النظام' }}</span>
                - أسندت إلى <span class="font-bold">{{ $task->employee->full_name }} @if($task->manual_employee_name) ({{ $task->manual_employee_name }}) @endif</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('hr.tasks.edit', $task) }}" class="bg-primary text-white px-4 py-2 rounded-xl font-bold hover:bg-primary-dark transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                تعديل المهمة
            </a>
            <button onclick="document.getElementById('editStatusModal').classList.remove('hidden')" class="bg-white border border-slate-200 text-primary px-4 py-2 rounded-xl font-bold hover:bg-slate-50 transition-colors">
                تحديث الحالة
            </button>
            @if($task->status !== 'cancelled')
            <button onclick="document.getElementById('cancelTaskModal').classList.remove('hidden')" class="bg-red-50 border border-red-200 text-red-600 px-4 py-2 rounded-xl font-bold hover:bg-red-100 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                إلغاء المهمة
            </button>
            @endif
            <a href="{{ route('hr.tasks.index') }}" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-xl font-bold hover:bg-slate-200 transition-colors">
                العودة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Details Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Task Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                <h3 class="font-bold text-lg text-primary mb-4 border-b border-slate-100 pb-4">التفاصيل</h3>
                


                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">



                    <div>
                        <span class="block text-xs text-slate-400 mb-1">تاريخ الإنشاء</span>
                        <span class="font-bold text-primary" dir="ltr">{{ $task->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    @if($task->customFieldValues->count() > 0)
                        <div class="col-span-2 md:col-span-3 mt-4 pt-4 border-t border-slate-100">
                            <h4 class="text-sm font-bold text-primary mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                تفاصيل إضافية للمهمة
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($task->customFieldValues as $cv)
                                    @if($cv->field && !empty($cv->value))
                                        <div class="{{ $cv->field->type === 'textarea' ? 'md:col-span-2 lg:col-span-3' : '' }}">
                                            <span class="block text-xs text-slate-400 mb-1">{{ $cv->field->label }}</span>
                                            @if($cv->field->type === 'file')
                                                @php
                                                    $fileUrl = filter_var($cv->value, FILTER_VALIDATE_URL) ? $cv->value : Storage::url($cv->value);
                                                    $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i', $cv->value);
                                                @endphp
                                                @if($isImage)
                                                    <a href="{{ $fileUrl }}" target="_blank" class="block mt-1">
                                                        <img src="{{ $fileUrl }}" alt="{{ $cv->field->label }}" class="rounded-lg border border-slate-200 max-w-full max-h-64 object-contain shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                                                    </a>
                                                @else
                                                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-2 mt-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-primary rounded-lg text-sm font-bold transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                        تحميل المرفق
                                                    </a>
                                                @endif
                                            @else
                                                <div class="font-bold text-primary text-sm whitespace-pre-wrap">{{ is_array(json_decode($cv->value, true)) ? implode(', ', json_decode($cv->value, true)) : $cv->value }}</div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                @if($task->completed_at)
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2 text-emerald-600 bg-emerald-50 p-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    <span class="font-bold text-sm">تم إنجاز المهمة في {{ $task->completed_at->format('Y-m-d h:i A') }}</span>
                </div>
                @endif

                @if($task->status === 'cancelled')
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="flex items-start gap-2 text-red-600 bg-red-50 p-3 rounded-lg">
                        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        <div>
                            <span class="font-bold text-sm block">تم إلغاء المهمة بواسطة {{ $task->cancelled_by === 'manager' ? 'المدير' : 'الموظف' }}</span>
                            @if($task->cancellation_reason)
                                <span class="text-sm text-red-500 mt-1 block">السبب: {{ $task->cancellation_reason }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Subtasks -->
            @if($task->subtasks->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-lg text-primary mb-4">المهام الفرعية</h3>
                <div class="space-y-3">
                    @foreach($task->subtasks as $sub)
                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-start gap-3">
                                @if($sub->status == 'completed')
                                    <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                @elseif($sub->status == 'in_progress')
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <svg class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"></circle></svg>
                                @endif
                                <div>
                                    <a href="{{ route('hr.tasks.show', $sub) }}" class="font-bold text-primary hover:text-primary transition-colors text-lg">{{ $sub->title }}</a>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @if($sub->status == 'completed')
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">مكتملة</span>
                                        @elseif($sub->status == 'in_progress')
                                            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">قيد التنفيذ</span>
                                        @elseif($sub->status == 'cancelled')
                                            <span class="text-[10px] font-bold text-slate-600 bg-slate-200 px-2 py-0.5 rounded">ملغية</span>
                                        @else
                                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">قيد الانتظار</span>
                                        @endif

                                        <span class="text-[10px] font-bold text-slate-600 bg-white border border-slate-200 px-2 py-0.5 rounded flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            {{ $sub->employee->full_name ?? 'غير مسند' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('hr.tasks.show', $sub) }}" class="text-sm bg-white border border-slate-200 text-slate-600 font-bold px-3 py-1 rounded-lg hover:bg-slate-50 transition-colors shrink-0">
                                التفاصيل
                            </a>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 border-t border-slate-100 pt-3">
                            @if($sub->location)
                            <div>
                                <span class="block text-[10px] text-slate-400 mb-0.5">موقع التنفيذ</span>
                                <span class="font-bold text-xs text-primary">{{ $sub->location }}</span>
                            </div>
                            @endif
                            
                            @if($sub->contact_number)
                            <div>
                                <span class="block text-[10px] text-slate-400 mb-0.5">رقم التواصل</span>
                                <span class="font-bold text-xs text-primary" dir="ltr">{{ $sub->contact_number }}</span>
                            </div>
                            @endif

                            @if($sub->cost_amount)
                            <div>
                                <span class="block text-[10px] text-slate-400 mb-0.5">التكلفة</span>
                                <span class="font-bold text-xs text-emerald-600">{{ number_format($sub->cost_amount, 2) }}</span>
                            </div>
                            @endif

                            @if($sub->execution_date || $sub->due_date)
                            <div>
                                <span class="block text-[10px] text-slate-400 mb-0.5">التاريخ</span>
                                <span class="font-bold text-xs text-primary" dir="ltr">
                                    {{ $sub->execution_date ? \Carbon\Carbon::parse($sub->execution_date)->format('Y-m-d') : ($sub->due_date ? $sub->due_date->format('Y-m-d') : 'غير محدد') }}
                                </span>
                            </div>
                            @endif
                        </div>
                        @if($sub->description)
                            <div class="mt-3 text-xs text-secondary bg-white p-2 rounded border border-slate-100 line-clamp-2">
                                {{ $sub->description }}
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Column (Comments & Attachments) -->
        <div class="space-y-6">
            <!-- Attachments -->
            @if($task->attachments->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-lg text-primary mb-4 border-b border-slate-100 pb-4">المرفقات الإثباتية</h3>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($task->attachments as $attachment)
                        <a href="{{ $attachment->file_url }}" target="_blank" class="block group relative rounded-lg overflow-hidden border border-slate-200 aspect-square bg-slate-50">
                            @if(str_contains($attachment->file_type, 'image'))
                                <img src="{{ $attachment->file_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Comments (Chat) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col h-[500px]">
                <div class="p-4 border-b border-slate-100 bg-slate-50 rounded-t-2xl">
                    <h3 class="font-bold text-lg text-primary">النقاش والملاحظات</h3>
                </div>
                
                <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-slate-50/50">
                    @forelse($task->comments as $comment)
                        <div class="flex gap-3 {{ $comment->user_id ? 'flex-row' : 'flex-row-reverse' }}">
                            <!-- Avatar -->
                            <div class="w-8 h-8 rounded-full bg-indigo-100 shrink-0 overflow-hidden">
                                @if($comment->user_id)
                                    <div class="w-full h-full flex items-center justify-center text-indigo-600 font-bold text-sm">{{ substr($comment->user->name, 0, 1) }}</div>
                                @else
                                    <img src="{{ $comment->employee->avatar_url ?? asset('images/default-avatar.png') }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            
                            <!-- Bubble -->
                            <div class="max-w-[80%] {{ $comment->user_id ? 'bg-white border border-slate-200' : 'bg-primary text-white' }} p-3 rounded-2xl {{ $comment->user_id ? 'rounded-tr-none' : 'rounded-tl-none' }} shadow-sm">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="text-xs font-bold {{ $comment->user_id ? 'text-primary' : 'text-white' }}">{{ $comment->user_id ? $comment->user->name : $comment->employee->full_name }}</span>
                                    <span class="text-[10px] {{ $comment->user_id ? 'text-slate-400' : 'text-primary-light' }}" dir="ltr">{{ $comment->created_at->format('H:i') }}</span>
                                </div>
                                <p class="text-sm">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-slate-400">
                            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span class="text-sm">لا توجد تعليقات حتى الآن</span>
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-slate-100 bg-white rounded-b-2xl">
                    <form action="{{ route('hr.tasks.comments.store', $task) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="comment" required placeholder="اكتب ملاحظة..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 focus:outline-none focus:border-primary">
                        <button type="submit" class="bg-primary text-white w-10 h-10 rounded-xl flex items-center justify-center hover:bg-primary-dark transition-colors shrink-0">
                            <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="editStatusModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-lg text-primary">تحديث حالة المهمة</h3>
                <button onclick="document.getElementById('editStatusModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('hr.tasks.update', $task) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <!-- Hidden Required Fields for update method -->
                <input type="hidden" name="title" value="{{ $task->title }}">
                <input type="hidden" name="priority" value="{{ $task->priority }}">
                <input type="hidden" name="employee_id" value="{{ $task->employee_id }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="form-label">الحالة الحالية: 
                            @if($task->status == 'completed') مكتملة
                            @elseif($task->status == 'in_progress') قيد التنفيذ
                            @elseif($task->status == 'cancelled') ملغية
                            @else قيد الانتظار @endif
                        </label>
                        <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                        </select>
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-xl font-bold hover:bg-primary-dark transition-colors">حفظ التغييرات</button>
                    <button type="button" onclick="document.getElementById('editStatusModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-3 rounded-xl font-bold hover:bg-slate-200 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cancel Task Modal -->
    <div id="cancelTaskModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-red-50">
                <h3 class="font-bold text-lg text-red-700">إلغاء المهمة</h3>
                <button onclick="document.getElementById('cancelTaskModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('hr.tasks.cancel', $task) }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="form-label">سبب الإلغاء <span class="text-red-500">*</span></label>
                        <textarea name="cancellation_reason" rows="4" required placeholder="اكتب سبب إلغاء المهمة..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 resize-none"></textarea>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                        <p class="text-sm text-amber-700 font-bold flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                            سيتم إلغاء المهمة وإبلاغ الموظف بذلك
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700 transition-colors">تأكيد الإلغاء</button>
                    <button type="button" onclick="document.getElementById('cancelTaskModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-3 rounded-xl font-bold hover:bg-slate-200 transition-colors">تراجع</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>


