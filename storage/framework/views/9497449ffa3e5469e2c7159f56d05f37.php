<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Chargement - <?php echo e(config('app.name', 'Benka')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="/icons/icon-180x180.png">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>

    <style>
        body {
            margin: 0;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: white;
        }

        .logo {
            width: 120px;
            height: 120px;
            margin-bottom: 2rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }

        .progress-container {
            width: 280px;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .progress-bar {
            height: 100%;
            background: white;
            border-radius: 10px;
            width: 0%;
            transition: width 0.3s ease;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .loading-text {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .loading-subtext {
            font-size: 14px;
            opacity: 0.8;
            text-align: center;
        }

        .checkmark {
            display: inline-block;
            margin-left: 8px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .checkmark.visible {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <!-- Logo -->
        <img src="/logobenka.png" alt="Benka" class="logo">

        <!-- App Name -->
        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 0.5rem;">Benka</h1>
        <p style="font-size: 16px; opacity: 0.9; margin-bottom: 3rem;">Gestion de Présence</p>

        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>

        <!-- Loading Text -->
        <div class="loading-text" id="loadingText">Initialisation...</div>
        <div class="loading-subtext" id="loadingSubtext">Préparation de l'application</div>
    </div>

    <script>
        // App Preloader - Loads and caches everything for instant performance
        const AppPreloader = {
            progress: 0,
            steps: [
                { name: 'Service Worker', weight: 10 },
                { name: 'Pages HTML', weight: 20 },
                { name: 'Employés', weight: 20 },
                { name: 'Métiers', weight: 15 },
                { name: 'Présences', weight: 20 },
                { name: 'Statistiques', weight: 15 }
            ],
            currentStep: 0,

            async init() {
                console.log('[Preloader] Starting preload sequence...');

                try {
                    // Step 1: Unregister old Service Workers and clear cache
                    await this.updateProgress('Nettoyage du cache...', 5);
                    await this.cleanupOldServiceWorker();

                    // Step 2: Register new Service Worker
                    await this.updateProgress('Enregistrement Service Worker...', 50);
                    await this.registerServiceWorker();

                    // Step 3: Preload build assets (CSS/JS) by triggering one page load
                    await this.updateProgress('Chargement des ressources...', 80);
                    await fetch('/dashboard', { credentials: 'same-origin' }).catch(() => {});

                    // Step 4: Final preparation
                    await this.updateProgress('Finalisation...', 95);
                    await this.delay(300);

                    // Mark as ready
                    await this.updateProgress('Prêt!', 100);
                    localStorage.setItem('app_preloaded', Date.now());

                    // Mark session as preloaded via API call (GET to avoid CSRF)
                    await fetch('/api/mark-preloaded', {
                        credentials: 'same-origin'
                    });

                    await this.delay(500);

                    // Redirect to dashboard
                    window.location.href = '/dashboard';

                } catch (error) {
                    console.error('[Preloader] Error during preload:', error);
                    document.getElementById('loadingText').textContent = 'Erreur de chargement';
                    document.getElementById('loadingSubtext').textContent = 'Redirection...';

                    // Still redirect after error
                    await this.delay(1500);
                    window.location.href = '/dashboard';
                }
            },

            async cleanupOldServiceWorker() {
                if ('serviceWorker' in navigator) {
                    try {
                        // Unregister ALL service workers
                        const registrations = await navigator.serviceWorker.getRegistrations();
                        console.log('[Preloader] Found', registrations.length, 'service workers to unregister');

                        for (let registration of registrations) {
                            await registration.unregister();
                            console.log('[Preloader] Unregistered service worker');
                        }

                        // Clear all caches
                        const cacheNames = await caches.keys();
                        console.log('[Preloader] Found', cacheNames.length, 'caches to delete');

                        for (let cacheName of cacheNames) {
                            await caches.delete(cacheName);
                            console.log('[Preloader] Deleted cache:', cacheName);
                        }

                        console.log('[Preloader] Cleanup complete');
                        await this.delay(500);
                    } catch (error) {
                        console.warn('[Preloader] Cleanup failed:', error);
                    }
                }
            },

            async registerServiceWorker() {
                if ('serviceWorker' in navigator) {
                    try {
                        const registration = await navigator.serviceWorker.register('/sw.js');
                        console.log('[Preloader] Service Worker registered:', registration.scope);

                        // Wait for SW to be ready
                        await navigator.serviceWorker.ready;
                        await this.delay(500);
                    } catch (error) {
                        console.warn('[Preloader] Service Worker registration failed:', error);
                    }
                }
            },

            async preloadPages() {
                const pages = ['/dashboard', '/attendance', '/employees', '/job-roles', '/history', '/statistics'];
                const promises = pages.map(page =>
                    fetch(page, { credentials: 'same-origin' })
                        .then(response => {
                            console.log(`[Preloader] Cached page: ${page}`);
                            return response;
                        })
                        .catch(err => console.warn(`[Preloader] Failed to cache ${page}:`, err))
                );

                await Promise.all(promises);
            },

            async preloadData(url, key) {
                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Store in localStorage for instant access
                        localStorage.setItem(`cache_${key}`, JSON.stringify({
                            data,
                            timestamp: Date.now()
                        }));
                        console.log(`[Preloader] Cached ${key}:`, data.length || 'N/A', 'items');
                    }
                } catch (error) {
                    console.warn(`[Preloader] Failed to preload ${key}:`, error);
                }
            },

            async preloadAttendance() {
                const today = new Date().toISOString().split('T')[0];
                // Just trigger a fetch to cache it
                try {
                    await fetch(`/dashboard`, { credentials: 'same-origin' });
                } catch (error) {
                    console.warn('[Preloader] Failed to preload attendance:', error);
                }
            },

            async updateProgress(text, percent) {
                document.getElementById('loadingText').textContent = text;
                document.getElementById('progressBar').style.width = percent + '%';

                if (percent === 100) {
                    document.getElementById('loadingSubtext').innerHTML = '✓ Application prête';
                }

                // Small delay for visual feedback
                await this.delay(200);
            },

            delay(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }
        };

        // Start preloading when page loads
        document.addEventListener('DOMContentLoaded', () => {
            AppPreloader.init();
        });

        // Also start immediately if DOM is already loaded
        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            AppPreloader.init();
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\Shadow\benka\resources\views/loading.blade.php ENDPATH**/ ?>