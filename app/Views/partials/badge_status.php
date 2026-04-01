<?php
// Partiel réutilisable : badge coloré selon le statut d'une commande
$map = [
    'draft'     => 'secondary',
    'confirmed' => 'primary',
    'delivered' => 'success',
    'cancelled' => 'danger',
];
$labels = [
    'draft'     => 'Brouillon',
    'confirmed' => 'Confirmée',
    'delivered' => 'Livrée',
    'cancelled' => 'Annulée',
];
$color = $map[$status ?? ''] ?? 'secondary';
$label = $labels[$status ?? ''] ?? esc($status ?? '');
?>
<span class="badge bg-<?= $color ?>"><?= $label ?></span>
