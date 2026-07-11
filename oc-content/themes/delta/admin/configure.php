<?php
  require_once 'functions.php';


  // Create menu
  $title = __('Configure', 'delta');
  del_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = del_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check, value or code

  $color = del_param_update('color', 'theme_action', 'value', 'theme-delta');
  $color2 = del_param_update('color2', 'theme_action', 'value', 'theme-delta');
  $color3 = del_param_update('color3', 'theme_action', 'value', 'theme-delta');
  $publish_category = del_param_update('publish_category', 'theme_action', 'value', 'theme-delta');
  $site_phone = del_param_update('site_phone', 'theme_action', 'value', 'theme-delta');
  $site_email = del_param_update('site_email', 'theme_action', 'value', 'theme-delta');
  $website_name = del_param_update('website_name', 'theme_action', 'value', 'theme-delta');
  $def_view = del_param_update('def_view', 'theme_action', 'value', 'theme-delta');
  $def_design = del_param_update('def_design', 'theme_action', 'value', 'theme-delta');

  $favorite_home = del_param_update('favorite_home', 'theme_action', 'check', 'theme-delta');
  $favorite_design = del_param_update('favorite_design', 'theme_action', 'value', 'theme-delta');
  $favorite_count = del_param_update('favorite_count', 'theme_action', 'value', 'theme-delta');

  $premium_home = del_param_update('premium_home', 'theme_action', 'check', 'theme-delta');
  $blog_home = del_param_update('blog_home', 'theme_action', 'check', 'theme-delta');
  $blog_home_count = del_param_update('blog_home_count', 'theme_action', 'value', 'theme-delta');
  $blog_home_design = del_param_update('blog_home_design', 'theme_action', 'value', 'theme-delta');

  $company_home = del_param_update('company_home', 'theme_action', 'check', 'theme-delta');
  $company_home_count = del_param_update('company_home_count', 'theme_action', 'value', 'theme-delta');
  $promote_home = del_param_update('promote_home', 'theme_action', 'check', 'theme-delta');
  $stats_home = del_param_update('stats_home', 'theme_action', 'check', 'theme-delta');

  $premium_home_count = del_param_update('premium_home_count', 'theme_action', 'value', 'theme-delta');
  $premium_search = del_param_update('premium_search', 'theme_action', 'check', 'theme-delta');
  $premium_search_count = del_param_update('premium_search_count', 'theme_action', 'value', 'theme-delta');
  $premium_home_design = del_param_update('premium_home_design', 'theme_action', 'value', 'theme-delta');
  $premium_search_design = del_param_update('premium_search_design', 'theme_action', 'value', 'theme-delta');

  $footer_link = del_param_update('footer_link', 'theme_action', 'check', 'theme-delta');
  $def_cur = del_param_update('def_cur', 'theme_action', 'value', 'theme-delta');
  $latest_random = del_param_update('latest_random', 'theme_action', 'check', 'theme-delta');
  $latest_picture = del_param_update('latest_picture', 'theme_action', 'check', 'theme-delta');
  $latest_premium = del_param_update('latest_premium', 'theme_action', 'check', 'theme-delta');
  $latest_category = del_param_update('latest_category', 'theme_action', 'value', 'theme-delta');
  $latest_design = del_param_update('latest_design', 'theme_action', 'value', 'theme-delta');

  $search_ajax = del_param_update('search_ajax', 'theme_action', 'check', 'theme-delta');
  $forms_ajax = del_param_update('forms_ajax', 'theme_action', 'check', 'theme-delta');
  $post_required = del_param_update('post_required', 'theme_action', 'value', 'theme-delta');
  $post_extra_exclude = del_param_update('post_extra_exclude', 'theme_action', 'value', 'theme-delta');

  $lazy_load = del_param_update('lazy_load', 'theme_action', 'check', 'theme-delta');
  $location_pick = del_param_update('location_pick', 'theme_action', 'check', 'theme-delta');
  $public_items = del_param_update('public_items', 'theme_action', 'value', 'theme-delta');
  $preview = del_param_update('preview', 'theme_action', 'check', 'theme-delta');
  $def_locations = del_param_update('def_locations', 'theme_action', 'value', 'theme-delta');

  $loc_one_row = del_param_update('loc_one_row', 'theme_action', 'check', 'theme-delta');
  $cat_one_row = del_param_update('cat_one_row', 'theme_action', 'check', 'theme-delta');
  $loc_box_region_search = del_param_update('loc_box_region_search', 'theme_action', 'check', 'theme-delta');
  $loc_box_city_search = del_param_update('loc_box_city_search', 'theme_action', 'check', 'theme-delta');

  $user_items = del_param_update('user_items', 'theme_action', 'check', 'theme-delta');
  $user_items_count = del_param_update('user_items_count', 'theme_action', 'value', 'theme-delta');
  $user_items_design = del_param_update('user_items_design', 'theme_action', 'value', 'theme-delta');

  $save_search_position = del_param_update('save_search_position', 'theme_action', 'value', 'theme-delta');
  $sample_favicons = del_param_update('sample_favicons', 'theme_action', 'check', 'theme-delta');

  $footer_social_define = del_param_update('footer_social_define', 'theme_action', 'check', 'theme-delta');
  $footer_social_whatsapp = del_param_update('footer_social_whatsapp', 'theme_action', 'value', 'theme-delta');
  $footer_social_facebook = del_param_update('footer_social_facebook', 'theme_action', 'value', 'theme-delta');
  $footer_social_pinterest = del_param_update('footer_social_pinterest', 'theme_action', 'value', 'theme-delta');
  $footer_social_instagram = del_param_update('footer_social_instagram', 'theme_action', 'value', 'theme-delta');
  $footer_social_x = del_param_update('footer_social_x', 'theme_action', 'value', 'theme-delta');
  $footer_social_linkedin = del_param_update('footer_social_linkedin', 'theme_action', 'value', 'theme-delta');


  if(Params::getParam('theme_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'delta') );
  }


  $post_extra_exclude_array = explode(',', $post_extra_exclude);
  $post_required_array = explode(',', $post_required);

?>


<div class="mb-body">

 
  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Configure', 'delta'); ?></div>

    <div class="mb-inside mb-minify">
      <form action="<?php echo osc_admin_render_theme_url('oc-content/themes/delta/admin/configure.php'); ?>" method="POST">
        <input type="hidden" name="theme_action" value="done" />

        <div class="mb-row mb-color-box">
          <label for="color" class="h1"><span><?php _e('Theme color #1', 'delta'); ?></span></label> 
      
          <input name="color" id="color" size="20" type="text" value="<?php echo osc_esc_html($color); ?>" />
          <span class="color-wrap">
            <input name="color-picker" id="" type="color" value="<?php echo osc_esc_html($color); ?>" />
          </span>
          <div class="mb-explain"><?php _e('Enter color in HEX format or select color with picker. Theme will use this color for buttons, borders, ... Example: #f29c12', 'delta'); ?></div>
        </div>

        <div class="mb-row mb-color-box">
          <label for="color2" class="h1"><span><?php _e('Theme color #2', 'delta'); ?></span></label> 
      
          <input name="color2" id="color2" size="20" type="text" value="<?php echo osc_esc_html($color2); ?>" />
          <span class="color-wrap">
            <input name="color-picker" id="" type="color" value="<?php echo osc_esc_html($color2); ?>" />
          </span>
          <div class="mb-explain"><?php _e('Enter color in HEX format or select color with picker. Theme will use this color for buttons, borders, ... Example: #f29c12', 'delta'); ?></div>
        </div>

        <div class="mb-row mb-color-box">
          <label for="color3" class="h1"><span><?php _e('Theme color #3', 'delta'); ?></span></label> 
      
          <input name="color3" id="color3" size="20" type="text" value="<?php echo osc_esc_html($color3); ?>" />
          <span class="color-wrap">
            <input name="color-picker" id="" type="color" value="<?php echo osc_esc_html($color3); ?>" />
          </span>
          <div class="mb-explain"><?php _e('Enter color in HEX format or select color with picker. Theme will use this color for buttons, borders, ... Example: #f29c12', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="site_phone" class="h3"><span><?php _e('Site Phone Number', 'delta'); ?></span></label> 
          <input size="40" name="site_phone" id="site_phone" type="text" value="<?php echo osc_esc_html(del_param('site_phone')); ?>" placeholder="<?php echo osc_esc_html(__('Site Phone Number', 'delta')); ?>" />

          <div class="mb-explain"><?php _e('Leave blank to disable, will be shown in footer', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="site_email" class="h3"><span><?php _e('Site Support Email', 'delta'); ?></span></label> 
          <input size="40" name="site_email" id="site_email" type="text" value="<?php echo osc_esc_html(del_param('site_email')); ?>" placeholder="<?php echo osc_esc_html(__('Site Support Email', 'delta')); ?>" />

          <div class="mb-explain"><?php _e('Leave blank to disable, will be shown in footer', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="website_name" class="h4"><span><?php _e('Website Name', 'delta'); ?></span></label> 
          <input size="40" name="website_name" id="website_name" type="text" value="<?php echo osc_esc_html(del_param('website_name')); ?>" placeholder="<?php echo osc_esc_html(__('Website Name', 'delta')); ?>" />

          <div class="mb-explain"><?php _e('Enter shortcut or short name of your website that will be used in footer and breadcrumbs', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="lazy_load" class=""><span><?php _e('Lazy Load', 'delta'); ?></span></label> 
          <input name="lazy_load" id="lazy_load" class="element-slide" type="checkbox" <?php echo (del_param('lazy_load') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Enable to deffer images loading. Images will be loaded when get into viewable area. This may rapidly improve seo rating of your site.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="def_locations" class="h30"><span><?php _e('Location Box Content', 'delta'); ?></span></label> 
          <select name="def_locations" id="def_locations">
            <option value="region" <?php echo (del_param('def_locations') == "region" ? 'selected="selected"' : ''); ?>><?php _e('Regions', 'delta'); ?></option>
            <option value="city" <?php echo (del_param('def_locations') == "city" ? 'selected="selected"' : ''); ?>><?php _e('Cities', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Select default content for location box. For cities only first 200 values will be included in list. Valid only for "small" location box.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="sample_favicons" class=""><span><?php _e('Use Sample Favicons', 'delta'); ?></span></label> 
          <input name="sample_favicons" id="sample_favicons" class="element-slide" type="checkbox" <?php echo (del_param('sample_favicons') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php echo sprintf(__('When enabled, sample favicons from theme folder (%s) will be used.', 'delta'), 'oc-content/themes/delta/images/favicons/sample/'); ?></div>
        </div>

        <div class="mb-row">
          <label for="loc_box_region_search" class=""><span><?php _e('Location Box Region Filter', 'delta'); ?></span></label> 
          <input name="loc_box_region_search" id="loc_box_region_search" class="element-slide" type="checkbox" <?php echo (del_param('loc_box_region_search') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, interactive filter input is added to location box.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="loc_box_city_search" class=""><span><?php _e('Location Box City Filter', 'delta'); ?></span></label> 
          <input name="loc_box_city_search" id="loc_box_city_search" class="element-slide" type="checkbox" <?php echo (del_param('loc_box_city_search') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, interactive filter input is added to location box.', 'delta'); ?></div>
        </div>
        
        
        

        <div class="mb-row">
          <label for="def_design" class="h22"><span><?php _e('Default items card design', 'delta'); ?></span></label> 
          <select name="def_design" id="def_design">
            <option value="" <?php echo (del_param('def_design') == '' ? 'selected="selected"' : ''); ?>><?php _e('Standard', 'delta'); ?></option>
            <option value="compact" <?php echo (del_param('def_design') == 'compact' ? 'selected="selected"' : ''); ?>><?php _e('Compact', 'delta'); ?></option>
            <option value="tiny" <?php echo (del_param('def_design') == 'tiny' ? 'selected="selected"' : ''); ?>><?php _e('Tiny', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Specify which card design will be used.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="premium_search_design" class="h22"><span><?php _e('Premium items card design (search)', 'delta'); ?></span></label> 
          <select name="premium_search_design" id="premium_design">
            <option value="" <?php echo (del_param('premium_search_design') == '' ? 'selected="selected"' : ''); ?>><?php _e('Standard', 'delta'); ?></option>
            <option value="compact" <?php echo (del_param('premium_search_design') == 'compact' ? 'selected="selected"' : ''); ?>><?php _e('Compact', 'delta'); ?></option>
            <option value="tiny" <?php echo (del_param('premium_search_design') == 'tiny' ? 'selected="selected"' : ''); ?>><?php _e('Tiny', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Specify which card design will be used.', 'delta'); ?></div>
        </div>



        <div class="mb-row"><h3 class="sec"><?php _e('Home page settings', 'delta'); ?></h3></div>


        <div class="mb-row">
          <label for="premium_home" class="h7"><span><?php _e('Show Premiums Block on Home', 'delta'); ?></span></label> 
          <input name="premium_home" id="premium_home" class="element-slide" type="checkbox" <?php echo (del_param('premium_home') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Show premium listings block on home page.', 'delta'); ?></div>
        </div>
        

        <div class="mb-row">
          <label for="favorite_home" class="h8"><span><?php _e('Show Favorite Items on Home', 'delta'); ?></span></label> 
          <input name="favorite_home" id="favorite_home" class="element-slide" type="checkbox" <?php echo (del_param('favorite_home') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Show users most favorited listings block on home page. Favorite items plugin must be installed.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="favorite_count" class="h11"><span><?php _e('Number of Favorite items on Home', 'delta'); ?></span></label> 
          <input size="8" name="favorite_count" id="favorite_count" type="number" value="<?php echo osc_esc_html(del_param('favorite_count')); ?>" />

          <div class="mb-explain"><?php _e('Most favorited listings count shown on home page. Favorite items plugin must be installed.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="favorite_design" class="h22"><span><?php _e('Favorite items card design', 'delta'); ?></span></label> 
          <select name="favorite_design" id="favorite_design">
            <option value="" <?php echo (del_param('favorite_design') == '' ? 'selected="selected"' : ''); ?>><?php _e('Standard', 'delta'); ?></option>
            <option value="compact" <?php echo (del_param('favorite_design') == 'compact' ? 'selected="selected"' : ''); ?>><?php _e('Compact', 'delta'); ?></option>
            <option value="tiny" <?php echo (del_param('favorite_design') == 'tiny' ? 'selected="selected"' : ''); ?>><?php _e('Tiny', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Specify which card design will be used.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="blog_home" class="h9"><span><?php _e('Show Blog Widget on Home', 'delta'); ?></span></label> 
          <input name="blog_home" id="blog_home" class="element-slide" type="checkbox" <?php echo (del_param('blog_home') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Show blog articles widget on home page. Blog plugin must be installed', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="blog_home_count" class="h11"><span><?php _e('Number of Blog Articles on Home', 'delta'); ?></span></label> 
          <input size="8" name="blog_home_count" id="blog_home_count" type="number" value="<?php echo $blog_home_count; ?>" />

          <div class="mb-explain"><?php _e('How many blog articles will be shown on home page.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="blog_home_design" class="h22"><span><?php _e('Blog Articles Design', 'delta'); ?></span></label> 
          <select name="blog_home_design" id="blog_home_design">
            <option value="list" <?php echo (del_param('blog_home_design') <> 'grid' ? 'selected="selected"' : ''); ?>><?php _e('List view', 'delta'); ?></option>
            <option value="grid" <?php echo (del_param('blog_home_design') == 'grid' ? 'selected="selected"' : ''); ?>><?php _e('Gallery view', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Select if blog carts will be formed as list or grid (gallery).', 'delta'); ?></div>
        </div>
        

        <div class="mb-row">
          <label for="company_home" class="h10"><span><?php _e('Show Companies on Home', 'delta'); ?></span></label> 
          <input name="company_home" id="company_home" class="element-slide" type="checkbox" <?php echo (del_param('company_home') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Show companies block on home page. Business profile plugin must be installed', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="company_home_count" class="h11"><span><?php _e('Number of Companies on Home', 'delta'); ?></span></label> 
          <input size="8" name="company_home_count" id="company_home_count" type="number" value="<?php echo $company_home_count; ?>" />

          <div class="mb-explain"><?php _e('How many companies will be shown on home page.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="promote_home" class="h10"><span><?php _e('Show Promote Block on Home', 'delta'); ?></span></label> 
          <input name="promote_home" id="promote_home" class="element-slide" type="checkbox" <?php echo (del_param('promote_home') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Show promote block on home page (Earn money right now)', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="premium_home_count" class="h11"><span><?php _e('Number of Premiums on Home', 'delta'); ?></span></label> 
          <input size="8" name="premium_home_count" id="premium_home_count" type="number" value="<?php echo osc_esc_html(del_param('premium_home_count')); ?>" />

          <div class="mb-explain"><?php _e('How many premium listings will be shown on home page.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="premium_home_design" class="h22"><span><?php _e('Premium items card design (home)', 'delta'); ?></span></label> 
          <select name="premium_home_design" id="premium_design">
            <option value="" <?php echo (del_param('premium_home_design') == '' ? 'selected="selected"' : ''); ?>><?php _e('Standard', 'delta'); ?></option>
            <option value="compact" <?php echo (del_param('premium_home_design') == 'compact' ? 'selected="selected"' : ''); ?>><?php _e('Compact', 'delta'); ?></option>
            <option value="tiny" <?php echo (del_param('premium_home_design') == 'tiny' ? 'selected="selected"' : ''); ?>><?php _e('Tiny', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Specify which card design will be used.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="latest_random" class="h19"><span><?php _e('Show Latest Items in Random Order', 'delta'); ?></span></label> 
          <input name="latest_random" id="latest_random" class="element-slide" type="checkbox" <?php echo (del_param('latest_random') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Enable to show latest items in ranodm order each time page is refreshed.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="latest_picture" class="h20"><span><?php _e('Latest Items Picture Only', 'delta'); ?></span></label> 
          <input name="latest_picture" id="latest_picture" class="element-slide" type="checkbox" <?php echo (del_param('latest_picture') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Enable to show in latest section on home page only listings those has at least 1 picture.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="latest_premium" class="h21"><span><?php _e('Latest Premium Items', 'delta'); ?></span></label> 
          <input name="latest_premium" id="latest_premium" class="element-slide" type="checkbox" <?php echo (del_param('latest_premium') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Enable to show in latest section on home page only listings those are premium.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="latest_category" class="h22"><span><?php _e('Category for Latest Items', 'delta'); ?></span></label> 
          <select name="latest_category" id="latest_category">
            <option value="" <?php echo (del_param('latest_category') == '' ? 'selected="selected"' : ''); ?>><?php _e('All categories', 'delta'); ?></option>

            <?php while(osc_has_categories()) { ?>
              <option value="<?php echo osc_category_id(); ?>" <?php echo (del_param('latest_category') == osc_category_id() ? 'selected="selected"' : ''); ?>><?php echo osc_category_name(); ?></option>
            <?php } ?>
          </select>

          <div class="mb-explain"><?php _e('Select category that will be used to feed listings into latest items section on home page.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="latest_design" class="h22"><span><?php _e('Latest items card design', 'delta'); ?></span></label> 
          <select name="latest_design" id="latest_design">
            <option value="" <?php echo (del_param('latest_design') == '' ? 'selected="selected"' : ''); ?>><?php _e('Standard', 'delta'); ?></option>
            <option value="compact" <?php echo (del_param('latest_design') == 'compact' ? 'selected="selected"' : ''); ?>><?php _e('Compact', 'delta'); ?></option>
            <option value="tiny" <?php echo (del_param('latest_design') == 'tiny' ? 'selected="selected"' : ''); ?>><?php _e('Tiny', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Specify which card design will be used.', 'delta'); ?></div>
        </div>



        <div class="mb-row"><h3 class="sec"><?php _e('Search page settings', 'delta'); ?></h3></div>

        <div class="mb-row">
          <label for="def_view" class="h5"><span><?php _e('Default View on Search Page', 'delta'); ?></span></label> 
          <select name="def_view" id="def_view">
            <option value="0" <?php echo (del_param('def_view') == 0 ? 'selected="selected"' : ''); ?>><?php _e('Gallery view', 'delta'); ?></option>
            <option value="1" <?php echo (del_param('def_view') == 1 ? 'selected="selected"' : ''); ?>><?php _e('List view', 'delta'); ?></option>
          </select>
        </div>
        

        <div class="mb-row">
          <label for="premium_search" class="h14"><span><?php _e('Show Premiums Block on Search', 'delta'); ?></span></label> 
          <input name="premium_search" id="premium_search" class="element-slide" type="checkbox" <?php echo (del_param('premium_search') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Show Premium Listings block on Search Page.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="premium_search_count" class="h15"><span><?php _e('Number of Premiums on Search', 'delta'); ?></span></label> 
          <input size="8" name="premium_search_count" id="premium_search_count" type="number" value="<?php echo osc_esc_html(del_param('premium_search_count') ); ?>" />

          <div class="mb-explain"><?php _e('How many premium listings will be shown on Search page.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="def_cur" class="h18"><span><?php _e('Currency in Search Box', 'delta'); ?></span></label> 
          <select name="def_cur" id="def_cur">
            <?php foreach(osc_get_currencies() as $c) { ?>
              <option value="<?php echo $c['s_description']; ?>" <?php echo (del_param('def_cur') == $c['s_description'] ? 'selected="selected"' : ''); ?>><?php echo $c['s_description']; ?></option>
            <?php } ?>
          </select>

          <div class="mb-explain"><?php _e('Select currency symbol that will be used on search page for min & max price fields.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="search_ajax" class="h23"><span><?php _e('Live Search using Ajax', 'delta'); ?></span></label> 
          <input name="search_ajax" id="search_ajax" class="element-slide" type="checkbox" <?php echo (del_param('search_ajax') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Enable live realtime search without reloading of search page.', 'delta'); ?></div>
        </div>

    
        <div class="mb-row">
          <label for="save_search_position" class="h24"><span><?php _e('Position of "Save search" section', 'delta'); ?></span></label> 
          <select name="save_search_position" id="save_search_position">
            <option value="" <?php echo (del_param('save_search_position') == '' ? 'selected="selected"' : ''); ?>><?php _e('Under search results', 'delta'); ?></option>
            <option value="TOP" <?php echo (del_param('save_search_position') == 'TOP' ? 'selected="selected"' : ''); ?>><?php _e('Above search results', 'delta'); ?></option>
            <option value="SIDE" <?php echo (del_param('save_search_position') == 'SIDE' ? 'selected="selected"' : ''); ?>><?php _e('Sidebar', 'delta'); ?></option>
          </select>
        </div>  



        <div class="mb-row"><h3 class="sec"><?php _e('Publish listing settings', 'delta'); ?></h3></div>

        <div class="mb-row">
          <label for="publish_category" class="h2"><span><?php _e('Category box on Publish page', 'delta'); ?></span></label> 
          <select name="publish_category" id="publish_category">
            <option value="1" <?php echo (del_param('publish_category') == 1 ? 'selected="selected"' : ''); ?>><?php _e('Fancy box', 'delta'); ?></option>
            <option value="2" <?php echo (del_param('publish_category') == 2 ? 'selected="selected"' : ''); ?>><?php _e('Cascading drop-downs', 'delta'); ?></option>
            <option value="3" <?php echo (del_param('publish_category') == 3 ? 'selected="selected"' : ''); ?>><?php _e('One select box', 'delta'); ?></option>
            <option value="4" <?php echo (del_param('publish_category') == 4 ? 'selected="selected"' : ''); ?>><?php _e('Interactive select box', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Select what type of category selection (box) will be used on publish/edit page.', 'delta'); ?></div>
        </div>

        <div class="mb-row mb-row-select-multiple">
          <label for="post_required" class="h25"><span><?php _e('Required Fields on Publish', 'delta'); ?></span></label> 

          <input type="hidden" name="post_required" id="post_required" value="<?php echo $post_required; ?>"/>
          <select id="post_required_multiple" name="post_required_multiple" multiple>
            <option value="" <?php if($post_required == '') { ?>selected="selected"<?php } ?>><?php _e('None', 'delta'); ?></option>
            <option value="location" <?php if(in_array('location', $post_required_array)) { ?>selected="selected"<?php } ?>><?php _e('Location', 'delta'); ?></option>
            <option value="country" <?php if(in_array('country', $post_required_array)) { ?>selected="selected"<?php } ?>><?php _e('Country', 'delta'); ?></option>
            <option value="region" <?php if(in_array('region', $post_required_array)) { ?>selected="selected"<?php } ?>><?php _e('Region', 'delta'); ?></option>
            <option value="city" <?php if(in_array('city', $post_required_array)) { ?>selected="selected"<?php } ?>><?php _e('City', 'delta'); ?></option>
            <option value="name" <?php if(in_array('name', $post_required_array)) { ?>selected="selected"<?php } ?>><?php _e('Contact Name', 'delta'); ?></option>
            <option value="phone" <?php if(in_array('phone', $post_required_array)) { ?>selected="selected"<?php } ?>><?php _e('Phone', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('If you select Location as required, it means that one of following fields must be filled: Country, Region or City', 'delta'); ?></div>
        </div>

        <div class="mb-row mb-row-select-multiple">
          <label for="post_extra_exclude" class="h26"><span><?php _e('Extra Fields exclude Categories', 'delta'); ?></span></label> 
  
          <input type="hidden" name="post_extra_exclude" id="post_extra_exclude" value="<?php echo $post_extra_exclude; ?>"/>
          <select id="post_extra_exclude_multiple" name="post_extra_exclude_multiple" multiple>
            <?php echo del_cat_list($post_extra_exclude_array); ?>
          </select>

          <div class="mb-explain"><?php _e('Select categories where you do not want to show Transaction and Condition on listing publish/edit page', 'delta'); ?></div>
        </div>




        <div class="mb-row"><h3 class="sec"><?php _e('Other settings', 'delta'); ?></h3></div>

        <div class="mb-row">
          <label for="footer_link" class="h16"><span><?php _e('Footer Link', 'delta'); ?></span></label> 
          <input name="footer_link" id="footer_link" class="element-slide" type="checkbox" <?php echo (del_param('footer_link') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Link to osclass will be shown in footer to support our project.', 'delta'); ?></div>
        </div>

    
        <div class="mb-row">
          <label for="forms_ajax" class="h24"><span><?php _e('Form submit without reload (Ajax)', 'delta'); ?></span></label> 
          <input name="forms_ajax" id="forms_ajax" class="element-slide" type="checkbox" <?php echo (del_param('forms_ajax') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('Contact seller, Add new comment & Send to friend forms will be submitted without page reload.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="public_items" class="h26"><span><?php _e('Number of Items on Public Profile', 'delta'); ?></span></label> 
          <input size="8" name="public_items" id="public_items" type="number" value="<?php echo del_param('public_items'); ?>" />

          <div class="mb-explain"><?php _e('How many listings will be shown on user public profile. Keep in mind that pagination is not available on public profile.', 'delta'); ?></div>
        </div>


        <div class="mb-row">
          <label for="user_items" class="h2"><span><?php _e('Enable Other User Items Block', 'delta'); ?></span></label> 
          <input name="user_items" id="user_items" class="element-slide" type="checkbox" <?php echo (del_param('user_items') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, Other user listings will be shown at listing page.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="user_items_count" class="h3"><span><?php _e('Number of Other User Items', 'delta'); ?></span></label> 
          <input name="user_items_count" id="user_items_count" type="number" min="1" value="<?php echo del_param('user_items_count'); ?>" />

          <div class="mb-explain"><?php _e('Enter how many other user listings will be shown on item page.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="user_items_design" class="h22"><span><?php _e('Other user items card design', 'delta'); ?></span></label> 
          <select name="user_items_design" id="user_items_design">
            <option value="" <?php echo (del_param('user_items_design') == '' ? 'selected="selected"' : ''); ?>><?php _e('Standard', 'delta'); ?></option>
            <option value="compact" <?php echo (del_param('user_items_design') == 'compact' ? 'selected="selected"' : ''); ?>><?php _e('Compact', 'delta'); ?></option>
            <option value="tiny" <?php echo (del_param('user_items_design') == 'tiny' ? 'selected="selected"' : ''); ?>><?php _e('Tiny', 'delta'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Specify which card design will be used.', 'delta'); ?></div>
        </div>




        <h3 class="sec"><?php _e('Social Network Links', 'delta'); ?></h3>

        <div class="mb-row">
          <label for="footer_social_define" class=""><span><?php _e('Define Social Links', 'delta'); ?></span></label> 
          <input name="footer_social_define" id="footer_social_define" class="element-slide" type="checkbox" <?php echo (del_param('footer_social_define') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, social links (in footer) will point to URLs defined here. If empty, link to that network will be hidden. Otherwise link is auto-generated (share link).', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="footer_social_whatsapp" class=""><span><?php _e('Whatsapp', 'delta'); ?></span></label> 
          <input size="80" name="footer_social_whatsapp" id="footer_social_whatsapp" type="text" value="<?php echo osc_esc_html(del_param('footer_social_whatsapp') ); ?>" />

          <div class="mb-explain"><?php _e('Define URL that points to your company URL on network. Keep blank to hide network link.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="footer_social_facebook" class=""><span><?php _e('Facebook', 'delta'); ?></span></label> 
          <input size="80" name="footer_social_facebook" id="footer_social_facebook" type="text" value="<?php echo osc_esc_html(del_param('footer_social_facebook') ); ?>" />

          <div class="mb-explain"><?php _e('Define URL that points to your company URL on network. Keep blank to hide network link.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="footer_social_pinterest" class=""><span><?php _e('Pinterest', 'delta'); ?></span></label> 
          <input size="80" name="footer_social_pinterest" id="footer_social_pinterest" type="text" value="<?php echo osc_esc_html(del_param('footer_social_pinterest') ); ?>" />

          <div class="mb-explain"><?php _e('Define URL that points to your company URL on network. Keep blank to hide network link.', 'delta'); ?></div>
        </div>

        <div class="mb-row">
          <label for="footer_social_instagram" class=""><span><?php _e('Instagram', 'delta'); ?></span></label> 
          <input size="80" name="footer_social_instagram" id="footer_social_instagram" type="text" value="<?php echo osc_esc_html(del_param('footer_social_instagram') ); ?>" />

          <div class="mb-explain"><?php _e('Define URL that points to your company URL on network. Keep blank to hide network link.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="footer_social_x" class=""><span><?php _e('X (Twitter)', 'delta'); ?></span></label> 
          <input size="80" name="footer_social_x" id="footer_social_x" type="text" value="<?php echo osc_esc_html(del_param('footer_social_x') ); ?>" />

          <div class="mb-explain"><?php _e('Define URL that points to your company URL on network. Keep blank to hide network link.', 'delta'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="footer_social_linkedin" class=""><span><?php _e('Linkedin', 'delta'); ?></span></label> 
          <input size="80" name="footer_social_linkedin" id="footer_social_linkedin" type="text" value="<?php echo osc_esc_html(del_param('footer_social_linkedin') ); ?>" />

          <div class="mb-explain"><?php _e('Define URL that points to your company URL on network. Keep blank to hide network link.', 'delta'); ?></div>
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