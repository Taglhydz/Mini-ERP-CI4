<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CommandeModel;

class EspaceClientController extends BaseController
{
    public function index(): string
    {
        $auth = session()->get('auth');

        $commandeModel = model(CommandeModel::class);

        $clientId = $auth['client_id'] ?? $auth['id'] ?? 0;

        $commandes = $commandeModel
            ->withClient()
            ->where('commandes.client_id', (int) $clientId)
            ->orderBy('commandes.id', 'DESC')
            ->findAll();

        return view('espace_client/index', [
            'titre'    => 'Mon espace',
            'commandes' => $commandes,
        ]);
    }
}
