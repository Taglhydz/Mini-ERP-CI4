<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * InitialSeeder — Données minimales pour la mise en production.
 *
 * Crée :
 *   - 1 entreprise par défaut
 *   - 1 compte administrateur global
 *   - 1 compte manager lié à l'entreprise
 *
 * !! IMPORTANT : Changez les mots de passe après le premier déploiement. !!
 *
 * Utilisation : php spark db:seed InitialSeeder
 */
class InitialSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // ── Entreprise par défaut ─────────────────────────────────────────────
        $this->db->table('companies')->insert([
            'name'        => 'Mon Entreprise',
            'slug'        => 'mon-entreprise',
            'email'       => 'contact@mon-entreprise.fr',
            'phone'       => '0100000000',
            'city'        => 'Paris',
            'postal_code' => '75001',
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $companyId = $this->db->insertID();

        // ── Administrateur global (sans company_id) ───────────────────────────
        $this->db->table('users')->insert([
            'username'      => 'Admin',
            'email'         => 'admin@demo.fr',
            'password_hash' => password_hash('Admin123!', PASSWORD_DEFAULT),
            'role'          => 'admin',
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        // ── Manager lié à l'entreprise ────────────────────────────────────────
        $this->db->table('users')->insert([
            'username'      => 'Manager',
            'email'         => 'manager@demo.fr',
            'password_hash' => password_hash('Manager123!', PASSWORD_DEFAULT),
            'role'          => 'manager',
            'company_id'    => $companyId,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        echo "\n";
        echo "  ✓ Entreprise : Mon Entreprise (id={$companyId})\n";
        echo "  ✓ Admin      : admin@demo.fr   / Admin123!   ← À CHANGER EN PRODUCTION\n";
        echo "  ✓ Manager    : manager@demo.fr / Manager123! ← À CHANGER EN PRODUCTION\n";
    }
}
