@extends('layouts.spa')

@section('title', 'Benka - Gestion de Présence')

@section('content')
<div x-data="spaManager()" x-init="init()" class="min-h-screen bg-base-200">
    <!-- Vue Attendance -->
    <div x-show="currentView === 'attendance'" x-transition class="view-container">
        <div x-html="views.attendance"></div>
    </div>

    <!-- Vue Employees -->
    <div x-show="currentView === 'employees'" x-transition class="view-container">
        <div x-html="views.employees"></div>
    </div>

    <!-- Vue Job Roles -->
    <div x-show="currentView === 'job-roles'" x-transition class="view-container">
        <div x-html="views.jobRoles"></div>
    </div>

    <!-- Vue History -->
    <div x-show="currentView === 'history'" x-transition class="view-container">
        <div x-html="views.history"></div>
    </div>

    <!-- Vue Statistics -->
    <div x-show="currentView === 'statistics'" x-transition class="view-container">
        <div x-html="views.statistics"></div>
    </div>

    <!-- Loading spinner pour les vues non chargées -->
    <template x-if="loading">
        <div class="flex items-center justify-center min-h-screen">
            <div class="loading loading-spinner loading-lg text-primary"></div>
        </div>
    </template>
</div>

<script>
function spaManager() {
    return {
        currentView: 'attendance',
        views: {
            attendance: '',
            employees: '',
            jobRoles: '',
            history: '',
            statistics: ''
        },
        loading: false,

        async init() {
            console.log('[SPA Alpine] Initializing...');

            // Charger la vue attendance par défaut
            await this.loadView('attendance');

            // Écouter les clics sur le menu
            this.setupNavigation();
        },

        setupNavigation() {
            // Intercepter les clics sur les liens du menu
            document.querySelectorAll('[data-spa-view]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const viewName = link.dataset.spaView;
                    this.switchView(viewName);
                });
            });
        },

        async switchView(viewName) {
            console.log(`[SPA Alpine] Switching to ${viewName}`);

            // Changer la vue actuelle
            this.currentView = viewName;

            // Charger le contenu si pas encore chargé
            const viewKey = this.getViewKey(viewName);
            if (!this.views[viewKey]) {
                await this.loadView(viewName);
            }

            // Mettre à jour le menu actif
            this.updateActiveMenu(viewName);
        },

        async loadView(viewName) {
            this.loading = true;
            const viewKey = this.getViewKey(viewName);

            try {
                const url = this.getViewUrl(viewName);
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const html = await response.text();
                    this.views[viewKey] = html;
                    console.log(`[SPA Alpine] Loaded ${viewName}`);

                    // Réinitialiser les scripts après chargement
                    this.$nextTick(() => this.initViewScripts(viewName));
                }
            } catch (error) {
                console.error(`[SPA Alpine] Error loading ${viewName}:`, error);
                this.views[viewKey] = '<div class="alert alert-error">Erreur de chargement</div>';
            } finally {
                this.loading = false;
            }
        },

        getViewKey(viewName) {
            const keys = {
                'attendance': 'attendance',
                'employees': 'employees',
                'job-roles': 'jobRoles',
                'history': 'history',
                'statistics': 'statistics'
            };
            return keys[viewName] || viewName;
        },

        getViewUrl(viewName) {
            // Use SPA routes that return just HTML content
            return `/spa/view/${viewName}`;
        },

        updateActiveMenu(viewName) {
            // Retirer l'état actif de tous les liens
            document.querySelectorAll('[data-spa-view]').forEach(link => {
                link.classList.remove('text-blue-600', 'border-t-2', 'border-blue-600');
                link.classList.add('text-gray-500');

                const svg = link.querySelector('svg');
                if (svg) svg.setAttribute('fill', 'none');
            });

            // Activer le lien correspondant
            const activeLink = document.querySelector(`[data-spa-view="${viewName}"]`);
            if (activeLink) {
                activeLink.classList.remove('text-gray-500');
                activeLink.classList.add('text-blue-600', 'border-t-2', 'border-blue-600');

                const svg = activeLink.querySelector('svg');
                if (svg) svg.setAttribute('fill', 'currentColor');
            }
        },

        initViewScripts(viewName) {
            // Réinitialiser les event listeners spécifiques à chaque vue
            console.log(`[SPA Alpine] Initializing scripts for ${viewName}`);

            // Déclencher un événement personnalisé pour que les scripts de la vue s'initialisent
            window.dispatchEvent(new CustomEvent('view-loaded', { detail: { view: viewName } }));
        }
    }
}
</script>
@endsection
