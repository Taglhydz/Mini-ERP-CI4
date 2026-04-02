<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddThemeFieldsToCompanies extends Migration
{
    public function up(): void
    {
        $fields = [
            'show_name' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'cover_path',
            ],
            'color_primary' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#0d6efd',
                'after'      => 'show_name',
            ],
        ];

        $this->forge->addColumn('companies', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('companies', ['show_name', 'color_primary']);
    }
}
