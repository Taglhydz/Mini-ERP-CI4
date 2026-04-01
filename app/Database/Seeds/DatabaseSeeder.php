<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder principal — à lancer via :
 *   php spark db:seed DatabaseSeeder
 *   ou : php spark migrate:refresh --seed
 *
 * Insère les données initiales (entreprise + admin + manager).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(InitialDataSeeder::class);
    }
}
