<?php

  // Create menu
  $title = __('Blog post comments', 'blog');
  blg_menu($title);


  if((Params::getParam('editId') > 0 || Params::getParam('editId') == -1) && Params::getParam('plugin_action') == 'done') {
    $id = Params::getParam('editId');

    if($id == -1) {
      $id = ModelBLG::newInstance()->insertComment(array('fk_i_blog_id' => Params::getParam('fk_i_blog_id')));
      Params::setParam('editId', $id);
    }

    $comment = ModelBLG::newInstance()->getComment($id);


    $data_comment = array(
      'pk_i_id' => $id,
      'fk_i_blog_id' => Params::getParam('fk_i_blog_id'),
      'fk_i_os_user_id' => Params::getParam('fk_i_os_user_id'),
      's_comment' => Params::getParam('s_comment'),
      'b_enabled' => (Params::getParam('b_enabled') == 'on' ? 1 : Params::getParam('b_enabled')),
      'dt_pub_date' => Params::getParam('dt_pub_date')
    );


    ModelBLG::newInstance()->updateComment($data_comment);

    message_ok( __('Comment successfully updated', 'blog') );
  }


  if(Params::getParam('deleteId') > 0) {
    ModelBLG::newInstance()->removeComment(Params::getParam('deleteId'));
    message_ok( __('Comment removed successfully', 'blog') );
  }

  if(Params::getParam('approveId') > 0) {
    ModelBLG::newInstance()->approveComment(Params::getParam('approveId'));
    message_ok( __('Comment approved successfully', 'blog') );
  }

  $blog_id = (Params::getParam('blogId') <> '' ? Params::getParam('blogId') : -1);
  $enabled = (Params::getParam('enabled') == -1 ? 0 : (Params::getParam('enabled') == 0 ? -1 : 1));


  $comments = ModelBLG::newInstance()->getComments($blog_id, $enabled);
  $blogs = ModelBLG::newInstance()->getBlogs();
?>


<div class="mb-body">

  <?php if(Params::getParam('editId') > 0 || Params::getParam('editId') == -1) { ?>
    <?php 
      $comment = ModelBLG::newInstance()->getComment(Params::getParam('editId'));
    ?>

    <div class="mb-box">
      <div class="mb-head">
        <i class="fa fa-plus-circle"></i> <?php _e('Add/edit comment', 'blog'); ?>
      </div>

      <div class="mb-inside mb-comment-edit">
        <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
          <input type="hidden" name="page" value="plugins" />
          <input type="hidden" name="action" value="renderplugin" />
          <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>comment.php" />
          <input type="hidden" name="plugin_action" value="done" />
          <input type="hidden" name="editId" value="<?php echo Params::getParam('editId'); ?>" />

          <div class="mb-row">
            <label for="dt_pub_date"><?php _e('Publish Date', 'blog'); ?></label>
            <input type="date" id="dt_pub_date" name="dt_pub_date" size="30" value="<?php echo @$comment['dt_pub_date'] <> '' ? date("Y-m-d", strtotime(@$comment['dt_pub_date'])): date("Y-m-d"); ?>" />

            <div class="mb-explain"><?php _e('Publish date of comment.', 'blog'); ?></div>
          </div>


          <div class="mb-row">
            <label for="s_comment"><?php _e('Comment', 'blog'); ?></label>
            <textarea id="s_comment" name="s_comment"><?php echo strip_tags((string)(@$comment['s_comment'] ?? ''), '<br>'); ?></textarea>

            <div class="mb-explain"><?php _e('User\'s description, hobby, ...', 'blog'); ?></div>
          </div>


          <div class="mb-row">
            <label for="fk_i_blog_id"><?php _e('Blog Post', 'blog'); ?></label>
            <select id="fk_i_blog_id" name="fk_i_blog_id" required>
              <option value="" <?php if(@$comment['fk_i_blog_id'] <= 0) { ?>selected="selected"<?php } ?>><?php _e('No blog post selected', 'blog'); ?></option>

              <?php if(count($blogs) > 0) { ?>
                <?php foreach($blogs as $b) { ?>
                  <option value="<?php echo $b['pk_i_id']; ?>" <?php if($b['pk_i_id'] == @$comment['fk_i_blog_id']) { ?>selected="selected"<?php } ?>><?php echo ($b['s_title'] <> '' ? $b['s_title'] : sprintf(__('Article #%s (%s)', 'blog'), $b['pk_i_id'], blg_get_locale())); ?></option>
                <?php } ?>
              <?php } ?>
            </select>

            <div class="mb-explain"><?php _e('Select blog post to which is this comment related.', 'blog'); ?></div>
          </div>


          <div class="mb-row mb-osclass-user">
            <label for="s_os_name"><span><?php _e('Osclass User', 'blog'); ?></span></label>

            <div class="mb-line">
              <?php $os_user = User::newInstance()->findByPrimaryKey(@$comment['fk_i_os_user_id']); ?>
              <input type="text" id="s_os_name" name="s_os_name" placeholder="<?php echo osc_esc_html(__('Type user name or email', 'blog')); ?>" value="<?php echo osc_esc_html(@$os_user['s_name']); ?>"/>

              <input type="text" id="fk_i_os_user_id" name="fk_i_os_user_id" readonly="readonly" value="<?php echo @$comment['fk_i_os_user_id']; ?>"/>
              <input type="text" id="s_os_email" name="s_os_email" readonly="readonly" placeholder="<?php echo osc_esc_html(__('Email', 'blog')); ?>" value="<?php echo (@$os_user['s_email']); ?>"/>
            </div>

            <div class="mb-explain"><?php _e('Start typing user name or email and select user you want to check from list.', 'blog'); ?></div>
          </div>


          <div class="mb-row">
            <label for="b_enabled"><?php _e('Enabled', 'blog'); ?></label>
            <input type="checkbox" id="b_enabled" name="b_enabled" class="element-slide" <?php echo (isset($comment['b_enabled']) ? ($comment['b_enabled'] == 1 ? 'checked' : '') : 'checked'); ?> />

            <div class="mb-explain"><?php _e('Set to YES to enable comment.', 'blog'); ?></div>
          </div>


          <div class="mb-row">&nbsp;</div>

          <div class="mb-foot">
            <?php if(!blg_is_demo()) { ?><button type="submit" class="mb-button"><?php _e('Save', 'blog');?></button><?php } ?>
          </div>
        </form>
      </div>
    </div>
  <?php } ?>



  <!-- COMMENTS SECTION -->
  <div class="mb-box">
    <div class="mb-head">
      <i class="fa fa-comments"></i> <?php echo $title; ?>
    </div>

    <div class="mb-inside mb-blog-comment">

      <div class="mb-row mb-notes">
        <div class="mb-line"><?php _e('List of comments on blog posts.', 'blog'); ?></div>
      </div>

      <div class="mb-row mb-filters">
        <select id="blogId" name="blogId" rel="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/comment.php&enabled=<?php echo Params::getParam('enabled'); ?>">
          <option value="" <?php if(Params::getParam('blogId') == 0) { ?>selected="selected"<?php } ?>><?php _e('All blog posts', 'blog'); ?></option>

          <?php if(count($blogs) > 0) { ?>
            <?php foreach($blogs as $b) { ?>
              <option value="<?php echo $b['pk_i_id']; ?>" <?php if($b['pk_i_id'] == Params::getParam('blogId')) { ?>selected="selected"<?php } ?>><?php echo ($b['s_title'] <> '' ? $b['s_title'] : sprintf(__('Article #%s (%s)', 'blog'), $b['pk_i_id'], blg_get_locale())); ?></option>
            <?php } ?>
          <?php } ?>
        </select>

        <select id="enabled" name="enabled" rel="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/comment.php&blogId=<?php echo Params::getParam('blogId'); ?>">
          <option value="" <?php if(Params::getParam('enabled') == 0) { ?>selected="selected"<?php } ?>><?php _e('Enabled & disabled', 'blog'); ?></option>
          <option value="-1" <?php if(Params::getParam('enabled') == -1) { ?>selected="selected"<?php } ?>><?php _e('Disabled only', 'blog'); ?></option>
          <option value="1" <?php if(Params::getParam('enabled') == 1) { ?>selected="selected"<?php } ?>><?php _e('Enabled only', 'blog'); ?></option>
        </select>
      </div>

      <div class="mb-table mb-table-comment">
        <div class="mb-table-head">
          <div class="mb-col-1"><span><?php _e('ID', 'blog');?></span></div>
          <div class="mb-col-4 mb-align-left"><span><?php _e('User', 'blog');?></span></div>
          <div class="mb-col-9 mb-align-left"><span><?php _e('Comment', 'blog');?></span></div>
          <div class="mb-col-5 mb-align-left"><span><?php _e('Blog Post', 'blog');?></span></div>
          <div class="mb-col-2"><span><?php _e('Date', 'blog');?></span></div>
          <div class="mb-col-3"><span>&nbsp;</span></div>
        </div>

        <?php if(count($comments) <= 0) { ?>
          <div class="mb-table-row mb-row-empty">
            <i class="fa fa-warning"></i><span><?php _e('No comments has been found', 'blog'); ?></span>
          </div>
        <?php } else { ?>
          <?php foreach($comments as $c) { ?>
            <div class="mb-table-row">
              <?php 
                $user = User::newInstance()->findByPrimaryKey($c['fk_i_os_user_id']); 
                $blog = ModelBLG::newInstance()->getBlogDetail($c['fk_i_blog_id']); 
              ?>

              <div class="mb-col-1 <?php if($c['b_enabled'] == 1) { ?>cenabled<?php } else { ?>cdisabled<?php } ?>"><?php echo $c['pk_i_id']; ?></div>
              <div class="mb-col-4 mb-align-left">
                <?php if($c['fk_i_os_user_id'] > 0 && isset($user['pk_i_id'])) { ?>
                  <a target="_blank" href="<?php echo osc_admin_base_url(true); ?>?page=users&action=edit&id=<?php echo $c['fk_i_os_user_id']; ?>" class="ouser"><?php echo $user['s_name'] . ' (' . $user['s_email'] . ')'; ?></a>
                <?php } else { ?>
                  <?php echo sprintf(__('User #%s (removed)', 'blog'), $c['fk_i_os_user_id']); ?>
                <?php } ?>
              </div>
              <div class="mb-col-9 mb-align-left"><?php echo strip_tags($c['s_comment'], '<br>'); ?></div>
              <div class="mb-col-5 mb-align-left">
                <?php if($c['fk_i_blog_id'] > 0) { ?>
                  <a target="_blank" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/blog.php&blogId=<?php echo $c['fk_i_blog_id']; ?>" class="oblog"><?php echo (@$blog['s_title'] <> '' ? $blog['s_title'] : __('No title', 'blog') . ' (' . blg_get_locale() . ')'); ?></a>
                <?php } else { ?>
                  <?php _e('No post', 'blog'); ?>
                <?php } ?>
              </div>

              <div class="mb-col-2"><span class="mb-has-tooltip odate" title="<?php echo $c['dt_pub_date']; ?>"><?php echo date('j. M Y', strtotime($c['dt_pub_date'])); ?></span></div>

              <div class="mb-col-3 ubtns">
                <?php if($c['b_enabled'] <> 1) { ?>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/comment.php&approveId=<?php echo $c['pk_i_id']; ?>" class="mb-btn mb-button-green"><i class="fa fa-check"></i></a>
                <?php } ?>

                <?php if(!blg_is_demo()) { ?>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/comment.php&editId=<?php echo $c['pk_i_id']; ?>" class="mb-btn mb-button-white"><i class="fa fa-pencil"></i></a>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/comment.php&deleteId=<?php echo $c['pk_i_id']; ?>" class="mb-btn mb-button-red" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this comment? Action cannot be undone', 'blog')); ?>')"><i class="fa fa-trash-o"></i></a>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        <?php } ?>
      </div>

      <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/comment.php?editId=-1" class="mb-add-user mb-add-comment"><i class="fa fa-plus-circle"></i><?php _e('Add new comment', 'blog'); ?></a>

      <div class="mb-row">&nbsp;</div>

    </div>
  </div>
</div>


<script type="text/javascript">
  var user_lookup_error = "<?php echo osc_esc_js(__('Error getting data, user not found', 'blog')); ?>";
  var user_lookup_url = "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=runhook&hook=blg_find_user&id=";
  var user_lookup_base = "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=userajax";
</script>

<?php echo blg_footer(); ?>