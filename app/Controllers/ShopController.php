<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class ShopController extends BaseController
{
    // ─── Catalogue public d'une boutique ─────────────────────────────────────

    public function catalog(string $slug): string
    {
        $company = $this->findCompanyOrFail($slug);

        // Mémoriser le contexte company en session (pour le login client)
        session()->set('current_company_id',   $company['id']);
        session()->set('current_company_slug', $company['slug']);

        $products = model(ProductModel::class)
            ->where('company_id', $company['id'])
            ->where('deleted_at', null)
            ->where('stock >', 0)
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('shop/catalog', [
            'titre'      => esc($company['name']) . ' — Catalogue',
            'company'    => $company,
            'products'   => $products,
            'isLoggedIn' => (bool) session()->get('isLoggedIn'),
            'username'   => session()->get('username'),
            'role'       => session()->get('role'),
            'cartCount'  => count(session()->get('cart') ?? []),
        ]);
    }

    // ─── Inscription client dans une boutique ────────────────────────────────

    public function register(string $slug): string|RedirectResponse
    {
        $company = $this->findCompanyOrFail($slug);

        // Un utilisateur déjà connecté n'a pas à s'inscrire
        if (session()->get('isLoggedIn')) {
            return redirect()->to('shop/' . $slug . '/catalog');
        }

        if ($this->request->is('post')) {
            $rules = [
                'first_name'       => 'required|min_length[2]|max_length[100]',
                'last_name'        => 'required|min_length[2]|max_length[100]',
                'email'            => 'required|valid_email|max_length[150]',
                'password'         => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $email = (string) $this->request->getPost('email');

            // Unicité email dans cette boutique
            $userModel = model(UserModel::class);
            if (! $userModel->isEmailAvailable($email, (int) $company['id'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['email' => 'Cette adresse email est déjà utilisée dans cette boutique.']);
            }

            $firstName = $this->request->getPost('first_name');
            $lastName  = $this->request->getPost('last_name');

            $newId = $userModel->insert([
                'username'   => trim($firstName . ' ' . $lastName),
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
                'password'   => $this->request->getPost('password'),
                'role'       => 'client',
                'company_id' => (int) $company['id'],
            ]);

            // ── Connexion automatique après inscription ────────────────────────
            session()->set([
                'isLoggedIn'   => true,
                'user_id'      => $newId,
                'username'     => trim($firstName . ' ' . $lastName),
                'role'         => 'client',
                'company_id'   => (int) $company['id'],
                'company_slug' => $company['slug'],
                'company_name' => $company['name'],
            ]);

            // ── Redirection : checkout si en cours, sinon catalogue ────────────
            $redirectAfter = session()->get('redirect_after_login');
            session()->remove('redirect_after_login');

            if ($redirectAfter
                && str_starts_with($redirectAfter, base_url('shop/' . $slug . '/'))
                && str_starts_with(parse_url($redirectAfter, PHP_URL_HOST) ?? '', parse_url(base_url(), PHP_URL_HOST))
            ) {
                return redirect()->to($redirectAfter)
                    ->with('success', 'Bienvenue ! Votre compte a été créé.');
            }

            return redirect()->to('shop/' . $slug . '/catalog')
                ->with('success', 'Bienvenue ! Votre compte a été créé. Vous pouvez maintenant passer commande.');
        }

        return view('shop/register', [
            'titre'      => 'Créer un compte — ' . esc($company['name']),
            'company'    => $company,
            'errors'     => session()->getFlashdata('errors') ?? [],
            'isLoggedIn' => false,
            'username'   => null,
            'role'       => null,
        ]);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function findCompanyOrFail(string $slug): array
    {
        $company = model(CompanyModel::class)
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        if (! $company) {
            throw PageNotFoundException::forPageNotFound('Boutique introuvable.');
        }

        return $company;
    }
}
