<style>
    .bg-pattern {
        background-color: #f3f4f6;
        background-image:
            url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
            url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%2359b5f9' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
</style>
<div class="min-h-screen bg-pattern" style="margin-top: env(safe-area-inset-top, 0px);">
    <!-- Header with safe area padding -->
    <div class="sticky z-20 bg-base-100 border-b border-base-300" style="top: env(safe-area-inset-top, 0px);">
        <div class="max-w-lg mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <h1 class="text-xl font-bold">Employes</h1>
                <button id="add-employee-btn" type="button" class="py-2 px-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter
                </button>
            </div>
        </div>
    </div>

    <!-- Liste -->
    <div class="max-w-lg mx-auto px-4 py-4">
        <div id="employee-list" class="space-y-3"></div>
        <div id="empty-state" class="text-center py-16 hidden">
            <div class="w-16 h-16 bg-base-300 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <p class="font-medium text-base-content/60">Aucun employe</p>
            <p class="text-sm text-base-content/40 mt-1">Ajoutez votre premier employe</p>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier -->
<dialog id="employee-modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 id="modal-title" class="font-bold text-lg mb-4">Ajouter un employe</h3>

        <!-- Zone d'erreur -->
        <div id="employee-error-zone" class="hidden bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p id="employee-error-message" class="text-sm font-medium text-red-800"></p>
                </div>
            </div>
        </div>

        <form id="employee-form" class="space-y-4">
            <div class="form-control">
                <label class="label"><span class="label-text">Prenom <span class="text-red-500">*</span></span></label>
                <input type="text" id="first-name" name="first_name" required maxlength="255" placeholder="Jean" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <span class="text-xs text-gray-500 mt-1">Entrez le prénom de l'employé</span>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Nom <span class="text-red-500">*</span></span></label>
                <input type="text" id="last-name" name="last_name" required maxlength="255" placeholder="Dupont" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <span class="text-xs text-gray-500 mt-1">Entrez le nom de famille de l'employé</span>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Metier <span class="text-red-500">*</span></span></label>
                <select id="job-role" name="job_role_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">Selectionner un metier</option>
                </select>
                <span class="text-xs text-gray-500 mt-1">Choisissez le métier de l'employé. <a href="#" onclick="alert('Allez dans la section Métiers pour en ajouter un nouveau'); return false;" class="text-blue-600 hover:underline">Ajouter un métier?</a></span>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Telephone (optionnel)</span></label>
                <input type="tel" id="phone" name="phone" maxlength="255" placeholder="06 12 34 56 78" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <span class="text-xs text-gray-500 mt-1">Numéro de téléphone de l'employé</span>
            </div>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" id="is-active" name="is_active" checked class="toggle toggle-success">
                    <span class="label-text">Employe actif</span>
                </label>
            </div>

            <div class="modal-action">
                <button type="button" onclick="closeEmployeeModal()" class="py-2.5 px-5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200">Annuler</button>
                <button type="submit" id="submit-btn" class="py-2.5 px-5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">Creer</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<!-- Modal Confirmation Suppression -->
<dialog id="delete-modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <div class="text-center">
            <div class="w-12 h-12 bg-error/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="font-bold text-lg">Supprimer l'employe ?</h3>
            <p id="delete-message" class="text-base-content/60 text-sm mt-2"></p>
        </div>
        <div class="modal-action justify-center">
            <button type="button" onclick="closeDeleteModal()" class="py-2.5 px-5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200">Annuler</button>
            <button type="button" id="confirm-delete-btn" class="py-2.5 px-5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">Supprimer</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<script>
    let editingEmployeeId = null;
    let deletingEmployeeId = null;

    // Initialize on data load
    document.addEventListener('DOMContentLoaded', function() {
        if (window.appData && window.appData.loaded) {
            loadEmployees();
        } else {
            window.addEventListener('json-data-loaded', loadEmployees);
        }
    });

    function loadEmployees() {
        console.log('[Employees] Loading employees...');

        // Load job roles for dropdown
        const jobRoles = window.jsonStorage.getJobRoles();

        // Filter out jobs with empty names (data corruption)
        const validJobRoles = jobRoles.filter(role => role.name && role.name.trim() !== '');

        const select = document.getElementById('job-role');
        select.innerHTML = '<option value="">Selectionner un metier</option>';
        validJobRoles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.id;
            option.textContent = role.name;
            select.appendChild(option);
        });

        // Load employees with stats
        const employees = window.jsonStorage.getEmployees();

        // Filter out employees with empty names (data corruption)
        const validEmployees = employees.filter(emp =>
            emp.first_name && emp.first_name.trim() !== '' &&
            emp.last_name && emp.last_name.trim() !== ''
        );

        // Calculate attendance stats for each employee
        const employeesWithStats = validEmployees.map(emp => {
            const empAttendance = window.appData.attendance.filter(a => a.employee_id === emp.id);
            const totalPresent = empAttendance.filter(a => a.status === 'present').length;
            const totalAbsent = empAttendance.filter(a => a.status === 'absent').length;
            const total = totalPresent + totalAbsent;
            const attendanceRate = total > 0 ? Math.round((totalPresent / total) * 100) : 0;

            const jobRole = jobRoles.find(r => r.id === emp.job_role_id);

            return {
                ...emp,
                total_present: totalPresent,
                total_absent: totalAbsent,
                attendance_rate: attendanceRate,
                job_role: jobRole
            };
        });

        const employeeList = document.getElementById('employee-list');
        const emptyState = document.getElementById('empty-state');

        if (employeesWithStats.length === 0) {
            employeeList.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        employeeList.innerHTML = employeesWithStats.map(emp => `
            <div class="card bg-base-100 border border-base-300">
                <div class="card-body p-4">
                    <div class="flex justify-between items-start gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold truncate">${escapeHtml(emp.first_name)} ${escapeHtml(emp.last_name)}</h3>
                                <span class="badge ${emp.is_active ? 'badge-success' : 'badge-ghost'} badge-sm">${emp.is_active ? 'Actif' : 'Inactif'}</span>
                            </div>
                            ${emp.job_role ? `<p class="text-sm text-primary mt-1">${escapeHtml(emp.job_role.name)}</p>` : ''}
                            ${emp.phone ? `<p class="text-sm text-base-content/60 mt-1">${escapeHtml(emp.phone)}</p>` : ''}
                        </div>
                        <div class="flex gap-1">
                            <button type="button" onclick="editEmployee(${emp.id})" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button type="button" onclick="confirmDelete(${emp.id}, '${escapeHtml(emp.first_name)} ${escapeHtml(emp.last_name)}')" class="p-2 text-red-500 rounded-lg hover:bg-red-50 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Stats de l'employe -->
                    <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-base-200">
                        <div class="text-center">
                            <p class="text-xs text-base-content/50 uppercase">Presences</p>
                            <p class="text-lg font-bold text-success">${emp.total_present || 0}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-base-content/50 uppercase">Absences</p>
                            <p class="text-lg font-bold text-error">${emp.total_absent || 0}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-base-content/50 uppercase">Taux</p>
                            <p class="text-lg font-bold text-primary">${emp.attendance_rate || 0}%</p>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    document.getElementById('add-employee-btn').addEventListener('click', function() {
        editingEmployeeId = null;
        document.getElementById('employee-form').reset();
        document.getElementById('is-active').checked = true;
        document.getElementById('modal-title').textContent = 'Ajouter un employe';
        document.getElementById('submit-btn').textContent = 'Creer';
        document.getElementById('employee-modal').showModal();
    });

    function editEmployee(employeeId) {
        console.log('[Employees] Editing employee:', employeeId);

        const employees = window.jsonStorage.getEmployees();
        const employee = employees.find(e => e.id === employeeId);

        if (!employee) {
            console.error('[Employees] Employee not found:', employeeId);
            return;
        }

        editingEmployeeId = employeeId;
        document.getElementById('first-name').value = employee.first_name;
        document.getElementById('last-name').value = employee.last_name;
        document.getElementById('job-role').value = employee.job_role_id || '';
        document.getElementById('phone').value = employee.phone || '';
        document.getElementById('is-active').checked = employee.is_active;

        document.getElementById('modal-title').textContent = 'Modifier l\'employe';
        document.getElementById('submit-btn').textContent = 'Enregistrer';
        document.getElementById('employee-modal').showModal();
    }

    function showEmployeeError(message) {
        const errorZone = document.getElementById('employee-error-zone');
        const errorMessage = document.getElementById('employee-error-message');
        errorMessage.textContent = message;
        errorZone.classList.remove('hidden');

        // Scroll to top of modal to show error
        document.querySelector('.modal-box').scrollTop = 0;
    }

    function hideEmployeeError() {
        const errorZone = document.getElementById('employee-error-zone');
        errorZone.classList.add('hidden');
    }

    document.getElementById('employee-form').addEventListener('submit', function(e) {
        e.preventDefault();
        hideEmployeeError();

        // Validation
        const firstName = document.getElementById('first-name').value.trim();
        const lastName = document.getElementById('last-name').value.trim();
        const jobRoleId = document.getElementById('job-role').value;
        const phone = document.getElementById('phone').value.trim();

        if (!firstName) {
            showEmployeeError('Le prénom est obligatoire. Veuillez entrer le prénom de l\'employé.');
            document.getElementById('first-name').focus();
            return;
        }

        if (firstName.length < 2) {
            showEmployeeError('Le prénom doit contenir au moins 2 caractères.');
            document.getElementById('first-name').focus();
            return;
        }

        if (!lastName) {
            showEmployeeError('Le nom est obligatoire. Veuillez entrer le nom de famille de l\'employé.');
            document.getElementById('last-name').focus();
            return;
        }

        if (lastName.length < 2) {
            showEmployeeError('Le nom doit contenir au moins 2 caractères.');
            document.getElementById('last-name').focus();
            return;
        }

        if (!jobRoleId) {
            showEmployeeError('Le métier est obligatoire. Veuillez sélectionner un métier dans la liste. Si aucun métier n\'est disponible, allez d\'abord dans la section Métiers pour en créer un.');
            document.getElementById('job-role').focus();
            return;
        }

        const data = {
            first_name: firstName,
            last_name: lastName,
            job_role_id: parseInt(jobRoleId),
            phone: phone || null,
            is_active: document.getElementById('is-active').checked,
        };

        try {
            // Add ID if editing
            if (editingEmployeeId) {
                data.id = parseInt(editingEmployeeId);
            }

            // Use local storage
            const savedEmployee = window.jsonStorage.saveEmployee(data);
            console.log('[Employees] Saved employee:', savedEmployee);

            closeEmployeeModal();
            loadEmployees();

        } catch (error) {
            console.error('[Employees] Error saving employee:', error);
            showEmployeeError('Erreur lors de l\'enregistrement de l\'employé. Veuillez réessayer.');
        }
    });

    function confirmDelete(employeeId, employeeName) {
        deletingEmployeeId = employeeId;
        document.getElementById('delete-message').textContent = `"${employeeName}" sera definitivement supprime.`;
        document.getElementById('delete-modal').showModal();
    }

    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        try {
            // Use local storage
            const success = window.jsonStorage.deleteEmployee(deletingEmployeeId);
            console.log('[Employees] Deleted employee:', deletingEmployeeId, success);

            closeDeleteModal();
            loadEmployees();

        } catch (error) {
            console.error('[Employees] Error deleting employee:', error);
            alert('Erreur lors de la suppression');
        }
    });

    function closeEmployeeModal() {
        document.getElementById('employee-modal').close();
        document.getElementById('employee-form').reset();
        editingEmployeeId = null;
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').close();
        deletingEmployeeId = null;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
</script>
