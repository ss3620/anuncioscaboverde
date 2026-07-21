/*!
 * Favorite Items Plugin — front-end JS
 * Uses jQuery (bundled with Osclass) to talk to the AJAX endpoint.
 */
(function ($) {
    'use strict';

    // Resolve config from the injected global when available, otherwise fall
    // back to per-button data-* attributes. This keeps favoriting working even
    // if the inline window.FavoriteItems script was not printed on the page.
    function resolveConfig($btn) {
        var g = window.FavoriteItems || {};
        var base = (typeof window.baseDir === 'string' && window.baseDir) ? window.baseDir : '/';
        var cfg = {
            ajaxUrl: g.ajaxUrl,
            userId: g.userId,
            loginUrl: g.loginUrl,
            labels: g.labels || {}
        };

        if ($btn && $btn.length) {
            if (!cfg.ajaxUrl) cfg.ajaxUrl = $btn.attr('data-fav-ajax');
            if (typeof cfg.userId === 'undefined' && typeof $btn.attr('data-fav-logged') !== 'undefined') {
                cfg.userId = parseInt($btn.attr('data-fav-logged'), 10);
            }
            if (!cfg.loginUrl) cfg.loginUrl = $btn.attr('data-fav-login');
        }

        if (!cfg.ajaxUrl) cfg.ajaxUrl = base + 'index.php?page=ajax&action=custom&route=favorite-items-toggle';
        if (!cfg.loginUrl) cfg.loginUrl = base + 'index.php?page=login';
        if (typeof cfg.userId === 'undefined') cfg.userId = 0;
        if (!cfg.labels.add) cfg.labels.add = 'Add to favorites';
        if (!cfg.labels.added) cfg.labels.added = 'Saved';
        if (!cfg.labels.loginRequired) cfg.labels.loginRequired = 'Please log in to save favorites.';
        return cfg;
    }

    function showToast(msg, isError) {
        var $t = $('<div class="fi-toast"></div>').text(msg);
        if (isError) $t.addClass('fi-toast--error');
        $('body').append($t);
        // force reflow to run transition
        void $t[0].offsetWidth;
        $t.addClass('is-visible');
        setTimeout(function () {
            $t.removeClass('is-visible');
            setTimeout(function () { $t.remove(); }, 400);
        }, 2200);
    }

    function updateButton($btn, favorited, itemCount, labels) {
        $btn.toggleClass('is-active', favorited);
        $btn.toggleClass('is_favorite', favorited);
        $btn.addClass('just-toggled');
        setTimeout(function () { $btn.removeClass('just-toggled'); }, 500);

        var $label = $btn.find('.favorite-items-label');
        if ($label.length && labels) {
            $label.text(favorited ? labels.added : labels.add);
        }
        var $count = $btn.find('.favorite-items-count');
        if ($count.length && typeof itemCount === 'number') {
            $count.text(itemCount);
        }
    }

    function toggleFavorite($btn) {
        var itemId = parseInt($btn.data('item-id'), 10);
        if (!itemId) return;

        var cfg = resolveConfig($btn);

        if (!cfg.userId) {
            showToast(cfg.labels.loginRequired, true);
            setTimeout(function () {
                if (cfg.loginUrl) window.location.href = cfg.loginUrl;
            }, 900);
            return;
        }

        if ($btn.hasClass('is-loading')) return;
        $btn.addClass('is-loading');

        $.ajax({
            url: cfg.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: { item_id: itemId, fav_action: 'toggle' }
        }).done(function (data) {
            if (!data || !data.ok) {
                showToast((data && data.message) || 'Something went wrong.', true);
                return;
            }

            // Update every button pointing at this item on the page
            $('.favorite-items-btn[data-item-id="' + itemId + '"]').each(function () {
                updateButton($(this), data.favorited, data.item_count, cfg.labels);
            });

            // If this button is on a "My Favorites" card and we just removed it, animate the card out
            if ($btn.data('remove-card') && !data.favorited) {
                var $card = $btn.closest('.favorite-items-card');
                $card.addClass('is-removing');
                setTimeout(function () {
                    $card.remove();
                    // Update on-page counter (page subtitle)
                    var $counter = $('[data-testid="favorites-page-count"] strong');
                    if ($counter.length) {
                        var n = Math.max(0, parseInt($counter.text(), 10) - 1);
                        $counter.text(n);
                        if (n === 0) { location.reload(); }
                    }
                }, 320);
            }

            // Update menu badge if present
            var $badge = $('[data-testid="favorites-menu-count"]');
            if ($badge.length && typeof data.user_count === 'number') {
                $badge.text(data.user_count);
            }

            showToast(data.favorited ? 'Added to favorites' : 'Removed from favorites', false);
        }).fail(function (xhr) {
            var msg = 'Network error. Please try again.';
            try {
                var res = JSON.parse(xhr.responseText);
                if (res && res.message) msg = res.message;
                if (res && res.error === 'login_required') {
                    showToast(msg, true);
                    setTimeout(function () {
                        var lu = res.login_url || resolveConfig($btn).loginUrl;
                        if (lu) window.location.href = lu;
                    }, 900);
                    return;
                }
            } catch (e) {}
            showToast(msg, true);
        }).always(function () {
            $btn.removeClass('is-loading');
        });
    }

    $(document).on('click', '.favorite-items-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggleFavorite($(this));
    });

    // Prevent the mini button from triggering the parent card link
    $(document).on('click', '.favorite-items-btn--mini', function (e) {
        e.preventDefault();
        e.stopPropagation();
    });

    /* -----------------------------------------------------------
     * Home-widget DOM relocator (Delta-theme compatible)
     *
     * The widget is injected via the `footer` hook, so by default
     * it sits at the very bottom of the <body>. Move it into a
     * sensible location inside the theme's main content area
     * so it appears in the home-page flow.
     * ----------------------------------------------------------- */
    $(function () {
        var $widget = $('#fi-home-widget');
        if (!$widget.length) return;
        // Explicit theme placement — bypass relocation.
        if ($widget.data('fi-direct') === 1 || $widget.data('fi-direct') === '1') return;

        // Preferred insertion points in Delta / common Osclass themes.
        // The first selector that returns a match wins.
        var candidates = [
            // Delta theme (current)
            '.home-container.latest',
            '#latest',
            '.home-container.premium',
            // Older / alternate Delta selectors
            'section.latest-items',
            '#latestItems',
            '.premium-content',
            '.latest-listings',
            // Fallbacks used by many Osclass themes
            'main .container:last-of-type',
            'main > .container',
            '#main .container',
            '.main .container',
            'footer'
        ];

        for (var i = 0; i < candidates.length; i++) {
            var $target = $(candidates[i]).first();
            if ($target.length) {
                // Insert AFTER the "latest items" style blocks, otherwise BEFORE the footer.
                if (candidates[i] === 'footer') {
                    $widget.insertBefore($target);
                } else {
                    $widget.insertAfter($target);
                }
                $widget.addClass('is-placed');
                return;
            }
        }
        // No candidate found — leave the widget where it is; it will simply
        // render above the footer at the bottom of the page.
        $widget.addClass('is-placed');
    });

})(jQuery);
