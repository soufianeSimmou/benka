/**
 * JSON Storage - Load on start, Save on close
 * Manages all application data in memory and syncs with server JSON files
 */

// Global data store
window.appData = {
    employees: { data: [], version: 1, last_updated: null },
    jobRoles: { data: [], version: 1, last_updated: null },
    attendance: { data: [], version: 1, last_updated: null },
    dailyStatus: { data: [], version: 1, last_updated: null },
    metadata: { version: 1, last_updated: null },
    loaded: false,
    saving: false
};

/**
 * Load all data from server JSON files
 */
async function loadJsonData() {
    console.log('[JSON] Loading data from server...');

    try {
        const response = await fetch('/api/json/load', {
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
            // Update global data store
            window.appData.employees = result.data.employees || { data: [], version: 1 };
            window.appData.jobRoles = result.data.jobRoles || { data: [], version: 1 };
            window.appData.attendance = result.data.attendance || { data: [], version: 1 };
            window.appData.dailyStatus = result.data.dailyStatus || { data: [], version: 1 };
            window.appData.metadata = result.data.metadata || { version: 1 };
            window.appData.loaded = true;

            console.log('[JSON] ✅ Data loaded successfully:', {
                employees: window.appData.employees.data.length,
                jobRoles: window.appData.jobRoles.data.length,
                attendance: window.appData.attendance.data.length,
                dailyStatus: window.appData.dailyStatus.data.length
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
        console.error('[JSON] ❌ Error loading data:', error);

        // Initialize with empty data if load fails
        window.appData.loaded = false;

        return false;
    }
}

/**
 * Save all data to server JSON files
 */
async function saveJsonData(trigger = 'manual') {
    if (window.appData.saving) {
        console.log('[JSON] Save already in progress, skipping...');
        return;
    }

    if (!window.appData.loaded) {
        console.log('[JSON] Data not loaded yet, skipping save');
        return;
    }

    console.log(`[JSON] Saving data (trigger: ${trigger})...`);
    window.appData.saving = true;

    try {
        const payload = {
            employees: window.appData.employees,
            jobRoles: window.appData.jobRoles,
            attendance: window.appData.attendance,
            dailyStatus: window.appData.dailyStatus,
            metadata: {
                ...window.appData.metadata,
                version: (window.appData.metadata.version || 0) + 1
            },
            trigger: trigger
        };

        const response = await fetch('/api/json/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();

        if (result.success) {
            console.log('[JSON] ✅ Data saved successfully at:', result.saved_at);
            return true;
        } else {
            throw new Error(result.error || 'Save failed');
        }
    } catch (error) {
        console.error('[JSON] ❌ Error saving data:', error);
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
        console.log('[JSON] beforeunload triggered, saving data...');

        // Use sendBeacon for reliable save during page unload
        if (window.appData.loaded) {
            const payload = {
                employees: window.appData.employees,
                jobRoles: window.appData.jobRoles,
                attendance: window.appData.attendance,
                dailyStatus: window.appData.dailyStatus,
                metadata: window.appData.metadata,
                trigger: 'beforeunload'
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });

            // sendBeacon is more reliable during page unload
            const sent = navigator.sendBeacon('/api/json/save', blob);

            if (sent) {
                console.log('[JSON] ✅ Data queued for save via sendBeacon');
            } else {
                console.warn('[JSON] ⚠️ sendBeacon failed, data may not be saved');
            }
        }

        // Don't show confirmation dialog (user doesn't need to be interrupted)
        // event.preventDefault();
        // event.returnValue = '';
    });

    // Save when page visibility changes (app goes to background)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && window.appData.loaded) {
            console.log('[JSON] Page hidden, saving data...');
            saveJsonData('visibility-hidden');
        }
    });

    // Save periodically (every 5 minutes as backup)
    setInterval(() => {
        if (window.appData.loaded && !window.appData.saving) {
            console.log('[JSON] Periodic auto-save...');
            saveJsonData('periodic');
        }
    }, 5 * 60 * 1000); // 5 minutes

    console.log('[JSON] Auto-save listeners registered');
}

/**
 * Helper: Get all employees
 */
function getEmployees() {
    return window.appData.employees.data || [];
}

/**
 * Helper: Get all job roles
 */
function getJobRoles() {
    return window.appData.jobRoles.data || [];
}

/**
 * Helper: Get attendance for a specific date
 */
function getAttendanceForDate(date) {
    return (window.appData.attendance.data || []).filter(record => record.date === date);
}

/**
 * Helper: Add or update employee
 */
function saveEmployee(employee) {
    const employees = window.appData.employees.data;
    const index = employees.findIndex(e => e.id === employee.id);

    if (index >= 0) {
        // Update existing
        employees[index] = { ...employees[index], ...employee, updated_at: new Date().toISOString() };
    } else {
        // Add new (auto-increment ID)
        const maxId = Math.max(0, ...employees.map(e => e.id || 0));
        employee.id = maxId + 1;
        employee.created_at = new Date().toISOString();
        employee.updated_at = new Date().toISOString();
        employees.push(employee);
    }

    window.appData.employees.last_updated = new Date().toISOString();
    return employee;
}

/**
 * Helper: Toggle attendance
 */
function toggleAttendance(employeeId, date) {
    const attendance = window.appData.attendance.data;
    const existingIndex = attendance.findIndex(r => r.employee_id === employeeId && r.date === date);

    if (existingIndex >= 0) {
        // Toggle existing record
        const current = attendance[existingIndex].status;
        attendance[existingIndex].status = current === 'present' ? 'absent' : 'present';
        attendance[existingIndex].updated_at = new Date().toISOString();
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
        return newRecord;
    }
}

// Initialize on DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', async () => {
        console.log('[JSON] DOMContentLoaded - initializing...');
        await loadJsonData();
        setupAutoSave();
    });
} else {
    // DOM already loaded
    console.log('[JSON] DOM already loaded - initializing...');
    loadJsonData().then(() => setupAutoSave());
}

// Export functions for global use
window.jsonStorage = {
    load: loadJsonData,
    save: saveJsonData,
    getEmployees,
    getJobRoles,
    getAttendanceForDate,
    saveEmployee,
    toggleAttendance
};

console.log('[JSON] json-storage.js loaded');
