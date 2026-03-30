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
                <!-- Prénom + Nom -->
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                    <input type="text" id="input-prenom" name="prenom"
                           class="form-control <?= session('errors.prenom') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('prenom', $client['prenom'] ?? '')) ?>"
                           autocomplete="given-name" required>
                    <div class="invalid-feedback"><?= session('errors.prenom') ?></div>
                </div>

                <div class="col-md-7">
                    <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                    <input type="text" id="input-nom" name="nom"
                           class="form-control <?= session('errors.nom') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('nom', $client['nom'] ?? '')) ?>"
                           autocomplete="family-name" required>
                    <div class="invalid-feedback"><?= session('errors.nom') ?></div>
                </div>

                <!-- Email + Téléphone -->
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('email', $client['email'] ?? '')) ?>"
                           autocomplete="email" required>
                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" id="input-telephone" name="telephone"
                           class="form-control" inputmode="numeric"
                           placeholder="06 12 34 56 78" maxlength="14"
                           value="<?= esc(old('telephone', $client['telephone'] ?? '')) ?>"
                           autocomplete="tel">
                    <div class="form-text">Format : 10 chiffres (ex : 06 12 34 56 78)</div>
                </div>

                <!-- Adresse décomposée -->
                <div class="col-2">
                    <label class="form-label fw-semibold">N°</label>
                    <input type="text" id="input-adresse-numero" name="adresse_numero"
                           class="form-control" inputmode="numeric"
                           placeholder="12" maxlength="10"
                           value="<?= esc(old('adresse_numero', $client['adresse_numero'] ?? '')) ?>">
                </div>

                <div class="col-3">
                    <label class="form-label fw-semibold">Type de voie</label>
                    <?php
                    $types   = ['Allée','Avenue','Boulevard','Chemin','Cour','Domaine',
                                'Hameau','Impasse','Lieu-dit','Lotissement','Passage','Place',
                                'Résidence','Route','Rue','Square','Voie','Zone'];
                    $selType = old('adresse_type_voie', $client['adresse_type_voie'] ?? '');
                    $isCustom = $selType !== '' && !in_array($selType, $types);
                    ?>
                    <select id="sel-type-voie" class="form-select">
                        <option value="">—</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= esc($t) ?>" <?= ($selType === $t) ? 'selected' : '' ?>>
                                <?= esc($t) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="__autre__" <?= $isCustom ? 'selected' : '' ?>>Autre…</option>
                    </select>
                    <input type="text" id="input-type-custom"
                           class="form-control mt-1 <?= $isCustom ? '' : 'd-none' ?>"
                           placeholder="Rue, Avenue..."
                           value="<?= $isCustom ? esc($selType) : '' ?>">
                    <input type="hidden" name="adresse_type_voie" id="input-type-voie"
                           value="<?= esc($selType) ?>">
                </div>

                <div class="col-7">
                    <label class="form-label fw-semibold">Nom de la voie</label>
                    <input type="text" id="input-nom-voie" name="adresse_nom_voie"
                           class="form-control"
                           placeholder="de la Paix"
                           value="<?= esc(old('adresse_nom_voie', $client['adresse_nom_voie'] ?? '')) ?>">
                </div>

                <!-- Ville + Code postal -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Ville</label>
                    <input type="text" id="input-ville" name="ville"
                           class="form-control"
                           value="<?= esc(old('ville', $client['ville'] ?? '')) ?>"
                           autocomplete="address-level2">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Code postal</label>
                    <input type="text" id="input-code-postal" name="code_postal"
                           class="form-control" inputmode="numeric"
                           maxlength="5" pattern="\d{5}"
                           value="<?= esc(old('code_postal', $client['code_postal'] ?? '')) ?>"
                           autocomplete="postal-code">
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
    // ── N° adresse : chiffres uniquement ────────────────────────────────────
    $('#input-adresse-numero').on('input', function () {
        this.value = this.value.replace(/\D/g, '').substring(0, 10);
    });

    // ── Type de voie : select + champ "Autre" ────────────────────────────────
    $('#sel-type-voie').on('change', function () {
        var val = this.value;
        if (val === '__autre__') {
            $('#input-type-custom').removeClass('d-none').val('').trigger('focus');
            $('#input-type-voie').val('');
        } else {
            $('#input-type-custom').addClass('d-none').val('');
            $('#input-type-voie').val(val);
        }
    });
    $('#input-type-custom').on('input', function () {
        $('#input-type-voie').val(this.value);
    });

    // ── Téléphone : formatage XX XX XX XX XX ────────────────────────────────
    function formatPhone(val) {
        var digits = val.replace(/\D/g, '').substring(0, 10);
        return digits.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
    }

    var $tel = $('#input-telephone');
    $tel.val(formatPhone($tel.val()));
    $tel.on('input', function () {
        var pos  = this.selectionStart;
        var prev = this.value.length;
        this.value = formatPhone(this.value);
        var delta = this.value.length - prev;
        this.setSelectionRange(pos + delta, pos + delta);
    });

    // ── Capitalisation (après espaces et tirets) ─────────────────────────────
    function capitalizeWords(str) {
        return str.toLowerCase().replace(/(^|[\s\-])([\p{L}])/gu, function (m, sep, letter) {
            return sep + letter.toUpperCase();
        });
    }

    $('#input-prenom, #input-nom, #input-ville, #input-nom-voie').on('blur', function () {
        this.value = capitalizeWords(this.value);
    });

    // ── Ville → auto-remplissage code postal (geo.api.gouv.fr) ───────────────
    $('#input-ville').on('blur', function () {
        var ville = this.value.trim();
        if (!ville) return;
        fetch('https://geo.api.gouv.fr/communes?nom=' + encodeURIComponent(ville)
              + '&fields=codesPostaux&limit=1&boost=population')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && data.length && data[0].codesPostaux && data[0].codesPostaux.length) {
                    var cp = data[0].codesPostaux[0];
                    var $cp = $('#input-code-postal');
                    if (!$cp.val()) {
                        $cp.val(cp);
                    }
                }
            })
            .catch(function () { /* Pas de connexion — silencieux */ });
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
        $('#input-prenom').val(capitalizeWords($('#input-prenom').val()));
        $('#input-nom').val(capitalizeWords($('#input-nom').val()));
        $('#input-ville').val(capitalizeWords($('#input-ville').val()));
        $('#input-nom-voie').val(capitalizeWords($('#input-nom-voie').val()));
    });
});
</script>
<?= $this->endSection() ?>
