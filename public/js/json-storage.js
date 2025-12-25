/**
 * JSON Storage - Load on start, Save on close
 * Manages all application data in memory and syncs with server JSON files
 */

// Global data store - simplified structure for database sync
window.appData = {
    employees: [],
    jobRoles: [],
    attendance: [],
    dailyStatus: [],
    loaded: false,
    saving: false,
    loadedAt: null
};

/**
 * Load all data from database
 */
async function loadJsonData() {
    console.log('[DATA] Loading data from database...');

    try {
        const response = await fetch('/api/data/load', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();

        if (result.success && result.data) {
            // Update global data store with database models
            window.appData.employees = result.data.employees || [];
            window.appData.jobRoles = result.data.jobRoles || [];
            window.appData.attendance = result.data.attendance || [];
            window.appData.dailyStatus = result.data.dailyStatus || [];
            window.appData.loaded = true;
            window.appData.loadedAt = result.loaded_at;

            console.log('[DATA] ✅ Data loaded successfully:', {
                employees: window.appData.employees.length,
                jobRoles: window.appData.jobRoles.length,
                attendance: window.appData.attendance.length,
                dailyStatus: window.appData.dailyStatus.length,
                loadedAt: result.loaded_at
            });

            // Trigger custom event for other scripts
            window.dispatchEvent(new CustomEvent('json-data-loaded', {
                detail: window.appData
            }));

            return true;
        } else {
            throw new Error('Invalid response format');
        }
    } catch (error) {
        console.error('[DATA] ❌ Error loading data:', error);

        // Initialize with empty data if load fails
        window.appData.loaded = false;

        return false;
    }
}

/**
 * Save all data to database
 */
async function saveJsonData(trigger = 'manual') {
    if (window.appData.saving) {
        console.log('[DATA] Save already in progress, skipping...');
        return;
    }

    if (!window.appData.loaded) {
        console.log('[DATA] Data not loaded yet, skipping save');
        return;
    }

    console.log(`[DATA] Saving data to database (trigger: ${trigger})...`);
    window.appData.saving = true;

    try {
        const payload = {
            employees: window.appData.employees,
            jobRoles: window.appData.jobRoles,
            attendance: window.appData.attendance,
            dailyStatus: window.appData.dailyStatus
        };

        const response = await fetch('/api/data/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();

        if (result.success) {
            console.log('[DATA] ✅ Data saved successfully at:', result.saved_at);
            return true;
        } else {
            throw new Error(result.error || 'Save failed');
        }
    } catch (error) {
        console.error('[DATA] ❌ Error saving data:', error);
        return false;
    } finally {
        window.appData.saving = false;
    }
}

/**
 * Save on page unload/close
 */
function setupAutoSave() {
    // Save when user closes tab/browser
    window.addEventListener('beforeunload', (event) => {
        console.log('[DATA] beforeunload triggered, saving data...');

        // Use sendBeacon for reliable save during page unload
        if (window.appData.loaded) {
            const payload = {
                employees: window.appData.employees,
                jobRoles: window.appData.jobRoles,
                attendance: window.appData.attendance,
                dailyStatus: window.appData.dailyStatus
            };

            const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });

            // sendBeacon is more reliable during page unload
            const sent = navigator.sendBeacon('/api/data/save', blob);

            if (sent) {
                console.log('[DATA] ✅ Data queued for save via sendBeacon');
            } else {
                console.warn('[DATA] ⚠️ sendBeacon failed, data may not be saved');
            }
        }

        // Don't show confirmation dialog (user doesn't need to be interrupted)
        // event.preventDefault();
        // event.returnValue = '';
    });

    // Save when page visibility changes (app goes to background)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && window.appData.loaded) {
            console.log('[DATA] Page hidden, saving data...');
            saveJsonData('visibility-hidden');
        }
    });

    // Save periodically (every 5 minutes as backup)
    setInterval(() => {
        if (window.appData.loaded && !window.appData.saving) {
            console.log('[DATA] Periodic auto-save...');
            saveJsonData('periodic');
        }
    }, 5 * 60 * 1000); // 5 minutes

    console.log('[DATA] Auto-save listeners registered');
}

/**
 * Helper: Get all employees (non-deleted)
 */
function getEmployees() {
    return window.appData.employees.filter(e => !e.deleted_at);
}

/**
 * Helper: Get all job roles
 */
function getJobRoles() {
    return window.appData.jobRoles || [];
}

/**
 * Helper: Get attendance for a specific date
 */
function getAttendanceForDate(date) {
    return window.appData.attendance.filter(record => record.date === date);
}

/**
 * Helper: Create or update job role
 */
function saveJobRole(jobRole) {
    const jobRoles = window.appData.jobRoles;
    const index = jobRoles.findIndex(r => r.id === jobRole.id);

    if (index >= 0) {
        // Update existing
        jobRoles[index] = { ...jobRoles[index], ...jobRole, updated_at: new Date().toISOString() };
        console.log('[DATA] Job role updated:', jobRoles[index]);
        return jobRoles[index];
    } else {
        // Add new (auto-increment ID)
        const maxId = Math.max(0, ...jobRoles.map(r => r.id || 0));
        const newRole = {
            id: maxId + 1,
            name: jobRole.name,
            description: jobRole.description || null,
            daily_salary: parseFloat(jobRole.daily_salary) || 0,
            hourly_rate: parseFloat(jobRole.hourly_rate) || 0,
            display_order: jobRole.display_order || jobRoles.length,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        jobRoles.push(newRole);
        console.log('[DATA] Job role created:', newRole);
        return newRole;
    }
}

/**
 * Helper: Delete job role
 */
function deleteJobRole(roleId) {
    const jobRoles = window.appData.jobRoles;
    const index = jobRoles.findIndex(r => r.id === roleId);

    if (index >= 0) {
        // Check if any employees have this job role
        const hasEmployees = window.appData.employees.some(
            e => e.job_role_id === roleId && !e.deleted_at
        );

        if (hasEmployees) {
            throw new Error('Cannot delete job role with associated employees');
        }

        jobRoles.splice(index, 1);
        console.log('[DATA] Job role deleted:', roleId);
        return true;
    }

    return false;
}

/**
 * Helper: Add or update employee
 */
function saveEmployee(employee) {
    const employees = window.appData.employees;
    const index = employees.findIndex(e => e.id === employee.id);

    if (index >= 0) {
        // Update existing
        employees[index] = { ...employees[index], ...employee, updated_at: new Date().toISOString() };
        console.log('[DATA] Employee updated:', employees[index]);
        return employees[index];
    } else {
        // Add new (auto-increment ID)
        const maxId = Math.max(0, ...employees.map(e => e.id || 0));
        const newEmployee = {
            id: maxId + 1,
            first_name: employee.first_name,
            last_name: employee.last_name,
            job_role_id: employee.job_role_id,
            phone: employee.phone || null,
            is_active: employee.is_active !== false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
            deleted_at: null
        };
        employees.push(newEmployee);
        console.log('[DATA] Employee created:', newEmployee);
        return newEmployee;
    }
}

/**
 * Helper: Soft delete employee
 */
function deleteEmployee(employeeId) {
    const employees = window.appData.employees;
    const index = employees.findIndex(e => e.id === employeeId);

    if (index >= 0) {
        employees[index].deleted_at = new Date().toISOString();
        employees[index].is_active = false;
        console.log('[DATA] Employee soft deleted:', employeeId);
        return true;
    }

    return false;
}

/**
 * Helper: Toggle attendance
 */
function toggleAttendance(employeeId, date) {
    const attendance = window.appData.attendance;
    const existingIndex = attendance.findIndex(r => r.employee_id === employeeId && r.date === date);

    if (existingIndex >= 0) {
        // Toggle existing record
        const current = attendance[existingIndex].status;
        attendance[existingIndex].status = current === 'present' ? 'absent' : 'present';
        attendance[existingIndex].updated_at = new Date().toISOString();
        console.log('[DATA] Attendance toggled:', attendance[existingIndex]);
        return attendance[existingIndex];
    } else {
        // Create new record (default: present)
        const maxId = Math.max(0, ...attendance.map(r => r.id || 0));
        const newRecord = {
            id: maxId + 1,
            employee_id: employeeId,
            date: date,
            status: 'present',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        attendance.push(newRecord);
        console.log('[DATA] Attendance created:', newRecord);
        return newRecord;
    }
}

/**
 * Helper: Complete daily attendance
 */
function completeDailyAttendance(date) {
    const dailyStatus = window.appData.dailyStatus;
    const existingIndex = dailyStatus.findIndex(s => s.date === date);

    if (existingIndex >= 0) {
        dailyStatus[existingIndex].is_completed = true;
        dailyStatus[existingIndex].completed_at = new Date().toISOString();
        console.log('[DATA] Daily attendance completed:', dailyStatus[existingIndex]);
        return dailyStatus[existingIndex];
    } else {
        const newStatus = {
            date: date,
            is_completed: true,
            completed_at: new Date().toISOString()
        };
        dailyStatus.push(newStatus);
        console.log('[DATA] Daily attendance status created:', newStatus);
        return newStatus;
    }
}

/**
 * Helper: Reopen daily attendance
 */
function reopenDailyAttendance(date) {
    const dailyStatus = window.appData.dailyStatus;
    const index = dailyStatus.findIndex(s => s.date === date);

    if (index >= 0) {
        dailyStatus.splice(index, 1);
        console.log('[DATA] Daily attendance reopened:', date);
        return true;
    }

    return false;
}

// Initialize on DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', async () => {
        console.log('[DATA] DOMContentLoaded - initializing...');
        await loadJsonData();
        setupAutoSave();
    });
} else {
    // DOM already loaded
    console.log('[DATA] DOM already loaded - initializing...');
    loadJsonData().then(() => setupAutoSave());
}

// Export functions for global use
window.jsonStorage = {
    // Core operations
    load: loadJsonData,
    save: saveJsonData,

    // Getters
    getEmployees,
    getJobRoles,
    getAttendanceForDate,

    // Job Role CRUD
    saveJobRole,
    deleteJobRole,

    // Employee CRUD
    saveEmployee,
    deleteEmployee,

    // Attendance operations
    toggleAttendance,
    completeDailyAttendance,
    reopenDailyAttendance
};

console.log('[DATA] json-storage.js loaded - local-first architecture ready');
