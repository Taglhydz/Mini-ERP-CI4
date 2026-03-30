<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-person-plus me-2 text-primary"></i>
            <?= isset($client) ? 'Modifier le client' : 'Nouveau client' ?>
        </h1>
    </div>
    <a href="<?= base_url('clients') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form action="<?= isset($client) ? base_url('clients/' . $client['id'] . '/update') : base_url('clients/store') ?>"
              method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                    <input type="text" id="input-nom" name="nom"
                           class="form-control <?= session('errors.nom') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('nom', $client['nom'] ?? '')) ?>"
                           autocomplete="organization" required>
                    <div class="invalid-feedback"><?= session('errors.nom') ?></div>
                </div>

                <div class="col-md-7">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('email', $client['email'] ?? '')) ?>" required>
                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" id="input-telephone" name="telephone"
                           class="form-control" inputmode="numeric"
                           placeholder="06 12 34 56 78" maxlength="14"
                           value="<?= esc(old('telephone', $client['telephone'] ?? '')) ?>">
                    <div class="form-text">Format : 10 chiffres (ex : 06 12 34 56 78)</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Adresse</label>
                    <textarea name="adresse" class="form-control" rows="2"><?= esc(old('adresse', $client['adresse'] ?? '')) ?></textarea>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Ville</label>
                    <input type="text" id="input-ville" name="ville"
                           class="form-control"
                           value="<?= esc(old('ville', $client['ville'] ?? '')) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Code postal</label>
                    <input type="text" id="input-code-postal" name="code_postal"
                           class="form-control" inputmode="numeric"
                           maxlength="5" pattern="\d{5}"
                           value="<?= esc(old('code_postal', $client['code_postal'] ?? '')) ?>">
                </div>

                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i><?= isset($client) ? 'Enregistrer' : 'Créer le client' ?>
                    </button>
                    <a href="<?= base_url('clients') ?>" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    // ── Téléphone : formatage XX XX XX XX XX ────────────────────────────────
    function formatPhone(val) {
        var digits = val.replace(/\D/g, '').substring(0, 10);
        return digits.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
    }

    var $tel = $('#input-telephone');

    // Formater la valeur déjà présente au chargement (ex : numéro de la BDD)
    $tel.val(formatPhone($tel.val()));

    $tel.on('input', function () {
        var pos  = this.selectionStart;
        var prev = this.value.length;
        this.value = formatPhone(this.value);
        var delta = this.value.length - prev;
        this.setSelectionRange(pos + delta, pos + delta);
    });

    // ── Capitalisation : Nom & Ville (après espaces et tirets) ──────────────
    function capitalizeWords(str) {
        return str.toLowerCase().replace(/(^|[\s\-])([\p{L}])/gu, function (m, sep, letter) {
            return sep + letter.toUpperCase();
        });
    }

    $('#input-nom, #input-ville').on('blur', function () {
        this.value = capitalizeWords(this.value);
    });

    // ── Ville : interdire les chiffres ───────────────────────────────────────
    $('#input-ville').on('input', function () {
        this.value = this.value.replace(/\d/g, '');
    });

    // ── Code postal : chiffres uniquement, 5 max ─────────────────────────────
    $('#input-code-postal').on('input', function () {
        this.value = this.value.replace(/\D/g, '').substring(0, 5);
    });

    // ── Avant soumission : capitaliser au cas où le champ n'a pas perdu le focus
    $('form').on('submit', function () {
        $('#input-nom').val(capitalizeWords($('#input-nom').val()));
        $('#input-ville').val(capitalizeWords($('#input-ville').val()));
    });
});
</script>
<?= $this->endSection() ?>
