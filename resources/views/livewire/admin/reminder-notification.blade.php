<div wire:poll.60s>
    @if($this->expiredReminders->count() > 0)
        <div style="margin-bottom: 20px;">
            @foreach($this->expiredReminders as $reminder)
                <div class="reminder-alert" style="background: rgba(244, 67, 54, 0.1); border: 1px solid var(--danger-color); padding: 15px; margin-bottom: 10px; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(244,67,54,0.15); animation: toastIn 0.5s ease;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: var(--danger-color); color: white; width: 45px; height: 45px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1.3rem; flex-shrink: 0;">
                            <i class="fa-solid fa-bell fa-shake"></i>
                        </div>
                        <div>
                            <h4 style="color: var(--danger-color); margin-bottom: 5px; font-weight: 800; font-size: 1.1rem;">حان موعد التذكير: {{ $reminder->title }}</h4>
                            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0;">
                                {{ $reminder->description }}
                                @if($reminder->amount)
                                    <span style="margin: 0 5px; color: var(--primary-color);">| المبلغ: {{ number_format($reminder->amount, 2) }}</span>
                                @endif
                                <span style="margin: 0 5px;">| التاريخ: {{ $reminder->reminder_date->format('Y-m-d') }} @if($reminder->reminder_time) {{ date('h:i A', strtotime($reminder->reminder_time)) }} @endif</span>
                            </p>
                        </div>
                    </div>
                    <div>
                        <button wire:click="markAsDone({{ $reminder->id }})" class="btn btn-success btn-sm" style="white-space: nowrap;">
                            <i class="fa-solid fa-check"></i> تم الإنجاز
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
