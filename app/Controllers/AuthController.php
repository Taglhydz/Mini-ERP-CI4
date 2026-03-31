<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\ClientLinker;
use App\Models\AuthTokenModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    protected UserModel $userModel;
    protected AuthTokenModel $authTokenModel;
    protected ClientLinker $clientLinker;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        $this->authTokenModel = model(AuthTokenModel::class);
        $this->clientLinker = new ClientLinker();
    }

    public function login()
    {
        if (session()->has('auth')) {
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

            $email    = (string) $this->request->getPost('email');
            $password = (string) $this->request->getPost('password');

            $user = $this->userModel->where('email', $email)->first();

            if (! $user || ! password_verify($password, $user['password_hash'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['login' => 'Email ou mot de passe invalide.']);
            }

            $userId = (int) $user['id'];
            $clientId = $this->clientLinker->ensureClientId($user);

            session()->set('auth', [
                'id'        => $userId,
                'username'  => $user['username'] ?? $user['email'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'client_id' => $clientId,
            ]);

            $this->authTokenModel->where('user_id', $userId)->delete();
            $tokenValue = $this->createToken($userId);
            $this->response->setCookie($this->buildTokenCookie($tokenValue));

            return redirect()->to($this->redirectByRole($user['role']))
                ->with('success', 'Connexion réussie. Bienvenue, ' . esc($user['username']) . ' !');
        }

        return view('auth/login', [
            'titre'  => 'Connexion',
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function register()
    {
        if (session()->has('auth')) {
            return redirect()->to('/');
        }

        if ($this->request->is('post')) {
            $rules = [
                'username'         => 'required|min_length[3]|max_length[60]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]',
                'role'             => 'required|in_list[client,user]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $userData = [
                'username' => $this->request->getPost('username'),
                'email'    => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'role'     => $this->request->getPost('role'),
            ];

            $insertedId = (int) $this->userModel->insert($userData);
            if ($insertedId) {
                $insertedUser = $this->userModel->find($insertedId);
                if ($insertedUser) {
                    $this->clientLinker->ensureClientId($insertedUser);
                }
            }

            return redirect()->to('auth/login')
                ->with('success', 'Compte créé, vous pouvez maintenant vous connecter.');
        }

        return view('auth/register', [
            'titre'  => 'Créer un compte',
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function logout(): RedirectResponse
    {
        $token = $this->request->getCookie('auth_token');
        if ($token && str_contains($token, ':')) {
            [$selector] = explode(':', $token, 2);
            $this->authTokenModel->where('selector', $selector)->delete();
        }
        $this->response->deleteCookie('auth_token');
        session()->remove('auth');

        return redirect()->to('auth/login')->with('success', 'Déconnexion effectuée.');
    }

    private function createToken(int $userId): string
    {
        $selector = bin2hex(random_bytes(9));
        $validator = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + $this->getTokenLifetime());

        $this->authTokenModel->insert([
            'user_id' => $userId,
            'selector' => $selector,
            'validator_hash' => password_hash($validator, PASSWORD_DEFAULT),
            'expires_at' => $expires,
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

    private function redirectByRole(string $role): string
    {
        return match ($role) {
            'client' => 'espace-client',
            default  => '/',   // admin & user → back-office
        };
    }
}
