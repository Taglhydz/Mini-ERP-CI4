<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder principal — exécuté par défaut.
 *
 * Utilisation :
 *   php spark db:seed                  → données minimales (production)
 *   php spark db:seed DemoSeeder       → données de démonstration complètes
 *   php spark migrate:refresh --seed   → repart de zéro + données initiales
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(InitialSeeder::class);
    }
}
