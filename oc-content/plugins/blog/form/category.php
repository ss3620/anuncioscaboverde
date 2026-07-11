<?php
  $category_id = Params::getParam('categoryId');
  $category = ModelBLG::newInstance()->getCategoryDetail($category_id);
  
  $per_page = blg_get_limits('category');
  $page_id = (Params::getParam('pageId') > 0 ? Params::getParam('pageId') : 0);
  $count_all = ModelBLG::newInstance()->countCategoryBlogs($category_id);

  $blogs = ModelBLG::newInstance()->getCategoryBlogs($category_id, array($per_page, $page_id));
?>


<div id="blg-body" class="blg-theme-<?php echo osc_current_web_theme(); ?>">
  <div id="blg-main">
    <?php echo blg_banner('search_top'); ?>

    <div class="blg-category-result">
      <h1><?php echo blg_get_cat_name($category); ?></h1>

      <?php if(blg_get_cat_description($category) <> '') { ?>
        <h2><?php echo blg_get_cat_description($category); ?></h2>
      <?php } ?>

      <?php 
        if(count($blogs) > 0) {
          $i = 1;

          foreach($blogs as $blog) {
            $limit = ($i == 1 ? 210 : 140);
            $class = ($i == 1 ? 'blg-first' : '');

            blg_article($blog, $class, $limit);
            
            if($i == 2 && count($blogs) >= 4) {
              echo blg_banner('search_middle');
            }

            $i++;
          }
          
          echo blg_paginate('category', array('categorySlug' => blg_get_category_slug($category), 'categoryId' => $category_id), $page_id, $per_page, $count_all, 'blg-pag-category');

        } else { 
          echo '<div class="blg-row blg-empty">' . __('There are no articles in this category', 'blog') . '</div>';
        }
      ?>
    </div>
    
    <?php echo blg_banner('search_bottom'); ?>
  </div>

  <?php require_once 'sidebar.php'; ?>

</div>