<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ProduitModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class ProduitController extends BaseController
{
    protected ProduitModel $produitModel;

    public function __construct()
    {
        $this->produitModel = model(ProduitModel::class);
    }

    // ─── Liste (vue + DataTables Ajax) ──────────────────────────────────────

    public function index(): string
    {
        return view('produits/index', ['titre' => 'Produits']);
    }

    public function ajax(): ResponseInterface
    {
        $request = $this->request;

        $draw   = (int) $request->getPost('draw');
        $start  = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');
        $search = $request->getPost('search')['value'] ?? '';

        $columns = ['reference', 'designation', 'designation', 'prix_unitaire', 'stock'];

        $orderColIndex = (int) ($request->getPost('order')[0]['column'] ?? 1);
        $orderDir      = strtoupper($request->getPost('order')[0]['dir'] ?? 'ASC');
        $orderDir      = in_array($orderDir, ['ASC', 'DESC']) ? $orderDir : 'ASC';
        $orderCol      = $columns[$orderColIndex] ?? 'designation';

        $builder = $this->produitModel->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('reference', $search)
                ->orLike('designation', $search)
                ->groupEnd();
        }

        $total    = $this->produitModel->countAllResults(false);
        $filtered = $builder->countAllResults(false);

        $rows = $builder->orderBy($orderCol, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $data = array_map(function (array $row): array {
            $row['prix_unitaire'] = number_format((float) $row['prix_unitaire'], 2, ',', ' ') . ' €';
            $row['actions']       = sprintf(
                '<a href="%s" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bi bi-pencil"></i></a>'
                . '<form action="%s" method="post" class="d-inline">'
                . csrf_field()
                . '<button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" data-confirm="Supprimer le produit &laquo;%s&raquo; ?"><i class="bi bi-trash"></i></button>'
                . '</form>',
                base_url('produits/' . $row['id'] . '/edit'),
                base_url('produits/' . $row['id'] . '/delete'),
                htmlspecialchars($row['designation'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
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

    // ─── Création ────────────────────────────────────────────────────────────

    public function create(): string
    {
        return view('produits/form', ['titre' => 'Nouveau produit']);
    }

    public function store(): RedirectResponse
    {
        $data = $this->request->getPost(['reference', 'designation', 'description', 'prix_unitaire', 'stock']);

        if (! $this->produitModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->produitModel->errors());
        }

        return redirect()->to(base_url('produits'))->with('success', 'Produit créé avec succès.');
    }

    // ─── Édition ─────────────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $produit = $this->produitModel->findOrFail($id);

        return view('produits/form', ['titre' => 'Modifier le produit', 'produit' => $produit]);
    }

    public function update(int $id): RedirectResponse
    {
        $this->produitModel->findOrFail($id);
        $data = $this->request->getPost(['reference', 'designation', 'description', 'prix_unitaire', 'stock']);

        $this->produitModel->setValidationRule('reference',
            "required|max_length[50]|is_unique[produits.reference,id,{$id}]");

        if (! $this->produitModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->produitModel->errors());
        }

        return redirect()->to(base_url('produits'))->with('success', 'Produit mis à jour.');
    }

    // ─── Suppression ─────────────────────────────────────────────────────────

    public function delete(int $id): RedirectResponse
    {
        $this->produitModel->findOrFail($id);
        $this->produitModel->delete($id);

        return redirect()->to(base_url('produits'))->with('success', 'Produit supprimé.');
    }
}
