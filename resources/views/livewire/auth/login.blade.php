<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh; min-height: 80dvh; padding: 1rem;">
    <div class="card glass" style="width: 100%; max-width: min(400px, 95vw);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <i class="fa-solid fa-wind fa-3x" style="color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h2 style="font-size: clamp(1.2rem, 4vw, 1.5rem);">نظام إدارة الشيشة</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem; font-size: clamp(0.85rem, 2.5vw, 1rem);">تسجيل الدخول للموظفين</p>
        </div>

        <form wire:submit.prevent="login">
            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" wire:model="email" class="form-control" dir="ltr" placeholder="email@example.com" inputmode="email" autocomplete="email">
                @error('email') <span style="color: var(--danger-color); font-size: 0.9rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">كلمة المرور</label>
                <input type="password" wire:model="password" class="form-control" dir="ltr" placeholder="********" autocomplete="current-password">
                @error('password') <span style="color: var(--danger-color); font-size: 0.9rem;">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn" style="width: 100%; margin-top: 1rem; padding: 0.9rem; font-size: clamp(0.95rem, 3vw, 1.1rem);">
                <i class="fa-solid fa-right-to-bracket"></i> دخول
            </button>
        </form>


    </div>
</div>
