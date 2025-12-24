<style>
    .bg-pattern {
        background-color: hsl(var(--b2));
        background-image:
            url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
            url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%2359b5f9' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
</style>
<div class="min-h-screen bg-pattern">
    <!-- Header -->
    <div class="sticky top-0 z-20 bg-base-100 border-b border-base-300">
        <div class="max-w-lg mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <h1 class="text-xl font-bold">Statistiques</h1>
                <select id="month-selector" class="select select-bordered select-sm">
                    <!-- Options populated by JS -->
                </select>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-lg mx-auto px-4 py-4 space-y-4">
        <!-- Stats globales -->
        <div class="stats stats-vertical lg:stats-horizontal w-full bg-base-100 border border-base-300">
            <div class="stat">
                <div class="stat-title">Jours travailles</div>
                <div id="stat-days" class="stat-value text-primary">0</div>
                <div class="stat-desc">ce mois</div>
            </div>
            <div class="stat">
                <div class="stat-title">Taux de presence</div>
                <div id="stat-rate" class="stat-value text-success">0%</div>
                <div class="stat-desc">moyenne</div>
            </div>
        </div>

        <!-- Resume -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4">
                <h2 class="card-title text-sm">Resume du mois</h2>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div class="text-center p-3 bg-success/10 rounded-lg">
                        <p class="text-2xl font-bold text-success" id="total-present">0</p>
                        <p class="text-xs text-base-content/60">Total presences</p>
                    </div>
                    <div class="text-center p-3 bg-error/10 rounded-lg">
                        <p class="text-2xl font-bold text-error" id="total-absent">0</p>
                        <p class="text-xs text-base-content/60">Total absences</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top employes -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4">
                <h2 class="card-title text-sm">Meilleurs taux de presence</h2>
                <div id="top-employees" class="space-y-2 mt-2">
                    <div class="flex items-center justify-center py-8">
                        <span class="loading loading-spinner loading-md"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats par metier -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4">
                <h2 class="card-title text-sm">Presence par metier</h2>
                <div id="stats-by-role" class="space-y-3 mt-2">
                    <div class="flex items-center justify-center py-8">
                        <span class="loading loading-spinner loading-md"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendrier du mois -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4">
                <h2 class="card-title text-sm">Calendrier du mois</h2>
                <div id="calendar" class="mt-2">
                    <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-base-content/60 mb-2">
                        <div>Lun</div>
                        <div>Mar</div>
                        <div>Mer</div>
                        <div>Jeu</div>
                        <div>Ven</div>
                        <div>Sam</div>
                        <div>Dim</div>
                    </div>
                    <div id="calendar-days" class="grid grid-cols-7 gap-1">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.addEventListener('DOMContentLoaded', function() {
        initMonthSelector();
        loadStatistics();
    });

    function initMonthSelector() {
        const selector = document.getElementById('month-selector');
        const now = new Date();

        for (let i = 0; i < 12; i++) {
            const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
            const option = document.createElement('option');
            option.value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
            option.textContent = date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
            selector.appendChild(option);
        }

        selector.addEventListener('change', loadStatistics);
    }

    async function loadStatistics() {
        const month = document.getElementById('month-selector').value;
        const [year, monthNum] = month.split('-');

        try {
            const response = await fetch(`/api/statistics?year=${year}&month=${monthNum}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Erreur');

            const data = await response.json();
            updateUI(data, parseInt(year), parseInt(monthNum));
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function updateUI(data, year, month) {
        // Stats globales
        document.getElementById('stat-days').textContent = data.working_days || 0;
        document.getElementById('stat-rate').textContent = (data.average_rate || 0) + '%';
        document.getElementById('total-present').textContent = data.total_present || 0;
        document.getElementById('total-absent').textContent = data.total_absent || 0;

        // Top employes
        const topContainer = document.getElementById('top-employees');
        if (data.top_employees && data.top_employees.length > 0) {
            topContainer.innerHTML = data.top_employees.map((emp, index) => `
                <div class="flex items-center justify-between p-2 ${index === 0 ? 'bg-blue-500 text-white' : 'bg-base-200'} rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="badge ${index === 0 ? 'bg-blue-700 text-white border-0' : 'badge-ghost'} badge-sm">${index + 1}</span>
                        <span class="font-medium text-sm">${escapeHtml(emp.name)}</span>
                    </div>
                    <span class="text-sm font-bold ${emp.rate >= 80 ? 'text-success' : emp.rate >= 50 ? 'text-warning' : 'text-error'}">${emp.rate}%</span>
                </div>
            `).join('');
        } else {
            topContainer.innerHTML = '<p class="text-center text-base-content/50 py-4">Aucune donnee</p>';
        }

        // Stats par metier
        const roleContainer = document.getElementById('stats-by-role');
        if (data.by_role && data.by_role.length > 0) {
            roleContainer.innerHTML = data.by_role.map(role => `
                <div class="space-y-1">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium">${escapeHtml(role.name)}</span>
                        <span class="text-base-content/60">${role.rate}%</span>
                    </div>
                    <progress class="progress ${role.rate >= 80 ? 'progress-success' : role.rate >= 50 ? 'progress-warning' : 'progress-error'} w-full" value="${role.rate}" max="100"></progress>
                </div>
            `).join('');
        } else {
            roleContainer.innerHTML = '<p class="text-center text-base-content/50 py-4">Aucune donnee</p>';
        }

        // Calendrier
        updateCalendar(year, month, data.daily_stats || {});
    }

    function updateCalendar(year, month, dailyStats) {
        const container = document.getElementById('calendar-days');
        const firstDay = new Date(year, month - 1, 1);
        const lastDay = new Date(year, month, 0);
        const startPadding = (firstDay.getDay() + 6) % 7;

        let html = '';

        // Empty cells before first day
        for (let i = 0; i < startPadding; i++) {
            html += '<div></div>';
        }

        // Days of month
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const dateKey = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const stat = dailyStats[dateKey];
            const dayOfWeek = new Date(year, month - 1, day).getDay();
            const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
            const today = new Date();
            const currentDate = new Date(year, month - 1, day);
            const isFuture = currentDate > today;

            let bgClass = 'bg-base-200';
            let textClass = 'text-base-content/60';
            let icon = '';

            if (isFuture) {
                // Future days - gray
                bgClass = 'bg-base-300';
                textClass = 'text-base-content/40';
            } else if (stat && stat.is_completed) {
                // Completed day - green with checkmark
                bgClass = 'bg-success text-success-content';
                textClass = '';
                icon = '<svg class="w-3 h-3 absolute top-0.5 right-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
            } else if (!isWeekend) {
                // Working day not completed - red with X
                bgClass = 'bg-error/20 border border-error';
                textClass = 'text-error';
                icon = '<svg class="w-3 h-3 absolute top-0.5 right-0.5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            } else {
                // Weekend
                bgClass = 'bg-base-300';
            }

            html += `<div class="aspect-square flex items-center justify-center text-xs font-medium rounded ${bgClass} ${textClass} relative">${icon}${day}</div>`;
        }

        container.innerHTML = html;
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
</script><?php /**PATH C:\Users\Shadow\benka\resources\views/partials/statistics.blade.php ENDPATH**/ ?>