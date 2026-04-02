<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script>(function(){var t=localStorage.getItem('erp-theme')||'light';document.documentElement.setAttribute('data-bs-theme',t);})();</script>
    <title><?= esc($titre ?? 'Mini-ERP') ?> — Mini-ERP</title>

    <link rel="preload" href="<?= base_url('assets/css/bootstrap.min.css') ?>?v=5.3.8" as="style" onload="this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>?v=5.3.8"></noscript>
    <link rel="preload" href="<?= base_url('assets/fonts/bootstrap-icons.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>?v=1.13.1" as="style" onload="this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>?v=1.13.1"></noscript>

    <?= $this->renderSection('styles') ?>
    <style>
        /* ── Variable hauteur header (mise à jour par header.js) ── */
        :root { --header-height: 64px; }

        .navbar-public {
            background: linear-gradient(135deg, #0d6efd 0%, #0a4fb4 100%);
            border-bottom: 3px solid rgba(255,255,255,.15);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transform: translateY(0);
            transition: transform .3s ease;
        }
        .navbar-public.navbar-hidden {
            transform: translateY(-100%);
        }
        .navbar-public .navbar-brand {
            font-size: 1.2rem;
            letter-spacing: .03em;
        }
        body > main {
            /* Compense la navbar fixe — mis à jour dynamiquement par header.js */
            padding-top: var(--header-height);
        }
        .footer-public {
            background: var(--bs-secondary-bg);
            border-top: 1px solid var(--bs-border-color);
            font-size: .82rem;
            color: var(--bs-secondary-color);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<?php
$isLoggedIn   = session()->get('isLoggedIn');
$sessionRole  = session()->get('role');
$sessionUser  = session()->get('username');
$companySlug  = session()->get('company_slug');
$cartSlug     = session()->get('current_company_slug');
$cartCount    = $cartSlug ? count(session()->get('cart') ?? []) : 0;

if (! $isLoggedIn) {
    $logoHref = base_url('/');
} elseif ($sessionRole === 'client' && $companySlug) {
    $logoHref = base_url('shop/' . $companySlug . '/catalog');
} else {
    $logoHref = base_url('admin/dashboard');
}
?>

<nav id="site-navbar" class="navbar navbar-expand-lg navbar-dark navbar-public shadow">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= $logoHref ?>">
            <span class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center"
                  style="width:34px;height:34px;">
                <i class="bi bi-box-seam text-white"></i>
            </span>
            Mini-ERP
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navPublic">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navPublic">
            <div class="ms-auto d-flex align-items-center gap-2">
                <?php if ($cartSlug): ?>
                <a id="nav-cart-btn"
                   href="<?= base_url('shop/' . $cartSlug . '/cart') ?>"
                   class="btn btn-sm btn-outline-light position-relative"
                   title="Mon panier">
                    <i class="bi bi-cart2"></i>
                    <?php if ($cartCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="font-size:.6rem;min-width:1.2em;padding:.2em .4em;">
                        <?= $cartCount ?>
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                <?php if (! $isLoggedIn): ?>
                    <a href="<?= base_url('login') ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-door-open me-1"></i>Connexion
                    </a>
                <?php elseif ($sessionRole === 'client'): ?>
                    <span class="text-white-50 small d-none d-md-inline"><?= esc($sessionUser) ?></span>
                    <?php if ($companySlug): ?>
                    <a href="<?= base_url('espace-client') ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-person-circle me-1"></i>Mon compte
                    </a>
                    <?php endif; ?>
                    <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                    </a>
                <?php else: ?>
                    <span class="text-white-50 small d-none d-md-inline"><?= esc($sessionUser) ?></span>
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-light text-primary fw-semibold">
                        <i class="bi bi-speedometer2 me-1"></i>Gestion
                    </a>
                    <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                    </a>
                <?php endif; ?>

                <button id="btn-theme-toggle" type="button"
                        class="btn btn-sm border-0" title="Basculer thème"
                        aria-label="Basculer thème clair / sombre">
                    <span class="theme-switch" role="presentation"
                          style="width:46px;height:24px;border-radius:999px;background:rgba(255,255,255,.18);position:relative;display:inline-flex;align-items:center;justify-content:space-between;padding:0 6px;">
                        <i class="bi bi-sun-fill text-white" style="font-size:11px;opacity:.7;"></i>
                        <i class="bi bi-moon-fill text-white" style="font-size:11px;opacity:.4;"></i>
                        <span style="position:absolute;top:2px;left:3px;width:20px;height:20px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.25);transition:transform .3s;"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Toast container -->
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1100;"></div>

<main class="flex-grow-1">
    <?= $this->renderSection('content') ?>
</main>

<footer class="footer-public py-3 mt-auto">
    <div class="container text-center">
        &copy; <?= date('Y') ?> Mini-ERP &mdash; Tous droits réservés.
    </div>
</footer>

<script src="<?= base_url('assets/js/jquery.min.js') ?>?v=4.0.0"></script>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>?v=5.3.8"></script>
<script src="<?= base_url('assets/js/toasts.js') ?>?v=1.0"></script>
<script src="<?= base_url('assets/js/header.js') ?>?v=1.0"></script>

<?= $this->renderSection('scripts') ?>

<script>
(function () {
    var root   = document.documentElement;
    var toggle = document.getElementById('btn-theme-toggle');
    if (toggle) {
        toggle.addEventListener('click', function () {
            var next = (root.getAttribute('data-bs-theme') || 'light') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-bs-theme', next);
            localStorage.setItem('erp-theme', next);
        });
    }
})();
</script>

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
