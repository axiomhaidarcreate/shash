<x-layouts.admin>
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('hr.custodies.index') }}" class="btn btn-outline" style="padding: 0.5rem 1rem;">
            <i class="fa-solid fa-arrow-right"></i> عودة
        </a>
        <div>
            <h2 class="text-2xl font-bold text-primary">ربط عهدة بموظف (تسليم)</h2>
            <p class="text-secondary mt-1">تخصيص وتسليم عهدة متاحة إلى أحد الموظفين وتوثيق ذلك.</p>
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
        <form action="{{ route('hr.custodies.assign.store') }}" method="POST" id="assignForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="form-label">الموظف المستلم <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="empSearch" placeholder="ابحث عن اسم الموظف..." autocomplete="off" class="form-control" oninput="filterDropdown('empSearch','empDropdown','emp-option')" onfocus="showDropdown('empDropdown')">
                        <input type="hidden" name="employee_id" id="employee_id_val">
                        <div id="empDropdown" class="hidden absolute z-20 w-full border border-slate-200 rounded-xl shadow-lg mt-1 max-h-52 overflow-y-auto" style="background-color: var(--bg-card, #fff);">
                            @foreach($employees as $emp)
                                <div class="px-4 py-2.5 text-sm cursor-pointer hover:bg-primary/10 hover:text-primary transition emp-option" data-value="{{ $emp->id }}" data-label="{{ $emp->full_name_ar }} ({{ $emp->employee_code }})" onclick="pickOption('empSearch','employee_id_val','empDropdown',this)">
                                    {{ $emp->full_name_ar }} <span class="text-slate-400 text-xs">({{ $emp->employee_code }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="form-label">العهدة المتاحة <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="custSearch" placeholder="ابحث عن اسم العهدة أو رقمها..." autocomplete="off" class="form-control" oninput="filterDropdown('custSearch','custDropdown','cust-option')" onfocus="showDropdown('custDropdown')">
                        <input type="hidden" name="custody_id" id="custody_id_val" required>
                        <div id="custDropdown" class="hidden absolute z-20 w-full border border-slate-200 rounded-xl shadow-lg mt-1 max-h-52 overflow-y-auto" style="background-color: var(--bg-card, #fff);">
                            @forelse($custodies as $c)
                                <div class="px-4 py-2.5 text-sm cursor-pointer hover:bg-primary/10 hover:text-primary transition cust-option" data-value="{{ $c->id }}" data-label="{{ $c->name }} [{{ $c->code }}]" onclick="pickOption('custSearch','custody_id_val','custDropdown',this)">
                                    {{ $c->name }} <span class="text-slate-400 text-xs">[{{ $c->code }}]</span>
                                </div>
                            @empty
                                <div class="px-4 py-3 text-sm text-slate-400 text-center">لا توجد عهد متاحة حالياً</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label">تاريخ الاستلام <span class="text-red-500">*</span></label>
                    <input type="date" name="assigned_at" value="{{ old('assigned_at', date('Y-m-d')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">تقرير الاستلام / ملاحظات قبل التسليم</label>
                    <textarea name="assignment_report" rows="3" placeholder="حالة العهدة وقت الاستلام، أي خدوش، مستلزمات مرفقة، إلخ..." class="form-control">{{ old('assignment_report') }}</textarea>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="mb-8 p-6 rounded-2xl border border-slate-200" style="background: rgba(0,0,0,0.02);">
                <label class="block text-sm font-bold text-primary mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-indigo-500"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                    توقيع الموظف المستلم <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-secondary mb-4">الرجاء من الموظف التوقيع في المربع أدناه باستخدام الماوس أو اللمس.</p>
                
                <div class="border-2 border-dashed border-slate-300 rounded-xl relative overflow-hidden" style="height: 200px; background-color: var(--bg-color, #f8fafc);">
                    <canvas id="signatureCanvas" class="w-full h-full cursor-crosshair"></canvas>
                </div>
                
                <div class="flex justify-end mt-3">
                    <button type="button" id="clearSignature" class="btn btn-outline text-xs">مسح التوقيع وإعادة المحاولة</button>
                </div>
                
                <input type="hidden" name="signature" id="signatureInput" value="">
            </div>

            <hr class="border-slate-100 my-8">

            <div class="flex justify-end gap-3">
                <a href="{{ route('hr.custodies.index') }}" class="px-6 py-3 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition text-sm">إلغاء</a>
                <button type="button" onclick="submitForm()" class="px-8 py-3 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-md text-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    اعتماد وتسليم العهدة
                </button>
            </div>
        </form>
    </div>

    <!-- Searchable Dropdown Logic -->
    <script>
        function showDropdown(dropId) {
            document.getElementById(dropId).classList.remove('hidden');
        }

        function filterDropdown(inputId, dropId, cls) {
            const q = document.getElementById(inputId).value.toLowerCase();
            const drop = document.getElementById(dropId);
            drop.classList.remove('hidden');
            drop.querySelectorAll('.' + cls).forEach(function(el) {
                const text = el.getAttribute('data-label').toLowerCase();
                el.style.display = text.includes(q) ? '' : 'none';
            });
        }

        function pickOption(inputId, hiddenId, dropId, el) {
            document.getElementById(inputId).value = el.getAttribute('data-label');
            document.getElementById(hiddenId).value = el.getAttribute('data-value');
            document.getElementById(dropId).classList.add('hidden');
        }

        // Also apply emp search using same logic
        function filterSelect(selId, inputId) {
            filterDropdown(inputId, 'empDropdown', 'emp-option');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            ['empDropdown','custDropdown'].forEach(function(id) {
                var drop = document.getElementById(id);
                if (drop && !drop.contains(e.target) && !e.target.matches('input')) {
                    drop.classList.add('hidden');
                }
            });
        });

        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        const clearBtn = document.getElementById('clearSignature');
        const form = document.getElementById('assignForm');
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
            if (!document.getElementById('employee_id_val').value) {
                alert('يرجى اختيار الموظف أولاً.');
                return;
            }
            if (!document.getElementById('custody_id_val').value) {
                alert('يرجى اختيار العهدة أولاً.');
                return;
            }
            if (!hasSignature) {
                alert('يرجى إضافة توقيع الموظف أولاً قبل الاعتماد.');
                return;
            }
            sigInput.value = canvas.toDataURL('image/png');
            form.submit();
        }
    </script>
</x-layouts.admin>


