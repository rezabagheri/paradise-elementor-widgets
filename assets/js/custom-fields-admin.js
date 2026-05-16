/**
 * Paradise Custom Fields — Admin JS
 *
 * Handles:
 *  - Group add / remove / collapse / drag-to-reorder
 *  - Field row add / remove / drag-to-reorder (within a group)
 *  - Type <select> change → swap which value-variant is visible + enable
 *    the right input (only one input per row stays enabled so the POST
 *    contains a single value)
 *  - wp.media picker for image fields
 *  - Copy-shortcode button writes [paradise_field key="…"] to clipboard
 *  - Renumber group/field indices after every reorder or remove so the
 *    name="paradise_custom_fields[groups][N][fields][M]…" indices stay
 *    0-based and contiguous when the form posts
 */
(function ($) {
    'use strict';

    $(function () {

        // ── Sortable helpers ──────────────────────────────────────────────────

        var sortableOpts = {
            handle:               '.paradise-cf-handle',
            axis:                 'y',
            cursor:               'grabbing',
            placeholder:          'paradise-cf-sortable-placeholder',
            forcePlaceholderSize: true,
        };

        function initFieldsSortable(tbody) {
            if (tbody) $(tbody).sortable($.extend({}, sortableOpts, { stop: renumberAll }));
        }

        document.querySelectorAll('.paradise-cf-fields').forEach(initFieldsSortable);

        $('#paradise-cf-groups').sortable($.extend({}, sortableOpts, {
            handle: '.paradise-cf-group-handle',
            items:  '> .paradise-cf-group',
            stop:   renumberAll,
        }));

        // ── Group: Add ────────────────────────────────────────────────────────

        document.getElementById('paradise-cf-add-group').addEventListener('click', function () {
            var container = document.getElementById('paradise-cf-groups');
            var idx       = parseInt(container.getAttribute('data-count') || '0', 10);
            var tpl       = document.getElementById('paradise-cf-tpl-group');
            var clone     = tpl.content.cloneNode(true);

            replaceAll(clone, '__GROUP__', idx);

            container.appendChild(clone);
            container.setAttribute('data-count', idx + 1);

            // Init sortable on the new group's fields tbody (currently empty,
            // but ready for when the user clicks "Add Field").
            container.lastElementChild
                .querySelectorAll('.paradise-cf-fields')
                .forEach(initFieldsSortable);

            var labelInput = container.lastElementChild.querySelector('.paradise-cf-group-label');
            if (labelInput) labelInput.focus();
        });

        // ── Group: Remove ─────────────────────────────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-cf-remove-group');
            if (!btn) return;
            // Destructive: a group holds all its fields. Confirm before remove.
            if (!window.confirm('Remove this group and all its fields? Changes apply on save.')) return;
            var group = btn.closest('.paradise-cf-group');
            if (group) {
                group.remove();
                renumberAll();
            }
        });

        // ── Group: Toggle collapse ────────────────────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-cf-group-toggle');
            if (!btn) return;
            var group = btn.closest('.paradise-cf-group');
            if (!group) return;
            var collapsed = group.classList.toggle('paradise-cf-group--collapsed');
            btn.setAttribute('aria-expanded', String(!collapsed));
        });

        // ── Field: Add ────────────────────────────────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-cf-add-field');
            if (!btn) return;
            var groupIdx = btn.getAttribute('data-group');
            var group    = btn.closest('.paradise-cf-group');
            var tbody    = group.querySelector('.paradise-cf-fields');
            var tpl      = document.getElementById('paradise-cf-tpl-field-row');

            if (!tbody || !tpl) return;

            var count = parseInt(tbody.getAttribute('data-count') || '0', 10);
            var clone = tpl.content.cloneNode(true);

            replaceAll(clone, '__GROUP__', groupIdx);
            replaceAll(clone, '__INDEX__', count);

            tbody.appendChild(clone);
            tbody.setAttribute('data-count', count + 1);

            // Focus the key input on the new row.
            var firstInput = tbody.querySelector('tr:last-child .paradise-cf-key-input');
            if (firstInput) firstInput.focus();
        });

        // ── Field: Remove ─────────────────────────────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-cf-remove-field');
            if (!btn) return;
            if (!window.confirm('Remove this field?')) return;
            var row = btn.closest('tr');
            if (row) {
                var tbody = row.parentElement;
                row.remove();
                renumberAll();
                // tbody count refreshed inside renumberAll
                void tbody;
            }
        });

        // ── Type select: swap visible variant + enable correct input ──────────

        /**
         * Each field row keeps ONE active value-input (matching the chosen
         * type) and disables the others. Disabled inputs are not submitted —
         * critical so we don't post two competing `[value]` keys when the
         * row's type changes.
         */
        document.addEventListener('change', function (e) {
            var sel = e.target.closest('.paradise-cf-type-select');
            if (!sel) return;

            var row    = sel.closest('tr');
            var wrap   = row.querySelector('.paradise-cf-value');
            var newTyp = sel.value;

            row.setAttribute('data-type', newTyp);
            wrap.setAttribute('data-active-type', newTyp);

            wrap.querySelectorAll('.paradise-cf-value-variant').forEach(function (variant) {
                var inputs = variant.querySelectorAll('input, textarea');
                var active = variant.getAttribute('data-type') === newTyp;
                inputs.forEach(function (i) { i.disabled = !active; });
            });
        });

        // ── Image picker: open wp.media ──────────────────────────────────────

        /**
         * Each image field has a hidden input (attachment ID), an optional
         * preview <img>, and Choose / Remove buttons. wp.media is the
         * standard WP modal — wp_enqueue_media() in PHP loads its assets.
         *
         * We instantiate a fresh frame per picker so each one carries its
         * own selection state. The frame is cached on the picker element
         * via a data-* property so reopening reuses the same instance
         * (faster, preserves last selection).
         */
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-cf-image-choose');
            if (!btn) return;

            var picker = btn.closest('.paradise-cf-image-picker');
            if (!picker) return;

            if (typeof wp === 'undefined' || !wp.media) {
                window.alert('Media library is not available.');
                return;
            }

            var frame = picker._mediaFrame;
            if (!frame) {
                frame = wp.media({
                    title:    'Choose Image',
                    button:   { text: 'Use this image' },
                    library:  { type: 'image' },
                    multiple: false,
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    setImage(picker, attachment.id, attachment.sizes && attachment.sizes.thumbnail
                        ? attachment.sizes.thumbnail.url
                        : attachment.url);
                });

                picker._mediaFrame = frame;
            }

            frame.open();
        });

        // ── Image picker: Remove ─────────────────────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-cf-image-remove');
            if (!btn) return;
            var picker = btn.closest('.paradise-cf-image-picker');
            if (!picker) return;
            setImage(picker, 0, '');
        });

        function setImage(picker, id, url) {
            var input   = picker.querySelector('.paradise-cf-image-id');
            var preview = picker.querySelector('.paradise-cf-image-preview');
            var img     = preview ? preview.querySelector('img') : null;
            var remove  = picker.querySelector('.paradise-cf-image-remove');
            var choose  = picker.querySelector('.paradise-cf-image-choose');

            input.value = id ? String(id) : '';
            picker.setAttribute('data-has-image', id ? '1' : '0');

            if (img) img.src = url || '';
            if (preview) preview.toggleAttribute('hidden', !id);
            if (remove)  remove.toggleAttribute('hidden',  !id);
            if (choose)  choose.textContent = id ? 'Change Image' : 'Choose Image';
        }

        // ── Color picker: live hex display ─────────────────────────────────────

        document.addEventListener('input', function (e) {
            var colorInput = e.target.closest('.paradise-cf-color-input');
            if (!colorInput) return;
            var display = colorInput.parentElement.querySelector('.paradise-cf-color-hex');
            if (display) display.textContent = colorInput.value;
        });

        // ── Range (open-bounded pair): sync hidden storage on input ───────────

        /**
         * Each range field renders two <input type="number"> elements (Min,
         * Max) plus a hidden input that stores the canonical "min,max"
         * string. Only the hidden input has a `name` attribute, so only it
         * posts. The two number inputs are UI-only.
         *
         * On every `input` event we update the hidden storage. We do NOT
         * enforce min ≤ max mid-type — typing a new Min like "150" before
         * later changing Max from 100 to 200 would otherwise feel hostile
         * (Max would jump under the user's fingers). The PHP sanitize on
         * save handles the swap, which is the right boundary to enforce
         * the invariant.
         */
        document.addEventListener('input', function (e) {
            var t = e.target;
            var isMin = t.classList && t.classList.contains('paradise-cf-range-min');
            var isMax = t.classList && t.classList.contains('paradise-cf-range-max');
            if (!isMin && !isMax) return;

            var wrap     = t.closest('.paradise-cf-range-double');
            var minInput = wrap.querySelector('.paradise-cf-range-min');
            var maxInput = wrap.querySelector('.paradise-cf-range-max');
            var storage  = wrap.querySelector('.paradise-cf-range-storage');

            // parseInt('', 10) is NaN; coerce to 0 so the stored form stays
            // "min,max" (both integers) even while the user is mid-typing.
            var minV = parseInt(minInput.value, 10);
            var maxV = parseInt(maxInput.value, 10);
            if (isNaN(minV)) minV = 0;
            if (isNaN(maxV)) maxV = 0;

            if (storage) storage.value = minV + ',' + maxV;
        });

        // ── Copy shortcode ────────────────────────────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-cf-copy-shortcode');
            if (!btn) return;
            var row = btn.closest('tr');
            var key = row.querySelector('.paradise-cf-key-input').value.trim();
            if (!key) {
                window.alert('Set a key first.');
                return;
            }
            var sc = '[paradise_field key="' + key + '"]';
            copyToClipboard(sc, btn);
        });

        /**
         * Cross-context clipboard copy. navigator.clipboard only works in
         * secure contexts (HTTPS) — for local dev on http://*.test the API
         * is undefined. Fall back to the textarea+execCommand trick which
         * works everywhere but requires a real DOM element.
         */
        function copyToClipboard(text, btn) {
            var done = function () { flash(btn); };
            var fail = function () { window.alert('Copy failed. Manually copy:\n' + text); };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(done, function () { fallback(); });
            } else {
                fallback();
            }

            function fallback() {
                try {
                    var ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity  = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    var ok = document.execCommand('copy');
                    document.body.removeChild(ta);
                    ok ? done() : fail();
                } catch (_) { fail(); }
            }
        }

        function flash(btn) {
            var icon = btn.querySelector('.dashicons');
            if (!icon) return;
            var old = icon.className;
            icon.className = 'dashicons dashicons-yes';
            setTimeout(function () { icon.className = old; }, 1200);
        }

        // ── Helpers ───────────────────────────────────────────────────────────

        /**
         * Replace `search` in every attribute of every element inside `node`
         * (DocumentFragment or Element). Same trick used by site-info-admin.js
         * for template instantiation.
         */
        function replaceAll(node, search, replacement) {
            var rep = String(replacement);
            node.querySelectorAll('*').forEach(function (el) {
                Array.from(el.attributes).forEach(function (attr) {
                    if (attr.value.indexOf(search) !== -1) {
                        el.setAttribute(attr.name, attr.value.split(search).join(rep));
                    }
                });
            });
        }

        /**
         * Rebuild every group/field index after a sort, add, or remove so
         * the name="paradise_custom_fields[groups][N][fields][M]…" pattern
         * stays 0-based and contiguous when the form posts. Because field
         * names embed BOTH the group index AND the field index, we have to
         * rewrite from the outside in.
         */
        function renumberAll() {
            var container = document.getElementById('paradise-cf-groups');
            var groups    = container.querySelectorAll(':scope > .paradise-cf-group');

            groups.forEach(function (group, gIdx) {
                group.setAttribute('data-group', gIdx);

                // Group-level fields (label, slug)
                group.querySelectorAll(':scope > .paradise-cf-group-header [name]').forEach(function (el) {
                    el.name = el.name.replace(/\[groups\]\[\d+\]/, '[groups][' + gIdx + ']');
                });

                // The "Add Field" button carries data-group too
                var addBtn = group.querySelector('.paradise-cf-add-field');
                if (addBtn) addBtn.setAttribute('data-group', gIdx);

                // Fields inside this group
                var tbody = group.querySelector('.paradise-cf-fields');
                if (!tbody) return;

                var rows = tbody.querySelectorAll(':scope > tr');
                rows.forEach(function (row, fIdx) {
                    row.querySelectorAll('[name]').forEach(function (el) {
                        el.name = el.name
                            .replace(/\[groups\]\[\d+\]/, '[groups][' + gIdx + ']')
                            .replace(/\[fields\]\[\d+\]/, '[fields][' + fIdx + ']');
                    });
                });

                tbody.setAttribute('data-count', rows.length);
            });

            container.setAttribute('data-count', groups.length);
        }
    });

})(jQuery);
