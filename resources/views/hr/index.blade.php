<x-layouts.admin>
    <div class="page-header" style="text-align: center; margin-bottom: 3rem; margin-top: 2rem;">
        <h1 style="font-size: 2rem;"><i class="fa-solid fa-users-viewfinder" style="color: var(--primary-color);"></i> بوابة الموارد البشرية</h1>
        <p style="font-size: 1.1rem; color: var(--text-secondary); margin-top: 0.5rem;">اختر القسم الذي تريد إدارته</p>
    </div>

    <!-- HR Navigation Cards -->
    <div class="grid grid-cols-3 mb-6" style="gap: 1.5rem; max-width: 1000px; margin: 0 auto;">
        <a href="{{ url('/hr/dashboard') }}" class="card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; padding: 2rem; transition: all 0.3s ease; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(212, 175, 55, 0.15); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.3rem; font-weight: 800;">الرئيسية</h3>
        </a>

        <a href="{{ url('/hr/employees') }}" class="card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; padding: 2rem; transition: all 0.3s ease; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(76, 175, 80, 0.15); color: #4CAF50; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fa-solid fa-users"></i>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.3rem; font-weight: 800;">الموظفين</h3>
        </a>

        <a href="{{ url('/hr/attendance') }}" class="card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; padding: 2rem; transition: all 0.3s ease; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(33, 150, 243, 0.15); color: #2196F3; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.3rem; font-weight: 800;">الحضور والانصراف</h3>
        </a>

        <a href="{{ url('/hr/payroll') }}" class="card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; padding: 2rem; transition: all 0.3s ease; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(255, 152, 0, 0.15); color: #FF9800; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.3rem; font-weight: 800;">الرواتب</h3>
        </a>

        <a href="{{ url('/hr/requests') }}" class="card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; padding: 2rem; transition: all 0.3s ease; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(233, 30, 99, 0.15); color: #E91E63; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.3rem; font-weight: 800;">الطلبات</h3>
        </a>

        <a href="{{ url('/hr/entitlements') }}" class="card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; padding: 2rem; transition: all 0.3s ease; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(156, 39, 176, 0.15); color: #9C27B0; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.3rem; font-weight: 800;">المستحقات</h3>
        </a>

        <a href="{{ url('/hr/custodies') }}" class="card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; padding: 2rem; transition: all 0.3s ease; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(96, 125, 139, 0.15); color: #607D8B; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.3rem; font-weight: 800;">العهد</h3>
        </a>

        <a href="{{ url('/hr/tasks') }}" class="card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; padding: 2rem; transition: all 0.3s ease; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(244, 67, 54, 0.15); color: #F44336; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.3rem; font-weight: 800;">المهام</h3>
        </a>

        <a href="{{ url('/hr/reports') }}" class="card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; padding: 2rem; transition: all 0.3s ease; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(0, 188, 212, 0.15); color: #00BCD4; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.3rem; font-weight: 800;">التقارير</h3>
        </a>
    </div>

    <!-- Styles for hover effect -->
    <style>
        .grid-cols-3 a.card:hover {
            transform: translateY(-8px);
            border-color: var(--primary-color);
            box-shadow: 0 12px 30px rgba(212, 175, 55, 0.25);
        }
    </style>
</x-layouts.admin>
