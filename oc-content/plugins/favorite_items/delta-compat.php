<?php
/**
 * Delta theme compatibility layer for Favorite Items.
 *
 * Delta expects the official MB Themes Favorite Items API:
 *   fi_most_favorited_items(), fi_make_favorite(), fi_save_favorite()
 * and the route name `favorite-lists`.
 *
 * This file maps those to our ModelFavorites / routes so the native
 * Delta homepage block and header/footer hearts work without the
 * commercial plugin.
 */

if (!defined('ABS_PATH')) {
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

/**
 * Gate marker — Delta only checks function_exists().
 */
if (!function_exists('fi_most_favorited_items')) {
    function fi_most_favorited_items()
    {
        return true;
    }
}

/**
 * Gate marker used by Delta header / footer / mmenu favorites links.
 */
if (!function_exists('fi_make_favorite')) {
    function fi_make_favorite()
    {
        return true;
    }
}

/**
 * Heart / save button HTML for Delta's del_make_favorite().
 *
 * @param int|null $item_id
 * @param array    $options Unused; kept for API compatibility.
 * @return string
 */
if (!function_exists('fi_save_favorite')) {
    function fi_save_favorite($item_id = null, $options = array())
    {
        if ($item_id === null || (int) $item_id <= 0) {
            $item_id = (int) osc_item_id();
        } else {
            $item_id = (int) $item_id;
        }

        if ($item_id <= 0) {
            return '';
        }

        $item = Item::newInstance()->findByPrimaryKey($item_id);
        if (!$item) {
            return '';
        }

        // favorite-button.php expects $item in scope
        ob_start();
        include FAVORITE_ITEMS_PATH . 'views/favorite-button.php';
        return (string) ob_get_clean();
    }
}

/**
 * Alias Delta's `favorite-lists` route to our My Favorites page.
 */
if (!function_exists('favorite_items_register_delta_routes')) {
    function favorite_items_register_delta_routes()
    {
        // Only register if the official plugin did not already claim this route name.
        // osc_add_route will overwrite or conflict depending on Osclass version;
        // we register under the same pattern Delta hardcodes in header/footer.
        osc_add_route(
            'favorite-lists',
            'user/favorites',
            'user/favorites',
            FAVORITE_ITEMS_FOLDER . 'views/favorites-page.php'
        );
    }
}
favorite_items_register_delta_routes();
