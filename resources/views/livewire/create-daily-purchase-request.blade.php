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

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="display: flex; align-items: center; justify-content: space-between;">
                            <span><i class="fa-solid fa-paperclip"></i> المرفق أو الصورة <span style="color: var(--text-secondary); font-size: 0.8rem;">(اختياري)</span></span>
                            @if($attachment)
                                <button type="button" wire:click="$set('attachment', null)" style="background: none; border: none; color: var(--danger-color); cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-trash"></i> حذف المرفق
                                </button>
                            @endif
                        </label>

                        @if($attachment)
                            {{-- Image/File Preview --}}
                            <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; text-align: center;">
                                @php
                                    $ext = strtolower(pathinfo($attachment->getClientOriginalName(), PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'heic']);
                                @endphp
                                @if($isImage)
                                    <img src="{{ $attachment->temporaryUrl() }}" alt="معاينة المرفق" style="max-height: 140px; max-width: 100%; border-radius: 6px; object-fit: contain;">
                                @else
                                    <div style="color: var(--text-primary); font-size: 0.9rem; padding: 10px;">
                                        <i class="fa-solid fa-file-pdf" style="color: #f44336; font-size: 2rem; display: block; margin-bottom: 6px;"></i>
                                        <span>{{ $attachment->getClientOriginalName() }}</span>
                                    </div>
                                @endif
                                <div style="font-size: 0.78rem; color: #4CAF50; margin-top: 6px; font-weight: 600;">
                                    <i class="fa-solid fa-circle-check"></i> تم التقاط / إرفاق الملف بنجاح
                                </div>
                            </div>
                        @else
                            {{-- Camera & Gallery Options --}}
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                {{-- Option 1: Direct Camera Capture --}}
                                <label style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 14px 8px; background: rgba(255, 152, 0, 0.1); border: 1.5px dashed #FF9800; border-radius: 8px; cursor: pointer; transition: all 0.2s; text-align: center;" title="التقاط صورة مباشرة عبر الكاميرا">
                                    <i class="fa-solid fa-camera" style="font-size: 1.5rem; color: #FF9800;"></i>
                                    <span style="font-size: 0.85rem; font-weight: 700; color: #FF9800;">تصوير بالكاميرا</span>
                                    <span style="font-size: 0.7rem; color: var(--text-secondary);">كاميرا الجوال مباشرة</span>
                                    <input type="file" wire:model="attachment" accept="image/*" capture="environment" style="display: none;">
                                </label>

                                {{-- Option 2: Choose from device / gallery --}}
                                <label style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 14px 8px; background: rgba(255, 255, 255, 0.04); border: 1.5px dashed var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.2s; text-align: center;" title="اختيار صورة أو مستند من الجهاز">
                                    <i class="fa-solid fa-images" style="font-size: 1.5rem; color: var(--text-secondary);"></i>
                                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);">رفع من الجهاز</span>
                                    <span style="font-size: 0.7rem; color: var(--text-secondary);">الاستوديو أو ملف PDF</span>
                                    <input type="file" wire:model="attachment" accept="image/*,.pdf" style="display: none;">
                                </label>
                            </div>
                        @endif

                        <div wire:loading wire:target="attachment" style="color: var(--primary-color); font-size: 0.85rem; margin-top: 8px; text-align: center;">
                            <i class="fa-solid fa-spinner fa-spin"></i> جاري رفع الصورة...
                        </div>
                        @error('attachment') <span style="color: var(--danger-color); font-size: 0.8rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
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
