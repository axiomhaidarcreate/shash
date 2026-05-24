<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="description" content="نظام إدارة صالة الشيشة - لوحة تحكم الموظفين">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="شيشة">
    <meta name="theme-color" content="#d4af37">
    <title>{{ $title ?? 'نظام إدارة الشيشة' }}</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    <style>
        :root {
            --bg-color: #0d0d0d;
            --surface-color: #1a1a1a;
            --surface-hover: #252525;
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
            /* Safe areas for notch devices */
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html {
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            font-family: var(--font-family);
            font-size: clamp(14px, 2.5vw, 16px);
            padding-bottom: var(--safe-bottom);
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg-color); }
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 3px; }

        /* ========= NAVBAR ========= */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.7rem clamp(0.8rem, 3vw, 1.5rem);
            padding-top: max(0.7rem, calc(var(--safe-top) + 0.4rem));
            background: rgba(26, 26, 26, 0.95);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            gap: 8px;
            flex-wrap: wrap;
        }

        .navbar-brand {
            font-size: clamp(1rem, 3vw, 1.2rem);
            font-weight: 800;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .navbar-nav {
            display: flex;
            gap: 4px;
            list-style: none;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            flex-shrink: 1;
            min-width: 0;
        }
        .navbar-nav::-webkit-scrollbar { display: none; }

        .navbar-nav a {
            padding: 0.45rem 0.8rem;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
            font-size: clamp(0.8rem, 2vw, 0.85rem);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            min-height: 44px;
        }

        .navbar-nav a:hover, .navbar-nav a.active {
            color: var(--primary-color);
            background: var(--primary-glow);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .navbar-user .user-info {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0.35rem 0.6rem;
            border-radius: var(--radius-sm);
            background: rgba(255,255,255,0.03);
            white-space: nowrap;
        }

        .logout-btn {
            background: none;
            border: 1px solid rgba(244,67,54,0.4);
            color: var(--danger-color);
            padding: 0.35rem 0.7rem;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 700;
            font-size: 0.8rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-family: var(--font-family);
            white-space: nowrap;
            min-height: 44px;
            min-width: 44px;
            justify-content: center;
        }

        .logout-btn:hover {
            background: var(--danger-color);
            color: white;
            border-color: var(--danger-color);
        }

        /* ========= MAIN ========= */
        main {
            flex: 1;
            padding: clamp(0.75rem, 3vw, 1.5rem);
            max-width: 1500px;
            margin: 0 auto;
            width: 100%;
        }

        /* ========= CARD ========= */
        .card {
            background-color: var(--surface-color);
            border-radius: var(--radius-md);
            padding: clamp(0.8rem, 3vw, 1.5rem);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: box-shadow 0.25s ease;
        }

        /* ========= BUTTONS ========= */
        .btn {
            background: linear-gradient(135deg, var(--primary-color), #c9a020);
            color: #000;
            border: none;
            padding: 0.65rem 1.2rem;
            border-radius: var(--radius-sm);
            font-size: clamp(0.85rem, 2.5vw, 0.95rem);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            font-family: var(--font-family);
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
            min-height: 44px;
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn:active { transform: scale(0.97); }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            min-height: 38px;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid rgba(212, 175, 55, 0.4);
            color: var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-glow);
            border-color: var(--primary-color);
            box-shadow: none;
        }

        .btn-success { background: linear-gradient(135deg, var(--success-color), #388E3C); color: white; }
        .btn-success:hover { box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); }

        .btn-warning { background: linear-gradient(135deg, var(--warning-color), #F57C00); color: black; }

        .btn-danger { background: linear-gradient(135deg, var(--danger-color), #D32F2F); color: white; }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            padding: 0.4rem 0.6rem;
        }
        .btn-ghost:hover { color: var(--text-primary); background: rgba(255,255,255,0.05); box-shadow: none; }

        /* ========= FORMS ========= */
        .form-group { margin-bottom: 1rem; }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.85rem;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 0.9rem;
            background-color: rgba(0,0,0,0.3);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            color: white;
            font-size: 1rem;
            font-family: var(--font-family);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            -webkit-appearance: none;
            min-height: 44px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }

        .form-control::placeholder { color: #555; }

        /* ========= GRID ========= */
        .grid { display: grid; gap: 1rem; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        
        /* Layout Grid helper */
        .layout-grid, .layout-grid-reverse {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        /* ========= DATA TABLE ========= */
        .data-table { width: 100%; border-collapse: collapse; text-align: right; }
        .data-table th { padding: 10px 12px; color: var(--text-secondary); font-size: 0.8rem; font-weight: 600; border-bottom: 2px solid var(--border-color); white-space: nowrap; }
        .data-table td { padding: 10px 12px; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; }
        .data-table tr:hover td { background: rgba(255,255,255,0.02); }

        /* ========= BADGES ========= */
        .badge {
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .badge-pending { background: rgba(255, 152, 0, 0.15); color: var(--warning-color); border: 1px solid rgba(255, 152, 0, 0.3); }
        .badge-prep { background: rgba(33, 150, 243, 0.15); color: var(--info-color); border: 1px solid rgba(33, 150, 243, 0.3); }
        .badge-ready { background: rgba(76, 175, 80, 0.15); color: var(--success-color); border: 1px solid rgba(76, 175, 80, 0.3); }
        .badge-delivered { background: rgba(158, 158, 158, 0.15); color: #9e9e9e; border: 1px solid rgba(158, 158, 158, 0.3); }
        .badge-cancelled { background: rgba(244, 67, 54, 0.15); color: var(--danger-color); border: 1px solid rgba(244, 67, 54, 0.3); }

        /* ========= TOAST ========= */
        .toast-container {
            position: fixed; top: max(env(safe-area-inset-top, 10px), 70px); left: 50%; transform: translateX(-50%);
            z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none;
            width: min(90%, 400px);
        }
        .toast {
            padding: 0.7rem 1.2rem; border-radius: var(--radius-md); font-weight: 700; font-size: clamp(0.85rem, 2.5vw, 0.95rem);
            animation: toastIn 0.4s ease, toastOut 0.4s ease 3s forwards; pointer-events: auto; text-align: center;
        }
        .toast-new-order { background: rgba(255, 152, 0, 0.95); color: white; box-shadow: 0 8px 30px rgba(255, 152, 0, 0.4); }
        .toast-ready { background: rgba(76, 175, 80, 0.95); color: white; box-shadow: 0 8px 30px rgba(76, 175, 80, 0.4); }
        .toast-delivered { background: rgba(212, 175, 55, 0.95); color: black; box-shadow: 0 8px 30px rgba(212, 175, 55, 0.4); }

        @keyframes toastIn { from { opacity:0; transform:translateY(-15px); } to { opacity:1; transform:translateY(0); } }
        @keyframes toastOut { from { opacity:1; } to { opacity:0; transform:translateY(-15px); } }

        /* =============== RESPONSIVE =============== */

        /* DESKTOP (1024px+) */
        @media (min-width: 1024px) {
            .layout-grid { grid-template-columns: 2fr 1fr; }
            .layout-grid-reverse { grid-template-columns: 1fr 1.2fr; }
        }

        /* TABLET (768px - 1023px) */
        @media (max-width: 1023px) {
            .grid-cols-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-cols-3 { grid-template-columns: repeat(2, 1fr); }
        }

        /* MOBILE (max 768px) */
        @media (max-width: 768px) {
            .navbar {
                flex-wrap: wrap;
                padding: max(0.6rem, calc(var(--safe-top) + 0.3rem)) 0.8rem 0.6rem 0.8rem;
            }
            .navbar-brand { font-size: 1rem; }

            .navbar-nav {
                order: 3;
                width: 100%;
                padding: 0.3rem 0;
                gap: 2px;
                justify-content: flex-start;
            }
            .navbar-nav a {
                font-size: 0.82rem;
                padding: 0.5rem 0.6rem;
                flex: 0 0 auto;
                min-height: 44px;
            }

            .navbar-user .user-info span { display: none; }
            .navbar-user .user-info { padding: 0.3rem; font-size: 1.2rem; min-height: 44px; min-width: 44px; justify-content: center; }
            .logout-btn span { display: inline; font-size: 0.85rem; }
            .logout-btn {
                background: rgba(244,67,54,0.12);
                border: 1px solid rgba(244,67,54,0.5);
                color: var(--danger-color);
                font-size: 1rem;
                padding: 0.4rem 0.8rem;
                min-height: 44px;
                border-radius: var(--radius-sm);
                gap: 6px;
            }

            .grid-cols-2 { grid-template-columns: 1fr; }
            .grid-cols-3 { grid-template-columns: 1fr; }
            .grid-cols-4 { grid-template-columns: repeat(2, 1fr); }

            h2 { font-size: clamp(1.1rem, 4vw, 1.3rem); }
            h3 { font-size: clamp(0.95rem, 3.5vw, 1.1rem); }
        }

        /* SMALL MOBILE (max 480px) */
        @media (max-width: 480px) {
            .grid-cols-4 { grid-template-columns: repeat(2, 1fr); }
            
            .card { border-radius: var(--radius-sm); }
            
            .btn { font-size: 0.9rem; padding: 0.65rem 0.8rem; }
            .btn-sm { padding: 0.4rem 0.6rem; font-size: 0.8rem; }

            .data-table th { padding: 8px 8px; font-size: 0.75rem; }
            .data-table td { padding: 8px 8px; font-size: 0.82rem; }
        }

        /* VERY SMALL MOBILE (max 360px) */
        @media (max-width: 360px) {
            .grid-cols-4 { grid-template-columns: 1fr 1fr; }
            .navbar-brand { font-size: 0.95rem; gap: 5px; }
            .navbar-nav a { padding: 0.4rem 0.5rem; font-size: 0.78rem; }
        }

        /* Landscape orientation for phones */
        @media (max-height: 500px) and (orientation: landscape) {
            .navbar {
                padding: 0.4rem 1rem;
                flex-wrap: nowrap;
            }
            .navbar-nav {
                order: unset;
                width: auto;
                padding: 0;
            }
            main { padding: 0.5rem 1rem; }
        }
    </style>
</head>
<body>
    @auth
    <nav class="navbar">
        <div class="navbar-brand">
            <i class="fa-solid fa-wind"></i>
            لاونج الشيشة
        </div>

        <ul class="navbar-nav">
            @if(auth()->user()->hasRole('admin'))
                <li><a href="{{ route('admin') }}" class="{{ request()->routeIs('admin') ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i> <span>لوحة التحكم</span></a></li>
                <li><a href="{{ route('order') }}" class="{{ request()->routeIs('order') ? 'active' : '' }}"><i class="fa-solid fa-pen-to-square"></i> <span>الطلبات</span></a></li>
                <li><a href="{{ route('kitchen') }}" class="{{ request()->routeIs('kitchen') ? 'active' : '' }}"><i class="fa-solid fa-fire"></i> <span>المطبخ</span></a></li>
                <li><a href="{{ route('delivery') }}" class="{{ request()->routeIs('delivery') ? 'active' : '' }}"><i class="fa-solid fa-truck-fast"></i> <span>الطلبات الجاهزة</span></a></li>
            @elseif(auth()->user()->hasRole('order_staff'))
                <li><a href="{{ route('order') }}" class="active"><i class="fa-solid fa-pen-to-square"></i> الطلبات</a></li>
            @elseif(auth()->user()->hasRole('kitchen_staff'))
                <li><a href="{{ route('kitchen') }}" class="active"><i class="fa-solid fa-fire"></i> المطبخ</a></li>
            @elseif(auth()->user()->hasRole('delivery_staff'))
                <li><a href="{{ route('delivery') }}" class="active"><i class="fa-solid fa-truck-fast"></i> الطلبات الجاهزة</a></li>
            @endif
        </ul>

        <div class="navbar-user">
            <div class="user-info">
                <i class="fa-solid fa-user-circle" style="color: var(--primary-color);"></i>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> <span>خروج</span></button>
            </form>
        </div>
    </nav>
    @endauth

    <div class="toast-container" id="toastContainer"></div>

    <main>
        {{ $slot }}
    </main>

    <footer style="margin-top: auto; padding: 1.5rem; text-align: center; border-top: 1px solid var(--border-color); background: rgba(0,0,0,0.2);">
        <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.8;">
            <div style="font-weight: 800; color: var(--primary-color); font-size: 0.95rem; margin-bottom: 4px;">تطوير المهندس: مازن أحمد حيدر</div>
            <div style="margin: 8px 0;">
                <a href="https://wa.me/967716053268" target="_blank" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: color 0.2s; padding: 4px 10px; background: rgba(255,255,255,0.05); border-radius: 20px;" onmouseover="this.style.color='#25D366'" onmouseout="this.style.color='inherit'">
                    <i class="fa-brands fa-whatsapp" style="font-size: 1rem;"></i> <span dir="ltr" style="font-family: monospace; font-size: 0.9rem;">+967 716 053 268</span>
                </a>
            </div>
            <div style="margin-top: 12px; font-size: 0.8rem; display: flex; justify-content: center; gap: 15px;">
                <a href="#" style="color: inherit; text-decoration: none; border-bottom: 1px dashed var(--text-secondary); padding-bottom: 2px; transition: all 0.2s;" onmouseover="this.style.color='var(--text-primary)'; this.style.borderColor='var(--text-primary)'" onmouseout="this.style.color='inherit'; this.style.borderColor='var(--text-secondary)'">الدعم الفني</a>
                <span style="opacity: 0.3;">|</span>
                <a href="#" style="color: inherit; text-decoration: none; border-bottom: 1px dashed var(--text-secondary); padding-bottom: 2px; transition: all 0.2s;" onmouseover="this.style.color='var(--text-primary)'; this.style.borderColor='var(--text-primary)'" onmouseout="this.style.color='inherit'; this.style.borderColor='var(--text-secondary)'">سياسة الخصوصية</a>
            </div>
        </div>
    </footer>

    @livewireScripts

    <script>
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        let audioCtx = null;
        function getAudioCtx() { if (!audioCtx) audioCtx = new AudioCtx(); return audioCtx; }

        function playSound(type) {
            try {
                const ctx = getAudioCtx();
                if (type === 'newOrder') {
                    playTone(ctx, 880, 0.12, 0, 'sine', 0.5);
                    playTone(ctx, 880, 0.12, 0.18, 'sine', 0.5);
                    playTone(ctx, 1100, 0.2, 0.4, 'sine', 0.4);
                    showToast('🔔 طلب جديد وصل!', 'toast-new-order');
                } else if (type === 'success') {
                    playTone(ctx, 523, 0.15, 0, 'sine', 0.4);
                    playTone(ctx, 659, 0.15, 0.12, 'sine', 0.4);
                    playTone(ctx, 784, 0.25, 0.24, 'sine', 0.35);
                    showToast('✅ تم بنجاح!', 'toast-delivered');
                } else if (type === 'ready') {
                    playTone(ctx, 660, 0.12, 0, 'triangle', 0.4);
                    playTone(ctx, 880, 0.12, 0.15, 'triangle', 0.4);
                    playTone(ctx, 1046, 0.2, 0.3, 'triangle', 0.35);
                    showToast('🟢 طلب جاهز للتوصيل!', 'toast-ready');
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

        function showToast(msg, cls) {
            const c = document.getElementById('toastContainer'), t = document.createElement('div');
            t.className = 'toast ' + cls; t.textContent = msg;
            c.appendChild(t); setTimeout(() => { if (t.parentNode) t.remove(); }, 3800);
        }

        document.addEventListener('click', function init() { getAudioCtx(); document.removeEventListener('click', init); }, { once: true });
        document.addEventListener('touchstart', function init() { getAudioCtx(); document.removeEventListener('touchstart', init); }, { once: true });

        // Silent Print Listeners
        document.addEventListener('livewire:init', () => {
            Livewire.on('print-kitchen-silent', (event) => {
                let orderIds = event.orderIds || (event[0] ? event[0].orderIds : null);
                if (orderIds) {
                    printSilent('/invoice/kitchen-print-batch?ids=' + orderIds + '&auto=1');
                }
            });

            Livewire.on('print-receipt-silent', (event) => {
                let orderId = event.orderId || (event[0] ? event[0].orderId : null);
                if (orderId) {
                    printSilent('/invoice/print/' + orderId + '?auto=1');
                }
            });

            function printSilent(url) {
                let iframe = document.getElementById('silentPrintIframe');
                if (iframe) {
                    iframe.remove();
                }
                iframe = document.createElement('iframe');
                iframe.id = 'silentPrintIframe';
                iframe.style.position = 'fixed';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                document.body.appendChild(iframe);

                iframe.onload = function() {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch (e) {
                        console.error('Silent print failed, opening fallback window', e);
                        window.open(url, '_blank', 'width=400,height=600');
                    }
                };
                iframe.src = url;
            }
        });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>
</body>
</html>
