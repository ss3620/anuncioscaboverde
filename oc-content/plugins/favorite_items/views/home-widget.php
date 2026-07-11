<?php
/**
 * Home-page widget: "Most favorited listings by users".
 * Rendered by favorite_items_render_home_widget() on the home page.
 *
 * Expected variables from parent scope:
 *   $items    array — list of item rows (from ModelFavorites::topFavoritedItemsFull())
 *   $title    string
 *   $subtitle string
 *   $icon     string ('heart' or 'star')
 *   $userId   int    (0 when guest)
 *   $direct   bool   (optional; true when rendered by an explicit theme hook)
 */
if (!defined('ABS_PATH')) { exit('ABS_PATH is not loaded. Direct access is not allowed.'); }

$fi_direct = isset($direct) && $direct ? '1' : '0';
$fi_showCount = (bool) osc_get_preference('show_count', 'favorite_items');
?>
<section id="fi-home-widget"
         class="fi-home-widget"
         data-fi-direct="<?php echo $fi_direct; ?>"
         data-testid="fi-home-widget"
         aria-labelledby="fi-home-widget-title">
    <div class="fi-home-widget__container">
        <header class="fi-home-widget__header">
            <div class="fi-home-widget__heading">
                <span class="favorite-items-icon favorite-items-icon--<?php echo osc_esc_html($icon); ?>" aria-hidden="true"></span>
                <h2 id="fi-home-widget-title" class="fi-home-widget__title" data-testid="fi-home-widget-title">
                    <?php echo osc_esc_html($title); ?>
                </h2>
            </div>
            <?php if (!empty($subtitle)): ?>
                <p class="fi-home-widget__subtitle" data-testid="fi-home-widget-subtitle">
                    <?php echo osc_esc_html($subtitle); ?>
                </p>
            <?php endif; ?>
        </header>

        <div class="row fi-home-widget__grid" data-testid="fi-home-widget-grid">
            <?php foreach ($items as $fi_row):
                $fi_itemId = (int) $fi_row['pk_i_id'];

                // Prepare item for Osclass helpers (osc_item_url, osc_format_price, etc.)
                View::newInstance()->_exportVariableToView('item', $fi_row);

                $fi_url   = osc_item_url();
                $fi_title = $fi_row['s_title'] ?: ('Item #' . $fi_itemId);
                $fi_favCount = (int) $fi_row['favorites_count'];
                $fi_isFav = $userId
                    ? ModelFavorites::newInstance()->isFavorite($userId, $fi_itemId)
                    : false;

                // Main image (thumbnail)
                $fi_res = ItemResource::newInstance()->getResource($fi_itemId);
                $fi_img = null;
                if (!empty($fi_res) && !empty($fi_res[0]['pk_i_id'])) {
                    $fi_img = osc_apply_filter(
                        'resource_url_thumbnail',
                        osc_base_url() . 'oc-content/uploads/' . $fi_res[0]['pk_i_id'] . '/' . $fi_res[0]['s_name'] . '_thumbnail.' . $fi_res[0]['s_extension']
                    );
                }

                // Price
                $fi_price = null;
                if (!empty($fi_row['i_price'])) {
                    $fi_price = osc_format_price($fi_row['i_price'], $fi_row['fk_c_currency_code'] ?? '');
                }

                // Location line
                $fi_loc = trim(($fi_row['s_city'] ?? '') . (!empty($fi_row['s_region']) ? (', ' . $fi_row['s_region']) : ''), ', ');
            ?>
                <article class="col-xs-12 col-sm-6 col-md-4 col-lg-3 fi-home-widget__col"
                         data-testid="fi-home-item-<?php echo $fi_itemId; ?>">
                    <div class="fi-home-card listing-card">
                        <a class="fi-home-card__media" href="<?php echo osc_esc_html($fi_url); ?>"
                           aria-label="<?php echo osc_esc_html($fi_title); ?>">
                            <?php if ($fi_img): ?>
                                <img src="<?php echo osc_esc_html($fi_img); ?>"
                                     alt="<?php echo osc_esc_html($fi_title); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="fi-home-card__noimg">No image</div>
                            <?php endif; ?>

                            <span class="fi-home-card__badge" data-testid="fi-home-item-badge-<?php echo $fi_itemId; ?>">
                                <span class="favorite-items-icon favorite-items-icon--<?php echo osc_esc_html($icon); ?>" aria-hidden="true"></span>
                                <span class="fi-home-card__badge-count"><?php echo $fi_favCount; ?></span>
                            </span>

                            <button type="button"
                                    class="favorite-items-btn favorite-items-btn--mini <?php echo $fi_isFav ? 'is-active' : ''; ?>"
                                    data-item-id="<?php echo $fi_itemId; ?>"
                                    data-testid="fi-home-toggle-<?php echo $fi_itemId; ?>"
                                    title="<?php echo osc_esc_html(osc_get_preference('button_label', 'favorite_items') ?: 'Add to favorites'); ?>">
                                <span class="favorite-items-icon favorite-items-icon--<?php echo osc_esc_html($icon); ?>" aria-hidden="true"></span>
                                <?php if ($fi_showCount): ?>
                                    <span class="favorite-items-count" data-testid="fi-home-count-<?php echo $fi_itemId; ?>"><?php echo $fi_favCount; ?></span>
                                <?php endif; ?>
                            </button>
                        </a>

                        <div class="fi-home-card__body">
                            <h3 class="fi-home-card__title">
                                <a href="<?php echo osc_esc_html($fi_url); ?>"
                                   data-testid="fi-home-item-title-<?php echo $fi_itemId; ?>">
                                    <?php echo osc_esc_html($fi_title); ?>
                                </a>
                            </h3>

                            <div class="fi-home-card__meta">
                                <?php if ($fi_price !== null): ?>
                                    <span class="fi-home-card__price" data-testid="fi-home-item-price-<?php echo $fi_itemId; ?>">
                                        <?php echo osc_esc_html($fi_price); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($fi_loc)): ?>
                                    <span class="fi-home-card__loc"><?php echo osc_esc_html($fi_loc); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
