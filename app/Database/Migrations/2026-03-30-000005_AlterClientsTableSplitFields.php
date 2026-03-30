<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterClientsTableSplitFields extends Migration
{
    public function up(): void
    {
        // Ajouter prenom après la colonne id
        $this->forge->addColumn('clients', [
            'prenom' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'id',
            ],
        ]);

        // Ajouter les 3 colonnes d'adresse décomposée
        $this->forge->addColumn('clients', [
            'adresse_numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
                'after'      => 'telephone',
            ],
            'adresse_type_voie' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'adresse_numero',
            ],
            'adresse_nom_voie' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'default'    => null,
                'after'      => 'adresse_type_voie',
            ],
        ]);

        // Supprimer l'ancienne colonne adresse monolithique
        $this->forge->dropColumn('clients', 'adresse');
    }

    public function down(): void
    {
        $this->forge->addColumn('clients', [
            'adresse' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'after'      => 'telephone',
            ],
        ]);

        $this->forge->dropColumn('clients', ['adresse_numero', 'adresse_type_voie', 'adresse_nom_voie', 'prenom']);
    }
}
