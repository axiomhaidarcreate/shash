<x-layouts.admin>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="text-2xl font-bold text-primary">قوالب العقود</h2>
            <p class="text-secondary mt-1">إنشاء وتعديل العقود لطباعتها للموظفين الجدد</p>
        </div>
        <a href="{{ route('hr.contract_templates.create') }}" class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 transition-colors shadow-sm self-start md:self-auto">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            إضافة قالب جديد
        </a>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-secondary">
                <thead class="text-xs text-slate-400 bg-slate-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">مسمى العقد</th>
                        <th scope="col" class="px-6 py-4 font-semibold">تاريخ الإنشاء</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates as $template)
                    <tr class="bg-white hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 font-bold text-primary">{{ $template->title }}</td>
                        <td class="px-6 py-4 text-secondary">{{ $template->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-3">
                                <!-- Print/Show -->
                                <a href="{{ route('hr.contract_templates.show', $template) }}" class="text-primary hover:text-primary-dark transition" title="عرض/طباعة">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0v2.796c0 1.136.879 2.073 2.006 2.152a48.24 48.24 0 006.488 0c1.127-.08 2.006-1.016 2.006-2.152V10.25z" /></svg>
                                </a>
                                <!-- Edit -->
                                <a href="{{ route('hr.contract_templates.edit', $template) }}" class="text-slate-400 hover:text-primary transition" title="تعديل"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg></a>
                                <!-- Delete -->
                                <form action="{{ route('hr.contract_templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد الحذف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-secondary">
                            لا توجد قوالب عقود لعرضها.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-100">{{ $templates->links() }}</div>
    </div>
</x-layouts.admin>


