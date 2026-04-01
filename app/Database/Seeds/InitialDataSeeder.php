<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder de données initiales.
 * Crée une entreprise par défaut et un compte admin pour démarrer.
 *
 * Utilisation : php spark db:seed InitialDataSeeder
 */
class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Entreprise par défaut ─────────────────────────────────────────────
        $this->db->table('companies')->insert([
            'id'         => 1,
            'name'       => 'Mon Entreprise',
            'slug'       => 'mon-entreprise',
            'email'      => 'contact@mon-entreprise.fr',
            'phone'      => '0100000000',
            'city'       => 'Paris',
            'postal_code' => '75001',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // ── Compte administrateur ─────────────────────────────────────────────
        $this->db->table('users')->insert([
            'username'      => 'Admin',
            'email'         => 'admin@demo.fr',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'role'          => 'admin',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        // ── Compte manager lié à l'entreprise ────────────────────────────────
        $this->db->table('users')->insert([
            'username'      => 'Manager',
            'email'         => 'manager@demo.fr',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'role'          => 'manager',
            'company_id'    => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        echo "Données initiales créées :\n";
        echo "  - Entreprise : Mon Entreprise (id=1)\n";
        echo "  - Admin      : admin@demo.fr   / password123\n";
        echo "  - Manager    : manager@demo.fr / password123\n";
    }
}
