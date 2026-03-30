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
        'prenom',
        'nom',
        'email',
        'telephone',
        'adresse_numero',
        'adresse_type_voie',
        'adresse_nom_voie',
        'ville',
        'code_postal',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'nom'               => 'required|min_length[2]|max_length[100]',
        'prenom'            => 'permit_empty|max_length[100]',
        'email'             => 'required|valid_email|max_length[150]|is_unique[clients.email,id,{id}]',
        'telephone'         => 'permit_empty|max_length[20]',
        'adresse_numero'    => 'permit_empty|max_length[10]',
        'adresse_type_voie' => 'permit_empty|max_length[50]',
        'adresse_nom_voie'  => 'permit_empty|max_length[200]',
        'ville'             => 'permit_empty|max_length[100]',
        'code_postal'       => 'permit_empty|max_length[10]',
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
