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
                <!-- First name + Last name -->
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                    <input type="text" id="input-first-name" name="first_name"
                           class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('first_name', $client['first_name'] ?? '')) ?>"
                           autocomplete="given-name" required>
                    <div class="invalid-feedback"><?= session('errors.first_name') ?></div>
                </div>

                <div class="col-md-7">
                    <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                    <input type="text" id="input-last-name" name="last_name"
                           class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('last_name', $client['last_name'] ?? '')) ?>"
                           autocomplete="family-name" required>
                    <div class="invalid-feedback"><?= session('errors.last_name') ?></div>
                </div>

                <!-- Email + Phone -->
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('email', $client['email'] ?? '')) ?>"
                           autocomplete="email" required>
                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" id="input-phone" name="phone"
                           class="form-control" inputmode="numeric"
                           placeholder="06 12 34 56 78" maxlength="14"
                           value="<?= esc(old('phone', $client['phone'] ?? '')) ?>"
                           autocomplete="tel">
                    <div class="form-text">Format : 10 chiffres (ex : 06 12 34 56 78)</div>
                </div>

                <!-- Adresse décomposée -->
                <div class="col-2">
                    <label class="form-label fw-semibold">N°</label>
                    <input type="text" id="input-street-number" name="street_number"
                           class="form-control" inputmode="numeric"
                           placeholder="12" maxlength="10"
                           value="<?= esc(old('street_number', $client['street_number'] ?? '')) ?>">
                </div>

                <div class="col-3">
                    <label class="form-label fw-semibold">Type de voie</label>
                    <?php
                    $types   = ['Allée','Avenue','Boulevard','Chemin','Cour','Domaine',
                                'Hameau','Impasse','Lieu-dit','Lotissement','Passage','Place',
                                'Résidence','Route','Rue','Square','Voie','Zone'];
                    $selType = old('street_type', $client['street_type'] ?? '');
                    $isCustom = $selType !== '' && !in_array($selType, $types);
                    ?>
                    <select id="sel-street-type" class="form-select">
                        <option value="">—</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= esc($t) ?>" <?= ($selType === $t) ? 'selected' : '' ?>>
                                <?= esc($t) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="__autre__" <?= $isCustom ? 'selected' : '' ?>>Autre…</option>
                    </select>
                    <input type="text" id="input-street-type-custom"
                           class="form-control mt-1 <?= $isCustom ? '' : 'd-none' ?>"
                           placeholder="Rue, Avenue..."
                           value="<?= $isCustom ? esc($selType) : '' ?>">
                    <input type="hidden" name="street_type" id="input-street-type"
                           value="<?= esc($selType) ?>">
                </div>

                <div class="col-7">
                    <label class="form-label fw-semibold">Nom de la voie</label>
                    <input type="text" id="input-street-name" name="street_name"
                           class="form-control"
                           placeholder="de la Paix"
                           value="<?= esc(old('street_name', $client['street_name'] ?? '')) ?>">
                </div>

                <!-- Ville + Code postal -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Ville</label>
                    <input type="text" id="input-city" name="city"
                           class="form-control"
                           value="<?= esc(old('city', $client['city'] ?? '')) ?>"
                           autocomplete="address-level2">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Code postal</label>
                    <input type="text" id="input-postal-code" name="postal_code"
                           class="form-control" inputmode="numeric"
                           maxlength="5" pattern="\d{5}"
                           value="<?= esc(old('postal_code', $client['postal_code'] ?? '')) ?>"
                           autocomplete="postal-code">
                </div>

                <!-- Mot de passe -->
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        Mot de passe
                        <?= ! isset($client) ? '<span class="text-danger">*</span>' : '' ?>
                    </label>
                    <input type="password" name="password"
                           class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                           <?= ! isset($client) ? 'required' : '' ?>>
                    <?php if (isset($client)): ?>
                        <div class="form-text">Laisser vide pour conserver le mot de passe actuel.</div>
                    <?php endif; ?>
                    <div class="invalid-feedback"><?= session('errors.password') ?></div>
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
    $('#input-street-number').on('input', function () {
        this.value = this.value.replace(/\D/g, '').substring(0, 10);
    });

    // ── Type de voie : select + champ "Autre" ────────────────────────────────
    $('#sel-street-type').on('change', function () {
        var val = this.value;
        if (val === '__autre__') {
            $('#input-street-type-custom').removeClass('d-none').val('').trigger('focus');
            $('#input-street-type').val('');
        } else {
            $('#input-street-type-custom').addClass('d-none').val('');
            $('#input-street-type').val(val);
        }
    });
    $('#input-street-type-custom').on('input', function () {
        $('#input-street-type').val(this.value);
    });

    // ── Téléphone : formatage XX XX XX XX XX ────────────────────────────────
    function formatPhone(val) {
        var digits = val.replace(/\D/g, '').substring(0, 10);
        return digits.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
    }

    var $tel = $('#input-phone');
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

    $('#input-first-name, #input-last-name, #input-city, #input-street-name').on('blur', function () {
        this.value = capitalizeWords(this.value);
    });

    // ── Ville → auto-remplissage code postal (geo.api.gouv.fr) ───────────────
    $('#input-city').on('blur', function () {
        var ville = this.value.trim();
        if (!ville) return;
        fetch('https://geo.api.gouv.fr/communes?nom=' + encodeURIComponent(ville)
              + '&fields=codesPostaux&limit=1&boost=population')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && data.length && data[0].codesPostaux && data[0].codesPostaux.length) {
                    var cp = data[0].codesPostaux[0];
                    var $cp = $('#input-postal-code');
                    if (!$cp.val()) {
                        $cp.val(cp);
                    }
                }
            })
            .catch(function () { /* Pas de connexion — silencieux */ });
    });

    // ── Ville : interdire les chiffres ───────────────────────────────────────
    $('#input-city').on('input', function () {
        this.value = this.value.replace(/\d/g, '');
    });

    // ── Code postal : chiffres uniquement, 5 max ─────────────────────────────
    $('#input-postal-code').on('input', function () {
        this.value = this.value.replace(/\D/g, '').substring(0, 5);
    });

    // ── Avant soumission : capitaliser au cas où le champ n'a pas perdu le focus
    $('form').on('submit', function () {
        $('#input-first-name').val(capitalizeWords($('#input-first-name').val()));
        $('#input-last-name').val(capitalizeWords($('#input-last-name').val()));
        $('#input-city').val(capitalizeWords($('#input-city').val()));
        $('#input-street-name').val(capitalizeWords($('#input-street-name').val()));
