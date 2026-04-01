<?php

declare(strict_types=1);

namespace App\Filters;

use App\Models\AuthTokenModel;
use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public const LOGIN_REQUIRED_MESSAGE = 'Veuillez vous connecter pour continuer.';

    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            $token = $request->getCookie('auth_token');
            if ($token) {
                $userData = $this->resolveToken($token);
                if ($userData) {
                    session()->set($userData);
                    return null;
                }
            }

            session()->setFlashdata('error', self::LOGIN_REQUIRED_MESSAGE);
            return redirect()->to('login');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing required.
    }

    private function resolveToken(string $cookie): ?array
    {
        if (! str_contains($cookie, ':')) {
            response()->deleteCookie('auth_token');
            return null;
        }

        [$selector, $validator] = explode(':', $cookie, 2);
        $tokenModel = model(AuthTokenModel::class);
        $tokenModel->purgeExpired();

        $token = $tokenModel->where('selector', $selector)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();

        if (! $token || ! password_verify($validator, $token['validator_hash'])) {
            if ($token) {
                $tokenModel->delete($token['id']);
            }
            response()->deleteCookie('auth_token');
            return null;
        }

        $userModel = model(UserModel::class);
        $user = $userModel->find($token['user_id']);
        if (! $user) {
            $tokenModel->delete($token['id']);
            response()->deleteCookie('auth_token');
            return null;
        }

        $company = $user['company_id']
            ? model(\App\Models\CompanyModel::class)->find($user['company_id'])
            : null;

        return [
            'isLoggedIn'   => true,
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'email'        => $user['email'],
            'role'         => $user['role'],
            'company_id'   => $user['company_id'],
            'company_slug' => $company['slug'] ?? null,
        ];
    }
}