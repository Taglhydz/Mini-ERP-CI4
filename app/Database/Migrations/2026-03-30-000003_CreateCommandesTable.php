<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommandesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'statut' => [
                'type'       => 'ENUM',
                'constraint' => ['brouillon', 'confirmee', 'livree', 'annulee'],
                'default'    => 'brouillon',
            ],
            'date_commande' => [
                'type' => 'DATE',
            ],
            'montant_ht' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '0.00',
            ],
            'taux_tva' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '20.00',
            ],
            'montant_ttc' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '0.00',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('commandes');
    }

    public function down(): void
    {
        $this->forge->dropTable('commandes');
    }
}
