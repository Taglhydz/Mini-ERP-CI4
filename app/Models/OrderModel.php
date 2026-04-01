<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'company_id',
        'user_id',
        'number',
        'status',
        'order_date',
        'amount_ht',
        'vat_rate',
        'amount_ttc',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'user_id'    => 'required|integer',
        'order_date' => 'required|valid_date[Y-m-d]',
        'status'     => 'required|in_list[draft,confirmed,delivered,cancelled]',
        'amount_ht'  => 'permit_empty|decimal',
        'vat_rate'   => 'permit_empty|decimal',
        'amount_ttc' => 'permit_empty|decimal',
    ];

    /**
     * Retourne les commandes avec les informations utilisateur jointes.
     * Inclut aussi les champs d'adresse pour le PDF de facture.
     */
    public function withUser(): static
    {
        return $this->select("orders.*, 
            CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) AS user_name,
            users.email AS user_email,
            users.street_number,
            users.street_type,
            users.street_name,
            users.city AS user_city,
            users.postal_code AS user_postal_code")
            ->join('users', 'users.id = orders.user_id', 'left');
    }

    /**
     * Génère un numéro de commande unique (ex: ORD-2026-0001).
     */
    public function generateNumber(): string
    {
        $year   = date('Y');
        $prefix = "ORD-{$year}-";

        $last = $this->db->table('orders')
            ->like('number', $prefix, 'after')
            ->orderBy('number', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $seq = $last ? (int) substr($last['number'], strlen($prefix)) + 1 : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
