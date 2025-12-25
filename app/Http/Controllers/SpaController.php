<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpaController extends Controller
{
    public function getView(Request $request, string $view): Response
    {
        // Return ONLY view content (no layout) for Alpine.js injection
        switch ($view) {
            case 'attendance':
                return $this->getAttendanceView($request);

            case 'employees':
                return $this->getEmployeesView($request);

            case 'job-roles':
                return $this->getJobRolesView($request);

            case 'history':
                return $this->getHistoryView($request);

            case 'statistics':
                return $this->getStatisticsView($request);

            default:
                abort(404);
        }
    }

    private function getAttendanceView(Request $request): Response
    {
        // Local-first: Just return the HTML template
        // Data will be loaded from window.appData by JavaScript
        $date = $request->input('date', today()->toDateString());

        $html = view('partials.attendance', [
            'date' => $date
        ])->render();

        return response($html);
    }

    private function getEmployeesView(Request $request): Response
    {
        // Local-first: Just return the HTML template
        // Data will be loaded from window.appData by JavaScript
        $html = view('partials.employees')->render();

        return response($html);
    }

    private function getJobRolesView(Request $request): Response
    {
        // Local-first: Just return the HTML template
        // Data will be loaded from window.appData by JavaScript
        $html = view('partials.job-roles')->render();

        return response($html);
    }

    private function getHistoryView(Request $request): Response
    {
        // Local-first: Just return the HTML template
        // Data will be loaded from window.appData by JavaScript
        $html = view('partials.history')->render();

        return response($html);
    }

    private function getStatisticsView(Request $request): Response
    {
        // Local-first: Just return the HTML template
        // Data will be loaded from window.appData by JavaScript
        $html = view('partials.statistics')->render();

        return response($html);
    }
}
