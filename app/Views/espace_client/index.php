<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Mon espace client</h1>
        <p class="text-muted mb-0 small">Bienvenue, <?= esc(session('auth')['username'] ?? '') ?></p>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent py-3">
        <h5 class="mb-0"><i class="bi bi-cart-fill me-2 text-primary"></i>Mes commandes</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($commandes)): ?>
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                Aucune commande pour l'instant.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th class="text-end">Montant TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $cmd): ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($cmd['id']) ?></td>
                                <td><?= esc(date('d/m/Y', strtotime($cmd['date_commande'] ?? $cmd['created_at']))) ?></td>
                                <td><?= $this->include('partials/badge_statut', ['statut' => $cmd['statut'] ?? 'en_attente']) ?></td>
                                <td class="text-end fw-semibold"><?= number_format((float) ($cmd['montant_ttc'] ?? 0), 2, ',', ' ') ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
