<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CommandeModel extends Model
{
    protected $table            = 'commandes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'numero',
        'client_id',
        'statut',
        'date_commande',
        'montant_ht',
        'taux_tva',
        'montant_ttc',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'client_id'      => 'required|integer',
        'date_commande'  => 'required|valid_date[Y-m-d]',
        'statut'         => 'required|in_list[brouillon,confirmee,livree,annulee]',
        'montant_ht'     => 'permit_empty|decimal',
        'taux_tva'       => 'permit_empty|decimal',
        'montant_ttc'    => 'permit_empty|decimal',
    ];

    /**
     * Retourne les commandes avec les informations client jointes.
     */
    public function withClient(): static
    {
        return $this->select('commandes.*, clients.nom AS client_nom, clients.email AS client_email')
                    ->join('clients', 'clients.id = commandes.client_id');
    }

    /**
     * Génère un numéro de commande unique (ex: CMD-2026-0001).
     */
    public function genererNumero(): string
    {
        $annee  = date('Y');
        $prefix = "CMD-{$annee}-";

        $derniere = $this->db->table('commandes')
            ->like('numero', $prefix, 'after')
            ->orderBy('numero', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $sequence = $derniere ? (int) substr($derniere['numero'], strlen($prefix)) + 1 : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
