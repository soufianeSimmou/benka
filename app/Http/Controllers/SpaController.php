<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;

class SpaController extends Controller
{
    public function getView(Request $request, string $view): Response
    {
        // Map view names to actual views
        $viewMap = [
            'attendance' => 'attendance',
            'employees' => 'employees',
            'job-roles' => 'job-roles',
            'history' => 'history',
            'statistics' => 'statistics'
        ];

        if (!isset($viewMap[$view])) {
            abort(404);
        }

        $viewName = $viewMap[$view];

        // Pour attendance, on doit passer les données
        if ($view === 'attendance') {
            $controller = app(AttendanceController::class);
            $viewData = $controller->showDashboard();

            // Extraire juste le contenu HTML sans le layout
            $html = $viewData->render();

            // Retourner juste le HTML
            return response($html);
        }

        // Pour les autres vues, juste rendre la vue
        $html = view($viewName)->render();

        return response($html);
    }
}
