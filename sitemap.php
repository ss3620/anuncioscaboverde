<?php
/**
 * Dynamic sitemap for Anúncios Cabo Verde.
 * URL: https://www.anuncioscaboverde.com/sitemap.php
 * Also add: Admin → Tools → cron / regenerate if using Osclass core Sitemap.
 */
define('ABS_PATH', dirname(__FILE__) . '/');
require_once ABS_PATH . 'oc-load.php';

header('Content-Type: application/xml; charset=utf-8');

$base = rtrim(osc_base_url(), '/');
$urls = array();

$urls[] = array('loc' => $base . '/', 'changefreq' => 'daily', 'priority' => '1.0');
$urls[] = array('loc' => osc_search_url(array('page' => 'search')), 'changefreq' => 'hourly', 'priority' => '0.9');
$urls[] = array('loc' => osc_contact_url(), 'changefreq' => 'monthly', 'priority' => '0.5');
$urls[] = array('loc' => osc_register_account_url(), 'changefreq' => 'monthly', 'priority' => '0.4');

$staticNames = array(
  'about-us', 'como-funciona', 'terms', 'privacy', 'cookies', 'faq',
  'seguranca', 'regras-anuncios', 'como-denunciar', 'contacto-completo'
);
foreach ($staticNames as $name) {
  $page = Page::newInstance()->findByInternalName($name);
  if ($page && !empty($page['pk_i_id'])) {
    View::newInstance()->_exportVariableToView('page', $page);
    $urls[] = array(
      'loc' => osc_static_page_url(),
      'changefreq' => 'monthly',
      'priority' => '0.6'
    );
  }
}

$categories = Category::newInstance()->listEnabled();
if (is_array($categories)) {
  foreach ($categories as $cat) {
    if (empty($cat['pk_i_id'])) continue;
    $urls[] = array(
      'loc' => osc_search_url(array('page' => 'search', 'sCategory' => $cat['pk_i_id'])),
      'changefreq' => 'daily',
      'priority' => '0.7'
    );
  }
}

$mSearch = new Search();
$mSearch->limit(0, 500);
$items = $mSearch->doSearch();
if (!is_array($items)) {
  $items = array();
}
foreach ($items as $item) {
  if (empty($item['pk_i_id'])) continue;
  View::newInstance()->_exportVariableToView('item', $item);
  $urls[] = array(
    'loc' => osc_item_url(),
    'lastmod' => !empty($item['dt_mod_date']) ? date('Y-m-d', strtotime($item['dt_mod_date'])) : date('Y-m-d', strtotime($item['dt_pub_date'])),
    'changefreq' => 'weekly',
    'priority' => '0.8'
  );
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
  echo "  <url>\n";
  echo '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</loc>\n";
  if (!empty($u['lastmod'])) {
    echo '    <lastmod>' . $u['lastmod'] . "</lastmod>\n";
  }
  echo '    <changefreq>' . $u['changefreq'] . "</changefreq>\n";
  echo '    <priority>' . $u['priority'] . "</priority>\n";
  echo "  </url>\n";
}
echo '</urlset>';
