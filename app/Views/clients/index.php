<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-people me-2 text-primary"></i>Clients</h1>
        <p class="text-muted small mb-0">Gestion du portefeuille clients</p>
    </div>
    <a href="<?= base_url('clients/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau client
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="table-clients" class="table table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Ville</th>
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
    $('#table-clients').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '<?= base_url('clients/ajax') ?>',
            type: 'POST',
        },
        columns: [
            { data: 'id',         width: '60px' },
            { data: 'nom' },
            { data: 'email' },
            { data: 'telephone',  defaultContent: '—' },
            { data: 'ville',      defaultContent: '—' },
            { data: 'actions',    orderable: false, className: 'text-center' },
        ],
        language: {
            url: '<?= base_url("assets/js/i18n/fr-FR.json") ?>',
        },
        order: [[0, 'desc']],
        columnDefs: [{ targets: 'no-sort', orderable: false }],
    });
});
</script>
<?= $this->endSection() ?>
