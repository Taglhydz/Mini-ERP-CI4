<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(1001);

        // Données fixes pour avoir des entreprises réalistes
        $entreprises = [
            ['Dupont & Associés',        'contact@dupont-associes.fr',    '01 42 56 78 90', '12 rue de la Paix',       'Paris',        '75001'],
            ['Menuiserie Leblanc',        'info@menuiserie-leblanc.fr',    '04 72 11 22 33', '8 avenue des Artisans',   'Lyon',         '69003'],
            ['TechnoSoft SARL',           'hello@technosoft.fr',           '03 88 45 67 89', '55 boulevard de l\'Europe','Strasbourg',  '67000'],
            ['Cabinet Martin Conseil',    'martin@cabinet-martin.fr',      '05 56 78 12 34', '3 allée des Pins',        'Bordeaux',     '33000'],
            ['Imprimerie Rousseau',       'commandes@imprimerie-rousseau.fr','02 51 23 45 67','17 zone industrielle',   'Nantes',       '44100'],
            ['BTP Girard',                'devis@btp-girard.fr',           '04 91 34 56 78', '29 chemin des Collines',  'Marseille',    '13008'],
            ['Pharmacie Moreau',          'pharmamoreau@sante.fr',         '01 34 56 78 90', '4 place du Marché',       'Versailles',   '78000'],
            ['Électricité Petit',         'contact@elec-petit.fr',         '03 20 12 34 56', '6 impasse des Acacias',   'Lille',        '59000'],
            ['Transports Dubois',         'logistics@transdubois.fr',      '02 35 67 89 01', '100 quai de la Seine',    'Rouen',        '76000'],
            ['Agence Pixel',              'bonjour@agence-pixel.fr',       '04 93 45 67 78', '2 promenade des Arts',    'Nice',         '06000'],
            ['AutoPro Renard',            'renard@autopro.fr',             '05 61 23 45 67', '42 avenue des Minimes',   'Toulouse',     '31200'],
            ['Librairie Lecomte',         'librairie@lecomte-livres.fr',   '04 76 34 56 78', '9 rue Stendhal',          'Grenoble',     '38000'],
            ['ConstrucForm',              'info@construcform.fr',          '02 40 56 78 90', '33 rue de la Loire',      'Saint-Nazaire','44600'],
            ['Services Plus',             'admin@servicesplus.fr',         '01 55 67 89 01', '15 avenue de Wagram',     'Paris',        '75017'],
            ['Fromagerie Bernard',        'fromagerie@bernard-terroir.fr', '03 89 12 34 56', '7 route des Vosges',      'Colmar',       '68000'],
        ];

        foreach ($entreprises as [$nom, $email, $tel, $adresse, $ville, $cp]) {
            $this->db->table('clients')->insert([
                'nom'         => $nom,
                'email'       => $email,
                'telephone'   => $tel,
                'adresse'     => $adresse,
                'ville'       => $ville,
                'code_postal' => $cp,
                'created_at'  => $faker->dateTimeBetween('-2 years', '-6 months')->format('Y-m-d H:i:s'),
                'updated_at'  => $faker->dateTimeBetween('-5 months', 'now')->format('Y-m-d H:i:s'),
            ]);
        }

        echo "  15 clients insérés.\n";
    }
}
