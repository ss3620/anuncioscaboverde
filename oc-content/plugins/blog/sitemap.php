<?php
// MAKE SURE OSC_PLUGIN_PATH FUNCTION EXISTS
if( !function_exists('osc_plugin_path') ) {
  function osc_plugin_path($file) {
    $file = preg_replace('|/+|','/', str_replace('\\','/',$file));
    $plugin_path = preg_replace('|/+|','/', str_replace('\\','/', PLUGINS_PATH));
    $file = $plugin_path . preg_replace('#^.*oc-content\/plugins\/#','',$file);
    return $file;
  }
}


// GENERATE SITEMAP
function blg_generate_sitemap() {
  $start_time = microtime(true);

  $categories = ModelBLG::newInstance()->getCategories();
  $authors = ModelBLG::newInstance()->getUsers();
  $blogs = ModelBLG::newInstance()->getBlogs(1);

  
  $locales = osc_get_locales();

  $filename = osc_base_path() . 'sitemap_blog.xml';
  @unlink($filename); 
  
  $start_xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
  file_put_contents($filename, $start_xml);


  // INDEX
  blg_sitemap_add_url(osc_route_url('blg-home'), date('Y-m-d'), 'always');


  // ADD CATEGORIES
  if(count($categories) > 0) {
    foreach($categories as $c) {
      blg_sitemap_add_url(osc_route_url('blg-category', array('categorySlug' => osc_sanitizeString($c['s_name']), 'categoryId' => $c['pk_i_id'])), date('Y-m-d'), 'hourly');
      //blg_sitemap_add_url(osc_route_url('blg-category', array('categorySlug' => osc_sanitizeString(blg_slug($c['s_name'])), 'categoryId' => $c['pk_i_id'])), date('Y-m-d'), 'hourly');
    }
  }

  // ADD AUTHORS
  if(count($authors) > 0) {
    foreach($authors as $a) {
      blg_sitemap_add_url(osc_route_url('blg-author', array('authorSlug' => osc_sanitizeString($a['s_name']), 'authorId' => $a['pk_i_id'])), date('Y-m-d'), 'hourly');
      //blg_sitemap_add_url(osc_route_url('blg-author', array('authorSlug' => osc_sanitizeString(blg_slug($a['s_name'])), 'authorId' => $a['pk_i_id'])), date('Y-m-d'), 'hourly');
    }
  }

  // ADD ARTICLES
  if(count($blogs) > 0) {
    foreach($blogs as $b) {
      blg_sitemap_add_url(osc_route_url('blg-post', array('blogSlug' => blg_get_slug($b), 'blogId' => $b['pk_i_id'])), date('Y-m-d'), 'daily');
    }
  }


  $end_xml = '</urlset>';
  file_put_contents($filename, $end_xml, FILE_APPEND);
  

  // PING SEARCH ENGINES
  blg_sitemap_ping_engines();
  
  // CALCULATE GENERATION TIME
  $time_elapsed = microtime(true) - $start_time;
  return $time_elapsed;
}



// ADD URL TO SITEMAP - HELP FUNCTION
function blg_sitemap_add_url($url = '', $date = '', $freq = 'daily') {
  if( preg_match('|\?(.*)|', $url, $match) ) {
    $sub_url = $match[1];
    $param = explode('&', $sub_url);
    foreach($param as &$p) {
      @list($key, $value) = @explode('=', $p);
      $p = $key . '=' . urlencode((string)$value);
    }
    $sub_url = implode('&', $param);
    $url = preg_replace('|\?.*|', '?' . $sub_url, $url);
  } else {
    $help = $url; 
    $help_encode = urlencode((string)$help);
    $help_fix = str_replace('%2C', ',', $help_encode);
    $help_fix = str_replace('%2F', '/', $help_fix);
    $help_fix = str_replace('%3A', ':', $help_fix);
    $url = $help_fix;     
  }

  $filename = osc_base_path() . 'sitemap_blog.xml';
  $xml  = '  <url>' . PHP_EOL;
  $xml .= '    <loc>' . str_replace('&rsquo;', '', htmlentities($url, ENT_QUOTES, "UTF-8")) . '</loc>' . PHP_EOL;
  $xml .= '    <lastmod>' . $date . '</lastmod>' . PHP_EOL;
  $xml .= '    <changefreq>' . $freq . '</changefreq>' . PHP_EOL;
  $xml .= '  </url>' . PHP_EOL;
  file_put_contents($filename, $xml, FILE_APPEND);
}



// PING SEARCH ENGINES WITH NEW SITEMAP - HELP FUNCTION
function blg_sitemap_ping_engines() {
  $sitemap = osc_base_url() . 'sitemap_blog.xml';

  osc_doRequest( 'https://www.google.com/webmasters/sitemaps/ping?sitemap='.urlencode((string)$sitemap), array());
  osc_doRequest( 'https://www.bing.com/webmaster/ping.aspx?siteMap='.urlencode((string)$sitemap), array());
  //osc_doRequest( 'https://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid='.osc_page_title().'&url='.urlencode($sitemap), array());
}



osc_add_hook('cron_daily', 'blg_generate_sitemap');

