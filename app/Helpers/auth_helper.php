<?php

declare(strict_types=1);

if (! function_exists('current_role')) {
    /**
     * Retourne le rôle de l'utilisateur connecté.
     *
     * @return string  'admin' | 'manager' | 'client' | ''
     */
    function current_role(): string
    {
        return (string) (session()->get('role') ?? '');
    }
}

if (! function_exists('is_admin')) {
    /**
     * Vérifie si l'utilisateur connecté est administrateur global.
     */
    function is_admin(): bool
    {
        return session()->get('role') === 'admin';
    }
}

if (! function_exists('is_manager')) {
    /**
     * Vérifie si l'utilisateur connecté est manager (back-office société).
     */
    function is_manager(): bool
    {
        return session()->get('role') === 'manager';
    }
}

if (! function_exists('is_client')) {
    /**
     * Vérifie si l'utilisateur connecté est un client (front-office).
     */
    function is_client(): bool
    {
        return session()->get('role') === 'client';
    }
}

if (! function_exists('is_logged_in')) {
    /**
     * Vérifie si un utilisateur est actuellement connecté.
     */
    function is_logged_in(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }
}

if (! function_exists('current_user')) {
    /**
     * Retourne les données de session de l'utilisateur connecté.
     *
     * @return array{id?: int, first_name?: string, last_name?: string, email?: string, role?: string, company_id?: int}
     */
    function current_user(): array
    {
        return [
            'id'         => (int) (session()->get('user_id') ?? 0),
            'first_name' => (string) (session()->get('first_name') ?? ''),
            'last_name'  => (string) (session()->get('last_name') ?? ''),
            'email'      => (string) (session()->get('email') ?? ''),
            'role'       => current_role(),
            'company_id' => (int) (session()->get('company_id') ?? 0),
        ];
    }
}
