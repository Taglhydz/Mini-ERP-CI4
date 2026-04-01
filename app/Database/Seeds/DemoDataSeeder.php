<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

/**
 * Seeder de démonstration — à lancer APRÈS InitialDataSeeder.
 *
 * Crée pour l'entreprise id=1 (Mon Entreprise) :
 *   - 20 produits
 *   - 10 clients (users role=client)
 *   - ~30 commandes avec leurs lignes
 *
 * Utilisation : php spark db:seed DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    private const COMPANY_ID = 1;
    private const VAT_RATE   = 20.00;

    public function run(): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(4242);

        $this->seedProducts($faker);
        $this->seedClients($faker);
        $this->seedOrders($faker);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRODUITS
    // ─────────────────────────────────────────────────────────────────────────

    private function seedProducts(\Faker\Generator $faker): void
    {
        $products = [
            // Informatique
            ['REF-INF-001', 'Ordinateur portable 15" Core i7',      'PC portable pro 16 Go RAM, 512 Go SSD, Windows 11',   1299.00,  12],
            ['REF-INF-002', 'Écran 27" Full HD',                     'Dalle LED 1920×1080, HDMI/DisplayPort, sans bords',    289.00,  25],
            ['REF-INF-003', 'Clavier mécanique rétroéclairé AZERTY', 'Switches bleus, USB, compatible Windows/Mac',           79.90,  45],
            ['REF-INF-004', 'Souris sans fil ergonomique',           'Nano-récepteur USB, autonomie 18 mois',                 39.90,  60],
            ['REF-INF-005', 'Disque dur externe 2 To USB 3.0',       'Portable, compatible PC et Mac',                        89.00,  30],
            ['REF-INF-006', 'Switch réseau 8 ports Gigabit',         'Plug & Play, 10/100/1000 Mbps',                         49.90,  18],

            // Bureautique & Fournitures
            ['REF-BUR-001', 'Ramette papier A4 80g (500 feuilles)',  'Papier blanc qualité supérieure, certifié PEFC',          8.90, 200],
            ['REF-BUR-002', 'Stylos bille bleu — boîte de 50',       'Écriture fluide, encre à séchage rapide',                12.50, 150],
            ['REF-BUR-003', 'Classeur à levier A4 80 mm',            'Dos large, étiquette personnalisable',                     4.90, 120],
            ['REF-BUR-004', 'Post-it 76×76 mm — pack de 12 blocs',   'Adhésif repositionnable, couleurs assorties',            11.90,  80],
            ['REF-BUR-005', 'Agrafeuse de bureau 24/6',              'Capacité 25 feuilles, livré avec 1000 agrafes',            9.50,  55],

            // Mobilier
            ['REF-MOB-001', 'Chaise ergonomique de bureau',          'Hauteur réglable, accoudoirs 3D, roulettes 60 mm',      299.00,   8],
            ['REF-MOB-002', 'Bureau assis-debout électrique',        'Plateau 160×80 cm, hauteur 72-120 cm motorisée',        649.00,   4],
            ['REF-MOB-003', 'Armoire métallique à clé 2 portes',     'H 180 × L 90 × P 40 cm, 4 étagères réglables',          349.00,   6],

            // Consommables
            ['REF-CON-001', 'Toner noir compatible HP LaserJet',     'Rendement 3 000 pages',                                  34.90,  40],
            ['REF-CON-002', 'Cartouche encre couleur 4-en-1 Epson',  'Rendement 650 pages, compatible WorkForce',              22.90,  35],
            ['REF-CON-003', 'Étiquettes adhésives blanches 99×57 mm','Lot 100 planches × 10 étiquettes',                       19.90,  70],

            // Services
            ['REF-SRV-001', 'Formation bureautique — 1 journée',     'Excel, Word, Outlook — présentiel ou distanciel',       350.00,  99],
            ['REF-SRV-002', 'Forfait maintenance informatique 10 h', 'Interventions sur site, valable 12 mois',                490.00,  99],
            ['REF-SRV-003', 'Audit cybersécurité TPE/PME',           'Rapport complet + préconisations personnalisées',        890.00,  99],
        ];

        foreach ($products as [$ref, $name, $description, $price, $stock]) {
            $this->db->table('products')->insert([
                'company_id'  => self::COMPANY_ID,
                'reference'   => $ref,
                'name'        => $name,
                'description' => $description,
                'unit_price'  => $price,
                'stock'       => $stock,
                'created_at'  => $faker->dateTimeBetween('-18 months', '-3 months')->format('Y-m-d H:i:s'),
                'updated_at'  => $faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d H:i:s'),
            ]);
        }

        echo "  ✓ " . count($products) . " produits insérés.\n";
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CLIENTS (users role=client)
    // ─────────────────────────────────────────────────────────────────────────

    private function seedClients(\Faker\Generator $faker): void
    {
        // [first_name, last_name, email, phone, street_number, street_type, street_name, city, postal_code]
        $clients = [
            ['Jean',      'Dupont',    'jean.dupont@demo.fr',      '0142567890', '12',  'Rue',       'de la Paix',        'Paris',         '75001'],
            ['Marc',      'Leblanc',   'marc.leblanc@demo.fr',     '0472112233', '8',   'Avenue',    'des Artisans',      'Lyon',          '69003'],
            ['Sophie',    'Moreau',    'sophie.moreau@demo.fr',    '0388456789', '55',  'Boulevard', "de l'Europe",       'Strasbourg',    '67000'],
            ['Claire',    'Martin',    'claire.martin@demo.fr',    '0556781234', '3',   'Allée',     'des Pins',          'Bordeaux',      '33000'],
            ['Pierre',    'Rousseau',  'pierre.rousseau@demo.fr',  '0251234567', '17',  'Zone',      'Industrielle Nord', 'Nantes',        '44100'],
            ['Ahmed',     'Girard',    'ahmed.girard@demo.fr',     '0491345678', '29',  'Chemin',    'des Collines',      'Marseille',     '13008'],
            ['Isabelle',  'Fontaine',  'isabelle.fontaine@demo.fr','0134567890', '4',   'Place',     'du Marché',         'Versailles',    '78000'],
            ['Thomas',    'Petit',     'thomas.petit@demo.fr',     '0320123456', '6',   'Impasse',   'des Acacias',       'Lille',         '59000'],
            ['Nathalie',  'Dubois',    'nathalie.dubois@demo.fr',  '0235678901', '100', 'Quai',      'de la Seine',       'Rouen',         '76000'],
            ['Lucas',     'Bernard',   'lucas.bernard@demo.fr',    '0493456778', '2',   'Promenade', 'des Arts',          'Nice',          '06000'],
        ];

        foreach ($clients as [$fn, $ln, $email, $phone, $streetNum, $streetType, $streetName, $city, $postal]) {
            $this->db->table('users')->insert([
                'username'      => $fn . ' ' . $ln,
                'first_name'    => $fn,
                'last_name'     => $ln,
                'email'         => $email,
                'password_hash' => password_hash('demo1234', PASSWORD_DEFAULT),
                'role'          => 'client',
                'company_id'    => self::COMPANY_ID,
                'phone'         => $phone,
                'street_number' => $streetNum,
                'street_type'   => $streetType,
                'street_name'   => $streetName,
                'city'          => $city,
                'postal_code'   => $postal,
                'created_at'    => $faker->dateTimeBetween('-18 months', '-6 months')->format('Y-m-d H:i:s'),
                'updated_at'    => $faker->dateTimeBetween('-5 months', 'now')->format('Y-m-d H:i:s'),
            ]);
        }

        echo "  ✓ " . count($clients) . " clients insérés (mot de passe : demo1234).\n";
    }

    // ─────────────────────────────────────────────────────────────────────────
    // COMMANDES + LIGNES
    // ─────────────────────────────────────────────────────────────────────────

    private function seedOrders(\Faker\Generator $faker): void
    {
        $userIds  = array_column(
            $this->db->table('users')
                ->where('company_id', self::COMPANY_ID)
                ->where('role', 'client')
                ->select('id')
                ->get()->getResultArray(),
            'id'
        );

        $products = $this->db->table('products')
            ->where('company_id', self::COMPANY_ID)
            ->where('deleted_at', null)
            ->select('id, name, unit_price')
            ->get()->getResultArray();

        if (empty($userIds) || empty($products)) {
            echo "  ✗ Impossible de créer les commandes : clients ou produits manquants.\n";
            return;
        }

        // Scénarios : [client_index, [product_index => qty, ...], date_relative, status]
        $scenarios = [
            [0, [0 => 1,  1 => 2],           '-13 months', 'delivered'],
            [1, [6 => 10, 7 => 5],            '-13 months', 'delivered'],
            [2, [12 => 1],                    '-12 months', 'delivered'],
            [3, [18 => 1, 19 => 1],           '-12 months', 'delivered'],
            [4, [0 => 2,  4 => 3],            '-11 months', 'delivered'],
            [5, [11 => 2, 12 => 1],           '-11 months', 'delivered'],
            [6, [6 => 20, 7 => 15, 8 => 10], '-10 months', 'delivered'],
            [7, [2 => 4,  3 => 4,  5 => 2],  '-10 months', 'delivered'],
            [8, [17 => 2],                    '-9 months',  'delivered'],
            [9, [1 => 1,  2 => 1,  3 => 1],  '-9 months',  'delivered'],
            [0, [14 => 5, 15 => 3],           '-8 months',  'delivered'],
            [1, [11 => 1, 12 => 1],           '-8 months',  'delivered'],
            [2, [0 => 3,  1 => 3],            '-7 months',  'delivered'],
            [3, [6 => 50, 7 => 30],           '-7 months',  'delivered'],
            [4, [18 => 3, 19 => 1],           '-7 months',  'delivered'],
            [5, [16 => 10],                   '-6 months',  'delivered'],
            [6, [0 => 1,  4 => 2],            '-6 months',  'delivered'],
            [7, [17 => 5, 18 => 1],           '-5 months',  'delivered'],
            [8, [11 => 2],                    '-5 months',  'delivered'],
            [9, [6 => 30, 7 => 20, 8 => 10], '-5 months',  'confirmed'],
            [0, [0 => 1,  1 => 2,  2 => 2],  '-4 months',  'confirmed'],
            [1, [13 => 1, 12 => 1],           '-4 months',  'confirmed'],
            [2, [14 => 10, 15 => 5],          '-3 months',  'confirmed'],
            [3, [6 => 100],                   '-3 months',  'confirmed'],
            [4, [18 => 2, 19 => 2],           '-3 months',  'confirmed'],
            [5, [0 => 5,  4 => 2],            '-2 months',  'confirmed'],
            [6, [2 => 3,  3 => 3,  5 => 1],  '-2 months',  'confirmed'],
            [7, [17 => 1],                    '-2 months',  'confirmed'],
            [8, [11 => 3],                    '-2 months',  'cancelled'],
            [9, [6 => 15, 7 => 10, 8 => 5],  '-6 weeks',   'delivered'],
            [0, [0 => 2,  1 => 1],            '-5 weeks',   'delivered'],
            [1, [19 => 1],                    '-4 weeks',   'confirmed'],
            [2, [14 => 8,  15 => 4],          '-3 weeks',   'confirmed'],
            [3, [16 => 20],                   '-2 weeks',   'confirmed'],
            [4, [0 => 1,  11 => 1],           '-10 days',   'confirmed'],
            [5, [8 => 10, 9 => 8,  10 => 5], '-8 days',    'draft'],
            [6, [18 => 1, 17 => 3],           '-5 days',    'draft'],
            [7, [6 => 25, 7 => 25],           '-3 days',    'draft'],
            [8, [0 => 1,  1 => 2,  2 => 1],  '-2 days',    'draft'],
            [9, [12 => 1],                    '-1 days',    'draft'],
        ];

        $year     = 2026;
        $sequence = 1;
        $count    = 0;

        foreach ($scenarios as [$clientIdx, $linesDef, $dateRelative, $status]) {
            $userId    = $userIds[$clientIdx % count($userIds)];
            $orderDate = $faker->dateTimeBetween($dateRelative)->format('Y-m-d');
            $number    = sprintf('CMD-%d-%04d', $year, $sequence++);

            $amountHt  = 0.0;

            // Insérer la commande (montants à 0, on mettra à jour après)
            $this->db->table('orders')->insert([
                'company_id'  => self::COMPANY_ID,
                'user_id'     => $userId,
                'number'      => $number,
                'status'      => $status,
                'order_date'  => $orderDate,
                'amount_ht'   => 0,
                'vat_rate'    => self::VAT_RATE,
                'amount_ttc'  => 0,
                'created_at'  => $orderDate . ' 08:00:00',
                'updated_at'  => $orderDate . ' 08:00:00',
            ]);

            $orderId = $this->db->insertID();

            // Insérer les lignes
            foreach ($linesDef as $productIdx => $qty) {
                $product = $products[$productIdx % count($products)];
                $price   = (float) $product['unit_price'];
                $sub     = round($price * $qty, 2);
                $amountHt += $sub;

                $this->db->table('order_items')->insert([
                    'order_id'   => $orderId,
                    'product_id' => $product['id'],
                    'name'       => $product['name'],
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $sub,
                ]);
            }

            // Mettre à jour les totaux
            $amountHt  = round($amountHt, 2);
            $amountTtc = round($amountHt * (1 + self::VAT_RATE / 100), 2);

            $this->db->table('orders')->where('id', $orderId)->update([
                'amount_ht'  => $amountHt,
                'amount_ttc' => $amountTtc,
            ]);

            $count++;
        }

        echo "  ✓ {$count} commandes insérées avec leurs lignes.\n";
    }
}
