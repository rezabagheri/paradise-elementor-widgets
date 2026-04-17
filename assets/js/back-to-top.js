/**
 * Back to Top Button — Paradise Elementor Widgets
 *
 * Shows the button after scrolling past the configured threshold.
 * Smooth-scrolls to the top on click.
 */
(function () {
    'use strict';

    var VISIBLE_CLASS = 'paradise-btt-btn--visible';

    function initButtons() {
        var buttons = document.querySelectorAll('.paradise-btt-btn');

        buttons.forEach(function (btn) {
            var isEditor  = btn.getAttribute('data-btt-edit') === 'true';
            var threshold = parseInt(btn.getAttribute('data-btt-threshold'), 10) || 300;

            // In editor: always show, no scroll or click logic
            if (isEditor) {
                btn.classList.add(VISIBLE_CLASS);
                return;
            }

            // Set initial visibility
            updateVisibility(btn, threshold);

            // Scroll listener (passive, RAF-throttled)
            var ticking = false;
            window.addEventListener('scroll', function () {
                if (!ticking) {
                    requestAnimationFrame(function () {
                        updateVisibility(btn, threshold);
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });

            // Click: smooth scroll to top
            btn.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    function updateVisibility(btn, threshold) {
        if (window.scrollY > threshold) {
            btn.classList.add(VISIBLE_CLASS);
        } else {
            btn.classList.remove(VISIBLE_CLASS);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initButtons);
    } else {
        initButtons();
    }

})();
