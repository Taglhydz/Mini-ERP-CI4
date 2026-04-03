<?php

declare(strict_types=1);

if (! function_exists('is_valid_hex_color')) {
    /**
     * Vérifie qu'une chaîne est une couleur hexadécimale valide à 6 chiffres (#RRGGBB).
     *
     * @param string $color
     * @return bool
     */
    function is_valid_hex_color(string $color): bool
    {
        return (bool) preg_match('/^#[0-9a-fA-F]{6}$/', $color);
    }
}

if (! function_exists('sanitize_hex_color')) {
    /**
     * Retourne la couleur si valide, sinon la valeur par défaut.
     *
     * @param string|null $color
     * @param string      $default  Couleur de repli (doit être un hex valide)
     * @return string
     */
    function sanitize_hex_color(?string $color, string $default = '#0d6efd'): string
    {
        if ($color !== null && is_valid_hex_color($color)) {
            return $color;
        }

        return $default;
    }
}

if (! function_exists('company_theme')) {
    /**
     * Extrait les données de thème visuel d'une société.
     *
     * Centralise le bloc répété dans catalog.php, checkout.php et espace_client/index.php.
     *
     * @param array<string, mixed> $company  Ligne de la table companies
     * @return array{
     *   hasCover: bool,
     *   hasLogo: bool,
     *   showName: bool,
     *   colorPrimary: string,
     *   colorSecondary: string
     * }
     */
    function company_theme(array $company): array
    {
        return [
            'hasCover'       => ! empty($company['cover_path']),
            'hasLogo'        => ! empty($company['logo_path']),
            'showName'       => isset($company['show_name']) ? (bool) $company['show_name'] : true,
            'colorPrimary'   => sanitize_hex_color($company['color_primary'] ?? null, '#0d6efd'),
            'colorSecondary' => sanitize_hex_color($company['color_secondary'] ?? null, '#ffffff'),
        ];
    }
}
