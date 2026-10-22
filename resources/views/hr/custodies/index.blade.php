<x-layouts.admin>
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>

    <!-- Page Header & Actions -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1><i class="fa-solid fa-boxes-packing" style="color: var(--primary-color);"></i> إدارة العهد</h1>
            <p>إضافة ومتابعة واسترجاع العهد والممتلكات الخاصة بالمنشأة.</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('hr.custodies.return') }}" class="btn btn-outline">
                <i class="fa-solid fa-link-slash"></i> فك ارتباط عهدة
            </a>
            <a href="{{ route('hr.custodies.assign') }}" class="btn" style="background: var(--info-color); border-color: var(--info-color); color: white;">
                <i class="fa-solid fa-link"></i> ربط عهدة بموظف
            </a>
            <a href="{{ route('hr.custodies.create') }}" class="btn">
                <i class="fa-solid fa-plus"></i> إضافة عهدة جديدة
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="toast toast-success" style="position: relative; top: 0; left: 0; transform: none; width: 100%; margin-bottom: 1rem; text-align: right; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="toast toast-error" style="position: relative; top: 0; left: 0; transform: none; width: 100%; margin-bottom: 1rem; text-align: right; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-3 mb-8">
        <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.2rem;">إجمالي العهد</p>
                <h3 style="font-size: 1.8rem; font-weight: 800;">{{ $stats['total'] }}</h3>
            </div>
            <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: var(--primary-glow); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
        </div>
        <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.2rem;">العهد المتاحة</p>
                <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--success-color);">{{ $stats['available'] }}</h3>
            </div>
            <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: rgba(76, 175, 80, 0.15); color: var(--success-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fa-solid fa-box-open"></i>
            </div>
        </div>
        <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.2rem;">العهد المستلمة</p>
                <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--warning-color);">{{ $stats['assigned'] }}</h3>
            </div>
            <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: rgba(255, 152, 0, 0.15); color: var(--warning-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>
    </div>

    <!-- Tabs Content -->
    <div x-data="{ tab: 'all' }" class="card" style="padding: 0; overflow: hidden;">
        
        <!-- Tabs Nav -->
        <div style="display: flex; gap: 1.5rem; border-bottom: 1px solid var(--border-color); padding: 0 1.5rem;">
            <button @click="tab = 'all'" :style="tab === 'all' ? 'border-bottom: 2px solid var(--primary-color); color: var(--text-primary); font-weight: 700;' : 'color: var(--text-secondary); font-weight: 600;'" style="padding: 1.2rem 0; background: none; border: none; cursor: pointer; transition: all 0.3s ease; border-bottom: 2px solid transparent;">جميع العهد المضافة</button>
            <button @click="tab = 'history'" :style="tab === 'history' ? 'border-bottom: 2px solid var(--primary-color); color: var(--text-primary); font-weight: 700;' : 'color: var(--text-secondary); font-weight: 600;'" style="padding: 1.2rem 0; background: none; border: none; cursor: pointer; transition: all 0.3s ease; border-bottom: 2px solid transparent;">سجل تسليم واسترجاع العهد</button>
        </div>

        <!-- Tab: All Custodies -->
        <div x-show="tab === 'all'">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">الرقم</th>
                            <th scope="col">الاسم</th>
                            <th scope="col">النوع</th>
                            <th scope="col" style="text-align: center;">الحالة</th>
                            <th scope="col">المستلم الحالي</th>
                            <th scope="col" style="text-align: center;">المرفقات</th>
                            <th scope="col" style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($custodies as $c)
                        <tr>
                            <td style="font-family: monospace; font-weight: 800; color: var(--primary-color);">{{ $c->code }}</td>
                            <td style="font-weight: 800; color: var(--text-primary);">{{ $c->name }}</td>
                            <td style="color: var(--text-secondary);">{{ $c->type }}</td>
                            <td style="text-align: center;">
                                @if($c->status === 'available') <span class="badge badge-active">متاح</span>
                                @elseif($c->status === 'assigned') <span class="badge badge-pending">مستلم</span>
                                @else <span class="badge" style="background: rgba(158,158,158,0.15); color: #9E9E9E;">{{ $c->status }}</span> @endif
                            </td>
                            <td style="color: var(--text-secondary); font-weight: 600;">
                                @if($c->status === 'assigned' && $c->currentAssignment)
                                    {{ $c->currentAssignment->employee->full_name_ar }}
                                @else
                                    <span style="opacity: 0.5;">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($c->attachments && count($c->attachments) > 0)
                                    <span class="badge" style="background: rgba(33, 150, 243, 0.15); color: var(--info-color);">{{ count($c->attachments) }} صور</span>
                                @else
                                    <span style="opacity: 0.5;">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <a href="{{ route('hr.custodies.edit', $c->id) }}" class="btn-ghost" title="تعديل العهدة">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if($c->status === 'assigned')
                                    <a href="{{ route('hr.custodies.return', ['custody_id' => $c->id]) }}" class="btn-ghost" title="استرجاع العهدة" style="color: var(--success-color);">
                                        <i class="fa-solid fa-link-slash"></i>
                                    </a>
                                    @endif
                                    <form action="{{ route('hr.custodies.destroy', $c->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف العهدة: {{ $c->name }}؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost" title="حذف العهدة" style="color: var(--danger-color); border: none; background: transparent; cursor: pointer;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                <i class="fa-solid fa-boxes-packing" style="font-size: 2rem; opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                                لا توجد أي عهدة مسجلة في النظام بعد.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: History -->
        <div x-show="tab === 'history'" style="display: none;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">العهدة</th>
                            <th scope="col">المستلم</th>
                            <th scope="col">تاريخ الاستلام</th>
                            <th scope="col">مرفق وتسليم الاستلام</th>
                            <th scope="col" style="text-align: center;">تاريخ الاسترجاع</th>
                            <th scope="col" style="text-align: center;">مرفقات وتسليم الاسترجاع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $a)
                        <tr>
                            <td style="font-weight: 800; color: var(--primary-color);">{{ $a->custody->name }} <span style="font-size: 0.75rem; color: var(--text-secondary);">({{ $a->custody->code }})</span></td>
                            <td style="font-weight: 800; color: var(--text-primary);">{{ $a->employee->full_name_ar }}</td>
                            <td style="font-weight: 600; color: var(--text-secondary);">{{ $a->assigned_at->format('Y-m-d') }}</td>
                            <td>
                                @if($a->assignment_signature)
                                    <span class="badge badge-active" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-check"></i> موقّع استلام
                                    </span>
                                @else 
                                    <span style="opacity: 0.5;">—</span> 
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($a->returned_at) 
                                    <span style="font-weight: 800; color: var(--primary-color);">{{ $a->returned_at->format('Y-m-d') }}</span>
                                @else 
                                    <span class="badge badge-pending">لم تُسترجع بعد</span> 
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($a->returned_at && $a->return_signature)
                                    <span class="badge badge-active" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-check"></i> موقّع تسليم
                                    </span>
                                @else 
                                    <span style="opacity: 0.5;">—</span> 
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                <i class="fa-solid fa-clock-rotate-left" style="font-size: 2rem; opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                                لا يوجد سجل لاستلام أو تسليم العهد.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.admin>


