<?php
  $per_page = blg_get_limits('home');
  $page_id = (Params::getParam('pageId') > 0 ? Params::getParam('pageId') : 0);
  $count_all = ModelBLG::newInstance()->countActiveBlogs();
  $blogs = ModelBLG::newInstance()->getActiveBlogs(array($per_page, $page_id));
?>


<div id="blg-body" class="blg-theme-<?php echo osc_current_web_theme(); ?>">
  <div id="blg-main">
    <?php echo blg_banner('home_top'); ?>
    
    <div class="blg-latest">
      <?php 
        if(is_array($blogs) && count($blogs) > 0) {
          $i = 1;

          foreach($blogs as $blog) {
            $limit = ($i == 1 ? 210 : 140);
            $class = ($i == 1 ? 'blg-first' : '');

            blg_article($blog, $class, $limit);

            $i++;
          }
          
          echo blg_paginate('home', array(), $page_id, $per_page, $count_all, 'blg-pag-home');
        } else { 
          echo '<div class="blg-row blg-empty blg-empty-latest">' . __('There are no articles yet', 'blog') . '</div>';
        }
      ?>
    </div>
    
    <?php echo blg_banner('home_bottom'); ?>
  </div>

  <?php require_once 'sidebar.php'; ?>

</div>