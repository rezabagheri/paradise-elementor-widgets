/**
 * Paradise Elementor Widgets — Admin page behaviour.
 *
 * Currently:
 *   - Bulk Enable/Disable buttons for each toggle group on the Settings page.
 *     A button declares its target via data-bulk-target; the table it acts
 *     on declares the matching data-bulk-id; checkbox state flips (no save).
 *
 * Vanilla JS, no jQuery. Runs once on DOMContentLoaded (or immediately if
 * the parser passed our script tag at the end of <body>).
 */
( function () {
    'use strict';

    function applyBulkAction( table, enable ) {
        if ( ! table ) {
            return;
        }
        table.querySelectorAll( 'input[type="checkbox"]' ).forEach( function ( cb ) {
            cb.checked = enable;
        } );
    }

    function init() {
        document.querySelectorAll( '[data-bulk-action]' ).forEach( function ( btn ) {
            btn.addEventListener( 'click', function () {
                var targetId = btn.getAttribute( 'data-bulk-target' );
                var action   = btn.getAttribute( 'data-bulk-action' );
                var table    = document.querySelector( '[data-bulk-id="' + targetId + '"]' );
                applyBulkAction( table, action === 'enable' );
            } );
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
