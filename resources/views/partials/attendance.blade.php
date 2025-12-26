<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    @keyframes pulse-success {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .employee-card {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .employee-card:active:not(.locked) {
        transform: scale(0.95);
    }

    .success-pulse {
        animation: pulse-success 0.6s ease-out;
    }

    .badge-slide-in {
        animation: slide-in 0.3s ease-out;
    }

    @keyframes counter-pop {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .counter-pop {
        animation: counter-pop 0.3s ease-out;
    }

    @keyframes check-bounce {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(0.9); }
        50% { transform: scale(1.1); }
        75% { transform: scale(0.95); }
    }

    @keyframes circle-pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
    }

    #completion-modal.show .check-icon {
        animation: check-bounce 0.6s ease-out 0.2s;
    }

    #completion-modal.show .circle-bg {
        animation: circle-pulse 2s ease-in-out infinite;
    }

    .bg-pattern {
        background-color: hsl(var(--b2));
        background-image:
            url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
            url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%2359b5f9' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
</style>
<div class="min-h-screen bg-pattern content-wrapper">
    <!-- Header avec navigation de date -->
    <div class="sticky top-0 z-20 bg-base-100 border-b border-base-300" >
        <div class="max-w-lg mx-auto px-4">
            <div class="flex items-center justify-between gap-3 py-3">
                <button onclick="navigateDate(-1)" class="btn btn-square btn-ghost">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <button onclick="toggleCalendar()" id="date-display" class="flex-1 text-center py-2 px-4 bg-blue-600 text-white rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span></span>
                </button>

                <button onclick="navigateDate(1)" class="btn btn-square btn-ghost">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Calendar Modal -->
    <div id="calendar-modal" class="hidden fixed inset-0 z-50 bg-black/50" onclick="toggleCalendar()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-base-100 rounded-2xl shadow-xl w-full max-w-sm" onclick="event.stopPropagation()">
                <!-- Calendar Header -->
                <div class="flex items-center justify-between p-4 border-b border-base-300">
                    <button type="button" onclick="changeMonth(-1)" class="btn btn-sm btn-ghost btn-circle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <h3 id="calendar-month-year" class="font-bold text-lg"></h3>
                    <button type="button" onclick="changeMonth(1)" class="btn btn-sm btn-ghost btn-circle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <!-- Calendar Grid -->
                <div class="p-4">
                    <!-- Days of week -->
                    <div class="grid grid-cols-7 gap-1 mb-2">
                        <div class="text-center text-xs font-semibold text-base-content/60 py-2">L</div>
                        <div class="text-center text-xs font-semibold text-base-content/60 py-2">M</div>
                        <div class="text-center text-xs font-semibold text-base-content/60 py-2">M</div>
                        <div class="text-center text-xs font-semibold text-base-content/60 py-2">J</div>
                        <div class="text-center text-xs font-semibold text-base-content/60 py-2">V</div>
                        <div class="text-center text-xs font-semibold text-base-content/60 py-2">S</div>
                        <div class="text-center text-xs font-semibold text-base-content/60 py-2">D</div>
                    </div>

                    <!-- Calendar days -->
                    <div id="calendar-days" class="grid grid-cols-7 gap-1"></div>
                </div>

                <!-- Calendar Footer -->
                <div class="p-4 border-t border-base-300">
                    <button onclick="toggleCalendar()" class="btn btn-block btn-sm">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Compteurs -->
    <div id="counters-section" class="sticky top-[60px] z-10 bg-base-100 border-b border-base-300" >
        <div class="max-w-lg mx-auto px-4 py-3">
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-base-200 rounded-lg p-3 text-center">
                    <p class="text-xs text-base-content/60 uppercase font-medium">Total</p>
                    <p id="counter-total" class="text-2xl font-bold">0</p>
                </div>
                <div class="bg-success/10 rounded-lg p-3 text-center">
                    <p class="text-xs text-success uppercase font-medium">Presents</p>
                    <p id="counter-present" class="text-2xl font-bold text-success">0</p>
                </div>
                <div class="bg-error/10 rounded-lg p-3 text-center">
                    <p class="text-xs text-error uppercase font-medium">Absents</p>
                    <p id="counter-absent" class="text-2xl font-bold text-error">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des employes -->
    <div class="max-w-lg mx-auto px-4 py-4 pb-6">
        <div id="locked-warning" class="alert alert-warning mb-4 flex items-center gap-2 hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span class="text-sm font-medium">Journée verrouillée - Cliquez sur "Modifier" pour débloquer</span>
        </div>
        <div id="employee-list" class="space-y-6">
            <!-- Will be populated by JavaScript -->
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-base-300 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-base-content/40 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <p class="font-medium text-base-content/60">Chargement...</p>
            </div>
        </div>
    </div>

    <!-- Bouton terminer / réouvrir -->
    <div id="action-button-container" class="max-w-lg mx-auto px-4 pt-4 pb-24">
        <!-- Will be populated by JavaScript -->
    </div>

    <!-- Modal de confirmation de fin de journée -->
    <div id="completion-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-base-100 rounded-2xl shadow-xl w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300" id="completion-modal-content">
            <div class="p-6 text-center">
                <!-- Icône de succès animée -->
                <div class="mx-auto w-20 h-20 bg-success/10 rounded-full flex items-center justify-center mb-4 circle-bg">
                    <svg class="w-10 h-10 text-success check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold mb-2">Terminer l'appel ?</h3>
                <p class="text-base-content/60 mb-6">
                    L'appel du <span id="modal-date" class="font-semibold"></span> sera marqué comme terminé.
                    <br><br>
                    <span class="text-sm">
                        <strong class="text-success" id="modal-present-count">0</strong> présent(s) •
                        <strong class="text-error" id="modal-absent-count">0</strong> absent(s)
                    </span>
                </p>

                <div class="flex gap-3">
                    <button type="button" onclick="hideCompletionModal()" class="btn btn-ghost flex-1">
                        Annuler
                    </button>
                    <button type="button" onclick="confirmCompletion()" class="btn btn-success flex-1 gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global state
    let currentDate = new Date().toISOString().split('T')[0];
    let isCompleted = false;
    let isToggling = false;

    // Calendar functionality
    let calendarCurrentDate = new Date();
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    // Initialize on data load
    document.addEventListener('DOMContentLoaded', function() {
        if (window.appData && window.appData.loaded) {
            loadAttendance();
        } else {
            window.addEventListener('json-data-loaded', loadAttendance);
        }
    });

    function loadAttendance() {
        console.log('[Attendance] Loading attendance for date:', currentDate);
        renderEmployeeList();
        updateCounters();
        updateDateDisplay();
        updateActionButton();
        calendarCurrentDate = new Date(currentDate);
    }

    function renderEmployeeList() {
        const employees = window.jsonStorage.getEmployees();
        const jobRoles = window.jsonStorage.getJobRoles();
        const attendance = window.appData.attendance.filter(r => r.date === currentDate);

        // Filter out employees with empty names (data corruption)
        const validEmployees = employees.filter(emp =>
            emp.first_name && emp.first_name.trim() !== '' &&
            emp.last_name && emp.last_name.trim() !== ''
        );

        // Check if day is completed
        const dailyStatus = window.appData.dailyStatus.find(s => s.date === currentDate);
        isCompleted = dailyStatus?.is_completed || false;

        // Group employees by job role
        const employeesByRole = {};
        validEmployees.forEach(emp => {
            const role = jobRoles.find(r => r.id === emp.job_role_id);
            const roleName = role?.name || 'Sans métier';

            if (!employeesByRole[roleName]) {
                employeesByRole[roleName] = [];
            }

            const attendanceRecord = attendance.find(a => a.employee_id === emp.id);
            employeesByRole[roleName].push({
                ...emp,
                isPresent: attendanceRecord?.status === 'present'
            });
        });

        // Render HTML
        const employeeList = document.getElementById('employee-list');
        const lockedWarning = document.getElementById('locked-warning');
        const countersSection = document.getElementById('counters-section');

        if (isCompleted) {
            lockedWarning.classList.remove('hidden');
            countersSection.classList.add('opacity-50');
            employeeList.classList.add('opacity-50', 'pointer-events-none');
        } else {
            lockedWarning.classList.add('hidden');
            countersSection.classList.remove('opacity-50');
            employeeList.classList.remove('opacity-50', 'pointer-events-none');
        }

        const roleNames = Object.keys(employeesByRole).sort();

        if (roleNames.length === 0) {
            employeeList.innerHTML = `
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-base-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <p class="font-medium text-base-content/60">Aucun employe disponible</p>
                    <p class="text-sm text-base-content/40 mt-1">Ajoutez des employes pour commencer</p>
                </div>
            `;
            return;
        }

        employeeList.innerHTML = roleNames.map(roleName => {
            const roleEmployees = employeesByRole[roleName];

            return `
                <div>
                    <!-- Titre du metier -->
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-1 h-5 bg-blue-600 rounded-full"></div>
                        <h3 class="text-sm font-bold uppercase tracking-wide">${roleName}</h3>
                        <span class="badge badge-ghost badge-sm">${roleEmployees.length}</span>
                    </div>

                    <!-- Liste -->
                    <div class="space-y-2">
                        ${roleEmployees.map(employee => `
                            <div
                                id="employee-${employee.id}"
                                data-employee-id="${employee.id}"
                                data-is-present="${employee.isPresent ? '1' : '0'}"
                                onclick="toggleAttendance(${employee.id})"
                                class="employee-card card bg-base-100 ${isCompleted ? 'locked' : 'cursor-pointer'} ${employee.isPresent ? 'border-2 border-success bg-success/5' : 'border border-base-300 hover:border-blue-500/30'}"
                            >
                                <div class="card-body p-4 flex-row items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all ${employee.isPresent ? 'bg-success text-success-content' : 'bg-base-200'}">
                                            ${employee.isPresent ? `
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            ` : `
                                                <svg class="w-5 h-5 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            `}
                                        </div>
                                        <span class="font-medium ${employee.isPresent ? 'text-success' : ''}">
                                            ${employee.first_name} ${employee.last_name}
                                        </span>
                                    </div>
                                    ${employee.isPresent ? '<span class="badge badge-success">Present</span>' : ''}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }).join('');
    }

    function updateCounters() {
        const attendance = window.appData.attendance.filter(r => r.date === currentDate);
        const employees = window.jsonStorage.getEmployees();

        // Filter out employees with empty names (data corruption)
        const validEmployees = employees.filter(emp =>
            emp.first_name && emp.first_name.trim() !== '' &&
            emp.last_name && emp.last_name.trim() !== ''
        );

        const present = attendance.filter(a => a.status === 'present').length;
        const absent = attendance.filter(a => a.status === 'absent').length;
        const total = validEmployees.length;

        document.getElementById('counter-total').textContent = total;
        document.getElementById('counter-present').textContent = present;
        document.getElementById('counter-absent').textContent = absent;

        // Update modal counters too
        document.getElementById('modal-present-count').textContent = present;
        document.getElementById('modal-absent-count').textContent = absent;
    }

    function updateDateDisplay() {
        const date = new Date(currentDate);
        const formatted = date.toLocaleDateString('fr-FR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long'
        });
        document.querySelector('#date-display span').textContent = formatted;
        document.getElementById('modal-date').textContent = formatted;
    }

    function updateActionButton() {
        const container = document.getElementById('action-button-container');

        if (isCompleted) {
            container.innerHTML = `
                <button type="button" onclick="reopenDay()" class="btn btn-outline btn-warning w-full gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier cette journee
                </button>
            `;
        } else {
            container.innerHTML = `
                <button type="button" onclick="showCompletionModal()" class="btn bg-blue-600 hover:bg-blue-700 text-white border-0 w-full">
                    Terminer la journee
                </button>
            `;
        }
    }

    function navigateDate(delta) {
        const date = new Date(currentDate);
        date.setDate(date.getDate() + delta);
        currentDate = date.toISOString().split('T')[0];
        loadAttendance();
    }

    function toggleCalendar() {
        const modal = document.getElementById('calendar-modal');
        const isHidden = modal.classList.contains('hidden');

        if (isHidden) {
            modal.classList.remove('hidden');
            renderCalendar();
        } else {
            modal.classList.add('hidden');
        }
    }

    function changeMonth(delta) {
        calendarCurrentDate.setMonth(calendarCurrentDate.getMonth() + delta);
        renderCalendar();
    }

    function renderCalendar() {
        const year = calendarCurrentDate.getFullYear();
        const month = calendarCurrentDate.getMonth();

        // Update header
        document.getElementById('calendar-month-year').textContent = `${monthNames[month]} ${year}`;

        // Get first day of month (0 = Sunday, 1 = Monday, etc.)
        const firstDay = new Date(year, month, 1).getDay();
        // Adjust so Monday = 0
        const firstDayAdjusted = firstDay === 0 ? 6 : firstDay - 1;

        // Get number of days in month
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const daysContainer = document.getElementById('calendar-days');
        daysContainer.innerHTML = '';

        // Add empty cells for days before month starts
        for (let i = 0; i < firstDayAdjusted; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'aspect-square';
            daysContainer.appendChild(emptyCell);
        }

        // Add day cells
        const today = new Date();
        const selectedDate = new Date(currentDate);

        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement('button');
            dayCell.type = 'button';
            dayCell.textContent = day;
            dayCell.className = 'aspect-square rounded-lg text-sm font-medium transition-all hover:bg-blue-500 hover:text-white';

            const cellDate = new Date(year, month, day);

            // Highlight today
            if (cellDate.toDateString() === today.toDateString()) {
                dayCell.classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
            }

            // Highlight selected date
            if (cellDate.toDateString() === selectedDate.toDateString()) {
                dayCell.classList.add('bg-blue-500', 'text-white');
            }

            dayCell.onclick = () => selectDate(year, month, day);
            daysContainer.appendChild(dayCell);
        }
    }

    function selectDate(year, month, day) {
        currentDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        toggleCalendar();
        loadAttendance();
    }

    async function toggleAttendance(employeeId) {
        console.log('[Attendance] Toggle called for employee:', employeeId);

        // Block if attendance is completed
        if (isCompleted) {
            console.log('[Attendance] Blocked - attendance is completed');
            return;
        }

        if (isToggling) {
            console.log('[Attendance] Blocked - already toggling');
            return;
        }
        isToggling = true;

        const card = document.getElementById(`employee-${employeeId}`);
        if (!card) {
            console.error('[Attendance] Card not found for employee:', employeeId);
            isToggling = false;
            return;
        }

        const isCurrentlyPresent = card.dataset.isPresent === '1';
        console.log('[Attendance] Current status:', isCurrentlyPresent ? 'present' : 'absent');

        // Add loading animation
        card.style.transform = 'scale(0.95)';
        card.style.opacity = '0.6';

        try {
            // Use local storage
            const record = window.jsonStorage.toggleAttendance(employeeId, currentDate);
            console.log('[Attendance] Toggled locally:', record);

            // Restore card state before update
            card.style.transform = '';
            card.style.opacity = '';

            // Add success animation
            card.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
            updateEmployeeCard(employeeId, !isCurrentlyPresent);
            updateCounters();

        } catch (error) {
            console.error('[Attendance] Error during toggle:', error);
            // Restore card state on error
            card.style.transform = '';
            card.style.opacity = '';

            // Shake animation on error
            card.style.animation = 'shake 0.5s';
            setTimeout(() => {
                card.style.animation = '';
            }, 500);

            // Show error alert
            alert('Erreur lors de la modification. Veuillez réessayer.');
        } finally {
            setTimeout(() => {
                isToggling = false;
            }, 150);
        }
    }

    function updateEmployeeCard(employeeId, isPresent) {
        const card = document.getElementById(`employee-${employeeId}`);
        const indicator = card.querySelector('.w-10.h-10');
        const badge = card.querySelector('.badge');
        const name = card.querySelector('span.font-medium');

        if (isPresent) {
            card.dataset.isPresent = '1';

            // Add pulse animation on success
            card.classList.add('success-pulse');
            setTimeout(() => card.classList.remove('success-pulse'), 600);

            // Update border and background with smooth transition
            card.classList.remove('border', 'border-base-300', 'hover:border-primary/30');
            card.classList.add('border-2', 'border-success', 'bg-success/5');

            // Animate indicator with scale effect
            indicator.style.transform = 'scale(0.8)';
            setTimeout(() => {
                indicator.classList.remove('bg-base-200');
                indicator.classList.add('bg-success', 'text-success-content');
                indicator.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
                indicator.style.transform = 'scale(1.1)';
                setTimeout(() => indicator.style.transform = '', 200);
            }, 100);

            name.classList.add('text-success');

            // Add badge with slide-in animation
            if (!badge) {
                const cardBody = card.querySelector('.card-body');
                const badgeEl = document.createElement('span');
                badgeEl.className = 'badge badge-success badge-slide-in';
                badgeEl.textContent = 'Present';
                cardBody.appendChild(badgeEl);
            }
        } else {
            card.dataset.isPresent = '0';

            // Update border and background
            card.classList.add('border', 'border-base-300', 'hover:border-blue-500/30');
            card.classList.remove('border-2', 'border-success', 'bg-success/5');

            // Animate indicator with scale effect
            indicator.style.transform = 'scale(0.8)';
            setTimeout(() => {
                indicator.classList.add('bg-base-200');
                indicator.classList.remove('bg-success', 'text-success-content');
                indicator.innerHTML = `<svg class="w-5 h-5 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`;
                indicator.style.transform = 'scale(1.1)';
                setTimeout(() => indicator.style.transform = '', 200);
            }, 100);

            name.classList.remove('text-success');

            // Remove badge with fade out
            if (badge) {
                badge.style.opacity = '0';
                badge.style.transform = 'translateY(-10px)';
                setTimeout(() => badge.remove(), 300);
            }
        }

        // Animate counter that changed
        const presentEl = document.getElementById('counter-present');
        const absentEl = document.getElementById('counter-absent');

        if (isPresent) {
            presentEl.classList.add('counter-pop');
            setTimeout(() => presentEl.classList.remove('counter-pop'), 300);
        } else {
            absentEl.classList.add('counter-pop');
            setTimeout(() => absentEl.classList.remove('counter-pop'), 300);
        }
    }

    // Completion modal functions
    function showCompletionModal() {
        const modal = document.getElementById('completion-modal');
        const content = document.getElementById('completion-modal-content');

        modal.classList.remove('hidden');
        modal.classList.add('show');

        // Trigger animation after a small delay
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function hideCompletionModal() {
        const modal = document.getElementById('completion-modal');
        const content = document.getElementById('completion-modal-content');

        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('show');
        }, 300);
    }

    async function confirmCompletion() {
        // Show loading state
        const confirmBtn = event.target.closest('button');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Confirmation...
        `;

        try {
            // Use local storage
            const status = window.jsonStorage.completeDailyAttendance(currentDate);
            console.log('[Attendance] Day completed locally:', status);

            // Reload view
            hideCompletionModal();
            loadAttendance();

            // Reset button state after success
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Confirmer
            `;

        } catch (error) {
            console.error('[Attendance] Error completing day:', error);
            alert('Erreur lors de la validation de la journée');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Confirmer
            `;
        }
    }

    async function reopenDay() {
        try {
            // Use local storage
            const success = window.jsonStorage.reopenDailyAttendance(currentDate);
            console.log('[Attendance] Day reopened locally:', success);

            // Reload view
            loadAttendance();

        } catch (error) {
            console.error('[Attendance] Error reopening day:', error);
            alert('Erreur lors de la réouverture de la journée');
        }
    }
</script>
