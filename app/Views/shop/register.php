<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">

            <div class="text-center mb-4">
                <div class="rounded-3 bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:60px;height:60px;">
                    <i class="bi bi-person-plus fs-3 text-primary"></i>
                </div>
                <h1 class="h4 fw-bold mb-1">Créer un compte</h1>
                <p class="text-muted mb-0">
                    Boutique&nbsp;: <strong><?= esc($company['name']) ?></strong>
                </p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <?php if (! empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $message): ?>
                                    <li><?= esc($message) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post"
                          action="<?= base_url('shop/' . esc($company['slug']) . '/register') ?>">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control"
                                       value="<?= esc(old('first_name')) ?>" required autofocus>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control"
                                       value="<?= esc(old('last_name')) ?>" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= esc(old('email')) ?>" required autocomplete="email">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Mot de passe <span class="text-danger">*</span>
                                    <small class="text-muted fw-normal">(8 min.)</small>
                                </label>
                                <input type="password" name="password" class="form-control" required
                                       autocomplete="new-password">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Confirmer <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirm" class="form-control" required
                                       autocomplete="new-password">
                            </div>

                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-person-check me-1"></i>Créer mon compte
                                </button>
                            </div>
                        </div>
                    </form>

                    <p class="text-center text-muted small mt-3 mb-0">
                        Déjà un compte ?
                        <a href="<?= base_url('login') . '?shop=' . urlencode($company['slug']) ?>">Se connecter</a>
                    </p>
                </div>
            </div>

            <p class="text-center mt-3 small">
                <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>" class="text-muted">
                    <i class="bi bi-arrow-left me-1"></i>Retour au catalogue
                </a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
