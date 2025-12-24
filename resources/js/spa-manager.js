// SPA Manager - Gère la navigation entre les vues sans rechargement de page
class SPAManager {
    constructor() {
        this.currentView = 'attendance';
        this.views = ['attendance', 'employees', 'job-roles', 'history', 'statistics'];
        this.init();
    }

    init() {
        console.log('[SPA] Initializing SPA Manager');

        // Gérer les clics sur le menu
        this.setupNavigation();

        // Charger les données initiales pour toutes les vues
        this.preloadAllViews();

        // Afficher la vue par défaut
        this.showView('attendance');
    }

    setupNavigation() {
        // Intercepter tous les clics sur les liens de navigation
        document.querySelectorAll('[data-spa-view]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const viewName = link.dataset.spaView;
                this.showView(viewName);
            });
        });
    }

    showView(viewName) {
        console.log(`[SPA] Switching to view: ${viewName}`);

        // Masquer toutes les vues
        this.views.forEach(view => {
            const viewElement = document.getElementById(`view-${view}`);
            if (viewElement) {
                viewElement.classList.add('hidden');
            }
        });

        // Afficher la vue demandée
        const targetView = document.getElementById(`view-${viewName}`);
        if (targetView) {
            targetView.classList.remove('hidden');
        }

        // Mettre à jour l'état actif du menu
        this.updateActiveMenu(viewName);

        // Stocker la vue actuelle
        this.currentView = viewName;

        // Charger les données si nécessaire
        this.loadViewData(viewName);
    }

    updateActiveMenu(viewName) {
        // Retirer l'état actif de tous les liens
        document.querySelectorAll('[data-spa-view]').forEach(link => {
            link.classList.remove('text-blue-600', 'border-t-2', 'border-blue-600');
            link.classList.add('text-gray-500');

            // Mettre à jour les SVG
            const svg = link.querySelector('svg');
            if (svg) {
                svg.setAttribute('fill', 'none');
            }
        });

        // Activer le lien correspondant
        const activeLink = document.querySelector(`[data-spa-view="${viewName}"]`);
        if (activeLink) {
            activeLink.classList.remove('text-gray-500');
            activeLink.classList.add('text-blue-600', 'border-t-2', 'border-blue-600');

            // Remplir le SVG
            const svg = activeLink.querySelector('svg');
            if (svg) {
                svg.setAttribute('fill', 'currentColor');
            }
        }
    }

    async preloadAllViews() {
        console.log('[SPA] Preloading all view data');

        // Charger employees
        await this.loadEmployees();

        // Charger job roles
        await this.loadJobRoles();
    }

    async loadViewData(viewName) {
        switch(viewName) {
            case 'employees':
                await this.loadEmployees();
                break;
            case 'job-roles':
                await this.loadJobRoles();
                break;
            case 'history':
                await this.loadHistory();
                break;
            case 'statistics':
                await this.loadStatistics();
                break;
        }
    }

    async loadEmployees() {
        // Le code existant d'employees sera intégré ici
        console.log('[SPA] Loading employees data');
    }

    async loadJobRoles() {
        // Le code existant de job-roles sera intégré ici
        console.log('[SPA] Loading job roles data');
    }

    async loadHistory() {
        // Le code existant d'history sera intégré ici
        console.log('[SPA] Loading history data');
    }

    async loadStatistics() {
        // Le code existant de statistics sera intégré ici
        console.log('[SPA] Loading statistics data');
    }
}

// Initialiser le SPA Manager au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    window.spaManager = new SPAManager();
});

export default SPAManager;
