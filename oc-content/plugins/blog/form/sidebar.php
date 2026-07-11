<?php
  $categories = ModelBLG::newInstance()->getCategories();
  $popular = ModelBLG::newInstance()->getPopularBlogs();
  $authors = ModelBLG::newInstance()->getAuthors();
  $comments = array();
  
  if(blg_param('comment_enabled') == 1) {
    $comments = ModelBLG::newInstance()->getLatestComments();
  }
  
  if(Params::getParam('blogId') > 0) {
    $blog_id = Params::getParam('blogId');
    $blog = ModelBLG::newInstance()->getBlogDetail($blog_id);
    $author = ModelBLG::newInstance()->getAuthor($blog['fk_i_user_id']);
  }
 
  $logged_user = false;
  if(osc_is_web_user_logged_in()) {
    $logged_user = ModelBLG::newInstance()->getUserByOsclassId(osc_logged_user_id());
  }

  $location = osc_get_osclass_location();
  $section = osc_get_osclass_section();
?>


<div id="blg-side">
  <?php echo blg_banner('side_top'); ?>

  <?php if(($location == 'blg' && $section == 'article') || Params::getParam('route') == 'blg-post') { ?>
    <?php if(osc_is_web_user_logged_in() && $logged_user && $logged_user['pk_i_id'] == $blog['fk_i_user_id']) { ?>
      <div class="blg-side-block blg-edit-post">
        <a class="blg-btn blg-btn-secondary" href="<?php echo osc_route_url('blg-edit', array('blogId' => $blog_id)); ?>"><?php _e('Edit article', 'blog'); ?></a>
      </div>
    <?php } ?>
  <?php } ?>

  <?php if((($location == 'blg' && $section <> 'article' && $section <> 'publish') || ($location == 'custom' && Params::getParam('route') != 'blg-publish' && Params::getParam('route') != 'blg-post'))) { ?>
    <?php if(osc_is_web_user_logged_in() && $logged_user !== false) { ?>
      <div class="blg-side-block blg-add-post">
        <a class="blg-btn blg-btn-primary" href="<?php echo osc_route_url('blg-publish'); ?>"><?php _e('Add new article', 'blog'); ?></a>
      </div>
    <?php } ?>
  <?php } ?>

  <?php if(($location == 'blg' && $section == 'article' || Params::getParam('route') == 'blg-post') && Params::getParam('blogId') > 0 && isset($author['pk_i_id'])) { ?>
    <div class="blg-side-block blg-about-author">
      <div class="blog-author-wrap">
        <div class="blg-author-img"><img src="<?php echo blg_user_img($author['s_image']); ?>" alt="<?php echo osc_esc_html($author['s_name']); ?>" loading="lazy"/></div>
        <div class="blg-author-name">
          <a href="<?php echo osc_route_url('blg-author', array('authorSlug' => osc_sanitizeString(blg_slug($author['s_name'], 'author')), 'authorId' => $author['pk_i_id'])); ?>" class="blg-author-link">
            <?php echo $author['s_name']; ?>
          </a>
        </div>

        <?php if($author['s_about'] <> '') { ?>
          <div class="blg-author-about"><?php echo $author['s_about']; ?></div>
        <?php } ?>

        <?php if($author['s_skills'] <> '') { ?>
          <div class="blg-author-skills"><?php echo $author['s_skills']; ?></div>
        <?php } ?>

        <div class="blg-author-footer">
          <div class="blg-footer-entry">
            <div class="blg-lab"><?php _e('Posts', 'blog'); ?></div>
            <div class="blg-val"><?php echo $author['blog_count']; ?></div>
          </div>

          <div class="blg-footer-entry">
            <div class="blg-lab"><?php _e('Registered', 'blog'); ?></div>
            <div class="blg-val"><?php echo date('m/Y', strtotime($author['dt_reg_date'])); ?></div>
          </div>
        </div>


      </div>
    </div>
  <?php } ?>


  <div class="blg-side-block blg-search">
    <div class="blg-side-header"><?php _e('Search on blog', 'blog'); ?></div>
    
    <form class="nocsrf" method="POST" name="blg_search" action="<?php echo osc_route_url('blg-action', array('blgPage' => 'search')); ?>">
      <input type="text" name="blgSearch" id="blgSearch" value="<?php echo osc_esc_html(urldecode(Params::getParam('keyword'))); ?>" required/>
      <button type="submit" title="<?php echo osc_esc_html(__('Search', 'blog')); ?>"><i class="fa fa-search"></i></button>
    </form>
  </div>


  <div class="blg-side-block blg-categories">
    <div class="blg-side-header"><?php _e('Categories', 'blog'); ?></div>

    <?php if(count($categories) > 0) { ?>
      <?php foreach($categories as $c) { ?>
        <div class="blg-row blg-cat-entry" <?php if($c['s_color'] <> '') { ?>style="border-left-color:<?php echo $c['s_color']; ?>;border-right-color:<?php echo $c['s_color']; ?>;"<?php } ?>>
          <a class="blg-cat-title" href="<?php echo osc_route_url('blg-category', array('categorySlug' => blg_get_category_slug($c), 'categoryId' => $c['pk_i_id'])); ?>"><?php echo blg_get_cat_name($c); ?></a>

          <?php if(blg_get_cat_description($c) <> '') { ?>
            <div class="blg-cat-desc"><?php echo blg_get_cat_description($c); ?></div>
          <?php } ?>
        </div>
      <?php } ?>
    <?php } ?>
  </div>

  <?php echo blg_banner('side_middle'); ?>

  <div class="blg-side-block blg-popular">
    <div class="blg-side-header"><?php _e('Most popular', 'blog'); ?></div>

    <?php if(count($popular) > 0) { ?>
      <?php $i = 1; ?>
      <?php foreach($popular as $p) { ?>
        <div class="blg-row blg-popular-entry">
          <span class="blg-popular-order"><?php echo $i; ?>.</span>
          <a class="blg-popular-title" href="<?php echo osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString(blg_get_slug($p, 'article')), 'blogId' => $p['pk_i_id'])); ?>"><?php echo osc_highlight(blg_get_title($p), 70); ?></a>
          <span class="blg-popular-count"><?php echo $p['i_view']; ?></span>
        </div>

        <?php $i++; ?>
      <?php } ?>
    <?php } ?>
  </div>


  <?php if((($location == 'blg' && $section <> 'article') || ($location == 'custom' && Params::getParam('route') != 'blg-post')) && count($authors) > 0) { ?>
    <div class="blg-side-block blg-authors">
      <div class="blg-side-header"><?php _e('Blog authors', 'blog'); ?></div>

      <div class="blog-authors-wrap">
        <?php if(count($authors) > 0) { ?>
          <?php foreach($authors as $a) { ?>
            <div class="blg-row blg-author-entry">
              <a href="<?php echo osc_route_url('blg-author', array('authorSlug' => osc_sanitizeString(blg_slug($a['s_name'], 'author')), 'authorId' => $a['pk_i_id'])); ?>" class="blg-author-label">
                <div class="author-img"><img src="<?php echo blg_user_img($a['s_image']); ?>" alt="<?php echo osc_esc_html($a['s_name']); ?>" loading="lazy"/></div>
                <div class="author-name">
                  <strong><?php echo $a['s_name']; ?></strong>
                  <em><?php echo ($a['blog_count'] == 1 ? __('1 article', 'blog') : sprintf(__('%d articles', 'blog'), $a['blog_count'])); ?></em>
                </div>
              </a>
            </div>
          <?php } ?>
        <?php } ?>
      </div>
    </div>
  <?php } ?>

  <?php if(blg_param('comment_enabled') == 1) { ?>
    <div class="blg-side-block blg-latest-comments">
      <div class="blg-side-header"><?php _e('Lately commented', 'blog'); ?></div>

      <?php if(count($comments) > 0) { ?>
        <?php foreach($comments as $c) { ?>
          <?php $blog = $c['blog']; ?>

          <div class="blg-row blg-comment-entry">
            <div class="blog-comment-text"><?php echo osc_highlight($c['s_comment'], 72); ?> &middot; </div>
            <a class="blg-comment-blog" href="<?php echo osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString(blg_get_slug($blog, 'article')), 'blogId' => $blog['pk_i_id'])); ?>"><?php echo osc_highlight(blg_get_title($blog), 36); ?></a>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
  <?php } ?>
  
  <?php echo blg_banner('side_bottom'); ?>
</div>