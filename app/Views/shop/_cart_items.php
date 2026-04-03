<?php
/**
 * Fragment inclus dans l'offcanvas panier (rendu par CartController::buildCartHtml).
 * Variables : $cart (array), $slug (string), $stocks (array productId => int)
 */
$stocks ??= [];
?>
<?php if (empty($cart)): ?>
    <div class="text-center text-muted py-5">
        <i class="bi bi-cart-x fs-1 d-block mb-3 opacity-50"></i>
        <p class="mb-0">Votre panier est vide.</p>
    </div>
<?php else: ?>
    <?php foreach ($cart as $productId => $item):
        $currentStock = $stocks[(int)$productId] ?? PHP_INT_MAX;
        $hasIssue     = $currentStock < (int) $item['qty'];
    ?>
    <div class="cart-item d-flex align-items-start gap-3 py-3 border-bottom<?= $hasIssue ? ' border-warning' : '' ?>"
         data-product-id="<?= (int) $productId ?>">

        <div class="flex-grow-1 overflow-hidden">
            <p class="fw-semibold small mb-1 text-truncate"><?= esc($item['name']) ?></p>
            <p class="text-muted small mb-0">
                <?= format_price($item['price']) ?> / u
            </p>
            <?php if ($hasIssue): ?>
            <div class="stock-warning d-flex align-items-center gap-1 mt-1">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:.75rem;"></i>
                <span class="text-warning small fw-semibold">
                    <?php if ($currentStock === 0): ?>
                        Rupture de stock
                    <?php else: ?>
                        Stock : <?= (int) $currentStock ?> (commandé : <?= (int) $item['qty'] ?>)
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Contrôles quantité -->
        <div class="d-flex align-items-center gap-1 flex-shrink-0">
            <button type="button"
                    class="btn btn-sm btn-outline-secondary btn-qty-dec"
                    data-id="<?= (int) $productId ?>"
                    style="width:28px;height:28px;padding:0;line-height:1;">
                <i class="bi bi-dash" style="font-size:.8rem;"></i>
            </button>
            <span class="px-2 fw-semibold" style="min-width:24px;text-align:center;">
                <?= (int) $item['qty'] ?>
            </span>
            <button type="button"
                    class="btn btn-sm btn-outline-secondary btn-qty-inc"
                    data-id="<?= (int) $productId ?>"
                    style="width:28px;height:28px;padding:0;line-height:1;">
                <i class="bi bi-plus" style="font-size:.8rem;"></i>
            </button>
        </div>

        <!-- Sous-total + suppression -->
        <div class="text-end flex-shrink-0" style="min-width:60px;">
            <p class="fw-semibold small mb-1">
                <?= format_price((float) $item['qty'] * (float) $item['price']) ?>
            </p>
            <button type="button"
                    class="btn btn-link text-danger p-0 btn-cart-remove"
                    data-id="<?= (int) $productId ?>"
                    title="Retirer">
                <i class="bi bi-trash" style="font-size:.9rem;"></i>
            </button>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
