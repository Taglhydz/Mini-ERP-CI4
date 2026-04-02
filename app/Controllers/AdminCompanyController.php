<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\OrderModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class AdminCompanyController extends BaseController
{
    protected CompanyModel $companyModel;

    public function __construct()
    {
        $this->companyModel = model(CompanyModel::class);
    }

    // ─── Liste ────────────────────────────────────────────────────────────────

    public function index(): string
    {
        // Toutes les entreprises, y compris les soft-deleted (désactivées)
        $companies = $this->companyModel->withDeleted()->findAll();

        $userModel    = model(UserModel::class);
        $productModel = model(ProductModel::class);
        $orderModel   = model(OrderModel::class);

        // Enrichir avec stats rapides
        foreach ($companies as &$company) {
            $cid = (int) $company['id'];
            $company['_clients']  = $userModel->where('company_id', $cid)->where('role', 'client')->countAllResults();
            $company['_products'] = $productModel->builder()->where('company_id', $cid)->countAllResults();
            $company['_orders']   = $orderModel->builder()->where('company_id', $cid)->countAllResults();
        }
        unset($company);

        return view('admin/companies/index', [
            'titre'     => 'Gestion des boutiques',
            'companies' => $companies,
        ]);
    }

    // ─── Création ─────────────────────────────────────────────────────────────

    public function create(): string
    {
        return view('admin/companies/form', [
            'titre'   => 'Nouvelle boutique',
            'company' => null,
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function store(): RedirectResponse
    {
        $data = $this->request->getPost(['name', 'slug', 'email', 'phone', 'city', 'postal_code', 'color_primary', 'color_secondary', 'show_name']);

        $data['show_name']       = $data['show_name'] === '1' ? 1 : 0;
        $data['color_primary']   = $this->sanitizeColor($data['color_primary'], '#0d6efd');
        $data['color_secondary'] = $this->sanitizeColor($data['color_secondary'], '#6c757d');

        if (! $this->companyModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->companyModel->errors());
        }

        return redirect()->to(base_url('admin/companies'))
            ->with('success', 'Boutique créée avec succès.');
    }

    // ─── Édition ──────────────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $company = $this->companyModel->withDeleted()->find($id);
        if (! $company) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/companies/form', [
            'titre'   => 'Modifier la boutique',
            'company' => $company,
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $company = $this->companyModel->withDeleted()->find($id);
        if (! $company) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->request->getPost(['name', 'slug', 'email', 'phone', 'city', 'postal_code', 'color_primary', 'color_secondary', 'show_name']);

        $data['show_name']       = $data['show_name'] === '1' ? 1 : 0;
        $data['color_primary']   = $this->sanitizeColor($data['color_primary'], $company['color_primary'] ?? '#0d6efd');
        $data['color_secondary'] = $this->sanitizeColor($data['color_secondary'], $company['color_secondary'] ?? '#6c757d');

        if (! $this->companyModel->skipValidation(false)->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->companyModel->errors());
        }

        return redirect()->to(base_url('admin/companies'))
            ->with('success', 'Boutique mise à jour.');
    }

    // ─── Suppression (soft-delete = désactivation) ────────────────────────────

    public function delete(int $id): ResponseInterface
    {
        $company = $this->companyModel->withDeleted()->find($id);
        if (! $company) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Boutique introuvable.']);
        }

        if ($company['deleted_at'] !== null) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Cette boutique est déjà désactivée.']);
        }

        $this->companyModel->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Boutique désactivée.']);
    }

    // ─── Activation / Désactivation ──────────────────────────────────────────

    public function toggle(int $id): ResponseInterface
    {
        $company = $this->companyModel->withDeleted()->find($id);
        if (! $company) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Boutique introuvable.']);
        }

        if ($company['deleted_at'] !== null) {
            // Réactiver
            $this->companyModel->db->table('companies')
                ->where('id', $id)
                ->update(['deleted_at' => null]);
            return $this->response->setJSON(['success' => true, 'active' => true, 'message' => 'Boutique réactivée.']);
        } else {
            // Désactiver
            $this->companyModel->delete($id);
            return $this->response->setJSON(['success' => true, 'active' => false, 'message' => 'Boutique désactivée.']);
        }
    }

    // ─── Helpers privés ───────────────────────────────────────────────────────

    private function sanitizeColor(mixed $value, string $default): string
    {
        $value = (string) $value;
        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $default;
    }
}
