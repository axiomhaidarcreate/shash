<x-layouts.admin>
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1><i class="fa-solid fa-sitemap" style="color: var(--primary-color);"></i> إدارة الأقسام</h1>
            <p>عرض وإدارة أقسام العمل في المنشأة</p>
        </div>
        <button onclick="document.getElementById('addDeptModal').style.display='flex'" class="btn" style="background: linear-gradient(135deg, #4CAF50, #2E7D32); color: white; border: none;">
            <i class="fa-solid fa-plus-circle"></i> إضافة قسم جديد
        </button>
    </div>

    @if(session('success'))
        <div style="background: rgba(76, 175, 80, 0.15); border: 1px solid rgba(76, 175, 80, 0.4); color: #4CAF50; padding: 12px 16px; border-radius: 8px; margin-bottom: 1.5rem; font-weight: bold;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم القسم (عربي)</th>
                        <th>الاسم (إنجليزي)</th>
                        <th>عدد الموظفين</th>
                        <th>الحالة</th>
                        <th style="text-align: left;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $index => $dept)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong style="color: var(--text-primary); font-size: 1rem;">{{ $dept->name_ar }}</strong></td>
                            <td style="color: var(--text-secondary);">{{ $dept->name ?? '—' }}</td>
                            <td>
                                <span class="badge" style="background: rgba(33, 150, 243, 0.15); color: #2196F3; border: 1px solid rgba(33, 150, 243, 0.3); font-size: 0.85rem;">
                                    {{ $dept->employees_count }} موظف
                                </span>
                            </td>
                            <td>
                                @if($dept->is_active)
                                    <span class="badge" style="background: rgba(76, 175, 80, 0.15); color: #4CAF50; border: 1px solid rgba(76, 175, 80, 0.3);">نشط</span>
                                @else
                                    <span class="badge" style="background: rgba(244, 67, 54, 0.15); color: #F44336; border: 1px solid rgba(244, 67, 54, 0.3);">معطل</span>
                                @endif
                            </td>
                            <td style="text-align: left;">
                                <form action="{{ route('hr.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline" style="color: #F44336; border-color: rgba(244, 67, 54, 0.3);" title="حذف القسم">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                لا توجد أقسام مسجلة حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Add Department -->
    <div id="addDeptModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card" style="width: 450px; max-width: 90%; position: relative;">
            <button onclick="document.getElementById('addDeptModal').style.display='none'" style="position: absolute; top: 15px; left: 15px; background: none; border: none; color: var(--text-secondary); font-size: 1.2rem; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 style="color: var(--primary-color); margin-bottom: 20px; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <i class="fa-solid fa-sitemap"></i> إضافة قسم جديد
            </h3>
            <form action="{{ route('hr.departments.store') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">اسم القسم (بالعربي) <span style="color: red;">*</span></label>
                    <input type="text" name="name_ar" required class="form-control" placeholder="مثال: مطبخ الباريستا، الصيانة...">
                </div>
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">اسم القسم (بالإنجليزي) <span style="color: var(--text-secondary); font-size: 0.8rem;">(اختياري)</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Barista, Maintenance...">
                </div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">الوصف <span style="color: var(--text-secondary); font-size: 0.8rem;">(اختياري)</span></label>
                    <textarea name="description" class="form-control" rows="2" placeholder="وصف مهام القسم..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('addDeptModal').style.display='none'" class="btn btn-outline">إلغاء</button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #4CAF50, #2E7D32); color: white; border: none; font-weight: bold; padding: 8px 20px;">
                        <i class="fa-solid fa-check"></i> حفظ القسم
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
