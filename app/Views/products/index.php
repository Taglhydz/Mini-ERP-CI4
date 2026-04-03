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

<!-- ── Modal mise à jour rapide du stock ──────────────────────────────────── -->
<div class="modal fade" id="modal-stock" tabindex="-1" aria-labelledby="modal-stock-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modal-stock-label">
                    <i class="bi bi-boxes me-2 text-success"></i>Modifier le stock
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3" id="modal-stock-product-name"></p>
                <label for="modal-stock-input" class="form-label fw-semibold">Nouvelle quantité</label>
                <input type="number" id="modal-stock-input" class="form-control"
                       min="0" step="1" value="0">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success btn-sm" id="modal-stock-save">
                    <i class="bi bi-floppy me-1"></i>Enregistrer
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

    // ── Modal mise à jour stock ───────────────────────────────────────────────
    var currentProductId = null;
    var modal            = new bootstrap.Modal(document.getElementById('modal-stock'));

    $('#table-products').on('click', '.btn-edit-stock', function () {
        currentProductId = $(this).data('id');
        var name         = $(this).data('name');
        var stock        = $(this).data('stock');
        $('#modal-stock-product-name').text(name);
        $('#modal-stock-input').val(stock);
        modal.show();
    });

    $('#modal-stock-save').on('click', function () {
        if (! currentProductId) return;
        var newStock = parseInt($('#modal-stock-input').val(), 10);
        if (isNaN(newStock) || newStock < 0) {
            showToast('Quantité invalide.', 'danger');
            return;
        }

        var csrfName  = $('meta[name="csrf-name"]').attr('content');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var data      = {};
        data[csrfName] = csrfToken;
        data['stock']  = newStock;

        $.ajax({
            url:  BASE_URL + 'products/' + currentProductId + '/stock',
            type: 'POST',
            data: data,
            success: function (res) {
                if (res.success) {
                    modal.hide();
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
