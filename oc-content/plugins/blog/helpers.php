<?php

// GLOBAL HELPERS
function blg_home_link() {
  return osc_route_url('blg-home');
}

function blg_home_button($text = '') {
  if($text == '') {
    $text = __('Blog', 'blog');
  }

  return '<a href="' . blg_home_link() . '" class="blg-button blg-button-home">' . $text . '</a>';
}

function blg_article($blog, $class = '', $limit = 200) {
  $b = $blog;
  require 'form/loop-single.php';
}

function blg_user_blog($user_id = '', $text = '', $link_only = 0) {
  $user_id = ($user_id == '' ? osc_item_user_id() : $user_id);
  $user_id = ($user_id == '' ? osc_premium_user_id() : $user_id);
  $user_id = ($user_id == '' ? osc_user_id() : $user_id);

  if($user_id > 0) {
    $author = ModelBLG::newInstance()->getUserByOsclassId($user_id);

    if($author) {
      $link = osc_route_url('blg-author', array('authorName' => osc_sanitizeString($author['s_name']), 'authorId' => $author['pk_i_id']));
      $text = ($text == '' ? sprintf(__('%s\'s blog', 'blog'), $author['s_name']) : $text);

      if($link_only == 0) {
        return $link;
      } else {
        return '<a href="' . $link . '" class="blg-button blg-button-author">' . $text . '</a>';
      }
    }
  }

  return false;
}

function blg_get_author($user_id) {
  $author = ModelBLG::newInstance()->getUserByOsclassId($user_id);

  if($author && isset($author['pk_i_id']) && $author['pk_i_id'] > 0) {
    return $author;
  }

  return false;
}


// CATEGORY HELPER FUNCTIONS
function blg_get_category($id) {
  return ModelBLG::newInstance()->getCategoryDetail($id);
}


function blg_get_cat_name($category) {
  if(isset($category['locales'])) {
    if(blg_field('name', $category['locales']) <> '') {
      return blg_field('name', $category['locales']);
    }
  }

  if(isset($category['s_name']) && trim($category['s_name']) <> '') {
    return $category['s_name'];
  }
  
  return __('New category', 'blog');
}


function blg_get_cat_description($category) {
  if(isset($category['locales'])) {
    if(blg_field('description', $category['locales']) <> '') {
      return blg_field('description', $category['locales']);
    }
  }

  if(isset($category['s_description']) && trim($category['s_description']) <> '') {
    return $category['s_description'];
  }
  
  return '';
}


// GET SLUG FOR CATEGORY
function blg_get_category_slug($category) {
  $text = osc_sanitizeString(blg_get_cat_name($category));

  if($text == '') {
    $text = 'category';
  }

  return $text;
}



// BLOG HELPER FUNCTIONS
function blg_get_article($id) {
  return ModelBLG::newInstance()->getBlogDetail($id);
}


function blg_get_title($blog) {
  if(isset($blog['locales'])) {
    if(blg_field('title', $blog['locales']) <> '') {
      return blg_field('title', $blog['locales']);
    }
  }

  if(isset($blog['s_title']) && $blog['s_title'] <> '') {
    return $blog['s_title'];
  }
  
  return __('New post', 'blog');
}


// BLOG HELPER FUNCTIONS
function blg_get_subtitle($blog) {
  if(isset($blog['locales'])) {
    if(blg_field('subtitle', $blog['locales']) <> '') {
      return blg_field('subtitle', $blog['locales']);
    }
  }

  if(isset($blog['s_subtitle']) && trim($blog['s_subtitle']) <> '') {
    return $blog['s_subtitle'];
  }
  
  return '';
}


function blg_get_description($blog) {
  if(isset($blog['locales'])) {
    if(blg_field('description', $blog['locales']) <> '') {
      return blg_field('description', $blog['locales']);
    }
  }

  if(isset($blog['s_description']) && trim($blog['s_description']) <> '') {
    return $blog['s_description'];
  }
  
  return '';
}


// GET SLUG FOR BLOG
function blg_get_slug($blog, $sanitize = false) {
  $text = '';
  
  if(isset($blog['s_slug']) && trim($blog['s_slug']) <> '') {
    $text = $blog['s_slug'];
  } else if (blg_get_title($blog) <> '') {
    $text = blg_get_title($blog);
  } else {
    $text = __('post', 'blog');
  }

  if($sanitize === true) {
    return osc_sanitizeString($text);
  }
  
  return $text;
}




function blg_get_seo_title($blog) {
  if(isset($blog['locales'])) {
    if(blg_field('seo_title', $blog['locales']) <> '') {
      return blg_field('seo_title', $blog['locales']);
    }
  }

  if(isset($blog['s_seo_title']) && trim($blog['s_seo_title']) <> '') {
    return $blog['s_seo_title'];
  }
  
  return blg_get_title($blog);
}

function blg_get_seo_description($blog) {
  if(isset($blog['locales'])) {
    if(blg_field('seo_description', $blog['locales']) <> '') {
      return blg_field('seo_description', $blog['locales']);
    }
  }

  if(isset($blog['s_seo_description']) && trim($blog['s_seo_description']) <> '') {
    return $blog['s_seo_description'];
  }
  
  return osc_highlight(blg_get_subtitle($blog) <> '' ? blg_get_subtitle($blog) : blg_get_description($blog), 320);
}
