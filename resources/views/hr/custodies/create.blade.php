<x-layouts.admin>
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('hr.custodies.index') }}" class="btn btn-outline" style="padding: 0.5rem 1rem;">
            <i class="fa-solid fa-arrow-right"></i> عودة
        </a>
        <div>
            <h2 class="text-2xl font-bold text-primary">إضافة عهدة جديدة</h2>
            <p class="text-secondary mt-1">سجل تفاصيل العهدة لإضافتها في النظام لتكون متاحة للربط.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-100 font-bold text-sm mb-6">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <form action="{{ route('hr.custodies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="form-label">رقم العهدة (فريد) <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="مثال: C-101" dir="ltr" class="form-control">
                </div>
                
                <div>
                    <label class="form-label">نوع العهدة <span class="text-red-500">*</span></label>
                    <select name="type" required class="form-control">
                        <option value="">— اختر النوع —</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">اسم العهدة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: لابتوب ديل أسود i7" class="form-control">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">تفاصيل / مواصفات العهدة</label>
                    <textarea name="details" rows="3" placeholder="اكتب أي ملاحظات أو مواصفات خاصة بالعهدة (مثل رقم السيريال أو الملحقات)..." class="form-control">{{ old('details') }}</textarea>
                </div>
            </div>

            <!-- Attachments -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-primary mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-slate-400"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                    صور مرفقات العهدة (اختياري)
                </label>
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center hover:bg-slate-50 transition cursor-pointer relative" onclick="document.getElementById('attachments').click()">
                    <input type="file" name="attachments[]" id="attachments" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="updateFileCount(this)">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    </div>
                    <p class="font-bold text-primary text-sm mb-1" id="fileText">اضغط هنا لرفع الصور، أو اسحبها وأفلتها</p>
                    <p class="text-xs text-secondary">مسموح بحد أقصى 10 صور (JPG, PNG)</p>
                </div>
            </div>

            <hr class="border-slate-100 my-8">

            <div class="flex justify-end gap-3">
                <a href="{{ route('hr.custodies.index') }}" class="px-6 py-3 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition text-sm">إلغاء</a>
                <button type="submit" class="px-8 py-3 rounded-xl font-bold text-white bg-primary hover:bg-primary-dark transition shadow-md text-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    حفظ وإضافة العهدة
                </button>
            </div>
        </form>
    </div>

    <script>
        function updateFileCount(input) {
            const count = input.files ? input.files.length : 0;
            const textElement = document.getElementById('fileText');
            if (count > 0) {
                if(count > 10) {
                    alert('عذراً، الحد الأقصى هو 10 صور فقط.');
                    input.value = '';
                    textElement.innerText = "اضغط هنا لرفع الصور، أو اسحبها وأفلتها";
                } else {
                    textElement.innerText = `تم تحديد ${count} صور`;
                    textElement.classList.add('text-indigo-600');
                }
            } else {
                textElement.innerText = "اضغط هنا لرفع الصور، أو اسحبها وأفلتها";
                textElement.classList.remove('text-indigo-600');
            }
        }
    </script>
</x-layouts.admin>


