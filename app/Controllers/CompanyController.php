<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CompanyModel;
use CodeIgniter\HTTP\RedirectResponse;

class CompanyController extends BaseController
{
    // ─── Paramètres boutique (logo + couverture) ──────────────────────────────

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

    public function updateImages(): RedirectResponse
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
                return redirect()->back()
                    ->with('errors', ['logo' => 'Format invalide. Utilisez JPG, PNG ou WebP.']);
            }
            if ($logo->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()
                    ->with('errors', ['logo' => 'Le logo ne doit pas dépasser 2 Mo.']);
            }

            $ext = $logo->getExtension();
            $this->deleteOldFiles($uploadDir, 'logo');
            $logo->move($uploadDir, 'logo.' . $ext);
            $updateData['logo_path'] = 'uploads/companies/' . $companyId . '/logo.' . $ext;
        }

        // ── Couverture ────────────────────────────────────────────────────────
        $cover = $this->request->getFile('cover');
        if ($cover && $cover->isValid() && ! $cover->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (! in_array($cover->getMimeType(), $allowedTypes, true)) {
                return redirect()->back()
                    ->with('errors', ['cover' => 'Format invalide. Utilisez JPG, PNG ou WebP.']);
            }
            if ($cover->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()
                    ->with('errors', ['cover' => 'L\'image de fond ne doit pas dépasser 5 Mo.']);
            }

            $ext = $cover->getExtension();
            $this->deleteOldFiles($uploadDir, 'cover');
            $cover->move($uploadDir, 'cover.' . $ext);
            $updateData['cover_path'] = 'uploads/companies/' . $companyId . '/cover.' . $ext;
        }

        if (! empty($updateData)) {
            model(CompanyModel::class)->update($companyId, $updateData);
            return redirect()->to(base_url('admin/company/settings'))
                ->with('success', 'Images mises à jour avec succès.');
        }

        return redirect()->to(base_url('admin/company/settings'))
            ->with('info', 'Aucune image sélectionnée.');
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
