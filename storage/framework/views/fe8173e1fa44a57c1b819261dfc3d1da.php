<?php $__env->startSection('title', 'Gestion des Metiers'); ?>
<?php $__env->startSection('page-name', 'job-roles'); ?>

<?php $__env->startSection('content'); ?>
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
                <h1 class="text-xl font-bold">Metiers & Salaires</h1>
                <button id="add-job-btn" type="button" class="btn btn-primary btn-sm gap-1">
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
        <div id="job-list" class="space-y-3"></div>
        <div id="empty-state" class="text-center py-16 hidden">
            <div class="w-16 h-16 bg-base-300 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="font-medium text-base-content/60">Aucun metier</p>
            <p class="text-sm text-base-content/40 mt-1">Ajoutez votre premier metier</p>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier -->
<dialog id="job-modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 id="modal-title" class="font-bold text-lg mb-4">Ajouter un metier</h3>
        <form id="job-form" class="space-y-4">
            <input type="hidden" id="job-id">

            <div class="form-control">
                <label class="label"><span class="label-text">Nom du metier</span></label>
                <input type="text" id="job-name" name="name" required maxlength="255" placeholder="ex: Macon, Electricien..." class="input input-bordered w-full">
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Description (optionnel)</span></label>
                <input type="text" id="job-description" name="description" maxlength="255" placeholder="Description du poste..." class="input input-bordered w-full">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="form-control">
                    <label class="label"><span class="label-text">Salaire/jour</span></label>
                    <label class="input input-bordered flex items-center gap-2">
                        <input type="number" id="daily-salary" name="daily_salary" step="0.01" min="0" placeholder="0.00" class="grow w-full">
                        <span class="text-base-content/50">EUR</span>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Salaire/heure</span></label>
                    <label class="input input-bordered flex items-center gap-2">
                        <input type="number" id="hourly-rate" name="hourly_rate" step="0.01" min="0" placeholder="0.00" class="grow w-full">
                        <span class="text-base-content/50">EUR</span>
                    </label>
                </div>
            </div>

            <div class="modal-action">
                <button type="button" onclick="closeJobModal()" class="btn btn-ghost">Annuler</button>
                <button type="submit" id="submit-btn" class="btn btn-primary">Creer</button>
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
            <h3 class="font-bold text-lg">Supprimer le metier ?</h3>
            <p id="delete-message" class="text-base-content/60 text-sm mt-2"></p>
        </div>
        <div class="modal-action justify-center">
            <button type="button" onclick="closeDeleteModal()" class="btn btn-ghost">Annuler</button>
            <button type="button" id="confirm-delete-btn" class="btn btn-error">Supprimer</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let editingJobId = null;
    let deletingJobId = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadJobRoles();
    });

    async function loadJobRoles() {
        try {
            const response = await fetch('/api/job-roles', { headers: { 'Accept': 'application/json' } });
            const jobs = await response.json();

            const jobList = document.getElementById('job-list');
            const emptyState = document.getElementById('empty-state');

            if (jobs.length === 0) {
                jobList.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            jobList.innerHTML = jobs.map(job => `
                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body p-4">
                        <div class="flex justify-between items-start gap-3 cursor-pointer" onclick="toggleJobCard(${job.id})">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold">${escapeHtml(job.name)}</h3>
                                    ${job.employees_count > 0 ? `
                                        <span class="badge badge-ghost badge-sm">${job.employees_count} employ\u00e9${job.employees_count > 1 ? 's' : ''}</span>
                                    ` : ''}
                                </div>
                                ${job.description ? `<p class="text-sm text-base-content/60 mt-1">${escapeHtml(job.description)}</p>` : ''}
                            </div>
                            <div class="flex gap-1">
                                <svg id="toggle-icon-${job.id}" class="w-5 h-5 text-base-content/40 transition-transform" style="transform: rotate(-90deg);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        <div id="job-details-${job.id}" class="transition-all overflow-hidden" style="max-height: 0; margin-top: 0;">
                            <!-- Liste des employés -->
                            ${job.employees && job.employees.length > 0 ? `
                                <div class="mt-3">
                                    <h4 class="text-xs font-semibold text-base-content/70 uppercase mb-2">Employés</h4>
                                    <div class="space-y-1">
                                        ${job.employees.map(emp => `
                                            <div class="flex items-center gap-2 p-2 bg-base-200 rounded-lg">
                                                <svg class="w-4 h-4 text-base-content/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span class="text-sm">${escapeHtml(emp.first_name)} ${escapeHtml(emp.last_name)}</span>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            ` : ''}

                            <div class="flex gap-2 mt-3">
                                ${job.daily_salary ? `
                                    <div class="flex-1 bg-secondary/10 rounded-lg p-3 text-center">
                                        <p class="text-xs text-secondary uppercase font-medium">Jour</p>
                                        <p class="text-sm font-bold text-secondary">${formatCurrency(job.daily_salary)}</p>
                                    </div>
                                ` : ''}
                                ${job.hourly_rate ? `
                                    <div class="flex-1 bg-primary/10 rounded-lg p-3 text-center">
                                        <p class="text-xs text-primary uppercase font-medium">Heure</p>
                                        <p class="text-sm font-bold text-primary">${formatCurrency(job.hourly_rate)}</p>
                                    </div>
                                ` : ''}
                         
                            </div>

                            <div class="flex gap-1 mt-3 pt-3 border-t border-base-300">
                                <button type="button" onclick="event.stopPropagation(); editJob(${job.id})" class="btn btn-ghost btn-sm flex-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Modifier
                                </button>
                                <button type="button" onclick="event.stopPropagation(); confirmDelete(${job.id}, '${escapeHtml(job.name)}')" class="btn btn-ghost btn-sm btn-error flex-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error:', error);
        }
    }

    document.getElementById('add-job-btn').addEventListener('click', function() {
        editingJobId = null;
        document.getElementById('job-form').reset();
        document.getElementById('modal-title').textContent = 'Ajouter un metier';
        document.getElementById('submit-btn').textContent = 'Creer';
        document.getElementById('job-modal').showModal();
    });

    async function editJob(jobId) {
        try {
            const response = await fetch('/api/job-roles', { headers: { 'Accept': 'application/json' } });
            const jobs = await response.json();
            const job = jobs.find(j => j.id === jobId);

            if (!job) return;

            editingJobId = jobId;
            document.getElementById('job-id').value = jobId;
            document.getElementById('job-name').value = job.name;
            document.getElementById('job-description').value = job.description || '';
            document.getElementById('daily-salary').value = job.daily_salary || '';
            document.getElementById('hourly-rate').value = job.hourly_rate || '';

            document.getElementById('modal-title').textContent = 'Modifier le metier';
            document.getElementById('submit-btn').textContent = 'Enregistrer';
            document.getElementById('job-modal').showModal();
        } catch (error) {
            console.error('Error:', error);
        }
    }

    document.getElementById('job-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const jobId = document.getElementById('job-id').value;
        const formData = new FormData(this);

        const data = {
            name: formData.get('name'),
            description: formData.get('description') || null,
            daily_salary: formData.get('daily_salary') ? parseFloat(formData.get('daily_salary')) : null,
            hourly_rate: formData.get('hourly_rate') ? parseFloat(formData.get('hourly_rate')) : null,
        };

        try {
            const url = jobId ? `/api/job-roles/${jobId}` : '/api/job-roles';
            const method = jobId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                const error = await response.json();
                alert(error.message || 'Erreur');
                return;
            }

            closeJobModal();
            await loadJobRoles();
        } catch (error) {
            console.error('Error:', error);
        }
    });

    function confirmDelete(jobId, jobName) {
        deletingJobId = jobId;
        document.getElementById('delete-message').textContent = `"${jobName}" sera definitivement supprime.`;
        document.getElementById('delete-modal').showModal();
    }

    document.getElementById('confirm-delete-btn').addEventListener('click', async function() {
        try {
            const response = await fetch(`/api/job-roles/${deletingJobId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });

            if (!response.ok) {
                const error = await response.json();
                alert(error.error || 'Erreur');
                return;
            }

            closeDeleteModal();
            await loadJobRoles();
        } catch (error) {
            console.error('Error:', error);
        }
    });

    function closeJobModal() {
        document.getElementById('job-modal').close();
        document.getElementById('job-form').reset();
        editingJobId = null;
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').close();
        deletingJobId = null;
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(value);
    }

    function toggleJobCard(jobId) {
        const detailsDiv = document.getElementById(`job-details-${jobId}`);
        const icon = document.getElementById(`toggle-icon-${jobId}`);

        if (detailsDiv.style.maxHeight && detailsDiv.style.maxHeight !== '0px') {
            // Replier
            detailsDiv.style.maxHeight = '0px';
            detailsDiv.style.marginTop = '0px';
            icon.style.transform = 'rotate(-90deg)';
        } else {
            // Déplier
            detailsDiv.style.maxHeight = detailsDiv.scrollHeight + 'px';
            detailsDiv.style.marginTop = '';
            icon.style.transform = 'rotate(0deg)';
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Shadow\benka\resources\views/job-roles.blade.php ENDPATH**/ ?>