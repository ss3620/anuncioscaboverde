<?php
  require_once 'functions.php';


  // Create menu
  $title = __('Plugins', 'delta');
  del_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = del_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check, value or code

  $scrolltop = del_param_update('scrolltop', 'theme_action', 'check', 'theme-delta');
  $related = del_param_update('related', 'theme_action', 'check', 'theme-delta');
  $related_count = del_param_update('related_count', 'theme_action', 'value', 'theme-delta');
  $related_design = del_param_update('related_design', 'theme_action', 'value', 'theme-delta');

  if(Params::getParam('theme_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'delta') );
  }
?>


<div class="mb-body">

  <div class="mb-info-box" style="margin:5px 0 30px 0;">
    <div class="mb-line"><strong><?php _e('Plugins for this theme', 'delta'); ?></strong></div>
    <div class="mb-line"><?php _e('We have modified for you many plugins to fit theme design that will work without need of any modifications', 'delta'); ?>.</div>
    <div class="mb-line"><?php _e('Plugins are not delivered in theme package, must be downloaded separately', 'delta'); ?>.</div>
    <div class="mb-line" style="margin:10px 0;"><a href="https://osclasspoint.com/theme-plugins/delta_plugins_20210604_oy12nQ.zip" target="_blank" class="mb-button-white"><i class="fa fa-download"></i> <?php _e('Download plugins', 'delta'); ?></a></div>
    <div class="mb-line" style="margin-top:15px;">- <?php _e('upload and extract downloaded file <strong>delta-plugins.zip</strong> into folder <strong>oc-content/plugins/</strong> on your hosting', 'delta'); ?>.</div>
    <div class="mb-line">- <?php _e('go to <strong>oc-admin > Plugins</strong> and install plugins you like', 'delta'); ?>.</div>
  </div>


 
  <!-- PLUGINS SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-puzzle-piece"></i> <?php _e('Plugin settings', 'delta'); ?></div>

    <div class="mb-inside mb-minify">
      <form action="<?php echo osc_admin_render_theme_url('oc-content/themes/delta/admin/plugins.php'); ?>" method="POST">
        <input type="hidden" name="theme_action" value="done" />

        <div class="mb-row">
          <label for="scrolltop" class="h1"><span><?php _e('Enable Scroll to Top', 'delta'); ?></span></label> 
          <input name="scrolltop" id="scrolltop" class="element-slide" type="checkbox" <?php echo (del_param('scrolltop') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, button that enables scroll to top will be added.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="related" class="h2"><span><?php _e('Enable Related Listings', 'delta'); ?></span></label> 
          <input name="related" id="related" class="element-slide" type="checkbox" <?php echo (del_param('related') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, related listings will be shown at listing page.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="related_count" class="h3"><span><?php _e('Number of Related Items', 'delta'); ?></span></label> 
          <input name="related_count" id="related_count" type="number" min="1" value="<?php echo del_param('related_count'); ?>" />

          <div class="mb-explain"><?php _e('Enter how many related listings will be shown on item page.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="related_design" class="h22"><span><?php _e('Related items card design', 'delta'); ?></span></label> 
          <select name="related_design" id="related_design">
            <option value="" <?php echo (del_param('related_design') == '' ? 'selected="selected"' : ''); ?>><?php _e('Standard', 'delta'); ?></option>
            <option value="compact" <?php echo (del_param('related_design') == 'compact' ? 'selected="selected"' : ''); ?>><?php _e('Compact', 'delta'); ?></option>
            <option value="tiny" <?php echo (del_param('related_design') == 'tiny' ? 'selected="selected"' : ''); ?>><?php _e('Tiny', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Specify which card design will be used.', 'delta'); ?></div>
        </div>


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