<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColorSecondaryToCompanies extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('companies', [
            'color_secondary' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#ffffff',
                'null'       => false,
                'after'      => 'color_primary',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('companies', 'color_secondary');
    }
}
