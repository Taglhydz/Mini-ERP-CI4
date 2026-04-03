<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-box me-2 text-success"></i>Produits</h1>
        <p class="text-muted small mb-0">Catalogue produits et stocks</p>
    </div>
    <a href="<?= base_url('products/create') ?>" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i>Nouveau produit
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="table-products" class="table table-hover w-100">
            <thead>
                <tr>
                    <th>Réf.</th>
                    <th>Désignation</th>
                    <th>Prix HT</th>
                    <th>Stock</th>
                    <th class="text-center no-sort">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- ── Modal réapprovisionnement (Option A — quantité à ajouter) ─────────── -->
<div class="modal fade" id="modal-restock" tabindex="-1" aria-labelledby="modal-restock-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modal-restock-label">
                    <i class="bi bi-plus-circle me-2 text-success"></i>Réapprovisionner
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="fw-semibold mb-1" id="modal-restock-name"></p>
                <p class="text-muted small mb-3">
                    Stock actuel&nbsp;: <span class="fw-bold text-dark" id="modal-restock-current"></span>
                </p>
                <label for="modal-restock-input" class="form-label fw-semibold">
                    Quantité à ajouter <span class="text-danger">*</span>
                </label>
                <input type="number" id="modal-restock-input" class="form-control"
                       min="1" step="1" value="1" required>
                <p class="mt-2 mb-0 small text-muted">
                    Nouveau stock&nbsp;: <span class="fw-bold text-success" id="modal-restock-preview"></span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success btn-sm" id="modal-restock-save">
                    <i class="bi bi-plus-circle me-1"></i>Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    var table = $('#table-products').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '<?= base_url('products/ajax') ?>',
            type: 'POST',
        },
        columns: [
            { data: 'reference' },
            { data: 'name' },
            { data: 'unit_price_formatted', className: 'text-end' },
            { data: 'stock_badge',          className: 'text-center', orderable: true },
            { data: 'actions',              orderable: false, className: 'text-center' },
        ],
        language: {
            url: '<?= base_url("assets/js/i18n/fr-FR.json") ?>',
        },
        order: [[1, 'asc']],
        columnDefs: [{ targets: 'no-sort', orderable: false }],
    });

    // ── Modal réapprovisionnement (Option A — quantité à ajouter) ──────────────
    var currentRestockId    = null;
    var currentRestockStock = 0;
    var $restockInput       = $('#modal-restock-input');
    var $restockPreview     = $('#modal-restock-preview');
    var modalEl             = document.getElementById('modal-restock');
    var bsModal             = modalEl ? new bootstrap.Modal(modalEl) : null;

    $('#table-products').on('click', '.btn-restock', function () {
        var $btn            = $(this);
        currentRestockId    = parseInt($btn.data('id'),    10);
        currentRestockStock = parseInt($btn.data('stock'), 10);
        var name            = String($btn.data('name') || '');

        if (isNaN(currentRestockId)    || currentRestockId <= 0)   { return; }
        if (isNaN(currentRestockStock) || currentRestockStock < 0) { currentRestockStock = 0; }

        $('#modal-restock-name').text(name);
        $('#modal-restock-current').text(currentRestockStock);
        $restockInput.val(1);
        $restockPreview.text(currentRestockStock + 1);

        if (bsModal) { bsModal.show(); }
    });

    $restockInput.on('input', function () {
        var addQty = parseInt($(this).val(), 10);
        if (!isNaN(addQty) && addQty >= 1) {
            $restockPreview
                .text(currentRestockStock + addQty)
                .removeClass('text-danger').addClass('text-success');
        } else {
            $restockPreview
                .text('—')
                .removeClass('text-success').addClass('text-danger');
        }
    });

    $('#modal-restock-save').on('click', function () {
        if (!currentRestockId) { return; }

        var addQty = parseInt($restockInput.val(), 10);
        if (isNaN(addQty) || addQty < 1) {
            showToast('Veuillez saisir une quantité valide (≥ 1).', 'danger');
            return;
        }

        var csrfName  = $('meta[name="csrf-name"]').attr('content');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (!csrfName || !csrfToken) {
            showToast('Erreur CSRF, veuillez recharger la page.', 'danger');
            return;
        }

        var data        = {};
        data[csrfName]  = csrfToken;
        data['qty_add'] = addQty;

        $.ajax({
            url:  '<?= base_url('products/') ?>' + currentRestockId + '/restock',
            type: 'POST',
            data: data,
            success: function (res) {
                if (res.success) {
                    if (bsModal) { bsModal.hide(); }
                    table.ajax.reload(null, false);
                    showToast('Stock mis à jour\u00a0!', 'success');
                } else {
                    showToast(res.message || 'Erreur lors de la mise à jour.', 'danger');
                }
            },
            error: function () {
                showToast('Erreur réseau.', 'danger');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
