<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $users = [
            [
                'username'     => 'Administrateur',
                'email'        => 'admin@mini-erp.test',
                'password_hash'=> password_hash('Admin123!', PASSWORD_DEFAULT),
                'role'         => 'admin',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'username'     => 'Utilisateur',
                'email'        => 'user@mini-erp.test',
                'password_hash'=> password_hash('User123!', PASSWORD_DEFAULT),
                'role'         => 'user',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'username'     => 'Client',
                'email'        => 'client@mini-erp.test',
                'password_hash'=> password_hash('Client123!', PASSWORD_DEFAULT),
                'role'         => 'client',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        foreach ($users as $user) {
            $this->db->table('users')->insert($user);
        }

        echo "  3 utilisateurs de démonstration insérés.\n";
    }
}
