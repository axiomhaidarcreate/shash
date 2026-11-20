<x-layouts.admin>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div class="mb-2 text-sm text-secondary">
                <a href="{{ route('hr.employees.index') }}" class="hover:text-primary transition">الموظفين</a> / 
                <span class="text-primary font-bold">إضافة موظف جديد</span>
            </div>
            <h2 class="text-2xl font-bold text-primary mt-2">إضافة موظف جديد</h2>
        </div>
        <a href="{{ route('hr.employees.index') }}" class="btn btn-outline">
            رجوع
        </a>
    </div>

    <div class="card">
        <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-r-4 border-red-500 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <h3 class="text-red-800 font-bold">يوجد أخطاء في البيانات المدخلة:</h3>
                    </div>
                    <ul class="list-disc list-inside text-red-700 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Avatar -->
            <div class="mb-8 flex flex-col items-center" x-data="{ avatarPreview: null }">
                <label class="block text-sm font-bold text-primary mb-3">الصورة الشخصية <span class="text-red-500">*</span></label>
                <div @click="$refs.avatarFileInput.click()" class="cursor-pointer group relative w-[400px] max-w-full h-[400px] rounded-2xl border-2 border-dashed border-slate-300 hover:border-primary bg-slate-50 hover:bg-primary/5 flex items-center justify-center overflow-hidden transition-all duration-300 shadow-sm">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" alt="Preview" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </template>
                    <template x-if="!avatarPreview">
                        <div class="flex flex-col items-center gap-1.5 text-slate-400 group-hover:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" /></svg>
                            <span class="text-xs font-bold">اختر ملف</span>
                        </div>
                    </template>
                    <!-- Hover overlay -->
                    <div x-show="avatarPreview" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-300 rounded-2xl">
                        <span class="text-white font-bold text-xs flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                            تغيير الصورة
                        </span>
                    </div>
                </div>
                <input type="file" name="avatar" accept="image/*" required x-ref="avatarFileInput" class="hidden" @change="if($event.target.files[0]) avatarPreview = URL.createObjectURL($event.target.files[0])">
                <p class="text-[11px] text-slate-400 mt-2">اضغط على المربع لاختيار صورة (PNG, JPG)</p>
            </div>

            <!-- Section 1: Basic Information -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">1. البيانات الأساسية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="form-label">الاسم الكامل (عربي) <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name_ar" required class="form-control">
                </div>
                <div>
                    <label class="form-label">الاسم الكامل (إنجليزي)</label>
                    <input type="text" name="full_name" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <label class="form-label" style="margin-bottom: 0;">القسم</label>
                        <button type="button" onclick="openQuickDeptModal()" style="background: none; border: none; color: var(--primary-color); font-size: 0.8rem; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-plus-circle"></i> إضافة قسم
                        </button>
                    </div>
                    <select name="department_id" id="department_select" class="form-control">
                        <option value="">— غير محدد —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">المسمى الوظيفي</label>
                    <input type="text" name="job_title_ar" class="form-control">
                </div>
            </div>

            <!-- Section 2: Identification & Official Docs -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">2. الأوراق الثبوتية والرسمية</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Column 1: Numbers -->
                <div class="flex flex-col gap-6">
                    <div>
                        <label class="form-label">رقم الهوية</label>
                        <input type="text" name="national_id" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">رقم الجواز</label>
                        <input type="text" name="passport_number" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                    </div>
                    <div>
                        <label class="form-label">رقم الشهادة الصحية</label>
                        <input type="text" name="health_cert_number" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                    </div>
                </div>

                <!-- Column 2: Expiry Dates -->
                <div class="flex flex-col gap-6">
                    <div>
                        <label class="form-label">تاريخ انتهاء الهوية</label>
                        <input type="date" name="national_id_expiry" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">تاريخ انتهاء الجواز</label>
                        <input type="date" name="passport_expiry" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">تاريخ انتهاء الشهادة الصحية</label>
                        <input type="date" name="health_cert_expiry" class="form-control">
                    </div>
                </div>

                <!-- Column 3: Attachments -->
                <div class="flex flex-col gap-6">
                    <div>
                        <label class="form-label">مرفق الهوية</label>
                        <input type="file" name="national_id_attachment" class="w-full text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-slate-100 file:text-primary">
                    </div>
                    <div>
                        <label class="form-label">مرفق الجواز</label>
                        <input type="file" name="passport_attachment" class="w-full text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-slate-100 file:text-primary">
                    </div>
                    <div>
                        <label class="form-label">مرفق الشهادة الصحية</label>
                        <input type="file" name="health_cert_attachment" class="w-full text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-slate-100 file:text-primary">
                    </div>
                </div>
            </div>

            <!-- Section 3: App Login Info -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">3. بيانات الدخول (التطبيق)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="form-label">رقم الموظف <span class="text-slate-400 font-normal">(اسم المستخدم)</span> <span class="text-red-500">*</span></label>
                    <input type="text" name="employee_code" minlength="4" required placeholder="EMP001" class="form-control">
                </div>
                <div>
                    <label class="form-label">كلمة المرور <span class="text-red-500">*</span></label>
                    <input type="password" name="password" minlength="6" required class="form-control">
                </div>
                <div>
                    <label class="form-label">البريد الإلكتروني <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                </div>
            </div>

            <!-- Section 4: Personal Info & Contact -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">4. البيانات الشخصية والتواصل</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="date_of_birth" id="dob_input" class="form-control">
                </div>
                <div>
                    <label class="form-label">العمر بالسنة</label>
                    <input type="text" id="age_output" readonly placeholder="يحسب تلقائياً" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-secondary cursor-not-allowed">
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">الكفيل السابق</label>
                    <input type="text" name="previous_sponsor" class="form-control">
                </div>
                <div>
                    <label class="form-label">رقم الموبايل 1 <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" required class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                </div>
                <div>
                    <label class="form-label">رقم الموبايل 2</label>
                    <input type="text" name="phone_2" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">الجنسية</label>
                    <input type="text" name="nationality" class="form-control">
                </div>
                <!-- hire_date moved to Section 5 -->
            </div>

            <!-- Section 5: Financials & Allowances -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">5. الرواتب والبدلات</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="form-label">تاريخ التعيين <span class="text-red-500">*</span></label>
                    <input type="date" name="hire_date" id="hire_date_input" required class="form-control">
                </div>
                <div>
                    <label class="form-label">الأيام المتبقية للشهر من التعيين</label>
                    <input type="text" id="remaining_days_output" readonly placeholder="يحسب تلقائياً" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-secondary cursor-not-allowed">
                </div>
                <div>
                    <label class="form-label">أيام العمل من بداية التعيين</label>
                    <input type="text" id="total_days_since_hire" readonly placeholder="يحسب تلقائياً" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-secondary cursor-not-allowed">
                </div>
                <div>
                    <label class="form-label">الراتب الأساسي <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" step="0.01" name="base_salary" required class="w-full border border-slate-200 rounded-xl px-4 py-2 pr-10 focus:border-primary focus:ring-primary/20">
                        <span class="absolute right-3 top-2.5 text-slate-400 select-none font-bold">ر.س</span>
                    </div>
                </div>
                <div>
                    <label class="form-label">النسبة %</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="percentage" max="100" class="w-full border border-slate-200 rounded-xl px-4 py-2 pr-10 focus:border-primary focus:ring-primary/20">
                        <span class="absolute right-3 top-2.5 text-slate-400 select-none font-bold">%</span>
                    </div>
                </div>
                <div>
                    <label class="form-label">بدل سكن</label>
                    <input type="number" step="0.01" name="housing_allowance" class="form-control">
                </div>
                <div>
                    <label class="form-label">أيام العمل في الشهر</label>
                    <input type="number" step="1" name="working_days" value="26" class="form-control">
                </div>
                <div>
                    <label class="form-label">ساعات العمل في اليوم</label>
                    <input type="number" step="1" name="daily_work_hours" value="8" class="form-control">
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">عنوان السكن</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">رابط السكن (Google Maps)</label>
                    <input type="url" name="address_link" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr" placeholder="https://maps.google.com/...">
                </div>
            </div>

            <!-- Section 6: Insurance & Contracts -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">6. التأمين والعقود</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-3 mb-2 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <label class="block text-sm font-bold text-blue-800 mb-2">ربط بنموذج عقد لتتمكن من طباعته</label>
                    <select name="contract_template_id" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-blue-500 focus:ring-blue-500/20">
                        <option value="">-- بدون ربط عقد --</option>
                        @foreach($contractTemplates as $template)
                            <option value="{{ $template->id }}" {{ old('contract_template_id') == $template->id ? 'selected' : '' }}>{{ $template->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">اسم التأمين الصحي</label>
                    <input type="text" name="insurance_name" class="form-control">
                </div>
                <div>
                    <label class="form-label">تاريخ بدايه التأمين</label>
                    <input type="date" name="insurance_start_date" class="form-control">
                </div>
                <div>
                    <label class="form-label">تاريخ انتهاء التأمين</label>
                    <input type="date" name="insurance_expiry_date" class="form-control">
                </div>

                <div class="lg:col-span-1">
                    <label class="form-label">صورة/مرفق العقد (بعد التوقيع)</label>
                    <input type="file" name="contract_attachment" class="mt-2 w-full text-sm file:mr-2 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-100 file:text-primary">
                </div>
                <div>
                    <label class="form-label">تاريخ انتهاء العقد</label>
                    <input type="date" name="contract_expiry_date" class="form-control">
                </div>
                <div>
                    <label class="form-label">تفعيل الحساب؟</label>
                    <select name="is_active" class="form-control">
                        <option value="1">تنشيط الموظف</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>
            </div>

            <!-- Section 7: System Account & Permissions -->
            <div x-data="{ createAccount: false, selectedRole: 'order_staff' }">
                <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">7. حساب النظام والصلاحيات <span class="text-sm font-normal text-slate-400">(اختياري)</span></h3>
                
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="create_system_account" value="1" x-model="createAccount"
                               class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary/20">
                        <div>
                            <span class="font-bold text-primary text-sm">إنشاء حساب مستخدم في النظام</span>
                            <p class="text-xs text-secondary mt-0.5">سيتم إنشاء حساب دخول للنظام بنفس البريد الإلكتروني وكلمة المرور المدخلة أعلاه، مع تعيين الصلاحية المختارة.</p>
                        </div>
                    </label>
                </div>

                <div x-show="createAccount" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label class="form-label">الصلاحية في النظام <span class="text-red-500">*</span></label>
                        <select name="system_role" x-model="selectedRole" class="form-control">
                            <option value="order_staff">موظف طلبات</option>
                            <option value="kitchen_staff">موظف مطبخ الشيشة</option>
                            <option value="barista_staff">موظف مطبخ الباريستا</option>
                            <option value="restaurant_staff">موظف مطبخ المطعم</option>
                            <option value="delivery_staff">موظف توصيل</option>
                            <option value="inventory_staff">موظف الجرد</option>
                            <option value="reception_staff">موظف استقبال</option>
                            <option value="greeting_staff">موظف تحايا</option>
                            <option value="daily_purchases_staff">موظف مشتريات الطلبات اليومية</option>
                            <option value="dynamic_kitchen_staff">موظف مطبخ ديناميكي</option>
                            <option value="admin">مدير</option>
                        </select>
                    </div>

                    <!-- Kitchen selection for dynamic_kitchen_staff -->
                    <div x-show="selectedRole === 'dynamic_kitchen_staff'" x-transition>
                        <label class="form-label">المطبخ <span class="text-red-500">*</span></label>
                        <select name="kitchen_slug" class="form-control">
                            <option value="">-- اختر المطبخ --</option>
                            @foreach(\App\Models\Kitchen::where('is_active', true)->get() as $kitchen)
                                <option value="{{ $kitchen->slug }}">{{ $kitchen->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assigned kitchens for delivery_staff -->
                    <div x-show="selectedRole === 'delivery_staff'" x-transition class="lg:col-span-2">
                        <label class="form-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z"/></svg>
                            المطابخ المسندة لموظف التوصيل
                        </label>
                        <div class="flex gap-4 flex-wrap mt-2 p-3 bg-slate-50 border border-dashed border-slate-300 rounded-xl">
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="checkbox" name="assigned_kitchens[]" value="shisha" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/20"> مطبخ الشيشة
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="checkbox" name="assigned_kitchens[]" value="barista" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/20"> مطبخ الباريستا
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="checkbox" name="assigned_kitchens[]" value="restaurant" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/20"> مطبخ المطعم
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 border-t border-slate-100 pt-6">

                <button type="submit" class="btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    حفظ بيانات الموظف المضافة
                </button>
            </div>
        </form>
    </div>

    <!-- Script to calculate age -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dobInput = document.getElementById('dob_input');
            const ageOutput = document.getElementById('age_output');

            dobInput.addEventListener('change', function() {
                if(this.value) {
                    const today = new Date();
                    const birthDate = new Date(this.value);
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const m = today.getMonth() - birthDate.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    ageOutput.value = age + ' سنة';
                } else {
                    ageOutput.value = '';
                }
            });

            const hireDateInput = document.getElementById('hire_date_input');
            const remainingDaysOutput = document.getElementById('remaining_days_output');

            function calculateRemainingDays(dateStr) {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                const year = date.getFullYear();
                const month = date.getMonth();
                const day = date.getDate();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const remaining = daysInMonth - day + 1;
                return remaining + ' يوم';
            }

            function calculateTotalDaysSinceHire(dateStr) {
                if (!dateStr) return '';
                const hireDate = new Date(dateStr);
                const today = new Date();
                // Set hours to 0 to compare just the dates accurately
                hireDate.setHours(0,0,0,0);
                today.setHours(0,0,0,0);
                const diffTime = today.getTime() - hireDate.getTime();
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                return diffDays >= 0 ? diffDays + ' يوم' : '0 يوم';
            }

            if(hireDateInput) {
                hireDateInput.addEventListener('change', function() {
                    remainingDaysOutput.value = calculateRemainingDays(this.value);
                    const totalDaysOut = document.getElementById('total_days_since_hire');
                    if (totalDaysOut) totalDaysOut.value = calculateTotalDaysSinceHire(this.value);
                });
            }
        });

        function openQuickDeptModal() {
            document.getElementById('quick_dept_name_ar').value = '';
            document.getElementById('quick_dept_name').value = '';
            document.getElementById('quick_dept_error').style.display = 'none';
            document.getElementById('quickDeptModal').style.display = 'flex';
        }
        function closeQuickDeptModal() {
            document.getElementById('quickDeptModal').style.display = 'none';
        }
        function submitQuickDept() {
            const nameAr = document.getElementById('quick_dept_name_ar').value.trim();
            const name = document.getElementById('quick_dept_name').value.trim();
            const errorEl = document.getElementById('quick_dept_error');
            const btn = document.getElementById('saveQuickDeptBtn');

            if (!nameAr) {
                errorEl.textContent = 'يرجى إدخال اسم القسم بالعربي';
                errorEl.style.display = 'block';
                return;
            }
            errorEl.style.display = 'none';
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';

            fetch('{{ route("hr.departments.quick_store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name_ar: nameAr, name: name })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'حفظ القسم';
                if (data.success && data.department) {
                    const select = document.getElementById('department_select');
                    const option = new Option(data.department.name_ar, data.department.id, true, true);
                    select.add(option);
                    closeQuickDeptModal();
                } else {
                    errorEl.textContent = data.message || 'حدث خطأ أثناء الحفظ';
                    errorEl.style.display = 'block';
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'حفظ القسم';
                errorEl.textContent = 'حدث خطأ في الاتصال';
                errorEl.style.display = 'block';
            });
        }
    </script>

    <!-- Quick Add Department Modal -->
    <div id="quickDeptModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card" style="width: 420px; max-width: 90%; position: relative;">
            <button type="button" onclick="closeQuickDeptModal()" style="position: absolute; top: 15px; left: 15px; background: none; border: none; color: var(--text-secondary); font-size: 1.2rem; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 style="color: var(--primary-color); margin-bottom: 15px; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <i class="fa-solid fa-sitemap"></i> إضافة قسم جديد
            </h3>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">اسم القسم (عربي) <span style="color: red;">*</span></label>
                <input type="text" id="quick_dept_name_ar" class="form-control" placeholder="مثال: الاستقبال، المحاسبة...">
                <span id="quick_dept_error" style="color: red; font-size: 0.8rem; display: none; margin-top: 4px;"></span>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">اسم القسم (إنجليزي - اختياري)</label>
                <input type="text" id="quick_dept_name" class="form-control" placeholder="e.g. Accounting...">
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeQuickDeptModal()" class="btn btn-outline">إلغاء</button>
                <button type="button" id="saveQuickDeptBtn" onclick="submitQuickDept()" class="btn" style="background: linear-gradient(135deg, #4CAF50, #2E7D32); color: white; border: none; font-weight: bold; padding: 8px 20px;">
                    حفظ القسم
                </button>
            </div>
        </div>
    </div>
</x-layouts.admin>


