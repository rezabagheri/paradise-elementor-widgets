/**
 * Paradise Site Info — Admin JS
 *
 * Handles:
 *  - Location add / remove / collapse / drag-to-reorder
 *  - Phone / email row add / remove / drag-to-reorder (per location)
 *  - Social row add / remove / drag-to-reorder (global)
 *  - Business hours open/closed toggle
 *  - Google Map live preview on map URL input
 */
(function ($) {
    'use strict';

    $(function () {

        // ── Sortable helpers ──────────────────────────────────────────────────

        var sortableOpts = {
            handle:               '.paradise-si-handle',
            axis:                 'y',
            cursor:               'grabbing',
            placeholder:          'paradise-si-sortable-placeholder',
            forcePlaceholderSize: true,
        };

        function initRowSortable(tbody) {
            if (tbody) $(tbody).sortable(sortableOpts);
        }

        // Init row sortables on existing location tbodies and the socials tbody
        document.querySelectorAll('.paradise-si-rows, .paradise-si-rows-global').forEach(initRowSortable);

        // Location-level sortable
        $('#paradise-si-locations').sortable($.extend({}, sortableOpts, {
            handle: '.paradise-si-loc-handle',
            items:  '> .paradise-si-location',
            stop:   renumberLocations,
        }));

        // ── Location: Add ─────────────────────────────────────────────────────

        document.getElementById('paradise-si-add-location').addEventListener('click', function () {
            var container = document.getElementById('paradise-si-locations');
            var locIdx    = parseInt(container.getAttribute('data-count') || '0', 10);
            var tpl       = document.getElementById('paradise-si-tpl-location');
            var clone     = tpl.content.cloneNode(true);

            replaceAll(clone, '__LOC__', locIdx);

            container.appendChild(clone);
            container.setAttribute('data-count', locIdx + 1);

            // Init row sortables on the new location's tbodies
            container.lastElementChild.querySelectorAll('.paradise-si-rows').forEach(initRowSortable);

            // Focus the label input
            var labelInput = container.lastElementChild.querySelector('.paradise-si-location-label');
            if (labelInput) labelInput.focus();
        });

        // ── Location: Remove (delegated) ─────────────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-si-remove-location');
            if (!btn) return;
            var loc = btn.closest('.paradise-si-location');
            if (loc) {
                loc.remove();
                renumberLocations();
            }
        });

        // ── Location: Toggle collapse (delegated) ─────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-si-location-toggle');
            if (!btn) return;
            var loc = btn.closest('.paradise-si-location');
            if (!loc) return;
            var collapsed = loc.classList.toggle('paradise-si-location--collapsed');
            btn.setAttribute('aria-expanded', String(!collapsed));
        });

        // ── Row: Add phones / emails (delegated) ──────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-si-add-row');
            if (!btn) return;

            var section = btn.getAttribute('data-section');   // 'phones' or 'emails'
            var locIdx  = btn.getAttribute('data-location');
            var tbody   = document.getElementById('paradise-si-' + section + '-' + locIdx);
            var tpl     = document.getElementById('paradise-si-tpl-' + section + '-row');

            if (!tbody || !tpl) return;

            var count = parseInt(tbody.getAttribute('data-count') || '0', 10);
            var clone = tpl.content.cloneNode(true);

            replaceAll(clone, '__LOC__',   locIdx);
            replaceAll(clone, '__INDEX__', count);

            tbody.appendChild(clone);
            tbody.setAttribute('data-count', count + 1);

            var first = tbody.querySelector('tr:last-child input');
            if (first) first.focus();
        });

        // ── Social row: Add ───────────────────────────────────────────────────

        document.getElementById('paradise-si-add-social').addEventListener('click', function () {
            var tbody = document.getElementById('paradise-si-socials');
            var tpl   = document.getElementById('paradise-si-tpl-social-row');
            var count = parseInt(tbody.getAttribute('data-count') || '0', 10);
            var clone = tpl.content.cloneNode(true);

            replaceAll(clone, '__INDEX__', count);
            tbody.appendChild(clone);
            tbody.setAttribute('data-count', count + 1);
        });

        // ── Row: Remove (delegated) ───────────────────────────────────────────

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.paradise-si-remove-row');
            if (btn) btn.closest('tr').remove();
        });

        // ── Business Hours: open/closed toggle (delegated) ────────────────────

        document.addEventListener('change', function (e) {
            if (!e.target.classList.contains('paradise-si-hours-toggle')) return;

            var checkbox = e.target;
            var rowId    = checkbox.getAttribute('data-row');
            var row      = document.getElementById(rowId);
            var label    = checkbox.closest('.paradise-si-toggle') &&
                           checkbox.closest('.paradise-si-toggle').querySelector('.paradise-si-toggle-label');
            var isOpen   = checkbox.checked;

            if (row)   row.classList.toggle('paradise-si-hours-closed', !isOpen);
            if (label) label.textContent = isOpen ? 'Open' : 'Closed';

            if (row) {
                row.querySelectorAll('.paradise-si-time').forEach(function (t) {
                    t.disabled = !isOpen;
                });
            }
        });

        // ── Map URL: live preview (debounced, delegated) ──────────────────────

        var mapTimer;
        document.addEventListener('input', function (e) {
            if (!e.target.classList.contains('paradise-si-map-url-input')) return;

            var input = e.target;
            clearTimeout(mapTimer);
            mapTimer = setTimeout(function () {
                var url     = input.value.trim();
                var section = input.closest('.paradise-si-section');
                var preview = section && section.querySelector('.paradise-si-map-preview');
                if (!preview) return;

                var frame = preview.querySelector('iframe');
                if (url) {
                    if (frame) frame.src = url;
                    preview.style.display = '';
                } else {
                    if (frame) frame.src = '';
                    preview.style.display = 'none';
                }
            }, 600);
        });

        // ── Helpers ───────────────────────────────────────────────────────────

        /**
         * Replace all occurrences of `search` in every attribute of every
         * element inside `node` (a DocumentFragment or Element).
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
         * After a location is removed or reordered, renumber every location card
         * so the indices are 0-based and contiguous. Updates:
         *  - data-location attribute on the card and all its children
         *  - name attributes (locations][N] → locations][newN])
         *  - tbody IDs (paradise-si-phones-N, paradise-si-emails-N)
         *  - hours row IDs and checkbox data-row values
         */
        function renumberLocations() {
            var cards = document.querySelectorAll('#paradise-si-locations > .paradise-si-location');

            cards.forEach(function (loc, newIdx) {
                var oldIdx = parseInt(loc.getAttribute('data-location'), 10);
                if (oldIdx === newIdx) return;

                var oldKey = 'locations][' + oldIdx + ']';
                var newKey = 'locations][' + newIdx + ']';

                // Update name attributes
                loc.querySelectorAll('[name]').forEach(function (el) {
                    if (el.name.indexOf(oldKey) !== -1) {
                        el.name = el.name.split(oldKey).join(newKey);
                    }
                });

                // Update data-location on card and descendants
                loc.setAttribute('data-location', newIdx);
                loc.querySelectorAll('[data-location]').forEach(function (el) {
                    el.setAttribute('data-location', newIdx);
                });

                // Update tbody IDs
                ['phones', 'emails'].forEach(function (section) {
                    var tbody = loc.querySelector('#paradise-si-' + section + '-' + oldIdx);
                    if (tbody) tbody.id = 'paradise-si-' + section + '-' + newIdx;
                });

                // Update hours row IDs and checkbox data-row
                var oldPrefix = 'paradise-si-hours-row-' + oldIdx + '-';
                var newPrefix = 'paradise-si-hours-row-' + newIdx + '-';

                loc.querySelectorAll('[id^="paradise-si-hours-row-"]').forEach(function (row) {
                    row.id = row.id.replace(oldPrefix, newPrefix);
                });
                loc.querySelectorAll('[data-row]').forEach(function (cb) {
                    cb.setAttribute('data-row', cb.getAttribute('data-row').replace(oldPrefix, newPrefix));
                });
            });

            // Keep container count in sync
            var container = document.getElementById('paradise-si-locations');
            if (container) container.setAttribute('data-count', cards.length);
        }

    });

})(jQuery);
