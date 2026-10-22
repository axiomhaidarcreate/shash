<div>
    <livewire:shift-status-banner />
    <livewire:shift-close-report section="greetings" />
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem); display: flex; align-items: center; gap: 10px;">
            <span><i class="fa-solid fa-gift" style="color: var(--primary-color);"></i> إدارة التحايا (شاشة المدير)</span>
            <button onclick="Livewire.dispatch('openDailyPurchaseModal')" class="btn" style="background: linear-gradient(135deg, #FF9800, #F57C00); color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); font-weight: bold; font-size: 0.85rem; box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3); transition: all 0.3s;">
                <i class="fa-solid fa-cart-plus"></i> طلبات
            </button>
        </h2>
    </div>

    @if (session()->has('message'))
        <div style="background: linear-gradient(135deg, rgba(76,175,80,0.15), rgba(76,175,80,0.05)); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; text-align: center; font-weight: bold;">
            <i class="fa-solid fa-circle-check"></i> {{ session('message') }}
        </div>
    @endif

    {{-- Active Day (Shift) Counters --}}
    <h3 style="margin-bottom: 1rem; font-size: 1.1rem; color: var(--primary-color);"><i class="fa-solid fa-clock-rotate-left"></i> إحصائيات الفترة المحاسبية النشطة (اليوم)</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: linear-gradient(135deg, rgba(255,193,7,0.15), rgba(255,193,7,0.05)); border: 1px solid #FFC107; border-radius: var(--radius-md); padding: 1.5rem; text-align: center;">
            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;"><i class="fa-solid fa-vault" style="color: #FFC107;"></i> الرصيد الموزع (الافتتاحي)</div>
            <div style="font-size: 2rem; font-weight: bold; color: #FFC107;">{{ $adminOpeningBalance }}</div>
        </div>
        <div style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(33, 150, 243, 0.05)); border: 1px solid #2196F3; border-radius: var(--radius-md); padding: 1.5rem; text-align: center;">
            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;"><i class="fa-solid fa-hand-sparkles" style="color: #2196F3;"></i> التحايا المباعة (المستخدم)</div>
            <div style="font-size: 2rem; font-weight: bold; color: #2196F3;">{{ $adminUsedBalance }}</div>
        </div>
        <div style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.15), rgba(76, 175, 80, 0.05)); border: 1px solid #4CAF50; border-radius: var(--radius-md); padding: 1.5rem; text-align: center;">
            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;"><i class="fa-solid fa-wallet" style="color: #4CAF50;"></i> الرصيد المتبقي مع الموظفين</div>
            <div style="font-size: 2rem; font-weight: bold; color: #4CAF50;">{{ $adminRemainingBalance }}</div>
        </div>
        <div style="background: linear-gradient(135deg, rgba(156, 39, 176, 0.15), rgba(156, 39, 176, 0.05)); border: 1px solid #9C27B0; border-radius: var(--radius-md); padding: 1.5rem; text-align: center;">
            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;"><i class="fa-solid fa-money-bill-wave" style="color: #9C27B0;"></i> الإيرادات</div>
            <div style="font-size: 2rem; font-weight: bold; color: #9C27B0;">{{ number_format($totalRevenueToday, 2) }} <span style="font-size: 1rem;">ريال</span></div>
        </div>
    </div>

    <div class="layout-grid">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Create New Greeting Product Section -->
            <div class="card">
                <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-plus-circle" style="color: var(--primary-color);"></i> إضافة منتج تحية جديد</h3>
                
                <div class="form-group">
                    <label class="form-label">اسم التحية الجديدة</label>
                    <input type="text" wire:model="newGreetingName" class="form-control" placeholder="مثال: تحية عيد ميلاد">
                    @error('newGreetingName') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <button wire:click="addGreetingProduct" class="btn btn-outline" style="width: 100%; padding: 0.8rem; margin-top: 0.5rem;">
                    <i class="fa-solid fa-save"></i> حفظ وإضافة التحية
                </button>
            </div>

            <!-- Add Quota Section -->
            <div class="card">
                <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-coins" style="color: var(--primary-color);"></i> شحن رصيد تحايا لموظف</h3>
                
                <div class="form-group">
                    <label class="form-label">اختر الموظف</label>
                    <select wire:model="staffId" class="form-control">
                        <option value="">-- اختر الموظف --</option>
                        @foreach($staffUsers as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                        @endforeach
                    </select>
                    @error('staffId') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">عدد التحايا المراد إضافتها (رصيد)</label>
                    <input type="number" wire:model="quantity" class="form-control" min="1" max="1000">
                    @error('quantity') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <button wire:click="addQuota" class="btn btn-success" style="width: 100%; padding: 0.9rem; font-size: 1.1rem; margin-top: 1rem;">
                    <i class="fa-solid fa-plus"></i> شحن الرصيد للموظف
                </button>
            </div>

            <!-- Today's Quota Log Section -->
            <div class="card">
                <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-list-check" style="color: var(--primary-color);"></i> سجل إضافات اليوم (الفترة المحاسبية النشطة)</h3>
                
                @if(isset($todayQuotas) && $todayQuotas->count() > 0)
                    <div style="overflow-x: auto; max-height: 350px;">
                        <table class="data-table" style="width: 100%; min-width: 400px; font-size: 0.9rem;">
                            <thead>
                                <tr>
                                    <th>الموظف</th>
                                    <th>الرصيد المضاف</th>
                                    <th>الوقت</th>
                                    <th>خيارات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayQuotas as $q)
                                    <tr>
                                        <td>{{ $q->staff->name ?? 'غير معروف' }}</td>
                                        <td>
                                            @if($editingQuotaId === $q->id)
                                                <input type="number" wire:model="editQuantity" class="form-control" style="width: 70px; padding: 2px 5px; height: auto; display: inline-block;">
                                            @else
                                                <span style="font-weight: bold; color: var(--success-color);">+{{ $q->quantity }}</span>
                                            @endif
                                        </td>
                                        <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                            {{ $q->created_at->format('h:i A') }}
                                        </td>
                                        <td>
                                            @if($editingQuotaId === $q->id)
                                                <button wire:click="updateQuota" class="btn btn-sm btn-success" style="padding: 2px 6px;" title="حفظ"><i class="fa-solid fa-check"></i></button>
                                                <button wire:click="cancelEditQuota" class="btn btn-sm btn-outline" style="padding: 2px 6px;" title="إلغاء"><i class="fa-solid fa-times"></i></button>
                                            @else
                                                <button wire:click="editQuota({{ $q->id }})" class="btn btn-sm btn-ghost" title="تعديل"><i class="fa-solid fa-edit" style="color: var(--info-color);"></i></button>
                                                <button wire:click="deleteQuota({{ $q->id }})" class="btn btn-sm btn-ghost" title="حذف" onclick="confirm('هل أنت متأكد من حذف هذا الرصيد؟') || event.stopImmediatePropagation()"><i class="fa-solid fa-trash" style="color: var(--danger-color);"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; color: var(--text-secondary); padding: 1rem;">لا توجد إضافات رصيد مسجلة حتى الآن.</div>
                @endif
            </div>
        </div>

        {{-- Staff Balances Section --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem; grid-column: span 2;">
            {{-- Per-Staff Balance Cards --}}
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="margin: 0; font-size: 1.1rem;"><i class="fa-solid fa-users" style="color: var(--primary-color);"></i> أرصدة الموظفين</h3>
                    <a href="{{ route('admin.greetings.reports') }}" class="btn btn-sm btn-outline"><i class="fa-solid fa-chart-line"></i> تقارير التحايا</a>
                </div>
                
                @if($staffUsers->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
                        @foreach($staffUsers as $staff)
                            @php $bal = $staffBalances[$staff->id] ?? ['opening' => 0, 'used' => 0, 'remaining' => 0]; @endphp
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.2rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4 style="margin: 0; color: var(--primary-color); font-size: 1.1rem;">
                                        <i class="fa-solid fa-user-tie"></i> {{ $staff->name }}
                                    </h4>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                                    <div style="background: rgba(255,193,7,0.1); border: 1px solid rgba(255,193,7,0.3); padding: 0.8rem; border-radius: var(--radius-sm); text-align: center;">
                                        <div style="font-size: 0.7rem; color: #FFC107; margin-bottom: 4px;">الرصيد الافتتاحي</div>
                                        <div style="font-size: 1.3rem; font-weight: bold; color: #fff;">{{ $bal['opening'] }}</div>
                                    </div>
                                    <div style="background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); padding: 0.8rem; border-radius: var(--radius-sm); text-align: center;">
                                        <div style="font-size: 0.7rem; color: #F44336; margin-bottom: 4px;">الرصيد المستخدم</div>
                                        <div style="font-size: 1.3rem; font-weight: bold; color: #fff;">{{ $bal['used'] }}</div>
                                    </div>
                                    <div style="background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); padding: 0.8rem; border-radius: var(--radius-sm); text-align: center;">
                                        <div style="font-size: 0.7rem; color: #4CAF50; margin-bottom: 4px;">الرصيد المتبقي</div>
                                        <div style="font-size: 1.3rem; font-weight: bold; color: #fff;">{{ $bal['remaining'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا يوجد موظفين تحايا مسجلين.</div>
                @endif
            </div>

            <!-- Open Accounts Grid (Read Only for Admin) -->
            <div class="card">
                <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-list" style="color: var(--primary-color);"></i> الحسابات النشطة الآن عند الموظفين</h3>
                
                @if($openAccounts->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
                        @foreach($openAccounts as $account)
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.2rem; display: flex; flex-direction: column; gap: 10px; position: relative;">
                                
                                <div style="position: absolute; top: 10px; left: 10px; background: rgba(33,150,243,0.15); color: #2196F3; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: bold;">
                                    موظف: {{ $account->staff->name ?? 'غير معروف' }}
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 10px;">
                                    <div>
                                        <h4 style="margin: 0; color: var(--primary-color); font-size: 1.2rem;">{{ $account->guest_name }}</h4>
                                        <div style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 4px;">
                                            @if($account->table)
                                                <i class="fa-solid fa-border-all"></i> طاولة {{ $account->table->number }}
                                            @else
                                                <i class="fa-solid fa-user-clock"></i> بدون طاولة
                                            @endif
                                        </div>
                                        <div style="font-size: 0.8rem; color: #888; margin-top: 4px;">
                                            <i class="fa-regular fa-clock"></i> بدأ منذ {{ $account->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div style="background: rgba(212, 175, 55, 0.2); border: 1px solid var(--primary-color); padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); text-align: center;">
                                        <div style="font-size: 0.7rem; color: var(--primary-color);">التحايا المطلوبة</div>
                                        <div style="font-size: 1.4rem; font-weight: bold; color: #fff;">{{ $account->total_greetings }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; color: var(--text-secondary); padding: 3rem 0;">
                        <i class="fa-solid fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color);"></i>
                        <p>لا توجد حسابات ضيوف مفتوحة للتحايا حالياً.</p>
                    </div>
                @endif
            </div>

            {{-- Monthly Daily Reports --}}
            @if(isset($dailyReports))
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <h3 style="margin: 0; font-size: 1.1rem;"><i class="fa-solid fa-file-invoice" style="color: var(--primary-color);"></i> تقارير الإغلاق اليومي للورديات</h3>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div class="form-group" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <label class="form-label" style="margin: 0; white-space: nowrap;">من تاريخ</label>
                            <input type="date" wire:model.live="filterStartDate" class="form-control" style="padding: 0.3rem; height: auto;">
                        </div>
                        <div class="form-group" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <label class="form-label" style="margin: 0; white-space: nowrap;">إلى تاريخ</label>
                            <input type="date" wire:model.live="filterEndDate" class="form-control" style="padding: 0.3rem; height: auto;">
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <a href="/invoice/greetings-report-print-thermal?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}"
                               target="_blank" class="btn"
                               style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                                <i class="fa-solid fa-receipt"></i> طباعة الكل (حراري)
                            </a>
                            <a href="/invoice/greetings-report-print?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}"
                               target="_blank" class="btn"
                               style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                                <i class="fa-solid fa-file-lines"></i> طباعة الكل (A4)
                            </a>
                        </div>
                    </div>
                </div>
                <div style="overflow-x: auto;">
                    <table class="data-table" style="min-width: 600px; width: 100%;">
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>الرصيد الافتتاحي</th>
                                <th>المباع</th>
                                <th>المتبقي</th>
                                <th>إجمالي المبلغ</th>
                                <th>وقت الفتح</th>
                                <th>وقت الإغلاق</th>
                                <th>طباعة</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($dailyReports as $dr)
                            @php
                                $openDate = $dr->opened_at ? $dr->opened_at->format('Y-m-d') : $dr->created_at->format('Y-m-d');
                                $closeDate = $dr->closed_at ? $dr->closed_at->format('Y-m-d') : $dr->created_at->format('Y-m-d');
                            @endphp
                            <tr>
                                <td style="font-weight: bold; color: var(--primary-color);">{{ $dr->staff->name ?? '-' }}</td>
                                <td>{{ $dr->opening_balance }}</td>
                                <td style="color: #F44336; font-weight: bold;">{{ $dr->used_balance }}</td>
                                <td style="color: #4CAF50; font-weight: bold;">{{ $dr->remaining_balance }}</td>
                                <td style="font-weight: bold;">{{ number_format($dr->total_amount, 2) }} ريال</td>
                                <td style="font-size: 0.85rem; color: #888; font-weight: bold;">
                                    {{ $dr->opened_at ? $dr->opened_at->format('Y-m-d h:i A') : $openDate }}
                                </td>
                                <td style="font-size: 0.85rem; color: #888; font-weight: bold;">
                                    {{ $dr->closed_at ? $dr->closed_at->format('Y-m-d h:i A') : $closeDate }}
                                </td>
                                <td>
                                    <button wire:click="printDailyReport({{ $dr->id }})" class="btn btn-sm btn-ghost" title="طباعة تقرير اليومية">
                                        <i class="fa-solid fa-print" style="color: var(--primary-color);"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-secondary);">لا توجد تقارير في هذه الفترة.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Daily Report Print Modal --}}
    @if($showDailyPrintModal && $dailyPrintReport)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; justify-content: center; align-items: center; padding: 1rem;">
        <div style="background: var(--surface-color); border-radius: var(--radius-lg); width: 95%; max-width: 450px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); border: 1px solid var(--border-color); overflow: hidden;">
            
            <div style="background: linear-gradient(135deg, var(--primary-color), #B8860B); padding: 1.2rem; text-align: center;">
                <h3 style="margin: 0; color: #fff; font-size: 1.3rem;"><i class="fa-solid fa-file-invoice"></i> تقرير إغلاق يومية</h3>
            </div>

            <div id="printable-daily-report" style="padding: 1.5rem;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="font-size: 1.3rem; font-weight: bold; color: var(--primary-color); margin-bottom: 0.5rem;">تقرير يومية التحايا</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">الموظف: {{ $dailyPrintReport['staff_name'] }}</div>
                </div>

                <div style="display: grid; gap: 0.8rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الموظف:</span>
                        <span style="font-weight: bold;">{{ $dailyPrintReport['staff_name'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">المدير (المغلق):</span>
                        <span style="font-weight: bold;">{{ $dailyPrintReport['manager_name'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الرصيد الافتتاحي:</span>
                        <span style="font-weight: bold;">{{ $dailyPrintReport['opening_balance'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الرصيد المستخدم:</span>
                        <span style="font-weight: bold; color: #F44336;">{{ $dailyPrintReport['used_balance'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الرصيد المتبقي:</span>
                        <span style="font-weight: bold; color: #4CAF50;">{{ $dailyPrintReport['remaining_balance'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.8rem; background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); border-radius: var(--radius-sm);">
                        <span style="font-weight: bold; font-size: 1.1rem;">الإيرادات:</span>
                        <span style="font-weight: bold; color: var(--success-color); font-size: 1.2rem;">{{ number_format($dailyPrintReport['total_amount'], 2) }} ريال</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                        <span style="color: var(--text-secondary);">وقت الفتح:</span>
                        <span style="font-size: 0.9rem;">{{ $dailyPrintReport['opened_at'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                        <span style="color: var(--text-secondary);">وقت الإغلاق:</span>
                        <span style="font-size: 0.9rem;">{{ $dailyPrintReport['closed_at'] }}</span>
                    </div>
                </div>
            </div>

            <div style="padding: 1rem 1.5rem 1.5rem; display: flex; gap: 0.8rem;">
                <button onclick="printDailyReportFn()" class="btn" style="flex: 1; background: var(--primary-color); color: #fff; padding: 0.8rem; font-weight: bold;">
                    <i class="fa-solid fa-print"></i> طباعة
                </button>
                <button wire:click="closeDailyPrintModal" class="btn" style="flex: 1; background: transparent; border: 1px solid var(--text-secondary); color: var(--text-secondary); padding: 0.8rem;">
                    إغلاق
                </button>
            </div>
        </div>
    </div>

    <script>
        function printDailyReportFn() {
            var reportContent = document.getElementById('printable-daily-report').innerHTML;
            var pw = window.open('', '_blank', 'width=400,height=600');
            pw.document.write('<html dir="rtl"><head><title>تقرير يومية</title>');
            pw.document.write('<style>');
            pw.document.write('@page { margin: 2mm; size: 80mm auto; }');
            pw.document.write('body { font-family: Tahoma, Arial, sans-serif; padding: 5mm; margin: 0 auto; direction: rtl; font-size: 15px; font-weight: bold; color: #000; width: 75mm; box-sizing: border-box; }');
            pw.document.write('div, span { font-weight: bold !important; color: #000 !important; }');
            pw.document.write('</style></head><body>');
            
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = reportContent;
            
            var rows = tempDiv.querySelectorAll('div[style*="display: flex"]');
            var thermalHtml = '<div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; font-size: 18px;">تقرير يومية التحايا</div>';
            
            rows.forEach(function(row) {
                var spans = row.querySelectorAll('span');
                if(spans.length >= 2) {
                    var label = spans[0].innerText;
                    var val = spans[1].innerText;
                    var isTotal = label.includes('الإيرادات');
                    var border = isTotal ? 'border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; font-size: 16px; margin-top: 10px;' : 'border-bottom: 1px dotted #ccc; padding: 5px 0;';
                    thermalHtml += '<div style="display: flex; justify-content: space-between; ' + border + '"><span>' + label + '</span><span>' + val + '</span></div>';
                }
            });

            pw.document.write(thermalHtml);
            pw.document.write('<div style="text-align:center; margin-top:15px; font-size:12px;">شكراً لجهودكم</div>');
            pw.document.write('</body></html>');
            pw.document.close();
            pw.focus();
            setTimeout(function() {
                pw.print();
                pw.close();
            }, 250);
        }
    </script>
    @endif

    @livewire('create-daily-purchase-request', ['kitchenSource' => 'تحايا'])
</div>
