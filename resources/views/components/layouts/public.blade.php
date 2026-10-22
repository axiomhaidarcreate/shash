<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <title>{{ $title ?? 'الروابط' }}</title>
    <meta name="description" content="صفحة الروابط العامة">
    <meta name="theme-color" content="#d4af37">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    <style>
        :root {
            --bg-color: #0d0d0d;
            --surface-color: #1a1a1a;
            --primary-color: #d4af37;
            --text-primary: #f0f0f0;
            --text-secondary: #888888;
            --border-color: #2a2a2a;
            --success-color: #4CAF50;
            --warning-color: #FF9800;
            --danger-color: #F44336;
            --info-color: #2196F3;
            --font-family: 'Tajawal', sans-serif;
            --radius-md: 12px;
            --radius-lg: 24px;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: var(--font-family);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 600px;
            padding: 2rem 1.5rem;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }

        .cover-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
            opacity: 0.6;
            mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
            -webkit-mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
        }

        .profile-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 2rem;
            margin-top: 50px;
            position: relative;
            z-index: 10;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-color);
            margin-bottom: 1rem;
            background-color: var(--surface-color);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        }

        .profile-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
            color: var(--primary-color);
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .profile-bio {
            font-size: 0.95rem;
            color: var(--text-primary);
            margin: 0;
            line-height: 1.5;
            max-width: 90%;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animated-link {
            animation: fadeInUp 0.5s ease backwards;
        }
    </style>
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
