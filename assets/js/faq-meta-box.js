/* global paradiseFaqMb, wp */
(function () {
    'use strict';

    var rows    = document.getElementById( 'paradise-faq-mb-rows' );
    var addBtn  = document.getElementById( 'paradise-faq-mb-add' );
    var counter = paradiseFaqMb.rowCount; // start after existing PHP-rendered rows

    var editorSettings = {
        tinymce: {
            toolbar1: 'bold italic | link | bullist numlist | removeformat',
            toolbar2: '',
            height: 150,
            wpautop: true,
        },
        quicktags: { buttons: 'strong,em,link,ul,ol,li,close' },
        mediaButtons: false,
    };

    // ── DOM helpers ───────────────────────────────────────────────────────────

    function el( tag, cls ) {
        var node = document.createElement( tag );
        if ( cls ) { node.className = cls; }
        return node;
    }

    // ── Build a new repeater row ──────────────────────────────────────────────

    function makeRow( index ) {
        var editorId = 'paradise_faq_a_new_' + index;

        var row = el( 'div', 'paradise-faq-mb-row' );
        row.dataset.editorId = editorId;

        // — Header —
        var header    = el( 'div', 'paradise-faq-mb-row-header' );
        var num       = el( 'span', 'paradise-faq-mb-num' );
        num.textContent = index + 1;
        var preview   = el( 'span', 'paradise-faq-mb-preview' );
        var removeBtn = el( 'button', 'paradise-faq-mb-remove button-link-delete' );
        removeBtn.type = 'button';
        removeBtn.textContent = paradiseFaqMb.labelRemove;
        header.appendChild( num );
        header.appendChild( preview );
        header.appendChild( removeBtn );

        // — Fields —
        var fields = el( 'div', 'paradise-faq-mb-fields' );

        var pQ   = el( 'p' );
        var lblQ = el( 'label' );
        lblQ.textContent = paradiseFaqMb.labelQuestion;
        var inputQ = el( 'input', 'widefat paradise-faq-mb-q' );
        inputQ.type = 'text';
        inputQ.name = 'paradise_faq_q[]';
        pQ.appendChild( lblQ );
        pQ.appendChild( inputQ );

        var pA       = el( 'p', 'paradise-faq-mb-answer-wrap' );
        var lblA     = el( 'label' );
        lblA.textContent = paradiseFaqMb.labelAnswer;
        var textarea = el( 'textarea', 'widefat' );
        textarea.id   = editorId;
        textarea.name = 'paradise_faq_a[]';
        textarea.rows = 5;
        pA.appendChild( lblA );
        pA.appendChild( textarea );

        fields.appendChild( pQ );
        fields.appendChild( pA );

        row.appendChild( header );
        row.appendChild( fields );
        return row;
    }

    // ── Renumber all rows ─────────────────────────────────────────────────────

    function renumber() {
        rows.querySelectorAll( '.paradise-faq-mb-row' ).forEach( function ( row, i ) {
            var num = row.querySelector( '.paradise-faq-mb-num' );
            if ( num ) { num.textContent = i + 1; }
        } );
    }

    // ── Add row ───────────────────────────────────────────────────────────────

    if ( addBtn ) {
        addBtn.addEventListener( 'click', function () {
            var row      = makeRow( counter );
            var editorId = row.dataset.editorId;
            rows.appendChild( row );

            // Initialize TinyMCE after the textarea is in the DOM.
            if ( window.wp && wp.editor ) {
                wp.editor.initialize( editorId, editorSettings );
            }

            counter++;
        } );
    }

    // ── Remove row & live preview ─────────────────────────────────────────────

    if ( rows ) {
        rows.addEventListener( 'click', function ( e ) {
            var btn = e.target.closest( '.paradise-faq-mb-remove' );
            if ( ! btn ) { return; }

            var row      = btn.closest( '.paradise-faq-mb-row' );
            var editorId = row.dataset.editorId;

            // Tear down TinyMCE before removing the element from the DOM.
            if ( editorId && window.wp && wp.editor ) {
                wp.editor.remove( editorId );
            }

            row.remove();
            renumber();
        } );

        // Update preview text while typing the question
        rows.addEventListener( 'input', function ( e ) {
            if ( e.target.classList.contains( 'paradise-faq-mb-q' ) ) {
                var preview = e.target.closest( '.paradise-faq-mb-row' ).querySelector( '.paradise-faq-mb-preview' );
                if ( preview ) {
                    preview.textContent = e.target.value.substring( 0, 60 );
                }
            }
        } );
    }
}());
