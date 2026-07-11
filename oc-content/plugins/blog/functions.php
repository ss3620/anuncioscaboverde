<?php


// CREATE REQUIRED UPLOAD FOLDERS
function blg_create_upload_folders() {
  // Create main folder
  if(!@mkdir(osc_uploads_path() . 'blog/') && !is_dir(osc_uploads_path() . 'blog/')) { 
    throw new \RuntimeException(sprintf(__('Directory "%s" was not created', 'blog'), osc_uploads_path() . 'blog/'));
  }
  
  // Create subfolders
  if(!@mkdir(osc_uploads_path() . 'blog/blog/') && !is_dir(osc_uploads_path() . 'blog/blog/')) { 
    throw new \RuntimeException(sprintf(__('Directory "%s" was not created', 'blog'), osc_uploads_path() . 'blog/blog/'));
  }
  
  if(!@mkdir(osc_uploads_path() . 'blog/tinymce/') && !is_dir(osc_uploads_path() . 'blog/tinymce/')) { 
    throw new \RuntimeException(sprintf(__('Directory "%s" was not created', 'blog'), osc_uploads_path() . 'blog/tinymce/'));
  }
  
  if(!@mkdir(osc_uploads_path() . 'blog/user/') && !is_dir(osc_uploads_path() . 'blog/user/')) { 
    throw new \RuntimeException(sprintf(__('Directory "%s" was not created', 'blog'), osc_uploads_path() . 'blog/user/'));
  }
}


// MIGRATE FILES TO UPLOADS FOLDER
// This is one time action. When done, old folder in plugin is removed and code is not triggered!
function blg_migrate_images() {
  $old_path_check = osc_content_path() . 'plugins/blog/img/tinymce';

  if(file_exists($old_path_check)) {
    ModelBLG::newInstance()->updateBlogImageStructure();
    
    blg_create_upload_folders();
    $subfolders = array('blog','tinymce','user');
    
    foreach($subfolders as $sub) {
      $file_path_old = osc_content_path() . 'plugins/blog/img/' . $sub . '/';
      
      if(file_exists($file_path_old)) {
        $files = glob($file_path_old . '*');
        
        if(count($files) > 0) {
          foreach($files as $file_old) {
            @rename($file_old, blg_file_path(basename($file_old), $sub));
          }
        }

        @rmdir($file_path_old);
      }
    }
  }
}

osc_add_hook('init_admin', 'blg_migrate_images');


// GET FILES UPLOADS PATH
function blg_file_path($file = '', $type = 'blog') {
  return osc_apply_filter('blg_file_path', osc_uploads_path() . 'blog/' . $type . '/' . $file, $type);
}


// GET FILES UPLOADS URL
function blg_file_url($file = '', $type = 'blog') {
  return osc_apply_filter('blg_file_url', osc_content_url() . 'uploads/blog/' . $type . '/' . $file, $type);
}


// CREATE SHARE URLS
function blg_share_block($url, $img_url = '', $title = '') {
  if(blg_param('share_buttons') == 1 && $url != '') {
  ?>
    <div id="blg-share" class="blg-share">
      <a class="blg-share-link whatsapp" href="whatsapp://send?text=<?php echo urlencode($url); ?>" data-action="share/whatsapp/share"><i class="fa fab fa-whatsapp"></i> <?php _e('Share on WhatsApp', 'blog'); ?></a></span>
      <a class="blg-share-link facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>"><i class="fa fab fa-facebook"></i> <?php _e('Share on Facebook', 'blog'); ?></a> 
      <a class="blg-share-link twitter" target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo urlencode($title); ?>&url=<?php echo urlencode($url); ?>"><i class="fa fab fa-twitter"></i> <?php _e('Share on Twitter', 'blog'); ?></a> 
      <a class="blg-share-link pinterest" target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode($url); ?>&media=<?php echo urlencode($img_url); ?>&description=<?php echo htmlspecialchars($title); ?>"><i class="fa fab fa-pinterest"></i> <?php _e('Share on Pinterest', 'blog'); ?></a> 
    </div>
  <?php
  }
}


// ADD LINK TO HEADER
function blg_header_link_hook() {
  if(blg_param('hook_header_links') == 1) {
    echo '<a href="' . blg_home_link() . '">' . __('Blog', 'blog') . '</a>';    
  }
}

osc_add_hook('header_links', 'blg_header_link_hook');


// GET CURRENT URL
function blg_current_url() {
  if(function_exists('osc_get_current_url')) {
    return osc_get_current_url();
  }
  
  return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

// GET CANONICAL URL
function blg_canonical_url($blog) {
  return osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString(blg_get_slug($blog)), 'blogId' => $blog['pk_i_id']));
}


// SHOW BANNER
function blg_banner($location) {
  $visible_demo_banners = array('home_top','search_top','article_middle','side_middle');
  $is_demo = blg_is_demo(true);
  $html = '';

  if(blg_param('enable_banners') == 1) {
    if($is_demo) {
      $class = ' is-demo';
    } else {
      $class = '';
    }
    
    if(blg_param('banner_optimize_adsense') == 1) {
      $class .= ' opt-adsense';
    }

    if(blg_param('banner_' . $location) == '') {
      $blank = ' blank';
    } else {
      $blank = '';
    }

    if($is_demo && blg_param('banner_' . $location) == '' ) {
      $title = ' title="' . __('You can define your own banner code from plugin settings', 'blog') . '"';
    } else {
      $title = '';
    }

    $html .= '<div id="bnr-plg" class="bnr-plg bnr-' . $location . $class . $blank . '"' . $title . '><div class="myad"><div class="text">';


    // BANNER ADS PLUGIN SUPPORT
    if(function_exists('ba_show_banner') && strpos(strtoupper(blg_param('banner_' . $location)), 'BANNER-ADS-PLUGIN') !== false) {
      $xdata = strtoupper(trim(blg_param('banner_' . $location)));

      if(strpos(blg_param('banner_' . $location), 'BANNER-ADS-PLUGIN-HOOK')) {
        $hook = trim(str_replace(array(' ', '  ', '{', '{{', '{{{', '}', '}}', '}}}', 'BANNER-ADS-PLUGIN-HOOK', ':'), '', $xdata));

        if(trim($hook) <> '') {
          $html .= ba_hook($hook, false);
        }
      } else if(strpos(blg_param('banner_' . $location), 'BANNER-ADS-PLUGIN-BANNER')) {
        $banner_id = trim(str_replace(array(' ', '  ', '{', '{{', '{{{', '}', '}}', '}}}', 'BANNER-ADS-PLUGIN-BANNER', ':'), '', $xdata));

        if(is_numeric($banner_id) && $banner_id > 0) {
          $html .= ba_show_banner($banner_id, false);
        }
      } else if(strpos(blg_param('banner_' . $location), 'BANNER-ADS-PLUGIN-ADVERT')) {
        $advert_id = trim(str_replace(array(' ', '  ', '{', '{{', '{{{', '}', '}}', '}}}', 'BANNER-ADS-PLUGIN-ADVERT', ':'), '', $xdata));

        if(is_numeric($advert_id) && $advert_id > 0) {
          $html .= ba_show_advert($advert_id);
        }
      }
    } else {
      $html .= blg_param('banner_' . $location);
    }


    if($is_demo && blg_param('banner_' . $location) == '' && in_array($location, $visible_demo_banners)) {
      $html .= '<div class="demo-text"><span>' . __('Banner space', 'blog') . '</span><strong>[' .  str_replace('_', ' ', $location) . ']</strong></div>';
    }

    $html .= '</div></div></div>';

    if(!($is_demo && in_array($location, $visible_demo_banners)) && trim(blg_param('banner_' . $location)) == '') {
      return '';
      
    } else {
      return $html;
    }
    
  } else {
    return false;
  }
}


// LIST OF BANNERS
function blg_banner_list() {
  $list = array(
    array('id' => 'banner_home_top', 'name' => __('Home Top', 'blog'), 'position' => __('Top of blog home page', 'blog')),
    array('id' => 'banner_home_bottom', 'name' => __('Home Bottom', 'blog'), 'position' => __('Bottom of blog home page', 'blog')),
    array('id' => 'banner_side_top', 'name' => __('Sidebar Top', 'blog'), 'position' => __('Top of blog sidebar', 'blog')),
    array('id' => 'banner_side_middle', 'name' => __('Sidebar Middle', 'blog'), 'position' => __('Middle of blog sidebar', 'blog')),
    array('id' => 'banner_side_bottom', 'name' => __('Sidebar Bottom', 'blog'), 'position' => __('Bottom of blog sidebar', 'blog')),
    array('id' => 'banner_search_top', 'name' => __('Search Top', 'blog'), 'position' => __('Top of blog search page', 'blog')),
    array('id' => 'banner_search_middle', 'name' => __('Search Middle', 'blog'), 'position' => __('Middle of blog search page', 'blog')),
    array('id' => 'banner_search_bottom', 'name' => __('Search Bottom', 'blog'), 'position' => __('Bottom of blog search page', 'blog')),
    array('id' => 'banner_article_top', 'name' => __('Article Top', 'blog'), 'position' => __('Top of blog article page', 'blog')),
    array('id' => 'banner_article_subtop', 'name' => __('Article SubTop', 'blog'), 'position' => __('Top of blog article page below subtitle', 'blog')),
    array('id' => 'banner_article_middle', 'name' => __('Article Middle', 'blog'), 'position' => __('Middle of blog article page', 'blog')),
    array('id' => 'banner_article_bottom', 'name' => __('Article Bottom', 'blog'), 'position' => __('Bottom of blog article page', 'blog'))
  );

  return $list;
}

// GET LIMITS
function blg_get_limits($type) {
  $type = strtoupper($type);
  $per_page = 10;
  
  if($type == 'POPULAR') {
    $per_page = (blg_param('popular_limit') > 0 ? blg_param('popular_limit') : 8);
  } else if($type == 'SEARCH' || $type == 'CATEGORY' || $type == 'AUTHOR') {
    $per_page = (blg_param('search_limit') > 0 ? blg_param('search_limit') : 12);
  } else if($type == 'ACTIVE' || $type == 'HOME') {
    $per_page = (blg_param('home_limit') > 0 ? blg_param('home_limit') : 15);
  } else if($type == 'WIDGET') {
    $view = (blg_param('widget_type') <> '' ? blg_param('widget_type') : 'grid');
    $per_page = ($view == 'grid' ? 5 : blg_param('widget_limit'));
    $per_page = ($per_page > 5 ? 5 : $per_page);
  } else if(trim($type) == '') {
    return 0;  // should be backoffice, disable limits
  }
  
  return $per_page;
}


// GENERATE PAGINATION
function blg_paginate($type, $data, $page_id, $per_page, $count_all, $class = '') {
  $html = '';
  $page_id = (int)$page_id;
  $page_id = ($page_id <= 0 ? 1 : $page_id);

  if($per_page < $count_all) {
    $html .= '<div class="blg-pagination ' . $class . '">';

    $pages = ceil($count_all/$per_page); 
    $page_actual = ($page_id == '' ? 1 : $page_id);

    if($pages > 6) {

      // Too many pages to list them all
      if($page_id == 1) { 
        $ids = array(1,2,3, $pages);

      } else if ($page_id > 1 && $page_id < $pages) {
        $ids = array(1,$page_id-1, $page_id, $page_id+1, $pages);

      } else {
        $ids = array(1, $page_id-2, $page_id-1, $page_id);
      }

      $old = -1;
      $ids = array_unique(array_filter($ids));

      foreach($ids as $i) {
        if($type == 'home') {
          $url = osc_route_url('blg-home-paginate', array('pageId' => $i));
        } else if ($type == 'category') {
          $url = osc_route_url('blg-category-paginate', array('categorySlug' => $data['categorySlug'], 'categoryId' => $data['categoryId'], 'pageId' => $i));
        } else if ($type == 'author') {
          $url = osc_route_url('blg-author-paginate', array('authorSlug' => $data['authorSlug'], 'authorId' => $data['authorId'], 'pageId' => $i));
        } else if ($type == 'search') {
          $url = osc_route_url('blg-search-paginate', array('keyword' => $data['keyword'], 'pageId' => $i));
        }

        if($old <> -1 && $old <> $i - 1) {
          $html .= '<span>&middot;&middot;&middot;</span>';
        }

        $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="blg-active"' : '') . '>' . $i . '</a>';
        $old = $i;
      }

    } else {

      // List all pages
      for ($i = 1; $i <= $pages; $i++) {
        if($type == 'home') {
          $url = osc_route_url('blg-home-paginate', array('pageId' => $i));
        } else if ($type == 'category') {
          $url = osc_route_url('blg-category-paginate', array('categorySlug' => $data['categorySlug'], 'categoryId' => $data['categoryId'], 'pageId' => $i));
        } else if ($type == 'author') {
          $url = osc_route_url('blg-author-paginate', array('authorSlug' => $data['authorSlug'], 'authorId' => $data['authorId'], 'pageId' => $i));
        } else if ($type == 'search') {
          $url = osc_route_url('blg-search-paginate', array('keyword' => $data['keyword'], 'pageId' => $i));
        }

        $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="blg-active"' : '') . '>' . $i . '</a>';
      }
    }

    $html .= '</div>';
  }

  return $html;
}


// GENERATE ADMIN PAGINATION
function blg_admin_paginate($file, $page_id, $per_page, $count_all, $class = '', $params = '') {
  $html = '';
  $page_id = (int)$page_id;
  $page_id = ($page_id <= 0 ? 1 : $page_id);
  $base_link = osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=' . $file . $params;

  if($per_page < $count_all) {
    $html .= '<div id="mb-pagination" class="' . $class . '">';
    $html .= '<div class="mb-pagination-wrap">';
    $html .= '<div>' . __('Page:', 'blog') . '</div>';

    $pages = ceil($count_all/$per_page); 
    $page_actual = ($page_id == '' ? 1 : $page_id);

    if($pages > 6) {

      // Too many pages to list them all
      if($page_id == 1) { 
        $ids = array(1,2,3, $pages);

      } else if ($page_id > 1 && $page_id < $pages) {
        $ids = array(1,$page_id-1, $page_id, $page_id+1, $pages);

      } else {
        $ids = array(1, $page_id-2, $page_id-1, $page_id);
      }

      $old = -1;
      $ids = array_unique(array_filter($ids));

      foreach($ids as $i) {
        $url = $base_link . '&pageId=' . $i;
        
        if($old <> -1 && $old <> $i - 1) {
          $html .= '<span>&middot;&middot;&middot;</span>';
        }

        $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="mb-active"' : '') . '>' . $i . '</a>';
        $old = $i;
      }

    } else {

      // List all pages
      for ($i = 1; $i <= $pages; $i++) {
        $url = $base_link . '&pageId=' . $i;
        $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="mb-active"' : '') . '>' . $i . '</a>';
      }
    }

    $html .= '</div>';
    $html .= '</div>';
  }

  return $html;
}

// BLOG WIDGET
function blg_widget($type = '') {
  if(blg_param('widget') == 1) { 
    require 'form/widget.php';
  }
}

osc_add_hook('blg_widget', 'blg_widget');


// CHECK IF PREMIUM OPTIONS ARE ENABLED
function blg_check_premium() {
  if(function_exists('osp_param')) {
    if(osp_param('groups_enabled') == 1) {
      if(blg_param('premium_groups') <> '') {
        return blg_param('premium_groups');
      }
    }
  }

  return false;
}


// CHECK IF BLOG IS PREMIUM
function blg_is_premium($blog) {
  if(isset($blog['i_status']) && $blog['i_status'] == 2) {
    if(blg_check_premium()) {
      if(!osc_is_web_user_logged_in()) {
        return blg_check_premium();
      }

      $author = ModelBLG::newInstance()->getUserByOsclassId(osc_logged_user_id());
      $groups = explode(',', (string)blg_check_premium());

      if(@$author['pk_i_id'] <> $blog['fk_i_user_id']) {
        $user_group = osp_get_user_group(osc_logged_user_id());
 
        if(!in_array($user_group, $groups)) {
          return blg_check_premium();
        }
      }
    }
  }

  return false;
}


// GET PREMIUM GROUPS
function blg_premium_groups() {
  if(blg_check_premium()) {
    $return = array();
    $groups = explode(',', (string)blg_check_premium());

    if(count($groups) > 0) {
      foreach($groups as $id) {
        $return[] = ModelOSP::newInstance()->getGroup($id);
      }

      return $return;
    }
  }

  return false;
}


// META TITLE
function blg_meta_title($tag) {
  $location = Rewrite::newInstance()->get_location();
  $section  = Rewrite::newInstance()->get_section();
  $route = Params::getParam('route');
  $pagination_title = ((int)Params::getParam('pageId') > 1 ? ' - ' . __('Page', 'blog') . ' ' . Params::getParam('pageId') : '');
  $page_title = ' - ' . osc_page_title();

  if($location == 'blg' && $section == 'home' || $route == 'blg-home') {
    $tag = __('Blog', 'blog') . $pagination_title . $page_title;

  } else if ($location == 'blg' && $section == 'publish' || $route == 'blg-publish') {
    $tag = __('Publish on blog', 'blog') . $pagination_title . $page_title;
  
  } else if ($location == 'blg' && $section == 'edit' || $route == 'blg-edit') {
    $tag = __('Edit blog article', 'blog') . $pagination_title . $page_title;

  } else if ($location == 'blg' && $section == 'search' || $route == 'blg-search') {
    $tag = sprintf(__('Search results for %s', 'blog'), urldecode(Params::getParam('keyword'))) . $pagination_title . $page_title;

  } else if ($location == 'blg' && $section == 'category' || $route == 'blg-category') {
    $category = ModelBLG::newInstance()->getCategoryDetail(Params::getParam('categoryId'));
    $tag = sprintf(__('%s blog', 'blog'), blg_get_cat_name($category)) . $pagination_title . $page_title;

  } else if ($location == 'blg' && $section == 'author' || $route == 'author') {
    $author = ModelBLG::newInstance()->getUser(Params::getParam('authorId'));
    $tag = sprintf(__('%s\'s blog', 'blog'), $author['s_name']) . $pagination_title . $page_title;

  } else if ($location == 'blg' && $section == 'article' || $route == 'blg-post') {
    $blog = ModelBLG::newInstance()->getBlogDetail(Params::getParam('blogId'));
    $tag = blg_get_seo_title($blog) . $pagination_title . $page_title;
  }

  return $tag;
}

osc_add_filter('meta_title_filter', 'blg_meta_title', 6);
osc_add_filter('structured_data_title_filter', 'blg_meta_title', 6);


// META DESCRIPTION
function blg_meta_description($tag) {
  $location = Rewrite::newInstance()->get_location();
  $section  = Rewrite::newInstance()->get_section();
  $route = Params::getParam('route');

  if($location == 'blg' && $section == 'home' || $route == 'blg-home') {
    $tag = __('Welcome to our blog, we believe you will find a lot of helpful articles here.', 'blog');

  } else if ($location == 'blg' && $section == 'category' || $route == 'blg-category') {
    $category = ModelBLG::newInstance()->getCategoryDetail(Params::getParam('categoryId'));
    $tag = blg_get_cat_description($category);

  } else if ($location == 'blg' && $section == 'article' || $route == 'blg-post') {
    $blog = ModelBLG::newInstance()->getBlogDetail(Params::getParam('blogId'));
    $tag = blg_get_seo_description($blog);
  }

  return $tag;
}

osc_add_filter('meta_description_filter', 'blg_meta_description', 6);
osc_add_filter('structured_data_description_filter', 'blg_meta_description', 6);


// STRUCTURED DATA
function blg_structured_image($img) {
  $location = Rewrite::newInstance()->get_location();
  $section  = Rewrite::newInstance()->get_section();
  $route = Params::getParam('route');

  $pagination_title = ((int)Params::getParam('pageId') > 1 ? ' - ' . __('Page', 'blog') . ' ' . Params::getParam('pageId') : '');
  $page_title = ' - ' . osc_page_title();

  if ($location == 'blg' && $section == 'article' || $route == 'blg-post') {
    $img = blg_img(Params::getParam('blogId'));
    
  } else if($location == 'blg') { // disable "logo" for BlogPosting
    $img = '';
  }

  return $img;
}

osc_add_filter('structured_data_image_filter', 'blg_structured_image', 6);


// STRUCTURED DATA TYPE
osc_add_filter('structured_data_type_filter', function($tag) { 
  $location = Rewrite::newInstance()->get_location();
  $section  = Rewrite::newInstance()->get_section();
  $route = Params::getParam('route');
  
  if($location == 'blg' && $section == 'article' || $route == 'blg-post') {
    $tag = 'BlogPosting';
  } else if($location == 'blg') {
    $tag = 'Blog';
  }
  
  return $tag;
});


// GENERATE SEARCH CLAUSE
function blg_search_clause($word, $table) {
  preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $word, $words);
  $words = $words[0];

  $clause_title = '(';
  $clause_subtitle = '(';
  $clause_desc = '(';

  $k = 0;
  if(count($words) > 0) {
    foreach($words as $w) {
      if($k > 0) {
        $clause_title .= ' OR ';
        $clause_subtitle .= ' OR ';
        $clause_desc .= ' OR ';
      }

      $clause_title .= sprintf("%s.%s like '%%" . str_replace('"', '', $w) . "%%'", $table, 's_title');
      $clause_subtitle .= sprintf("%s.%s like '%%" . str_replace('"', '', $w) . "%%'", $table, 's_subtitle');
      $clause_desc .= sprintf("%s.%s like '%%" . str_replace('"', '', $w) . "%%'", $table, 's_description');
      $k++;
    }
  }

  $clause_title .= ')';
  $clause_subtitle .= ')';
  $clause_desc .= ')';

  return '(' . $clause_title . ' OR ' . $clause_subtitle . ' OR ' . $clause_desc . ')';
}


// CREATE SLUG FROM TITLE
function blg_slug($text, $alt_text = '') {
  if(blg_param('sanitize') == 1) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '', $text)));
  } else {
    $slug = strtolower(trim($text));
  }
  
  if($slug == '') {
    return ($alt_text != '' ? $alt_text : 'blog');
  }

  return $slug;
}


// GET USER
function blg_find_user() {
  $user_id = Params::getParam('id');
  
  if($user_id <> '' && $user_id > 0) {
    $user = User::newInstance()->findByPrimaryKey($user_id);
    echo json_encode(array('user' => array('id' => $user_id, 'name' => $user['s_name'], 'email' => $user['s_email'])));
  } else {
    echo json_encode(array('user' => array('id' => 0, 'name' => '', 'email' =>'')));
  }

  exit;
}

osc_add_hook('ajax_admin_blg_find_user', 'blg_find_user');


// UPDATE BLOG POSITION - AJAX
function blg_position() {
  $order = Params::getParam('blg');

  if(is_array($order) && count($order) > 0) {
    $i = 0;
    foreach($order as $o) {
      ModelBLG::newInstance()->updateBlogPosition($o, $i+1);
      $i++;
    }
  }

  exit;
}

osc_add_hook('ajax_admin_blg_position', 'blg_position');


// UPDATE BLOG POSITION - AJAX
function blg_cat_position() {
  $order = Params::getParam('cat');

  if(is_array($order) && count($order) > 0) {
    $i = 0;
    foreach($order as $o) {
      ModelBLG::newInstance()->updateCategoryPosition($o, $i+1);
      $i++;
    }
  }

  exit;
}

osc_add_hook('ajax_admin_blg_cat_position', 'blg_cat_position');



// GET CORRECT LOCALE VALUE
function blg_field($column, $data, $locale = '') {
  if ($locale == '') {
    $locale = osc_current_user_locale();
  }
 
  $value = @$data[$locale][$column];

  if($value == '') {
    $value = @$data[osc_language()][$column];     // default osclass language

    if($value == '') {
      $aLocales = osc_get_locales();
      foreach($aLocales as $locale) {
        $value = @$data[@$locale['pk_c_code']][$column];
        if($value != '') {
          break;
        }
      }
    }
  }

  return (string) $value;
}


// GENERATE STATUS LABEL
function blg_status($status) {
  $html = '';

  if($status == 0) { 
    $html = '<span class="st0 mb-has-tooltip" title="' . osc_esc_html(__('Post is visible to it\'s author only', 'blog')) . '"><i class="fa fa-user-secret"></i> <span>' . __('Private', 'blog') . '</span></span>';
  } else if($status == 1) { 
    $html = '<span class="st1 mb-has-tooltip" title="' . osc_esc_html(__('Post is visible to anyone', 'blog')) . '"><i class="fa fa-users"></i> <span>' . __('Public', 'blog') . '</span></span>';
  } else if($status == 2) { 
    $html = '<span class="st2 mb-has-tooltip" title="' . osc_esc_html(__('Post is visible to premium users only (member of group)', 'blog')) . '"><i class="fa fa-star"></i> <span>' . __('Premium', 'blog') . '</span></span>';
  } else {
    $html = '<span class="st3 mb-has-tooltip" title="' . osc_esc_html(__('Post is not visible to anyone', 'blog')) . '"><i class="fa fa-question"></i> <span>' . __('Unknown', 'blog') . '</span></span>';
  }

  return $html;
}


// GENERATE AUTHOR LABEL
function blg_author($id) {
  $html = '';

  $user = ModelBLG::newInstance()->getUser($id);
  $img = blg_user_img(@$user['s_image']);

  if(@$user['s_name'] <> '') { 
    $name = $user['s_name'];
  } else {
    $name = __('No author', 'blog');
  }

  $html = '<span class="author-img"><img src="' . $img . '" alt="' . osc_esc_html($name) . '"/></span> <span class="author-name">' . $name . '</span>';

  return $html;
}


// GET BLOG IMAGE IN FRONT
function blg_img_link($file) {
  $img = blg_img(0, $file);

  if(!$img) {
    return osc_base_url() . 'oc-content/plugins/blog/img/blog-default.png';
  }

  return $img;
}


// GET BLOG IMAGE LINK
function blg_img($id, $file_name = '', $version = 0) {
  if($version == 1) {
    $v = '?v=' . date('YmdHis');
  } else {
    $v = '';
  }

  if($file_name == 'blog-default.png') {
    return osc_base_url() . 'oc-content/plugins/blog/img/blog-default.png';
    
  } else if($file_name <> '') {
    //return osc_base_url() . 'oc-content/plugins/blog/img/blog/' . $file_name . $v;
    return blg_file_url($file_name, 'blog') . $v;
    
  } else {
    $blog = ModelBLG::newInstance()->getBlog($id);

    if(@$blog['s_image'] <> '') {
      // return osc_base_url() . 'oc-content/plugins/blog/img/blog/' . $blog['s_image'] . $v;
      return blg_file_url($blog['s_image'], 'blog') . $v;

    } else {
      return false;
    }
  }
}


// GET USER IMAGE LINK
function blg_user_img($file_name = '', $version = 0) {
  if($version == 1) {
    $v = '?v=' . date('YmdHis');
  } else {
    $v = '';
  }

  if($file_name <> '' && $file_name <> 'user-default.png') {
    // return osc_base_url() . 'oc-content/plugins/blog/img/user/' . $file_name . $v;
    return blg_file_url($file_name, 'user') . $v;
    
  } else {
    return osc_base_url() . 'oc-content/plugins/blog/img/user-default.png';
  }
}


// ADD NOTIFICATION TO ADMIN TOOLBAR MENU
function blg_admin_toolbar_comments(){
  if( !osc_is_moderator() ) {
    $total = ModelBLG::newInstance()->countCommentsByType(0);

    if($total > 0) {
      $title = '<i class="circle circle-red">'.$total.'</i>' . ($total == 1 ? __('Blog comment', 'blog') : __('Blog comments', 'blog'));
      AdminToolbar::newInstance()->add_menu(
        array(
          'id' => 'blog_comment',
          'title' => $title,
          'href'  => osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/comment.php&enabled=-1',
          'meta'  => array('class' => 'action-btn action-btn-black')
        )
      );
    }
  }
}

osc_add_hook( 'add_admin_toolbar_menus', 'blg_admin_toolbar_comments', 1 );



// ADD NOTIFICATION TO ADMIN TOOLBAR MENU
function blg_admin_toolbar_articles(){
  if( !osc_is_moderator() ) {
    $total = ModelBLG::newInstance()->countBlogs(0);

    if($total > 0) {
      $title = '<i class="circle circle-red">'.$total.'</i>' . ($total == 1 ? __('Blog article', 'blog') : __('Blog articles', 'blog'));
      AdminToolbar::newInstance()->add_menu(
        array(
          'id' => 'blog_article',
          'title' => $title,
          'href'  => osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/list.php&statusId=0',
          'meta'  => array('class' => 'action-btn action-btn-black')
        )
      );
    }
  }
}

osc_add_hook( 'add_admin_toolbar_menus', 'blg_admin_toolbar_articles', 1 );



// GET PLUGIN PARAMETER
function blg_param($name) {
  return osc_get_preference($name, 'plugin-blog');
}


// CHECK IF RUNNING ON DEMO
function blg_is_demo($ignore_admin = false) {
  if(!$ignore_admin && osc_logged_admin_username() == 'admin') {
    return false;
  } else if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'],'abprofitrade') !== false)) {
    return true;
  } else {
    return false;
  }
}


// GET CURRENT OR DEFAULT ADMIN LOCALE
function blg_get_locale() {
  $locales = OSCLocale::newInstance()->listAllEnabled();

  if(Params::getParam('blgLocale') <> '') {
    $current = Params::getParam('blgLocale');
  } else {
    $current = (osc_current_user_locale() <> '' ? osc_current_user_locale() : osc_current_admin_locale());
    $current_exists = false;

    // check if current locale exist in front-office
    foreach( $locales as $l ) {
      if($current == $l['pk_c_code']) {
        $current_exists = true;
      }
    }

    if( !$current_exists ) {
      $i = 0;
      foreach( $locales as $l ) {
        if( $i==0 ) {
          $current = $l['pk_c_code'];
        }

        $i++;
      }
    }
  }

  return $current;
}


// CREATE LOCALE SELECT BOX
function blg_locale_box( $file, $param = '', $id = -1 ) {
  $html = '';
  $locales = OSCLocale::newInstance()->listAllEnabled();
  $current = blg_get_locale();

  $id_string = '';
  if($id > 0 && $param <> '') {
    $id_string = '&' . $param . '=' . $id;
  }

  $html .= '<select rel="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=blog/admin/' . $file . $id_string . '" class="mb-select mb-select-locale" id="blgLocale" name="blgLocale">';

  foreach( $locales as $l ) {
    $html .= '<option value="' . $l['pk_c_code'] . '" ' . ($current == $l['pk_c_code'] ? 'selected="selected"' : '') . '>' . $l['s_name'] . '</option>';
  }
 
  $html .= '</select>';
  return $html;
}


// Add native lazy loading to img tags in HTML content
function blg_lazy_images($html) {
  if($html == '' || stripos($html, '<img') === false) {
    return $html;
  }

  return preg_replace('/<img(?![^>]*\bloading\s*=)/i', '<img loading="lazy"', $html);
}



// CORE FUNCTIONS
if(!function_exists('mb_param_update')) {
  function mb_param_update( $param_name, $update_param_name, $type = NULL, $plugin_var_name = NULL ) {
    $val = '';
    if( $type == 'check') {

      // Checkbox input
      if( Params::getParam( $param_name ) == 'on' ) {
        $val = 1;
      } else {
        if( Params::getParam( $update_param_name ) == 'done' ) {
          $val = 0;
        } else {
          $val = ( osc_get_preference( $param_name, $plugin_var_name ) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
        }
      }
      
    } else if ($type == 'code') {

      if( Params::getParam( $update_param_name ) == 'done' && Params::existParam($param_name)) {
        $val = stripslashes(Params::getParam( $param_name, false, false ));
      } else {
        $val = ( osc_get_preference( $param_name, $plugin_var_name) != '' ) ? stripslashes(osc_get_preference( $param_name, $plugin_var_name )) : '';
      }
      
    } else {

      // Other inputs (text, password, ...)
      if( Params::getParam( $update_param_name ) == 'done' && Params::existParam($param_name)) {
        $val = Params::getParam( $param_name );
      } else {
        $val = ( osc_get_preference( $param_name, $plugin_var_name) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
      }
    }


    // If save button was pressed, update param
    if( Params::getParam( $update_param_name ) == 'done' ) {

      if(osc_get_preference( $param_name, $plugin_var_name ) == '') {
        osc_set_preference( $param_name, $val, $plugin_var_name, 'STRING');  
      } else {
        $dao_preference = new Preference();
        $dao_preference->update( array( "s_value" => $val ), array( "s_section" => $plugin_var_name, "s_name" => $param_name ));
        osc_reset_preferences();
        unset($dao_preference);
      }
    }

    return $val;
  }
}


if(!function_exists('mb_param_update_code')) {
  function mb_param_update_code( $param_name, $update_param_name, $type = NULL, $plugin_var_name = NULL ) {
    $val = '';

    // code type only
    if( Params::getParam( $update_param_name ) == 'done' && Params::existParam($param_name)) {
      $val = stripslashes(Params::getParam( $param_name, false, false ));
    } else {
      $val = ( osc_get_preference( $param_name, $plugin_var_name) != '' ) ? stripslashes(osc_get_preference( $param_name, $plugin_var_name )) : '';
    }
    

    // If save button was pressed, update param
    if( Params::getParam( $update_param_name ) == 'done' ) {

      if(osc_get_preference( $param_name, $plugin_var_name ) == '') {
        osc_set_preference( $param_name, $val, $plugin_var_name, 'STRING');  
      } else {
        $dao_preference = new Preference();
        $dao_preference->update( array( "s_value" => $val ), array( "s_section" => $plugin_var_name, "s_name" => $param_name ));
        osc_reset_preferences();
        unset($dao_preference);
      }
    }

    return $val;
  }
}


if(!function_exists('message_ok')) {
  function message_ok( $text ) {
    $final  = '<div class="flashmessage flashmessage-ok flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


if(!function_exists('message_error')) {
  function message_error( $text ) {
    $final  = '<div class="flashmessage flashmessage-error flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


if( !function_exists('osc_is_contact_page') ) {
  function osc_is_contact_page() {
    $location = Rewrite::newInstance()->get_location();
    $section = Rewrite::newInstance()->get_section();
    
    if( $location == 'contact' ) {
      return true ;
    }

    return false ;
  }
}


// COOKIES WORK
if(!function_exists('mb_set_cookie')) {
  function mb_set_cookie($name, $val) {
    Cookie::newInstance()->set_expires( 86400 * 30 );
    Cookie::newInstance()->push($name, $val);
    Cookie::newInstance()->set();
  }
}


if(!function_exists('mb_get_cookie')) {
  function mb_get_cookie($name) {
    return Cookie::newInstance()->get_value($name);
  }
}

if(!function_exists('mb_drop_cookie')) {
  function mb_drop_cookie($name) {
    Cookie::newInstance()->pop($name);
  }
}
