<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;
use Faker\Generator;

/**
 * DemoSeeder — Données de démonstration complètes (développement / présentation).
 *
 * Appelle InitialSeeder puis ajoute :
 *   - 2 entreprises supplémentaires (3 au total)
 *   - 1 manager par entreprise supplémentaire
 *   - 5 clients par entreprise (15 clients)
 *   - 10 produits par entreprise, catalogues thématiques (30 produits)
 *   - ~12 commandes par entreprise avec leurs lignes (36 commandes)
 *
 * Utilisation : php spark db:seed DemoSeeder
 */
class DemoSeeder extends Seeder
{
    private const VAT_RATE = 20.00;

    /** IDs des entreprises dans l'ordre d'insertion */
    private array $companyIds = [];

    // ── Catalogues produits par entreprise (index 0, 1, 2) ───────────────────
    private const CATALOGS = [
        // Entreprise 0 — Mon Entreprise : IT & Bureautique
        0 => [
            ['PRO-INF-001', 'Ordinateur portable Core i7',      'Pro 16 Go RAM, 512 Go SSD, Windows 11',            1299.00,  12],
            ['PRO-INF-002', 'Écran 27" Full HD',                 'LED 1920×1080, HDMI/DisplayPort',                   289.00,  25],
            ['PRO-INF-003', 'Clavier mécanique AZERTY',          'Switches bleus, rétroéclairé, USB',                  79.90,  45],
            ['PRO-INF-004', 'Souris sans fil ergonomique',       'Nano-récepteur, 18 mois d\'autonomie',               39.90,  60],
            ['PRO-BUR-001', 'Ramette papier A4 500 feuilles',    'Papier 80 g/m², certifié PEFC',                       8.90, 200],
            ['PRO-BUR-002', 'Stylos bille bleu — boîte de 50',  'Encre à séchage rapide, pointe M',                   12.50, 150],
            ['PRO-MOB-001', 'Chaise ergonomique de bureau',      'Hauteur réglable, accoudoirs 3D, roulettes 60 mm',  299.00,   8],
            ['PRO-MOB-002', 'Bureau assis-debout électrique',    'Plateau 160×80 cm, motorisé 72-120 cm',             649.00,   4],
            ['PRO-CON-001', 'Toner noir HP LaserJet compatible', 'Rendement 3 000 pages',                              34.90,  40],
            ['PRO-SRV-001', 'Formation bureautique — 1 journée', 'Excel, Word, Outlook — présentiel ou distanciel',   350.00,  99],
        ],
        // Entreprise 1 — Tech Solutions : Web & Numérique
        1 => [
            ['TSO-HEB-001', 'Hébergement web Pro — 1 an',       '99,9 % uptime, SSD NVMe, PHP 8.2',                  120.00,  99],
            ['TSO-HEB-002', 'Nom de domaine .fr — 1 an',        'Renouvellement inclus la première année',             14.90,  99],
            ['TSO-DGT-001', 'Maintenance WordPress mensuelle',   'MàJ core+plugins, sauvegardes, monitoring 24/7',     49.90,  99],
            ['TSO-DGT-002', 'Audit SEO complet',                 '30 préconisations + plan d\'action prioritisé',      349.00,  99],
            ['TSO-DGT-003', 'Site vitrine sur mesure',           '5 pages, responsive, RGPD, SEO-ready',             1490.00,   5],
            ['TSO-DGT-004', 'Boutique e-commerce',               'WooCommerce + Stripe, formation incluse',           2490.00,   3],
            ['TSO-SEC-001', 'Certificat SSL Wildcard — 1 an',   'Sous-domaines illimités, DV, autorenew',              89.00,  99],
            ['TSO-SEC-002', 'Audit cybersécurité TPE/PME',       'Rapport complet + préconisations personnalisées',    890.00,  10],
            ['TSO-SUP-001', 'Support technique 10 heures',       'Téléphone + télémaintenance, 12 mois',               490.00,  99],
            ['TSO-SUP-002', 'Migration site web',                'Transfert hébergeur garanti, zéro interruption',     299.00,  99],
        ],
        // Entreprise 2 — Boutique Dupont : Mode & Accessoires
        2 => [
            ['BDU-TSH-001', 'T-shirt coton bio Homme',          'Jersey 180 g, certifié GOTS, S/M/L/XL',              19.90,  80],
            ['BDU-TSH-002', 'T-shirt coton bio Femme',          'Jersey 160 g, coupe semi-ajustée, XS/S/M/L',         19.90,  80],
            ['BDU-PAN-001', 'Jean slim stretch brut',           'Denim 98 % coton + 2 % élasthane',                    59.90,  40],
            ['BDU-PAN-002', 'Chino beige coupe droite',         '100 % coton twill, coupe relax',                      49.90,  35],
            ['BDU-VTM-001', 'Veste en lin naturel',             '100 % lin, légère, printemps-été',                    89.90,  20],
            ['BDU-VTM-002', 'Hoodie molleton unisexe',          '80 % coton / 20 % polyester recyclé',                 44.90,  50],
            ['BDU-SAC-001', 'Sac à dos toile 25 L',             'Renforts cuir, compartiment laptop 13"',              79.90,  25],
            ['BDU-ACC-001', 'Ceinture cuir vachette',           'Tailles 36-44, boucle métal laiton',                  29.90,  60],
            ['BDU-ACC-002', 'Casquette brodée logo',            '100 % coton, 6 panneaux, taille unique',              24.90,  45],
            ['BDU-ACC-003', 'Chaussettes — lot de 5 paires',    'Coton peigné, renforcé talon & bout de pied',         14.90, 100],
        ],
    ];

    // ── Clients par entreprise (index 0, 1, 2) ────────────────────────────────
    // Format : [prénom, nom, email, téléphone, n°, type_voie, nom_voie, ville, cp]
    private const CLIENTS = [
        0 => [
            ['Jean',      'Dupont',   'jean.dupont@demo.fr',      '0142567890', '12',  'Rue',        'de la Paix',       'Paris',       '75001'],
            ['Sophie',    'Martin',   'sophie.martin@demo.fr',    '0472112233', '8',   'Avenue',     'des Artisans',     'Lyon',        '69003'],
            ['Pierre',    'Rousseau', 'pierre.rousseau@demo.fr',  '0388456789', '55',  'Boulevard',  "de l'Europe",      'Strasbourg',  '67000'],
            ['Marie',     'Laurent',  'marie.laurent@demo.fr',    '0556781234', '3',   'Allée',      'des Pins',         'Bordeaux',    '33000'],
            ['Thomas',    'Petit',    'thomas.petit@demo.fr',     '0251234567', '17',  'Zone',       'Industrielle Nord','Nantes',      '44100'],
        ],
        1 => [
            ['Alice',     'Bernard',  'alice.bernard@demo.fr',    '0491345678', '29',  'Chemin',     'des Collines',     'Marseille',   '13008'],
            ['Hugo',      'Moreau',   'hugo.moreau@demo.fr',      '0134567890', '4',   'Place',      'du Marché',        'Versailles',  '78000'],
            ['Camille',   'Leclerc',  'camille.leclerc@demo.fr',  '0320123456', '6',   'Impasse',    'des Acacias',      'Lille',       '59000'],
            ['Lucas',     'Dubois',   'lucas.dubois@demo.fr',     '0235678901', '100', 'Quai',       'de la Seine',      'Rouen',       '76000'],
            ['Emma',      'Richard',  'emma.richard@demo.fr',     '0493456778', '2',   'Promenade',  'des Arts',         'Nice',        '06000'],
        ],
        2 => [
            ['Nicolas',   'Simon',    'nicolas.simon@demo.fr',    '0561234567', '42',  'Avenue',     'des Minimes',      'Toulouse',    '31200'],
            ['Julie',     'Lefèvre',  'julie.lefevre@demo.fr',    '0476345678', '9',   'Rue',        'Stendhal',         'Grenoble',    '38000'],
            ['Antoine',   'Garcia',   'antoine.garcia@demo.fr',   '0240567890', '33',  'Rue',        'de la Loire',      'Saint-Nazaire','44600'],
            ['Laura',     'Michel',   'laura.michel@demo.fr',     '0155678901', '15',  'Avenue',     'de Wagram',        'Paris',       '75017'],
            ['Kévin',     'Roux',     'kevin.roux@demo.fr',       '0389123456', '7',   'Route',      'des Vosges',       'Colmar',      '68000'],
        ],
    ];

    // ── Scénarios de commandes (communs aux 3 entreprises) ────────────────────
    // Format : [client_index (0-4), [product_index => qty, ...], date_offset, status]
    private const ORDER_SCENARIOS = [
        [0, [0 => 1,  1 => 2],          '-11 months', 'delivered'],
        [1, [4 => 5,  5 => 3],          '-10 months', 'delivered'],
        [2, [7 => 2],                   ' -9 months', 'delivered'],
        [3, [1 => 1,  2 => 1, 3 => 1], ' -8 months', 'delivered'],
        [4, [8 => 4,  9 => 2],          ' -7 months', 'delivered'],
        [0, [0 => 3,  6 => 1],          ' -6 months', 'delivered'],
        [1, [3 => 10, 4 => 5],          ' -5 months', 'confirmed'],
        [2, [5 => 2,  9 => 1],          ' -4 months', 'confirmed'],
        [3, [7 => 6,  8 => 3],          ' -3 months', 'confirmed'],
        [4, [0 => 1,  2 => 2, 6 => 1], ' -2 months', 'confirmed'],
        [0, [1 => 5],                   ' -3 weeks',  'draft'],
        [1, [4 => 2,  7 => 1],          ' -1 weeks',  'draft'],
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function run(): void
    {
        echo "\n── InitialSeeder ─────────────────────────────────────────────────────────\n";
        $this->call(InitialSeeder::class);

        $faker = Factory::create('fr_FR');
        $faker->seed(4242);

        // Récupère l'ID de l'entreprise créée par InitialSeeder
        $row = $this->db->table('companies')
            ->where('slug', 'mon-entreprise')
            ->get()->getRowArray();

        $this->companyIds[] = (int) $row['id'];

        echo "\n── DemoSeeder ────────────────────────────────────────────────────────────\n";
        $this->seedCompanies($faker);
        $this->seedManagers($faker);
        $this->seedClients($faker);
        $this->seedProducts($faker);
        $this->seedOrders($faker);

        echo "\n";
        echo "  Comptes de démonstration :\n";
        echo "    admin@demo.fr          / Admin123!\n";
        echo "    manager@demo.fr        / Manager123!\n";
        echo "    manager2@demo.fr       / Manager123!\n";
        echo "    manager3@demo.fr       / Manager123!\n";
        echo "    jean.dupont@demo.fr    / demo1234  (client — et autres clients)\n";
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ENTREPRISES
    // ─────────────────────────────────────────────────────────────────────────

    private function seedCompanies(Generator $faker): void
    {
        $companies = [
            [
                'name'        => 'Tech Solutions',
                'slug'        => 'tech-solutions',
                'email'       => 'contact@tech-solutions-demo.fr',
                'phone'       => '0155000001',
                'city'        => 'Lyon',
                'postal_code' => '69001',
            ],
            [
                'name'        => 'Boutique Dupont',
                'slug'        => 'boutique-dupont',
                'email'       => 'bonjour@boutique-dupont-demo.fr',
                'phone'       => '0144000002',
                'city'        => 'Bordeaux',
                'postal_code' => '33000',
            ],
        ];

        foreach ($companies as $data) {
            $createdAt = $faker->dateTimeBetween('-2 years', '-6 months')->format('Y-m-d H:i:s');
            $this->db->table('companies')->insert(array_merge($data, [
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]));
            $this->companyIds[] = $this->db->insertID();
        }

        echo "  ✓ " . count($companies) . " entreprises supplémentaires insérées.\n";
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MANAGERS
    // ─────────────────────────────────────────────────────────────────────────

    private function seedManagers(Generator $faker): void
    {
        // Managers pour les entreprises 2 et 3 (l'entreprise 1 a déjà son manager)
        $managers = [
            ['Manager Tech',   'manager2@demo.fr', $this->companyIds[1]],
            ['Manager Dupont', 'manager3@demo.fr', $this->companyIds[2]],
        ];

        $now = date('Y-m-d H:i:s');

        foreach ($managers as [$username, $email, $companyId]) {
            $this->db->table('users')->insert([
                'username'      => $username,
                'email'         => $email,
                'password_hash' => password_hash('Manager123!', PASSWORD_DEFAULT),
                'role'          => 'manager',
                'company_id'    => $companyId,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        echo "  ✓ " . count($managers) . " managers insérés.\n";
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CLIENTS
    // ─────────────────────────────────────────────────────────────────────────

    private function seedClients(Generator $faker): void
    {
        $total = 0;

        foreach ($this->companyIds as $idx => $companyId) {
            $clients = self::CLIENTS[$idx] ?? [];

            foreach ($clients as [$fn, $ln, $email, $phone, $streetNum, $streetType, $streetName, $city, $postal]) {
                $createdAt = $faker->dateTimeBetween('-18 months', '-6 months')->format('Y-m-d H:i:s');
                $updatedAt = $faker->dateTimeBetween('-5 months', 'now')->format('Y-m-d H:i:s');

                $this->db->table('users')->insert([
                    'username'      => $fn . ' ' . $ln,
                    'first_name'    => $fn,
                    'last_name'     => $ln,
                    'email'         => $email,
                    'password_hash' => password_hash('demo1234', PASSWORD_DEFAULT),
                    'role'          => 'client',
                    'company_id'    => $companyId,
                    'phone'         => $phone,
                    'street_number' => $streetNum,
                    'street_type'   => $streetType,
                    'street_name'   => $streetName,
                    'city'          => $city,
                    'postal_code'   => $postal,
                    'created_at'    => $createdAt,
                    'updated_at'    => $updatedAt,
                ]);
                $total++;
            }
        }

        echo "  ✓ {$total} clients insérés (mot de passe : demo1234).\n";
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRODUITS
    // ─────────────────────────────────────────────────────────────────────────

    private function seedProducts(Generator $faker): void
    {
        $total = 0;

        foreach ($this->companyIds as $idx => $companyId) {
            $catalog = self::CATALOGS[$idx] ?? [];

            foreach ($catalog as [$ref, $name, $description, $price, $stock]) {
                $this->db->table('products')->insert([
                    'company_id'  => $companyId,
                    'reference'   => $ref,
                    'name'        => $name,
                    'description' => $description,
                    'unit_price'  => $price,
                    'stock'       => $stock,
                    'created_at'  => $faker->dateTimeBetween('-18 months', '-3 months')->format('Y-m-d H:i:s'),
                    'updated_at'  => $faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d H:i:s'),
                ]);
                $total++;
            }
        }

        echo "  ✓ {$total} produits insérés.\n";
    }

    // ─────────────────────────────────────────────────────────────────────────
    // COMMANDES + LIGNES
    // ─────────────────────────────────────────────────────────────────────────

    private function seedOrders(Generator $faker): void
    {
        $year     = 2026;
        $sequence = 1;
        $total    = 0;

        foreach ($this->companyIds as $companyId) {
            // Clients et produits de cette entreprise
            $userIds = array_column(
                $this->db->table('users')
                    ->where('company_id', $companyId)
                    ->where('role', 'client')
                    ->select('id')
                    ->get()->getResultArray(),
                'id'
            );

            $products = $this->db->table('products')
                ->where('company_id', $companyId)
                ->where('deleted_at', null)
                ->orderBy('id', 'ASC')
                ->select('id, name, unit_price')
                ->get()->getResultArray();

            if (empty($userIds) || empty($products)) {
                echo "  ✗ Commandes ignorées pour company_id={$companyId} : clients ou produits manquants.\n";
                continue;
            }

            foreach (self::ORDER_SCENARIOS as [$clientIdx, $linesDef, $dateOffset, $status]) {
                $userId = $userIds[$clientIdx % count($userIds)];
                $number = sprintf('CMD-%d-%04d', $year, $sequence++);
                $dt     = $faker->dateTimeBetween($dateOffset . ' -3 days', $dateOffset . ' +3 days');

                // Calcul des montants et préparation des lignes
                $amountHt = 0.0;
                $items    = [];

                foreach ($linesDef as $productIdx => $qty) {
                    $product   = $products[$productIdx % count($products)];
                    $unitPrice = (float) $product['unit_price'];
                    $subtotal  = round($qty * $unitPrice, 2);
                    $amountHt += $subtotal;

                    $items[] = [
                        'product_id' => $product['id'],
                        'name'       => $product['name'],
                        'quantity'   => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal'   => $subtotal,
                    ];
                }

                $amountHt  = round($amountHt, 2);
                $amountTtc = round($amountHt * (1 + self::VAT_RATE / 100), 2);

                // Insertion commande
                $this->db->table('orders')->insert([
                    'company_id' => $companyId,
                    'user_id'    => $userId,
                    'number'     => $number,
                    'status'     => $status,
                    'order_date' => $dt->format('Y-m-d'),
                    'amount_ht'  => $amountHt,
                    'vat_rate'   => self::VAT_RATE,
                    'amount_ttc' => $amountTtc,
                    'notes'      => $faker->boolean(20) ? $faker->sentence(6) : null,
                    'created_at' => $dt->format('Y-m-d H:i:s'),
                    'updated_at' => $dt->modify('+' . random_int(1, 48) . ' hours')->format('Y-m-d H:i:s'),
                ]);

                $orderId = $this->db->insertID();

                // Insertion lignes de commande
                foreach ($items as $item) {
                    $item['order_id'] = $orderId;
                    $this->db->table('order_items')->insert($item);
                }

                $total++;
            }
        }

        echo "  ✓ {$total} commandes insérées avec leurs lignes.\n";
    }
}
