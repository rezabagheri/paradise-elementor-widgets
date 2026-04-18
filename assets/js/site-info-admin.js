/**
 * Paradise Site Info — Admin repeater rows
 *
 * Handles "Add" and "Remove" row buttons for each section table,
 * and drag-to-reorder via jQuery UI Sortable.
 * Uses <template> elements to clone new rows and __INDEX__ as placeholder.
 */
(function ($) {
    'use strict';

    $(function () {

        // ── Drag-to-reorder (Sortable) ────────────────────────────────────────

        $('tbody[id^="paradise-si-"]').sortable({
            handle:      '.paradise-si-handle',
            axis:        'y',
            cursor:      'grabbing',
            placeholder: 'paradise-si-sortable-placeholder',
            helper:      'clone',
            forcePlaceholderSize: true,
        });

        // ── Add row ───────────────────────────────────────────────────────────

        document.querySelectorAll('.paradise-si-add').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target   = btn.getAttribute('data-target');
                var tbody    = document.getElementById('paradise-si-' + target);
                var template = document.getElementById('paradise-si-tpl-' + target);

                if (!tbody || !template) return;

                var count   = parseInt(tbody.getAttribute('data-count') || '0', 10);
                var clone   = template.content.cloneNode(true);

                // Replace __INDEX__ with the actual row index in all name attrs
                clone.querySelectorAll('[name]').forEach(function (el) {
                    el.name = el.name.replace(/__INDEX__/g, count);
                });

                tbody.appendChild(clone);
                tbody.setAttribute('data-count', count + 1);

                // Focus the first input in the new row
                var firstInput = tbody.querySelector('tr:last-child input, tr:last-child select');
                if (firstInput) firstInput.focus();
            });
        });

        // ── Remove row (delegated) ────────────────────────────────────────────

        document.querySelectorAll('tbody[id^="paradise-si-"]').forEach(function (tbody) {
            tbody.addEventListener('click', function (e) {
                if (e.target.classList.contains('paradise-si-remove')) {
                    e.target.closest('tr').remove();
                }
            });
        });

    });

})(jQuery);
