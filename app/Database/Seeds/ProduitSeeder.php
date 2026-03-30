<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class ProduitSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(2002);

        $produits = [
            // Informatique
            ['REF-INF-001', 'Ordinateur portable 15" Core i7',     'PC portable professionnel, 16 Go RAM, 512 Go SSD',    1299.00, 12],
            ['REF-INF-002', 'Écran 27" Full HD',                    'Écran LED 27 pouces, résolution 1920x1080, HDMI/DP',   289.00, 25],
            ['REF-INF-003', 'Clavier mécanique rétroéclairé',       'Clavier AZERTY switches bleus, USB',                   79.90,  45],
            ['REF-INF-004', 'Souris sans fil ergonomique',          'Autonomie 18 mois, USB nano-récepteur',                39.90,  60],
            ['REF-INF-005', 'Disque dur externe 2 To',              'USB 3.0, compatible PC/Mac',                            89.00,  30],
            ['REF-INF-006', 'Switch réseau 8 ports Gigabit',        'Plug & Play, 10/100/1000 Mbps',                        49.90,  18],

            // Bureautique & Fournitures
            ['REF-BUR-001', 'Ramette papier A4 80g (500 feuilles)', 'Papier blanc qualité supérieure',                       8.90, 200],
            ['REF-BUR-002', 'Stylos bille bleu (boîte de 50)',      'Écriture fluide, encre à séchage rapide',               12.50, 150],
            ['REF-BUR-003', 'Classeur à levier A4 80mm',            'Dos large, étiquette personnalisable',                   4.90, 120],
            ['REF-BUR-004', 'Post-it 76x76mm (pack de 12)',         'Adhésif repositionnable, couleurs assorties',           11.90,  80],
            ['REF-BUR-005', 'Agrafeuse de bureau 24/6',             'Capacité 25 feuilles, incluse 1000 agrafes',             9.50,  55],

            // Mobilier
            ['REF-MOB-001', 'Chaise ergonomique de bureau',         'Réglable en hauteur, accoudoirs, roulettes 60mm',     299.00,   8],
            ['REF-MOB-002', 'Bureau assis-debout électrique',       'Plateau 160x80cm, hauteur 72-120cm motorisée',        649.00,   4],
            ['REF-MOB-003', 'Armoire métallique à clé 2 portes',    'HxLxP : 180x90x40cm, 4 étagères réglables',           349.00,   6],

            // Consommables
            ['REF-CON-001', 'Cartouche toner noir compatible',      'Rendement 3000 pages, compatible HP LaserJet',         34.90,  40],
            ['REF-CON-002', 'Cartouche encre couleur 4-en-1',       'Compatible Epson WF, rendement 650 pages',             22.90,  35],
            ['REF-CON-003', 'Étiquettes adhésives blanches 99x57mm','Planche de 10, lot de 100 planches',                   19.90,  70],

            // Services / Formations
            ['REF-SRV-001', 'Formation bureautique 1 journée',      'Excel, Word, Outlook — présentiel ou distanciel',     350.00,  99],
            ['REF-SRV-002', 'Maintenance informatique annuelle',     'Forfait 10h intervention sur site',                   490.00,  99],
            ['REF-SRV-003', 'Audit cybersécurité TPE/PME',          'Rapport complet + préconisations',                    890.00,  99],
        ];

        foreach ($produits as [$ref, $designation, $description, $prix, $stock]) {
            $this->db->table('produits')->insert([
                'reference'   => $ref,
                'designation' => $designation,
                'description' => $description,
                'prix_unitaire' => $prix,
                'stock'       => $stock,
                'created_at'  => $faker->dateTimeBetween('-18 months', '-3 months')->format('Y-m-d H:i:s'),
                'updated_at'  => $faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d H:i:s'),
            ]);
        }

        echo "  20 produits insérés.\n";
    }
}
