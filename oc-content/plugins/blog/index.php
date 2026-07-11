<?php
/*
  Plugin Name: Blog Plugin
  Plugin URI: https://osclasspoint.com/osclass-plugins/messaging-and-communication/osclass-blog-and-news-plugin-i84
  Description: Adds functionality to create blog section in osclass, allow users to become authors and create new articles on blog
  Version: 1.8.5
  Author: MB Themes
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: blog
  Plugin update URI: blog
  Support URI: https://forums.osclasspoint.com/blog-and-news-plugin/
  Product Key: kK7dgkItDQ66WZX76BzB
*/

define('BLG_VERSION_ID', 101);        // Version of DB state

require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelBLG.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'helpers.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'sitemap.php';


osc_enqueue_style('blg-user-style', osc_base_url() . 'oc-content/plugins/blog/css/user.css?v=' . date('YmdHis'));
osc_enqueue_style('font-awesome47', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
osc_enqueue_style('light-gallery', '//cdnjs.cloudflare.com/ajax/libs/lightgallery/1.6.11/css/lightgallery.min.css');

osc_register_script('light-gallery', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.6.11/js/lightgallery-all.min.js', 'jquery');
osc_register_script('blg-user', osc_base_url() . 'oc-content/plugins/blog/js/user.js?v=' . date('YmdHis'), array('jquery', 'light-gallery'));
osc_enqueue_script('light-gallery');
osc_enqueue_script('blg-user');



osc_add_route('blg-publish', 'blog/publish', 'blog/publish', osc_plugin_folder(__FILE__).'form/publish.php', false, 'blg', 'publish');
osc_add_route('blg-edit', 'blog/edit/(.+)', 'blog/edit/{blogId}', osc_plugin_folder(__FILE__).'form/publish.php', false, 'blg', 'edit');
osc_add_route('blg-action', 'blog/action/(.+)/', 'blog/action/{blgPage}/', osc_plugin_folder(__FILE__).'form/action.php');
osc_add_route('blg-post', 'blog/article/(.+)-b([0-9]+)', 'blog/article/{blogSlug}-b{blogId}', osc_plugin_folder(__FILE__).'form/article.php', false, 'blg', 'article');

osc_add_route('blg-search-paginate', 'blog/search/(.+)/([0-9]+)', 'blog/search/{keyword}/{pageId}', osc_plugin_folder(__FILE__).'form/search.php', false, 'blg', 'search');
osc_add_route('blg-search', 'blog/search/(.+)', 'blog/search/{keyword}', osc_plugin_folder(__FILE__).'form/search.php', false, 'blg', 'search');
osc_add_route('blg-author-paginate', 'blog/author/(.+)-a([0-9]+)/([0-9]+)', 'blog/author/{authorSlug}-a{authorId}/{pageId}', osc_plugin_folder(__FILE__).'form/author.php', false, 'blg', 'author');
osc_add_route('blg-author', 'blog/author/(.+)-a([0-9]+)', 'blog/author/{authorSlug}-a{authorId}', osc_plugin_folder(__FILE__).'form/author.php', false, 'blg', 'author');
osc_add_route('blg-category-paginate', 'blog/category/(.+)-c([0-9]+)/([0-9]+)', 'blog/category/{categorySlug}-c{categoryId}/{pageId}', osc_plugin_folder(__FILE__).'form/category.php', false, 'blg', 'category');
osc_add_route('blg-category', 'blog/category/(.+)-c([0-9]+)', 'blog/category/{categorySlug}-c{categoryId}', osc_plugin_folder(__FILE__).'form/category.php', false, 'blg', 'category');

osc_add_route('blg-home-paginate', 'blog/([0-9]+)', 'blog/{pageId}', osc_plugin_folder(__FILE__).'form/home.php', false, 'blg', 'home');
osc_add_route('blg-home', 'blog/', 'blog/', osc_plugin_folder(__FILE__).'form/home.php', false, 'blg', 'home');


//echo osc_route_url('blg-home');
//echo osc_route_url('blg-post', array('blogSlug' => 'my-first-blog', 'blogId' => 1));


// INSTALL FUNCTION - DEFINE VARIABLES
function blg_call_after_install() {
  osc_set_preference('blog_order', 1, 'plugin-blog', 'INTEGER');
  osc_set_preference('blog_validate', 1, 'plugin-blog', 'INTEGER');
  osc_set_preference('comment_enabled', 1, 'plugin-blog', 'INTEGER');
  osc_set_preference('comment_validate', 1, 'plugin-blog', 'INTEGER');
  osc_set_preference('premium_groups', '', 'plugin-blog', 'STRING');
  osc_set_preference('hook_header_links', 0, 'plugin-blog', 'INTEGER');
  osc_set_preference('widget', 0, 'plugin-blog', 'INTEGER');
  osc_set_preference('widget_type', 'grid', 'plugin-blog', 'STRING');
  osc_set_preference('widget_limit', 10, 'plugin-blog', 'INTEGER');
  osc_set_preference('widget_category', 0, 'plugin-blog', 'INTEGER');
  osc_set_preference('home_limit', 15, 'plugin-blog', 'INTEGER');
  osc_set_preference('search_limit', 30, 'plugin-blog', 'INTEGER');
  osc_set_preference('popular_limit', 8, 'plugin-blog', 'INTEGER');
  osc_set_preference('enable_banners', 0, 'plugin-blog', 'INTEGER');
  osc_set_preference('banner_optimize_adsense', 0, 'plugin-blog', 'INTEGER');
  osc_set_preference('canonical_redirect', 0, 'plugin-blog', 'INTEGER');
  osc_set_preference('sanitize', 1, 'plugin-blog', 'INTEGER');

  ModelBLG::newInstance()->install();
  
  osc_set_preference('version', BLG_VERSION_ID, 'plugin-blog', 'INTEGER');

  blg_create_upload_folders();
}



// AUTOMATIC PLUGIN UPDATE
// Version ID is number greater than 100 and reference to "version of database state" for plugin
function blg_install_plugin_update() {
  $plugin = 'blog';
  
  if(!in_array(Params::getParam('action'), array('widget','add_post','add','enable','disable','install','uninstall')) && !in_array(Params::getParam('page'), array('ajax','login','market','upgrade','appearance'))) { 
    $installed_version = (int)blg_param('version');
    $current_version = (int)BLG_VERSION_ID;
    
    if($installed_version <= 100) {
      blg_create_upload_folders();
    }
    
    if($installed_version > 0 && $current_version > $installed_version) {
      $ignore_error = (Params::getParam('forceupdateplugin') == $plugin ? true : false);
      blg_update_version($ignore_error);
    }
  }
}

osc_add_hook('init_admin', 'blg_install_plugin_update', 10);


// PLUGIN UPDATE
function blg_update_version($ignore_error = false) {
  $result = ModelBLG::newInstance()->versionUpdate($ignore_error);
  
  // if failed, do not update version and try DB update again
  if($result !== false || $ignore_error !== false) {
    osc_set_preference('version', BLG_VERSION_ID, 'plugin-blog', 'INTEGER');
    osc_reset_preferences();

    osc_add_flash_ok_message(sprintf(__('Blog Plugin database structure has been updated on background into db version %s.', 'blog'), BLG_VERSION_ID), 'admin');
  }
  
  // ignore error and force version update of plugin
  if($ignore_error === true) {
    osc_add_flash_ok_message(sprintf(__('Force update of "%s" completed! Verify plugin functionality, in case of problems reinstall plugin.', 'blog'), __('Blog Plugin', 'blog')), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins');
    exit;
  }
}

osc_add_hook(osc_plugin_path(__FILE__) . '_enable', 'blg_update_version');



// UNINSTALL PLUGIN
function blg_call_after_uninstall() {
  ModelBLG::newInstance()->uninstall();
}


// ADMIN MENU
function blg_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/blog/css/admin.css?v=' . date('YmdHis') . '" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/blog/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/blog/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/blog/js/admin.js?v=' . date('YmdHis') . '"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/blog/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/blog/js/bootstrap-switch.js"></script>';

  if( $title == '') { $title = __('Configure', 'blog'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>Blog Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'blog') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/list.php"><i class="fa fa-reorder"></i><span>' . __('Articles', 'blog') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/blog.php"><i class="fa fa-plus-circle"></i><span>' . __('Add Article', 'blog') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/user.php"><i class="fa fa-user"></i><span>' . __('Users', 'blog') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/category.php"><i class="fa fa-gears"></i><span>' . __('Categories', 'blog') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/comment.php"><i class="fa fa-comments"></i><span>' . __('Comments', 'blog') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/banner.php"><i class="fa fa-bullhorn"></i><span>' . __('Banners', 'blog') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function blg_footer() {
  $pluginInfo = osc_plugin_get_info('blog/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="https://osclasspoint.com"><img src="https://osclasspoint.com/favicon.ico" alt="MB Themes" /> osclasspoint.com</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'blog') . '</a>';
  $text .= '<a target="_blank" href="https://forums.osclasspoint.com/"><i class="fa fa-handshake-o"></i> ' . __('Support Forums', 'blog') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'blog') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  return $text;
}



// ADD MENU LINK TO PLUGIN LIST
function blg_admin_menu() {
echo '<h3><a href="#">Blog Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Configure', 'blog') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/list.php') . '">&raquo; ' . __('Articles', 'blog') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/blog.php') . '">&raquo; ' . __('Add Article', 'blog') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/user.php') . '">&raquo; ' . __('Users', 'blog') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/category.php') . '">&raquo; ' . __('Categories', 'blog') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/comment.php') . '">&raquo; ' . __('Comments', 'blog') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/banner.php') . '">&raquo; ' . __('Banners', 'blog') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','blg_admin_menu', 1);



// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function blg_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/configure.php' );
}

osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'blg_conf' );	


// CALL WHEN PLUGIN IS ACTIVATED - INSTALLED
osc_register_plugin(osc_plugin_path(__FILE__), 'blg_call_after_install');

// SHOW UNINSTALL LINK
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'blg_call_after_uninstall');
