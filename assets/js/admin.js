/**
 * Paradise Elementor Widgets — Admin page behaviour.
 *
 * Currently:
 *   - Bulk Enable/Disable buttons for each toggle group on the Settings page.
 *     A button declares its target via data-bulk-target; the table it acts
 *     on declares the matching data-bulk-id; checkbox state flips (no save).
 *   - Live filter input ([data-paradise-filter]) that hides rows whose
 *     label/description don't match the typed term. Empty cards (no visible
 *     rows after filtering) collapse to a "no matches" hint.
 *
 * Vanilla JS, no jQuery. Runs once on DOMContentLoaded (or immediately if
 * the parser passed our script tag at the end of <body>).
 */
( function () {
    'use strict';

    // ── Bulk Enable / Disable ────────────────────────────────────────────────

    function applyBulkAction( table, enable ) {
        if ( ! table ) {
            return;
        }
        table.querySelectorAll( 'input[type="checkbox"]' ).forEach( function ( cb ) {
            cb.checked = enable;
        } );
    }

    function initBulk() {
        document.querySelectorAll( '[data-bulk-action]' ).forEach( function ( btn ) {
            btn.addEventListener( 'click', function () {
                var targetId = btn.getAttribute( 'data-bulk-target' );
                var action   = btn.getAttribute( 'data-bulk-action' );
                var table    = document.querySelector( '[data-bulk-id="' + targetId + '"]' );
                applyBulkAction( table, action === 'enable' );
            } );
        } );
    }

    // ── Filter / search ──────────────────────────────────────────────────────

    function filterRows( term ) {
        var needle = term.trim().toLowerCase();

        document.querySelectorAll( '.paradise-ew-toggles' ).forEach( function ( table ) {
            var anyVisible = false;

            table.querySelectorAll( 'tbody tr' ).forEach( function ( row ) {
                var nameCell = row.querySelector( '.paradise-ew-toggles__name' );
                var descCell = row.querySelector( '.paradise-ew-toggles__desc' );
                var haystack = (
                    ( nameCell ? nameCell.textContent : '' ) + ' ' +
                    ( descCell ? descCell.textContent : '' )
                ).toLowerCase();

                var match = needle === '' || haystack.indexOf( needle ) !== -1;
                row.style.display = match ? '' : 'none';
                if ( match ) anyVisible = true;
            } );

            // Collapse the whole card if no rows survived the filter.
            var card = table.closest( '.paradise-ew-admin__card' );
            if ( card ) {
                card.style.display = anyVisible ? '' : 'none';
            }
        } );
    }

    function initFilter() {
        var input = document.querySelector( '[data-paradise-filter]' );
        if ( ! input ) {
            return;
        }
        input.addEventListener( 'input', function () {
            filterRows( input.value );
        } );
    }

    // ── Boot ─────────────────────────────────────────────────────────────────

    function init() {
        initBulk();
        initFilter();
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
