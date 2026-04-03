<?php

declare(strict_types=1);

if (! function_exists('format_price')) {
    /**
     * Formate un montant en euros avec séparateur de milliers.
     *
     * @param float|int|string $amount
     * @return string  Ex : "1 234,56 €"
     */
    function format_price(float|int|string $amount): string
    {
        return number_format((float) $amount, 2, ',', ' ') . ' €';
    }
}

if (! function_exists('format_date')) {
    /**
     * Formate une date en chaîne lisible.
     *
     * @param string|null $date   Chaîne compatible strtotime()
     * @param string      $format Format PHP (par défaut : d/m/Y)
     * @return string
     */
    function format_date(?string $date, string $format = 'd/m/Y'): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        $ts = strtotime($date);

        return $ts !== false ? date($format, $ts) : '';
    }
}

if (! function_exists('truncate_text')) {
    /**
     * Tronque un texte à un nombre maximum de caractères.
     *
     * @param string $text
     * @param int    $max
     * @param string $suffix Suffixe ajouté si le texte est tronqué
     * @return string
     */
    function truncate_text(string $text, int $max = 120, string $suffix = '…'): string
    {
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return mb_substr($text, 0, $max) . $suffix;
    }
}
