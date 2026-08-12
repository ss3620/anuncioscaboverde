<?php
if (!defined('ABS_PATH')) {
  exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

/**
 * Keep the Blog plugin installed for later, but hide it from the public site.
 */
function del_acv_is_public_blog_request() {
  if (defined('OC_ADMIN') && OC_ADMIN) {
    return false;
  }
  $route = (string) Params::getParam('route');
  if ($route !== '' && strpos($route, 'blg-') === 0) {
    return true;
  }
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if ($uri !== '' && preg_match('#/(blog)(/|\?|$)#i', $uri)) {
    return true;
  }
  return false;
}

function del_acv_hide_public_blog() {
  if (!del_acv_is_public_blog_request()) {
    return;
  }
  header('HTTP/1.0 404 Not Found');
  if (function_exists('osc_current_web_theme_path')) {
    osc_current_web_theme_path('404.php');
  }
  exit;
}
osc_add_hook('init', 'del_acv_hide_public_blog', 1);

function del_acv_disable_blog_public_prefs() {
  if (osc_get_preference('acv_blog_hidden_v1', 'theme-delta') == '1') {
    return;
  }
  osc_set_preference('blog_home', '0', 'theme-delta');
  if (function_exists('blg_param')) {
    osc_set_preference('hook_header_links', '0', 'plugin-blog');
    osc_set_preference('widget', '0', 'plugin-blog');
  }
  osc_set_preference('acv_blog_hidden_v1', '1', 'theme-delta');
  osc_reset_preferences();
}
osc_add_hook('init', 'del_acv_disable_blog_public_prefs', 2);
