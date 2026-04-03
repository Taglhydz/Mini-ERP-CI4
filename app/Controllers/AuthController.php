<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\AuthTokenModel;
use App\Models\CompanyModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    protected UserModel $userModel;
    protected AuthTokenModel $authTokenModel;

    public function __construct()
    {
        $this->userModel      = model(UserModel::class);
        $this->authTokenModel = model(AuthTokenModel::class);
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectLoggedIn();
        }

        // ── Contexte boutique : GET ?shop=slug (affichage) ou POST shop_slug (soumission) ──
        $rawSlug     = trim((string) ($this->request->getGet('shop') ?? $this->request->getPost('shop_slug') ?? ''));
        $shopCompany = null;

        if ($rawSlug !== '') {
            $shopCompany = model(CompanyModel::class)
                ->where('slug', $rawSlug)
                ->where('deleted_at', null)
                ->first();
            // Slug inconnu → on le traite silencieusement comme absent
            if (! $shopCompany) {
                $rawSlug = '';
            }
        }

        if ($this->request->is('post')) {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $email    = (string) $this->request->getPost('email');
            $password = (string) $this->request->getPost('password');

            // ── 1. Admin / Manager : priorité absolue, aucune restriction company ──
            $staffUser = $this->userModel
                ->whereIn('role', ['admin', 'manager'])
                ->where('email', $email)
                ->where('deleted_at', null)
                ->first();

            if ($staffUser) {
                if (! password_verify($password, $staffUser['password_hash'])) {
                    return redirect()->back()->withInput()
                        ->with('errors', ['login' => 'Email ou mot de passe invalide.']);
                }
                return $this->loginUser($staffUser);
            }

            // ── 2. Client : contexte boutique obligatoire ──────────────────────
            $contextCompanyId = $shopCompany
                ? (int) $shopCompany['id']
                : ((int) session()->get('current_company_id') ?: null);

            if ($contextCompanyId === null) {
                return redirect()->back()->withInput()->with('errors', [
                    'login' => 'Vous devez accéder à une boutique pour vous connecter. '
                        . 'Veuillez choisir votre boutique depuis l\'accueil.',
                ]);
            }

            $clientUser = $this->userModel
                ->where('role', 'client')
                ->where('email', $email)
                ->where('company_id', $contextCompanyId)
                ->where('deleted_at', null)
                ->first();

            // Message identique que l'email soit absent ou appartienne à une autre boutique
            if (! $clientUser || ! password_verify($password, $clientUser['password_hash'])) {
                return redirect()->back()->withInput()
                    ->with('errors', ['login' => 'Email ou mot de passe invalide.']);
            }

            return $this->loginUser($clientUser);
        }

        return view('auth/login', [
            'titre'    => $shopCompany
                ? 'Connexion — ' . esc($shopCompany['name'])
                : 'Connexion',
            'errors'   => session()->getFlashdata('errors') ?? [],
            'shopSlug' => $rawSlug,
            'shopName' => $shopCompany['name'] ?? null,
        ]);
    }

    private function loginUser(array $user): RedirectResponse
    {
        $userId  = (int) $user['id'];
        $company = $user['company_id']
            ? model(CompanyModel::class)->find($user['company_id'])
            : null;

        session()->set([
            'isLoggedIn'   => true,
            'user_id'      => $userId,
            'username'     => $user['username'],
            'role'         => $user['role'],
            'company_id'   => $user['company_id'],
            'company_slug' => $company['slug'] ?? null,
            'company_name' => $company['name'] ?? null,
        ]);

        $this->authTokenModel->where('user_id', $userId)->delete();
        $tokenValue = $this->createToken($userId);
        $this->response->setCookie($this->buildTokenCookie($tokenValue));

        $successMsg    = 'Connexion réussie. Bienvenue, ' . esc($user['username']) . ' !';
        $redirectAfter = session()->get('redirect_after_login');
        $slug          = $company['slug'] ?? null;

        session()->remove('redirect_after_login');

        // Admin/manager : redirect_after_login utilisable librement
        if ($redirectAfter && in_array($user['role'], ['admin', 'manager'], true)) {
            return redirect()->to($redirectAfter)->with('success', $successMsg);
        }

        // Client : redirect_after_login utilisable uniquement dans sa boutique
        if ($redirectAfter && $user['role'] === 'client' && $slug !== null) {
            if (str_starts_with($redirectAfter, base_url('shop/' . $slug . '/'))) {
                return redirect()->to($redirectAfter)->with('success', $successMsg);
            }
        }

        return redirect()->to($this->redirectByRole($user['role'], $slug))
            ->with('success', $successMsg);
    }

    private function redirectLoggedIn(): RedirectResponse
    {
        return redirect()->to(
            $this->redirectByRole(
                (string) session()->get('role'),
                session()->get('company_slug')
            )
        );
    }

    private function redirectByRole(string $role, ?string $slug = null): string
    {
        return match ($role) {
            'admin', 'manager' => 'admin/dashboard',
            'client'           => 'shop/' . ($slug ?? '') . '/catalog',
            default            => '/',
        };
    }

    public function register()
    {
        // L'inscription clients se fait via /shop/{slug}/register
        return redirect()->to('/')
            ->with('info', 'Pour créer un compte client, choisissez d\'abord une boutique.');
    }

    public function logout(): RedirectResponse
    {
        $token = $this->request->getCookie('auth_token');
        if ($token && str_contains($token, ':')) {
            [$selector] = explode(':', $token, 2);
            $this->authTokenModel->where('selector', $selector)->delete();
        }
        $this->response->deleteCookie('auth_token');
        session()->destroy();

        return redirect()->to('/')->with('success', 'Déconnexion effectuée.');
    }

    private function createToken(int $userId): string
    {
        $selector  = bin2hex(random_bytes(9));
        $validator = bin2hex(random_bytes(32));
        $expires   = date('Y-m-d H:i:s', time() + $this->getTokenLifetime());

        $this->authTokenModel->insert([
            'user_id'        => $userId,
            'selector'       => $selector,
            'validator_hash' => password_hash($validator, PASSWORD_DEFAULT),
            'expires_at'     => $expires,
        ]);

        return $selector . ':' . $validator;
    }

    private function buildTokenCookie(string $value): array
    {
        return [
            'name'     => 'auth_token',
            'value'    => $value,
            'expire'   => $this->getTokenLifetime(),
            'httponly' => true,
            'samesite' => 'Lax',
        ];
    }

    private function getTokenLifetime(): int
    {
        return 60 * 60 * 24 * 7; // 7 jours
    }
}

