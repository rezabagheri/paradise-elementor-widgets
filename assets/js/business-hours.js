/**
 * Paradise Business Hours — client-side open/closed detection
 *
 * Reads hours from data-bh-hours JSON and the site timezone offset from
 * data-bh-tz-offset (minutes), computes the current day/time in the
 * site's timezone, and updates the badge and today highlight accordingly.
 */
(function () {
    'use strict';

    var DAY_SLUGS = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    function initWidget(wrap) {
        var hoursData  = JSON.parse(wrap.getAttribute('data-bh-hours') || '{}');
        var tzOffset   = parseInt(wrap.getAttribute('data-bh-tz-offset') || '0', 10);
        var showBadge  = wrap.getAttribute('data-bh-show-badge') === '1';
        var highlight  = wrap.getAttribute('data-bh-highlight') === '1';

        // Compute current time in the site's timezone.
        var now         = new Date();
        var utcMs       = now.getTime() + now.getTimezoneOffset() * 60000;
        var siteMs      = utcMs + tzOffset * 60000;
        var siteNow     = new Date(siteMs);

        var todaySlug   = DAY_SLUGS[siteNow.getDay()];
        var currentTime = pad(siteNow.getHours()) + ':' + pad(siteNow.getMinutes());

        // Highlight today's row.
        if (highlight) {
            var rows = wrap.querySelectorAll('.paradise-bh-row');
            rows.forEach(function (row) {
                if (row.getAttribute('data-bh-day') === todaySlug) {
                    row.classList.add('paradise-bh-row--today');
                }
            });
        }

        // Update badge.
        if (showBadge) {
            var badge = wrap.querySelector('.paradise-bh-badge');
            if (!badge) return;

            var entry  = hoursData[todaySlug];
            var isOpen = false;

            if (entry && entry.open && entry.from && entry.to) {
                isOpen = currentTime >= entry.from && currentTime <= entry.to;
            }

            badge.classList.remove('paradise-bh-badge--open', 'paradise-bh-badge--closed');
            badge.classList.add(isOpen ? 'paradise-bh-badge--open' : 'paradise-bh-badge--closed');
            badge.textContent = isOpen ? 'Open Now' : 'Closed';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.paradise-bh-wrap').forEach(initWidget);
    });

})();
