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
                'editEmployee', 'closeEmployeeModal', 'loadEmployees',
                // Job-roles functions
                'editJob', 'closeJobModal', 'toggleJobCard', 'loadJobRoles',
                // History functions
                'loadEmployeeSummary',
                // Statistics functions
                'loadStatistics', 'updateUI', 'updateCalendar', 'escapeHtml',
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
                                if (typeof loadEmployees !== 'undefined') window.loadEmployees = loadEmployees;
                                if (typeof loadJobRoles !== 'undefined') window.loadJobRoles = loadJobRoles;
                            }

                            if ('${viewName}' === 'job-roles') {
                                if (typeof editJob !== 'undefined') window.editJob = editJob;
                                if (typeof confirmDelete !== 'undefined') window.confirmDelete = confirmDelete;
                                if (typeof closeJobModal !== 'undefined') window.closeJobModal = closeJobModal;
                                if (typeof closeDeleteModal !== 'undefined') window.closeDeleteModal = closeDeleteModal;
                                if (typeof toggleJobCard !== 'undefined') window.toggleJobCard = toggleJobCard;
                                if (typeof loadJobRoles !== 'undefined') window.loadJobRoles = loadJobRoles;
                            }

                            if ('${viewName}' === 'history') {
                                if (typeof loadEmployeeSummary !== 'undefined') window.loadEmployeeSummary = loadEmployeeSummary;
                            }

                            if ('${viewName}' === 'statistics') {
                                if (typeof loadStatistics !== 'undefined') window.loadStatistics = loadStatistics;
                                if (typeof updateUI !== 'undefined') window.updateUI = updateUI;
                                if (typeof updateCalendar !== 'undefined') window.updateCalendar = updateCalendar;
                                if (typeof escapeHtml !== 'undefined') window.escapeHtml = escapeHtml;
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

            // STEP 3: Call initialization functions that would normally run on DOMContentLoaded
            setTimeout(() => {
                if (viewName === 'job-roles' && typeof window.loadJobRoles === 'function') {
                    console.log('[SPA] 🔄 Calling loadJobRoles()');
                    window.loadJobRoles();
                }

                if (viewName === 'employees') {
                    if (typeof window.loadJobRoles === 'function') {
                        console.log('[SPA] 🔄 Calling loadJobRoles() for employees');
                        window.loadJobRoles();
                    }
                    if (typeof window.loadEmployees === 'function') {
                        console.log('[SPA] 🔄 Calling loadEmployees()');
                        window.loadEmployees();
                    }
                }

                if (viewName === 'history') {
                    console.log('[SPA] 📅 Initializing History view');

                    // STEP 1: Initialize date inputs
                    const today = new Date();
                    const thirtyDaysAgo = new Date();
                    thirtyDaysAgo.setDate(today.getDate() - 30);

                    const startInput = document.getElementById('start-date');
                    const endInput = document.getElementById('end-date');

                    if (startInput && endInput) {
                        startInput.valueAsDate = thirtyDaysAgo;
                        endInput.valueAsDate = today;
                        console.log('[SPA] ✅ Date inputs initialized');
                    }

                    // STEP 2: Attach button listener
                    const loadBtn = document.getElementById('load-summary-btn');
                    if (loadBtn) {
                        loadBtn.addEventListener('click', () => {
                            if (window.loadEmployeeSummary) window.loadEmployeeSummary();
                        });
                        console.log('[SPA] ✅ Button listener attached');
                    }

                    // STEP 3: Call load function
                    if (typeof window.loadEmployeeSummary === 'function') {
                        console.log('[SPA] 🔄 Calling loadEmployeeSummary()');
                        window.loadEmployeeSummary();
                    }
                }

                if (viewName === 'statistics') {
                    console.log('[SPA] 📊 Initializing Statistics view');

                    // STEP 1: Initialize month selector (copied from initMonthSelector)
                    const selector = document.getElementById('month-selector');
                    if (selector) {
                        const today = new Date();
                        let year = today.getFullYear();
                        let month = today.getMonth();

                        selector.innerHTML = '';
                        for (let i = 0; i < 12; i++) {
                            const option = document.createElement('option');
                            const optionDate = new Date(year, month, 1);
                            const value = `${optionDate.getFullYear()}-${String(optionDate.getMonth() + 1).padStart(2, '0')}`;
                            const label = optionDate.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long' });

                            option.value = value;
                            option.textContent = label.charAt(0).toUpperCase() + label.slice(1);

                            if (i === 0) option.selected = true;

                            selector.appendChild(option);

                            month--;
                            if (month < 0) {
                                month = 11;
                                year--;
                            }
                        }

                        // Attach change listener
                        selector.addEventListener('change', () => {
                            if (window.loadStatistics) window.loadStatistics();
                        });

                        console.log('[SPA] ✅ Month selector initialized with 12 months');
                    }

                    // STEP 2: Call load function
                    if (typeof window.loadStatistics === 'function') {
                        console.log('[SPA] 🔄 Calling loadStatistics()');
                        window.loadStatistics();
                    }
                }
            }, 50);
        }
    }
}
</script>
@endsection
