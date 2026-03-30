<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'nom',
        'email',
        'telephone',
        'adresse',
        'ville',
        'code_postal',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'nom'   => 'required|min_length[2]|max_length[100]',
        'email' => 'required|valid_email|max_length[150]|is_unique[clients.email,id,{id}]',
        'telephone' => 'permit_empty|max_length[20]',
        'ville'     => 'permit_empty|max_length[100]',
        'code_postal' => 'permit_empty|max_length[10]',
    ];

    protected $validationMessages = [
        'nom'   => ['required' => 'Le nom est obligatoire.'],
        'email' => [
            'required'    => "L'email est obligatoire.",
            'valid_email' => "L'email n'est pas valide.",
            'is_unique'   => 'Cet email est déjà utilisé.',
        ],
    ];
}
