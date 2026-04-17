/**
 * Off-Canvas Menu — Paradise Elementor Widgets
 *
 * Wires up trigger, overlay, and close button for each .paradise-ocm-wrap.
 * Manages body scroll lock when any panel is open.
 *
 * Public API (window.Paradise):
 *   Paradise.openOffCanvas(id)     — open panel by widget id
 *   Paradise.closeOffCanvas(id)    — close panel by widget id
 *   Paradise.toggleOffCanvas(id)   — toggle panel
 */
(function () {
    'use strict';

    window.Paradise = window.Paradise || {};

    var OPEN_CLASS = 'paradise-ocm--open';
    var BODY_LOCK  = 'paradise-ocm-body-lock';

    /** Map of id → { panel, overlay, trigger } */
    var instances = {};

    // ── Initialisation ────────────────────────────────────────────────────────

    function initAll() {
        document.querySelectorAll('.paradise-ocm-wrap').forEach(initWrap);
    }

    function initWrap(wrap) {
        var id            = wrap.getAttribute('data-ocm-id');
        var isEditor      = wrap.getAttribute('data-ocm-edit') === 'true';
        var closeOnOverlay = wrap.getAttribute('data-ocm-overlay') !== 'false';

        var trigger  = wrap.querySelector('.paradise-ocm-trigger');
        var panel    = wrap.querySelector('.paradise-ocm-panel');
        var overlay  = wrap.querySelector('.paradise-ocm-overlay');
        var closeBtn = wrap.querySelector('.paradise-ocm-close');

        if (!panel) return;

        instances[id] = { panel: panel, overlay: overlay, trigger: trigger };

        // No interaction in editor
        if (isEditor) return;

        if (trigger) {
            trigger.addEventListener('click', function () { openPanel(id); });
        }

        if (overlay && closeOnOverlay) {
            overlay.addEventListener('click', function () { closePanel(id); });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function () { closePanel(id); });
        }

        // ESC key closes the panel
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isPanelOpen(id)) {
                closePanel(id);
            }
        });
    }

    // ── Open / close ──────────────────────────────────────────────────────────

    function isPanelOpen(id) {
        var inst = instances[id];
        return inst && inst.panel.classList.contains(OPEN_CLASS);
    }

    function openPanel(id) {
        var inst = instances[id];
        if (!inst) return;

        inst.panel.classList.add(OPEN_CLASS);
        if (inst.overlay) inst.overlay.classList.add(OPEN_CLASS);
        if (inst.trigger) inst.trigger.setAttribute('aria-expanded', 'true');

        document.body.classList.add(BODY_LOCK);
    }

    function closePanel(id) {
        var inst = instances[id];
        if (!inst) return;

        inst.panel.classList.remove(OPEN_CLASS);
        if (inst.overlay) inst.overlay.classList.remove(OPEN_CLASS);
        if (inst.trigger) inst.trigger.setAttribute('aria-expanded', 'false');

        // Only remove body lock when no other panels remain open
        var anyOpen = Object.keys(instances).some(function (k) {
            return k !== id && isPanelOpen(k);
        });
        if (!anyOpen) {
            document.body.classList.remove(BODY_LOCK);
        }
    }

    // ── Public API ────────────────────────────────────────────────────────────

    Paradise.openOffCanvas = openPanel;
    Paradise.closeOffCanvas = closePanel;
    Paradise.toggleOffCanvas = function (id) {
        isPanelOpen(id) ? closePanel(id) : openPanel(id);
    };

    // ── Bootstrap ─────────────────────────────────────────────────────────────

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

})();
