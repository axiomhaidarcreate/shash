<x-slot name="title">عروض الولاء</x-slot>

<div>
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>عروض الولاء</h1>
            <p>إدارة عروض وبرامج الولاء للعملاء</p>
        </div>
        <button wire:click="openModal" class="btn btn-success">
            <i class="fa-solid fa-plus"></i> إضافة عرض جديد
        </button>
    </div>

    <div class="card mb-4">
        <div class="table-responsive">
            <table class="data-table w-100 text-right">
                <thead>
                    <tr>
                        <th>اسم العرض</th>
                        <th>نقاط الزيارة</th>
                        <th>نقاط الطلب</th>
                        <th>نقاط الريال</th>
                        <th>مستويات المكافآت</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                        <tr>
                            <td><strong>{{ $offer->name }}</strong></td>
                            <td>{{ $offer->points_per_visit }}</td>
                            <td>{{ $offer->points_per_order }}</td>
                            <td>{{ $offer->points_per_riyal }}</td>
                            <td>
                                @if($offer->rewardTiers && $offer->rewardTiers->count() > 0)
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        @foreach($offer->rewardTiers as $tier)
                                            <div style="display: flex; align-items: center; gap: 8px; background: rgba(212,175,55,0.08); padding: 5px 10px; border-radius: 6px; border-right: 3px solid var(--primary-color);">
                                                <span class="badge badge-prep" style="min-width: 60px; text-align: center;">{{ $tier->required_points }} نقطة</span>
                                                <i class="fa-solid fa-arrow-left" style="color: var(--text-secondary); font-size: 0.7rem;"></i>
                                                <span style="font-size: 0.85rem;">{{ $tier->reward_description }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($offer->reward_description)
                                    {{-- Fallback to legacy single reward --}}
                                    <div style="display: flex; align-items: center; gap: 8px; background: rgba(212,175,55,0.08); padding: 5px 10px; border-radius: 6px; border-right: 3px solid var(--primary-color);">
                                        <span class="badge badge-prep" style="min-width: 60px; text-align: center;">{{ $offer->required_points }} نقطة</span>
                                        <i class="fa-solid fa-arrow-left" style="color: var(--text-secondary); font-size: 0.7rem;"></i>
                                        <span style="font-size: 0.85rem;">{{ $offer->reward_description }}</span>
                                    </div>
                                @else
                                    <span style="color: var(--text-secondary);">---</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="edit({{ $offer->id }})" class="btn btn-sm btn-outline-info" style="border:1px solid #17a2b8; color:#17a2b8; background:transparent;">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:click="delete({{ $offer->id }})" wire:confirm="هل أنت متأكد من حذف هذا العرض؟" class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">لا توجد عروض ولاء حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    @if($isModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1050; display:flex; justify-content:center; align-items:center;">
            <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); width:90%; max-width:700px; max-height:90vh; overflow-y:auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <div class="modal-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between">
                        <h5 class="modal-title">{{ $offer_id ? 'تعديل عرض الولاء' : 'إضافة عرض جديد' }}</h5>
                        <button type="button" class="btn-close btn-ghost" wire:click="closeModal" style="font-size:1.5rem; line-height:1;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="store">
                            <div class="form-group mb-3">
                                <label class="form-label">اسم العرض</label>
                                <input type="text" wire:model="name" class="form-control" required placeholder="مثال: العرض الذهبي">
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div class="form-group">
                                    <label class="form-label">نقاط لكل زيارة</label>
                                    <input type="number" wire:model="points_per_visit" class="form-control" min="0">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">نقاط لكل طلب</label>
                                    <input type="number" wire:model="points_per_order" class="form-control" min="0">
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="form-label">نقاط لكل ريال</label>
                                <input type="number" wire:model="points_per_riyal" class="form-control" min="0">
                            </div>

                            {{-- Reward Tiers Section --}}
                            <div style="border-top: 2px solid var(--primary-color); padding-top: 1rem; margin-top: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <label class="form-label" style="color: var(--primary-color); font-weight: bold; margin-bottom: 0; font-size: 1.05rem;">
                                        <i class="fa-solid fa-trophy"></i> مستويات المكافآت
                                    </label>
                                    <button type="button" wire:click="addTier" class="btn btn-sm btn-success" style="padding: 4px 12px;">
                                        <i class="fa-solid fa-plus"></i> إضافة مستوى
                                    </button>
                                </div>

                                @error('rewardTiers')
                                    <div class="text-danger small mb-2"><i class="fa-solid fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror

                                @if(count($rewardTiers) === 0)
                                    <div style="text-align: center; padding: 1.5rem; background: rgba(255,255,255,0.03); border: 2px dashed var(--border-color); border-radius: var(--radius-sm); color: var(--text-secondary);">
                                        <i class="fa-solid fa-layer-group" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                                        لا توجد مستويات مكافآت بعد. اضغط "إضافة مستوى" لبدء الإضافة.
                                    </div>
                                @endif

                                @foreach($rewardTiers as $index => $tier)
                                    <div style="background: rgba(212,175,55,0.05); border: 1px solid rgba(212,175,55,0.2); border-radius: var(--radius-sm); padding: 0.8rem; margin-bottom: 0.8rem; position: relative;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                            <span style="background: var(--primary-color); color: #000; font-weight: bold; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">{{ $index + 1 }}</span>
                                            <span style="font-weight: bold; font-size: 0.9rem; color: var(--primary-color);">المستوى {{ $index + 1 }}</span>
                                            <button type="button" wire:click="removeTier({{ $index }})" class="btn btn-sm" style="margin-right: auto; margin-left: 0; padding: 2px 8px; color: var(--danger-color); background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); border-radius: 4px; font-size: 0.75rem;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="form-group">
                                                <label class="form-label" style="font-size: 0.85rem;">النقاط المطلوبة</label>
                                                <input type="number" wire:model="rewardTiers.{{ $index }}.required_points" class="form-control" min="1" placeholder="مثال: 50">
                                                @error("rewardTiers.{$index}.required_points") <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" style="font-size: 0.85rem;">وصف المكافأة</label>
                                                <input type="text" wire:model="rewardTiers.{{ $index }}.reward_description" class="form-control" placeholder="مثال: شيشة مجانية">
                                                @error("rewardTiers.{$index}.reward_description") <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-secondary">
                                <button type="button" wire:click="closeModal" class="btn btn-ghost">إلغاء</button>
                                <button type="submit" class="btn btn-success">حفظ العرض</button>
                            </div>
                        </form>
                    </div>
        </div>
    @endif
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                showToast(event[0].message, event[0].type);
            });
        });
    </script>
</div>
