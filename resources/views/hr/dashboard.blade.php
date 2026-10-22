<x-layouts.admin>
    <!-- Page Header & Actions -->
        <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>
<div class="page-header">
        <h1><i class="fa-solid fa-chart-pie" style="color: var(--primary-color);"></i> الرئيسية - الموارد البشرية</h1>
        <p>مرحباً بك مجدداً، إليك ملخص أداء المؤسسة اليوم</p>
    </div>

    <!-- Stats Row 1: 4 Cards -->
    <div class="grid grid-cols-4 mb-4">
        {{-- Card 1: إجمالي الموظفين --}}
        <div class="card" style="padding: 1.2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <span class="badge badge-active">+{{ $totalEmployees > 0 ? '12%' : '0%' }}</span>
                <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: rgba(33, 150, 243, 0.15); color: var(--info-color); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem;">إجمالي الموظفين</p>
            <p style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ number_format($totalEmployees) }}</p>
        </div>

        {{-- Card 2: عدد الحضور --}}
        <div class="card" style="padding: 1.2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <span class="badge" style="background: rgba(156, 39, 176, 0.15); color: #9C27B0;">مباشر</span>
                <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: rgba(156, 39, 176, 0.15); color: #9C27B0; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem;">الحضور اليوم</p>
            <p style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ number_format($todayAttendance) }}</p>
        </div>

        {{-- Card 3: Featured — الغياب اليوم --}}
        <div class="card" style="padding: 1.2rem; background: linear-gradient(135deg, rgba(244, 67, 54, 0.1), rgba(244, 67, 54, 0.05)); border-color: rgba(244, 67, 54, 0.2);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <span class="badge badge-inactive">اليوم</span>
                <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: rgba(244, 67, 54, 0.15); color: var(--danger-color); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem;">الغياب اليوم</p>
            <p style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ number_format($todayAbsence) }}</p>
        </div>

        {{-- Card 4: ساعات التأخير --}}
        <div class="card" style="padding: 1.2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <span class="badge badge-pending">اليوم</span>
                <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: rgba(255, 152, 0, 0.15); color: var(--warning-color); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem;">ساعات التأخير</p>
            <p style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ number_format($delayHours) }}</p>
        </div>
    </div>

    <!-- Stats Row 2: Deductions -->
    <div class="grid grid-cols-3 mb-6">
        <div class="card" style="padding: 1.2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <span class="badge" style="background: rgba(244,67,54,0.1); color: var(--danger-color);">هذا الشهر</span>
                <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: rgba(244,67,54,0.1); color: var(--danger-color); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem;">إجمالي الاستقطاعات</p>
            <div style="display: flex; align-items: baseline; gap: 8px;">
                <p style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ number_format($totalDeductions) }}</p>
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);">ر.س</span>
            </div>
        </div>

        <div class="card" style="padding: 1.2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <span class="badge" style="background: rgba(244,67,54,0.1); color: var(--danger-color);">هذا الشهر</span>
                <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: rgba(244,67,54,0.1); color: var(--danger-color); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-user-minus"></i>
                </div>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem;">خصومات الغياب</p>
            <div style="display: flex; align-items: baseline; gap: 8px;">
                <p style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ number_format($totalAbsentDeductions) }}</p>
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);">ر.س</span>
            </div>
        </div>

        <div class="card" style="padding: 1.2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <span class="badge" style="background: rgba(255,152,0,0.1); color: var(--warning-color);">هذا الشهر</span>
                <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: rgba(255,152,0,0.1); color: var(--warning-color); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-stopwatch"></i>
                </div>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem;">خصومات التأخير</p>
            <div style="display: flex; align-items: baseline; gap: 8px;">
                <p style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ number_format($totalDelayDeductions) }}</p>
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);">ر.س</span>
            </div>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="layout-grid mb-8">
        <!-- Recent Notifications -->
        <div class="card" style="display: flex; flex-direction: column; padding: 0; min-height: 350px;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 4px;">الإشعارات الحديثة</h3>
                <p style="color: var(--text-secondary); font-size: 0.85rem;">آخر طلبات وتنبيهات الموظفين الواردة للنظام</p>
            </div>
            
            <div style="flex: 1; overflow-y: auto;">
                @forelse($recentNotifications ?? [] as $notification)
                <div style="padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(33, 150, 243, 0.1); color: var(--info-color); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h4 style="font-weight: 700; font-size: 0.95rem; margin-bottom: 4px;">{{ $notification->title }}</h4>
                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 6px;">{{ $notification->message }}</p>
                        <span style="color: var(--text-secondary); font-size: 0.75rem; direction: ltr; display: block; text-align: left;">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div style="display: flex; flex-direction: column; items-center; justify-content: center; height: 100%; padding: 3rem; color: var(--text-secondary); text-align: center;">
                    <i class="fa-solid fa-bell-slash" style="font-size: 2.5rem; opacity: 0.5; margin-bottom: 1rem;"></i>
                    <p style="font-weight: 600;">لا توجد إشعارات حديثة</p>
                </div>
                @endforelse
            </div>
            
            @if(isset($recentNotifications) && count($recentNotifications) > 0)
            <div style="padding: 1rem; border-top: 1px solid var(--border-color); text-align: center; background: rgba(0,0,0,0.1);">
                <a href="#" style="color: var(--primary-color); font-weight: 700; font-size: 0.9rem; text-decoration: none;">عرض جميع الإشعارات</a>
            </div>
            @endif
        </div>

        <!-- Salary Distribution -->
        <div class="card" style="display: flex; flex-direction: column; min-height: 350px;">
            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 800;">توزيع الرواتب</h3>
            </div>
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 1.5rem;">
                @php
                    $colors = ['var(--primary-color)', '#2196F3', '#FF9800', '#4CAF50', '#9C27B0'];
                    $colorIndex = 0;
                @endphp
                @forelse($salaryDistribution as $deptName => $percentage)
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 45px; text-align: left; font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);">{{ $percentage }}%</div>
                        <div style="flex: 1; height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden;">
                            <div style="height: 100%; border-radius: 4px; width: {{ $percentage }}%; background: {{ $colors[$colorIndex % count($colors)] }};"></div>
                        </div>
                        <div style="width: 90px; font-size: 0.85rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $deptName }}</div>
                    </div>
                    @php $colorIndex++; @endphp
                @empty
                    <div style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                        لا توجد بيانات رواتب حالياً
                    </div>
                @endforelse
            </div>
            <p style="font-size: 0.75rem; color: var(--text-secondary); text-align: center; margin-top: 1.5rem;">بناءً على التقارير المالية المعتمدة.</p>
        </div>
    </div>
</x-layouts.admin>


