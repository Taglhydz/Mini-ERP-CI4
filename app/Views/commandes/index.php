<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-cart me-2 text-warning"></i>Commandes</h1>
        <p class="text-muted small mb-0">Suivi et gestion des commandes</p>
    </div>
    <a href="<?= base_url('commandes/create') ?>" class="btn btn-warning text-white">
        <i class="bi bi-plus-lg me-1"></i>Nouvelle commande
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="table-commandes" class="table table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th>Numéro</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Montant HT</th>
                    <th>Montant TTC</th>
                    <th>Statut</th>
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
    $('#table-commandes').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '<?= base_url('commandes/ajax') ?>',
            type: 'POST',
        },
        columns: [
            { data: 'numero' },
            { data: 'client_nom' },
            { data: 'date_commande' },
            { data: 'montant_ht',  className: 'text-end' },
            { data: 'montant_ttc', className: 'text-end' },
            { data: 'statut' },
            { data: 'actions',     orderable: false, className: 'text-center' },
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
