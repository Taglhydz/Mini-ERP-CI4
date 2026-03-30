<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LigneCommandeModel extends Model
{
    protected $table            = 'lignes_commande';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'commande_id',
        'produit_id',
        'designation',
        'quantite',
        'prix_unitaire',
        'sous_total',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'commande_id'   => 'required|integer',
        'produit_id'    => 'required|integer',
        'designation'   => 'required|max_length[200]',
        'quantite'      => 'required|integer|greater_than[0]',
        'prix_unitaire' => 'required|decimal|greater_than_equal_to[0]',
        'sous_total'    => 'required|decimal|greater_than_equal_to[0]',
    ];

    /**
     * Retourne les lignes d'une commande avec le détail produit.
     */
    public function getByCommande(int $commandeId): array
    {
        return $this->where('commande_id', $commandeId)->findAll();
    }

    /**
     * Supprime toutes les lignes d'une commande et les recrée.
     */
    public function syncLignes(int $commandeId, array $lignes): void
    {
        $this->where('commande_id', $commandeId)->delete();

        foreach ($lignes as $ligne) {
            $ligne['commande_id'] = $commandeId;
            $ligne['sous_total']  = round($ligne['quantite'] * $ligne['prix_unitaire'], 2);
            $this->insert($ligne);
        }
    }
}
