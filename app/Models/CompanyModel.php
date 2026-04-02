<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table            = 'companies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'name',
        'slug',
        'email',
        'phone',
        'city',
        'postal_code',
        'logo_path',
        'cover_path',
        'show_name',
        'color_primary',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[150]',
        'slug' => 'required|max_length[100]|is_unique[companies.slug,id,{id}]|alpha_dash',
    ];

    protected $validationMessages = [
        'name' => ['required' => 'Le nom de l\'entreprise est obligatoire.'],
        'slug' => [
            'required'   => 'Le slug est obligatoire.',
            'is_unique'  => 'Ce slug est déjà utilisé.',
            'alpha_dash' => 'Le slug ne peut contenir que des lettres, chiffres et tirets.',
        ],
    ];
}
