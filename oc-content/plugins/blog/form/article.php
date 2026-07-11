<?php
  $blog_id = Params::getParam('blogId');
  $blog = ModelBLG::newInstance()->getBlogDetail($blog_id);
  
  if(!isset($blog['pk_i_id'])) {
    osc_add_flash_error_message(__('Article does not exists', 'blog'));
    header('Location:' . osc_route_url('blg-home'));
    exit;
  }
  
  $author = ModelBLG::newInstance()->getAuthor($blog['fk_i_user_id']);
  $comments = array();
  
  if(blg_param('comment_enabled') == 1) {
    $comments = ModelBLG::newInstance()->getComments($blog_id, 1);
  }
  
  ModelBLG::newInstance()->updateBlogViews($blog_id);
  
  $blog_url = osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString(blg_get_slug($blog)), 'blogId' => $blog['pk_i_id']));
  $canonical_url = blg_canonical_url($blog);
  
  if(blg_current_url() != $canonical_url && blg_param('canonical_redirect') == 1) {
    header('Location:' . $canonical_url);
    exit;
  }
?>

<div id="blg-body" class="blg-theme-<?php echo osc_current_web_theme(); ?>">
  <div id="blg-main" class="blg-content">
    <?php echo blg_banner('article_top'); ?>
    
    <h1><?php echo blg_get_title($blog); ?></h1>

    <?php if(blg_get_subtitle($blog) <> '') { ?>
      <h2><?php echo blg_get_subtitle($blog); ?></h2>
    <?php } ?>

    <div class="blg-details">
      <?php if($blog['i_category'] > 0) { ?>
        <?php $blog_cat = ModelBLG::newInstance()->getCategoryDetail($blog['i_category']); ?>
        <div class="blg-detail-entry blg-detail-cat" <?php if($blog_cat['s_color'] <> '') { ?>style="background:<?php echo $blog_cat['s_color']; ?>;"<?php } ?>><?php echo blg_get_cat_name($blog_cat); ?></div>
      <?php } else { ?>
        <div class="blg-detail-entry blg-detail-cat"><?php _e('Uncategorized', 'blog'); ?></div>
      <?php } ?>

      <div class="blg-detail-entry"><?php echo date('j. M Y', strtotime($blog['dt_pub_date'])); ?></div>
      <div class="blg-detail-entry"><?php echo sprintf(__('%d views', 'blog'), $blog['i_view']); ?></div>
    </div>

    <?php echo blg_banner('article_subtop'); ?>

    <div class="blg-content-text">
      <div class="blg-primary-img">
        <a href="<?php echo blg_img_link($blog['s_image']); ?>">
          <img src="<?php echo blg_img_link($blog['s_image']); ?>" alt="<?php echo osc_esc_html(blg_get_title($blog)); ?>" loading="lazy"/>
        </a>
      </div>

      <div class="blg-raw-text">
        <?php
          if(!blg_is_premium($blog)) {
            echo blg_lazy_images(blg_get_description($blog));
          } else {
            echo blg_lazy_images(osc_highlight(blg_get_description($blog), 800));
          }
        ?>
      </div>

      <?php if(blg_is_premium($blog)) { ?>
        <div class="blg-premium-cover"></div>
      <?php } ?>
    </div>
    
    <?php blg_share_block($blog_url, blg_img_link($blog['s_image']), blg_get_title($blog)); ?>
    
    <?php echo blg_banner('article_middle'); ?>

    <?php require 'premium.php'; ?>


    <?php if(blg_param('comment_enabled') == 1) { ?>
      <div class="blg-comments">
        <h2><?php _e('Comments', 'blog'); ?></h2>

        <?php if(count($comments) > 0) { ?>
          <?php $j = 0; ?>
          <?php foreach($comments as $c) { ?>
            <?php
              $j++;
              $cuser = User::newInstance()->findByPrimaryKey($c['fk_i_os_user_id']);
              $cauthor = ModelBLG::newInstance()->getUserByOsclassId($c['fk_i_os_user_id']);

              if(!$cauthor) {
                $is_author = false;
              } else {
                $is_author = true;
              }
            ?>


            <div class="blg-row<?php if($j == 1) { ?> blg-comment-first<?php } ?>">
              <div class="blg-comment-img">
                <?php 
                  $cimg = '';

                  if($is_author) { 
                    $cname = $cauthor['s_name'];
                    $cimg = blg_user_img($cauthor['s_image']);
                  } else {
                    $cname = (@$cuser['s_name'] <> '' ? $cuser['s_name'] : __('Anonymous', 'blog'));

                    if(function_exists('profile_picture_show')) {
                      if($c['fk_i_os_user_id'] > 0) {
                        $cimg = profile_picture_show(null, 'item', 200, null, $c['fk_i_os_user_id']);
                      } 
                    }
                  }

                  if($cimg == '') {
                    $cimg = osc_base_url() . 'oc-content/plugins/blog/img/user-default.png';
                  }
                ?>

                <img src="<?php echo $cimg; ?>" alt="<?php echo osc_esc_html($cname); ?>" loading="lazy"/>
              </div>

              <div class="blg-comment-text">
                <div class="blg-comment-top">
                  <span class="blg-comment-user">
                    <?php if($is_author && @$cauthor['pk_i_id'] > 0) { ?>
                      <a href="<?php echo osc_route_url('blg-author', array('authorSlug' => osc_sanitizeString(blg_slug($cauthor['s_name'])), 'authorId' => $cauthor['pk_i_id'])); ?>" class="blg-comment-author-link"><?php echo $cname; ?></a>
                    <?php } else { ?>
                      <?php echo $cname; ?>
                    <?php } ?>
                  </span>

                  <span class="blg-comment-del"><?php _e('on', 'blog'); ?></span>
                  <span class="blg-comment-date"><?php echo date('j. M Y', strtotime($c['dt_pub_date'])); ?></span>
                </div>

                <div class="blg-comment-bot"><?php echo strip_tags($c['s_comment'], '<br>'); ?></div>
              </div>
            </div>
          <?php } ?>
        <?php } else { ?>
          <div class="blg-row blg-empty"><?php _e('No comments has been added on this post', 'blog'); ?></div>
        <?php } ?>
      </div>

      <div class="blg-new-comment">
        <h3><?php _e('Add new comment', 'blog'); ?></h3>

        <?php if(!osc_is_web_user_logged_in()) { ?>
          <div class="blg-row blg-not-logged"><?php _e('You must be logged in to add new comment', 'blog'); ?>. <a href="<?php echo osc_user_login_url(); ?>"><?php _e('Log in', 'blog'); ?></a></div>

        <?php } else { ?>
          <form class="nocsrf" method="POST" name="blg_new_comment" action="<?php echo osc_route_url('blg-action', array('blgPage' => 'comment')); ?>">
            <input type="hidden" name="fk_i_blog_id" value="<?php echo $blog_id; ?>" />

            <div class="blg-row">
              <label for="s_name"><?php _e('Your name', 'blog'); ?></label>
              <input type="text" name="s_name" id="s_name" value="<?php echo osc_esc_html(osc_logged_user_name()); ?>" readonly required/>
            </div>

            <div class="blg-row">
              <label for="s_comment"><?php _e('Comment', 'blog'); ?></label>
              <textarea name="s_comment" id="s_comment" required></textarea>
            </div>

            <button class="blg-btn blg-btn-primary" type="submit" title="<?php echo osc_esc_html(__('Submit', 'blog')); ?>"><i class="fa fa-check"></i> <?php echo osc_esc_html(__('Submit', 'blog')); ?></button>
          </form>
        <?php } ?>
      </div>
    <?php } ?>
    
    <?php echo blg_banner('article_bottom'); ?>
  </div>

  <?php require_once 'sidebar.php'; ?>
</div>