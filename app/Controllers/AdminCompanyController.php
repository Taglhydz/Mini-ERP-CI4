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
        $data = $this->request->getPost(['name', 'email', 'phone', 'city', 'postal_code', 'color_primary', 'color_secondary', 'show_name']);

        $data['slug']            = $this->generateUniqueSlug((string) $data['name']);
        $data['show_name']       = $data['show_name'] === '1' ? 1 : 0;
        $data['color_primary']   = sanitize_hex_color($data['color_primary'], '#0d6efd');
        $data['color_secondary'] = sanitize_hex_color($data['color_secondary'], '#6c757d');

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

        $data = $this->request->getPost(['name', 'email', 'phone', 'city', 'postal_code', 'color_primary', 'color_secondary', 'show_name']);

        $data['slug']            = $this->generateUniqueSlug((string) $data['name'], $id);
        $data['show_name']       = $data['show_name'] === '1' ? 1 : 0;
        $data['color_primary']   = sanitize_hex_color($data['color_primary'], $company['color_primary'] ?? '#0d6efd');
        $data['color_secondary'] = sanitize_hex_color($data['color_secondary'], $company['color_secondary'] ?? '#6c757d');

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


    private function slugify(string $text): string
    {
        $map = [
            'à'=>'a','â'=>'a','ä'=>'a','á'=>'a','ã'=>'a','å'=>'a',
            'è'=>'e','ê'=>'e','ë'=>'e','é'=>'e',
            'ì'=>'i','î'=>'i','ï'=>'i','í'=>'i',
            'ò'=>'o','ô'=>'o','ö'=>'o','ó'=>'o','õ'=>'o','ø'=>'o',
            'ù'=>'u','û'=>'u','ü'=>'u','ú'=>'u',
            'ç'=>'c','ñ'=>'n','ý'=>'y','ÿ'=>'y',
            'œ'=>'oe','æ'=>'ae','ß'=>'ss',
            'À'=>'a','Â'=>'a','Ä'=>'a','Á'=>'a','Ã'=>'a','Å'=>'a',
            'È'=>'e','Ê'=>'e','Ë'=>'e','É'=>'e',
            'Ì'=>'i','Î'=>'i','Ï'=>'i','Í'=>'i',
            'Ò'=>'o','Ô'=>'o','Ö'=>'o','Ó'=>'o','Õ'=>'o','Ø'=>'o',
            'Ù'=>'u','Û'=>'u','Ü'=>'u','Ú'=>'u',
            'Ç'=>'c','Ñ'=>'n','Ý'=>'y',
            'Œ'=>'oe','Æ'=>'ae',
            "'"=>'-',
        ];
        $text = strtr($text, $map);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        $text = preg_replace('/-{2,}/', '-', $text);
        return substr($text ?: 'boutique', 0, 80);
    }

    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = $this->slugify($name);
        $slug = $base;
        $i    = 2;
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $builder = $this->companyModel->db->table('companies')->where('slug', $slug);
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    // ─── Suppression logo / cover ─────────────────────────────────────────────

    public function removeLogo(int $id): RedirectResponse
    {
        $company = $this->companyModel->withDeleted()->find($id);
        if (! $company) {
            return redirect()->back()->with('error', 'Boutique introuvable.');
        }
        $this->deleteCompanyFile($company['logo_path'] ?? null);
        $this->companyModel->update($id, ['logo_path' => null]);
        return redirect()->back()->with('success', 'Logo supprimé.');
    }

    public function removeCover(int $id): RedirectResponse
    {
        $company = $this->companyModel->withDeleted()->find($id);
        if (! $company) {
            return redirect()->back()->with('error', 'Boutique introuvable.');
        }
        $this->deleteCompanyFile($company['cover_path'] ?? null);
        $this->companyModel->update($id, ['cover_path' => null]);
        return redirect()->back()->with('success', 'Image de fond supprimée.');
    }

    private function deleteCompanyFile(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }
        $uploadBase = realpath(FCPATH . 'uploads/companies');
        $filePath   = realpath(FCPATH . ltrim($relativePath, '/'));
        if ($filePath && $uploadBase && str_starts_with($filePath, $uploadBase . DIRECTORY_SEPARATOR)) {
            @unlink($filePath);
        }
    }
}
