/**
 * JSON Storage - Offline-First Architecture
 *
 * Priority: localStorage > database
 * - Data is stored in localStorage (works offline)
 * - Database sync is secondary/backup
 * - App works 100% without internet connection
 */

const STORAGE_KEY = 'benka_app_data';

// Global data store
window.appData = {
    employees: [],
    jobRoles: [],
    attendance: [],
    dailyStatus: [],
    loaded: false,
    saving: false,
    lastSaved: null
};

/**
 * Save to localStorage (immediate, synchronous)
 */
function saveToLocalStorage() {
    try {
        const data = {
            employees: window.appData.employees,
            jobRoles: window.appData.jobRoles,
            attendance: window.appData.attendance,
            dailyStatus: window.appData.dailyStatus,
            lastSaved: new Date().toISOString()
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        window.appData.lastSaved = data.lastSaved;
        console.log('[DATA] ✅ Saved to localStorage');
        return true;
    } catch (error) {
        console.error('[DATA] ❌ Error saving to localStorage:', error);
        return false;
    }
}

/**
 * Load from localStorage (immediate, synchronous)
 */
function loadFromLocalStorage() {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            const data = JSON.parse(stored);
            window.appData.employees = data.employees || [];
            window.appData.jobRoles = data.jobRoles || [];
            window.appData.attendance = data.attendance || [];
            window.appData.dailyStatus = data.dailyStatus || [];
            window.appData.lastSaved = data.lastSaved;
            console.log('[DATA] ✅ Loaded from localStorage:', {
                employees: window.appData.employees.length,
                jobRoles: window.appData.jobRoles.length,
                attendance: window.appData.attendance.length,
                dailyStatus: window.appData.dailyStatus.length,
                lastSaved: data.lastSaved
            });
            return true;
        }
        console.log('[DATA] No data in localStorage, starting fresh');
        return false;
    } catch (error) {
        console.error('[DATA] ❌ Error loading from localStorage:', error);
        return false;
    }
}

/**
 * Sync to database (background, async) - backup only
 */
async function syncToDatabase(trigger = 'manual') {
    if (window.appData.saving) {
        console.log('[DATA] Sync already in progress, skipping...');
        return false;
    }

    window.appData.saving = true;
    console.log(`[DATA] Syncing to database (trigger: ${trigger})...`);

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.warn('[DATA] No CSRF token found, skipping database sync');
            return false;
        }

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
                'X-CSRF-TOKEN': csrfToken.content
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            const result = await response.json();
            console.log('[DATA] ✅ Synced to database at:', result.saved_at);
            return true;
        } else {
            console.warn('[DATA] ⚠️ Database sync failed:', response.status);
            return false;
        }
    } catch (error) {
        console.warn('[DATA] ⚠️ Database sync error (offline?):', error.message);
        return false;
    } finally {
        window.appData.saving = false;
    }
}

/**
 * Load from database (fallback if localStorage is empty)
 */
async function loadFromDatabase() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.warn('[DATA] No CSRF token, cannot load from database');
            return false;
        }

        const response = await fetch('/api/data/load', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        if (result.success && result.data) {
            window.appData.employees = result.data.employees || [];
            window.appData.jobRoles = result.data.jobRoles || [];
            window.appData.attendance = result.data.attendance || [];
            window.appData.dailyStatus = result.data.dailyStatus || [];

            // Save to localStorage for future offline use
            saveToLocalStorage();

            console.log('[DATA] ✅ Loaded from database and cached locally');
            return true;
        }
        return false;
    } catch (error) {
        console.warn('[DATA] ⚠️ Could not load from database (offline?):', error.message);
        return false;
    }
}

/**
 * Initialize data - localStorage first, database as fallback
 */
async function initializeData() {
    console.log('[DATA] Initializing (offline-first)...');

    // Step 1: Try localStorage first (works offline)
    const hasLocalData = loadFromLocalStorage();

    if (hasLocalData) {
        window.appData.loaded = true;

        // Trigger event for other scripts
        window.dispatchEvent(new CustomEvent('json-data-loaded', {
            detail: window.appData
        }));

        // Background sync to database (non-blocking)
        syncToDatabase('init-background');

        return true;
    }

    // Step 2: No local data, try database
    console.log('[DATA] No local data, trying database...');
    const hasDbData = await loadFromDatabase();

    if (hasDbData) {
        window.appData.loaded = true;

        window.dispatchEvent(new CustomEvent('json-data-loaded', {
            detail: window.appData
        }));

        return true;
    }

    // Step 3: No data anywhere, start fresh
    console.log('[DATA] No data found, starting with empty data');
    window.appData.loaded = true;
    saveToLocalStorage();

    window.dispatchEvent(new CustomEvent('json-data-loaded', {
        detail: window.appData
    }));

    return true;
}

/**
 * Save data - localStorage immediately, database in background
 */
function saveData(trigger = 'manual') {
    // Always save to localStorage first (synchronous, reliable)
    saveToLocalStorage();

    // Then sync to database in background (async, may fail offline)
    syncToDatabase(trigger);
}

/**
 * Setup auto-save listeners
 */
function setupAutoSave() {
    // Save when page is hidden (iOS PWA friendly)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && window.appData.loaded) {
            console.log('[DATA] Page hidden, saving...');
            saveData('visibility-hidden');
        }
    });

    // Save before unload
    window.addEventListener('beforeunload', () => {
        if (window.appData.loaded) {
            saveToLocalStorage(); // Synchronous save only
        }
    });

    // Periodic database sync (every 5 minutes)
    setInterval(() => {
        if (window.appData.loaded && !window.appData.saving) {
            syncToDatabase('periodic');
        }
    }, 5 * 60 * 1000);

    console.log('[DATA] Auto-save listeners registered');
}

// ============================================
// Helper Functions (CRUD operations)
// ============================================

function getEmployees() {
    return window.appData.employees.filter(e => !e.deleted_at);
}

function getJobRoles() {
    return window.appData.jobRoles || [];
}

function getAttendanceForDate(date) {
    return window.appData.attendance.filter(record => record.date === date);
}

function saveJobRole(jobRole) {
    const jobRoles = window.appData.jobRoles;
    const index = jobRoles.findIndex(r => r.id === jobRole.id);
    let result;

    if (index >= 0) {
        jobRoles[index] = { ...jobRoles[index], ...jobRole, updated_at: new Date().toISOString() };
        result = jobRoles[index];
    } else {
        const maxId = Math.max(0, ...jobRoles.map(r => r.id || 0));
        result = {
            id: maxId + 1,
            name: jobRole.name,
            description: jobRole.description || null,
            daily_salary: parseFloat(jobRole.daily_salary) || 0,
            hourly_rate: parseFloat(jobRole.hourly_rate) || 0,
            display_order: jobRole.display_order || jobRoles.length,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        jobRoles.push(result);
    }

    saveData('job-role-change');
    return result;
}

function deleteJobRole(roleId) {
    const jobRoles = window.appData.jobRoles;
    const index = jobRoles.findIndex(r => r.id === roleId);

    if (index >= 0) {
        const hasEmployees = window.appData.employees.some(
            e => e.job_role_id === roleId && !e.deleted_at
        );

        if (hasEmployees) {
            throw new Error('Cannot delete job role with associated employees');
        }

        jobRoles.splice(index, 1);
        saveData('job-role-delete');
        return true;
    }
    return false;
}

function saveEmployee(employee) {
    const employees = window.appData.employees;
    const index = employees.findIndex(e => e.id === employee.id);
    let result;

    if (index >= 0) {
        employees[index] = { ...employees[index], ...employee, updated_at: new Date().toISOString() };
        result = employees[index];
    } else {
        const maxId = Math.max(0, ...employees.map(e => e.id || 0));
        result = {
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
        employees.push(result);
    }

    saveData('employee-change');
    return result;
}

function deleteEmployee(employeeId) {
    const employees = window.appData.employees;
    const index = employees.findIndex(e => e.id === employeeId);

    if (index >= 0) {
        employees[index].deleted_at = new Date().toISOString();
        employees[index].is_active = false;
        saveData('employee-delete');
        return true;
    }
    return false;
}

function toggleAttendance(employeeId, date) {
    const attendance = window.appData.attendance;
    const existingIndex = attendance.findIndex(r => r.employee_id === employeeId && r.date === date);
    let result;

    if (existingIndex >= 0) {
        const current = attendance[existingIndex].status;
        attendance[existingIndex].status = current === 'present' ? 'absent' : 'present';
        attendance[existingIndex].updated_at = new Date().toISOString();
        result = attendance[existingIndex];
    } else {
        const maxId = Math.max(0, ...attendance.map(r => r.id || 0));
        result = {
            id: maxId + 1,
            employee_id: employeeId,
            date: date,
            status: 'present',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        attendance.push(result);
    }

    saveData('attendance-change');
    return result;
}

function completeDailyAttendance(date) {
    const dailyStatus = window.appData.dailyStatus;
    const existingIndex = dailyStatus.findIndex(s => s.date === date);
    let result;

    if (existingIndex >= 0) {
        dailyStatus[existingIndex].is_completed = true;
        dailyStatus[existingIndex].completed_at = new Date().toISOString();
        result = dailyStatus[existingIndex];
    } else {
        result = {
            date: date,
            is_completed: true,
            completed_at: new Date().toISOString()
        };
        dailyStatus.push(result);
    }

    saveData('daily-status-complete');
    return result;
}

function reopenDailyAttendance(date) {
    const dailyStatus = window.appData.dailyStatus;
    const index = dailyStatus.findIndex(s => s.date === date);

    if (index >= 0) {
        dailyStatus.splice(index, 1);
        saveData('daily-status-reopen');
        return true;
    }
    return false;
}

// ============================================
// Initialization
// ============================================

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initializeData().then(() => setupAutoSave());
    });
} else {
    initializeData().then(() => setupAutoSave());
}

// Export for global use
window.jsonStorage = {
    // Core
    load: initializeData,
    save: saveData,
    syncToDatabase,

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

    // Attendance
    toggleAttendance,
    completeDailyAttendance,
    reopenDailyAttendance
};

console.log('[DATA] json-storage.js loaded - OFFLINE-FIRST architecture');
