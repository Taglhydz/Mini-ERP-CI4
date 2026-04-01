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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    $('#table-products').DataTable({
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
            { data: 'stock',                className: 'text-center' },
            { data: 'actions',              orderable: false, className: 'text-center' },
        ],
        language: {
            url: '<?= base_url("assets/js/i18n/fr-FR.json") ?>',
        },
        order: [[1, 'asc']],
        columnDefs: [{ targets: 'no-sort', orderable: false }],
    });
});
</script>
<?= $this->endSection() ?>
