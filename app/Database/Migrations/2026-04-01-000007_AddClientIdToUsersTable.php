<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClientIdToUsersTable extends Migration
{
    public function up(): void
    {
        $fields = [
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('users', $fields);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'SET NULL', 'CASCADE', 'fk_users_client_id');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('users', 'fk_users_client_id');
        $this->forge->dropColumn('users', 'client_id');
    }
}
