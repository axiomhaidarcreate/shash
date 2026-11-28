<!-- PWA Install Prompt Component -->
<div id="pwa-install-banner" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 92%; max-width: 440px; background: rgba(20, 20, 20, 0.95); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 2px solid var(--primary-color, #d4af37); border-radius: 18px; padding: 16px 18px; box-shadow: 0 12px 40px rgba(0, 0, 0, 0.7), 0 0 25px rgba(212, 175, 55, 0.25); z-index: 999999; direction: rtl; font-family: 'Tajawal', sans-serif; animation: pwaSlideUp 0.4s ease-out;">
    <div style="display: flex; align-items: center; gap: 14px; position: relative;">
        <!-- Logo -->
        <div style="flex-shrink: 0; position: relative;">
            <img src="/images/mars-logo.png" alt="مارس لاونج" style="width: 58px; height: 58px; border-radius: 50%; object-fit: cover; border: 2px solid #d4af37; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4); display: block;">
            <span style="position: absolute; bottom: -3px; right: -3px; background: #4CAF50; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #141414;"></span>
        </div>

        <!-- Text Info -->
        <div style="flex: 1; min-width: 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                <h4 style="margin: 0; color: #fff; font-size: 1.05rem; font-weight: 800; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-mobile-screen-button" style="color: #d4af37; font-size: 0.95rem;"></i>
                    تثبيت تطبيق مارس لاونج
                </h4>
                <button type="button" id="pwa-close-btn" style="background: none; border: none; color: #888; font-size: 1.1rem; cursor: pointer; padding: 0 4px; line-height: 1;" title="إغلاق">&times;</button>
            </div>
            <p style="margin: 4px 0 0 0; color: #aaa; font-size: 0.82rem; line-height: 1.4;">
                احصل على التطبيق على جهازك لتصفح أسرع وإشعارات فورية!
            </p>
        </div>
    </div>

    <!-- iOS Instructions (shown only on iOS) -->
    <div id="pwa-ios-instructions" style="display: none; margin-top: 12px; padding: 10px 12px; background: rgba(212, 175, 55, 0.1); border: 1px dashed rgba(212, 175, 55, 0.4); border-radius: 10px; font-size: 0.82rem; color: #f0f0f0; line-height: 1.5;">
        <i class="fa-solid fa-circle-info" style="color: #d4af37;"></i>
        للتثبيت على الآيفون: اضغط على زر المشاركة 
        <span style="display: inline-block; padding: 1px 6px; background: rgba(255,255,255,0.15); border-radius: 4px; font-weight: bold;"><i class="fa-solid fa-arrow-up-from-bracket"></i></span> 
        ثم اختر <strong style="color: #d4af37;">"إضافة إلى الصفحة الرئيسية"</strong> ➕
    </div>

    <!-- Install Action Button (shown on Android / Chrome / Desktop) -->
    <div id="pwa-install-action" style="margin-top: 12px; display: flex; gap: 10px;">
        <button type="button" id="pwa-install-btn" style="flex: 1; background: linear-gradient(135deg, #d4af37, #aa820a); color: #000; font-weight: 800; font-size: 0.95rem; border: none; border-radius: 10px; padding: 10px 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.35); transition: transform 0.2s, box-shadow 0.2s;">
            <i class="fa-solid fa-download"></i>
            تثبيت التطبيق الآن
        </button>
        <button type="button" id="pwa-later-btn" style="background: rgba(255,255,255,0.08); color: #bbb; border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; padding: 10px 14px; font-size: 0.85rem; cursor: pointer;">
            لاحقاً
        </button>
    </div>
</div>

<style>
@keyframes pwaSlideUp {
    from {
        opacity: 0;
        transform: translate(-50%, 30px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}
#pwa-install-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.5);
}
</style>

<script>
(function() {
    // Check if already in standalone PWA mode
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                         window.navigator.standalone === true || 
                         document.referrer.includes('android-app://');

    if (isStandalone) {
        return; // Already running as installed app!
    }

    let deferredPrompt = null;
    const banner = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-install-btn');
    const closeBtn = document.getElementById('pwa-close-btn');
    const laterBtn = document.getElementById('pwa-later-btn');
    const iosInstructions = document.getElementById('pwa-ios-instructions');
    const installAction = document.getElementById('pwa-install-action');

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function(err) {
            console.log('SW registration error:', err);
        });
    }

    // Detect iOS
    const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent) && !window.MSStream;

    // Handle beforeinstallprompt (Android, Chrome, Edge, Windows)
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        
        // Show banner immediately on load
        if (banner) {
            banner.style.display = 'block';
        }
    });

    // If iOS Safari, show the iOS banner with instructions
    if (isIos && banner && iosInstructions && installAction) {
        // Show after a brief delay so page loads smoothly
        setTimeout(function() {
            iosInstructions.style.display = 'block';
            installAction.style.display = 'none';
            banner.style.display = 'block';
        }, 1200);
    }

    // Install button click
    if (installBtn) {
        installBtn.addEventListener('click', async function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const choiceResult = await deferredPrompt.userChoice;
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the PWA install prompt');
                    if (banner) banner.style.display = 'none';
                }
                deferredPrompt = null;
            } else {
                // If beforeinstallprompt hasn't fired yet or not supported
                alert('لتثبيت التطبيق: افتح قائمة خيارات المتصفح (⋮) ثم اضغط على "تثبيت التطبيق" أو "إضافة إلى الشاشة الرئيسية"');
            }
        });
    }

    // Dismiss buttons
    function dismissBanner() {
        if (banner) banner.style.display = 'none';
    }

    if (closeBtn) closeBtn.addEventListener('click', dismissBanner);
    if (laterBtn) laterBtn.addEventListener('click', dismissBanner);

    // Track successful install
    window.addEventListener('appinstalled', function() {
        console.log('PWA installed successfully');
        if (banner) banner.style.display = 'none';
        deferredPrompt = null;
    });
})();
</script>
