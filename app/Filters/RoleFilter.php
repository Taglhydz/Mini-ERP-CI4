<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');

        // Admin bypass : accès complet à toutes les zones protégées
        if ($role === 'admin') {
            return null;
        }

        $allowed = [];
        if (! empty($arguments) && is_array($arguments)) {
            $allowed = array_filter(array_map('trim', explode(',', $arguments[0])));
        }

        if (empty($allowed)) {
            return null;
        }

        if (! in_array($role, $allowed, true)) {
            session()->setFlashdata('error', 'Accès refusé : vous n\'avez pas les droits requis.');

            return redirect()->to($role === 'client' ? 'espace-client' : '/');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing required.
    }
}
