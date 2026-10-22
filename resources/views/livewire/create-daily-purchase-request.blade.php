<div>
    @if($isOpen)
        <!-- Modal Overlay -->
        <div class="modal-overlay" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
            <!-- Modal Content -->
            <div class="card" style="width: min(90%, 500px); max-height: 90vh; overflow-y: auto; padding: 20px; position: relative;">
                <button wire:click="closeModal" style="position: absolute; top: 15px; left: 15px; background: none; border: none; color: var(--text-secondary); font-size: 1.2rem; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <h3 style="color: var(--primary-color); margin-bottom: 20px; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <i class="fa-solid fa-cart-plus"></i> تقديم طلب مشتريات يومي ({{ $kitchenSource }})
                </h3>

                <form wire:submit.prevent="submit">
                    <div class="form-group">
                        <label class="form-label">اسم الصنف <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" wire:model.defer="itemName" placeholder="مثال: طماطم، سكر، الخ" required>
                        @error('itemName') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2">
                        <div class="form-group">
                            <label class="form-label">العدد <span style="color: red;">*</span></label>
                            <input type="number" class="form-control" wire:model.defer="quantity" min="1" required>
                            @error('quantity') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">الوزن / مل (اختياري)</label>
                            <input type="text" class="form-control" wire:model.defer="weightOrMl" placeholder="مثال: 5 كيلو، 500 مل">
                            @error('weightOrMl') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2">
                        <div class="form-group">
                            <label class="form-label">الأهمية <span style="color: red;">*</span></label>
                            <select class="form-control" wire:model.defer="importance" required>
                                <option value="عادي">عادي</option>
                                <option value="متوسط">متوسط</option>
                                <option value="عاجل">عاجل</option>
                            </select>
                            @error('importance') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">تاريخ الاحتياج <span style="color: red;">*</span></label>
                            <input type="date" class="form-control" wire:model.defer="neededDate" required>
                            @error('neededDate') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">مرفق (صورة أو ملف - اختياري)</label>
                        <input type="file" class="form-control" wire:model="attachment" accept=".jpg,.jpeg,.png,.pdf" style="padding: 0.4rem;">
                        <div wire:loading wire:target="attachment" style="color: var(--primary-color); font-size: 0.8rem; margin-top: 5px;">جاري الرفع...</div>
                        @error('attachment') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn" style="flex: 1;" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit"><i class="fa-solid fa-paper-plane"></i> إرسال الطلب</span>
                            <span wire:loading wire:target="submit"><i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...</span>
                        </button>
                        <button type="button" class="btn btn-outline" wire:click="closeModal" style="flex: 1;">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
