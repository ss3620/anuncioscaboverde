<?php
  // Create menu
  $title = __('Blog Posts', 'blog');
  blg_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value
  $active_template = mb_param_update('active_template', 'plugin_action', 'value', 'plugin-blog');


  if(Params::getParam('plugin_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'blog') );
  }


  if(Params::getParam('removeId') > 0) {
    ModelBLG::newInstance()->removeBlog(Params::getParam('removeId'));
    message_ok( __('Post removed successfully', 'blog') );
  }

  $def_per_page = 20;
  $page_id = (Params::getParam('pageId') > 0 ? (int)Params::getParam('pageId') : 0);
  $per_page = $def_per_page;

  $status_id = (Params::getParam('statusId') <> '' ? Params::getParam('statusId') : -1);
  $category_id = (Params::getParam('categoryId') <> '' ? Params::getParam('categoryId') : -1);
  $author_id = (Params::getParam('authorId') <> '' ? Params::getParam('authorId') : -1);

  $param_string = '';
  if($status_id != -1) { $param_string .= '&statusId=' . (int)$status_id; }
  if($category_id != -1) { $param_string .= '&categoryId=' . (int)$category_id; }
  if($author_id != -1) { $param_string .= '&authorId=' . (int)$author_id; }

  $count_all = ModelBLG::newInstance()->countBlogs($status_id, $category_id, $author_id);
  $blogs = ModelBLG::newInstance()->getBlogs($status_id, $category_id, $author_id, '', '', '', array($per_page, $page_id));

  $blg_reorder_enabled = ($status_id == -1 && $category_id == -1 && $author_id == -1 && blg_param('blog_order') == 1 && ($page_id <= 0 || $page_id == 1) && $count_all <= $per_page);

?>



<div class="mb-body">

  <div class="mb-message-js"></div>

  <!-- BLOG SECTION -->
  <div class="mb-box">
    <div class="mb-head">
      <i class="fa fa-list"></i> <?php _e('Blog posts', 'blog'); ?>
    </div>

    <div class="mb-inside mb-blog" id="mb-blog">

      <?php if(count($blogs) > 0) { ?>
        <?php foreach($blogs as $b) { ?>
          <?php $comments_count = ModelBLG::newInstance()->countComments($b['pk_i_id']); ?>

          <div class="mb-one" data-id="<?php echo $b['pk_i_id']; ?>" id="blg_<?php echo $b['pk_i_id']; ?>">
            <div class="mb-line">
              <span class="img"><img src="<?php if(blg_img($b['pk_i_id'])) { echo blg_img($b['pk_i_id'], '', 1); } else { echo blg_img(0, 'blog-default.png', 1); } ?>"/></span>
              <span class="title">
                <?php echo ($b['s_title'] <> '' ? $b['s_title'] : __('New post', 'blog')); ?>
                <?php if($comments_count > 0) { ?>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/comment.php&blogId=<?php echo $b['pk_i_id']; ?>" class="cm mb-has-tooltip" title="<?php echo osc_esc_html(sprintf(__('%d comment(s) on post.', 'blog'), $comments_count) . '<br/>' . __('Click to show these comments.', 'blog')); ?>"><?php echo $comments_count; ?></a>
                <?php } ?>
              </span>
              <span class="author">
                <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/list.php&authorId=<?php echo $b['fk_i_user_id']; ?>">
                  <?php echo blg_author($b['fk_i_user_id']); ?>
                </a>
              </span>
              <span class="status"><?php echo blg_status($b['i_status']); ?></span>
              <span class="date"><span class="mb-has-tooltip" title="<?php echo $b['dt_pub_date']; ?>"><?php echo date('j. M Y', strtotime($b['dt_pub_date'])); ?></span></span>

              <?php if(!blg_is_demo()) { ?>
                <span class="remove blgbtn"><a class="mb-has-tooltip" title="<?php echo osc_esc_html(__('Delete post', 'blog')); ?>" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/list.php&removeId=<?php echo $b['pk_i_id']; ?><?php echo $param_string; ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this post? Action cannot be undone', 'blog')); ?>')"><i class="fa fa-trash-o"></i></a></span>
                <span class="edit blgbtn"><a class="mb-has-tooltip" title="<?php echo osc_esc_html(__('Edit in new window', 'blog')); ?>" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/blog.php&blogId=<?php echo $b['pk_i_id']; ?>"><i class="fa fa-pencil"></i></a></span>
              <?php } else { ?>
                <span class="remove blgbtn"><a class="mb-has-tooltip mb-disabled" disabled title="<?php echo osc_esc_html(__('You cannot remove article, it is demo site', 'blog')); ?>" href="#" onclick="return false"><i class="fa fa-trash-o"></i></a></span>
                <span class="edit blgbtn"><a class="mb-has-tooltip mb-disabled" disabled title="<?php echo osc_esc_html(__('You cannot edit article, it is demo site', 'blog')); ?>" href="#" onclick="return false"><i class="fa fa-pencil"></i></a></span>
              <?php } ?>

              <span class="open blgbtn"><a class="mb-has-tooltip" target="_blank" title="<?php echo osc_esc_html(__('Open in front', 'blog')); ?>" href="<?php echo osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString($b['s_slug'] <> '' ? $b['s_slug'] : $b['s_title']), 'blogId' => $b['pk_i_id'])); ?>"><i class="fa fa-external-link"></i></a></span>

              <?php if($blg_reorder_enabled) { ?>
                <span class="move blgbtn"><i class="fa fa-arrows mb-has-tooltip" title="<?php echo osc_esc_html(__('Reorder posts', 'blog')); ?>"></i></span>
              <?php } ?>
            </div>
          </div>
        <?php } ?>

        <?php echo blg_admin_paginate('blog/admin/list.php', Params::getParam('pageId'), $per_page, $count_all, '', $param_string); ?>
      <?php } else { ?>
        <?php if($status_id >= 0) { ?>
          <div class="mb-no-blogs"><?php _e('No blog posts with selected status found', 'blog'); ?></div>
        <?php } else if($category_id >= 0) { ?>
          <div class="mb-no-blogs"><?php _e('You have not created any blog post in this category yet', 'blog'); ?></div>
        <?php } else if($author_id >= 0) { ?>
          <div class="mb-no-blogs"><?php _e('This author hasn\'t created any blog posts yet', 'blog'); ?></div>
        <?php } else { ?>
          <div class="mb-no-blogs"><?php _e('You have not created any blog post yet', 'blog'); ?></div>
        <?php } ?>
      <?php } ?>

      <?php if($status_id == -1) { ?>
        <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/blog.php" class="mb-add-blog"><i class="fa fa-plus-circle"></i><?php _e('Add new post', 'blog'); ?></a>
      <?php } ?>
    </div>
  </div>
</div>


<script type="text/javascript">
  var blg_position_url = "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=runhook&hook=blg_position";

  var blg_message_ok = "<?php echo osc_esc_html(__('Success!', 'blog')); ?>";
  var blg_message_wait = "<?php echo osc_esc_html(__('Updating, please wait...', 'blog')); ?>";
  var blg_message_error = "<?php echo osc_esc_html(__('Error!', 'blog')); ?>";


  $(document).ready(function(){
    <?php if($blg_reorder_enabled) { ?>
    var blg_list = '';

    $('#mb-blog').sortable({
      axis: "y",
      forcePlaceholderSize: true,
      handle: '.mb-line',
      helper: 'clone',
      items: '.mb-one',
      opacity: .8,
      placeholder: 'placeholder',
      revert: 100,
      tabSize: 5,
      tolerance: 'intersect',
      start: function(event, ui) {
        blg_list = $(this).sortable('serialize');
      },
      stop: function (event, ui) {
        var c_blg_list = $(this).sortable('serialize');

        blg_message(blg_message_wait, 'info');

        if(blg_list != c_blg_list) {
          $.ajax({
            url: blg_position_url,
            type: "GET",
            data: c_blg_list,
            success: function(response){
              //console.log(response);
              blg_message(blg_message_ok, 'ok');
            },
            error: function(response) {
              blg_message(blg_message_error, 'error');
              console.log(response);
            }
          });
        }
      }
    });
    <?php } ?>

  });
</script>


<?php echo blg_footer(); ?>