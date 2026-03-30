<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ProduitModel extends Model
{
    protected $table            = 'produits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'reference',
        'designation',
        'description',
        'prix_unitaire',
        'stock',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'reference'     => 'required|max_length[50]|is_unique[produits.reference,id,{id}]',
        'designation'   => 'required|min_length[2]|max_length[200]',
        'prix_unitaire' => 'required|decimal|greater_than_equal_to[0]',
        'stock'         => 'permit_empty|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'reference'   => ['is_unique' => 'Cette référence est déjà utilisée.'],
        'designation' => ['required' => 'La désignation est obligatoire.'],
        'prix_unitaire' => [
            'required' => 'Le prix unitaire est obligatoire.',
            'decimal'  => 'Le prix doit être un nombre décimal.',
        ],
    ];
}
