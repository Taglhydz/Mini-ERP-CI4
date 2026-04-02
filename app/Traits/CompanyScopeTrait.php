<?php

declare(strict_types=1);

namespace App\Traits;

trait CompanyScopeTrait
{
    /**
     * Retourne le company_id de session pour un manager, null pour un admin.
     * Un admin voit toutes les entreprises (pas de filtre).
     */
    protected function getCompanyId(): ?int
    {
        if (session()->get('role') === 'manager') {
            $id = (int) session()->get('company_id');
            return $id > 0 ? $id : null;
        }

        return null;
    }

    protected function isAdmin(): bool
    {
        return session()->get('role') === 'admin';
    }

    /**
     * Lève une exception 404 si l'utilisateur courant (manager) n'appartient pas
     * à la même entreprise que la ressource demandée.
     * Les admins passent toujours.
     */
    protected function assertCompanyAccess(int $resourceCompanyId): void
    {
        if ($this->isAdmin()) {
            return;
        }

        if ($this->getCompanyId() !== $resourceCompanyId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
