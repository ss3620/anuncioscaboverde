<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
</head>
<body id="body-user-items" class="body-ua">
  <?php osc_current_web_theme_path('header.php') ; ?>

  <?php echo del_user_menu_top(); ?>

  <?php
    $param = (osc_version() >= 830 ? 'sItemType' : 'itemType');
    $current_type = (Params::getParam($param) <> '' ? Params::getParam($param) : 'all');
    $separator = osc_rewrite_enabled() ? '?' : '&';
    $base_url = osc_user_list_items_url();

    // Define item types
    $types = array(
      'all' => __('All items', 'delta'),
      'active' => __('Active items', 'delta'),
      'pending_validate' => __('Pending items', 'delta'),
      'expired' => __('Expired items', 'delta')
    );

    // Page subtitles
    $titles = array(
      'all' => __('List of all your listings, including active, inactive and expired items', 'delta'),
      'active' => __('Listings those are active and visible on site', 'delta'),
      'pending_validate' => __('Listings pending approval. Unapproved listings are not visible on site.', 'delta'),
      'expired' => __('Listings those has expired and are now not visible in site', 'delta')
    );

    // Count items for each type
    $counts = array(
      'active' => Item::newInstance()->countItemTypesByUserID(osc_logged_user_id(), 'active'),
      'pending_validate' => Item::newInstance()->countItemTypesByUserID(osc_logged_user_id(), 'pending_validate'),
      'expired' => Item::newInstance()->countItemTypesByUserID(osc_logged_user_id(), 'expired')
    );
    
    $counts['all'] = $counts['active'] + $counts['pending_validate'] + $counts['expired'];
  ?>

  <div class="inside user_account">
    <div class="usr-menu">
      <?php foreach($types as $type => $label) { ?>
        <a href="<?php echo $base_url . ($type != 'all' ? $separator . $param . '=' . $type : ''); ?>" class="<?php if($current_type == $type) { ?>active<?php } ?>">
          <strong><?php echo $label; ?></strong>
          <span><?php echo sprintf(__('%s listings', 'delta'), ($counts[$type] ?? 0)); ?></span>
        </a>
      <?php } ?>
    </div>

    <div id="main" class="items">
      <div class="inner-box">
        <div class="inside">
          <?php osc_run_hook('user_items_top'); ?>
          
          <h1><?php echo $types[$current_type] ?? __('Items', 'delta'); ?></h1>
          <h2><?php echo $titles[$current_type] ?? ''; ?></h2>

          <?php if(osc_version() >= 830) { ?>
            <form name="user-items-search" action="<?php echo osc_base_url(true); ?>" method="get" class="user-items-search-form nocsrf">
              <input type="hidden" name="page" value="user"/>
              <input type="hidden" name="action" value="items"/>

              <?php osc_run_hook('user_items_search_form_top'); ?>
              
              <div class="control-group">
                <label class="control-label" for="sItemType"><?php _e('Item type', 'delta'); ?></label>
                
                <div class="controls">
                  <?php UserForm::search_item_type_select(); ?>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label" for="sPattern"><?php _e('Keyword', 'delta'); ?></label>
                
                <div class="controls">
                  <?php UserForm::search_pattern_text(); ?>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label" for="sCategory"><?php _e('Category', 'delta'); ?></label>
                
                <div class="controls">
                  <?php UserForm::search_category_select(); ?>
                </div>
              </div>

              
              <?php osc_run_hook('user_items_search_form_bottom'); ?>
              
              <div class="actions">
                <button type="submit" class="btn btn-primary"><?php _e('Apply', 'delta'); ?></button>
              </div>
            </form>
          <?php } ?>

          <?php if(osc_count_items() > 0) { ?>
            <?php while(osc_has_items()) { ?>
              <div class="uitem lazy<?php if(osc_item_is_inactive()) { ?> inactive<?php } ?><?php if(osc_item_is_expired()) { ?> expired<?php } ?>">
                <?php if(osc_images_enabled_at_items()) { ?>
                  <div class="image">
                    <a href="<?php echo osc_item_url(); ?>">
                      <?php if(osc_count_item_resources() > 0) { ?>
                        <img src="<?php echo osc_resource_thumbnail_url(); ?>" title="<?php echo osc_esc_html(osc_item_title()); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?>" />
                      <?php } else { ?>
                        <img src="<?php echo del_get_noimage(); ?>" title="<?php echo osc_esc_html(osc_item_title()); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?>" />
                      <?php } ?>
                    </a>
                  </div>
                <?php } ?>

                <div class="body">
                  <?php
                    $loc = @array_filter(array(osc_item_city(), osc_item_region(), osc_item_country()))[0];
                  ?>

                  <div class="category">
                    <?php echo osc_item_category(); ?>
                    <?php if($loc <> '') { ?>
                      / <?php echo $loc; ?>
                    <?php } ?>
                  </div>

                  <div class="pub"><?php echo osc_format_date(osc_item_pub_date()); ?></div>

                  <div class="title">
                    <a href="<?php echo osc_item_url(); ?>"><?php echo osc_item_title(); ?></a>

                    <?php if(osc_item_is_inactive()) {?>
                      <div class="ua-premium inactive"><span><?php _e('Inactive', 'delta'); ?></span></div>
                    <?php } else if(osc_item_is_expired()) { ?>
                       <div class="ua-premium expired"><span><?php _e('Expired', 'delta'); ?></span></div>
                    <?php } else if(osc_item_is_premium()) { ?>
                      <div class="ua-premium mbBg3" title="<?php _e('This listing is premium', 'delta'); ?>"><?php _e('Premium', 'delta'); ?></div>
                    <?php } ?>
                  </div>

                  <?php if(del_check_category_price(osc_item_category_id())) { ?>
                    <span class="price mbCl"><?php echo osc_item_formated_price(); ?></span>
                  <?php } ?>

                  <?php osc_run_hook('user_items_body', osc_item_id()); ?>

                  <div class="buttons">
                    <?php if(osc_item_can_renew()) { ?>
                      <a class="renew" href="<?php echo osc_item_renew_url();?>" ><?php _e('Renew', 'delta'); ?></a>
                      <span class="delim">/</span>
                    <?php } ?>
            
                    <?php if(osc_item_is_active() && osc_can_deactivate_items()) {?>
                      <a class="deactivate" href="<?php echo osc_item_deactivate_url();?>" ><?php _e('Deactivate', 'delta'); ?></a>
                      <span class="delim">/</span>
                    <?php } ?>
                    
                    <?php if(osc_item_is_inactive()) { ?>
                      <?php if((function_exists('iv_add_item') && osc_get_preference('enable','plugin-item_validation') <> 1) || !function_exists('iv_add_item')) { ?>
                        <a class="activate" target="_blank" href="<?php echo osc_item_activate_url(); ?>"><?php _e('Validate', 'delta'); ?></a>
                        <span class="delim">/</span>
                      <?php } ?>
                    <?php } else { ?>
                      <?php $item_extra = del_item_extra(osc_item_id()); ?>
                      <?php 
                        if (osc_rewrite_enabled()) { 
                          if( $item_extra['i_sold'] == 0 ) {
                            $sold_url = '?itemId=' . osc_item_id() . '&markSold=1&secret=' . osc_item_field('s_secret');
                            $reserved_url = '?itemId=' . osc_item_id() . '&markSold=2&secret=' . osc_item_field('s_secret');
                          } else {
                            $sold_url = '?itemId=' . osc_item_id() . '&markSold=0&secret=' . osc_item_field('s_secret');
                            $reserved_url = '?itemId=' . osc_item_id() . '&markSold=0&secret=' . osc_item_field('s_secret');
                          }
                        } else {
                          if( $item_extra['i_sold'] == 0 ) {
                            $sold_url = '&itemId=' . osc_item_id() . '&markSold=1&secret=' . osc_item_field('s_secret');
                            $reserved_url = '&itemId=' . osc_item_id() . '&markSold=2&secret=' . osc_item_field('s_secret');
                          } else {
                            $sold_url = '&itemId=' . osc_item_id() . '&markSold=0&secret=' . osc_item_field('s_secret');
                            $reserved_url = '&itemId=' . osc_item_id() . '&markSold=0&secret=' . osc_item_field('s_secret');
                          }
                        }
                      ?>

                      <?php if(!in_array(osc_item_category_id(), del_extra_fields_hide())) { ?>
                        <a class="sold round2 tr1" href="<?php echo osc_user_list_items_url() . $sold_url; ?>"><?php echo ($item_extra['i_sold'] == 1 ? __('Not sold', 'delta') : __('Sold', 'delta')); ?></a>
                        <span class="delim">/</span>

                        <a class="reserved" href="<?php echo osc_user_list_items_url() . $reserved_url; ?>"><?php echo ($item_extra['i_sold'] == 2 ? __('Not reserved', 'delta') : __('Reserve', 'delta')); ?></a>
                        <span class="delim">/</span>
                      <?php } ?>                  
                    <?php } ?>
                    
                    <a class="edit" target="_blank" href="<?php echo osc_item_edit_url(); ?>" rel="nofollow"><?php _e('Edit', 'delta'); ?></a>
                    <span class="delim">/</span>

                    <?php if(function_exists('republish_link_raw') && republish_link_raw(osc_item_id())) { ?>
                      <a class="republish" href="<?php echo republish_link_raw(osc_item_id()); ?>" rel="nofollow"><?php _e('Republish', 'delta'); ?></a>
                      <span class="delim">/</span>
                    <?php } ?>
                    
                    <a class="delete" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to delete this listing? This action cannot be undone.', 'delta')); ?>')" href="<?php echo osc_item_delete_url(); ?>" rel="nofollow"><?php _e('Delete', 'delta'); ?></a>

                    <?php osc_run_hook('user_items_action', osc_item_id()); ?>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="ua-items-empty">
              <img src="<?php echo osc_current_web_theme_url('images/ua-empty.jpg'); ?>"/>
              <span><?php _e('No listings found', 'delta'); ?></span>
            </div>
          <?php } ?>

          <?php osc_run_hook('user_items_bottom'); ?>

          <div class="paginate">
            <?php echo osc_pagination_items(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>