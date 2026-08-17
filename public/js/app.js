(function () {
    'use strict';

    function onReady(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    onReady(function () {
        var header = document.getElementById('siteHeader');
        if (header) {
            var onScroll = function () {
                header.classList.toggle('is-scrolled', window.scrollY > 10);
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }

        var navToggle = document.getElementById('navToggle');
        var siteNav = document.getElementById('siteNav');
        if (navToggle && siteNav) {
            navToggle.addEventListener('click', function () {
                var open = siteNav.classList.toggle('is-open');
                document.body.classList.toggle('nav-open', open);
                navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

        var adminToggle = document.getElementById('adminNavToggle');
        var adminSidebar = document.getElementById('adminSidebar');
        if (adminToggle && adminSidebar) {
            adminToggle.addEventListener('click', function () {
                var open = adminSidebar.classList.toggle('is-open');
                adminToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

        document.querySelectorAll('[data-dismiss-alert]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var alert = btn.closest('.alert');
                if (alert) {
                    alert.remove();
                }
            });
        });

        document.querySelectorAll('select[data-reference-field]').forEach(function (select) {
            var targetId = select.getAttribute('data-reference-field');
            var target = document.getElementById(targetId);
            if (!target) {
                return;
            }

            var sync = function () {
                var option = select.options[select.selectedIndex];
                target.value = option && option.getAttribute('data-reference')
                    ? option.getAttribute('data-reference')
                    : '';
            };

            select.addEventListener('change', sync);
            sync();
        });

        var profileMenu = document.getElementById('adminProfileMenu');
        var profileTrigger = document.getElementById('adminProfileTrigger');
        if (profileMenu && profileTrigger) {
            profileTrigger.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = profileMenu.classList.toggle('is-open');
                profileTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
            });

            document.addEventListener('click', function (e) {
                if (!profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('is-open');
                    profileTrigger.setAttribute('aria-expanded', 'false');
                }
            });
        }

        document.querySelectorAll('form[data-role-warning]').forEach(function (form) {
            var roleSelect = form.querySelector('select[name="role"]');
            if (!roleSelect) {
                return;
            }

            form.addEventListener('submit', function (e) {
                var original = roleSelect.getAttribute('data-role-original');
                if (original && roleSelect.value !== original) {
                    var message = form.getAttribute('data-role-warning');
                    var confirmed = window.confirm(message);
                    if (!confirmed) {
                        e.preventDefault();
                    }
                }
            });
        });
    });
})();
