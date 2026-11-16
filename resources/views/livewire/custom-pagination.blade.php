@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="تنقل الصفحات" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; margin: 20px 0 10px 0;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; max-width: 420px;">
                
                {{-- زر الصفحة السابقة (سهم السابق) --}}
                @if ($paginator->onFirstPage())
                    <button type="button" disabled
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; border-radius: 10px; background: rgba(255,255,255,0.04); color: #555; border: 1px solid var(--border-color, #2a2a2a); cursor: not-allowed; font-size: 0.95rem; font-weight: 600; flex: 1;"
                        title="الصفحة السابقة">
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.85rem;"></i>
                        <span>السابق</span>
                    </button>
                @else
                    <button type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; border-radius: 10px; background: var(--surface-color, #1a1a1a); color: var(--text-primary, #f0f0f0); border: 1px solid var(--border-color, #2a2a2a); cursor: pointer; font-size: 0.95rem; font-weight: 600; flex: 1; transition: all 0.2s;"
                        onmouseover="this.style.borderColor='var(--primary-color, #d4af37)'; this.style.color='var(--primary-color, #d4af37)'"
                        onmouseout="this.style.borderColor='var(--border-color, #2a2a2a)'; this.style.color='var(--text-primary, #f0f0f0)'"
                        title="الصفحة السابقة">
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.85rem;"></i>
                        <span>السابق</span>
                    </button>
                @endif

                {{-- رقم الصفحة الذي هو فيها --}}
                <div style="display: inline-flex; align-items: center; justify-content: center; padding: 10px 18px; border-radius: 10px; background: rgba(212, 175, 55, 0.12); border: 1px solid rgba(212, 175, 55, 0.4); color: var(--primary-color, #d4af37); font-weight: 800; font-size: 0.95rem; min-width: 130px; text-align: center; white-space: nowrap;">
                    <span>صفحة {{ $paginator->currentPage() }} من {{ $paginator->lastPage() }}</span>
                </div>

                {{-- زر الصفحة التالية (سهم التالي) --}}
                @if ($paginator->hasMorePages())
                    <button type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; border-radius: 10px; background: var(--surface-color, #1a1a1a); color: var(--text-primary, #f0f0f0); border: 1px solid var(--border-color, #2a2a2a); cursor: pointer; font-size: 0.95rem; font-weight: 600; flex: 1; transition: all 0.2s;"
                        onmouseover="this.style.borderColor='var(--primary-color, #d4af37)'; this.style.color='var(--primary-color, #d4af37)'"
                        onmouseout="this.style.borderColor='var(--border-color, #2a2a2a)'; this.style.color='var(--text-primary, #f0f0f0)'"
                        title="الصفحة التالية">
                        <span>التالي</span>
                        <i class="fa-solid fa-chevron-left" style="font-size: 0.85rem;"></i>
                    </button>
                @else
                    <button type="button" disabled
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; border-radius: 10px; background: rgba(255,255,255,0.04); color: #555; border: 1px solid var(--border-color, #2a2a2a); cursor: not-allowed; font-size: 0.95rem; font-weight: 600; flex: 1;"
                        title="الصفحة التالية">
                        <span>التالي</span>
                        <i class="fa-solid fa-chevron-left" style="font-size: 0.85rem;"></i>
                    </button>
                @endif

            </div>

            {{-- تفاصيل عدد الطلبات --}}
            <div style="font-size: 0.8rem; color: var(--text-secondary, #888); text-align: center; margin-top: 2px;">
                عرض {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} من إجمالي {{ $paginator->total() }} طلب
            </div>
        </nav>
    @endif
</div>
