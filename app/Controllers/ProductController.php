<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class ProductController extends BaseController
{
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = model(ProductModel::class);
    }

    // ─── Liste (vue + DataTables Ajax) ──────────────────────────────────────

    public function index(): string
    {
        return view('products/index', ['titre' => 'Produits']);
    }

    public function ajax(): ResponseInterface
    {
        $draw   = (int) $this->request->getPost('draw');
        $start  = (int) $this->request->getPost('start');
        $length = (int) $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';

        $columns = ['reference', 'name', 'name', 'unit_price', 'stock'];

        $orderColIndex = (int) ($this->request->getPost('order')[0]['column'] ?? 1);
        $orderDir      = strtoupper($this->request->getPost('order')[0]['dir'] ?? 'ASC');
        $orderDir      = in_array($orderDir, ['ASC', 'DESC']) ? $orderDir : 'ASC';
        $orderCol      = $columns[$orderColIndex] ?? 'name';

        $companyId = $this->getCompanyId();

        $builder = $this->productModel->builder();
        $builder->where('deleted_at', null);

        if ($companyId !== null) {
            $builder->where('company_id', $companyId);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('reference', $search)
                ->orLike('name', $search)
                ->groupEnd();
        }

        $totalB = $this->productModel->builder()->where('deleted_at', null);
        if ($companyId !== null) {
            $totalB->where('company_id', $companyId);
        }
        $total    = $totalB->countAllResults();
        $filtered = $builder->countAllResults(false);

        $rows = $builder->orderBy($orderCol, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $data = array_map(function (array $row): array {
            $row['unit_price_formatted'] = number_format((float) $row['unit_price'], 2, ',', ' ') . ' €';
            $row['actions']              = sprintf(
                '<a href="%s" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bi bi-pencil"></i></a>'
                . '<button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer"'
                . ' data-confirm="Supprimer le produit &laquo;%s&raquo; ?"'
                . ' data-delete-url="%s" data-table="table-products"><i class="bi bi-trash"></i></button>',
                base_url('products/' . $row['id'] . '/edit'),
                htmlspecialchars($row['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                base_url('products/' . $row['id'] . '/delete')
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
        return view('products/form', ['titre' => 'Nouveau produit']);
    }

    public function store(): RedirectResponse
    {
        $data = $this->request->getPost(['reference', 'name', 'description', 'unit_price', 'stock']);

        $companyId = $this->getCompanyId();
        if ($companyId !== null) {
            $data['company_id'] = $companyId;
        }

        if (! $this->productModel->isReferenceUnique($data['reference'], $companyId)) {
            return redirect()->back()->withInput()
                ->with('errors', ['reference' => 'Cette référence est déjà utilisée.']);
        }

        if (! $this->productModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
        }

        return redirect()->to(base_url('products'))->with('success', 'Produit créé avec succès.');
    }

    // ─── Édition ─────────────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $product = $this->productModel->find($id);
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->assertCompanyAccess((int) ($product['company_id'] ?? 0));

        return view('products/form', ['titre' => 'Modifier le produit', 'product' => $product]);
    }

    public function update(int $id): RedirectResponse
    {
        $product = $this->productModel->find($id);
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->assertCompanyAccess((int) ($product['company_id'] ?? 0));

        $data = $this->request->getPost(['reference', 'name', 'description', 'unit_price', 'stock']);

        // Vérification unicité référence par entreprise (step 4 : filtrée par company_id)
        if (! $this->productModel->isReferenceUnique($data['reference'], $product['company_id'] ?? null, $id)) {
            return redirect()->back()->withInput()
                ->with('errors', ['reference' => 'Cette référence est déjà utilisée.']);
        }

        if (! $this->productModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
        }

        return redirect()->to(base_url('products'))->with('success', 'Produit mis à jour.');
    }

    // ─── Suppression ─────────────────────────────────────────────────────────

    public function delete(int $id): ResponseInterface
    {
        $product = $this->productModel->find($id);
        if (! $product) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Produit introuvable.']);
        }

        if (! $this->isAdmin() && $this->getCompanyId() !== (int) ($product['company_id'] ?? 0)) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Accès refusé.']);
        }

        $this->productModel->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Produit supprimé.']);
    }
}
