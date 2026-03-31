<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class AuthTokenModel extends Model
{
    protected $table = 'auth_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'selector', 'validator_hash', 'expires_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function purgeExpired(): void
    {
        $now = date('Y-m-d H:i:s');
        $this->where('expires_at <', $now)->delete();
    }
}
