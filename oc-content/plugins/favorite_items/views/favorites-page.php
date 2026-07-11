<?php
/**
 * "My Favorites" user page.
 * Requires login. Lists all items the user has favorited.
 */
if (!defined('ABS_PATH')) { exit('ABS_PATH is not loaded. Direct access is not allowed.'); }

if (!osc_is_web_user_logged_in()) {
    osc_add_flash_warning_message('Please log in to access your favorites.');
    header('Location: ' . osc_user_login_url());
    exit;
}

$userId = (int) osc_logged_user_id();
$favorites = ModelFavorites::newInstance()->getUserFavorites($userId, 200, 0);

// Render page in the current theme
osc_current_web_theme_path('header.php');

$icon      = osc_get_preference('icon', 'favorite_items') ?: 'heart';
$iconColor = osc_get_preference('icon_color', 'favorite_items') ?: '#e11d48';
?>
<main class="favorite-items-page" style="--fi-color: <?php echo osc_esc_html($iconColor); ?>;">
    <div class="favorite-items-page__container">
        <header class="favorite-items-page__header">
            <h1 class="favorite-items-page__title" data-testid="favorites-page-title">
                <span class="favorite-items-icon favorite-items-icon--<?php echo osc_esc_html($icon); ?>" aria-hidden="true"></span>
                My Favorites
            </h1>
            <p class="favorite-items-page__subtitle" data-testid="favorites-page-count">
                You have <strong><?php echo count($favorites); ?></strong> favorited item<?php echo count($favorites) === 1 ? '' : 's'; ?>.
            </p>
        </header>

        <?php if (empty($favorites)): ?>
            <div class="favorite-items-empty" data-testid="favorites-empty-state">
                <div class="favorite-items-empty__icon favorite-items-icon--<?php echo osc_esc_html($icon); ?>"></div>
                <h2>No favorites yet</h2>
                <p>Browse listings and tap the <em><?php echo osc_esc_html($icon); ?></em> icon to save them here for later.</p>
                <a href="<?php echo osc_esc_html(osc_search_url()); ?>" class="favorite-items-cta" data-testid="favorites-browse-listings">Browse listings</a>
            </div>
        <?php else: ?>
            <ul class="favorite-items-grid" data-testid="favorites-grid">
                <?php foreach ($favorites as $fav):
                    View::newInstance()->_exportVariableToView('item', $fav);
                    $itemUrl = osc_item_url();
                    $resource = ItemResource::newInstance()->getResource($fav['pk_i_id']);
                    $img = null;
                    if (!empty($resource) && !empty($resource[0]['pk_i_id'])) {
                        $img = osc_apply_filter('resource_url_thumbnail',
                            osc_base_url() . 'oc-content/uploads/' . $resource[0]['pk_i_id'] . '/' . $resource[0]['s_name'] . '_thumbnail.' . $resource[0]['s_extension']
                        );
                    }
                ?>
                    <li class="favorite-items-card" data-testid="favorite-card-<?php echo (int) $fav['pk_i_id']; ?>">
                        <a class="favorite-items-card__media" href="<?php echo osc_esc_html($itemUrl); ?>">
                            <?php if ($img): ?>
                                <img src="<?php echo osc_esc_html($img); ?>" alt="<?php echo osc_esc_html($fav['s_title'] ?? ''); ?>" loading="lazy">
                            <?php else: ?>
                                <div class="favorite-items-card__noimg">No image</div>
                            <?php endif; ?>
                        </a>
                        <div class="favorite-items-card__body">
                            <h3 class="favorite-items-card__title">
                                <a href="<?php echo osc_esc_html($itemUrl); ?>" data-testid="favorite-card-title-<?php echo (int) $fav['pk_i_id']; ?>">
                                    <?php echo osc_esc_html($fav['s_title'] ?? ('Item #' . $fav['pk_i_id'])); ?>
                                </a>
                            </h3>
                            <div class="favorite-items-card__meta">
                                <?php if (!empty($fav['i_price'])): ?>
                                    <span class="favorite-items-card__price">
                                        <?php echo osc_esc_html(osc_format_price($fav['i_price'], $fav['fk_c_currency_code'] ?? '')); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="favorite-items-card__added">
                                    Saved <?php echo osc_esc_html(date('M j, Y', strtotime($fav['dt_favorited']))); ?>
                                </span>
                            </div>
                            <div class="favorite-items-card__actions">
                                <a href="<?php echo osc_esc_html($itemUrl); ?>" class="favorite-items-card__view" data-testid="favorite-card-view-<?php echo (int) $fav['pk_i_id']; ?>">View listing</a>
                                <button type="button"
                                        class="favorite-items-card__remove favorite-items-btn is-active"
                                        data-item-id="<?php echo (int) $fav['pk_i_id']; ?>"
                                        data-remove-card="1"
                                        data-testid="favorite-card-remove-<?php echo (int) $fav['pk_i_id']; ?>">
                                    <span class="favorite-items-icon favorite-items-icon--<?php echo osc_esc_html($icon); ?>" aria-hidden="true"></span>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>
<?php osc_current_web_theme_path('footer.php'); ?>
