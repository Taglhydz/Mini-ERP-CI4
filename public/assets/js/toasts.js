/**
 * toasts.js — Notifications toast & modale de confirmation
 * Mini-ERP / CodeIgniter 4
 */
/* global bootstrap */

// ── Notifications toast ───────────────────────────────────────────────────────

/**
 * Affiche un toast Bootstrap en bas à droite avec une barre de progression.
 *
 * @param {string} message   Texte à afficher
 * @param {string} type      'success' | 'error' | 'danger' | 'warning' | 'info'
 * @param {number} duration  Durée en ms (défaut : 4000)
 */
function showToast(message, type, duration) {
    type     = type     || 'info';
    duration = (duration !== undefined) ? duration : 4000;

    var themes = {
        success: { bg: 'bg-success text-white', bar: 'bg-white', icon: 'bi-check-circle-fill',         closeBtn: 'btn-close-white' },
        error:   { bg: 'bg-danger  text-white', bar: 'bg-white', icon: 'bi-exclamation-triangle-fill', closeBtn: 'btn-close-white' },
        danger:  { bg: 'bg-danger  text-white', bar: 'bg-white', icon: 'bi-exclamation-triangle-fill', closeBtn: 'btn-close-white' },
        warning: { bg: 'bg-warning text-dark',  bar: 'bg-dark',  icon: 'bi-exclamation-circle-fill',  closeBtn: ''               },
        info:    { bg: 'bg-info    text-white', bar: 'bg-white', icon: 'bi-info-circle-fill',          closeBtn: 'btn-close-white' },
    };

    var t  = themes[type] || themes.info;
    var id = 'toast-' + Date.now() + '-' + Math.floor(Math.random() * 9999);

    var html =
        '<div id="' + id + '" class="toast align-items-center border-0 ' + t.bg + '" role="alert" aria-live="assertive" aria-atomic="true">' +
            '<div class="d-flex">' +
                '<div class="toast-body d-flex align-items-center gap-2">' +
                    '<i class="bi ' + t.icon + ' fs-5 flex-shrink-0"></i>' +
                    '<span>' + message + '</span>' +
                '</div>' +
                '<button type="button" class="btn-close ' + t.closeBtn + ' me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>' +
            '</div>' +
            '<div style="height:4px;border-radius:0 0 0.375rem 0.375rem;overflow:hidden;">' +
                '<div id="' + id + '-bar" class="' + t.bar + '" style="height:100%;width:100%;transition:width ' + duration + 'ms linear;opacity:.45;"></div>' +
            '</div>' +
        '</div>';

    document.getElementById('toast-container').insertAdjacentHTML('beforeend', html);

    var el    = document.getElementById(id);
    var toast = new bootstrap.Toast(el, { autohide: false });
    toast.show();

    // Lancer l'animation de la barre après un court délai (sinon la transition est ignorée)
    setTimeout(function () {
        var bar = document.getElementById(id + '-bar');
        if (bar) bar.style.width = '0%';
    }, 50);

    // Auto-dismiss
    setTimeout(function () {
        toast.hide();
        el.addEventListener('hidden.bs.toast', function () { el.remove(); });
    }, duration);
}

// ── Modale de confirmation ────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-confirm]');
        if (!trigger) return;
        e.preventDefault();
        e.stopPropagation();

        var message = trigger.dataset.confirm || 'Confirmer cette action ?';
        var form    = trigger.closest('form');

        var modalEl = document.getElementById('modal-confirm');
        document.getElementById('modal-confirm-body').textContent = message;

        var modal  = bootstrap.Modal.getOrCreateInstance(modalEl);
        var okBtn  = document.getElementById('btn-confirm-ok');

        // Cloner le bouton pour supprimer les anciens listeners avant d'en attacher un nouveau
        var fresh = okBtn.cloneNode(true);
        okBtn.replaceWith(fresh);

        fresh.addEventListener('click', function () {
            modal.hide();

            var deleteUrl = trigger.dataset.deleteUrl;
            var tableId   = trigger.dataset.table;

            if (deleteUrl) {
                // Suppression Ajax : pas de rechargement de page
                fetch(deleteUrl, {
                    method:  'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    showToast(data.message, data.success ? 'success' : 'error');
                    // Recharger uniquement la DataTable concernée
                    if (tableId && typeof $ !== 'undefined' && $.fn.DataTable.isDataTable('#' + tableId)) {
                        $('#' + tableId).DataTable().ajax.reload(null, false);
                    }
                })
                .catch(function () {
                    showToast('Une erreur est survenue.', 'error');
                });
            } else if (form) {
                form.submit();
            }
        });

        modal.show();
    });
});
