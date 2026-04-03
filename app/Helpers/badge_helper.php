<?php

declare(strict_types=1);

if (! function_exists('badge_status')) {
    /**
     * Retourne un badge Bootstrap coloré selon le statut d'une commande.
     *
     * @param string|null $status  'draft' | 'confirmed' | 'delivered' | 'cancelled'
     * @return string  HTML <span class="badge bg-X">Label</span>
     */
    function badge_status(?string $status): string
    {
        $colorMap = [
            'draft'     => 'secondary',
            'confirmed' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ];

        $labelMap = [
            'draft'     => 'Brouillon',
            'confirmed' => 'Confirmée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
        ];

        $key   = $status ?? '';
        $color = $colorMap[$key] ?? 'secondary';
        $label = $labelMap[$key] ?? esc($key);

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }
}

if (! function_exists('badge_stock')) {
    /**
     * Retourne un badge Bootstrap coloré selon la quantité en stock.
     *
     * @param int    $qty      Quantité en stock
     * @param string $context  'admin' (affiche la quantité) | 'front' (affiche un texte client)
     * @return string  HTML <span class="badge bg-X">...</span>
     */
    function badge_stock(int $qty, string $context = 'admin'): string
    {
        if ($qty === 0) {
            $color      = 'danger';
            $extraClass = '';
            $label      = $context === 'front' ? 'Rupture de stock' : '0';
        } elseif ($qty <= 5) {
            $color      = 'warning';
            $extraClass = ' text-dark';
            $label      = $context === 'front' ? 'Plus que ' . $qty . ' en stock' : (string) $qty;
        } else {
            $color      = 'success';
            $extraClass = '';
            $label      = $context === 'front' ? 'En stock' : (string) $qty;
        }

        return '<span class="badge bg-' . $color . $extraClass . '">' . $label . '</span>';
    }
}

if (! function_exists('badge_role')) {
    /**
     * Retourne un badge Bootstrap coloré selon le rôle d'un utilisateur.
     *
     * @param string|null $role  'admin' | 'manager' | 'client'
     * @return string  HTML <span class="badge bg-X">Label</span>
     */
    function badge_role(?string $role): string
    {
        $colorMap = [
            'admin'   => 'danger',
            'manager' => 'primary',
            'client'  => 'secondary',
        ];

        $labelMap = [
            'admin'   => 'Admin',
            'manager' => 'Manager',
            'client'  => 'Client',
        ];

        $key   = $role ?? '';
        $color = $colorMap[$key] ?? 'secondary';
        $label = $labelMap[$key] ?? esc($key);

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }
}
