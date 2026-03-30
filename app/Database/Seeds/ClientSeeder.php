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

        // Données fixes : [prenom, nom, email, tel, numero, type_voie, nom_voie, ville, cp]
        $entreprises = [
            ['Jean',      'Dupont',    'contact@dupont-associes.fr',       '0142567890', '12',  'Rue',        'de la Paix',         'Paris',        '75001'],
            ['Marc',      'Leblanc',   'info@menuiserie-leblanc.fr',       '0472112233', '8',   'Avenue',     'des Artisans',       'Lyon',         '69003'],
            ['Sophie',    'Moreau',    'hello@technosoft.fr',              '0388456789', '55',  'Boulevard',  "de l'Europe",        'Strasbourg',   '67000'],
            ['Claire',    'Martin',    'martin@cabinet-martin.fr',         '0556781234', '3',   'Allée',      'des Pins',           'Bordeaux',     '33000'],
            ['Pierre',    'Rousseau',  'commandes@imprimerie-rousseau.fr', '0251234567', '17',  'Zone',       'Industrielle Nord',  'Nantes',       '44100'],
            ['Ahmed',     'Girard',    'devis@btp-girard.fr',              '0491345678', '29',  'Chemin',     'des Collines',       'Marseille',    '13008'],
            ['Isabelle',  'Moreau',    'pharmamoreau@sante.fr',            '0134567890', '4',   'Place',      'du Marché',          'Versailles',   '78000'],
            ['Thomas',    'Petit',     'contact@elec-petit.fr',            '0320123456', '6',   'Impasse',    'des Acacias',        'Lille',        '59000'],
            ['Nathalie',  'Dubois',    'logistics@transdubois.fr',         '0235678901', '100', 'Quai',       'de la Seine',        'Rouen',        '76000'],
            ['Lucas',     'Bernard',   'bonjour@agence-pixel.fr',          '0493456778', '2',   'Promenade',  'des Arts',           'Nice',         '06000'],
            ['Élodie',    'Renard',    'renard@autopro.fr',                '0561234567', '42',  'Avenue',     'des Minimes',        'Toulouse',     '31200'],
            ['Hugo',      'Lecomte',   'librairie@lecomte-livres.fr',      '0476345678', '9',   'Rue',        'Stendhal',           'Grenoble',     '38000'],
            ['Marie',     'Laurent',   'info@construcform.fr',             '0240567890', '33',  'Rue',        'de la Loire',        'Saint-Nazaire','44600'],
            ['François',  'Durand',    'admin@servicesplus.fr',            '0155678901', '15',  'Avenue',     'de Wagram',          'Paris',        '75017'],
            ['Sylvie',    'Bernard',   'fromagerie@bernard-terroir.fr',    '0389123456', '7',   'Route',      'des Vosges',         'Colmar',       '68000'],
        ];

        foreach ($entreprises as [$prenom, $nom, $email, $tel, $numero, $typeVoie, $nomVoie, $ville, $cp]) {
            $this->db->table('clients')->insert([
                'prenom'             => $prenom,
                'nom'                => $nom,
                'email'              => $email,
                'telephone'          => $tel,
                'adresse_numero'     => $numero,
                'adresse_type_voie'  => $typeVoie,
                'adresse_nom_voie'   => $nomVoie,
                'ville'              => $ville,
                'code_postal'        => $cp,
                'created_at'         => $faker->dateTimeBetween('-2 years', '-6 months')->format('Y-m-d H:i:s'),
                'updated_at'         => $faker->dateTimeBetween('-5 months', 'now')->format('Y-m-d H:i:s'),
            ]);
        }

        echo "  15 clients insérés.\n";
    }
}
