<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLignesCommandeTable extends Migration
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
            'commande_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'produit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'designation' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'quantite' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'prix_unitaire' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'sous_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('commande_id', 'commandes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('produit_id', 'produits', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('lignes_commande');
    }

    public function down(): void
    {
        $this->forge->dropTable('lignes_commande');
    }
}
