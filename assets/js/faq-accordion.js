(function () {
    'use strict';

    function initFaq(wrap) {
        if (!wrap || wrap.dataset.faqInit) return;
        wrap.dataset.faqInit = '1';

        var mode = wrap.dataset.faqMode || 'accordion';

        wrap.addEventListener('click', function (e) {
            var question = e.target.closest('.paradise-faq-question');
            if (!question || !wrap.contains(question)) return;
            toggle(wrap, question, mode);
        });

        wrap.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            var question = e.target.closest('.paradise-faq-question');
            if (!question || !wrap.contains(question)) return;
            e.preventDefault();
            toggle(wrap, question, mode);
        });
    }

    function toggle(wrap, question, mode) {
        var item   = question.closest('.paradise-faq-item');
        var isOpen = item.classList.contains('paradise-faq-item--open');

        if (mode === 'accordion') {
            wrap.querySelectorAll('.paradise-faq-item--open').forEach(function (el) {
                el.classList.remove('paradise-faq-item--open');
                var q = el.querySelector('.paradise-faq-question');
                if (q) q.setAttribute('aria-expanded', 'false');
            });
            if (!isOpen) {
                item.classList.add('paradise-faq-item--open');
                question.setAttribute('aria-expanded', 'true');
            }
        } else {
            item.classList.toggle('paradise-faq-item--open', !isOpen);
            question.setAttribute('aria-expanded', String(!isOpen));
        }
    }

    function initAll() {
        document.querySelectorAll('.paradise-faq-wrap').forEach(initFaq);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Elementor editor: re-init when widget is rendered or settings change
    if (typeof window.elementorFrontend !== 'undefined') {
        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/paradise_faq_accordion.default',
            function ($el) {
                var wrap = $el[0] && $el[0].querySelector('.paradise-faq-wrap');
                if (wrap) {
                    delete wrap.dataset.faqInit;
                    initFaq(wrap);
                }
            }
        );
    }
})();
