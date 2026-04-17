/**
 * Announcement Bar — Paradise Elementor Widgets
 *
 * Handles dismiss logic for each .paradise-ab-wrap on the page.
 * Storage keys use the prefix 'paradise-ab-' + bar id.
 *
 * dismiss_duration values:
 *   'session' — sessionStorage flag
 *   'days'    — localStorage JSON { expires: timestamp }
 *   'forever' — localStorage string 'forever'
 */
(function () {
    'use strict';

    var STORAGE_PREFIX = 'paradise-ab-';

    /**
     * Return true if the bar with this id has been dismissed.
     */
    function isDismissed(id) {
        var key = STORAGE_PREFIX + id;

        // Forever
        if (localStorage.getItem(key) === 'forever') {
            return true;
        }

        // Days (JSON with expires timestamp)
        var stored = localStorage.getItem(key);
        if (stored && stored !== 'forever') {
            try {
                var data = JSON.parse(stored);
                if (data && data.expires && Date.now() < data.expires) {
                    return true;
                }
                // Expired — clean up
                localStorage.removeItem(key);
            } catch (e) {
                localStorage.removeItem(key);
            }
        }

        // Session
        if (sessionStorage.getItem(key) === '1') {
            return true;
        }

        return false;
    }

    /**
     * Record dismissal in storage based on duration setting.
     */
    function recordDismissal(id, duration, days) {
        var key = STORAGE_PREFIX + id;

        if (duration === 'forever') {
            localStorage.setItem(key, 'forever');
        } else if (duration === 'days') {
            var expires = Date.now() + (parseInt(days, 10) || 7) * 24 * 60 * 60 * 1000;
            localStorage.setItem(key, JSON.stringify({ expires: expires }));
        } else {
            // session (default)
            sessionStorage.setItem(key, '1');
        }
    }

    /**
     * Initialise all announcement bars on the page.
     */
    function initBars() {
        var bars = document.querySelectorAll('.paradise-ab-wrap');

        bars.forEach(function (wrap) {
            var isEditor   = wrap.getAttribute('data-ab-edit') === 'true';
            var id         = wrap.getAttribute('data-ab-id');
            var duration   = wrap.getAttribute('data-ab-duration') || 'session';
            var days       = wrap.getAttribute('data-ab-days') || '7';
            var closeBtn   = wrap.querySelector('.paradise-ab-close');

            // In editor mode: always visible, no dismiss logic
            if (isEditor) {
                return;
            }

            // Hide if already dismissed
            if (isDismissed(id)) {
                wrap.setAttribute('data-ab-hidden', 'true');
                return;
            }

            // Attach close handler
            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    recordDismissal(id, duration, days);
                    wrap.setAttribute('data-ab-hidden', 'true');
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBars);
    } else {
        initBars();
    }

})();
