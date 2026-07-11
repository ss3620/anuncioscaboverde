<?php
  // Create menu
  $title = __('Configure', 'blog');
  blg_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value
  $blog_order = mb_param_update('blog_order', 'plugin_action', 'check', 'plugin-blog');
  $blog_validate = mb_param_update('blog_validate', 'plugin_action', 'check', 'plugin-blog');
  $comment_enabled = mb_param_update('comment_enabled', 'plugin_action', 'check', 'plugin-blog');
  $comment_validate = mb_param_update('comment_validate', 'plugin_action', 'check', 'plugin-blog');
  $premium_groups = mb_param_update('premium_groups', 'plugin_action', 'value', 'plugin-blog');
  $hook_header_links = mb_param_update('hook_header_links', 'plugin_action', 'check', 'plugin-blog');
  $widget = mb_param_update('widget', 'plugin_action', 'check', 'plugin-blog');
  $widget_type = mb_param_update('widget_type', 'plugin_action', 'value', 'plugin-blog');
  $widget_limit = mb_param_update('widget_limit', 'plugin_action', 'value', 'plugin-blog');
  $widget_category = mb_param_update('widget_category', 'plugin_action', 'value', 'plugin-blog');

  $home_limit = mb_param_update('home_limit', 'plugin_action', 'value', 'plugin-blog');
  $search_limit = mb_param_update('search_limit', 'plugin_action', 'value', 'plugin-blog');
  $popular_limit = mb_param_update('popular_limit', 'plugin_action', 'value', 'plugin-blog');
  $canonical_redirect = mb_param_update('canonical_redirect', 'plugin_action', 'check', 'plugin-blog');
  $share_buttons = mb_param_update('share_buttons', 'plugin_action', 'check', 'plugin-blog');
  $sanitize = mb_param_update('sanitize', 'plugin_action', 'check', 'plugin-blog');



  $premium_groups_array = explode(',', (string)($premium_groups ?? ''));
  $premium_content = false;
  
  if(function_exists('osp_param')) {
    if(osp_param('groups_enabled') == 1) {
      $osp_groups = ModelOSP::newInstance()->getGroups();
      $premium_content = true;
    }
  }


  if(Params::getParam('plugin_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'blog') );
  }


  // GENERATE SITEMAP
  if(Params::getParam('blgSitemap') == 'generate') {
    $execution_time = blg_generate_sitemap();
    message_ok(__('Sitemap generated correctly in', 'blog') . ' ' . round($execution_time, 2) . ' ' . __('seconds', 'blog') . '. <br/><a href="' . osc_base_url() . 'sitemap_blog.xml" target="_blank">' . osc_base_url() . 'sitemap_blog.xml</a>');
  }

?>



<div class="mb-body">

  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head">
      <i class="fa fa-wrench"></i> <?php _e('Configure', 'blog'); ?>
    </div>

    <div class="mb-inside">
      <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_action" value="done" />



        <div class="mb-row">
          <label for="hook_header_links"><span><?php _e('Add Button to Header', 'blog'); ?></span></label> 
          <input type="checkbox" name="hook_header_links" id="hook_header_links" class="element-slide" <?php echo ($hook_header_links == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain"><?php _e('When enabled, "Blog" button/link is added into header. Require Osclass 8.2 theme hooks support.', 'blog'); ?></div>
        </div>
        
        
        <div class="mb-row">
          <label for="blog_order"><span><?php _e('Enable Article Ordering', 'blog'); ?></span></label> 
          <input type="checkbox" name="blog_order" id="blog_order" class="element-slide" <?php echo ($blog_order == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain"><?php _e('When enabled, admin can reorder blog articles, otherwise articles are sorted by publish date descending.', 'blog'); ?></div>
        </div>


        <div class="mb-row">
          <label for="home_limit"><span><?php _e('Home Page Limit', 'blog'); ?></span></label> 
          <input type="number" size="10" name="home_limit" id="home_limit" value="<?php echo $home_limit; ?>"/>
          <div class="mb-input-desc"><?php _e('articles', 'blog'); ?></div>

          <div class="mb-explain"><?php _e('Limit number of articles shown on home page.', 'blog'); ?></div>
        </div>

        <div class="mb-row">
          <label for="search_limit"><span><?php _e('Search Page Limit', 'blog'); ?></span></label> 
          <input type="number" name="search_limit" id="search_limit" value="<?php echo $search_limit; ?>"/>
          <div class="mb-input-desc"><?php _e('articles', 'blog'); ?></div>

          <div class="mb-explain"><?php _e('Limit number of articles shown on search page.', 'blog'); ?></div>
        </div>

        <div class="mb-row">
          <label for="popular_limit"><span><?php _e('Popular Articles Limit', 'blog'); ?></span></label> 
          <input type="number" name="popular_limit" id="popular_limit" value="<?php echo $popular_limit; ?>"/>
          <div class="mb-input-desc"><?php _e('articles', 'blog'); ?></div>

          <div class="mb-explain"><?php _e('Limit number of articles shown in Popular Articles section.', 'blog'); ?></div>
        </div>


        <div class="mb-row">
          <label for="blog_validate"><span><?php _e('Require Article Validation', 'blog'); ?></span></label> 
          <input type="checkbox" name="blog_validate" id="blog_validate" class="element-slide" <?php echo ($blog_validate == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain"><?php _e('When enabled, each article must be validated by admin first. These articles will have status private by default.', 'blog'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="canonical_redirect"><span><?php _e('Redirect Article to Canonical URL', 'blog'); ?></span></label> 
          <input type="checkbox" name="canonical_redirect" id="canonical_redirect" class="element-slide" <?php echo ($canonical_redirect == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain"><?php _e('When enabled, plugin will always redirect article to canonical version of URL, if not matching. Test well before using!', 'blog'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="share_buttons"><span><?php _e('Enable Share Buttons', 'blog'); ?></span></label> 
          <input type="checkbox" name="share_buttons" id="share_buttons" class="element-slide" <?php echo ($share_buttons == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain"><?php _e('When enabled, share buttons are added at bottom of article page (above comments section).', 'blog'); ?></div>
        </div>      

        <div class="mb-row">
          <label for="comment_enabled"><span><?php _e('Enable Comments', 'blog'); ?></span></label> 
          <input type="checkbox" name="comment_enabled" id="comment_enabled" class="element-slide" <?php echo ($comment_enabled == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain"><?php _e('When enabled, comments can be added on articles.', 'blog'); ?></div>
        </div>

        <div class="mb-row">
          <label for="comment_validate"><span><?php _e('Require Comments Validation', 'blog'); ?></span></label> 
          <input type="checkbox" name="comment_validate" id="comment_validate" class="element-slide" <?php echo ($comment_validate == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain"><?php _e('When enabled, each comment must be validated by admin before it is shown in blog.', 'blog'); ?></div>
        </div>

        <div class="mb-row mb-row-select-multiple">
          <label for="premium_groups_multiple"><span><?php _e('Premium Groups', 'blog'); ?></span></label> 

          <input type="hidden" name="premium_groups" id="premium_groups" value="<?php echo $premium_groups; ?>"/>
          <select id="premium_groups_multiple" name="premium_groups_multiple" multiple>
            <?php if(!$premium_content || count($osp_groups) <= 0) { ?>
              <option value="" selected="selected"><?php _e('No groups in Osclass Pay Plugin', 'blog'); ?></option>
            <?php } else { ?>
              <?php foreach($osp_groups as $g) { ?>
                <option value="<?php echo $g['pk_i_id']; ?>" <?php if(in_array($g['pk_i_id'], $premium_groups_array)) { ?>selected="selected"<?php } ?>><?php echo $g['s_name']; ?></option>
              <?php } ?>
            <?php } ?>
          </select>

          <div class="mb-explain"><?php _e('Select user groups from Osclass Pay Plugin that are allowed to see premium content.', 'blog'); ?></div>
        </div>

        <div class="mb-row">
          <label for="sanitize"><span><?php _e('Sanitize URL strings', 'blog'); ?></span></label> 
          <input type="checkbox" name="sanitize" id="sanitize" class="element-slide" <?php echo ($sanitize == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('When enabled, plugin will try to sanitize text in URLs. Otherwise only Osclass built-in sanitization (or none) is used.', 'blog'); ?></div>
            <div class="mb-line"><?php _e('Try to disable when using with non-latin characters (Cyrillic, Greek, ...', 'blog'); ?></div>
          </div>
        </div>  
        

        <div class="mb-subtitle"><?php _e('Widget settings', 'blog'); ?></div>

        <div class="mb-row">
          <label for="widget"><span><?php _e('Enable Widget', 'blog'); ?></span></label> 
          <input type="checkbox" name="widget" id="widget" class="element-slide" <?php echo ($widget == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain"><?php _e('When enabled, widget will be shown in front.', 'blog'); ?></div>
        </div>

        <div class="mb-row">
          <label for="widget_type"><span><?php _e('Widget Type', 'blog'); ?></span></label> 
          <select name="widget_type">
            <option value="grid" <?php if($widget_type == 'grid') { ?>selected="selected"<?php } ?>><?php _e('Grid', 'blog'); ?></option>
            <option value="list" <?php if($widget_type == 'list') { ?>selected="selected"<?php } ?>><?php _e('List', 'blog'); ?></option>
          </select>
          
          <div class="mb-explain"><?php _e('Select type of widget. Grid - for large width space (home page). List - for small width space (side bar).', 'blog'); ?></div>
        </div>

        <div class="mb-row">
          <label for="widget_limit"><span><?php _e('Widget Limit', 'blog'); ?></span></label> 
          <input type="number" name="widget_limit" id="widget_limit" min="1" max="5" value="<?php echo $widget_limit; ?>"/>
          <div class="mb-input-desc"><?php _e('articles', 'blog'); ?></div>

          <div class="mb-explain"><?php _e('Limit number of articles in widget of type list.', 'blog'); ?></div>
        </div>

        <div class="mb-row">
          <label for="widget_category"><span><?php _e('Widget Category', 'blog'); ?></span></label> 

          <?php $cats = ModelBLG::newInstance()->getCategories(); ?>

          <select name="widget_category">
            <option value="0" <?php if(count($cats) <= 0) { ?>selected="selected"<?php } ?>><?php _e('All categories', 'blog'); ?></option>

            <?php if(count($cats) > 0) { ?>
              <?php foreach($cats as $c) { ?>
                <option value="<?php echo $c['pk_i_id']; ?>" <?php if($widget_category == $c['pk_i_id']) { ?>selected="selected"<?php } ?>><?php echo $c['s_name']; ?></option>
              <?php } ?>
            <?php } ?>
          </select>
          
          <div class="mb-explain"><?php _e('Select category from that will be article shown in widget.', 'blog'); ?></div>
        </div>



        <div class="mb-subtitle-end"></div>


        <div class="mb-row">
          <label><span><?php _e('Sitemap', 'blog'); ?></span></label> 
          <a class="mb-button-green mb-regenerate" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/configure.php&blgSitemap=generate"><?php _e('Regenerate sitemap now', 'blog'); ?></a>

          <div class="mb-explain"><?php _e('Sitemap is regenerated automatically via cron daily.', 'blog'); ?></div>
        </div>


        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <?php if(!blg_is_demo()) { ?><button type="submit" class="mb-button"><?php _e('Save', 'blog');?></button><?php } ?>
        </div>
      </form>
    </div>
  </div>




  <!-- PLUGIN INTEGRATION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Plugin Setup', 'blog'); ?></div>

    <div class="mb-inside">
      <div class="mb-row"><?php _e('No theme modification are required to use all functions of plugin, it is required only to show link/button to your blog', 'blog'); ?></div>
      <div class="mb-row">
        <strong><?php _e('Button', 'blog'); ?></strong>
        <span class="mb-code">&lt;?php if(function_exists('blg_home_button')) { echo blg_home_button(); } ?&gt;</span>
      </div>

      <div class="mb-row">
        <strong><?php _e('Raw link', 'blog'); ?></strong>
        <span class="mb-code">&lt;?php if(function_exists('blg_home_link')) { echo blg_home_link(); } ?&gt;</span>
      </div>

      <div class="mb-row">
        <strong><?php _e('Widget', 'blog'); ?></strong>
        <span class="mb-code">&lt;?php osc_run_hook('blg_widget'); ?&gt;</span>
      </div>

      <div class="mb-row"><?php _e('Link to your blog home page is:', 'blog'); ?> <a href="<?php echo osc_route_url('blg-home'); ?>"><?php echo osc_route_url('blg-home'); ?></a></div>
    </div>
  </div>
</div>


<?php echo blg_footer(); ?>