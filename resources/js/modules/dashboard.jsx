import React from 'react';
import { createRoot } from 'react-dom/client';
import Dashboard from '../components/Dashboard.jsx';

export function initDashboard() {
    const dashboardContainer = document.getElementById('dashboard-root');

    if (dashboardContainer) {
        const root = createRoot(dashboardContainer);
        root.render(<Dashboard />);
    }
}

// Auto-initialize if on statistics page
document.addEventListener('DOMContentLoaded', function() {
    const page = document.body.dataset.page;

    if (page === 'statistics') {
        initDashboard();
    }
});
