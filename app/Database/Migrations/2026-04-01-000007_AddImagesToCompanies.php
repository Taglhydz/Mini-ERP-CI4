<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImagesToCompanies extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('companies', [
            'logo_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'postal_code',
            ],
            'cover_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'logo_path',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('companies', ['logo_path', 'cover_path']);
    }
}
