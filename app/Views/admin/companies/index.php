<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0"><i class="bi bi-buildings-fill me-2 text-primary"></i>Boutiques</h2>
        <p class="text-muted small mb-0">Gestion de toutes les boutiques enregistrées.</p>
    </div>
    <a href="<?= base_url('admin/companies/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouvelle boutique
    </a>
</div>

<div class="card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:50px"></th>
                        <th>Nom / Slug</th>
                        <th>Couleurs</th>
                        <th class="text-center">Clients</th>
                        <th class="text-center">Produits</th>
                        <th class="text-center">Commandes</th>
                        <th class="text-center">État</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($companies)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-shop-window fs-1 d-block mb-2 opacity-25"></i>
                            Aucune boutique enregistrée.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($companies as $c): ?>
                    <?php $active = $c['deleted_at'] === null; ?>
                    <tr class="<?= $active ? '' : 'opacity-50' ?>">
                        <!-- Logo -->
                        <td class="ps-3">
                            <?php if (! empty($c['logo_path'])): ?>
                            <img src="<?= base_url(esc($c['logo_path'])) ?>"
                                 alt="Logo"
                                 class="rounded-circle border"
                                 style="width:38px;height:38px;object-fit:contain;">
                            <?php else: ?>
                            <div class="rounded-circle border bg-light d-flex align-items-center justify-content-center"
                                 style="width:38px;height:38px;">
                                <i class="bi bi-shop text-secondary small"></i>
                            </div>
                            <?php endif; ?>
                        </td>

                        <!-- Nom + slug -->
                        <td>
                            <div class="fw-semibold"><?= esc($c['name']) ?></div>
                            <div class="text-muted small font-monospace"><?= esc($c['slug']) ?></div>
                        </td>

                        <!-- Couleurs -->
                        <td>
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="rounded-circle border shadow-sm"
                                      style="width:20px;height:20px;background:<?= esc($c['color_primary'] ?? '#0d6efd') ?>;"
                                      title="Primaire : <?= esc($c['color_primary'] ?? '') ?>"></span>
                                <span class="rounded-circle border shadow-sm"
                                      style="width:20px;height:20px;background:<?= esc($c['color_secondary'] ?? '#6c757d') ?>;"
                                      title="Secondaire : <?= esc($c['color_secondary'] ?? '') ?>"></span>
                            </span>
                        </td>

                        <!-- Stats -->
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-normal px-2">
                                <?= (int) $c['_clients'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success fw-normal px-2">
                                <?= (int) $c['_products'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-normal px-2">
                                <?= (int) $c['_orders'] ?>
                            </span>
                        </td>

                        <!-- État -->
                        <td class="text-center">
                            <?php if ($active): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td class="text-end pe-3">
                            <a href="<?= base_url('shop/' . esc($c['slug']) . '/catalog') ?>"
                               class="btn btn-sm btn-outline-secondary me-1" title="Voir la boutique"
                               target="_blank" rel="noopener">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= base_url('admin/companies/' . $c['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary me-1" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-sm <?= $active ? 'btn-outline-warning' : 'btn-outline-success' ?> me-1 btn-toggle-company"
                                    title="<?= $active ? 'Désactiver' : 'Activer' ?>"
                                    data-id="<?= (int) $c['id'] ?>"
                                    data-url="<?= base_url('admin/companies/' . $c['id'] . '/toggle') ?>"
                                    data-active="<?= $active ? '1' : '0' ?>">
                                <i class="bi bi-<?= $active ? 'pause-circle' : 'play-circle' ?>"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Supprimer définitivement"
                                    data-confirm="Supprimer définitivement la boutique &laquo;<?= htmlspecialchars($c['name'], ENT_QUOTES | ENT_HTML5) ?>&raquo; ?"
                                    data-delete-url="<?= base_url('admin/companies/' . $c['id'] . '/delete') ?>"
                                    data-table="">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
/* ── Toggle activation boutique ─────────────────────────────────────── */
document.querySelectorAll('.btn-toggle-company').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var url    = btn.dataset.url;
        var active = btn.dataset.active === '1';

        if (! confirm((active ? 'Désactiver' : 'Activer') + ' cette boutique ?')) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({ '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(function () { location.reload(); }, 800);
            } else {
                showToast(data.message || 'Erreur.', 'error');
            }
        })
        .catch(function () { showToast('Erreur réseau.', 'error'); });
    });
});
</script>
<?= $this->endSection() ?>
