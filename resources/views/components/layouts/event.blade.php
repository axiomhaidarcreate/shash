<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <title>{{ $title ?? 'الطلب الذاتي - الحفلات' }}</title>
    <meta name="theme-color" content="#d4af37">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (via CDN for standalone layout) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Tajawal', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: '#d4af37',
                        secondary: '#1a1a1a',
                        dark: '#0a0a0a',
                    },
                    animation: {
                        'gradient-x': 'gradient-x 15s ease infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        'gradient-x': {
                            '0%, 100%': {
                                'background-size': '200% 200%',
                                'background-position': 'left center'
                            },
                            '50%': {
                                'background-size': '200% 200%',
                                'background-position': 'right center'
                            }
                        },
                        'float': {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0a0a0a;
            color: #ffffff;
            font-family: 'Tajawal', sans-serif;
            overflow-x: hidden;
        }

        /* Glassmorphism utilities */
        .glass {
            background: rgba(20, 20, 20, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .glass-card {
            background: linear-gradient(145deg, rgba(30, 30, 30, 0.6), rgba(15, 15, 15, 0.8));
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255,255,255,0.05);
        }

        .glass-input {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            transition: all 0.3s ease;
        }

        .glass-input:focus {
            border-color: #d4af37;
            background: rgba(20, 20, 20, 0.8);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.2);
            outline: none;
        }

        /* Ambient Glow Background */
        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            overflow: hidden;
            background: #050505;
        }

        .ambient-glow-1 {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(212,175,55,0.15) 0%, rgba(0,0,0,0) 70%);
            filter: blur(60px);
            animation: float 8s ease-in-out infinite alternate;
        }

        .ambient-glow-2 {
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 60vw;
            height: 60vw;
            background: radial-gradient(circle, rgba(156,39,176,0.1) 0%, rgba(0,0,0,0) 70%);
            filter: blur(80px);
            animation: float 12s ease-in-out infinite alternate-reverse;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 4px; }

        .btn-premium {
            background: linear-gradient(135deg, #d4af37, #aa8222);
            color: #000;
            font-weight: 800;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-shadow: 0 1px 2px rgba(255,255,255,0.3);
        }

        .btn-premium::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            20% { left: 200%; }
            100% { left: 200%; }
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.4);
        }
        
        .btn-premium:active {
            transform: translateY(1px);
        }

        .item-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .item-card:hover {
            transform: translateY(-3px);
            border-color: #d4af37;
            box-shadow: 0 10px 20px rgba(0,0,0,0.4), 0 0 15px rgba(212,175,55,0.15);
        }
    </style>
    @livewireStyles
</head>
<body class="antialiased selection:bg-primary selection:text-black">
    <!-- Ambient Background -->
    <div class="ambient-bg">
        <div class="ambient-glow-1"></div>
        <div class="ambient-glow-2"></div>
    </div>

    <main class="relative z-10 min-h-screen flex flex-col pb-10">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
