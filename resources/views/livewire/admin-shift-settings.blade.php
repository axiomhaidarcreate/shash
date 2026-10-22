<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2><i class="fa-solid fa-clock"></i> إدارة الفترات المحاسبية وأوقات العمل</h2>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success" style="background: rgba(76,175,80,0.15); color: #4CAF50; padding: 1rem; border-radius: var(--radius-sm); border: 1px solid #4CAF50; margin-bottom: 1rem; font-weight: bold; text-align: center;">
            <i class="fa-solid fa-check-circle"></i> {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger" style="background: rgba(244,67,54,0.15); color: #F44336; padding: 1rem; border-radius: var(--radius-sm); border: 1px solid #F44336; margin-bottom: 1rem; font-weight: bold; text-align: center;">
            <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="layout-grid">
        <!-- Settings Form -->
        <div class="card">
            <h3 style="margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
                <i class="fa-solid fa-cogs"></i> إعدادات الأتمتة (الأوقات التلقائية)
            </h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">
                سيقوم النظام بالفتح أو الإغلاق التلقائي للفترة المحاسبية في الأوقات المحددة بالضبط (بدون أي تأخير).
            </p>

            <div class="form-group">
                <label class="form-label">وقت بدء العمل التلقائي (HH:MM)</label>
                <input type="time" wire:model="shiftStartTime" class="form-control" style="width: 100%;">
                @error('shiftStartTime') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group mt-3">
                <label class="form-label">وقت الإغلاق التلقائي (HH:MM)</label>
                <input type="time" wire:model="shiftEndTime" class="form-control" style="width: 100%;">
                @error('shiftEndTime') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <button wire:click="saveSettings" class="btn btn-primary" style="margin-top: 1rem; width: 100%; padding: 0.8rem;">
                <i class="fa-solid fa-save"></i> حفظ الأوقات
            </button>
        </div>

        <!-- Manual Controls -->
        <div class="card">
            <h3 style="margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
                <i class="fa-solid fa-toggle-on"></i> التحكم اليدوي بالفترة المحاسبية
            </h3>

            <div style="text-align: center; margin: 1.5rem 0;">
                <div style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 0.5rem;">حالة الفترة المحاسبية الآن:</div>
                @if($activeShift)
                    <div style="display: inline-block; background: rgba(76,175,80,0.15); border: 2px solid #4CAF50; color: #4CAF50; padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: bold; font-size: 1.2rem;">
                        <i class="fa-solid fa-door-open"></i> الفترة المحاسبية مفتوحة
                    </div>
                    <div style="font-size: 0.85rem; color: #888; margin-top: 0.5rem;">
                        تم الفتح بواسطة: {{ $activeShift->openedBy->name ?? 'النظام الآلي' }}<br>
                        الوقت: {{ $activeShift->opened_at->format('Y-m-d h:i A') }}
                    </div>
                @else
                    <div style="display: inline-block; background: rgba(244,67,54,0.15); border: 2px solid #F44336; color: #F44336; padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: bold; font-size: 1.2rem;">
                        <i class="fa-solid fa-door-closed"></i> الفترة المحاسبية مغلقة
                    </div>
                @endif
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button wire:click="startShiftManual" 
                        class="btn" 
                        style="flex: 1; background: #4CAF50; color: white; padding: 1rem; font-size: 1.1rem;"
                        {{ $activeShift ? 'disabled' : '' }}>
                    <i class="fa-solid fa-play"></i> بدء العمل الآن (فتح للجميع)
                </button>

                <button wire:click="closeShiftManual" 
                        class="btn" 
                        style="flex: 1; background: #F44336; color: white; padding: 1rem; font-size: 1.1rem;"
                        onclick="return confirm('هل أنت متأكد من إغلاق الفترة المحاسبية لجميع الموظفين؟')"
                        {{ !$activeShift ? 'disabled' : '' }}>
                    <i class="fa-solid fa-stop"></i> إغلاق العمل الآن (إغلاق للجميع)
                </button>
            </div>
        </div>
    </div>
</div>
