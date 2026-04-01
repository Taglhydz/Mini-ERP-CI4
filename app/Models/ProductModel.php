<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'company_id',
        'reference',
        'name',
        'description',
        'unit_price',
        'stock',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // La référence unique par entreprise est vérifiée au niveau applicatif
    // (index composite DB : UNIQUE(company_id, reference))
    protected $validationRules = [
        'reference'  => 'required|max_length[50]',
        'name'       => 'required|min_length[2]|max_length[200]',
        'unit_price' => 'required|decimal|greater_than_equal_to[0]',
        'stock'      => 'permit_empty|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'reference'   => ['required' => 'La référence est obligatoire.'],
        'name'        => ['required' => 'Le nom du produit est obligatoire.'],
        'unit_price'  => [
            'required' => 'Le prix unitaire est obligatoire.',
            'decimal'  => 'Le prix doit être un nombre décimal.',
        ],
    ];

    /**
     * Vérifie si une référence est unique pour une entreprise donnée.
     * Exclut l'enregistrement courant lors d'une mise à jour.
     */
    public function isReferenceUnique(string $reference, ?int $companyId, int $excludeId = 0): bool
    {
        $builder = $this->where('reference', $reference)
                        ->where('company_id', $companyId)
                        ->where('deleted_at', null);

        if ($excludeId > 0) {
            $builder = $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}
