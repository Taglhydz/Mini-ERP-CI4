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
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->has('auth')) {
            $token = $request->getCookie('auth_token');
            if ($token) {
                $userData = $this->resolveToken($token);
                if ($userData) {
                    session()->set('auth', $userData);
                    return null;
                }
            }

            session()->setFlashdata('error', 'Veuillez vous connecter pour continuer.');
            return redirect()->to('auth/login');
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

        return [
            'id' => $user['id'],
            'username' => $user['username'] ?? $user['email'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
    }
}
