<?php
// Partiel réutilisable : badge coloré selon le statut d'une commande
$map = [
    'brouillon' => 'secondary',
    'confirmee' => 'primary',
    'livree'    => 'success',
    'annulee'   => 'danger',
];
$labels = [
    'brouillon' => 'Brouillon',
    'confirmee' => 'Confirmée',
    'livree'    => 'Livrée',
    'annulee'   => 'Annulée',
];
$color = $map[$statut ?? ''] ?? 'secondary';
$label = $labels[$statut ?? ''] ?? esc($statut ?? '');
?>
<span class="badge bg-<?= $color ?>"><?= $label ?></span>
