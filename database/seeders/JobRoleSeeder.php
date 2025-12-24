<?php

namespace Database\Seeders;

use App\Models\JobRole;
use Illuminate\Database\Seeder;

class JobRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Maçon', 'description' => 'Maçon généraliste', 'display_order' => 1],
            ['name' => 'Électricien', 'description' => 'Électricien', 'display_order' => 2],
            ['name' => 'Plombier', 'description' => 'Plombier', 'display_order' => 3],
            ['name' => 'Charpentier', 'description' => 'Charpentier', 'display_order' => 4],
            ['name' => 'Peintre', 'description' => 'Peintre', 'display_order' => 5],
            ['name' => 'Carreleur', 'description' => 'Carreleur', 'display_order' => 6],
            ['name' => 'Menuisier', 'description' => 'Menuisier', 'display_order' => 7],
            ['name' => 'Chef de chantier', 'description' => 'Chef de chantier', 'display_order' => 8],
        ];

        foreach ($roles as $role) {
            JobRole::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
