<x-layouts.admin>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div class="mb-2 text-sm text-secondary">
                <a href="{{ route('hr.employees.index') }}" class="hover:text-primary transition">الموظفين</a> / 
                <span class="text-primary font-bold">تعديل بيانات الموظف</span>
            </div>
            <h2 class="text-2xl font-bold text-primary mt-2">تعديل الموظف: {{ $employee->full_name_ar ?? $employee->full_name }}</h2>
        </div>
        <a href="{{ route('hr.employees.index') }}" class="btn btn-outline">
            رجوع
        </a>
    </div>

    <div class="card">
        <form action="{{ route('hr.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
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
            <div class="mb-8 flex flex-col items-center gap-6 border-b border-slate-200 pb-8">
                @if($employee->avatar_url)
                    <img id="avatar_preview" src="{{ $employee->avatar_url }}" alt="الصورة الشخصية" class="w-[400px] max-w-full h-[400px] rounded-2xl object-cover shrink-0 border border-slate-200 shadow-sm">
                    <div id="avatar_placeholder" class="hidden w-[400px] max-w-full h-[400px] rounded-2xl bg-slate-100 items-center justify-center shrink-0 border border-slate-200 text-slate-400 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    </div>
                @else
                    <img id="avatar_preview" src="" alt="الصورة الشخصية" class="hidden w-[400px] max-w-full h-[400px] rounded-2xl object-cover shrink-0 border border-slate-200 shadow-sm">
                    <div id="avatar_placeholder" class="w-[400px] max-w-full h-[400px] rounded-2xl bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200 text-slate-400 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    </div>
                @endif
                <div class="w-full text-center mt-2">
                    <label class="form-label">تحديث الصورة الشخصية (اختياري)</label>
                    <input type="file" id="avatar_input" name="avatar" accept="image/*" class="w-full md:w-1/2 mx-auto border border-slate-200 rounded-xl px-4 py-2 focus:border-primary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer focus:outline-none">
                </div>
            </div>

            <!-- Section 1: Basic Info -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">1. البيانات الأساسية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="form-label">الاسم الكامل (عربي) <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name_ar" value="{{ $employee->full_name_ar }}" required class="form-control">
                </div>
                <div>
                    <label class="form-label">الاسم الكامل (إنجليزي) <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ $employee->full_name }}" required class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                </div>
                <div>
                    <label class="form-label">القسم</label>
                    <select name="department_id" class="form-control">
                        <option value="">— غير محدد —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">المسمى الوظيفي</label>
                    <input type="text" name="job_title_ar" value="{{ $employee->job_title_ar }}" class="form-control">
                </div>
            </div>

            <!-- Section 2: ID Docs -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">2. الأوراق الثبوتية والرسمية</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Column 1: Numbers -->
                <div class="flex flex-col gap-6">
                    <div>
                        <label class="form-label">رقم الهوية</label>
                        <input type="text" name="national_id" value="{{ $employee->national_id }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">رقم الجواز</label>
                        <input type="text" name="passport_number" value="{{ $employee->passport_number }}" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                    </div>
                    <div>
                        <label class="form-label">رقم الشهادة الصحية</label>
                        <input type="text" name="health_cert_number" value="{{ $employee->health_cert_number }}" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                    </div>
                </div>

                <!-- Column 2: Expiry Dates -->
                <div class="flex flex-col gap-6">
                    <div>
                        <label class="form-label">تاريخ انتهاء الهوية</label>
                        <input type="date" name="national_id_expiry" value="{{ $employee->national_id_expiry ? \Carbon\Carbon::parse($employee->national_id_expiry)->format('Y-m-d') : '' }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">تاريخ انتهاء الجواز</label>
                        <input type="date" name="passport_expiry" value="{{ $employee->passport_expiry ? \Carbon\Carbon::parse($employee->passport_expiry)->format('Y-m-d') : '' }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">تاريخ انتهاء الشهادة الصحية</label>
                        <input type="date" name="health_cert_expiry" value="{{ $employee->health_cert_expiry ? \Carbon\Carbon::parse($employee->health_cert_expiry)->format('Y-m-d') : '' }}" class="form-control">
                    </div>
                </div>

                <!-- Column 3: Attachments -->
                <div class="flex flex-col gap-6">
                    <div>
                        <label class="form-label">مرفق الهوية</label>
                        <div class="flex items-center gap-2">
                            <input type="file" name="national_id_attachment" class="w-full text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-slate-100 file:text-primary">
                            @if($employee->national_id_attachment)
                                <a href="{{ $employee->national_id_attachment }}" target="_blank" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-bold whitespace-nowrap">عرض المرفق</a>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="form-label">مرفق الجواز</label>
                        <div class="flex items-center gap-2">
                            <input type="file" name="passport_attachment" class="w-full text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-slate-100 file:text-primary">
                            @if($employee->passport_attachment)
                                <a href="{{ $employee->passport_attachment }}" target="_blank" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-bold whitespace-nowrap">عرض المرفق</a>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="form-label">مرفق الشهادة الصحية</label>
                        <div class="flex items-center gap-2">
                            <input type="file" name="health_cert_attachment" class="w-full text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-slate-100 file:text-primary">
                            @if($employee->health_cert_attachment)
                                <a href="{{ $employee->health_cert_attachment }}" target="_blank" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-bold whitespace-nowrap">عرض المرفق</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: App Login Info -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">3. بيانات الدخول (التطبيق)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="form-label">رقم الموظف <span class="text-slate-400 font-normal">(اسم المستخدم)</span> <span class="text-red-500">*</span></label>
                    <input type="text" name="employee_code" value="{{ $employee->employee_code }}" minlength="4" required class="form-control">
                </div>
                <div>
                    <label class="form-label">كلمة المرور <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="password" value="{{ $employee->password }}" minlength="6" placeholder="كلمة المرور" class="form-control" dir="ltr">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400">عدّل للتغيير</span>
                    </div>
                </div>
                <div>
                    <label class="form-label">البريد الإلكتروني <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ $employee->email }}" required class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                </div>
            </div>

            <!-- Section 4: Personal Info & Contact -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">4. البيانات الشخصية والتواصل</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="date_of_birth" id="dob_input" value="{{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') : '' }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">العمر بالسنة</label>
                    <input type="text" id="age_output" readonly placeholder="يحسب تلقائياً" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-secondary cursor-not-allowed">
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">الكفيل السابق</label>
                    <input type="text" name="previous_sponsor" value="{{ $employee->previous_sponsor }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">رقم الموبايل 1 <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ $employee->phone }}" required class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                </div>
                <div>
                    <label class="form-label">رقم الموبايل 2</label>
                    <input type="text" name="phone_2" value="{{ $employee->phone_2 }}" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr">
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">الجنسية</label>
                    <input type="text" name="nationality" value="{{ $employee->nationality }}" class="form-control">
                </div>
                <!-- hire_date moved to Section 5 -->
            </div>

            <!-- Section 5: Financials & Allowances -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">5. الرواتب والبدلات</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="form-label">تاريخ التعيين <span class="text-red-500">*</span></label>
                    <input type="date" name="hire_date" id="hire_date_input" value="{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : '' }}" required class="form-control">
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
                        <input type="number" step="0.01" name="base_salary" value="{{ $employee->base_salary }}" required class="w-full border border-slate-200 rounded-xl px-4 py-2 pr-10 focus:border-primary focus:ring-primary/20">
                        <span class="absolute right-3 top-2.5 text-slate-400 select-none font-bold">ر.س</span>
                    </div>
                </div>
                <div>
                    <label class="form-label">النسبة %</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="percentage" value="{{ $employee->percentage }}" max="100" class="w-full border border-slate-200 rounded-xl px-4 py-2 pr-10 focus:border-primary focus:ring-primary/20">
                        <span class="absolute right-3 top-2.5 text-slate-400 select-none font-bold">%</span>
                    </div>
                </div>
                <div>
                    <label class="form-label">بدل سكن</label>
                    <input type="number" step="0.01" name="housing_allowance" value="{{ $employee->housing_allowance }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">أيام العمل في الشهر</label>
                    <input type="number" step="1" name="working_days" value="{{ $employee->working_days ?? 26 }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">ساعات العمل في اليوم</label>
                    <input type="number" step="1" name="daily_work_hours" value="{{ $employee->daily_work_hours ?? 8 }}" class="form-control">
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">عنوان السكن</label>
                    <input type="text" name="address" value="{{ $employee->address }}" class="form-control">
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">رابط السكن (Google Maps)</label>
                    <input type="url" name="address_link" value="{{ $employee->address_link }}" class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 text-left" dir="ltr" placeholder="https://maps.google.com/...">
                </div>
            </div>

            <!-- Section 6: Insurance & Contracts -->
            <h3 style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">6. التأمين والعقود</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-3 mb-2 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <label class="block text-sm font-bold text-blue-800 mb-2">ربط بنموذج عقد لتتمكن من طباعته</label>
                    <div class="flex flex-col md:flex-row gap-4 md:items-center">
                        <select name="contract_template_id" class="flex-1 border border-slate-200 rounded-xl px-4 py-2 focus:border-blue-500 focus:ring-blue-500/20">
                            <option value="">-- بدون ربط عقد --</option>
                            @foreach($contractTemplates as $template)
                                <option value="{{ $template->id }}" {{ old('contract_template_id', $employee->contract_template_id) == $template->id ? 'selected' : '' }}>{{ $template->title }}</option>
                            @endforeach
                        </select>
                        @if($employee->contract_template_id)
                            <a href="{{ route('hr.employees.printContract', $employee) }}" target="_blank" class="bg-white border border-blue-200 text-blue-700 hover:bg-blue-100 px-4 py-2 rounded-xl text-sm font-bold shrink-0 transition flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0v2.796c0 1.136.879 2.073 2.006 2.152a48.24 48.24 0 006.488 0c1.127-.08 2.006-1.016 2.006-2.152V10.25z" /></svg>
                                طباعة العقد
                            </a>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="form-label">اسم التأمين الصحي</label>
                    <input type="text" name="insurance_name" value="{{ $employee->insurance_name }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">تاريخ بدايه التأمين</label>
                    <input type="date" name="insurance_start_date" value="{{ $employee->insurance_start_date ? \Carbon\Carbon::parse($employee->insurance_start_date)->format('Y-m-d') : '' }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">تاريخ انتهاء التأمين</label>
                    <input type="date" name="insurance_expiry_date" value="{{ $employee->insurance_expiry_date ? \Carbon\Carbon::parse($employee->insurance_expiry_date)->format('Y-m-d') : '' }}" class="form-control">
                </div>

                <div class="lg:col-span-1">
                    <label class="form-label">صورة/مرفق العقد (بعد التوقيع)</label>
                    <div class="flex flex-col mt-2 gap-2">
                        <input type="file" name="contract_attachment" class="w-full text-sm file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-100 file:text-primary">
                        @if($employee->contract_attachment)
                            <a href="{{ $employee->contract_attachment }}" target="_blank" class="text-xs bg-green-100 text-green-700 px-2 py-2 rounded-xl text-center font-bold inline-block">عرض العقد المرفق</a>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="form-label">تاريخ انتهاء العقد</label>
                    <input type="date" name="contract_expiry_date" value="{{ $employee->contract_expiry_date ? \Carbon\Carbon::parse($employee->contract_expiry_date)->format('Y-m-d') : '' }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">تفعيل الحساب؟</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $employee->is_active ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ !$employee->is_active ? 'selected' : '' }}>مفصول / غير نشط</option>
                    </select>
                </div>

                <!-- Resignation Attachment (Shown conditionally or always optionally) -->
                @if(!$employee->is_active)
                <div class="lg:col-span-3 bg-red-50 p-4 border border-red-100 rounded-xl mt-4">
                    <label class="block text-sm font-bold text-red-800 mb-2">الموظف غير نشط / مفصول: مرفق الإقالة (الاستقالة)</label>
                    <div class="flex flex-col gap-2">
                        <input type="file" name="resignation_attachment" class="w-full text-sm file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-red-100 file:text-red-700">
                        @if($employee->resignation_attachment)
                            <a href="{{ $employee->resignation_attachment }}" target="_blank" class="text-xs bg-red-100 text-red-700 px-2 py-2 rounded-xl border border-red-200 text-center font-bold inline-block">عرض الإقالة/الاستقالة</a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="flex justify-end gap-3 mt-8 border-t border-slate-100 pt-6">
                <button type="submit" class="btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>

    <!-- Script to calculate age -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dobInput = document.getElementById('dob_input');
            const ageOutput = document.getElementById('age_output');

            function calculateAge(dateString) {
                if(!dateString) return '';
                const today = new Date();
                const birthDate = new Date(dateString);
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age + ' سنة';
            }

            // Init
            if(dobInput.value) {
                ageOutput.value = calculateAge(dobInput.value);
            }

            // On Change
            dobInput.addEventListener('change', function() {
                ageOutput.value = calculateAge(this.value);
            });

            // Remaining Days Logic
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
                hireDate.setHours(0,0,0,0);
                today.setHours(0,0,0,0);
                const diffTime = today.getTime() - hireDate.getTime();
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                return diffDays >= 0 ? diffDays + ' يوم' : '0 يوم';
            }

            if(hireDateInput) {
                // Init
                if(hireDateInput.value) {
                    remainingDaysOutput.value = calculateRemainingDays(hireDateInput.value);
                    const totalDaysOut = document.getElementById('total_days_since_hire');
                    if (totalDaysOut) totalDaysOut.value = calculateTotalDaysSinceHire(hireDateInput.value);
                }
                
                hireDateInput.addEventListener('change', function() {
                    remainingDaysOutput.value = calculateRemainingDays(this.value);
                    const totalDaysOut = document.getElementById('total_days_since_hire');
                    if (totalDaysOut) totalDaysOut.value = calculateTotalDaysSinceHire(this.value);
                });
            }

            // Avatar Preview Logic
            const avatarInput = document.getElementById('avatar_input');
            const avatarPreview = document.getElementById('avatar_preview');
            const avatarPlaceholder = document.getElementById('avatar_placeholder');

            if (avatarInput) {
                avatarInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                            avatarPreview.classList.remove('hidden');
                            
                            if (avatarPlaceholder) {
                                avatarPlaceholder.classList.add('hidden');
                                avatarPlaceholder.classList.remove('flex');
                            }
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</x-layouts.admin>


