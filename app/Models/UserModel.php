<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',       // virtuel : converti en password_hash par le callback
        'password_hash',
        'role',
        'phone',
        'street_number',
        'street_type',
        'street_name',
        'city',
        'postal_code',
        'company_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (! empty($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
        }

        return $data;
    }

    /**
     * Vérifie si un email est disponible pour une entreprise donnée.
     * Exclut l'utilisateur courant lors d'une mise à jour.
     */
    public function isEmailAvailable(string $email, ?int $companyId, int $excludeId = 0): bool
    {
        $builder = $this->where('email', $email)
                        ->where('company_id', $companyId)
                        ->where('deleted_at', null);

        if ($excludeId > 0) {
            $builder = $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}
