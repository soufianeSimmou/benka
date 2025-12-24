import './bootstrap';
import 'flowbite';
import './modules/dashboard.jsx';

// Import Alpine.js for SPA
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

console.log('[APP] Alpine.js initialized');

// Turbo disabled - was breaking POST forms (statistics, employees, etc)
// Just using Service Worker v9 for asset caching is enough
// import * as Turbo from '@hotwired/turbo';

// AJAX form submission for attendance toggle (no page reload)
document.addEventListener('DOMContentLoaded', function() {
    const page = document.body.dataset.page;

    if (page === 'attendance') {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Handle employee toggle forms with AJAX
        document.querySelectorAll('form[action*="/attendance/toggle"]').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);
                const employeeId = formData.get('employee_id');
                const date = formData.get('date');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Update button styling without page reload
                        const button = form.querySelector('button');
                        if (data.status === 'present') {
                            button.classList.remove('bg-gray-100', 'border-gray-300', 'text-gray-700', 'hover:bg-gray-50', 'active:bg-gray-200');
                            button.classList.add('bg-green-100', 'border-green-500', 'text-green-900', 'active:bg-green-200');

                            // Add checkmark if not present
                            if (!button.querySelector('span:last-child')?.textContent.includes('Présent')) {
                                button.innerHTML = `<span>${button.querySelector('span:first-child').textContent}</span><span class="float-right text-green-600">✓ Présent</span>`;
                            }
                        } else {
                            button.classList.remove('bg-green-100', 'border-green-500', 'text-green-900', 'active:bg-green-200');
                            button.classList.add('bg-gray-100', 'border-gray-300', 'text-gray-700', 'hover:bg-gray-50', 'active:bg-gray-200');

                            // Remove checkmark
                            button.innerHTML = `<span>${button.querySelector('span:first-child').textContent}</span>`;
                        }

                        // Update counters via fetch
                        updateCounters(date);
                    }
                } catch (error) {
                    console.error('Error toggling attendance:', error);
                    alert('Erreur lors de la mise à jour de la présence');
                }
            });
        });
    }
});

// Helper function to update counter display
async function updateCounters(date) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    try {
        const formData = new FormData();
        formData.append('current_date', date);
        formData.append('direction', 'current');

        // This would require a new endpoint, but for now we can use client-side calculation
        const presentCount = document.querySelectorAll('button.bg-green-100').length;
        const totalCount = document.querySelectorAll('form[action*="/attendance/toggle"] button').length;
        const absentCount = totalCount - presentCount;

        // Update the counter display (if structure matches)
        const counters = document.querySelectorAll('[class*="text-2xl font-bold"]');
        if (counters.length >= 3) {
            counters[1].textContent = presentCount;
            counters[2].textContent = absentCount;
        }
    } catch (error) {
        console.error('Error updating counters:', error);
    }
}

console.log('App initialized - Session-based authentication active');
