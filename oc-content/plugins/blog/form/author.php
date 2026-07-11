<?php
  $author_id = Params::getParam('authorId');
  $author = ModelBLG::newInstance()->getUser($author_id);
  
  $per_page = blg_get_limits('author');
  $page_id = (Params::getParam('pageId') > 0 ? Params::getParam('pageId') : 0);
  $count_all = ModelBLG::newInstance()->countAuthorBlogs($author_id);

  $blogs = ModelBLG::newInstance()->getAuthorBlogs($author_id, array($per_page, $page_id));
?>


<div id="blg-body" class="blg-theme-<?php echo osc_current_web_theme(); ?>">
  <div id="blg-main">
    <?php echo blg_banner('search_top'); ?>

    <div class="blg-author-result">
      <h1><?php echo sprintf(__('%s\'s articles', 'blog'), $author['s_name']); ?></h1>

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
          
          echo blg_paginate('author', array('authorSlug' => osc_sanitizeString(blg_slug($author['s_name'])), 'authorId' => $author_id), $page_id, $per_page, $count_all, 'blg-pag-author');

        } else { 
          echo '<div class="blg-row blg-empty blg-empty-author">' . __('There are no articles from this author', 'blog') . '</div>';
        }
      ?>
    </div>
    
    <?php echo blg_banner('search_bottom'); ?>
  </div>

  <?php require_once 'sidebar.php'; ?>

</div>