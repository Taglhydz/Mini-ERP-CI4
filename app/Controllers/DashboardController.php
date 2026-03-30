<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\CommandeModel;
use App\Models\ProduitModel;
use CodeIgniter\HTTP\RedirectResponse;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $clientModel   = model(ClientModel::class);
        $produitModel  = model(ProduitModel::class);
        $commandeModel = model(CommandeModel::class);

        $stats = [
            'clients'  => $clientModel->countAllResults(),
            'produits' => $produitModel->countAllResults(),
            'commandes' => $commandeModel->countAllResults(),
            'ca_total'  => (float) ($commandeModel->selectSum('montant_ht')->get()->getRow()->montant_ht ?? 0),
        ];

        $dernieres_commandes = $commandeModel->withClient()
            ->orderBy('commandes.id', 'DESC')
            ->limit(5)
            ->findAll();

        return view('dashboard/index', [
            'titre'               => 'Tableau de bord',
            'stats'               => $stats,
            'dernieres_commandes' => $dernieres_commandes,
        ]);
    }
}
