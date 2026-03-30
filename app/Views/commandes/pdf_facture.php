<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }

        .page { padding: 40px; }

        /* En-tête */
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left  { display: table-cell; width: 60%; vertical-align: top; }
        .header-right { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
        .company-name { font-size: 22px; font-weight: bold; color: #0d6efd; }
        .doc-title    { font-size: 18px; font-weight: bold; color: #555; margin-top: 8px; }
        .doc-meta     { font-size: 10px; color: #777; margin-top: 4px; }

        /* Séparateur */
        hr.divider { border: none; border-top: 2px solid #0d6efd; margin: 20px 0; }

        /* Blocs adresses */
        .addresses { display: table; width: 100%; margin-bottom: 25px; }
        .addr-block { display: table-cell; width: 50%; vertical-align: top; padding: 12px; }
        .addr-block.from { background: #f8f9fa; }
        .addr-block.to   { background: #e7f1ff; }
        .addr-label { font-weight: bold; font-size: 9px; text-transform: uppercase;
                      letter-spacing: 0.5px; color: #888; margin-bottom: 5px; }
        .addr-name  { font-size: 13px; font-weight: bold; }

        /* Table lignes */
        .lines-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .lines-table thead tr { background: #0d6efd; color: #fff; }
        .lines-table th { padding: 8px 10px; text-align: left; font-size: 10px; }
        .lines-table td { padding: 7px 10px; border-bottom: 1px solid #e9ecef; }
        .lines-table tbody tr:nth-child(even) { background: #f8f9fa; }
        .text-right { text-align: right; }

        /* Totaux */
        .totals { float: right; width: 260px; margin-top: 10px; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { padding: 5px 8px; }
        .totals .total-ht  { font-weight: bold; }
        .totals .total-ttc { background: #0d6efd; color: #fff; font-size: 13px; font-weight: bold; }

        /* Pied de page */
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
                N° <?= esc($commande['numero']) ?><br>
                Date : <?= date('d/m/Y', strtotime($commande['date_commande'])) ?>
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
            <div class="addr-name"><?= esc($commande['client_nom']) ?></div>
            <?php if (! empty($commande['adresse'])): ?>
                <div><?= esc($commande['adresse']) ?></div>
            <?php endif; ?>
            <?php if (! empty($commande['code_postal']) || ! empty($commande['ville'])): ?>
                <div><?= esc($commande['code_postal'] ?? '') ?> <?= esc($commande['ville'] ?? '') ?></div>
            <?php endif; ?>
            <div><?= esc($commande['client_email']) ?></div>
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
        <?php foreach ($lignes as $ligne): ?>
            <tr>
                <td><?= esc($ligne['designation']) ?></td>
                <td class="text-right"><?= esc($ligne['quantite']) ?></td>
                <td class="text-right"><?= number_format((float)$ligne['prix_unitaire'], 2, ',', ' ') ?> €</td>
                <td class="text-right"><?= number_format((float)$ligne['sous_total'], 2, ',', ' ') ?> €</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totaux -->
    <div class="totals">
        <table>
            <tr class="total-ht">
                <td>Total HT</td>
                <td class="text-right"><?= number_format((float)$commande['montant_ht'], 2, ',', ' ') ?> €</td>
            </tr>
            <tr>
                <td>TVA (<?= esc($commande['taux_tva']) ?>%)</td>
                <td class="text-right">
                    <?= number_format((float)$commande['montant_ttc'] - (float)$commande['montant_ht'], 2, ',', ' ') ?> €
                </td>
            </tr>
            <tr class="total-ttc">
                <td>Total TTC</td>
                <td class="text-right"><?= number_format((float)$commande['montant_ttc'], 2, ',', ' ') ?> €</td>
            </tr>
        </table>
    </div>

    <?php if (! empty($commande['notes'])): ?>
        <div style="clear:both; margin-top:30px; padding:10px; background:#fffbee; border-left:3px solid #ffc107;">
            <strong>Notes :</strong><br><?= nl2br(esc($commande['notes'])) ?>
        </div>
    <?php endif; ?>

    <div class="footer">
        Document généré le <?= date('d/m/Y à H:i') ?> — Mini-ERP
    </div>
</div>
</body>
</html>
