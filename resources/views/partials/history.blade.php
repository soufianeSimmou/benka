<div class="min-h-screen content-wrapper">

    <!-- Header -->
    <div class="sticky z-20 bg-base-100 border-b border-base-300" style="top: env(safe-area-inset-top, 0px);">
        <div class="max-w-lg mx-auto px-4 py-4">
            <h1 class="text-xl font-bold mb-4">Historique</h1>

            <div class="flex gap-2">
                <div class="form-control flex-1">
                    <label class="label py-1"><span class="label-text text-xs">Debut</span></label>
                    <input type="date" id="start-date" class="input input-bordered input-sm w-full">
                </div>
                <div class="form-control flex-1">
                    <label class="label py-1"><span class="label-text text-xs">Fin</span></label>
                    <input type="date" id="end-date" class="input input-bordered input-sm w-full">
                </div>
                <div class="flex items-end">
                    <button id="load-history-btn" class="btn btn-primary btn-sm btn-square">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des employes par carte -->
    <div class="max-w-lg mx-auto px-4 py-4 pb-20">
        <div id="employee-cards" class="space-y-4">
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-base-300 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-medium text-base-content/60">Selectionnez une periode</p>
                <p class="text-sm text-base-content/40 mt-1">Pour voir l'historique des absences par personne</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date();
        const thirtyDaysAgo = new Date(today);
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

        document.getElementById('start-date').valueAsDate = thirtyDaysAgo;
        document.getElementById('end-date').valueAsDate = today;

        document.getElementById('load-history-btn').addEventListener('click', loadEmployeeSummary);

        // Wait for data to load, then load history automatically
        if (window.appData && window.appData.loaded) {
            loadEmployeeSummary();
        } else {
            window.addEventListener('json-data-loaded', loadEmployeeSummary);
        }
    });

    function loadEmployeeSummary() {
        console.log('[History] Loading employee summary...');

        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;

        if (!startDate || !endDate) {
            alert('Veuillez selectionner une plage de dates');
            return;
        }

        const employeeCards = document.getElementById('employee-cards');

        // Calculate summary from local data
        const employeeSummary = [];
        const employees = window.jsonStorage.getEmployees();
        const jobRoles = window.jsonStorage.getJobRoles();

        employees.forEach(emp => {
            // Filter attendance records for this employee within the date range
            const empAttendance = window.appData.attendance.filter(a =>
                a.employee_id === emp.id && a.date >= startDate && a.date <= endDate
            );

            // Skip employees with no attendance in this period
            if (empAttendance.length === 0) return;

            const presentDays = empAttendance.filter(a => a.status === 'present').length;
            const absences = empAttendance.filter(a => a.status === 'absent');
            const absentDays = absences.length;

            // Format absence dates
            const formattedAbsences = absences.map(a => ({
                date: a.date,
                formatted: new Date(a.date).toLocaleDateString('fr-FR', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long'
                })
            }));

            const jobRole = jobRoles.find(r => r.id === emp.job_role_id);

            employeeSummary.push({
                name: `${emp.first_name} ${emp.last_name}`,
                job_role: jobRole?.name || 'Sans métier',
                present_days: presentDays,
                absent_days: absentDays,
                absences: formattedAbsences
            });
        });

        if (employeeSummary.length === 0) {
            employeeCards.innerHTML = `
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-base-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="font-medium text-base-content/60">Aucun employe</p>
                    <p class="text-sm text-base-content/40 mt-1">Aucune donnee pour cette periode</p>
                </div>
            `;
            return;
        }

        employeeCards.innerHTML = employeeSummary.map(employee => {
            const absencesList = employee.absences.length > 0
                ? employee.absences.map(absence => `
                    <div class="flex items-start gap-2 py-1">
                        <svg class="w-4 h-4 text-error mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-sm">${escapeHtml(absence.formatted)}</span>
                    </div>
                `).join('')
                : '<p class="text-sm text-success italic py-2">Aucune absence</p>';

            return `
                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body p-4">
                        <!-- Header de la carte -->
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-base-300">
                            <div>
                                <h3 class="font-bold text-base">${escapeHtml(employee.name)}</h3>
                                <p class="text-xs text-base-content/60">${escapeHtml(employee.job_role)}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center ${employee.absent_days === 0 ? 'bg-success/10' : 'bg-base-200'}">
                                <svg class="w-5 h-5 ${employee.absent_days === 0 ? 'text-success' : 'text-base-content/40'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Liste des absences -->
                        <div class="mb-3">
                            <h4 class="text-xs font-semibold text-base-content/70 uppercase mb-2">Absences</h4>
                            <div class="space-y-1 max-h-48 overflow-y-auto">
                                ${absencesList}
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-base-300">
                            <div class="bg-success/10 rounded-lg p-2 text-center">
                                <p class="text-xs text-success uppercase font-medium">Presences</p>
                                <p class="text-lg font-bold text-success">${employee.present_days}</p>
                            </div>
                            <div class="bg-error/10 rounded-lg p-2 text-center">
                                <p class="text-xs text-error uppercase font-medium">Absences</p>
                                <p class="text-lg font-bold text-error">${employee.absent_days}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
</script>
