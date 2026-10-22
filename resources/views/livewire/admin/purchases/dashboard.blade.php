<div>
    <div class="page-header">
        <h1>قسم المشتريات</h1>
        <p>إدارة المشتريات، الفواتير، الموردين، وحالة المخزون والجرد</p>
    </div>

    <div class="grid grid-cols-5">
        <!-- فواتير المشتريات -->
        <a href="{{ route('admin.purchases.invoices') }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="text-align: center; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 15px; padding: 2rem;">
                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 3rem; color: #4CAF50;"></i>
                <h3 style="font-size: 1.2rem;">فواتير المشتريات</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">إدارة وعرض وتسجيل فواتير الشراء</p>
            </div>
        </a>

        <!-- المصروفات -->
        <a href="{{ route('admin.expenses') }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="text-align: center; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 15px; padding: 2rem;">
                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 3rem; color: #F44336;"></i>
                <h3 style="font-size: 1.2rem;">المصروفات</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">إدارة المصروفات والالتزامات</p>
            </div>
        </a>

        <!-- الموردين -->
        <a href="{{ route('admin.purchases.suppliers') }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="text-align: center; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 15px; padding: 2rem;">
                <i class="fa-solid fa-users" style="font-size: 3rem; color: #2196F3;"></i>
                <h3 style="font-size: 1.2rem;">الموردين</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">إدارة بيانات الموردين وأرصدتهم</p>
            </div>
        </a>

        <!-- الجرد -->
        <a href="{{ route('admin.inventory') }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="text-align: center; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 15px; padding: 2rem;">
                <i class="fa-solid fa-boxes-stacked" style="font-size: 3rem; color: #FF9800;"></i>
                <h3 style="font-size: 1.2rem;">الجرد</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">جرد وتقييم الموجودات والتوالف</p>
            </div>
        </a>

        <!-- المخزون -->
        <a href="{{ route('admin.products') }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="text-align: center; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 15px; padding: 2rem;">
                <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #9C27B0;"></i>
                <h3 style="font-size: 1.2rem;">المخزون والمنتجات</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">إدارة الأصناف والمخزون الحالي</p>
            </div>
        </a>

        <!-- الجرد الفعلي -->
        <a href="{{ route('admin.physical_inventory') }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="text-align: center; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 15px; padding: 2rem;">
                <i class="fa-solid fa-clipboard-check" style="font-size: 3rem; color: #00BCD4;"></i>
                <h3 style="font-size: 1.2rem;">الجرد الفعلي</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">إجراء عمليات الجرد الفعلية وتسويتها</p>
            </div>
        </a>

        <!-- طلبات المشتريات اليومية -->
        <a href="{{ route('admin.daily_purchases_requests') }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="text-align: center; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 15px; padding: 2rem;">
                <i class="fa-solid fa-cart-arrow-down" style="font-size: 3rem; color: #E91E63;"></i>
                <h3 style="font-size: 1.2rem;">طلبات المشتريات اليومية</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">إدارة النواقص ومشتريات المطابخ اليومية</p>
            </div>
        <!-- التقارير -->
        <a href="{{ route('admin.reports') }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="text-align: center; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 15px; padding: 2rem;">
                <i class="fa-solid fa-chart-pie" style="font-size: 3rem; color: #FF5722;"></i>
                <h3 style="font-size: 1.2rem;">التقارير</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">عرض تقارير النظام الشاملة والمبيعات</p>
            </div>
        </a>
    </div>
</div>
