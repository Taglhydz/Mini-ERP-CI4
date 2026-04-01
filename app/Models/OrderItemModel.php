<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'order_id',
        'product_id',
        'name',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'order_id'   => 'required|integer',
        'product_id' => 'permit_empty|integer',
        'name'       => 'required|max_length[200]',
        'quantity'   => 'required|integer|greater_than[0]',
        'unit_price' => 'required|decimal|greater_than_equal_to[0]',
        'subtotal'   => 'required|decimal|greater_than_equal_to[0]',
    ];

    /**
     * Retourne les lignes d'une commande.
     */
    public function getByOrder(int $orderId): array
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    /**
     * Supprime toutes les lignes d'une commande et les recrée.
     */
    public function syncItems(int $orderId, array $items): void
    {
        $this->where('order_id', $orderId)->delete();

        foreach ($items as $item) {
            $item['order_id'] = $orderId;
            $item['subtotal'] = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
            $this->insert($item);
        }
    }
}
