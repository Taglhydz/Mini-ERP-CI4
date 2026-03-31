<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-3">Créer un compte</h1>
                <p class="text-center text-muted mb-4">Choisissez votre rôle et commencez à utiliser l'ERP.</p>

                <?php if (! empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $message): ?>
                                <li><?= esc($message) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('auth/register') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" required value="<?= old('username') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= old('email') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe <small class="text-muted">(8 caractères min.)</small></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                    <div class="mb-4">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="client" <?= old('role', 'client') === 'client' ? 'selected' : '' ?>>Client</option>
                            <option value="user"   <?= old('role') === 'user'   ? 'selected' : '' ?>>User (back-office)</option>
                        </select>
                        <small class="text-muted">Les comptes Admin sont gérés par l'administrateur.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Créer un compte</button>
                </form>

                <p class="text-center text-muted mt-3 mb-0">
                    Vous avez déjà un compte ?
                    <a href="<?= base_url('auth/login') ?>">Se connecter</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
