<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-box me-2 text-success"></i>Produits</h1>
        <p class="text-muted small mb-0">Catalogue produits et stocks</p>
    </div>
    <a href="<?= base_url('produits/create') ?>" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i>Nouveau produit
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="table-produits" class="table table-hover w-100">
            <thead class="table-light">
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
    $('#table-produits').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '<?= base_url('produits/ajax') ?>',
            type: 'POST',
        },
        columns: [
            { data: 'reference' },
            { data: 'designation' },
            { data: 'prix_unitaire', className: 'text-end' },
            { data: 'stock',        className: 'text-center' },
            { data: 'actions',      orderable: false, className: 'text-center' },
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/fr-FR.json',
        },
        order: [[1, 'asc']],
        columnDefs: [{ targets: 'no-sort', orderable: false }],
    });
});
</script>
<?= $this->endSection() ?>
