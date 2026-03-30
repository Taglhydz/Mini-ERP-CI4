<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titre ?? 'Mini-ERP') ?> — Mini-ERP</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables + Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">

    <?= $this->renderSection('styles') ?>
</head>
<body>

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

<!-- Contenu principal -->
<main class="container-fluid py-4 px-4">

    <!-- Messages flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>

<!-- Footer -->
<footer class="bg-light border-top text-center text-muted small py-3 mt-auto">
    Mini-ERP &copy; <?= date('Y') ?> — CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables + Bootstrap 5 -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
