@extends('layouts.spa')

@section('title', 'Benka - Gestion de Présence')

@section('content')
<div x-data="spaApp()" x-init="init()" class="min-h-screen">
    <!-- Loading Overlay -->
    <div x-show="loading" class="fixed inset-0 z-50 flex items-center justify-center bg-base-100/80">
        <div class="loading loading-spinner loading-lg text-primary"></div>
    </div>

    <!-- View Container -->
    <div x-show="!loading" class="view-content">
        <div x-html="currentContent"></div>
    </div>
</div>

<script>
console.log('[SPA] Alpine.js SPA Initializing...');

function spaApp() {
    return {
        currentView: 'attendance',
        currentContent: '',
        loading: true,
        cache: {},

        async init() {
            console.log('[SPA] Init called');

            // Setup menu navigation
            this.setupMenu();

            // Load initial view
            await this.loadView('attendance');

            console.log('[SPA] Initialization complete');
        },

        setupMenu() {
            // Listen for menu clicks
            const links = document.querySelectorAll('[data-spa-view]');

            if (links.length === 0) {
                console.error('[SPA] ❌ No menu links found with [data-spa-view]!');
                return;
            }

            console.log(`[SPA] ✅ Found ${links.length} menu links`);

            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const viewName = link.getAttribute('data-spa-view');
                    console.log('[SPA] Menu clicked:', viewName);
                    this.switchView(viewName);
                });
            });

            console.log('[SPA] Menu listeners attached');
        },

        async switchView(viewName) {
            console.log('[SPA] Switching to:', viewName);

            this.currentView = viewName;
            this.updateMenuActive(viewName);

            await this.loadView(viewName);
        },

        async loadView(viewName) {
            console.log('[SPA] Loading view:', viewName);
            this.loading = true;

            // Timeout après 10 secondes
            const timeoutId = setTimeout(() => {
                console.error('[SPA] ⏱️ Loading timeout for:', viewName);
                this.loading = false;
                this.currentContent = `
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="alert alert-error max-w-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <div class="font-bold">Timeout de chargement</div>
                                <div class="text-sm">La vue ${viewName} a pris trop de temps à charger</div>
                            </div>
                        </div>
                    </div>
                `;
            }, 10000);

            try {
                // Check cache first
                if (this.cache[viewName]) {
                    console.log('[SPA] 📦 Using cached content for:', viewName);
                    this.currentContent = this.cache[viewName];
                    this.loading = false;
                    clearTimeout(timeoutId);
                    this.reinitScripts();
                    return;
                }

                // Fetch view content
                const url = `/spa/view/${viewName}`;
                console.log('[SPA] 🌐 Fetching:', url);

                const response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const html = await response.text();
                console.log('[SPA] ✅ Received HTML length:', html.length);

                // Clear timeout on success
                clearTimeout(timeoutId);

                // Cache the content
                this.cache[viewName] = html;
                this.currentContent = html;

                // Reinitialize scripts after DOM update
                this.$nextTick(() => {
                    this.reinitScripts();
                });

                console.log('[SPA] ✅ View loaded successfully:', viewName);
            } catch (error) {
                console.error('[SPA] ❌ Error loading view:', error);
                clearTimeout(timeoutId);

                this.currentContent = `
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="alert alert-error max-w-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <div class="font-bold">Erreur de chargement</div>
                                <div class="text-sm">${error.message}</div>
                            </div>
                        </div>
                    </div>
                `;
            } finally {
                this.loading = false;
            }
        },

        updateMenuActive(viewName) {
            // Remove active state from all menu items
            document.querySelectorAll('[data-spa-view]').forEach(link => {
                link.classList.remove('text-blue-600', 'border-t-2', 'border-blue-600');
                link.classList.add('text-gray-500');

                const svg = link.querySelector('svg');
                if (svg) svg.setAttribute('fill', 'none');
            });

            // Add active state to current menu item
            const activeLink = document.querySelector(`[data-spa-view="${viewName}"]`);
            if (activeLink) {
                activeLink.classList.remove('text-gray-500');
                activeLink.classList.add('text-blue-600', 'border-t-2', 'border-blue-600');

                const svg = activeLink.querySelector('svg');
                if (svg) svg.setAttribute('fill', 'currentColor');
            }
        },

        reinitScripts() {
            // Execute inline scripts in the loaded content
            const scripts = document.querySelectorAll('.view-content script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            console.log('[SPA] Scripts reinitialized');
        }
    }
}
</script>
@endsection
