<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titre ?? 'Mini-ERP') ?> — Mini-ERP</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>">
    <!-- DataTables + Bootstrap 5 -->
    <link rel="stylesheet" href="<?= base_url('assets/css/dataTables.bootstrap5.min.css') ?>">

    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Barre de navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
            <i class="bi bi-box-seam me-2"></i>Mini-ERP
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= (current_url(true)->getPath() === '/clients') ? 'active' : '' ?>"
                       href="<?= base_url('clients') ?>">
                        <i class="bi bi-people me-1"></i>Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (current_url(true)->getPath() === '/produits') ? 'active' : '' ?>"
                       href="<?= base_url('produits') ?>">
                        <i class="bi bi-box me-1"></i>Produits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (current_url(true)->getPath() === '/commandes') ? 'active' : '' ?>"
                       href="<?= base_url('commandes') ?>">
                        <i class="bi bi-cart me-1"></i>Commandes
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Conteneur de toasts (bas droite) -->
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1100;"></div>

<!-- Modale de confirmation suppression -->
<div class="modal fade" id="modal-confirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white border-0 pb-2">
                <h6 class="modal-title mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body py-3" id="modal-confirm-body">Confirmer cette action ?</div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Annuler
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-ok">
                    <i class="bi bi-trash me-1"></i>Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Contenu principal -->
<main class="container-fluid py-4 px-4 flex-grow-1">

    <?= $this->renderSection('content') ?>
</main>

<!-- Footer -->
<footer class="bg-light border-top text-center text-muted small py-3 mt-auto">
    Mini-ERP &copy; <?= date('Y') ?> — CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?>
</footer>

<!-- jQuery -->
<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
<!-- Bootstrap 5 JS -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!-- DataTables + Bootstrap 5 -->
<script src="<?= base_url('assets/js/dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap5.min.js') ?>"></script>
<!-- Toasts & confirm -->
<script src="<?= base_url('assets/js/toasts.js') ?>"></script>

<?= $this->renderSection('scripts') ?>

<?php $flashSuccess = session()->getFlashdata('success'); ?>
<?php $flashError   = session()->getFlashdata('error');   ?>
<?php if ($flashSuccess): ?>
<script>showToast('<?= esc($flashSuccess, 'js') ?>', 'success');</script>
<?php endif; ?>
<?php if ($flashError): ?>
<script>showToast('<?= esc($flashError, 'js') ?>', 'error');</script>
<?php endif; ?>
</body>
</html>
