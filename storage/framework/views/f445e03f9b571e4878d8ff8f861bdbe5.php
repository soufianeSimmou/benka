<?php $__env->startSection('title', 'Benka - Gestion de Présence'); ?>

<?php $__env->startSection('content'); ?>
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

// Global helper to invalidate SPA cache from within views
window.invalidateSpaCache = function(viewName) {
    if (window.spaInstance && window.spaInstance.invalidateCache) {
        window.spaInstance.invalidateCache(viewName);
    }
};

function spaApp() {
    return {
        currentView: 'attendance',
        currentContent: '',
        loading: true,
        cache: {},

        async init() {
            console.log('[SPA] Init called');

            // Expose this instance globally for cache invalidation
            window.spaInstance = this;

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

        invalidateCache(viewName) {
            if (this.cache[viewName]) {
                delete this.cache[viewName];
                console.log(`[SPA] 🗑️ Cache invalidated for: ${viewName}`);
            }
        },

        reinitScripts() {
            const viewName = this.currentView;
            const scripts = document.querySelectorAll('.view-content script');

            console.log(`[SPA] Found ${scripts.length} scripts to reinitialize for view: ${viewName}`);

            // STEP 1: Clean up old functions from previous views
            const allFunctions = [
                // Attendance functions
                'toggleAttendance', 'toggleCalendar', 'changeMonth', 'renderCalendar', 'selectDate',
                'showCompletionModal', 'hideCompletionModal', 'confirmCompletion',
                'updateEmployeeCard', 'updateCounters',
                // Employees functions
                'editEmployee', 'closeEmployeeModal',
                // Job-roles functions
                'editJob', 'closeJobModal', 'toggleJobCard',
                // Shared functions (different per view)
                'confirmDelete', 'closeDeleteModal'
            ];

            allFunctions.forEach(fn => {
                if (window[fn]) {
                    delete window[fn];
                }
            });

            // STEP 2: Re-execute scripts and expose functions for current view
            scripts.forEach((oldScript, index) => {
                try {
                    const newScript = document.createElement('script');
                    const scriptContent = oldScript.textContent;

                    newScript.textContent = `
                        (function() {
                            ${scriptContent}

                            // Expose functions based on current view: ${viewName}

                            if ('${viewName}' === 'attendance') {
                                if (typeof toggleAttendance !== 'undefined') window.toggleAttendance = toggleAttendance;
                                if (typeof toggleCalendar !== 'undefined') window.toggleCalendar = toggleCalendar;
                                if (typeof changeMonth !== 'undefined') window.changeMonth = changeMonth;
                                if (typeof renderCalendar !== 'undefined') window.renderCalendar = renderCalendar;
                                if (typeof selectDate !== 'undefined') window.selectDate = selectDate;
                                if (typeof showCompletionModal !== 'undefined') window.showCompletionModal = showCompletionModal;
                                if (typeof hideCompletionModal !== 'undefined') window.hideCompletionModal = hideCompletionModal;
                                if (typeof confirmCompletion !== 'undefined') window.confirmCompletion = confirmCompletion;
                                if (typeof updateEmployeeCard !== 'undefined') window.updateEmployeeCard = updateEmployeeCard;
                                if (typeof updateCounters !== 'undefined') window.updateCounters = updateCounters;
                            }

                            if ('${viewName}' === 'employees') {
                                if (typeof editEmployee !== 'undefined') window.editEmployee = editEmployee;
                                if (typeof confirmDelete !== 'undefined') window.confirmDelete = confirmDelete;
                                if (typeof closeEmployeeModal !== 'undefined') window.closeEmployeeModal = closeEmployeeModal;
                                if (typeof closeDeleteModal !== 'undefined') window.closeDeleteModal = closeDeleteModal;
                            }

                            if ('${viewName}' === 'job-roles') {
                                if (typeof editJob !== 'undefined') window.editJob = editJob;
                                if (typeof confirmDelete !== 'undefined') window.confirmDelete = confirmDelete;
                                if (typeof closeJobModal !== 'undefined') window.closeJobModal = closeJobModal;
                                if (typeof closeDeleteModal !== 'undefined') window.closeDeleteModal = closeDeleteModal;
                                if (typeof toggleJobCard !== 'undefined') window.toggleJobCard = toggleJobCard;
                            }
                        })();
                    `;

                    oldScript.parentNode.replaceChild(newScript, oldScript);
                    console.log(`[SPA] ✅ Reinitialized script ${index + 1} for ${viewName}`);
                } catch (error) {
                    console.error(`[SPA] ❌ Error reinitializing script ${index + 1}:`, error);
                }
            });

            console.log(`[SPA] Scripts reinitialized for ${viewName}`);
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Shadow\benka\resources\views/spa.blade.php ENDPATH**/ ?>