<?php
  $keyword = urldecode(Params::getParam('keyword'));
  $per_page = blg_get_limits('search');
  $page_id = (Params::getParam('pageId') > 0 ? Params::getParam('pageId') : 0);
  $count_all = ModelBLG::newInstance()->countSearchBlogs($keyword);
  
  $blogs = ModelBLG::newInstance()->getSearchBlogs($keyword, array($per_page, $page_id));
?>


<div id="blg-body" class="blg-theme-<?php echo osc_current_web_theme(); ?>">
  <div id="blg-main">
    <?php echo blg_banner('search_top'); ?>
  
    <div class="blg-search-result">
      <h1><?php echo sprintf(__('Search results for <u>%s</u>', 'blog'), $keyword); ?></h1>

      <?php 
        if(count($blogs) > 0) {
          $i = 1;



          foreach($blogs as $blog) {
            $limit = 140;
            $class = '';

            blg_article($blog, $class, $limit);

            if($i == 2 && count($blogs) >= 4) {
              echo blg_banner('search_middle');
            }
          
            $i++;
          }

          echo blg_paginate('search', array('keyword' => $keyword), $page_id, $per_page, $count_all, 'blg-pag-search');

        } else { 
          echo '<div class="blg-row blg-empty blg-empty-latest">' . __('There are no articles matching your search criteria', 'blog') . '</div>';
        }
      ?>
    </div>
    
    <?php echo blg_banner('search_bottom'); ?>
  </div>

  <?php require_once 'sidebar.php'; ?>

</div>