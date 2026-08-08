<?php
if (!defined('ABS_PATH')) {
  exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

/**
 * Friendly URLs were enabled via SQL without regenerating rewrite_rules.
 * That makes /user/register etc. fall through to the homepage.
 *
 * Fix: if rewrite is ON but rules are missing/incomplete, rebuild the
 * standard rule set from current permalink preferences (Osclass defaults).
 */
function del_acv_pref_or($name, $default) {
  $v = osc_get_preference($name);
  return ($v !== null && $v !== '') ? $v : $default;
}

function del_acv_rebuild_rewrite_rules() {
  if (osc_get_preference('acv_rewrite_rules_v2', 'theme-delta') == '1') {
    return;
  }
  if (!osc_rewrite_enabled()) {
    osc_set_preference('acv_rewrite_rules_v2', '1', 'theme-delta');
    return;
  }

  try {
    $existing = osc_unserialize(osc_get_preference('rewrite_rules'));
    $has_register = false;
    if (is_array($existing)) {
      foreach ($existing as $uri) {
        if (is_string($uri) && strpos($uri, 'page=register') !== false) {
          $has_register = true;
          break;
        }
      }
    }
    if (is_array($existing) && count($existing) >= 15 && $has_register) {
      osc_set_preference('acv_rewrite_rules_v2', '1', 'theme-delta');
      return;
    }

    // Ensure slug preferences exist (defaults match Osclass)
    $defaults = array(
      'rewrite_contact' => 'contact',
      'rewrite_feed' => 'feed',
      'rewrite_language' => 'language',
      'rewrite_search' => 'search',
      'rewrite_item_mark' => 'item/mark',
      'rewrite_item_send_friend' => 'item/send-friend',
      'rewrite_item_contact' => 'item/contact',
      'rewrite_item_new' => 'item/new',
      'rewrite_item_activate' => 'item/activate',
      'rewrite_item_deactivate' => 'item/deactivate',
      'rewrite_item_renew' => 'item/renew',
      'rewrite_item_edit' => 'item/edit',
      'rewrite_item_delete' => 'item/delete',
      'rewrite_item_resource_delete' => 'item/resource/delete',
      'rewrite_user_login' => 'user/login',
      'rewrite_user_dashboard' => 'user/dashboard',
      'rewrite_user_logout' => 'user/logout',
      'rewrite_user_register' => 'user/register',
      'rewrite_user_activate' => 'user/activate',
      'rewrite_user_activate_alert' => 'user/activate_alert',
      'rewrite_user_profile' => 'user/profile',
      'rewrite_user_items' => 'user/items',
      'rewrite_user_alerts' => 'user/alerts',
      'rewrite_user_recover' => 'user/recover',
      'rewrite_user_forgot' => 'user/forgot',
      'rewrite_user_change_password' => 'user/change-password',
      'rewrite_user_change_email' => 'user/change-email',
      'rewrite_user_change_username' => 'user/change-username',
      'rewrite_user_change_email_confirm' => 'user/change-email-confirm',
      'rewrite_page' => 'page/{PAGE_SLUG}',
      'rewrite_cat_url' => '{CATEGORIES}',
      'rewrite_item_url' => '{CATEGORIES}/{ITEM_TITLE}_{ITEM_ID}',
    );
    foreach ($defaults as $k => $d) {
      if (osc_get_preference($k) === '' || osc_get_preference($k) === null) {
        osc_set_preference($k, $d);
      }
    }
    osc_reset_preferences();

    $p = function ($name) {
      return del_acv_pref_or($name, '');
    };

    $search_url = $p('rewrite_search') ?: 'search';
    $item_url = $p('rewrite_item_url') ?: '{CATEGORIES}/{ITEM_TITLE}_{ITEM_ID}';
    $page_url = $p('rewrite_page') ?: 'page/{PAGE_SLUG}';
    $cat_url = $p('rewrite_cat_url') ?: '{CATEGORIES}';

    $rewrite = Rewrite::newInstance();
    $rewrite->clearRules();

    $rewrite->addRule('^' . $p('rewrite_contact') . '/?$', 'index.php?page=contact');
    $rewrite->addRule('^' . $p('rewrite_feed') . '/?$', 'index.php?page=search&sFeed=rss');
    $rewrite->addRule('^' . $p('rewrite_feed') . '/(.+)/?$', 'index.php?page=search&sFeed=$1');
    $rewrite->addRule('^' . $p('rewrite_language') . '/(.*?)/?$', 'index.php?page=language&locale=$1');
    $rewrite->addRule('^' . $search_url . '$', 'index.php?page=search');
    $rewrite->addRule('^' . $search_url . '/(.*)$', 'index.php?page=search&sParams=$1');

    $rewrite->addRule('^' . $p('rewrite_item_mark') . '/(.*?)/([0-9]+)/?$', 'index.php?page=item&action=mark&as=$1&id=$2');
    $rewrite->addRule('^' . $p('rewrite_item_send_friend') . '/([0-9]+)/?$', 'index.php?page=item&action=send_friend&id=$1');
    $rewrite->addRule('^' . $p('rewrite_item_contact') . '/([0-9]+)/?$', 'index.php?page=item&action=contact&id=$1');
    $rewrite->addRule('^' . $p('rewrite_item_new') . '/?$', 'index.php?page=item&action=item_add');
    $rewrite->addRule('^' . $p('rewrite_item_new') . '/([0-9]+)/?$', 'index.php?page=item&action=item_add&catId=$1');
    $rewrite->addRule('^' . $p('rewrite_item_activate') . '/([0-9]+)/(.*?)/?$', 'index.php?page=item&action=activate&id=$1&secret=$2');
    $rewrite->addRule('^' . $p('rewrite_item_deactivate') . '/([0-9]+)/(.*?)/?$', 'index.php?page=item&action=deactivate&id=$1&secret=$2');
    $rewrite->addRule('^' . $p('rewrite_item_renew') . '/([0-9]+)/(.*?)/?$', 'index.php?page=item&action=renew&id=$1&secret=$2');
    $rewrite->addRule('^' . $p('rewrite_item_edit') . '/([0-9]+)/(.*?)/?$', 'index.php?page=item&action=item_edit&id=$1&secret=$2');
    $rewrite->addRule('^' . $p('rewrite_item_delete') . '/([0-9]+)/(.*?)/?$', 'index.php?page=item&action=item_delete&id=$1&secret=$2');
    $rewrite->addRule('^' . $p('rewrite_item_resource_delete') . '/([0-9]+)/([0-9]+)/([0-9A-Za-z]+)/?(.*?)/?$', 'index.php?page=item&action=deleteResource&id=$1&item=$2&code=$3&secret=$4');

    $item_rule = str_replace(
      array('{ITEM_ID}', '{CATEGORY}', '{CATEGORIES}', '{ITEM_TITLE}', '{ITEM_COUNTRY}', '{ITEM_COUNTRY_CODE}', '{ITEM_REGION}', '{ITEM_CITY}', '{ITEM_CITY_AREA}', '{ITEM_ZIP}', '{ITEM_CONTACT_NAME}', '{ITEM_CONTACT_EMAIL}', '{ITEM_CURRENCY_CODE}', '{ITEM_PUB_DATE}'),
      array('([0-9]+)', '.*', '.*', '.*', '.*', '.*', '.*', '.*', '.*', '.*', '.*', '.*', '.*', '.*'),
      $item_url
    );
    $rewrite->addRule('^' . $item_rule . '$', 'index.php?page=item&id=$1');

    $rewrite->addRule('^' . $p('rewrite_user_login') . '/?$', 'index.php?page=login');
    $rewrite->addRule('^' . $p('rewrite_user_dashboard') . '/?$', 'index.php?page=user&action=dashboard');
    $rewrite->addRule('^' . $p('rewrite_user_logout') . '/?$', 'index.php?page=main&action=logout');
    $rewrite->addRule('^' . $p('rewrite_user_register') . '/?$', 'index.php?page=register&action=register');
    $rewrite->addRule('^' . $p('rewrite_user_activate') . '/([0-9]+)/(.*?)/?$', 'index.php?page=register&action=validate&id=$1&code=$2');
    $rewrite->addRule('^' . $p('rewrite_user_activate_alert') . '/([0-9]+)/([a-zA-Z0-9]+)/(.+)$', 'index.php?page=user&action=activate_alert&id=$1&email=$3&secret=$2');
    $rewrite->addRule('^' . $p('rewrite_user_profile') . '/?$', 'index.php?page=user&action=profile');
    $rewrite->addRule('^' . $p('rewrite_user_profile') . '/([0-9]+)/?$', 'index.php?page=user&action=pub_profile&id=$1');
    $rewrite->addRule('^' . $p('rewrite_user_items') . '?$', 'index.php?page=user&action=items');
    $rewrite->addRule('^' . $p('rewrite_user_alerts') . '/?$', 'index.php?page=user&action=alerts');
    $rewrite->addRule('^' . $p('rewrite_user_recover') . '/?$', 'index.php?page=login&action=recover');
    $rewrite->addRule('^' . $p('rewrite_user_forgot') . '/([0-9]+)/(.*)/?$', 'index.php?page=login&action=forgot&userId=$1&code=$2');
    $rewrite->addRule('^' . $p('rewrite_user_change_password') . '/?$', 'index.php?page=user&action=change_password');
    $rewrite->addRule('^' . $p('rewrite_user_change_email') . '/?$', 'index.php?page=user&action=change_email');
    $rewrite->addRule('^' . $p('rewrite_user_change_username') . '/?$', 'index.php?page=user&action=change_username');
    $rewrite->addRule('^' . $p('rewrite_user_change_email_confirm') . '/([0-9]+)/(.*?)/?$', 'index.php?page=user&action=change_email_confirm&userId=$1&code=$2');

    $rewrite->addRule('^' . str_replace('{PAGE_SLUG}', '([\p{L}\p{N}_\-,]+)', $page_url) . '/?$', 'index.php?page=page&slug=$1');
    $rewrite->addRule('^(.+?)\.php(.*)$', '$1.php$2');

    $param_pos = 1;
    $rewrite->addRule('^' . str_replace('{CATEGORIES}', '(.+)', str_replace('{CATEGORY_NAME}', '([^/]+)', str_replace('{CATEGORY_ID}', '([0-9]+)', $cat_url))) . '/([0-9]+)$', 'index.php?page=search&sCategory=$' . $param_pos . '&iPage=$' . ($param_pos + 1));
    $rewrite->addRule('^' . str_replace('{CATEGORIES}', '(.+)', str_replace('{CATEGORY_NAME}', '([^/]+)', str_replace('{CATEGORY_ID}', '([0-9]+)', $cat_url))) . '/?$', 'index.php?page=search&sCategory=$' . $param_pos);
    $rewrite->addRule('^(.+)/([0-9]+)$', 'index.php?page=search&iPage=$2');
    $rewrite->addRule('^(.+)$', 'index.php?page=search');

    $rewrite->setRules();
    osc_set_preference('acv_rewrite_rules_v2', '1', 'theme-delta');
    osc_reset_preferences();
  } catch (Exception $e) {
    // ignore
  }
}
osc_add_hook('init', 'del_acv_rebuild_rewrite_rules', 4);
