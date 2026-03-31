<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\ClientModel;
use App\Models\UserModel;

class ClientLinker
{
    protected ClientModel $clientModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->clientModel = model(ClientModel::class);
        $this->userModel   = model(UserModel::class);
    }

    public function ensureClientId(array $user): ?int
    {
        $clientId = $user['client_id'] ?? null;
        if (! empty($clientId)) {
            return (int) $clientId;
        }

        $client = $this->clientModel->where('email', $user['email'] ?? '')->first();
        if ($client) {
            $clientId = (int) $client['id'];
            $this->userModel->update($user['id'], ['client_id' => $clientId]);
            return $clientId;
        }

        if (($user['role'] ?? '') !== 'client') {
            return null;
        }

        [$prenom, $nom] = $this->splitUsername($user['username'] ?? $user['email'] ?? 'Client');

        $inserted = $this->clientModel->insert([
            'prenom' => $prenom,
            'nom'    => $nom,
            'email'  => $user['email'],
        ]);

        if (! $inserted) {
            return null;
        }

        $clientId = $this->clientModel->getInsertID();
        $this->userModel->update($user['id'], ['client_id' => $clientId]);

        return $clientId !== null ? (int) $clientId : null;
    }

    private function splitUsername(string $value): array
    {
        $value = trim($value);
        if ($value === '') {
            return ['Client', 'Inconnu'];
        }

        $parts = preg_split('/\s+/', $value, 2, PREG_SPLIT_NO_EMPTY);
        if (empty($parts)) {
            return ['Client', 'Inconnu'];
        }

        if (count($parts) === 1) {
            return [$parts[0], 'Client'];
        }

        return [$parts[0], $parts[1]];
    }
}
