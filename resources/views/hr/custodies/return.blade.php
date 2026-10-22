<x-layouts.admin>
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('hr.custodies.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-secondary hover:bg-slate-50 hover:text-primary transition shadow-sm border border-slate-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-primary">استرجاع عهدة (فك ارتباط)</h2>
            <p class="text-secondary mt-1">استلام عهدة من موظف وتوثيق حالة التسليم.</p>
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

    <div class="max-w-4xl bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:p-8">
        
        <!-- Selection -->
        <div class="mb-8 bg-slate-50 p-6 rounded-2xl border border-slate-200">
            <label class="form-label">اختر العهدة المطلوب استرجاعها <span class="text-red-500">*</span></label>
            <form id="selectForm" action="{{ route('hr.custodies.return') }}" method="GET">
                <select name="custody_id" onchange="document.getElementById('selectForm').submit()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                    <option value="">— اختر العهدة —</option>
                    @foreach($assignments as $a)
                        <option value="{{ $a->custody_id }}" {{ request('custody_id') == $a->custody_id ? 'selected' : '' }}>
                            {{ $a->custody->name }} [{{ $a->custody->code }}] — مستلمة لدى: {{ $a->employee->full_name_ar }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($selectedAssignment)
        <form action="{{ route('hr.custodies.return.store') }}" method="POST" id="returnForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="assignment_id" value="{{ $selectedAssignment->id }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="form-label">الموظف (المستلم الحالي)</label>
                    <input type="text" disabled value="{{ $selectedAssignment->employee->full_name_ar }}" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-600 cursor-not-allowed">
                </div>
                
                <div>
                    <label class="form-label">تاريخ الاسترجاع <span class="text-red-500">*</span></label>
                    <input type="date" name="returned_at" value="{{ old('returned_at', date('Y-m-d')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">ملاحظات وتقرير الاسترجاع</label>
                    <textarea name="return_notes" rows="3" placeholder="هل يوجد أي نواقص أو تلفيات في العهدة؟ اذكرها هنا..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">{{ old('return_notes') }}</textarea>
                </div>
            </div>

            <!-- Attachments -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-primary mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-slate-400"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /></svg>
                    صور مرفقات العهدة بعد الاستلام (لإثبات الحالة)
                </label>
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:bg-slate-50 transition cursor-pointer relative" onclick="document.getElementById('attachments').click()">
                    <input type="file" name="attachments[]" id="attachments" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="updateFileCount(this)">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    </div>
                    <p class="font-bold text-primary text-xs mb-1" id="fileText">اضغط هنا لرفع الصور (كحد أقصى 10 صور)</p>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-200">
                <label class="block text-sm font-bold text-primary mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-indigo-500"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                    توقيع المسؤول عن استلام العهدة (الاسترجاع) <span class="text-red-500">*</span>
                </label>
                
                <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl relative overflow-hidden" style="height: 200px;">
                    <canvas id="signatureCanvas" class="w-full h-full cursor-crosshair"></canvas>
                </div>
                
                <div class="flex justify-end mt-3">
                    <button type="button" id="clearSignature" class="text-xs font-bold text-secondary hover:text-red-500 transition px-3 py-1 bg-white border border-slate-200 rounded shadow-sm">مسح التوقيع وإعادة المحاولة</button>
                </div>
                
                <input type="hidden" name="return_signature" id="signatureInput" value="">
            </div>

            <hr class="border-slate-100 my-8">

            <div class="flex justify-end gap-3">
                <a href="{{ route('hr.custodies.index') }}" class="px-6 py-3 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition text-sm">إلغاء</a>
                <button type="button" onclick="submitForm()" class="px-8 py-3 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-md text-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    تأكيد واسترجاع العهدة
                </button>
            </div>
        </form>
        @else
        <div class="text-center py-10">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <p class="text-secondary font-bold">يرجى تحديد إحدى العهد من القائمة أعلاه لبدء إجراءات الاسترجاع.</p>
        </div>
        @endif
    </div>

    @if($selectedAssignment)
    <!-- Canvas Signature Logic & File Input Logic -->
    <script>
        function updateFileCount(input) {
            const count = input.files ? input.files.length : 0;
            const textElement = document.getElementById('fileText');
            if (count > 0) {
                if(count > 10) {
                    alert('عذراً، الحد الأقصى هو 10 صور فقط.');
                    input.value = '';
                    textElement.innerText = "اضغط هنا لرفع الصور (كحد أقصى 10 صور)";
                } else {
                    textElement.innerText = `تم تحديد ${count} صور`;
                    textElement.classList.add('text-indigo-600');
                }
            } else {
                textElement.innerText = "اضغط هنا لرفع الصور (كحد أقصى 10 صور)";
                textElement.classList.remove('text-indigo-600');
            }
        }

        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        const clearBtn = document.getElementById('clearSignature');
        const form = document.getElementById('returnForm');
        const sigInput = document.getElementById('signatureInput');
        
        let isDrawing = false;
        let hasSignature = false;

        function resizeCanvas() {
            const rect = canvas.parentElement.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#1e293b';
        }
        
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        function getCoordinates(e) {
            const rect = canvas.getBoundingClientRect();
            let x, y;
            if (e.touches && e.touches.length > 0) {
                x = e.touches[0].clientX - rect.left;
                y = e.touches[0].clientY - rect.top;
            } else {
                x = e.clientX - rect.left;
                y = e.clientY - rect.top;
            }
            return [x, y];
        }

        function startDrawing(e) {
            isDrawing = true;
            hasSignature = true;
            const [x, y] = getCoordinates(e);
            ctx.beginPath();
            ctx.moveTo(x, y);
            e.preventDefault();
        }

        function draw(e) {
            if (!isDrawing) return;
            const [x, y] = getCoordinates(e);
            ctx.lineTo(x, y);
            ctx.stroke();
            e.preventDefault();
        }

        function stopDrawing() {
            isDrawing = false;
            ctx.closePath();
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        canvas.addEventListener('touchstart', startDrawing, {passive: false});
        canvas.addEventListener('touchmove', draw, {passive: false});
        canvas.addEventListener('touchend', stopDrawing);

        clearBtn.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasSignature = false;
        });

        function submitForm() {
            if (!hasSignature) {
                alert('يرجى إضافة توقيع الاسترجاع أولاً قبل الاعتماد.');
                return;
            }
            sigInput.value = canvas.toDataURL('image/png');
            form.submit();
        }
    </script>
    @endif
</x-layouts.admin>


