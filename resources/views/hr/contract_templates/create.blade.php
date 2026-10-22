<x-layouts.admin>
    <div class="flex items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-primary">إضافة قالب عقد</h2>
        </div>
        <a href="{{ route('hr.contract_templates.index') }}" class="bg-white hover:bg-slate-50 text-primary px-4 py-2.5 rounded-xl font-semibold border border-slate-200">رجوع</a>
    </div>

    <div class="card">
        <form action="{{ route('hr.contract_templates.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="form-label">اسم العقد (مثال: عقد عمل جزئي)</label>
                <input type="text" name="title" required class="form-control">
            </div>
            <div class="mb-6">
                <label class="form-label">نص العقد بالمحتوى</label>
                <textarea name="content" required rows="15" class="form-control"></textarea>
                <div class="mt-3 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <p class="text-xs font-bold text-blue-800 mb-2">يمكنك استخدام المتغيرات التالية لكي يتم تعبئتها تلقائياً عند طباعة العقد لكل موظف:</p>
                    <div class="flex flex-wrap gap-2 text-xs font-mono text-blue-700">
                        <span class="bg-blue-100 px-2 py-1 rounded">[اسم الموظف]</span>
                        <span class="bg-blue-100 px-2 py-1 rounded">[الراتب]</span>
                        <span class="bg-blue-100 px-2 py-1 rounded">[رقم الهوية]</span>
                        <span class="bg-blue-100 px-2 py-1 rounded">[تاريخ التعيين]</span>
                        <span class="bg-blue-100 px-2 py-1 rounded">[رقم الجواز]</span>
                        <span class="bg-blue-100 px-2 py-1 rounded">[المسمى الوظيفي]</span>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-8">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-xl font-medium">حفظ القالب</button>
            </div>
        </form>
    </div>
</x-layouts.admin>


