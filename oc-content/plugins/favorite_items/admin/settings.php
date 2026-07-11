<?php
/**
 * Admin: Plugin Settings page
 */
if (!defined('ABS_PATH')) { exit('ABS_PATH is not loaded. Direct access is not allowed.'); }

// Save form
if (Params::getParam('save') === '1') {
    $icon        = Params::getParam('icon');
    $icon_size   = (int) Params::getParam('icon_size');
    $icon_color  = trim(Params::getParam('icon_color'));
    $show_count  = Params::getParam('show_count') ? '1' : '0';
    $label_add   = trim(Params::getParam('button_label'));
    $label_on    = trim(Params::getParam('button_label_active'));

    $home_enabled = Params::getParam('home_widget_enabled') ? '1' : '0';
    $home_title   = trim(Params::getParam('home_widget_title'));
    $home_sub     = trim(Params::getParam('home_widget_subtitle'));
    $home_limit   = (int) Params::getParam('home_widget_limit');
    $home_min     = (int) Params::getParam('home_widget_min_favorites');

    if (!in_array($icon, array('heart', 'star'), true)) $icon = 'heart';
    if ($icon_size < 12 || $icon_size > 96)             $icon_size = 24;
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $icon_color)) $icon_color = '#e11d48';
    if ($label_add === '') $label_add = 'Add to favorites';
    if ($label_on  === '') $label_on  = 'Saved';
    if ($home_title === '') $home_title = 'Most favorited listings by users';
    if ($home_limit < 1 || $home_limit > 24) $home_limit = 8;
    if ($home_min < 0) $home_min = 0;

    osc_set_preference('icon',                 $icon,       'favorite_items', 'STRING');
    osc_set_preference('icon_size',            $icon_size,  'favorite_items', 'INTEGER');
    osc_set_preference('icon_color',           $icon_color, 'favorite_items', 'STRING');
    osc_set_preference('show_count',           $show_count, 'favorite_items', 'BOOLEAN');
    osc_set_preference('button_label',         $label_add,  'favorite_items', 'STRING');
    osc_set_preference('button_label_active',  $label_on,   'favorite_items', 'STRING');
    osc_set_preference('home_widget_enabled',      $home_enabled, 'favorite_items', 'BOOLEAN');
    osc_set_preference('home_widget_title',        $home_title,   'favorite_items', 'STRING');
    osc_set_preference('home_widget_subtitle',     $home_sub,     'favorite_items', 'STRING');
    osc_set_preference('home_widget_limit',        $home_limit,   'favorite_items', 'INTEGER');
    osc_set_preference('home_widget_min_favorites',$home_min,     'favorite_items', 'INTEGER');
    osc_reset_preferences();

    osc_add_flash_ok_message('Settings saved.', 'admin');
    $url = osc_admin_render_plugin_url('favorite_items/admin/settings.php');
    header('Location: ' . $url);
    exit;
}

$icon       = osc_get_preference('icon', 'favorite_items') ?: 'heart';
$iconSize   = (int) (osc_get_preference('icon_size', 'favorite_items') ?: 24);
$iconColor  = osc_get_preference('icon_color', 'favorite_items') ?: '#e11d48';
$showCount  = (bool) osc_get_preference('show_count', 'favorite_items');
$labelAdd   = osc_get_preference('button_label', 'favorite_items') ?: 'Add to favorites';
$labelOn    = osc_get_preference('button_label_active', 'favorite_items') ?: 'Saved';

$homeEnabled = (bool) osc_get_preference('home_widget_enabled', 'favorite_items');
$homeTitle   = osc_get_preference('home_widget_title', 'favorite_items') ?: 'Most favorited listings by users';
$homeSub     = osc_get_preference('home_widget_subtitle', 'favorite_items') ?: '';
$homeLimit   = (int) (osc_get_preference('home_widget_limit', 'favorite_items') ?: 8);
$homeMin     = (int) (osc_get_preference('home_widget_min_favorites', 'favorite_items') ?: 1);

osc_add_hook('admin_page_header', function() {
    echo '<h1 style="margin-bottom:12px">Favorite Items — Settings</h1>';
});
?>
<link rel="stylesheet" href="<?php echo FAVORITE_ITEMS_URL; ?>assets/css/favorite.css?v=<?php echo FAVORITE_ITEMS_VERSION; ?>">
<div class="fi-admin" style="--fi-color: <?php echo osc_esc_html($iconColor); ?>; --fi-size: <?php echo (int) $iconSize; ?>px;">
    <h1 class="fi-admin__title">Favorite Items — Settings</h1>

    <form action="<?php echo osc_esc_html(osc_admin_render_plugin_url('favorite_items/admin/settings.php')); ?>" method="post" class="fi-admin__form" data-testid="admin-settings-form">
        <input type="hidden" name="save" value="1">

        <div class="fi-admin__row">
            <label class="fi-admin__label">Icon style</label>
            <div class="fi-admin__control">
                <label class="fi-admin__radio">
                    <input type="radio" name="icon" value="heart" <?php echo $icon === 'heart' ? 'checked' : ''; ?> data-testid="admin-icon-heart">
                    <span class="favorite-items-icon favorite-items-icon--heart" aria-hidden="true"></span> Heart
                </label>
                <label class="fi-admin__radio">
                    <input type="radio" name="icon" value="star" <?php echo $icon === 'star' ? 'checked' : ''; ?> data-testid="admin-icon-star">
                    <span class="favorite-items-icon favorite-items-icon--star" aria-hidden="true"></span> Star
                </label>
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-color">Icon color</label>
            <div class="fi-admin__control">
                <input id="fi-color" type="color" name="icon_color" value="<?php echo osc_esc_html($iconColor); ?>" data-testid="admin-icon-color">
                <input type="text" name="icon_color_text" value="<?php echo osc_esc_html($iconColor); ?>" pattern="#[0-9a-fA-F]{6}" style="width:110px" oninput="document.getElementById('fi-color').value=this.value">
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-size">Icon size (px)</label>
            <div class="fi-admin__control">
                <input id="fi-size" type="number" name="icon_size" min="12" max="96" value="<?php echo (int) $iconSize; ?>" data-testid="admin-icon-size">
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-label">Button label</label>
            <div class="fi-admin__control">
                <input id="fi-label" type="text" name="button_label" value="<?php echo osc_esc_html($labelAdd); ?>" style="width:280px" data-testid="admin-button-label">
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-label-on">Active label</label>
            <div class="fi-admin__control">
                <input id="fi-label-on" type="text" name="button_label_active" value="<?php echo osc_esc_html($labelOn); ?>" style="width:280px" data-testid="admin-button-label-active">
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-count">Show favorite count</label>
            <div class="fi-admin__control">
                <input id="fi-count" type="checkbox" name="show_count" value="1" <?php echo $showCount ? 'checked' : ''; ?> data-testid="admin-show-count">
                <span>Display the number of times each item has been favorited.</span>
            </div>
        </div>

        <div class="fi-admin__section-title">Home page widget</div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-home-enabled">Show on home page</label>
            <div class="fi-admin__control">
                <input id="fi-home-enabled" type="checkbox" name="home_widget_enabled" value="1" <?php echo $homeEnabled ? 'checked' : ''; ?> data-testid="admin-home-enabled">
                <span>Renders a "Most favorited listings by users" section on the home page (Delta theme &amp; most Osclass themes supported).</span>
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-home-title">Section title</label>
            <div class="fi-admin__control">
                <input id="fi-home-title" type="text" name="home_widget_title" value="<?php echo osc_esc_html($homeTitle); ?>" style="width:100%; max-width:420px" data-testid="admin-home-title">
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-home-sub">Section subtitle</label>
            <div class="fi-admin__control">
                <input id="fi-home-sub" type="text" name="home_widget_subtitle" value="<?php echo osc_esc_html($homeSub); ?>" style="width:100%; max-width:420px" data-testid="admin-home-subtitle" placeholder="(optional)">
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-home-limit">Items to show</label>
            <div class="fi-admin__control">
                <input id="fi-home-limit" type="number" name="home_widget_limit" min="1" max="24" value="<?php echo (int) $homeLimit; ?>" data-testid="admin-home-limit" style="width:100px">
                <span>Between 1 and 24. Cards are shown in a responsive 4-column grid.</span>
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label" for="fi-home-min">Minimum favorites</label>
            <div class="fi-admin__control">
                <input id="fi-home-min" type="number" name="home_widget_min_favorites" min="0" max="9999" value="<?php echo (int) $homeMin; ?>" data-testid="admin-home-min" style="width:100px">
                <span>Only include items that have at least this many favorites.</span>
            </div>
        </div>

        <div class="fi-admin__row">
            <label class="fi-admin__label">Preview</label>
            <div class="fi-admin__control">
                <div class="favorite-items-wrap">
                    <button type="button" class="favorite-items-btn is-active" data-testid="admin-preview-btn" onclick="return false;">
                        <span class="favorite-items-icon favorite-items-icon--<?php echo osc_esc_html($icon); ?>" aria-hidden="true"></span>
                        <span class="favorite-items-label"><?php echo osc_esc_html($labelOn); ?></span>
                        <?php if ($showCount): ?><span class="favorite-items-count">12</span><?php endif; ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="fi-admin__actions">
            <button type="submit" class="btn btn-primary fi-admin__save" data-testid="admin-settings-save">Save changes</button>
        </div>
    </form>
</div>
