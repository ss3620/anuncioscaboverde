<?php
  require_once 'functions.php';


  // Create menu
  $title = __('Category', 'delta');
  del_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = del_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check, value or code

  $default_logo = del_param_update('default_logo', 'theme_action', 'check', 'theme-delta');


  switch( Params::getParam('theme_action') ) {
    case('upload_logo'):
      $package = Params::getFiles('logo');
      if( $package['error'] == UPLOAD_ERR_OK ) {
        $logo_dir = WebThemes::newInstance()->getCurrentThemePath() . 'images/';
        $allowed_exts = del_logo_extensions();
        $ext = strtolower(pathinfo($package['name'], PATHINFO_EXTENSION));

        // Prefer real image type over client filename (keeps PNG as PNG)
        if(function_exists('exif_imagetype')) {
          $image_type = @exif_imagetype($package['tmp_name']);
          $type_map = array(
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_WEBP => 'webp'
          );
          if(isset($type_map[$image_type])) {
            $ext = $type_map[$image_type];
          }
        }

        if($ext === 'jpeg') {
          $ext = 'jpg';
        }

        if(in_array($ext, $allowed_exts, true)) {
          // Remove previous logo in any supported format before saving
          foreach($allowed_exts as $old_ext) {
            $old_logo = $logo_dir . 'logo.' . $old_ext;
            if(file_exists($old_logo)) {
              @unlink($old_logo);
            }
          }

          if(move_uploaded_file($package['tmp_name'], $logo_dir . 'logo.' . $ext)) {
            osc_set_preference('default_logo', '0', 'theme-delta');
            osc_add_flash_ok_message(__('The logo image has been uploaded correctly', 'delta'), 'admin');
          } else {
            osc_add_flash_error_message(__("An error has occurred, please try again", 'delta'), 'admin');
          }
        } else {
          osc_add_flash_error_message(__('Following formats are allowed: png, gif, jpg', 'delta'), 'admin');
        }
      } else {
        osc_add_flash_error_message(__("An error has occurred, please try again", 'delta'), 'admin');
      }
      header('Location: ' . osc_admin_render_theme_url('oc-content/themes/' . osc_current_web_theme() . '/admin/header.php')); exit;
      break;

    case('remove'):
      $logo_dir = WebThemes::newInstance()->getCurrentThemePath() . 'images/';
      $removed = false;
      foreach(del_logo_extensions() as $ext) {
        $logo_path = $logo_dir . 'logo.' . $ext;
        if(file_exists($logo_path)) {
          @unlink($logo_path);
          $removed = true;
        }
      }
      if($removed) {
        osc_add_flash_ok_message(__('The logo image has been removed', 'delta'), 'admin');
      } else {
        osc_add_flash_error_message(__("Image not found", 'delta'), 'admin');
      }
      header('Location: ' . osc_admin_render_theme_url('oc-content/themes/' . osc_current_web_theme() . '/admin/header.php')); exit;
      break;
  } 


  if(Params::getParam('theme_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'delta') );
  }
?>


<div class="mb-body">
  <!-- LOGO PREVIEW -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-display"></i> <?php _e('Logo preview', 'delta'); ?></div>

    <div class="mb-inside">
      <form action="<?php echo osc_admin_render_theme_url('oc-content/themes/delta/admin/header.php');?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="theme_action" value="done" />

        <div class="mb-row">
          <label for="default_logo" class=""><span><?php _e('Use Default Logo', 'delta'); ?></span></label> 
          <input name="default_logo" id="default_logo" class="element-slide" type="checkbox" <?php echo (del_param('default_logo') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('If you did not upload any logo yet, osclass default logo will be used.', 'delta'); ?></div>
        </div>
        
        <div class="mb-foot">
          <button type="submit" class="mb-button"><?php _e('Save', 'delta');?></button>
        </div>
      </form>
    </div>
  </div>


  <?php if( is_writable( WebThemes::newInstance()->getCurrentThemePath() . "images/") ) { ?>
    <!-- LOGO PREVIEW -->
    <div class="mb-box">
      <div class="mb-head"><i class="fa fa-display"></i> <?php _e('Logo preview', 'delta'); ?></div>

      <?php
        $logo_preview_src = '';
        $logo_path_base = WebThemes::newInstance()->getCurrentThemePath() . 'images/logo.';
        foreach(del_logo_extensions() as $ext) {
          if(file_exists($logo_path_base . $ext)) {
            $logo_preview_src = osc_current_web_theme_url('images/logo.' . $ext);
            break;
          }
        }
      ?>
      <?php if($logo_preview_src !== '') { ?>
        <div class="mb-inside">
          <img class="mb-image-preview" border="0" alt="<?php echo osc_esc_html( osc_page_title() ); ?>" src="<?php echo $logo_preview_src;?>" />
        </div>

        <form action="<?php echo osc_admin_render_theme_url('oc-content/themes/delta/admin/header.php');?>" method="post" enctype="multipart/form-data">
          <input type="hidden" name="theme_action" value="remove" />

          <div class="mb-foot">
            <button type="submit" class="mb-button"><?php _e('Remove', 'delta');?></button>
          </div>
        </form>

      <?php } else { ?>
        <div class="mb-inside">
          <div class="mb-warning">
            <?php _e('No logo has been uploaded yet', 'delta'); ?>
          </div>
        </div>
      <?php } ?>
    </div>



    <!-- LOGO UPLOAD -->
    <div class="mb-box">
      <div class="mb-head"><i class="fa fa-upload"></i> <?php _e('Logo upload', 'delta'); ?></div>

      <form action="<?php echo osc_admin_render_theme_url('oc-content/themes/delta/admin/header.php'); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="theme_action" value="upload_logo" />

        <div class="mb-inside">
          <div class="mb-points">
            <div class="mb-row">- <strong><?php _e('When new logo is uploaded, do not forget to clean your browser cache (CTRL + R or CTRL + F5)', 'delta'); ?></strong></div>
            <div class="mb-row">- <?php _e('The preferred size of the logo is 200x50px.', 'delta'); ?></div>
            <div class="mb-row">- <?php _e('Following formats are allowed: png, gif, jpg','delta'); ?></div>

            <?php if($logo_preview_src !== '') { ?>
              <div class="mb-row">- <?php _e('Uploading another logo will overwrite the current logo.', 'delta'); ?></div>
            <?php } ?>
          </div>

          <input type="file" name="logo" id="package" />
        </div>
 
        <?php if(!del_is_demo() || osc_logged_admin_username() == 'admin') { ?>
          <div class="mb-foot">
            <button type="submit" class="mb-button"><?php _e('Upload', 'delta');?></button>
          </div>
        <?php } ?>
      </form>
    <?php } else { ?>
      <div class="mb-warning">
        <div class="mb-row">
          <?php
            $msg  = sprintf(__('The images folder <strong>%s</strong> is not writable on your server', 'delta'), WebThemes::newInstance()->getCurrentThemePath() ."images/" ) .", ";
            $msg .= __("OSClass can't upload the logo image from the administration panel.", 'delta') . ' ';
            $msg .= __('Please make the aforementioned image folder writable.', 'delta') . ' ';
            echo $msg;
          ?>
        </div>

        <div class="mb-row">
          <?php _e('To make a directory writable under UNIX execute this command from the shell:','delta'); ?>
        </div>

        <div class="mb-row">
          chmod a+w <?php echo WebThemes::newInstance()->getCurrentThemePath() ."images/" ; ?>
        </div>
      </div>
    <?php } ?>
  </div>
</div>


<?php echo del_footer(); ?>