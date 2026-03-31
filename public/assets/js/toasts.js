/**
 * toasts.js — Notifications toast & modale de confirmation
 * Mini-ERP / CodeIgniter 4
 */
/* global bootstrap */

var TOAST_STORAGE_KEY = 'mini-erp-toast-queue';

function _readPersistentToasts() {
    if (typeof sessionStorage === 'undefined') {
        return [];
    }

    try {
        var raw = sessionStorage.getItem(TOAST_STORAGE_KEY);
        if (!raw) {
            return [];
        }
        var parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        return [];
    }
}

function _writePersistentToasts(entries) {
    if (typeof sessionStorage === 'undefined') {
        return;
    }

    try {
        if (entries.length) {
            sessionStorage.setItem(TOAST_STORAGE_KEY, JSON.stringify(entries));
        } else {
            sessionStorage.removeItem(TOAST_STORAGE_KEY);
        }
    } catch (e) {
        // Storage might be full or unavailable; silently ignore
    }
}

function _persistToastEntry(entry) {
    var now     = Date.now();
    var payload = {
        id:        entry.id,
        message:   entry.message,
        type:      entry.type,
        duration:  entry.duration,
        startTime: entry.startTime,
        expiresAt: entry.startTime + entry.duration,
    };
    var entries = _readPersistentToasts().filter(function (toast) {
        return toast.id !== entry.id && toast.expiresAt > now;
    });
    entries.push(payload);
    _writePersistentToasts(entries);
}

function _removePersistentToast(id) {
    var now     = Date.now();
    var entries = _readPersistentToasts().filter(function (toast) {
        return toast.id !== id && toast.expiresAt > now;
    });
    _writePersistentToasts(entries);
}

function _rehydrateToasts() {
    var now = Date.now();
    _readPersistentToasts().forEach(function (stored) {
        var remaining = stored.expiresAt - now;
        if (remaining <= 0) {
            _removePersistentToast(stored.id);
            return;
        }
        showToast(stored.message, stored.type, stored.duration, {
            storageId:  stored.id,
            startTime:  stored.startTime,
            skipStorage: true,
        });
    });
}

// ── Notifications toast ───────────────────────────────────────────────────────

/**
 * Affiche un toast Bootstrap en bas à droite avec une barre de progression.
 *
 * @param {string} message   Texte à afficher
 * @param {string} type      'success' | 'error' | 'danger' | 'warning' | 'info'
 * @param {number} duration  Durée en ms (défaut : 4000)
 * @param {Object} options   Internal helpers (storageId, startTime, skipStorage)
 */
function showToast(message, type, duration, options) {
    options = options || {};
    type = type || 'info';
    var now = Date.now();
    var totalDuration = (duration !== undefined) ? Math.max(0, duration) : 4000;
    var startTime = options.startTime || now;
    var elapsed = (typeof options.elapsed === 'number') ? options.elapsed : Math.max(0, now - startTime);
    elapsed = Math.min(elapsed, totalDuration);
    var remainingDuration = Math.max(0, totalDuration - elapsed);
    var id = options.storageId || ('toast-' + Date.now() + '-' + Math.floor(Math.random() * 9999));
    var ratio = totalDuration > 0 ? remainingDuration / totalDuration : 0;
    ratio = Math.max(0, Math.min(ratio, 1));
    var transitionDuration = Math.max(0, remainingDuration);
    var initialWidth = (ratio * 100).toFixed(2);

    var themes = {
        success: { bg: 'bg-success text-white', bar: 'bg-white', icon: 'bi-check-circle-fill',         closeBtn: 'btn-close-white' },
        error:   { bg: 'bg-danger  text-white', bar: 'bg-white', icon: 'bi-exclamation-triangle-fill', closeBtn: 'btn-close-white' },
        danger:  { bg: 'bg-danger  text-white', bar: 'bg-white', icon: 'bi-exclamation-triangle-fill', closeBtn: 'btn-close-white' },
        warning: { bg: 'bg-warning text-dark',  bar: 'bg-dark',  icon: 'bi-exclamation-circle-fill',  closeBtn: ''               },
        info:    { bg: 'bg-info    text-white', bar: 'bg-white', icon: 'bi-info-circle-fill',          closeBtn: 'btn-close-white' },
    };

    var t  = themes[type] || themes.info;

    var html =
        '<div id="' + id + '" class="toast align-items-center border-0 ' + t.bg + '" role="alert" aria-live="assertive" aria-atomic="true">' +
            '<div class="d-flex">' +
                '<div class="toast-body d-flex align-items-center gap-2">' +
                    '<i class="bi ' + t.icon + ' fs-5 flex-shrink-0"></i>' +
                    '<span>' + message + '</span>' +
                '</div>' +
                '<button type="button" class="btn-close ' + t.closeBtn + ' me-2 m-auto" aria-label="Fermer" data-toast-close></button>' +
            '</div>' +
            '<div style="height:4px;border-radius:0 0 0.375rem 0.375rem;overflow:hidden;">' +
                '<div id="' + id + '-bar" class="' + t.bar + '" style="height:100%;width:' + initialWidth + '%;opacity:.45;transition:width ' + transitionDuration + 'ms linear;"></div>' +
            '</div>' +
        '</div>';

    var container = document.getElementById('toast-container');
    if (!container) return;

    container.insertAdjacentHTML('beforeend', html);

    var el = document.getElementById(id);
    if (!el) return;

    if (!options.skipStorage) {
        _persistToastEntry({
            id:        id,
            message:   message,
            type:      type,
            duration:  totalDuration,
            startTime: startTime,
        });
    }

    var toast = new bootstrap.Toast(el, { autohide: false });
    toast.show();

    var bar = document.getElementById(id + '-bar');
    if (bar) {
        if (remainingDuration > 0) {
            if (typeof requestAnimationFrame === 'function') {
                requestAnimationFrame(function () {
                    bar.style.width = '0%';
                });
            } else {
                setTimeout(function () { bar.style.width = '0%'; }, 50);
            }
        } else {
            bar.style.width = '0%';
        }
    }

    function addEntryClass() {
        el.classList.add('toast-entry');
    }

    if (typeof requestAnimationFrame === 'function') {
        requestAnimationFrame(addEntryClass);
    } else {
        setTimeout(addEntryClass, 0);
    }

    var dismissTimer;
    var removalGuard = false;
    var removalFallback;
    var transitionHandler;

    function finalizeToast() {
        if (removalGuard) return;
        removalGuard = true;
        clearTimeout(removalFallback);
        if (transitionHandler) {
            el.removeEventListener('transitionend', transitionHandler);
        }
        toast.dispose();
        if (el.parentNode) {
            el.parentNode.removeChild(el);
        }
        _removePersistentToast(id);
    }

    function startExit() {
        if (removalGuard) return;
        clearTimeout(dismissTimer);
        el.classList.remove('toast-entry');
        el.classList.add('toast-exit');

        transitionHandler = function (event) {
            if (event.target !== el) return;
            finalizeToast();
        };
        el.addEventListener('transitionend', transitionHandler);
        removalFallback = setTimeout(finalizeToast, 600);
    }

    dismissTimer = setTimeout(startExit, remainingDuration);

    var closeBtn = el.querySelector('[data-toast-close]');
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            startExit();
        });
    }
}

// ── Modale de confirmation ────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
    _rehydrateToasts();

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
