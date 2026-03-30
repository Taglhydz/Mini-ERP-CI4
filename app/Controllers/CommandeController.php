<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\CommandeModel;
use App\Models\LigneCommandeModel;
use App\Models\ProduitModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class CommandeController extends BaseController
{
    protected CommandeModel     $commandeModel;
    protected LigneCommandeModel $ligneModel;

    public function __construct()
    {
        $this->commandeModel = model(CommandeModel::class);
        $this->ligneModel    = model(LigneCommandeModel::class);
    }

    // ─── Liste ───────────────────────────────────────────────────────────────

    public function index(): string
    {
        return view('commandes/index', ['titre' => 'Commandes']);
    }

    public function ajax(): ResponseInterface
    {
        $request = $this->request;

        $draw   = (int) $request->getPost('draw');
        $start  = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');
        $search = $request->getPost('search')['value'] ?? '';

        $columns = ['commandes.numero', 'clients.nom', 'commandes.date_commande',
                    'commandes.montant_ht', 'commandes.montant_ttc', 'commandes.statut'];

        $orderColIndex = (int) ($request->getPost('order')[0]['column'] ?? 0);
        $orderDir      = strtoupper($request->getPost('order')[0]['dir'] ?? 'DESC');
        $orderDir      = in_array($orderDir, ['ASC', 'DESC']) ? $orderDir : 'DESC';
        $orderCol      = $columns[$orderColIndex] ?? 'commandes.id';

        $builder = $this->commandeModel->db->table('commandes')
            ->select('commandes.*, clients.nom AS client_nom')
            ->join('clients', 'clients.id = commandes.client_id')
            ->where('commandes.deleted_at', null);

        if ($search !== '') {
            $builder->groupStart()
                ->like('commandes.numero', $search)
                ->orLike('clients.nom', $search)
                ->groupEnd();
        }

        $total    = (clone $builder)->countAllResults(false);
        $filtered = $builder->countAllResults(false);

        $rows = $builder->orderBy($orderCol, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $data = array_map(function (array $row): array {
            $row['montant_ht']  = number_format((float) $row['montant_ht'], 2, ',', ' ') . ' €';
            $row['montant_ttc'] = number_format((float) $row['montant_ttc'], 2, ',', ' ') . ' €';
            $row['statut']      = view('partials/badge_statut', ['statut' => $row['statut']]);
            $row['actions']     = sprintf(
                '<a href="%s" class="btn btn-sm btn-outline-secondary me-1" title="Voir"><i class="bi bi-eye"></i></a>'
                . '<a href="%s" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bi bi-pencil"></i></a>'
                . '<a href="%s" class="btn btn-sm btn-outline-info me-1" title="PDF"><i class="bi bi-file-pdf"></i></a>'
                . '<form action="%s" method="post" class="d-inline">'
                . csrf_field()
                . '<button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" data-confirm="Supprimer la commande &laquo;%s&raquo; ?"><i class="bi bi-trash"></i></button>'
                . '</form>',
                base_url('commandes/' . $row['id']),
                base_url('commandes/' . $row['id'] . '/edit'),
                base_url('commandes/' . $row['id'] . '/pdf'),
                base_url('commandes/' . $row['id'] . '/delete'),
                htmlspecialchars($row['numero'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
            );

            return $row;
        }, $rows);

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    // ─── Détail ──────────────────────────────────────────────────────────────

    public function show(int $id): string
    {
        $commande = $this->commandeModel->withClient()->where('commandes.id', $id)->first();
        if (! $commande) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $lignes = $this->ligneModel->getByCommande($id);

        return view('commandes/show', [
            'titre'    => 'Commande ' . $commande['numero'],
            'commande' => $commande,
            'lignes'   => $lignes,
        ]);
    }

    // ─── Création ────────────────────────────────────────────────────────────

    public function create(): string
    {
        return view('commandes/form', [
            'titre'   => 'Nouvelle commande',
            'clients' => model(ClientModel::class)->findAll(),
            'produits' => model(ProduitModel::class)->findAll(),
        ]);
    }

    public function store(): RedirectResponse
    {
        $post    = $this->request->getPost();
        $lignes  = $this->parseLignes($post);
        $montantHt  = array_sum(array_column($lignes, 'sous_total'));
        $tauxTva    = (float) ($post['taux_tva'] ?? 20);
        $montantTtc = round($montantHt * (1 + $tauxTva / 100), 2);

        $data = [
            'numero'         => $this->commandeModel->genererNumero(),
            'client_id'      => (int) $post['client_id'],
            'statut'         => $post['statut'] ?? 'brouillon',
            'date_commande'  => $post['date_commande'],
            'montant_ht'     => $montantHt,
            'taux_tva'       => $tauxTva,
            'montant_ttc'    => $montantTtc,
            'notes'          => $post['notes'] ?? null,
        ];

        if (! $this->commandeModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->commandeModel->errors());
        }

        $commandeId = $this->commandeModel->getInsertID();
        $this->ligneModel->syncLignes($commandeId, $lignes);

        return redirect()->to(base_url('commandes/' . $commandeId))
            ->with('success', 'Commande créée avec succès.');
    }

    // ─── Édition ─────────────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $commande = $this->commandeModel->findOrFail($id);
        $lignes   = $this->ligneModel->getByCommande($id);

        return view('commandes/form', [
            'titre'    => 'Modifier la commande',
            'commande' => $commande,
            'lignes'   => $lignes,
            'clients'  => model(ClientModel::class)->findAll(),
            'produits' => model(ProduitModel::class)->findAll(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $this->commandeModel->findOrFail($id);
        $post       = $this->request->getPost();
        $lignes     = $this->parseLignes($post);
        $montantHt  = array_sum(array_column($lignes, 'sous_total'));
        $tauxTva    = (float) ($post['taux_tva'] ?? 20);
        $montantTtc = round($montantHt * (1 + $tauxTva / 100), 2);

        $data = [
            'client_id'      => (int) $post['client_id'],
            'statut'         => $post['statut'],
            'date_commande'  => $post['date_commande'],
            'montant_ht'     => $montantHt,
            'taux_tva'       => $tauxTva,
            'montant_ttc'    => $montantTtc,
            'notes'          => $post['notes'] ?? null,
        ];

        if (! $this->commandeModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->commandeModel->errors());
        }

        $this->ligneModel->syncLignes($id, $lignes);

        return redirect()->to(base_url('commandes/' . $id))
            ->with('success', 'Commande mise à jour.');
    }

    // ─── Suppression ─────────────────────────────────────────────────────────

    public function delete(int $id): RedirectResponse
    {
        $this->commandeModel->findOrFail($id);
        $this->commandeModel->delete($id);

        return redirect()->to(base_url('commandes'))->with('success', 'Commande supprimée.');
    }

    // ─── Génération PDF ──────────────────────────────────────────────────────

    public function pdf(int $id): ResponseInterface
    {
        $commande = $this->commandeModel->withClient()->where('commandes.id', $id)->first();
        if (! $commande) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $lignes = $this->ligneModel->getByCommande($id);
        $html   = view('commandes/pdf_facture', ['commande' => $commande, 'lignes' => $lignes]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'facture-' . $commande['numero'] . '.pdf';
        $output   = $dompdf->output();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($output);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function parseLignes(array $post): array
    {
        $lignes      = [];
        $produitIds  = $post['produit_id']    ?? [];
        $designations = $post['designation']  ?? [];
        $quantites   = $post['quantite']      ?? [];
        $prix        = $post['prix_unitaire'] ?? [];

        foreach ($produitIds as $i => $produitId) {
            if (empty($produitId)) {
                continue;
            }

            $qte        = max(1, (int) ($quantites[$i] ?? 1));
            $prixUnit   = max(0, (float) ($prix[$i] ?? 0));

            $lignes[] = [
                'produit_id'    => (int) $produitId,
                'designation'   => $designations[$i] ?? '',
                'quantite'      => $qte,
                'prix_unitaire' => $prixUnit,
                'sous_total'    => round($qte * $prixUnit, 2),
            ];
        }

        return $lignes;
    }
}
