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
 *   - Per-row Copy Shortcode buttons (.paradise-si-copy-shortcode) on the
 *     Site Info page: reads the current label/platform value from the
 *     same <tr> and writes a [paradise_site_info ...] shortcode to the
 *     clipboard, then briefly flips the button icon to a checkmark.
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
            if ( cb.checked === enable ) {
                return;
            }
            cb.checked = enable;
            // Programmatic .checked = X doesn't fire a change event on
            // its own, so the dirty tracker (which listens to change)
            // wouldn't notice the bulk flip. Dispatch one explicitly.
            cb.dispatchEvent( new Event( 'change', { bubbles: true } ) );
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

    // ── Copy shortcode (Site Info repeaters) ────────────────────────────────

    /**
     * Build the [paradise_site_info ...] shortcode for the row that the
     * given button lives in. Reads the current value of the label/platform
     * field — so the shortcode reflects unsaved edits, not just what the
     * server rendered.
     */
    function buildShortcode( button ) {
        var row      = button.closest( 'tr' );
        var type     = button.getAttribute( 'data-copy-type' );      // 'phone' | 'email' | 'social'
        var location = button.getAttribute( 'data-copy-location' ); // optional, present on phone/email only

        if ( ! row || ! type ) {
            return '';
        }

        if ( 'social' === type ) {
            var platformSelect = row.querySelector( 'select' );
            var platform       = platformSelect ? platformSelect.value : '';
            if ( ! platform ) {
                return '';
            }
            return '[paradise_site_info type="social" platform="' + platform + '"]';
        }

        // Phone / Email: prefer label (human-readable), fall back to the
        // row's current position in its tbody as the numeric index.
        var labelInput = row.querySelector( 'input[name*="[label]"]' );
        var label      = labelInput ? labelInput.value.trim() : '';
        var locAttr    = location ? ' location="' + location + '"' : '';

        if ( label ) {
            return '[paradise_site_info type="' + type + '"' + locAttr + ' label="' + label + '"]';
        }

        var index = Array.prototype.indexOf.call( row.parentNode.children, row );
        return '[paradise_site_info type="' + type + '"' + locAttr + ' index="' + index + '"]';
    }

    function flashCopied( button ) {
        button.classList.add( 'is-copied' );
        window.setTimeout( function () {
            button.classList.remove( 'is-copied' );
        }, 1200 );
    }

    function initCopyShortcode() {
        // Event delegation on document so cloned rows (added via JS by
        // site-info-admin.js) inherit the behaviour without re-binding.
        document.addEventListener( 'click', function ( e ) {
            var button = e.target.closest( '.paradise-si-copy-shortcode' );
            if ( ! button ) {
                return;
            }
            var shortcode = buildShortcode( button );
            if ( ! shortcode || ! navigator.clipboard ) {
                return;
            }
            navigator.clipboard.writeText( shortcode ).then( function () {
                flashCopied( button );
            } );
        } );
    }

    // ── Unsaved-changes indicator ───────────────────────────────────────────

    /**
     * Shows the .paradise-ew-admin__dirty pill in the page header as soon
     * as any form field changes, and warns the user via the browser's
     * native confirm dialog if they try to navigate away with unsaved work.
     *
     * Once dirty, stays dirty until the page reloads (the form submission
     * flow on Settings / Site Info redirects back, so the indicator
     * resets naturally on the next render).
     */
    function initDirtyTracking() {
        var indicator = document.querySelector( '.paradise-ew-admin__dirty' );
        var form      = document.querySelector( '.paradise-ew-admin form' );
        if ( ! indicator || ! form ) {
            return;
        }

        var isDirty = false;

        function markDirty() {
            if ( isDirty ) {
                return;
            }
            isDirty = true;
            indicator.hidden = false;
        }

        form.addEventListener( 'input',  markDirty );
        form.addEventListener( 'change', markDirty );

        // Native confirm dialog before leaving with unsaved work. Modern
        // browsers ignore the message string and show their own generic
        // copy — a non-empty returnValue is the trigger.
        window.addEventListener( 'beforeunload', function ( e ) {
            if ( ! isDirty ) {
                return;
            }
            e.preventDefault();
            e.returnValue = '';
        } );

        // Form submission counts as "saving" — clear the flag so the
        // beforeunload handler doesn't fire on the redirect that follows.
        form.addEventListener( 'submit', function () {
            isDirty = false;
        } );
    }

    // ── Scroll the success notice into view on post-save reload ─────────────

    /**
     * After a successful save the page redirects with a query flag
     * (?settings-updated=true from Options API, or ?saved=1 from the
     * custom Site Info handler). The notice itself renders at the top
     * of .wrap — but on long forms (Site Info especially) some browsers
     * preserve scroll position after a POST→GET redirect and the user
     * lands back near the Save button, missing the confirmation.
     *
     * Smooth-scroll the success notice into view on load. No-op if the
     * page already shows it (e.g. browser already scrolled to top).
     */
    function initScrollOnSave() {
        var params = new URLSearchParams( window.location.search );
        if ( ! params.has( 'settings-updated' ) && ! params.has( 'saved' ) ) {
            return;
        }
        // Defer to next tick so the notice is in the DOM and laid out.
        window.setTimeout( function () {
            var notice = document.querySelector(
                '.paradise-ew-admin .notice-success, .paradise-ew-admin .updated'
            );
            if ( notice ) {
                notice.scrollIntoView( { behavior: 'smooth', block: 'start' } );
            }
        }, 0 );
    }

    // ── Boot ─────────────────────────────────────────────────────────────────

    function init() {
        initBulk();
        initFilter();
        initCopyShortcode();
        initDirtyTracking();
        initScrollOnSave();
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
