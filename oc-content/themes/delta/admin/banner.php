<?php
  require_once 'functions.php';


  // Create menu
  $title = __('Advertisement', 'delta');
  del_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = del_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check, value or code

  $banners = del_param_update('banners', 'theme_action', 'check', 'theme-delta');
  $banner_optimize_adsense = del_param_update('banner_optimize_adsense', 'theme_action', 'check', 'theme-delta');
 
 
  foreach(del_banner_list() as $b) {
    del_param_update($b['id'], 'theme_action', 'code', 'theme-delta');
  }


  if(Params::getParam('theme_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'delta') );
  }
?>


<div class="mb-body">
  <div class="mb-notes">
    <div class="mb-line"><?php _e('If you use Banner Ads Plugin, you can use banner or advert directly in theme banner space by defining its ID in bellow form.', 'delta'); ?></div>
    <div class="mb-line"><?php _e('Usage examples: {{BANNER-ADS-PLUGIN-HOOK: my_custom_hook}}, {{BANNER-ADS-PLUGIN-BANNER: 123}}, {{BANNER-ADS-PLUGIN-ADVERT: 987}}', 'delta'); ?></div>
  </div>
 
  <!-- BANNER SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-clone"></i> <?php _e('Advertisement', 'delta'); ?></div>

    <div class="mb-inside mb-minify">
      <form action="<?php echo osc_admin_render_theme_url('oc-content/themes/delta/admin/banner.php'); ?>" method="POST">
        <input type="hidden" name="theme_action" value="done" />

        <div class="mb-row">
          <label for="banners" class="h1"><span><?php _e('Enable Theme Banners', 'delta'); ?></span></label> 
          <input name="banners" id="banners" class="element-slide" type="checkbox" <?php echo (del_param('banners') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, bellow banners will be shown in front page.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="banner_optimize_adsense" class="h2"><span><?php _e('Optimize Banners for Adsense', 'delta'); ?></span></label> 
          <input name="banner_optimize_adsense" id="banner_optimize_adsense" class="element-slide" type="checkbox" <?php echo (del_param('banner_optimize_adsense') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, default width and height of banners will be removed and will be set to 100%. Fluid/responsive adsense banners should then work better.', 'delta'); ?></div>
        </div>
        
        
        <?php foreach(del_banner_list() as $b) { ?>
          <div class="mb-row">
            <label for="<?php echo $b['id']; ?>" class="h29"><span><?php echo ucwords(str_replace('_', ' ', $b['id'])); ?></span></label> 
            <textarea class="mb-textarea mb-textarea-large" name="<?php echo $b['id']; ?>" placeholder="<?php echo osc_esc_html(__('Will be shown', 'delta')); ?>: <?php echo $b['position']; ?>"><?php echo stripslashes(del_param($b['id']) ); ?></textarea>
          </div>
        <?php } ?>



        <div class="mb-row">&nbsp;</div>

        <?php if(!del_is_demo() || osc_logged_admin_username() == 'admin') { ?>
          <div class="mb-foot">
            <button type="submit" class="mb-button"><?php _e('Save', 'delta');?></button>
          </div>
        <?php } ?>
      </form>
    </div>
  </div>

</div>


<?php echo del_footer(); ?>