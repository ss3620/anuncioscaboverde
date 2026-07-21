<?php
/*
Plugin Name: Favorite Items
Plugin URI: https://github.com/emergent/osclass-favorite-items
Description: Allows registered users to favorite listings they like, get a better overview and see them any time they return to your classifieds. Includes a "My Favorites" page for users and an admin dashboard with statistics.
Version: 1.1.2
Author: Emergent Labs
Author URI: https://emergent.sh
Short Name: favorite_items
Plugin update URI: favorite_items
*/

if (!defined('ABS_PATH')) {
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

/* ------------------------------------------------------------------
 * CONSTANTS
 * ------------------------------------------------------------------ */
define('FAVORITE_ITEMS_VERSION', '1.1.2');
define('FAVORITE_ITEMS_PATH', osc_plugins_path() . 'favorite_items/');
define('FAVORITE_ITEMS_URL',  osc_plugins_url()  . 'favorite_items/');
define('FAVORITE_ITEMS_FOLDER', 'favorite_items/');

require_once FAVORITE_ITEMS_PATH . 'ModelFavorites.php';
require_once FAVORITE_ITEMS_PATH . 'delta-compat.php';

/* ------------------------------------------------------------------
 * INSTALL / UNINSTALL
 * ------------------------------------------------------------------ */
function favorite_items_install()
{
    $conn = DBConnectionClass::newInstance()->getOsclassDb();
    $comm = new DBCommandClass($conn);

    $prefix = DB_TABLE_PREFIX;

    // Favorites table
    $sql = "CREATE TABLE IF NOT EXISTS {$prefix}t_item_favorite (
        fk_i_item_id INT UNSIGNED NOT NULL,
        fk_i_user_id INT UNSIGNED NOT NULL,
        dt_added DATETIME NOT NULL,
        PRIMARY KEY (fk_i_item_id, fk_i_user_id),
        INDEX idx_user (fk_i_user_id),
        INDEX idx_item (fk_i_item_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $comm->query($sql);

    // Default settings
    if (osc_get_preference('icon', 'favorite_items') === '') {
        osc_set_preference('icon', 'heart', 'favorite_items', 'STRING');
    }
    if (osc_get_preference('icon_color', 'favorite_items') === '') {
        osc_set_preference('icon_color', '#e11d48', 'favorite_items', 'STRING');
    }
    if (osc_get_preference('icon_size', 'favorite_items') === '') {
        osc_set_preference('icon_size', '24', 'favorite_items', 'INTEGER');
    }
    if (osc_get_preference('show_count', 'favorite_items') === '') {
        osc_set_preference('show_count', '1', 'favorite_items', 'BOOLEAN');
    }
    if (osc_get_preference('button_label', 'favorite_items') === '') {
        osc_set_preference('button_label', 'Add to favorites', 'favorite_items', 'STRING');
    }
    if (osc_get_preference('button_label_active', 'favorite_items') === '') {
        osc_set_preference('button_label_active', 'Saved', 'favorite_items', 'STRING');
    }
    if (osc_get_preference('home_widget_enabled', 'favorite_items') === '') {
        osc_set_preference('home_widget_enabled', '1', 'favorite_items', 'BOOLEAN');
    }
    if (osc_get_preference('home_widget_title', 'favorite_items') === '') {
        osc_set_preference('home_widget_title', 'Most favorited listings by users', 'favorite_items', 'STRING');
    }
    if (osc_get_preference('home_widget_subtitle', 'favorite_items') === '') {
        osc_set_preference('home_widget_subtitle', 'The listings our community loves the most right now', 'favorite_items', 'STRING');
    }
    if (osc_get_preference('home_widget_limit', 'favorite_items') === '') {
        osc_set_preference('home_widget_limit', '8', 'favorite_items', 'INTEGER');
    }
    if (osc_get_preference('home_widget_min_favorites', 'favorite_items') === '') {
        osc_set_preference('home_widget_min_favorites', '1', 'favorite_items', 'INTEGER');
    }
}

function favorite_items_uninstall()
{
    $conn = DBConnectionClass::newInstance()->getOsclassDb();
    $comm = new DBCommandClass($conn);
    $prefix = DB_TABLE_PREFIX;

    $comm->query("DROP TABLE IF EXISTS {$prefix}t_item_favorite;");

    // Remove preferences
    $comm->delete($prefix . 't_preference', array('s_section' => 'favorite_items'));
}

/* ------------------------------------------------------------------
 * ROUTES
 *   /user/favorites  -> user "My Favorites" page (must be logged in)
 * ------------------------------------------------------------------ */
osc_add_route(
    'favorite-items-user-favorites',
    'user/favorites',
    'user/favorites',
    FAVORITE_ITEMS_FOLDER . 'views/favorites-page.php'
);

/* ------------------------------------------------------------------
 * AJAX ENDPOINT
 *   URL: index.php?page=ajax&action=custom&route=favorite-items-toggle
 * ------------------------------------------------------------------ */
osc_add_route(
    'favorite-items-toggle',
    'favorites/toggle',
    'favorites/toggle',
    FAVORITE_ITEMS_FOLDER . 'ajax.php'
);

/* ------------------------------------------------------------------
 * HOOKS
 * ------------------------------------------------------------------ */

// Register plugin install/uninstall
osc_register_plugin(osc_plugin_path(__FILE__), 'favorite_items_install');
osc_add_hook('uninstall_' . osc_plugin_path(__FILE__), 'favorite_items_uninstall');

// Enqueue assets on public pages
function favorite_items_enqueue()
{
    osc_enqueue_style('favorite-items-css', FAVORITE_ITEMS_URL . 'assets/css/favorite.css?v=' . FAVORITE_ITEMS_VERSION);
    osc_enqueue_script('jquery');
    osc_register_script('favorite-items-js', FAVORITE_ITEMS_URL . 'assets/js/favorite.js?v=' . FAVORITE_ITEMS_VERSION, 'jquery');
    osc_enqueue_script('favorite-items-js');
}
osc_add_hook('header', 'favorite_items_enqueue');

// Inject globals for the JS (endpoint URL, current user id, translations)
function favorite_items_inject_globals()
{
    $ajax_url = osc_base_url(true) . '?page=custom&route=favorite-items-toggle';
    $user_id  = osc_is_web_user_logged_in() ? (int) osc_logged_user_id() : 0;
    $login_url = osc_user_login_url();
    $label     = osc_esc_js(osc_get_preference('button_label', 'favorite_items') ?: 'Add to favorites');
    $labelOn   = osc_esc_js(osc_get_preference('button_label_active', 'favorite_items') ?: 'Saved');
    ?>
    <script>
    window.FavoriteItems = {
        ajaxUrl: <?php echo json_encode($ajax_url); ?>,
        userId: <?php echo (int) $user_id; ?>,
        loginUrl: <?php echo json_encode($login_url); ?>,
        labels: {
            add: <?php echo json_encode($label); ?>,
            added: <?php echo json_encode($labelOn); ?>,
            loginRequired: "Please log in to save favorites."
        }
    };
    </script>
    <?php
}
osc_add_hook('header', 'favorite_items_inject_globals', 20);

// Show the favorite button on item detail page
function favorite_items_item_detail_button()
{
    // Delta already renders via del_make_favorite() -> fi_save_favorite()
    if (function_exists('del_make_favorite')) {
        return;
    }
    $item = osc_item();
    if (!$item || empty($item['pk_i_id'])) return;
    include FAVORITE_ITEMS_PATH . 'views/favorite-button.php';
}
osc_add_hook('item_detail', 'favorite_items_item_detail_button');

// Show a small heart on the item listing (search results)
function favorite_items_search_item_button()
{
    // Delta cards already call del_make_favorite() / fi_save_favorite()
    if (function_exists('del_make_favorite')) {
        return;
    }
    $item = osc_item();
    if (!$item || empty($item['pk_i_id'])) return;
    $count = ModelFavorites::newInstance()->countByItem((int) $item['pk_i_id']);
    $isFav = osc_is_web_user_logged_in()
        ? ModelFavorites::newInstance()->isFavorite((int) osc_logged_user_id(), (int) $item['pk_i_id'])
        : false;
    $icon = osc_get_preference('icon', 'favorite_items') ?: 'heart';
    ?>
    <button type="button"
            class="favorite-items-btn favorite-items-btn--mini <?php echo $isFav ? 'is-active' : ''; ?>"
            data-testid="favorite-toggle-mini-<?php echo (int) $item['pk_i_id']; ?>"
            data-item-id="<?php echo (int) $item['pk_i_id']; ?>"
            title="<?php echo osc_esc_html(osc_get_preference('button_label', 'favorite_items') ?: 'Add to favorites'); ?>">
        <span class="favorite-items-icon favorite-items-icon--<?php echo osc_esc_html($icon); ?>" aria-hidden="true"></span>
        <?php if (osc_get_preference('show_count', 'favorite_items')): ?>
            <span class="favorite-items-count" data-testid="favorite-count-mini-<?php echo (int) $item['pk_i_id']; ?>"><?php echo (int) $count; ?></span>
        <?php endif; ?>
    </button>
    <?php
}
osc_add_hook('search_item', 'favorite_items_search_item_button');

// Add "Favorites" entry to the user account menu
function favorite_items_user_menu()
{
    if (!osc_is_web_user_logged_in()) return;
    $count = ModelFavorites::newInstance()->countByUser((int) osc_logged_user_id());
    $url = osc_route_url('favorite-items-user-favorites');
    ?>
    <li class="favorite-items-menu-item">
        <a href="<?php echo osc_esc_html($url); ?>" data-testid="favorites-menu-link">
            <span class="favorite-items-icon favorite-items-icon--<?php echo osc_esc_html(osc_get_preference('icon', 'favorite_items') ?: 'heart'); ?>" aria-hidden="true"></span>
            My Favorites
            <span class="favorite-items-badge" data-testid="favorites-menu-count"><?php echo (int) $count; ?></span>
        </a>
    </li>
    <?php
}
osc_add_hook('user_menu', 'favorite_items_user_menu');

/* ------------------------------------------------------------------
 * HOME PAGE WIDGET — "Most favorited listings by users"
 * ------------------------------------------------------------------ */
function favorite_items_render_home_widget()
{
    // Only render on the home page and only if enabled
    if (!function_exists('osc_is_home_page') || !osc_is_home_page()) return;
    if (!osc_get_preference('home_widget_enabled', 'favorite_items')) return;

    // Delta already renders the native "Most favorited" block via main.php when
    // fi_most_favorited_items() exists — skip footer inject to avoid duplicates.
    if (function_exists('del_draw_item') && function_exists('del_param') && del_param('favorite_home') == 1) {
        return;
    }

    $limit = (int) (osc_get_preference('home_widget_limit', 'favorite_items') ?: 8);
    $minFav = (int) (osc_get_preference('home_widget_min_favorites', 'favorite_items') ?: 1);
    $items = ModelFavorites::newInstance()->topFavoritedItemsFull($limit);

    if (empty($items)) return;

    // Filter by min favorites threshold
    $items = array_values(array_filter($items, function ($it) use ($minFav) {
        return (int) $it['favorites_count'] >= $minFav;
    }));
    if (empty($items)) return;

    $title    = osc_get_preference('home_widget_title', 'favorite_items') ?: 'Most favorited listings by users';
    $subtitle = osc_get_preference('home_widget_subtitle', 'favorite_items') ?: '';
    $icon     = osc_get_preference('icon', 'favorite_items') ?: 'heart';
    $userId   = osc_is_web_user_logged_in() ? (int) osc_logged_user_id() : 0;

    include FAVORITE_ITEMS_PATH . 'views/home-widget.php';
}
// Fallback for non-Delta themes: inject via footer + JS relocator.
osc_add_hook('footer', 'favorite_items_render_home_widget', 5);

// A dedicated hook so theme authors can drop
//   osc_run_hook('favorite_items_home_widget');
// directly inside their home.php for a native (non-JS-relocated) placement.
osc_add_hook('favorite_items_home_widget', 'favorite_items_render_home_widget_direct');
function favorite_items_render_home_widget_direct()
{
    // Same as the auto-injected widget but bypasses the home-page gate,
    // so themes can place it explicitly.
    if (!osc_get_preference('home_widget_enabled', 'favorite_items')) return;
    $limit = (int) (osc_get_preference('home_widget_limit', 'favorite_items') ?: 8);
    $minFav = (int) (osc_get_preference('home_widget_min_favorites', 'favorite_items') ?: 1);
    $items = ModelFavorites::newInstance()->topFavoritedItemsFull($limit);
    if (empty($items)) return;
    $items = array_values(array_filter($items, function ($it) use ($minFav) {
        return (int) $it['favorites_count'] >= $minFav;
    }));
    if (empty($items)) return;

    $title    = osc_get_preference('home_widget_title', 'favorite_items') ?: 'Most favorited listings by users';
    $subtitle = osc_get_preference('home_widget_subtitle', 'favorite_items') ?: '';
    $icon     = osc_get_preference('icon', 'favorite_items') ?: 'heart';
    $userId   = osc_is_web_user_logged_in() ? (int) osc_logged_user_id() : 0;
    $direct   = true;
    include FAVORITE_ITEMS_PATH . 'views/home-widget.php';
}

// When an item is deleted, remove any favorites pointing to it (safety net)
function favorite_items_after_item_delete($item)
{
    if (!empty($item['pk_i_id'])) {
        ModelFavorites::newInstance()->deleteByItem((int) $item['pk_i_id']);
    }
}
osc_add_hook('delete_item', 'favorite_items_after_item_delete');

// When a user is deleted, clear their favorites
function favorite_items_after_user_delete($user)
{
    if (!empty($user['pk_i_id'])) {
        ModelFavorites::newInstance()->deleteByUser((int) $user['pk_i_id']);
    }
}
osc_add_hook('delete_user', 'favorite_items_after_user_delete');

/* ------------------------------------------------------------------
 * ADMIN MENU
 * ------------------------------------------------------------------ */
function favorite_items_admin_menu()
{
    echo '<h3><a href="#">Favorite Items</a></h3>
          <ul>
            <li><a href="' . osc_admin_render_plugin_url(FAVORITE_ITEMS_FOLDER . 'admin/settings.php') . '" data-testid="admin-favorites-settings">Settings</a></li>
            <li><a href="' . osc_admin_render_plugin_url(FAVORITE_ITEMS_FOLDER . 'admin/stats.php')    . '" data-testid="admin-favorites-stats">Statistics</a></li>
          </ul>';
}
osc_add_hook('admin_menu', 'favorite_items_admin_menu');

// Configure link shown in the plugins list
function favorite_items_configure_link($links, $plugin_short_name)
{
    if ($plugin_short_name === 'favorite_items') {
        $url = osc_admin_render_plugin_url(FAVORITE_ITEMS_FOLDER . 'admin/settings.php');
        $links['configure'] = '<a href="' . osc_esc_html($url) . '">Configure</a>';
    }
    return $links;
}
osc_add_filter('plugin_row_meta', 'favorite_items_configure_link', 10, 2);
