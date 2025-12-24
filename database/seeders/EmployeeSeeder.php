<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\JobRole;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            // Maçons
            ['first_name' => 'Jean', 'last_name' => 'Dupont', 'job_role' => 'Maçon', 'phone' => '06 12 34 56 78'],
            ['first_name' => 'Marie', 'last_name' => 'Martin', 'job_role' => 'Maçon', 'phone' => '06 23 45 67 89'],
            ['first_name' => 'Pierre', 'last_name' => 'Bernard', 'job_role' => 'Maçon', 'phone' => '06 34 56 78 90'],

            // Électriciens
            ['first_name' => 'Paul', 'last_name' => 'Rousseau', 'job_role' => 'Électricien', 'phone' => '06 45 67 89 01'],
            ['first_name' => 'Sophie', 'last_name' => 'Leclerc', 'job_role' => 'Électricien', 'phone' => '06 56 78 90 12'],

            // Plombiers
            ['first_name' => 'Luc', 'last_name' => 'Moreau', 'job_role' => 'Plombier', 'phone' => '06 67 89 01 23'],
            ['first_name' => 'Anne', 'last_name' => 'Lambert', 'job_role' => 'Plombier', 'phone' => '06 78 90 12 34'],

            // Charpentiers
            ['first_name' => 'Michel', 'last_name' => 'Lefebvre', 'job_role' => 'Charpentier', 'phone' => '06 89 01 23 45'],
            ['first_name' => 'Claire', 'last_name' => 'Legrand', 'job_role' => 'Charpentier', 'phone' => '06 90 12 34 56'],

            // Peintres
            ['first_name' => 'Denis', 'last_name' => 'Petit', 'job_role' => 'Peintre', 'phone' => '06 01 23 45 67'],

            // Chef de chantier
            ['first_name' => 'Robert', 'last_name' => 'Durand', 'job_role' => 'Chef de chantier', 'phone' => '06 12 34 56 78'],
        ];

        foreach ($employees as $emp) {
            $jobRole = JobRole::where('name', $emp['job_role'])->first();

            if ($jobRole) {
                Employee::create([
                    'first_name' => $emp['first_name'],
                    'last_name' => $emp['last_name'],
                    'job_role_id' => $jobRole->id,
                    'phone' => $emp['phone'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
