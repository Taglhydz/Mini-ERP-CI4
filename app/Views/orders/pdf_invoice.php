<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }

        .page { padding: 40px; }

        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left  { display: table-cell; width: 60%; vertical-align: top; }
        .header-right { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
        .company-name { font-size: 22px; font-weight: bold; color: #0d6efd; }
        .doc-title    { font-size: 18px; font-weight: bold; color: #555; margin-top: 8px; }
        .doc-meta     { font-size: 10px; color: #777; margin-top: 4px; }

        hr.divider { border: none; border-top: 2px solid #0d6efd; margin: 20px 0; }

        .addresses { display: table; width: 100%; margin-bottom: 25px; }
        .addr-block { display: table-cell; width: 50%; vertical-align: top; padding: 12px; }
        .addr-block.from { background: #f8f9fa; }
        .addr-block.to   { background: #e7f1ff; }
        .addr-label { font-weight: bold; font-size: 9px; text-transform: uppercase;
                      letter-spacing: 0.5px; color: #888; margin-bottom: 5px; }
        .addr-name  { font-size: 13px; font-weight: bold; }

        .lines-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .lines-table thead tr { background: #0d6efd; color: #fff; }
        .lines-table th { padding: 8px 10px; text-align: left; font-size: 10px; }
        .lines-table td { padding: 7px 10px; border-bottom: 1px solid #e9ecef; }
        .lines-table tbody tr:nth-child(even) { background: #f8f9fa; }
        .text-right { text-align: right; }

        .totals { float: right; width: 260px; margin-top: 10px; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { padding: 5px 8px; }
        .totals .total-ht  { font-weight: bold; }
        .totals .total-ttc { background: #0d6efd; color: #fff; font-size: 13px; font-weight: bold; }

        .footer { margin-top: 60px; font-size: 9px; color: #aaa; text-align: center;
                  border-top: 1px solid #e9ecef; padding-top: 10px; }
    </style>
</head>
<body>
<div class="page">

    <!-- En-tête -->
    <div class="header">
        <div class="header-left">
            <div class="company-name">Mini-ERP</div>
            <div style="font-size:10px; color:#777; margin-top:4px;">
                123 Rue de l'Entreprise — 75001 Paris<br>
                contact@mini-erp.fr — 01 23 45 67 89
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">FACTURE</div>
            <div class="doc-meta">
                N° <?= esc($order['number']) ?><br>
                Date : <?= format_date($order['order_date']) ?>
            </div>
        </div>
    </div>

    <hr class="divider">

    <!-- Adresses -->
    <div class="addresses">
        <div class="addr-block from">
            <div class="addr-label">Émetteur</div>
            <div class="addr-name">Mini-ERP SARL</div>
            <div>123 Rue de l'Entreprise</div>
            <div>75001 Paris</div>
        </div>
        <div class="addr-block to">
            <div class="addr-label">Destinataire</div>
            <div class="addr-name"><?= esc($order['user_name']) ?></div>
            <?php
            $streetParts = array_filter([
                $order['street_number'] ?? '',
                $order['street_type']   ?? '',
                $order['street_name']   ?? '',
            ]);
            if ($streetParts): ?>
                <div><?= esc(implode(' ', $streetParts)) ?></div>
            <?php endif; ?>
            <?php if (! empty($order['user_city'])): ?>
                <div><?= esc(($order['user_postal_code'] ?? '') . ' ' . $order['user_city']) ?></div>
            <?php endif; ?>
            <div><?= esc($order['user_email']) ?></div>
        </div>
    </div>

    <!-- Lignes de commande -->
    <table class="lines-table">
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Prix HT</th>
                <th class="text-right">Sous-total HT</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= esc($item['name']) ?></td>
                <td class="text-right"><?= esc($item['quantity']) ?></td>
                <td class="text-right"><?= format_price($item['unit_price']) ?></td>
                <td class="text-right"><?= format_price($item['subtotal']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totaux -->
    <div class="totals">
        <table>
            <tr class="total-ht">
                <td>Total HT</td>
                <td class="text-right"><?= format_price($order['amount_ht']) ?></td>
            </tr>
            <tr>
                <td>TVA (<?= esc($order['vat_rate']) ?>%)</td>
                <td class="text-right">
                    <?= format_price((float)$order['amount_ttc'] - (float)$order['amount_ht']) ?>
                </td>
            </tr>
            <tr class="total-ttc">
                <td>Total TTC</td>
                <td class="text-right"><?= format_price($order['amount_ttc']) ?></td>
            </tr>
        </table>
    </div>

    <?php if (! empty($order['notes'])): ?>
        <div style="clear:both; margin-top:30px; padding:10px; background:#fffbee; border-left:3px solid #ffc107;">
            <strong>Notes :</strong><br><?= nl2br(esc($order['notes'])) ?>
        </div>
    <?php endif; ?>

    <div class="footer">
        Document généré le <?= date('d/m/Y à H:i') ?> — Mini-ERP
    </div>
</div>
</body>
</html>
