<?php

  // Create menu
  $title = __('Blog users', 'blog');
  blg_menu($title);


  if((Params::getParam('editId') > 0 || Params::getParam('editId') == -1) && Params::getParam('plugin_action') == 'done') {
    $id = Params::getParam('editId');

    if($id == -1) {
      $id = ModelBLG::newInstance()->insertUser(array('s_name' => __('No name', 'blog')));
      Params::setParam('editId', $id);
    }

    $user = ModelBLG::newInstance()->getUser($id);

    // UPLOAD IMAGE
    $upload_status = false;
    // $upload_dir = osc_plugins_path() . 'blog/img/user/';
    $upload_dir = blg_file_path('', 'user');

    if(isset($_FILES['image']) && $_FILES['image']['name'] <> ''){
      if(@$user['s_image'] <> '') {
        if(file_exists($upload_dir . $user['s_image'])) {
          unlink($upload_dir . $user['s_image']);
        }
      }


      $file_ext = $ext = pathinfo($_FILES['image']['name'])['extension'];
      $file_name  = $id . '.' . $file_ext;
      $file_tmp   = $_FILES['image']['tmp_name'];
      $file_type  = $_FILES['image']['type'];   
      $extensions = array('jpg', 'png', 'gif');

      if(in_array($file_ext,$extensions) === false) {
        $errors = __('extension not allowed, only allowed extension are jpg, png or gif!', 'blog');
      }
            
      if(empty($errors)==true){
        move_uploaded_file($file_tmp, $upload_dir.$file_name);
        $upload_status = true;
      } else {
        message_error(__('There was error when uploading image', 'blog') . ': ' . $errors);
      }
    }


    $data_user = array(
      'pk_i_id' => $id,
      'fk_i_user_id' => Params::getParam('fk_i_user_id'),
      's_name' => Params::getParam('s_name'),
      's_about' => Params::getParam('s_about'),
      's_skills' => Params::getParam('s_skills'),
      's_category_id' => Params::getParam('s_category_id'),
      'dt_reg_date' => Params::getParam('dt_reg_date')
    );

    if($upload_status) {
      $data_user['s_image'] = $file_name;
    }


    ModelBLG::newInstance()->updateUser($data_user);

    message_ok( __('User successfully updated', 'blog') );
  }


  if(Params::getParam('deleteId') > 0) {
    $user = ModelBLG::newInstance()->getUser(Params::getParam('deleteId'));
    // $upload_dir = osc_plugins_path() . 'blog/img/user/';
    $upload_dir = blg_file_path('', 'user');

    if(@$user['s_image'] <> '') {
      if(file_exists($upload_dir . $user['s_image'])) {
        unlink($upload_dir . $user['s_image']);
      }
    }

    ModelBLG::newInstance()->removeUser(Params::getParam('deleteId'));
    message_ok( __('User removed successfully', 'blog') );
  }

  $users = ModelBLG::newInstance()->getUsers();
  $categories = ModelBLG::newInstance()->getCategories();
?>


<div class="mb-body">

  <?php if(Params::getParam('editId') > 0 || Params::getParam('editId') == -1) { ?>
    <?php 
      $user = ModelBLG::newInstance()->getUser(Params::getParam('editId'));
      $category_array = explode(',', (string)(@$user['s_category_id'] ?? ''));
    ?>

    <div class="mb-box">
      <div class="mb-head">
        <i class="fa fa-plus-circle"></i> <?php _e('Add/edit user', 'blog'); ?>
      </div>

      <div class="mb-inside mb-user-edit">
        <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
          <input type="hidden" name="page" value="plugins" />
          <input type="hidden" name="action" value="renderplugin" />
          <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>user.php" />
          <input type="hidden" name="plugin_action" value="done" />
          <input type="hidden" name="editId" value="<?php echo Params::getParam('editId'); ?>" />

          <div class="mb-row">
            <label for="dt_reg_date"><?php _e('Registration Date', 'blog'); ?></label>
            <input type="date" id="dt_reg_date" name="dt_reg_date" size="30" value="<?php echo @$user['dt_reg_date'] <> '' ? date("Y-m-d", strtotime(@$user['dt_reg_date'])): date("Y-m-d"); ?>" />

            <div class="mb-explain"><?php _e('Date when user started to be active on blog.', 'blog'); ?></div>
          </div>

          <div class="mb-row">
            <label for="s_name"><?php _e('Name', 'blog'); ?></label>
            <input type="text" id="s_name" name="s_name" size="30" value="<?php echo @$user['s_name']; ?>" />

            <div class="mb-explain"><?php _e('Name will be shown on all blogs this user will create.', 'blog'); ?></div>
          </div>

          <div class="mb-row">
            <label for="s_skills"><?php _e('Skills', 'blog'); ?></label>
            <input type="text" id="s_skills" name="s_skills" size="60" value="<?php echo @$user['s_skills']; ?>" />

            <div class="mb-explain"><?php _e('User\'s best skills. Example: PHP, HTML, CSS', 'blog'); ?></div>
          </div>

          <div class="mb-row">
            <label for="s_about"><?php _e('About', 'blog'); ?></label>
            <textarea id="s_about" name="s_about"><?php echo @$user['s_about']; ?></textarea>

            <div class="mb-explain"><?php _e('User\'s description, hobby, ...', 'blog'); ?></div>
          </div>

          <div class="mb-row image">
            <label for="image"><span><?php _e('Profile Picture', 'blog'); ?></span></label> 

            <div class="mb-img-right">
              <?php $img = blg_user_img(@$user['s_image'], 1); ?>
              <?php if($img) { ?>
                <a class="mb-img-preview" href="<?php echo $img; ?>" target="_blank"><img class="mb-blog-img" src="<?php echo $img; ?>" /></a>
              <?php } ?>

              <div class="mb-file">
                <label class="file-label">
                  <span class="wrap"><i class="fa fa-paperclip"></i> <span><?php echo (@$user['s_image'] == '' ? __('Upload image', 'blog') : __('Replace image', 'blog')); ?></span></span>
                  <input type="file" id="image" name="image" />
                </label>

                <div class="file-text"><?php _e('Allowed extensions', 'blog'); ?>: .png, .jpg, .gif</div>
              </div>
            </div>
          </div>


          <div class="mb-row mb-row-select-multiple">
            <label for="category_multiple"><span><?php _e('Allowed Categories', 'blog'); ?></span></label> 

            <input type="hidden" name="s_category_id" id="s_category_id" value="<?php echo @$user['s_category_id']; ?>"/>
            <select id="category_multiple" name="category_multiple" multiple>
              <?php if(count($categories) > 0) { ?>
                <?php foreach($categories as $c) { ?>
                  <option value="<?php echo $c['pk_i_id']; ?>" <?php if(in_array($c['pk_i_id'], $category_array)) { ?>selected="selected"<?php } ?>><?php echo ($c['s_name'] <> '' ? $c['s_name'] : sprintf(__('Category #%s (%s)', 'blog'), $c['pk_i_id'], blg_get_locale())); ?></option>
                <?php } ?>
              <?php } else { ?>
                <option value=""><?php _e('No blog categories created', 'blog'); ?></option>
              <?php } ?>
            </select>

            <div class="mb-explain"><?php _e('If not category selected, advert is shown in all categories.', 'blog'); ?></div>
          </div>


          <div class="mb-row mb-osclass-user">
            <label for="s_os_name"><span><?php _e('Osclass User Profile', 'blog'); ?></span></label>

            <div class="mb-line">
              <input type="text" id="s_os_name" name="s_os_name" placeholder="<?php echo osc_esc_html(__('Type user name or email', 'blog')); ?>" value="<?php echo osc_esc_html(@$user['s_os_name']); ?>"/>

              <input type="text" id="fk_i_user_id" name="fk_i_user_id" readonly="readonly" value="<?php echo @$user['fk_i_user_id']; ?>"/>
              <input type="text" id="s_os_email" name="s_os_email" readonly="readonly" placeholder="<?php echo osc_esc_html(__('Email', 'blog')); ?>" value="<?php echo (@$user['s_os_email']); ?>"/>
            </div>

            <div class="mb-explain"><?php _e('Start typing user name or email and select user you want to check from list.', 'blog'); ?></div>
          </div>



          <div class="mb-row">&nbsp;</div>

          <div class="mb-foot">
            <?php if(!blg_is_demo()) { ?><button type="submit" class="mb-button"><?php _e('Save', 'blog');?></button><?php } ?>
          </div>
        </form>
      </div>
    </div>
  <?php } ?>



  <!-- USERS SECTION -->
  <div class="mb-box">
    <div class="mb-head">
      <i class="fa fa-users"></i> <?php echo $title; ?>
    </div>

    <div class="mb-inside mb-blog-user">

      <div class="mb-row mb-notes">
        <div class="mb-line"><?php _e('List of users that are eligible to create blog posts.', 'blog'); ?></div>
      </div>

      <div class="mb-table mb-table-log">
        <div class="mb-table-head">
          <div class="mb-col-1"><span>&nbsp;</span></div>
          <div class="mb-col-3 mb-align-left"><span><?php _e('Name', 'blog');?></span></div>
          <div class="mb-col-7 mb-align-left"><span><?php _e('About', 'blog');?></span></div>
          <div class="mb-col-3 mb-align-left"><span><?php _e('Skills', 'blog');?></span></div>
          <div class="mb-col-2"><span><?php _e('Categories', 'blog');?></span></div>
          <div class="mb-col-2"><span><?php _e('Posts', 'blog');?></span></div>
          <div class="mb-col-4"><span><?php _e('Osclass User', 'blog');?></span></div>
          <div class="mb-col-2"><span>&nbsp;</span></div>
        </div>

        <?php if(count($users) <= 0) { ?>
          <div class="mb-table-row mb-row-empty">
            <i class="fa fa-warning"></i><span><?php _e('No blog users has been found', 'blog'); ?></span>
          </div>
        <?php } else { ?>
          <?php foreach($users as $u) { ?>
            <div class="mb-table-row">
              <div class="mb-col-1 upic"><img src="<?php echo blg_user_img($u['s_image']); ?>"/></div>
              <div class="mb-col-3 mb-align-left"><?php echo ($u['s_name'] <> '' ? $u['s_name'] : __('No name', 'blog')); ?></div>
              <div class="mb-col-7 mb-align-left"><?php echo ($u['s_about'] <> '' ? $u['s_about'] : '-'); ?></div>
              <div class="mb-col-3 mb-align-left"><?php echo ($u['s_skills'] <> '' ? $u['s_skills'] : '-'); ?></div>
              <div class="mb-col-2">
                <?php 
                  $tip = '';
                  if($u['s_category_id'] == '') {
                    $tip .= __('All categories enabled', 'blog');
                    $count_name = __('All categories', 'blog');

                  } else {
                    $cats = explode(',', (string)$u['s_category_id']);
                    foreach($cats as $c) {
                      $cat = ModelBLG::newInstance()->getCategoryDetail($c);
                      
                      if(isset($cat['pk_i_id'])) {
                        $tip .= ($cat['s_name'] <> '' ? $cat['s_name'] : sprintf(__('Category #%s (%s)', 'blog'), $cat['pk_i_id'], blg_get_locale())) . '</br>';
                      }
                    }


                    if(count($cats) == 1) {
                      $count_name = __('1 category', 'blog');
                    } else {
                      $count_name = sprintf(__('%d categories', 'blog'), count($cats));
                    }
                  }
                ?>

                <a href="#" onclick="return false;" title="<?php echo osc_esc_html($tip); ?>" class="mb-cat-count mb-has-tooltip"><?php echo $count_name; ?></a>
              </div>

              <div class="mb-col-2">
                <?php $count_blogs = ModelBLG::newInstance()->countBlogs(-1, -1, $u['pk_i_id']); ?>
                <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/list.php&authorId=<?php echo $u['pk_i_id']; ?>"><?php echo ($count_blogs == 1 ? __('1 post', 'blog') : sprintf(__('%d posts', 'blog'), $count_blogs)); ?></a>
              </div>
              <div class="mb-col-4">
                <?php if($u['fk_i_user_id'] > 0) { ?>
                  <a target="_blank" href="<?php echo osc_admin_base_url(true); ?>?page=users&action=edit&id=<?php echo $u['fk_i_user_id']; ?>" class="ouser"><?php echo $u['s_os_name'] . ' (' . $u['s_os_email'] . ')'; ?></a>
                <?php } else { ?>
                  -
                <?php } ?>
              </div>
              <div class="mb-col-2 mb-align-right">
                <?php if(!blg_is_demo()) { ?>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/user.php&editId=<?php echo $u['pk_i_id']; ?>" class="mb-btn mb-button-white"><i class="fa fa-pencil"></i></a>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/user.php&deleteId=<?php echo $u['pk_i_id']; ?>" class="mb-btn mb-button-red" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this user? Action cannot be undone', 'blog')); ?>')"><i class="fa fa-trash-o"></i></a>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        <?php } ?>
      </div>

      <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/user.php?editId=-1" class="mb-add-user"><i class="fa fa-plus-circle"></i><?php _e('Add new user', 'blog'); ?></a>

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