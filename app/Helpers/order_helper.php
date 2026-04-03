<?php

declare(strict_types=1);

if (! function_exists('calc_item_subtotal')) {
    /**
     * Calcule le sous-total d'une ligne de commande.
     *
     * @param float|int|string $qty       Quantité
     * @param float|int|string $unitPrice Prix unitaire HT
     * @return float  Sous-total arrondi à 2 décimales
     */
    function calc_item_subtotal(float|int|string $qty, float|int|string $unitPrice): float
    {
        return round((float) $qty * (float) $unitPrice, 2);
    }
}

if (! function_exists('calc_order_totals')) {
    /**
     * Calcule les totaux HT, TVA et TTC d'une commande à partir de ses lignes.
     *
     * @param array<array{subtotal?: float|int|string, qty?: float|int|string, price?: float|int|string}> $items
     * @param float $vatRate  Taux de TVA en pourcentage (ex : 20 pour 20 %)
     * @return array{amount_ht: float, vat_rate: float, vat_amount: float, amount_ttc: float}
     */
    function calc_order_totals(array $items, float $vatRate = 20.0): array
    {
        // Supporte des items avec 'subtotal' précalculé, ou 'qty'+'price'
        $amountHt = 0.0;
        foreach ($items as $item) {
            if (isset($item['subtotal'])) {
                $amountHt += (float) $item['subtotal'];
            } else {
                $amountHt += calc_item_subtotal($item['qty'] ?? 0, $item['price'] ?? 0);
            }
        }

        $vatAmount = round($amountHt * $vatRate / 100, 2);
        $amountTtc = round($amountHt * (1 + $vatRate / 100), 2);

        return [
            'amount_ht'  => round($amountHt, 2),
            'vat_rate'   => $vatRate,
            'vat_amount' => $vatAmount,
            'amount_ttc' => $amountTtc,
        ];
    }
}
