<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="description" content="نظام مارس سوفت - لوحة تحكم المدير">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="مارس سوفت">
    <meta name="theme-color" content="#d4af37">
    <title>{{ $title ?? 'مارس سوفت - لوحة تحكم المدير' }}</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            }
        }
    </script>
    @livewireStyles
    <style>
        :root {
            --bg-color: #0d0d0d;
            --surface-color: #1a1a1a;
            --surface-hover: #252525;
            --sidebar-color: #141414;
            --primary-color: #d4af37;
            --primary-hover: #e5c158;
            --primary-glow: rgba(212, 175, 55, 0.15);
            --text-primary: #f0f0f0;
            --text-secondary: #888888;
            --border-color: #2a2a2a;
            --success-color: #4CAF50;
            --warning-color: #FF9800;
            --danger-color: #F44336;
            --info-color: #2196F3;
            --font-family: 'Tajawal', sans-serif;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.2);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.3);
            --sidebar-width: 260px;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        /* Light Theme Override */
        body.light-theme {
            --bg-color: #f4f6f9;
            --surface-color: #ffffff;
            --surface-hover: #f8f9fa;
            --sidebar-color: #ffffff;
            --text-primary: #333333;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.1);
            --primary-glow: rgba(212, 175, 55, 0.1);
        }
        body.light-theme .form-control {
            background-color: #ffffff;
            color: #333333;
        }
        body.light-theme .form-control:focus {
            background-color: #fff;
        }
        body.light-theme .sidebar-link:hover { background: rgba(0,0,0,0.04); }
        body.light-theme .sidebar-user { background: rgba(0,0,0,0.03); }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            overflow-x: hidden;
            width: 100%;
            position: relative;
        }
        html { -webkit-text-size-adjust: 100%; -webkit-tap-highlight-color: transparent; }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            min-height: 100dvh;
            font-family: var(--font-family);
            font-size: clamp(14px, 2.5vw, 16px);
            padding-bottom: var(--safe-bottom);
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg-color); }
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-color); }

        /* ========= SIDEBAR ========= */
        .sidebar {
            position: fixed; right: 0; top: 0;
            width: var(--sidebar-width); height: 100vh; height: 100dvh;
            background: var(--sidebar-color);
            border-left: 1px solid var(--border-color);
            display: flex; flex-direction: column;
            z-index: 200; transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            padding-top: var(--safe-top);
            will-change: transform;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }

        .sidebar-brand { padding: 1.5rem; text-align: center; border-bottom: 1px solid var(--border-color); }
        .sidebar-brand h2 { color: var(--primary-color); font-size: clamp(1.1rem, 3vw, 1.3rem); font-weight: 800; }
        .sidebar-brand p { color: var(--text-secondary); font-size: 0.8rem; margin-top: 4px; }

        .sidebar-nav { flex: 1; padding: 1rem 0.8rem; overflow-y: auto; -webkit-overflow-scrolling: touch; }
        .sidebar-section { margin-bottom: 1.5rem; }
        .sidebar-section-title { color: var(--text-secondary); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 0 0.8rem; margin-bottom: 0.5rem; }

        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 0.7rem 1rem; border-radius: var(--radius-sm);
            color: var(--text-secondary); text-decoration: none;
            font-weight: 600; font-size: 0.95rem;
            transition: all 0.2s ease; margin-bottom: 2px;
            min-height: 44px;
        }
        .sidebar-link:hover { color: var(--text-primary); background: rgba(255,255,255,0.04); }
        .sidebar-link.active { color: var(--primary-color); background: var(--primary-glow); }
        .sidebar-link i { width: 20px; text-align: center; font-size: 1rem; }

        .sidebar-footer { padding: 1rem; border-top: 1px solid var(--border-color); padding-bottom: max(1rem, var(--safe-bottom)); }
        .sidebar-user { display: flex; align-items: center; gap: 10px; padding: 0.6rem 0.8rem; border-radius: var(--radius-sm); background: rgba(255,255,255,0.03); margin-bottom: 0.8rem; }
        .sidebar-user-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), #c9a020); display: flex; align-items: center; justify-content: center; color: #000; font-weight: 800; font-size: 0.9rem; flex-shrink: 0; }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-info .name { font-weight: 700; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-info .role { font-size: 0.75rem; color: var(--primary-color); }

        /* ========= MAIN CONTENT ========= */
        .main-content { margin-right: var(--sidebar-width); min-height: 100vh; min-height: 100dvh; padding: clamp(1rem, 3vw, 2rem); }
        .page-header { margin-bottom: clamp(1rem, 3vw, 2rem); }
        .page-header h1 { font-size: clamp(1.2rem, 4vw, 1.6rem); font-weight: 800; margin-bottom: 0.3rem; }
        .page-header p { color: var(--text-secondary); font-size: clamp(0.8rem, 2.5vw, 0.9rem); }

        /* ========= CARD ========= */
        .card { background-color: var(--surface-color); border-radius: var(--radius-md); padding: clamp(1rem, 3vw, 1.5rem); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); transition: box-shadow 0.25s ease; }
        .card:hover { box-shadow: var(--shadow-md); }

        /* ========= BUTTONS ========= */
        .btn { background: linear-gradient(135deg, var(--primary-color), #c9a020); color: #000; border: none; padding: 0.65rem 1.2rem; border-radius: var(--radius-sm); font-size: clamp(0.85rem, 2.5vw, 0.95rem); font-weight: 700; cursor: pointer; transition: all 0.25s ease; display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; font-family: var(--font-family); min-height: 44px; touch-action: manipulation; -webkit-tap-highlight-color: transparent; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3); }
        .btn:active { transform: translateY(0); }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.85rem; min-height: 38px; }
        .btn-outline { background: transparent; border: 1px solid rgba(212, 175, 55, 0.4); color: var(--primary-color); }
        .btn-outline:hover { background: var(--primary-glow); border-color: var(--primary-color); box-shadow: none; }
        .btn-success { background: linear-gradient(135deg, var(--success-color), #388E3C); color: white; }
        .btn-success:hover { box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); }
        .btn-warning { background: linear-gradient(135deg, var(--warning-color), #F57C00); color: black; }
        .btn-danger { background: linear-gradient(135deg, var(--danger-color), #D32F2F); color: white; }
        .btn-danger:hover { box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3); }
        .btn-ghost { background: transparent; color: var(--text-secondary); padding: 0.4rem 0.6rem; }
        .btn-ghost:hover { color: var(--text-primary); background: rgba(255,255,255,0.05); box-shadow: none; }

        .logout-btn { background: none; border: 1px solid rgba(244,67,54,0.3); color: var(--danger-color); padding: 0.5rem 1rem; border-radius: var(--radius-sm); cursor: pointer; transition: all 0.3s ease; font-weight: 700; font-size: 0.85rem; font-family: var(--font-family); display: flex; align-items: center; gap: 6px; width: 100%; justify-content: center; min-height: 44px; }
        .logout-btn:hover { background: var(--danger-color); color: white; border-color: var(--danger-color); }

        /* ========= FORMS ========= */
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; margin-bottom: 0.4rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; }
        .form-control { width: 100%; padding: 0.65rem 0.9rem; background-color: rgba(0,0,0,0.3); border: 1px solid var(--border-color); border-radius: var(--radius-sm); color: white; font-size: 1rem; font-family: var(--font-family); transition: border-color 0.3s ease, box-shadow 0.3s ease; -webkit-appearance: none; min-height: 44px; }
        .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15); }
        .form-control::placeholder { color: #555; }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23888' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: left 12px center; padding-left: 30px; }

        /* ========= GRID ========= */
        .grid { display: grid; gap: 1.5rem; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-cols-5 { grid-template-columns: repeat(5, 1fr); }
        .layout-grid, .layout-grid-reverse { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }

        @media (min-width: 1024px) {
            .layout-grid { grid-template-columns: 2fr 1fr; }
            .layout-grid-reverse { grid-template-columns: 1fr 1.2fr; }
        }

        /* ========= BADGE ========= */
        .badge { padding: 0.2rem 0.55rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
        .badge-active { background: rgba(76, 175, 80, 0.15); color: var(--success-color); border: 1px solid rgba(76,175,80,0.3); }
        .badge-inactive { background: rgba(158,158,158,0.15); color: #9e9e9e; border: 1px solid rgba(158,158,158,0.3); }
        .badge-pending { background: rgba(255, 152, 0, 0.15); color: var(--warning-color); border: 1px solid rgba(255, 152, 0, 0.3); }
        .badge-prep { background: rgba(33, 150, 243, 0.15); color: var(--info-color); border: 1px solid rgba(33, 150, 243, 0.3); }
        .badge-ready { background: rgba(76, 175, 80, 0.15); color: var(--success-color); border: 1px solid rgba(76, 175, 80, 0.3); }
        .badge-delivered { background: rgba(158, 158, 158, 0.15); color: #9e9e9e; border: 1px solid rgba(158, 158, 158, 0.3); }

        /* ========= TABLE ========= */
        .data-table { width: 100%; border-collapse: collapse; text-align: right; }
        .data-table th { padding: 10px 12px; color: var(--text-secondary); font-size: 0.8rem; font-weight: 600; border-bottom: 2px solid var(--border-color); white-space: nowrap; }
        .data-table td { padding: 10px 12px; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; }
        .data-table tr:hover td { background: rgba(255,255,255,0.02); }

        /* ========= TOAST ========= */
        .toast-container { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; width: min(90%, 400px); }
        .toast { padding: 0.8rem 1.5rem; border-radius: var(--radius-md); font-weight: 700; font-size: 1rem; animation: toastIn 0.4s ease, toastOut 0.4s ease 3s forwards; pointer-events: auto; text-align: center; }
        .toast-success { background: rgba(76,175,80,0.95); color: white; box-shadow: 0 8px 30px rgba(76,175,80,0.3); }
        @keyframes toastIn { from { opacity:0; transform:translateY(-15px); } to { opacity:1; transform:translateY(0); } }
        @keyframes toastOut { from { opacity:1; } to { opacity:0; transform:translateY(-15px); } }

        /* ========= MOBILE TOGGLE ========= */
        .sidebar-toggle {
            display: none; position: fixed;
            top: max(12px, calc(var(--safe-top) + 8px)); right: 12px;
            z-index: 300;
            background: var(--primary-color); border: none; color: #000;
            width: 46px; height: 46px; border-radius: var(--radius-sm);
            font-size: 1.3rem; cursor: pointer;
            align-items: center; justify-content: center;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .sidebar-toggle:active { transform: scale(0.92); }

        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
            z-index: 190;
            opacity: 0; transition: opacity 0.35s ease;
        }
        .sidebar-overlay.open { display: block; opacity: 1; }

        /* TABLET (≤1024px) */
        @media (max-width: 1024px) {
            .sidebar {
                transform: none;
                right: -320px; /* Hide off-screen strictly using right property */
                width: min(280px, 80vw);
                box-shadow: -5px 0 30px rgba(0,0,0,0.4);
                transition: right 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar.open { right: 0; }
            .sidebar-toggle { display: flex; }
            .main-content { margin-right: 0; padding-top: max(1rem, calc(var(--safe-top) + 4.2rem)); }
            .grid-cols-3, .grid-cols-4, .grid-cols-5 { grid-template-columns: repeat(2, 1fr); }
        }

        /* MOBILE (≤768px) */
        @media (max-width: 768px) {
            .main-content { padding: 0.7rem; padding-top: max(0.7rem, calc(var(--safe-top) + 4.2rem)); }
            .grid-cols-2, .grid-cols-3, .grid-cols-5 { grid-template-columns: repeat(2, 1fr); }
            .grid-cols-4 { grid-template-columns: repeat(2, 1fr); }
            .card { border-radius: var(--radius-sm); padding: clamp(0.6rem, 2vw, 1rem); }
            .data-table th { padding: 8px 6px; font-size: 0.73rem; }
            .data-table td { padding: 8px 6px; font-size: 0.8rem; }
            .layout-grid, .layout-grid-reverse { gap: 1rem; }
        }

        /* SMALL MOBILE (≤480px) */
        @media (max-width: 480px) {
            .sidebar { width: min(280px, 85vw); }
            .btn-sm { padding: 0.35rem 0.6rem; font-size: 0.8rem; }
            .grid { gap: 0.6rem; }
        }

        /* LANDSCAPE PHONE */
        @media (max-height: 500px) and (orientation: landscape) {
            .main-content { padding: 0.5rem 1rem; padding-top: max(0.5rem, calc(var(--safe-top) + 3.5rem)); }
            .sidebar { width: 240px; }
        }

        /* ========= PRINT STYLES ========= */
        @media print {
            body { background: white; color: black; font-size: 12pt; }
            .sidebar, .sidebar-toggle, .sidebar-overlay, .no-print { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
            .card { box-shadow: none !important; border: 1px solid #ccc !important; break-inside: avoid; background: white !important; }
            .data-table { border: 1px solid #ccc; }
            .data-table th { background: #f0f0f0 !important; color: #000 !important; border-bottom: 2px solid #aaa !important; }
            .data-table td { border-bottom: 1px solid #ddd !important; color: #000 !important; }
            .grid { gap: 0.5rem; }
            * { text-shadow: none !important; }
            @page { margin: 1cm; }
        }

        /* ===== Sidebar Dropdown / Submenu ===== */
        .sidebar-dropdown-toggle {
            width: 100%;
            background: none;
            border: none;
            font-family: var(--font-family);
            cursor: pointer;
            text-align: right;
        }
        .sidebar-submenu {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 0;
        }
        .sidebar-dropdown.open .sidebar-submenu {
            max-height: 600px;
            opacity: 1;
        }
        .sidebar-dropdown.open .sidebar-dropdown-arrow {
            transform: rotate(180deg);
        }
        .sidebar-sublink {
            padding-right: 2.2rem !important;
            font-size: 0.88rem !important;
            color: var(--text-secondary);
        }
        .sidebar-sublink:hover { color: var(--text-primary) !important; }
        .sidebar-sublink.active { color: var(--primary-color) !important; }
    </style>
    <style>
        /* Table Light Theme Tweaks */
        body.light-theme .data-table th { background-color: #f1f3f5; color: #495057; }
        body.light-theme .card[style*="background: linear-gradient(145deg, #1f1f1f, #252525)"] { background: #fff !important; border: 1px solid #dee2e6 !important; }
        body.light-theme .data-table[style*="background-color: #222"] { background-color: #fff !important; }
        body.light-theme .data-table thead[style*="background-color: #2a2a2a"] { background-color: #f8f9fa !important; }
        body.light-theme input[style*="background: #000"],
        body.light-theme select[style*="background: #000"] { background: #fff !important; color: #333 !important; }
    </style>
</head>
@php
    $appTheme = \App\Models\Setting::where('key', 'app_theme')->value('value') ?? 'dark';
@endphp
<body class="{{ $appTheme === 'light' ? 'light-theme' : '' }}">
    <!-- Mobile Toggle -->
    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
        <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="/images/mars-logo.png" alt="Mars Soft" style="width: 70px; height: 70px; border-radius: 50%; margin: 0 auto 8px; display: block; object-fit: cover; border: 2px solid var(--primary-color); box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);">
            <h2 style="font-size: 1.1rem;">مارس سوفت</h2>
            <p>لوحة تحكم المدير</p>
        </div>

        <nav class="sidebar-nav">
            @role('admin')
            <div class="sidebar-section">
                <div class="sidebar-section-title">الرئيسية</div>
                <a href="{{ route('admin') }}" class="sidebar-link {{ request()->routeIs('admin') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-chart-line"></i> لوحة التحكم
                </a>
            </div>
            @endrole

            @role('admin')
            <div class="sidebar-section">
                <div class="sidebar-section-title">العمليات</div>
                <a href="{{ route('order') }}" class="sidebar-link {{ request()->routeIs('order') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-pen-to-square"></i> إنشاء طلب
                </a>
                {{-- ===== بوابة المطابخ (نفس نظام بوابة الموارد البشرية) ===== --}}
                @php
                    $kitchenActive = request()->routeIs('kitchens.portal') || request()->routeIs('kitchen') || request()->routeIs('barista') || request()->routeIs('restaurant') || request()->routeIs('dynamic.kitchen');
                @endphp
                <a href="{{ route('kitchens.portal') }}" class="sidebar-link {{ $kitchenActive ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-kitchen-set"></i> المطابخ
                </a>
                <a href="{{ route('delivery') }}" class="sidebar-link {{ request()->routeIs('delivery') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-truck-fast"></i> الطلبات الجاهزة
                </a>
                <a href="{{ route('admin.reception') }}" class="sidebar-link {{ request()->routeIs('admin.reception') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-ticket"></i> الاستقبال
                </a>
                <a href="{{ route('admin.greetings') }}" class="sidebar-link {{ request()->routeIs('admin.greetings') || request()->routeIs('admin.greetings.reports') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-gift"></i> التحايا
                </a>
            </div>
            @endrole


            @role('admin')
            <div class="sidebar-section">
                <div class="sidebar-section-title">إدارة العملاء والولاء</div>
                <a href="{{ route('admin.customers') }}" class="sidebar-link {{ request()->routeIs('admin.customers') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-users" style="color:var(--primary-color);"></i> العملاء والرسائل
                </a>
                <a href="{{ route('admin.subscription_packages') }}" class="sidebar-link {{ request()->routeIs('admin.subscription_packages') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-crown"></i> باقات الاشتراك
                </a>
                <a href="{{ route('admin.loyalty.offers') }}" class="sidebar-link {{ request()->routeIs('admin.loyalty.offers') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-star"></i> عروض الولاء
                </a>
                <a href="{{ route('admin.loyalty.customers') }}" class="sidebar-link {{ request()->routeIs('admin.loyalty.customers') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-users-viewfinder"></i> عملاء الولاء
                </a>
                <a href="{{ route('admin.subscribed_customers') }}" class="sidebar-link {{ request()->routeIs('admin.subscribed_customers') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-user-check"></i> المشتركين
                </a>
            </div>
            @endrole

            <div class="sidebar-section">
                <div class="sidebar-section-title">الإدارة</div>
                @hasanyrole('admin|inventory_staff|daily_purchases_staff')
                @php
                    $purchasesActive = request()->routeIs('admin.purchases*') || request()->routeIs('admin.expenses') || request()->routeIs('admin.inventory') || request()->routeIs('admin.physical_inventory') || request()->routeIs('admin.daily_purchases_requests') || request()->routeIs('admin.products') || request()->routeIs('admin.reports');
                @endphp
                <a href="{{ route('admin.purchases') }}" class="sidebar-link {{ $purchasesActive ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-cart-shopping"></i> المشتريات والمخزون
                </a>
                @endhasanyrole

                @php $pendingReminders = \App\Models\Reminder::where('is_done', false)->count(); @endphp
                <a href="{{ route('admin.reminders') }}" class="sidebar-link {{ request()->routeIs('admin.reminders') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-bell"></i> التذكيرات
                    @if($pendingReminders > 0)
                        <span style="background: var(--danger-color); color: white; border-radius: 50%; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800; margin-right: auto;">{{ $pendingReminders }}</span>
                    @endif
                </a>

                @role('admin')
                @php
                    $hrActive = request()->is('hr*');
                @endphp
                <a href="{{ url('/hr/portal') }}" class="sidebar-link {{ request()->is('hr*') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-users-viewfinder"></i> الموارد البشرية
                </a>

                <a href="{{ route('admin.employees') }}" class="sidebar-link {{ request()->routeIs('admin.employees') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-users-gear"></i> الموظفين (قديم)
                </a>
                <a href="{{ route('admin.kitchens') }}" class="sidebar-link {{ request()->routeIs('admin.kitchens') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-kitchen-set"></i> إدارة المطابخ
                </a>
                <a href="{{ route('admin.artists') }}" class="sidebar-link {{ request()->routeIs('admin.artists') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-microphone-lines" style="color: #E91E63;"></i> إدارة الحفلات والحجوزات
                </a>
                <a href="{{ route('admin.bookings') }}" class="sidebar-link {{ request()->routeIs('admin.bookings') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-calendar-check" style="color: #4CAF50;"></i> حجوزات العملاء
                </a>
                <a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-gear"></i> الإعدادات
                </a>
                <a href="{{ route('admin.site') }}" class="sidebar-link {{ request()->routeIs('admin.site') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-globe"></i> إدارة الموقع العام
                </a>
                <a href="{{ route('admin.shifts') }}" class="sidebar-link {{ request()->routeIs('admin.shifts') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                    <i class="fa-solid fa-clock"></i> الفترات المحاسبية والأوقات
                </a>
                @endrole
            </div>
        </nav>

        <div class="sidebar-footer">
            <div style="text-align: center; margin-bottom: 15px; font-size: 0.75rem; color: var(--text-secondary); line-height: 1.6;">
                <img src="/images/mars-logo.png" alt="Mars Soft" style="width: 36px; height: 36px; border-radius: 50%; margin: 0 auto 6px; display: block; object-fit: cover; opacity: 0.8;">
                <div style="font-weight: 800; color: var(--primary-color); margin-bottom: 4px;">برمجة شركة مارس سوفت</div>
                <div style="font-size: 0.7rem; color: var(--text-secondary);">Mars Soft Company</div>
                <div style="margin-top: 8px; font-size: 0.7rem; display: flex; justify-content: center; gap: 10px;">
                    <a href="#" style="color: inherit; text-decoration: none; border-bottom: 1px dashed var(--text-secondary); padding-bottom: 1px; transition: all 0.2s;" onmouseover="this.style.color='var(--text-primary)'; this.style.borderColor='var(--text-primary)'" onmouseout="this.style.color='inherit'; this.style.borderColor='var(--text-secondary)'">الدعم الفني</a>
                    <span style="opacity: 0.5;">|</span>
                    <a href="#" style="color: inherit; text-decoration: none; border-bottom: 1px dashed var(--text-secondary); padding-bottom: 1px; transition: all 0.2s;" onmouseover="this.style.color='var(--text-primary)'; this.style.borderColor='var(--text-primary)'" onmouseout="this.style.color='inherit'; this.style.borderColor='var(--text-secondary)'">سياسة الخصوصية</a>
                </div>
            </div>

            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">
                        @if(auth()->user()->hasRole('admin'))
                            مدير النظام
                        @elseif(auth()->user()->hasRole('inventory_staff'))
                            موظف الجرد
                        @elseif(auth()->user()->hasRole('daily_purchases_staff'))
                            موظف مشتريات
                        @else
                            موظف
                        @endif
                    </div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <div class="toast-container" id="toastContainer"></div>

    <div class="main-content">
        @if(request()->routeIs('admin'))
            @livewire('admin.reminder-notification')
        @endif
        {{ $slot }}
    </div>

    @livewireScripts

    <script>
        function toggleDropdown(id) {
            const el = document.getElementById(id);
            if(el) {
                el.classList.toggle('open');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const btn = document.getElementById('sidebarToggle');
            const isOpen = sidebar.classList.contains('open');

            if (isOpen) {
                sidebar.classList.remove('open');
                overlay.style.opacity = '0';
                btn.innerHTML = '<i class="fa-solid fa-bars"></i>';
                setTimeout(() => { overlay.classList.remove('open'); }, 350);
            } else {
                overlay.classList.add('open');
                requestAnimationFrame(() => {
                    overlay.style.opacity = '1';
                    sidebar.classList.add('open');
                });
                btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            }
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth <= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                const btn = document.getElementById('sidebarToggle');
                sidebar.classList.remove('open');
                overlay.style.opacity = '0';
                btn.innerHTML = '<i class="fa-solid fa-bars"></i>';
                setTimeout(() => { overlay.classList.remove('open'); }, 350);
            }
        }

        // Swipe to close sidebar
        (function() {
            let touchStartX = 0, touchStartY = 0, swiping = false;
            const sidebar = document.getElementById('sidebar');

            sidebar.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                swiping = true;
            }, { passive: true });

            sidebar.addEventListener('touchmove', function(e) {
                if (!swiping) return;
                const dx = e.touches[0].clientX - touchStartX;
                const dy = Math.abs(e.touches[0].clientY - touchStartY);
                // If horizontal swipe to the right (RTL = close) and more horizontal than vertical
                if (dx > 60 && dy < 40) {
                    closeSidebarOnMobile();
                    swiping = false;
                }
            }, { passive: true });

            sidebar.addEventListener('touchend', function() { swiping = false; }, { passive: true });
        })();

        // Sound system
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        let audioCtx = null;
        function getAudioCtx() {
            if (!audioCtx) audioCtx = new AudioCtx();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            return audioCtx;
        }

        // === Browser Notifications ===
        function initNotifications() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        }

        function sendNotification(title, body, icon) {
            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    const n = new Notification(title, {
                        body: body,
                        icon: icon || '/icons/icon-192.png',
                        badge: '/icons/icon-192.png',
                        vibrate: [200, 100, 200],
                        tag: 'shisha-' + Date.now(),
                        requireInteraction: false,
                    });
                    setTimeout(() => n.close(), 6000);
                } catch(e) { console.warn('Notification error', e); }
            }
        }

        function playSound(type) {
            try {
                const ctx = getAudioCtx();
                if (type === 'newOrder') {
                    playTone(ctx, 880, 0.12, 0, 'sine', 0.5);
                    playTone(ctx, 880, 0.12, 0.18, 'sine', 0.5);
                    playTone(ctx, 1100, 0.2, 0.4, 'sine', 0.4);
                    showToast('🔔 طلب جديد وصل!', 'new-order');
                    sendNotification('🔔 طلب جديد!', 'وصل طلب جديد - يرجى التحقق فوراً');
                } else if (type === 'success') {
                    playTone(ctx, 523, 0.15, 0, 'sine', 0.4);
                    playTone(ctx, 659, 0.15, 0.12, 'sine', 0.4);
                    playTone(ctx, 784, 0.25, 0.24, 'sine', 0.35);
                    showToast('✅ تم بنجاح!', 'success');
                    sendNotification('✅ تمت العملية بنجاح', 'تم إرسال/تحديث الطلب بنجاح');
                } else if (type === 'urgent') {
                    playTone(ctx, 900, 0.25, 0, 'sawtooth', 0.5);
                    playTone(ctx, 700, 0.25, 0.3, 'sawtooth', 0.5);
                    playTone(ctx, 900, 0.25, 0.6, 'sawtooth', 0.5);
                    playTone(ctx, 700, 0.25, 0.9, 'sawtooth', 0.5);
                    showToast('🚨 تنبيه استعجال!', 'success');
                    sendNotification('🚨 استعجال طارئ!', 'تم استعجال طلب - يرجى الإسراع!');
                }
            } catch(e) { console.warn('Sound error', e); }
        }

        function playTone(ctx, freq, dur, start, wave, vol) {
            const o = ctx.createOscillator(), g = ctx.createGain();
            o.type = wave; o.frequency.setValueAtTime(freq, ctx.currentTime + start);
            g.gain.setValueAtTime(vol, ctx.currentTime + start);
            g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + start + dur);
            o.connect(g); g.connect(ctx.destination);
            o.start(ctx.currentTime + start); o.stop(ctx.currentTime + start + dur + 0.05);
        }

        function showToast(msg, type) {
            const c = document.getElementById('toastContainer'), t = document.createElement('div');
            t.className = 'toast toast-' + (type || 'success'); t.textContent = msg;
            c.appendChild(t); setTimeout(() => { if (t.parentNode) t.parentNode.removeChild(t); }, 3800);
        }

        function initAudioAndNotifications() {
            getAudioCtx();
            initNotifications();
        }
        document.addEventListener('click', initAudioAndNotifications, { once: true });
        document.addEventListener('touchstart', initAudioAndNotifications, { once: true });
        initNotifications();
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>
</body>
</html>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>
</body>
</html>
