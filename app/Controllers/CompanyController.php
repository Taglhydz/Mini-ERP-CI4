<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CompanyModel;
use CodeIgniter\HTTP\RedirectResponse;

class CompanyController extends BaseController
{
    // ─── Paramètres boutique (images + thème) ────────────────────────────────

    public function settings(): string
    {
        $companyId = (int) session()->get('company_id');
        $company   = model(CompanyModel::class)->find($companyId);

        if (! $company) {
            return redirect()->to(base_url('admin/dashboard'))
                ->with('error', 'Entreprise introuvable.');
        }

        return view('admin/company/settings', [
            'titre'   => 'Paramètres de la boutique',
            'company' => $company,
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function updateSettings(): RedirectResponse
    {
        $companyId = (int) session()->get('company_id');
        $company   = model(CompanyModel::class)->find($companyId);

        if (! $company) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        $updateData = [];
        $uploadDir  = FCPATH . 'uploads/companies/' . $companyId . '/';

        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // ── Logo ──────────────────────────────────────────────────────────────
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && ! $logo->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (! in_array($logo->getMimeType(), $allowedTypes, true)) {
                return redirect()->back()->with('errors', ['logo' => 'Format invalide. Utilisez JPG, PNG ou WebP.']);
            }
            if ($logo->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->with('errors', ['logo' => 'Le logo ne doit pas dépasser 2 Mo.']);
            }
            $this->deleteOldFiles($uploadDir, 'logo');
            $logo->move($uploadDir, 'logo.' . $logo->getExtension());
            $updateData['logo_path'] = 'uploads/companies/' . $companyId . '/logo.' . $logo->getExtension();
        }

        // ── Couverture ────────────────────────────────────────────────────────
        $cover = $this->request->getFile('cover');
        if ($cover && $cover->isValid() && ! $cover->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (! in_array($cover->getMimeType(), $allowedTypes, true)) {
                return redirect()->back()->with('errors', ['cover' => 'Format invalide. Utilisez JPG, PNG ou WebP.']);
            }
            if ($cover->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->with('errors', ['cover' => 'L\'image de fond ne doit pas dépasser 5 Mo.']);
            }
            $this->deleteOldFiles($uploadDir, 'cover');
            $cover->move($uploadDir, 'cover.' . $cover->getExtension());
            $updateData['cover_path'] = 'uploads/companies/' . $companyId . '/cover.' . $cover->getExtension();
        }

        // ── Thème : show_name + color_primary ─────────────────────────────────
        $updateData['show_name'] = $this->request->getPost('show_name') === '1' ? 1 : 0;

        $colorPrimary = (string) $this->request->getPost('color_primary');
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $colorPrimary)) {
            $updateData['color_primary'] = $colorPrimary;
        }

        $colorSecondary = (string) $this->request->getPost('color_secondary');
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $colorSecondary)) {
            $updateData['color_secondary'] = $colorSecondary;
        }

        model(CompanyModel::class)->update($companyId, $updateData);

        return redirect()->to(base_url('admin/company/settings'))
            ->with('success', 'Paramètres mis à jour avec succès.');
    }

    // ─── Suppression logo / cover (manager) ───────────────────────────────────

    public function removeLogo(): RedirectResponse
    {
        $companyId = (int) session()->get('company_id');
        $company   = model(CompanyModel::class)->find($companyId);
        if (! $company) {
            return redirect()->back()->with('error', 'Entreprise introuvable.');
        }
        $this->deleteOldFiles(FCPATH . 'uploads/companies/' . $companyId . '/', 'logo');
        model(CompanyModel::class)->update($companyId, ['logo_path' => null]);
        return redirect()->back()->with('success', 'Logo supprimé.');
    }

    public function removeCover(): RedirectResponse
    {
        $companyId = (int) session()->get('company_id');
        $company   = model(CompanyModel::class)->find($companyId);
        if (! $company) {
            return redirect()->back()->with('error', 'Entreprise introuvable.');
        }
        $this->deleteOldFiles(FCPATH . 'uploads/companies/' . $companyId . '/', 'cover');
        model(CompanyModel::class)->update($companyId, ['cover_path' => null]);
        return redirect()->back()->with('success', 'Image de fond supprimée.');
    }

    // ─── Supprime les anciens fichiers d'un type (logo.* ou cover.*) ──────────
    private function deleteOldFiles(string $dir, string $prefix): void
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $file = $dir . $prefix . '.' . $ext;
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }
}
