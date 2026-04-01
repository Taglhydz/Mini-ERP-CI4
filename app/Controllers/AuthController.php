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
            return redirect()->to('/');
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

            $email     = (string) $this->request->getPost('email');
            $password  = (string) $this->request->getPost('password');
            $companyId = session()->get('current_company_id');

            if ($companyId) {
                // Chemin client : email + entreprise
                $user = $this->userModel
                    ->where('email', $email)
                    ->where('company_id', (int) $companyId)
                    ->first();
            } else {
                // Chemin admin / manager : pas de contexte boutique
                $user = $this->userModel
                    ->where('email', $email)
                    ->groupStart()
                        ->where('company_id', null)
                        ->orWhereIn('role', ['admin', 'manager'])
                    ->groupEnd()
                    ->first();
            }

            if (! $user || ! password_verify($password, $user['password_hash'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['login' => 'Email ou mot de passe invalide.']);
            }

            // Un client ne peut pas se connecter sans contexte boutique
            if ($user['role'] === 'client' && ! $companyId) {
                return redirect()->to('/')
                    ->with('error', 'Veuillez d\'abord choisir une boutique pour vous connecter.');
            }

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
            ]);

            $this->authTokenModel->where('user_id', $userId)->delete();
            $tokenValue = $this->createToken($userId);
            $this->response->setCookie($this->buildTokenCookie($tokenValue));

            return redirect()->to($this->redirectByRole($user['role'], $company['slug'] ?? null))
                ->with('success', 'Connexion réussie. Bienvenue, ' . esc($user['username']) . ' !');
        }

        return view('auth/login', [
            'titre'  => 'Connexion',
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
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

    private function redirectByRole(string $role, ?string $slug = null): string
    {
        return match ($role) {
            'client' => 'shop/' . ($slug ?? '') . '/catalog',
            default  => 'admin/dashboard',
        };
    }
}

