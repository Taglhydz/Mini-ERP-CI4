<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <?php if (! empty($shopName)): ?>
                        <!-- Contexte boutique -->
                        <div class="text-center mb-3">
                            <div class="rounded-3 bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2"
                                 style="width:52px;height:52px;">
                                <i class="bi bi-shop-window fs-3 text-primary"></i>
                            </div>
                            <h1 class="h4 fw-bold mb-0">Connexion</h1>
                            <p class="text-muted small mt-1 mb-0">
                                Boutique&nbsp;: <strong><?= esc($shopName) ?></strong>
                            </p>
                        </div>
                    <?php else: ?>
                        <!-- Contexte global (admin / manager) -->
                        <h1 class="h4 text-center mb-1">Connexion</h1>
                        <p class="text-center text-muted mb-4 small">
                            Managers et administrateurs : connectez-vous directement.<br>
                            Clients&nbsp;: <a href="<?= base_url('/') ?>">choisissez d'abord une boutique</a>.
                        </p>
                    <?php endif; ?>

                    <?php if (! empty($errors)): ?>
                        <div class="alert alert-danger mt-3">
                            <ul class="mb-0">
                                <?php foreach ($errors as $message): ?>
                                    <li><?= esc($message) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (! empty($errors['login']) && str_contains($errors['login'], 'boutique')): ?>
                                <div class="mt-2">
                                    <a href="<?= base_url('/') ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-house me-1"></i>Voir les boutiques
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('login') ?>" class="mt-3">
                        <?= csrf_field() ?>
                        <?php if (! empty($shopSlug)): ?>
                            <input type="hidden" name="shop_slug" value="<?= esc($shopSlug) ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   required autofocus value="<?= esc(old('email')) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    </form>

                    <div class="d-flex flex-column gap-2 mt-3">
                        <?php if (! empty($shopSlug)): ?>
                            <a href="<?= base_url('shop/' . esc($shopSlug) . '/register') ?>"
                               class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-person-plus me-1"></i>Pas de compte ? Créer un compte
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('/') ?>" class="btn btn-sm btn-link text-muted">
                            <i class="bi bi-arrow-left me-1"></i>Retour vers les boutiques
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
