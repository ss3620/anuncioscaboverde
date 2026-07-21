<?php
/**
 * Favorite button embedded on the item detail page.
 * Uses $item (from osc_item()) from the parent scope.
 */
if (!defined('ABS_PATH')) { exit('ABS_PATH is not loaded. Direct access is not allowed.'); }

$itemId = (int) $item['pk_i_id'];
$count  = ModelFavorites::newInstance()->countByItem($itemId);
$isFav  = osc_is_web_user_logged_in()
    ? ModelFavorites::newInstance()->isFavorite((int) osc_logged_user_id(), $itemId)
    : false;

$icon      = osc_get_preference('icon', 'favorite_items') ?: 'heart';
$iconSize  = (int) (osc_get_preference('icon_size', 'favorite_items') ?: 24);
$iconColor = osc_get_preference('icon_color', 'favorite_items') ?: '#e11d48';
$labelAdd  = osc_get_preference('button_label', 'favorite_items') ?: 'Add to favorites';
$labelOn   = osc_get_preference('button_label_active', 'favorite_items') ?: 'Saved';
$showCount = (bool) osc_get_preference('show_count', 'favorite_items');
// Delta theme CSS targets .fi_save_favorite / .is_favorite on listing cards
$deltaClasses = 'fi_save_favorite' . ($isFav ? ' is_favorite' : '');
// Self-contained config so the button works even if the inline
// window.FavoriteItems global was not injected on this page.
$favAjaxUrl = osc_base_url(true) . '?page=ajax&action=custom&route=favorite-items-toggle';
$favLoggedIn = osc_is_web_user_logged_in() ? 1 : 0;
$favLoginUrl = osc_user_login_url();
?>
<div class="favorite-items-wrap" style="--fi-color: <?php echo osc_esc_html($iconColor); ?>; --fi-size: <?php echo (int) $iconSize; ?>px;">
    <button type="button"
            class="favorite-items-btn <?php echo $deltaClasses; ?> <?php echo $isFav ? 'is-active' : ''; ?>"
            data-testid="favorite-toggle-btn"
            data-item-id="<?php echo $itemId; ?>"
            data-fav-ajax="<?php echo osc_esc_html($favAjaxUrl); ?>"
            data-fav-logged="<?php echo (int) $favLoggedIn; ?>"
            data-fav-login="<?php echo osc_esc_html($favLoginUrl); ?>">
        <span class="favorite-items-icon favorite-items-icon--<?php echo osc_esc_html($icon); ?>" aria-hidden="true"></span>
        <span class="favorite-items-label" data-testid="favorite-toggle-label">
            <?php echo osc_esc_html($isFav ? $labelOn : $labelAdd); ?>
        </span>
        <?php if ($showCount): ?>
            <span class="favorite-items-count" data-testid="favorite-item-count"><?php echo (int) $count; ?></span>
        <?php endif; ?>
    </button>
</div>
