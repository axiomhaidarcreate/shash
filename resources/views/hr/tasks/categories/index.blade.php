<x-layouts.admin>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-primary">تصنيفات المهام</h2>
            <p class="text-secondary mt-1">إدارة أنواع وتصنيفات المهام (مثال: نظافة، صيانة، ديكور)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('hr.tasks.index') }}" class="bg-slate-100 text-primary px-5 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition-colors flex items-center gap-2 border border-slate-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                العودة للمهام
            </a>
            <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" class="bg-primary text-white px-5 py-2.5 rounded-xl font-bold hover:bg-primary-dark transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                إضافة تصنيف
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="py-4 px-6 text-secondary font-bold text-sm">التصنيف</th>
                        <th class="py-4 px-6 text-secondary font-bold text-sm">اللون</th>
                        <th class="py-4 px-6 text-secondary font-bold text-sm">عدد المهام</th>
                        <th class="py-4 px-6 text-secondary font-bold text-sm">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-6">
                            <span class="font-bold text-primary">{{ $category->name }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($category->color)
                                <div class="w-6 h-6 rounded-full border border-slate-200" style="background-color: {{ $category->color }}"></div>
                            @else
                                <span class="text-slate-400 text-sm">لا يوجد</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <span class="bg-indigo-50 text-indigo-600 py-1 px-3 rounded-lg font-bold text-sm">{{ $category->tasks_count }} مهمة</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <button onclick="editCategory('{{ $category->id }}', '{{ $category->name }}', '{{ $category->color }}')" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                </button>
                                <form action="{{ route('hr.tasks.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-secondary">
                            لا توجد تصنيفات مضافة حتى الآن.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addCategoryModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-lg text-primary">إضافة تصنيف جديد</h3>
                <button onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('hr.tasks.categories.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="form-label">اسم التصنيف <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="form-label">اللون (اختياري)</label>
                        <input type="color" name="color" class="w-full h-12 rounded-xl cursor-pointer">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-xl font-bold hover:bg-primary-dark transition-colors">حفظ التصنيف</button>
                    <button type="button" onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-3 rounded-xl font-bold hover:bg-slate-200 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editCategoryModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-lg text-primary">تعديل التصنيف</h3>
                <button onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="editCategoryForm" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="form-label">اسم التصنيف <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="form-label">اللون (اختياري)</label>
                        <input type="color" name="color" id="edit_color" class="w-full h-12 rounded-xl cursor-pointer">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-xl font-bold hover:bg-primary-dark transition-colors">تحديث التصنيف</button>
                    <button type="button" onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-3 rounded-xl font-bold hover:bg-slate-200 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editCategory(id, name, color) {
            document.getElementById('edit_name').value = name;
            if(color) document.getElementById('edit_color').value = color;
            document.getElementById('editCategoryForm').action = '/tasks/categories/' + id;
            document.getElementById('editCategoryModal').classList.remove('hidden');
        }
    </script>
</x-layouts.admin>


