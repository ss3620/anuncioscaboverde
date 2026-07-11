<div class="blg-row<?php echo ' ' . $class; ?>">
  <a class="blg-title blg-title-mobile" href="<?php echo osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString(blg_get_slug($b, 'article')), 'blogId' => $b['pk_i_id'])); ?>"><?php echo blg_get_title($b); ?></a>

  <a class="blg-img" href="<?php echo osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString(blg_get_slug($b, 'article')), 'blogId' => $b['pk_i_id'])); ?>">
    <div class="blg-img-wrap">
      <img src="<?php echo blg_img_link($b['s_image']); ?>" alt="<?php echo osc_esc_html(blg_get_title($b)); ?>" loading="lazy"/>
    </div>
  </a>

  <a class="blg-card-link" href="<?php echo osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString(blg_get_slug($b, 'article')), 'blogId' => $b['pk_i_id'])); ?>"></a>


  <div class="blg-img-div">
    <div style="background-image:url('<?php echo blg_img_link($b['s_image']); ?>');"></div>
  </div>

  <div class="blg-text">
    <a class="blg-title" href="<?php echo osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString(blg_get_slug($b, 'article')), 'blogId' => $b['pk_i_id'])); ?>">
      <?php 
        if(osc_get_osclass_section() == 'search') {
          $keyword = urldecode(Params::getParam('keyword'));
          echo str_ireplace($keyword, '<strong>' . $keyword . '</strong>', strip_tags(blg_get_title($b)));
        } else {
          echo strip_tags(blg_get_title($b));
        }
      ?>
    </a>
    <div class="blg-desc">
      <?php 
        if(osc_get_osclass_section() == 'search') {
          $keyword = urldecode(Params::getParam('keyword'));
          echo str_ireplace($keyword, '<strong>' . $keyword . '</strong>', strip_tags(osc_highlight(blg_get_subtitle($b) <> '' ? blg_get_subtitle($b) : blg_get_description($b), $limit)));
        } else {
          echo strip_tags(osc_highlight(blg_get_subtitle($b) <> '' ? blg_get_subtitle($b) : blg_get_description($b), $limit));
        }
      ?>

      <?php if($b['comments_count'] > 0) { ?>
        <span class="blg-comments-count"><i class="fa fa-comments"></i><span><?php echo $b['comments_count']; ?></span></span>
      <?php } ?>               
    </div>

    <?php if($b['i_category'] > 0) { ?>
      <?php $blog_cat = ModelBLG::newInstance()->getCategoryDetail($b['i_category']); ?>
      <div class="blg-blog-category" <?php if($blog_cat['s_color'] <> '') { ?>style="background: <?php echo $blog_cat['s_color']; ?>"<?php } ?>><?php echo blg_get_cat_name($blog_cat); ?></div>
    <?php } else { ?>
      <div class="blg-blog-category blg-uncategorized"><?php _e('Uncategorized', 'blog'); ?></div>
    <?php } ?>
  </div>
</div>