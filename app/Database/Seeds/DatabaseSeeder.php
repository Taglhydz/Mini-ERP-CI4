<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder principal — à lancer via :
 *   php spark db:seed DatabaseSeeder
 *
 * Insère dans l'ordre : clients → produits → commandes (avec lignes).
 * Les tables doivent être vides avant le premier lancement.
 * Pour repartir de zéro :
 *   php spark migrate:refresh --seed DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== Mini-ERP — Seeding des données de démonstration ===\n\n";

        $this->call(UsersSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(ProduitSeeder::class);
        $this->call(CommandeSeeder::class);

        echo "\n=== Terminé. ===\n\n";
    }
}
