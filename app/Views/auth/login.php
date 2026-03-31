<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-3">Connexion</h1>
                <p class="text-center text-muted mb-4">Accédez à votre espace Mini-ERP.</p>

                <?php if (! empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $message): ?>
                                <li><?= esc($message) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('auth/login') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required autofocus value="<?= old('email') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>

                <p class="text-center text-muted mt-3 mb-0">
                    Pas encore de compte ?
                    <a href="<?= base_url('auth/register') ?>">Créer un compte</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
