<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class CommandeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(3003);

        // Récupère les IDs clients et produits insertés
        $clientIds  = array_column($this->db->table('clients')->select('id')->get()->getResultArray(), 'id');
        $produits   = $this->db->table('produits')->select('id, prix_unitaire, designation')->get()->getResultArray();

        if (empty($clientIds) || empty($produits)) {
            echo "  ERREUR : lancez d'abord ClientSeeder et ProduitSeeder.\n";
            return;
        }

        $statuts   = ['brouillon', 'confirmee', 'confirmee', 'livree', 'livree', 'livree', 'annulee'];
        $tauxTva   = 20.00;
        $annee     = 2026;
        $sequence  = 1;

        // ~40 commandes réparties sur les 14 derniers mois
        $scenarios = [
            // [client_index, [produit_index => quantite], date_relative, statut_force]
            [0,  [0 => 1,  1 => 2],           '-13 months', 'livree'],
            [1,  [6 => 10, 7 => 5],            '-13 months', 'livree'],
            [2,  [12 => 1],                    '-12 months', 'livree'],
            [3,  [18 => 1, 19 => 1],           '-12 months', 'livree'],
            [4,  [0 => 2,  4 => 3],            '-11 months', 'livree'],
            [5,  [11 => 2, 12 => 1],           '-11 months', 'livree'],
            [6,  [6 => 20, 8 => 15, 9 => 10], '-10 months', 'livree'],
            [7,  [2 => 4,  3 => 4,  5 => 2],  '-10 months', 'livree'],
            [8,  [17 => 2],                    '-9 months',  'livree'],
            [9,  [1 => 1,  2 => 1,  3 => 1],  '-9 months',  'livree'],
            [10, [14 => 5, 15 => 3],           '-8 months',  'livree'],
            [11, [11 => 1, 13 => 1],           '-8 months',  'livree'],
            [12, [0 => 3,  1 => 3],            '-7 months',  'livree'],
            [13, [6 => 50, 7 => 30],           '-7 months',  'livree'],
            [14, [18 => 3, 19 => 1],           '-7 months',  'livree'],
            [0,  [16 => 10],                   '-6 months',  'livree'],
            [1,  [0 => 1,  4 => 2],            '-6 months',  'livree'],
            [2,  [17 => 5, 18 => 1],           '-5 months',  'livree'],
            [3,  [11 => 2],                    '-5 months',  'livree'],
            [4,  [6 => 30, 8 => 20, 9 => 10], '-5 months',  'confirmee'],
            [5,  [0 => 1,  1 => 2,  2 => 2],  '-4 months',  'confirmee'],
            [6,  [13 => 1, 12 => 1],           '-4 months',  'confirmee'],
            [7,  [14 => 10, 15 => 5],          '-3 months',  'confirmee'],
            [8,  [6 => 100],                   '-3 months',  'confirmee'],
            [9,  [18 => 2, 19 => 2],           '-3 months',  'confirmee'],
            [10, [0 => 5,  4 => 2],            '-2 months',  'confirmee'],
            [11, [2 => 3,  3 => 3,  5 => 1],  '-2 months',  'confirmee'],
            [12, [17 => 1],                    '-2 months',  'confirmee'],
            [13, [11 => 3],                    '-2 months',  'annulee'],
            [14, [6 => 15, 7 => 10, 8 => 5],  '-6 weeks',   'livree'],
            [0,  [0 => 2,  1 => 1],            '-5 weeks',   'livree'],
            [1,  [19 => 1],                    '-4 weeks',   'confirmee'],
            [2,  [14 => 8,  15 => 4],          '-3 weeks',   'confirmee'],
            [3,  [16 => 20],                   '-2 weeks',   'confirmee'],
            [4,  [0 => 1,  11 => 1],           '-10 days',   'confirmee'],
            [5,  [8 => 10, 9 => 8,  10 => 5], '-8 days',    'brouillon'],
            [6,  [18 => 1, 17 => 3],           '-5 days',    'brouillon'],
            [7,  [6 => 25, 7 => 25],           '-3 days',    'brouillon'],
            [8,  [0 => 1,  1 => 2,  2 => 1],  '-2 days',    'brouillon'],
            [9,  [12 => 1],                    '-1 days',    'brouillon'],
        ];

        foreach ($scenarios as $scenario) {
            [$clientIdx, $lignesDef, $dateRelative, $statut] = $scenario;

            $clientId = $clientIds[$clientIdx % count($clientIds)];
            $numero   = sprintf('CMD-%d-%04d', $annee, $sequence++);

            $dateCommande = $faker->dateTimeBetween($dateRelative . ' -3 days', $dateRelative . ' +3 days');

            // Calcul des montants
            $montantHt = 0.0;
            $lignesData = [];

            foreach ($lignesDef as $produitIdx => $quantite) {
                $produit     = $produits[$produitIdx % count($produits)];
                $prixUnit    = (float) $produit['prix_unitaire'];
                $sousTotal   = round($quantite * $prixUnit, 2);
                $montantHt  += $sousTotal;

                $lignesData[] = [
                    'produit_id'    => $produit['id'],
                    'designation'   => $produit['designation'],
                    'quantite'      => $quantite,
                    'prix_unitaire' => $prixUnit,
                    'sous_total'    => $sousTotal,
                ];
            }

            $montantHt  = round($montantHt, 2);
            $montantTtc = round($montantHt * (1 + $tauxTva / 100), 2);

            // Insertion commande
            $this->db->table('commandes')->insert([
                'numero'        => $numero,
                'client_id'     => $clientId,
                'statut'        => $statut,
                'date_commande' => $dateCommande->format('Y-m-d'),
                'montant_ht'    => $montantHt,
                'taux_tva'      => $tauxTva,
                'montant_ttc'   => $montantTtc,
                'notes'         => $faker->boolean(25)
                    ? $faker->sentence(rand(5, 12))
                    : null,
                'created_at'    => $dateCommande->format('Y-m-d H:i:s'),
                'updated_at'    => $dateCommande->modify('+' . rand(1, 48) . ' hours')->format('Y-m-d H:i:s'),
            ]);

            $commandeId = $this->db->insertID();

            // Insertion lignes
            foreach ($lignesData as $ligne) {
                $ligne['commande_id'] = $commandeId;
                $this->db->table('lignes_commande')->insert($ligne);
            }
        }

        echo "  40 commandes insérées avec leurs lignes.\n";
    }
}
