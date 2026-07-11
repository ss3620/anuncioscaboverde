<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php'); ?>
  <link rel="stylesheet" media="print" href="<?php echo osc_current_web_theme_url('css/print.css?v=' . date('YmdHis')); ?>">

  <?php
    $itemviewer = (Params::getParam('itemviewer') == 1 ? 1 : 0);
    $item_extra = del_item_extra(osc_item_id());

    $location_array = array(osc_item_city(), osc_item_region(), osc_item_country_code());
    $location_array = array_filter($location_array);
    $location = implode(', ', $location_array);

    $location2_array = array(osc_item_address(), osc_item_zip(), osc_item_city_area(), osc_item_city(), osc_item_region(), osc_item_country());
    $location2_array = array_filter($location2_array);
    $location2 = implode(', ', $location2_array);

    $is_company = false;
    
    if(osc_item_user_id() <> 0) {
      $item_user = User::newInstance()->findByPrimaryKey(osc_item_user_id());
      View::newInstance()->_exportVariableToView('user', $item_user);
      $user_item_count = $item_user['i_items'];
      
      if($item_user['b_company'] == 1) {
        $is_company = true;
      }
    } else {
      $item_user = false;
      $user_item_count = Item::newInstance()->countItemTypesByEmail(osc_item_contact_email(), 'active');
    }
    
    
    $contact_name = (osc_item_contact_name() <> '' ? osc_item_contact_name() : __('Anonymous', 'delta'));

    $user_location_array = array(osc_user_city(), osc_user_region(), osc_user_country(), (osc_user_address() <> '' ? '<br/>' . osc_user_address() : ''));
    $user_location_array = array_filter($user_location_array);
    $user_location = implode(', ', $user_location_array);

    $item_user_location_array = array(osc_user_address(), osc_user_zip(), osc_user_city_area(), osc_user_city(), osc_user_region(), osc_user_country());
    $item_user_location_array = array_filter($item_user_location_array);
    $item_user_location = implode(', ', $item_user_location_array);
    

    $mobile_found = true;
    
    $mobile = osc_item_field("s_contact_phone");
    if($mobile == '') { $mobile = $item_extra['s_phone']; }      
    if($mobile == '' && function_exists('bo_mgr_show_mobile')) { $mobile = bo_mgr_show_mobile(); }
    if($mobile == '' && osc_item_user_id() <> 0) { $mobile = $item_user['s_phone_mobile']; }      
    if($mobile == '' && osc_item_user_id() <> 0) { $mobile = $item_user['s_phone_land']; } 
    if($mobile == '' || $mobile == null) { $mobile = ''; }
    $mobile_login_required = false;


    if(osc_item_show_phone() == 0) {
      $mobile = __('No phone number', 'delta');
      $mobile_found = false;
    } else if(osc_get_preference('reg_user_can_see_phone', 'osclass') == 1 && !osc_is_web_user_logged_in() && strlen(trim($mobile)) >= 4) {
      $mobile = __('Login to see phone number', 'delta');
      $mobile_found = true;
      $mobile_login_required = true;
    } else if(trim($mobile) == '' || strlen(trim($mobile)) < 4) { 
      $mobile = __('No phone number', 'delta');
      $mobile_found = false;
    }
    
    $item_user_mobile = isset($item_user['s_phone_mobile']) ? $item_user['s_phone_mobile'] : '';
    if($item_user_mobile == '' || $item_user_mobile == null) { $item_user_mobile = ''; }

    $item_user_mobile_found = true;
    $item_user_mobile_login_required = false;
    
    if(osc_get_preference('reg_user_can_see_phone', 'osclass') == 1 && !osc_is_web_user_logged_in() && strlen(trim($item_user_mobile)) >= 4) {
      $item_user_mobile = __('Login to see phone number', 'delta');
      $item_user_mobile_login_required = true;
    } else if(trim($item_user_mobile) == '' || strlen(trim($item_user_mobile)) < 4) { 
      $item_user_mobile = __('No phone number', 'delta');
      $item_user_mobile_found = false;
    } 
    
    $item_user_land = isset($item_user['s_phone_land']) ? $item_user['s_phone_land'] : '';
    if($item_user_land == '' || $item_user_land == null) { $item_user_land = ''; }

    $item_user_land_found = true;
    $item_user_land_login_required = false;
      
    if(osc_get_preference('reg_user_can_see_phone', 'osclass') == 1 && !osc_is_web_user_logged_in() && strlen(trim($item_user_land)) >= 4) {
      $item_user_land = __('Login to see phone number', 'delta');
      $item_user_land_login_required = true;
    } else if(trim($item_user_land) == '' || strlen(trim($item_user_land)) < 4) { 
      $item_user_land = __('No phone number', 'delta');
      $item_user_land_found = false;
    } 
    

    $has_cf = false;
    while(osc_has_item_meta()) {
      if(osc_item_meta_value() != '') {
        $has_cf = true;
        break;
      }
    }

    View::newInstance()->_reset('metafields');
    
    $make_offer_enabled = false;

    if(function_exists('mo_ajax_url')) {
      $history = osc_get_preference('history', 'plugin-make_offer');
      $category = osc_get_preference('category', 'plugin-make_offer');
      $category_array = explode(',', $category);

      $root = Category::newInstance()->findRootCategory(osc_item_category_id());
      $root_id = $root['pk_i_id'];

      if((in_array($root_id, $category_array) || trim($category) == '') && (osc_item_price() > 0 || osc_item_price() !== 0)) {
        $setting = ModelMO::newInstance()->getOfferSettingByItemId(osc_item_id());

        if((isset($setting['i_enabled']) && $setting['i_enabled'] == 1) || ((!isset($setting['i_enabled']) || $setting['i_enabled'] == '') && $history == 1)) {
          $make_offer_enabled = true;
        }
      }
    }
    
    $link_array = array('page' => 'search', 'sCategory' => osc_item_category_id(), 'sCountry' => osc_item_country_code(), 'sRegion' => osc_item_region_id(), 'sCity' => osc_item_city_id());
  ?>


  <!-- FACEBOOK OPEN GRAPH TAGS -->
  <?php osc_reset_resources(); ?>
  <?php osc_get_item_resources(); ?>
  <?php $resource_url = osc_resource_url(); ?>

  <meta property="og:title" content="<?php echo osc_esc_html(osc_item_title()); ?>" />
  <?php if(osc_count_item_resources() > 0) { ?><meta property="og:image" content="<?php echo $resource_url; ?>" /><?php } ?>
  <meta property="og:site_name" content="<?php echo osc_esc_html(osc_page_title()); ?>"/>
  <meta property="og:url" content="<?php echo osc_item_url(); ?>" />
  <meta property="og:description" content="<?php echo osc_esc_html(osc_highlight(osc_item_description(), 500)); ?>" />
  <meta property="og:type" content="article" />
  <meta property="og:locale" content="<?php echo osc_current_user_locale(); ?>" />
  <meta property="product:retailer_item_id" content="<?php echo osc_item_id(); ?>" /> 
  <meta property="product:price:amount" content="<?php echo strip_tags(osc_item_formated_price()); ?>" />
  <?php if(osc_item_price() <> '' and osc_item_price() <> 0) { ?><meta property="product:price:currency" content="<?php echo osc_item_currency(); ?>" /><?php } ?>

  <?php if(!function_exists('osc_structured_data_enabled')) { ?>
  <!-- GOOGLE RICH SNIPPETS -->
  <span itemscope itemtype="http://schema.org/Product">
    <meta itemprop="name" content="<?php echo osc_esc_html(osc_item_title()); ?>" />
    <meta itemprop="description" content="<?php echo osc_esc_html(osc_highlight(osc_item_description(), 500)); ?>" />
    <?php if(osc_count_item_resources() > 0) { ?><meta itemprop="image" content="<?php echo $resource_url; ?>" /><?php } ?>
  </span>
  <?php } ?>
  
</head>

<body id="body-item" class="page-body<?php if($itemviewer == 1) { ?> itemviewer<?php } ?><?php if(del_device() <> '') { echo ' dvc-' . del_device(); } ?>">
  <?php osc_current_web_theme_path('header.php') ; ?>

  <div id="listing" class="inside">
    <?php osc_run_hook('item_top'); ?>
    <?php echo del_banner('item_top'); ?>

    <!-- LISTING BODY - LEFT SIDE -->
    <div class="item">
      <div class="wbox imgbox">
        <?php if(osc_item_is_expired()) { ?>
          <div id="so-re" class="expired">
            <span><?php _e('This listing is expired!', 'delta'); ?></span>
          </div>
        <?php } ?>

        <?php if($item_extra['i_sold'] > 0) { ?>
          <div id="so-re" class="<?php echo ($item_extra['i_sold'] == 1 ? 'sold' : 'reserved'); ?>">
            <span><?php echo ($item_extra['i_sold'] == 1 ? __('Seller has marked this listing as <strong>SOLD</strong>', 'delta') : __('Seller has marked this listing as <strong>RESERVED</strong>', 'delta')); ?></span>
          </div>
        <?php } ?>
       

        <div class="basic isDesktop isTablet">
          <h1>
            <?php if(del_check_category_price(osc_item_category_id())) { ?>
              <div class="price mbCl3 p-<?php echo osc_item_price(); ?>x<?php if(osc_item_price() <= 0) { ?> isstring<?php } ?>"><?php echo osc_item_formated_price(); ?></div>
            <?php } ?>
            
            <?php echo osc_item_title(); ?>
          </h1>
          
          <?php if($make_offer_enabled) { ?>
            <a href="#" id="mk-offer" class="make-offer-link" data-item-id="<?php echo osc_item_id(); ?>" data-item-currency="<?php echo osc_item_currency(); ?>" data-ajax-url="<?php echo mo_ajax_url(); ?>&moAjaxOffer=1&itemId=<?php echo osc_item_id(); ?>"><?php _e('Submit your offer', 'delta'); ?></a>
          <?php } ?>
        </div>
        
        <?php osc_run_hook('item_title'); ?>



        <!-- HEADER & BASIC DATA -->
        <div class="pre-basic isDesktop isTablet">
          <?php if(function_exists('show_qrcode')) { ?>
            <div class="qr-code noselect">
              <?php show_qrcode(); ?>
            </div>
          <?php } ?>

          <div class="location">
            <?php if($location <> '') { ?>
              <a target="_blank"  href="https://maps.google.com/maps?daddr=<?php echo urlencode($location); ?>">
                <?php echo $location; ?>
              </a>
            <?php } else { ?>
              <?php _e('Unknown location', 'delta'); ?>
            <?php } ?>
          </div>
          
          <div class="category">
            <?php echo osc_item_category(); ?>
          </div>
          
          <div class="date">
            <?php echo sprintf(__('Posted %s', 'delta'), del_smart_date(osc_item_pub_date())); ?>
          </div>

          <div class="views">
            <?php echo sprintf(__('%d views', 'delta'), osc_item_views()); ?>
          </div>
        </div>



        <!-- IMAGE BOX -->
        <?php if(osc_images_enabled_at_items()) { ?> 
          <div class="main-data">
            <div id="slideimg" class="img<?php if(osc_count_item_resources() <= 0 ) { ?> noimg<?php } ?>">
              <a href="<?php echo osc_search_url($link_array); ?>" class="mlink isMobile"><i class="fas fa-arrow-left"></i></a>
              <a href="#contact" class="mlink con isMobile"><i class="far fa-envelope"></i></a>
              <a href="#" class="mlink share isMobile"><svg aria-hidden="true" focusable="false" dxmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="20px" height="20px"><path fill="currentColor" d="M352 320c-25.6 0-48.9 10-66.1 26.4l-98.3-61.5c5.9-18.8 5.9-39.1 0-57.8l98.3-61.5C303.1 182 326.4 192 352 192c53 0 96-43 96-96S405 0 352 0s-96 43-96 96c0 9.8 1.5 19.6 4.4 28.9l-98.3 61.5C144.9 170 121.6 160 96 160c-53 0-96 43-96 96s43 96 96 96c25.6 0 48.9-10 66.1-26.4l98.3 61.5c-2.9 9.4-4.4 19.1-4.4 28.9 0 53 43 96 96 96s96-43 96-96-43-96-96-96zm0-272c26.5 0 48 21.5 48 48s-21.5 48-48 48-48-21.5-48-48 21.5-48 48-48zM96 304c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm256 160c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48z" class=""></path></svg></a>
              
              <?php osc_get_item_resources(); ?>
              <?php osc_reset_resources(); ?>

              <?php if(osc_count_item_resources() > 0 ) { ?>  
                <div class="swiper-container">
                  <div class="swiper-wrapper">
                    <?php for($i = 0;osc_has_item_resources(); $i++) { ?>
                      <li class="swiper-slide">
                        <a href="<?php echo osc_resource_url(); ?>">
                          <img class="<?php echo (del_is_lazy() ? 'lazy' : ''); ?>" src="<?php echo (del_is_lazy() ? del_get_noimage('large') : osc_resource_url()); ?>" data-src="<?php echo osc_resource_url(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?> - <?php echo $i+1;?>/<?php echo osc_count_item_resources(); ?>"/>
                        </a>
                      </li>
                    <?php } ?>
                  </div>
                  
                  <div class="swiper-pg"></div>

                  <div class="swiper-button swiper-next"><i class="fas fa-angle-right"></i></div>
                  <div class="swiper-button swiper-prev"><i class="fas fa-angle-left"></i></div>
                </div>
              <?php } else { ?>

                <div class="image-empty"><?php _e('No pictures', 'delta'); ?></div>

              <?php } ?>
            </div>
            
            <?php osc_get_item_resources(); ?>
            <?php osc_reset_resources(); ?>

            <?php if(osc_count_item_resources() > 0 ) { ?>
              <div class="thumbs swiper-thumbs">
                <div class="scroll up"><i class="fas fa-angle-up"></i></div>
                
                <ul>
                  <?php for($i = 0;osc_has_item_resources(); $i++) { ?>
                    <li class="<?php if($i == 0) { ?>active<?php } ?>" data-id="<?php echo $i; ?>">
                      <img class="<?php echo (del_is_lazy() ? 'lazy' : ''); ?>" src="<?php echo (del_is_lazy() ? del_get_noimage() : osc_resource_thumbnail_url()); ?>" data-src="<?php echo osc_resource_thumbnail_url(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?> - <?php echo $i+1;?>"/>
                    </li>
                  <?php } ?>
                </ul>
                
                <div class="scroll down"><i class="fas fa-angle-down"></i></div>
              </div>
            <?php } ?>
          </div>
        <?php } ?>

        <?php osc_run_hook('item_images'); ?>
        

        <div class="basic isMobile">
          <h1>
            <?php if(del_check_category_price(osc_item_category_id())) { ?>
              <div class="price mbCl3 p-<?php echo osc_item_price(); ?>x<?php if(osc_item_price() <= 0) { ?> isstring<?php } ?>"><?php echo osc_item_formated_price(); ?></div>
            <?php } ?>
            
            <?php echo osc_item_title(); ?>
          </h1>
          
          <?php if($make_offer_enabled) { ?>
            <a href="#" id="mk-offer" class="make-offer-link" data-item-id="<?php echo osc_item_id(); ?>" data-item-currency="<?php echo osc_item_currency(); ?>" data-ajax-url="<?php echo mo_ajax_url(); ?>&moAjaxOffer=1&itemId=<?php echo osc_item_id(); ?>"><?php _e('Submit your offer', 'delta'); ?></a>
          <?php } ?>
        </div>



        <!-- HEADER & BASIC DATA -->
        <div class="pre-basic isMobile">
          <?php if(function_exists('show_qrcode')) { ?>
            <div class="qr-code noselect">
              <?php show_qrcode(); ?>
            </div>
          <?php } ?>

          <div class="location">
            <?php if($location <> '') { ?>
              <a target="_blank"  href="https://maps.google.com/maps?daddr=<?php echo urlencode($location); ?>">
                <?php echo $location; ?>
              </a>
            <?php } else { ?>
              <?php _e('Unknown location', 'delta'); ?>
            <?php } ?>
          </div>
          
          <div class="category">
            <?php echo osc_item_category(); ?>
          </div>
          
          <div class="date">
            <?php echo sprintf(__('Posted %s', 'delta'), del_smart_date(osc_item_pub_date())); ?>
          </div>

          <div class="views">
            <?php echo sprintf(__('%d views', 'delta'), osc_item_views()); ?>
          </div>
        </div>


        <!-- DESCRIPTION -->
        <div class="data">
          <?php del_make_favorite(); ?>

          <div class="description">
            <h2><?php _e('Description', 'delta'); ?></h2>

            <div class="text">
              <?php echo osc_item_description(); ?>
            </div>
          </div>
          
          <?php osc_run_hook('item_description'); ?>


          <!-- CUSTOM FIELDS -->
          <?php if($has_cf || $item_extra['i_transaction'] <> '' || $item_extra['i_condition'] <> '') { ?>
            <div class="custom-fields">
              <h2><?php _e('Attributes', 'delta'); ?></h2>

              <div class="list">
                <?php if(!in_array(osc_item_category_id(), del_extra_fields_hide())) { ?>
                  <?php if(del_get_simple_name($item_extra['i_condition'], 'condition') <> '') { ?>
                    <div class="field name item-condition value<?php echo $item_extra['i_transaction']; ?>">
                      <span class="name"><?php echo __('Condition', 'delta'); ?>:</span> 
                      <span class="value"><?php echo del_get_simple_name($item_extra['i_condition'], 'condition'); ?></span>
                    </div>
                  <?php } ?>

                  <?php if(del_get_simple_name($item_extra['i_transaction'], 'transaction') <> '') { ?>
                    <div class="field name item-transaction value<?php echo $item_extra['i_transaction']; ?>">
                      <span class="name"><?php echo __('Transaction', 'delta'); ?>:</span> 
                      <span class="value"><?php echo del_get_simple_name($item_extra['i_transaction'], 'transaction'); ?></span>
                    </div>
                  <?php } ?>
                <?php } ?>
                
                
                <?php while(osc_has_item_meta()) { ?>
                  <?php if(osc_item_meta_value() != '') { ?>
                    <div class="field name<?php echo osc_item_meta_name(); ?> value<?php echo osc_esc_html(osc_item_meta_value()); ?>">
                      <span class="name"><?php echo osc_item_meta_name(); ?><?php if(substr(trim(osc_item_meta_name()), -1) != ':') { echo ':'; } ?></span> 
                      <span class="value"><?php echo osc_item_meta_value(); ?></span>
                    </div>
                  <?php } ?>
                <?php } ?>
              </div>

            </div>
          <?php } ?>
          
          <?php osc_run_hook('item_meta'); ?>

     
          <!-- PLUGIN HOOK -->
          <div id="plugin-hook">
            <?php osc_run_hook('item_detail', osc_item()); ?>  
          </div>
        </div>

        <?php echo del_banner('item_description'); ?>
      </div>
      
      
      <?php
        // GET REGISTRATION DATE AND TYPE
        $reg_type = '';
        $last_online = '';

        if($item_user && $item_user['dt_reg_date'] <> '') { 
          $reg_type = sprintf(__('Registered for %s', 'delta'), del_smart_date2($item_user['dt_reg_date']));
        } else if ($item_user) { 
          $reg_type = __('Registered user', 'delta');
        } else {
          $reg_type = __('Unregistered user', 'delta');
        }

        if($item_user) {
          $last_online = sprintf(__('Last online %s', 'delta'), del_smart_date($item_user['dt_access_date']));
        }
        
        $user_about = nl2br(strip_tags(osc_user_info()));
      ?>

      <!-- REGISTERED USERS BLOCK -->
      <?php if(osc_item_user_id() > 0) { ?>
        <div class="wbox" id="about">
          <h2><?php echo sprintf(__('%s\'s profile', 'delta'), $item_user['s_name']); ?></h2>
          
          <div class="lb">
            <div class="user-card">
              <div class="image">
                <img src="<?php echo del_profile_picture($item_user['pk_i_id'], 'large'); ?>" alt="<?php echo osc_esc_html($contact_name); ?>"/>
              </div>
              
              <strong class="name"><?php echo $item_user['s_name']; ?></strong>
              
              <?php if(function_exists('ur_show_rating_link') && osc_item_user_id() > 0) { ?>
                <span class="ur-fdb">
                  <span class="strs"><?php echo ur_show_rating_stars(); ?></span>
                  <span class="lnk"><?php echo ur_add_rating_link(); ?></span>
                </span>
              <?php } ?>
          
              <span class="posting"><?php echo $reg_type; ?></span>
              
              <?php if($last_online != '') { ?>
                <span class="lastonline"><?php echo $last_online; ?></span>
              <?php } ?>
            </div>
          </div>
          
          <div class="rb">
            <strong class="about-head"><?php echo __('Seller\'s description', 'delta'); ?></strong>
            <span class="about"><?php echo (trim($user_about) <> '' ? $user_about : __('No description left by seller', 'delta')); ?></span>

            <?php if($item_user_land_login_required) { ?>
              <div class="extra">
                <a href="<?php echo osc_user_login_url(); ?>" class="" title="<?php echo osc_esc_html(__('Login to show number', 'delta')); ?>">
                  <span><?php _e('Login to show number', 'delta'); ?></span>
                </a>
              </div>            
            <?php } else if($item_user_land_found) { ?>
              <div class="extra">
                <a href="#" class="mobile" data-phone="<?php echo $item_user_land; ?>" title="<?php echo osc_esc_html(__('Click to show number', 'delta')); ?>">
                  <span><?php echo substr($item_user_land, 0, strlen($item_user_land) - 4) . 'xxxx'; ?></span>
                </a>
              </div>
            <?php } ?>

            <?php if($item_user_mobile_login_required) { ?>
              <div class="extra">
                <a href="<?php echo osc_user_login_url(); ?>" class="" title="<?php echo osc_esc_html(__('Login to show number', 'delta')); ?>">
                  <span><?php _e('Login to show number', 'delta'); ?></span>
                </a>
              </div>            
            <?php } else if($item_user_mobile_found) { ?>
              <div class="extra">
                <a href="#" class="mobile" data-phone="<?php echo $item_user_mobile; ?>" title="<?php echo osc_esc_html(__('Click to show number', 'delta')); ?>">
                  <span><?php echo substr($item_user_mobile, 0, strlen($item_user_mobile) - 4) . 'xxxx'; ?></span>
                </a>
              </div>
            <?php } ?>

            <?php if($item_user_location <> '') { ?>
              <strong class="address">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo $item_user_location; ?>
              </strong>
            <?php } ?>   

          </div>
          

          <div class="links">
            <a href="<?php echo osc_user_public_profile_url(osc_item_user_id()); ?>">
              <svg viewBox="0 0 32 32" width="14" height="14"><defs><path id="mbIconHome" d="M26.05 27.328a.862.862 0 01-.86.861h-4.982V17.41h-9v10.78H6.227a.863.863 0 01-.862-.862V13.125L15.634 2.82 26.05 13.082v14.246zm-12.842.861h5V19.41h-5v8.78zM31.41 15.552L15.62 0 0 15.676l1.416 1.412 1.949-1.956v12.196a2.865 2.865 0 002.862 2.861H25.19a2.864 2.864 0 002.86-2.86V15.051l1.956 1.925 1.404-1.425z"></path></defs><use fill="currentColor" xlink:href="#mbIconHome" fill-rule="evenodd" transform="translate(0 1)"></use></svg>
              <span><?php _e('Public profile', 'delta'); ?></span>
            </a>

            <a href="<?php echo osc_search_url(array('page' => 'search', 'userId' => osc_item_user_id())); ?>">
              <svg viewBox="0 0 32 32" width="14" height="14"><defs><path id="mbIconSearch" d="M12.618 23.318c-6.9 0-10.7-3.8-10.7-10.7 0-6.9 3.8-10.7 10.7-10.7 6.9 0 10.7 3.8 10.7 10.7 0 3.458-.923 6.134-2.745 7.955-1.821 1.822-4.497 2.745-7.955 2.745zm17.491 5.726l-7.677-7.678c1.854-2.155 2.804-5.087 2.804-8.748C25.236 4.6 20.636 0 12.618 0S0 4.6 0 12.618c0 8.019 4.6 12.618 12.618 12.618 3.485 0 6.317-.85 8.44-2.531l7.696 7.695 1.355-1.356z"></path></defs><use fill="currentColor" xlink:href="#mbIconSearch" fill-rule="evenodd"></use></svg>
              <span><?php _e('All seller listings', 'delta'); ?></span>
            </a>

            <?php if (trim(osc_user_website()) != '') { ?>
              <a href="<?php echo osc_user_website(); ?>">
                <svg viewBox="0 0 32 32" width="14" height="14"><defs><path id="mbIconExternal" d="M21.77 4.424l5.277-1.414-1.414 5.278-3.863-3.864zM29.874.18l-3.207 11.97-3.703-3.705-9.76 9.761-1.414-1.414 9.76-9.761-3.644-3.644L29.874.18zM22 24.323V14h2v10.324A5.682 5.682 0 0118.324 30H5.676A5.682 5.682 0 010 24.323V11.675A5.682 5.682 0 015.676 6H16v2H5.676A3.68 3.68 0 002 11.675v12.648A3.68 3.68 0 005.676 28h12.648A3.68 3.68 0 0022 24.323z"></path></defs><use fill="currentColor" xlink:href="#mbIconExternal" fill-rule="evenodd" transform="translate(1 1)"></use></svg>
                <span><?php echo osc_user_website(); ?></span>
              </a>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
      
      
      <!-- CONTACT BLOCK -->
      <div class="wbox" id="contact">
        <h2><?php echo sprintf(__('Contact %s (seller)', 'delta'), $contact_name); ?></h2>
        
        <div class="row">
          <form action="<?php echo osc_base_url(true) ; ?>" method="post" name="contact_form" id="contact_form">
            <input type="hidden" name="action" value="contact_post" />
            <input type="hidden" name="page" value="item" />
            <input type="hidden" name="id" value="<?php echo osc_item_id() ; ?>" />

            <?php osc_prepare_user_info() ; ?>

            <?php ContactForm::js_validation(); ?>
            <ul id="error_list"></ul>

            <?php if( osc_item_is_expired () ) { ?>
              <div class="problem">
                <?php _e('This listing expired, you cannot contact seller.', 'delta') ; ?>
              </div>
            <?php } else if( (osc_logged_user_id() == osc_item_user_id()) && osc_logged_user_id() != 0 ) { ?>
              <div class="problem">
                <?php _e('It is your own listing, you cannot contact yourself.', 'delta') ; ?>
              </div>
            <?php } else if( osc_reg_user_can_contact() && !osc_is_web_user_logged_in() ) { ?>
              <div class="problem">
                <?php _e('You must log in or register a new account in order to contact the advertiser.', 'delta') ; ?>
              </div>
            <?php } else { ?> 
              <div class="lb">
                <div id="item-card">
                  <?php if(osc_images_enabled_at_items()) { ?> 
                    <?php osc_get_item_resources(); ?>
                    <?php osc_reset_resources(); ?>

                    <?php if(osc_count_item_resources() > 0 ) { ?>
                      <div class="img">
                        <?php for($i = 0;osc_has_item_resources(); $i++) { ?>
                          <img src="<?php echo osc_resource_url(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?> - <?php echo $i+1;?>"/>
                          <?php break; ?>
                        <?php } ?>
                      </div>
                    <?php } ?>
                  <?php } ?>
                  
                  <div class="dsc">
                    <strong><?php echo osc_item_title(); ?></strong>
                    
                    <?php if(del_check_category_price(osc_item_category_id())) { ?>
                      <div><?php echo osc_item_formated_price(); ?></div>
                    <?php } ?>
                  </div>
                </div>

                <div class="row">
                  <label for="yourName"><?php _e('Name', 'delta') ; ?><span class="req">*</span></label> 
                  <div class="input-box"><?php ContactForm::your_name(); ?></div>
                </div>

                <div class="row">
                  <label for="yourEmail"><span><?php _e('E-mail', 'delta') ; ?></span><span class="req">*</span></label> 
                  <div class="input-box"><?php ContactForm::your_email(); ?></div>
                </div>       
           
                <div class="row">
                  <label for="phoneNumber"><span><?php _e('Phone', 'delta') ; ?></span></label> 
                  <div class="input-box"><?php ContactForm::your_phone_number(); ?></div>
                </div>
              </div>
        
              <div class="rb">
                <div class="row">
                  <?php ContactForm::your_message(); ?>
                </div>
                
                <?php del_show_recaptcha('contact_listing'); ?>

                <div class="row">
                  <button type="<?php echo (del_param('forms_ajax') == 1 ? 'button' : 'submit'); ?>" id="send-message" class="mbBg2 item-form-submit" data-type="contact">
                    <i class="fas fa-envelope"></i> <?php _e('Send message', 'delta'); ?>
                  </button>
                </div>
              </div>
            <?php } ?>
          </form>

          <div class="info"><?php _e('To protect against prohibited activities, we may check your message before it is forwarded to the recipient and, if necessary, block it.', 'delta'); ?></div>
        </div>
        
        <?php osc_run_hook('item_contact'); ?>
      </div>
      
      
      <!-- OTHER USER ITEMS BLOCK -->
      <?php if($user_item_count > 1 && del_param('user_items') == 1) { ?>
        <div class="wbox" id="user-items">
          <h2><?php _e('Other items from this seller', 'delta'); ?></h2>
          
          <div class="wrap">
            <?php del_related_ads('user', del_param('user_items_design'), del_param('user_items_count')); ?>
          </div>
        </div>      
      <?php } ?>
      

      <!-- COMMENTS BLOCK -->
      <?php if( osc_comments_enabled()) { ?>
        <div class="wbox" id="comment">
          <h2><?php _e('Comments', 'delta'); ?></h2>

          <div class="wrap">
            <?php if(osc_item_total_comments() > 0) { ?>
              <?php while(osc_has_item_comments()) { ?>
                <div class="comment">
                  <div class="image">
                    <img src="<?php echo del_profile_picture(osc_comment_user_id(), 'medium'); ?>" alt="<?php echo osc_esc_html(osc_comment_author_name()); ?>"/>
                  </div>

                  <div class="info">
                    <h3>
                      <?php echo(osc_comment_title() == '' ? __('Comment', 'delta') : osc_comment_title()); ?>
                      <span class="date"><?php echo del_smart_date(osc_comment_pub_date()); ?></span>
                    </h3>
                    
                    <?php if(function_exists('osc_enable_comment_rating') && osc_enable_comment_rating()) { ?>
                      <div class="rating">
                        <?php for($i = 1; $i <= 5; $i++) { ?>
                          <?php
                            $class = '';
                            if(osc_comment_rating() >= $i) {
                              $class = ' fill';
                            }
                          ?>
                          <i class="fa fa-star<?php echo $class; ?>"></i>
                        <?php } ?>

                        <span>(<?php echo sprintf(__('%d of 5', 'sigma'), osc_comment_rating()); ?>)</span>
                      </div>
                    <?php } ?>

                    <div class="body">
                      <?php
                        $comment_author = (osc_comment_author_name() == '' ? __('Anonymous', 'delta') : osc_comment_author_name());
                        
                        if(osc_comment_user_id() > 0) {
                          $comment_author = '<a href="' . osc_user_public_profile_url(osc_comment_user_id()) . '">' . $comment_author . '</a>';
                        }
                      ?>
                      
                      <strong><?php echo sprintf(__('%s wrote:', 'delta'), $comment_author); ?></strong>
                      <?php echo nl2br(osc_comment_body()); ?>
                    </div>
 
                    <?php if(osc_comment_user_id() && (osc_comment_user_id() == osc_logged_user_id())) { ?>
                      <a rel="nofollow" class="remove" href="<?php echo osc_delete_comment_url(); ?>" title="<?php echo osc_esc_html(__('Delete your comment', 'delta')); ?>">
                        <i class="fa fa-trash-o"></i> <span class="isDesktop"><?php _e('Delete', 'delta'); ?></span>
                      </a>
                    <?php } ?>
                  </div>
                  
                  
                  <?php if(function_exists('osc_enable_comment_reply') && osc_enable_comment_reply()) { ?>
                    <?php osc_get_comment_replies(); ?>
                    <?php if(osc_count_comment_replies() > 0) { ?>
                      <div id="comment-replies">
                        <?php while (osc_has_comment_replies()) { ?>
                          <div class="comment">
                            <div class="image">
                              <img src="<?php echo del_profile_picture(osc_comment_reply_user_id(), 'medium'); ?>" alt="<?php echo osc_esc_html(osc_comment_reply_author_name()); ?>"/>
                            </div>

                            <div class="info">
                              <h3>
                                <?php echo(osc_comment_reply_title() == '' ? __('Comment', 'delta') : osc_comment_reply_title()); ?>
                                <span class="date"><?php echo del_smart_date(osc_comment_reply_pub_date()); ?></span>
                              </h3>
                              
                              <?php if(function_exists('osc_enable_comment_rating') && osc_enable_comment_rating()) { ?>
                                <div class="rating">
                                  <?php for($i = 1; $i <= 5; $i++) { ?>
                                    <?php
                                      $class = '';
                                      if(osc_comment_reply_rating() >= $i) {
                                        $class = ' fill';
                                      }
                                    ?>
                                    <i class="fa fa-star<?php echo $class; ?>"></i>
                                  <?php } ?>

                                  <span>(<?php echo sprintf(__('%d of 5', 'sigma'), osc_comment_reply_rating()); ?>)</span>
                                </div>
                              <?php } ?>

                              <div class="body">
                                <?php
                                  $comment_author = (osc_comment_reply_author_name() == '' ? __('Anonymous', 'delta') : osc_comment_reply_author_name());
                                  
                                  if(osc_comment_reply_user_id() > 0) {
                                    $comment_author = '<a href="' . osc_user_public_profile_url(osc_comment_reply_user_id()) . '">' . $comment_author . '</a>';
                                  }
                                ?>
                                
                                <strong><?php echo sprintf(__('%s wrote:', 'delta'), $comment_author); ?></strong>
                                <?php echo nl2br(osc_comment_reply_body()); ?>
                              </div>
           
                              <?php if(osc_comment_reply_user_id() && (osc_comment_reply_user_id() == osc_logged_user_id())) { ?>
                                <a rel="nofollow" class="remove" href="<?php echo osc_delete_comment_url(); ?>" title="<?php echo osc_esc_html(__('Delete your comment', 'delta')); ?>">
                                  <i class="fa fa-trash-o"></i> <span class="isDesktop"><?php _e('Delete', 'delta'); ?></span>
                                </a>
                              <?php } ?>
                            </div>
                          </div>
                        <?php } ?>
                      </div>
                    <?php } ?>
                  <?php } ?>

                  <?php if(
                    (osc_reg_user_post_comments() && osc_is_web_user_logged_in() || !osc_reg_user_post_comments())
                    && function_exists('osc_enable_comment_reply')
                    && osc_enable_comment_reply() 
                    && (
                      osc_comment_reply_user_type() == ''
                      || osc_comment_reply_user_type() == 'LOGGED' && osc_is_web_user_logged_in()
                      || osc_comment_reply_user_type() == 'OWNER' && (osc_logged_user_id() == osc_item_user_id() && osc_item_user_id() > 0 || osc_logged_user_email() == osc_item_contact_email())
                      || osc_comment_reply_user_type() == 'ADMIN' && osc_is_admin_user_logged_in()
                    )
                  ) { ?>
                    <p class="comment-reply-row">
                      <?php $reply_params = array('replyToCommentId' => osc_comment_id()); ?>
                      <a class="btn btn-secondary comment-reply open-form" href="<?php echo del_item_fancy_url('comment', $reply_params); ?>"><?php _e('Reply', 'delta'); ?></a>
                    </p>
                  <?php } ?>
                  
                </div>
              <?php } ?>

              <div class="paginate comment-pagi"><?php echo osc_comments_pagination(); ?></div>

            <?php } else { ?>
              <div class="empty-comment"><?php _e('No comments has been added yet, be first to comment this ad!', 'delta'); ?></div>

            <?php } ?>
            
            <?php if(osc_reg_user_post_comments() && osc_is_web_user_logged_in() || !osc_reg_user_post_comments()) { ?>
              <a class="open-form new-comment btn mbBg3" href="<?php echo del_item_fancy_url('comment'); ?>" data-type="comment">
                <i class="fas fa-comment-alt"></i>
                <?php _e('Add comment', 'delta'); ?>
              </a>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
      
      <?php osc_run_hook('item_comment'); ?>

      <div class="itm-links">
        <a href="#" class="print"><i class="fas fa-print"></i> <?php _e('Print', 'delta'); ?></a>
        
        <?php if(!osc_item_send_friend_form_disabled()) { ?>
          <a class="friend open-form" href="<?php echo del_item_fancy_url('friend'); ?>" data-type="friend"><i class="far fa-thumbs-up"></i> <?php _e('Recommend', 'delta'); ?></a>
        <?php } ?>
        
        <div class="item-share">
          <a class="facebook" title="<?php echo osc_esc_html(__('Share on Facebook', 'delta')); ?>" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(osc_item_url()); ?>"><i class="fab fa-facebook"></i></a> 
          <a class="twitter" title="<?php echo osc_esc_html(__('Share on Twitter', 'delta')); ?>" target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo urlencode(osc_item_title()); ?>&url=<?php echo urlencode(osc_item_url()); ?>"><i class="fab fa-twitter"></i></a> 
          <a class="pinterest" title="<?php echo osc_esc_html(__('Share on Pinterest', 'delta')); ?>" target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(osc_item_url()); ?>&media=<?php echo urlencode($resource_url); ?>&description=<?php echo htmlspecialchars(osc_item_title()); ?>"><i class="fab fa-pinterest"></i></a> 
        </div>
      </div>
    </div>



    <!-- SIDEBAR - RIGHT -->
    <div class="side">
      <?php osc_run_hook('item_sidebar_top'); ?>
      
      <div class="wbox" id="seller">
        <h2>
          <?php _e('Seller details', 'delta'); ?>
          <?php if(osc_item_user_id() > 0) { ?>
            <a href="#about" class="hbtn"><?php _e('Details', 'delta'); ?></a>
          <?php } ?>
        </h2>
        
        <div class="user-box">
          <div class="wrap">
            <div class="user-img">
              <img src="<?php echo del_profile_picture(osc_item_user_id(), 'small'); ?>" alt="<?php echo osc_esc_html($contact_name); ?>" />
            </div>
            
            <strong class="name">
              <?php if(osc_item_user_id() > 0) { ?>
                <a href="<?php echo osc_user_public_profile_url(osc_item_user_id()); ?>"><?php echo $contact_name; ?></a>
              <?php } else { ?>
                <?php echo $contact_name; ?>
              <?php } ?>
            </strong>
            
            <div class="counts">
              <?php echo sprintf(__('%d active listings', 'delta'), $user_item_count); ?>
            </div>

            <div class="company">
              <?php echo ($is_company ? __('Professional seller', 'delta') : __('Non-Professional seller', 'delta')); ?>
            </div>
            
            <div class="times">
              <?php echo implode('<br/>', array_filter(array($reg_type, $last_online))); ?>
            </div>

            <?php if(del_chat_button(osc_item_user_id())) { echo del_chat_button(osc_item_user_id()); } ?>
            
            <a href="#contact" class="contact btn mbBg2">
              <i class="fas fa-envelope"></i>
              <span><?php _e('Contact', 'delta'); ?></span>
            </a>
            
            <?php if(osc_item_user_id() > 0) { ?>
              <a href="<?php echo osc_search_url(array('page' => 'search', 'userId' => osc_item_user_id())); ?>" class="other btn">
                <?php _e('All items', 'delta'); ?>
              </a>
            <?php } ?>
          </a>
          </div>
        </div>

        
        <div class="bottom-menu">
        
          <?php if($mobile_found) { ?>
            <div class="elem">
              <i class="fas fa-phone"></i>

              <?php if($mobile_login_required) { ?>
                <a href="<?php echo osc_user_login_url(); ?>" class="mobile login-required" data-phone="" title="<?php echo osc_esc_html(__('Login to show number', 'delta')); ?>"><?php echo osc_esc_html(__('Login to show number', 'delta')); ?></a>
              <?php } else { ?>
                <a href="#" class="mobile" data-phone="<?php echo $mobile; ?>" title="<?php echo osc_esc_html(__('Click to show number', 'delta')); ?>">
                  <span><?php echo substr($mobile, 0, strlen($mobile) - 4) . 'xxxx'; ?></span>
                </a>
              <?php } ?>
            </div>
          <?php } ?>
          
        
          <?php if(osc_item_show_email()) { ?>
            <div class="elem">
              <i class="fas fa-at"></i> 
              <a href="#" class="email" data-email="<?php echo osc_item_contact_email(); ?>" title="<?php echo osc_esc_html(__('Click to show email', 'delta')); ?>"><span><?php echo del_mask_email(osc_item_contact_email()); ?></span></a>
            </div>
          <?php } ?>

          <?php if(osc_item_user_id() > 0) { ?>
            <div class="elem">
              <i class="fas fa-home"></i>
              <a href="<?php echo osc_user_public_profile_url(osc_item_user_id()); ?>"><?php echo __('Seller\'s profile', 'delta'); ?></a>
            </div>
          <?php } ?>
          
          <?php if(trim(osc_user_website()) <> '') { ?>
            <div class="elem">
              <i class="fas fa-external-link-alt"></i>
              <a href="<?php echo osc_user_website(); ?>" target="_blank"><?php echo osc_user_website(); ?></a>
            </div>
          <?php } ?>
        </div>
      </div>

      <?php if(function_exists('sp_buttons')) { ?>
        <div class="sms-payments">
          <?php echo sp_buttons(osc_item_id());?>
        </div>
      <?php } ?>


      <div class="wbox location">
        <h2><?php _e('Listing location', 'delta'); ?></h2>
        
        <?php if($location2 <> '') { ?>
          <div class="row">
            <strong><?php echo $location2; ?></strong>
          </div>
          
          <?php if(osc_item_latitude() <> 0 && osc_item_longitude() <> 0) { ?>
            <div class="row latlong"><?php echo osc_item_latitude(); ?>, <?php echo osc_item_longitude(); ?></div> 
          <?php } ?>
          
          <div class="row">
            <a class="dir" target="_blank" href="https://maps.google.com/maps?daddr=<?php echo urlencode($location2); ?>">
              <i class="fas fa-map-marked-alt"></i>
              <?php _e('Get directions', 'delta'); ?>
            </a>
          </div>
        <?php } else { ?>
          <div class="row unknw"><?php _e('Unknown location', 'delta'); ?></div>
        <?php } ?>
        
        <div class="loc-hook">
          <?php osc_run_hook('location'); ?>
        </div>
      </div>


      <?php echo del_banner('item_sidebar'); ?>

      
      <div class="wbox safe-block">
        <h2><?php _e('Stay safe!', 'delta'); ?></h2>
        <div class="txt">
          <svg xmlns="http://www.w3.org/2000/svg" height="48" version="1.1" viewBox="-38 0 512 512.00142" width="48"> <g id="surface1"> <path d="M 217.996094 158.457031 C 164.203125 158.457031 120.441406 202.21875 120.441406 256.007812 C 120.441406 309.800781 164.203125 353.5625 217.996094 353.5625 C 271.785156 353.5625 315.546875 309.800781 315.546875 256.007812 C 315.546875 202.21875 271.785156 158.457031 217.996094 158.457031 Z M 275.914062 237.636719 L 206.027344 307.523438 C 203.09375 310.457031 199.246094 311.925781 195.402344 311.925781 C 191.558594 311.925781 187.714844 310.460938 184.78125 307.523438 L 158.074219 280.816406 C 152.207031 274.953125 152.207031 265.441406 158.074219 259.574219 C 163.9375 253.707031 173.449219 253.707031 179.316406 259.574219 L 195.402344 275.660156 L 254.671875 216.394531 C 260.535156 210.527344 270.046875 210.527344 275.914062 216.394531 C 281.78125 222.257812 281.78125 231.769531 275.914062 237.636719 Z M 275.914062 237.636719 " style=" stroke:none;fill-rule:nonzero;fill:<?php echo del_param('color'); ?>;fill-opacity:1;" /> <path d="M 435.488281 138.917969 L 435.472656 138.519531 C 435.25 133.601562 435.101562 128.398438 435.011719 122.609375 C 434.59375 94.378906 412.152344 71.027344 383.917969 69.449219 C 325.050781 66.164062 279.511719 46.96875 240.601562 9.042969 L 240.269531 8.726562 C 227.578125 -2.910156 208.433594 -2.910156 195.738281 8.726562 L 195.40625 9.042969 C 156.496094 46.96875 110.957031 66.164062 52.089844 69.453125 C 23.859375 71.027344 1.414062 94.378906 0.996094 122.613281 C 0.910156 128.363281 0.757812 133.566406 0.535156 138.519531 L 0.511719 139.445312 C -0.632812 199.472656 -2.054688 274.179688 22.9375 341.988281 C 36.679688 379.277344 57.492188 411.691406 84.792969 438.335938 C 115.886719 468.679688 156.613281 492.769531 205.839844 509.933594 C 207.441406 510.492188 209.105469 510.945312 210.800781 511.285156 C 213.191406 511.761719 215.597656 512 218.003906 512 C 220.410156 512 222.820312 511.761719 225.207031 511.285156 C 226.902344 510.945312 228.578125 510.488281 230.1875 509.925781 C 279.355469 492.730469 320.039062 468.628906 351.105469 438.289062 C 378.394531 411.636719 399.207031 379.214844 412.960938 341.917969 C 438.046875 273.90625 436.628906 199.058594 435.488281 138.917969 Z M 217.996094 383.605469 C 147.636719 383.605469 90.398438 326.367188 90.398438 256.007812 C 90.398438 185.648438 147.636719 128.410156 217.996094 128.410156 C 288.351562 128.410156 345.59375 185.648438 345.59375 256.007812 C 345.59375 326.367188 288.351562 383.605469 217.996094 383.605469 Z M 217.996094 383.605469 " style=" stroke:none;fill-rule:nonzero;fill:<?php echo del_param('color'); ?>;fill-opacity:1;" /> </g> </svg>
          <?php _e('Never pay down a deposit in a bank account until you have met the seller, seen signed a purchase agreement. No serious private advertisers ask for a down payment before you meet. Receiving an email with an in-scanned ID does not mean that you have identified the sender. You do this on the spot, when you sign a purchase agreement.', 'delta'); ?>
        </div>
      </div>


      <div class="ftr-block">
        <?php if(osc_is_web_user_logged_in() && osc_item_user_id() == osc_logged_user_id()) { ?>
          <div class="manage">
            <a href="<?php echo osc_item_edit_url(); ?>"><span><?php _e('Edit', 'delta'); ?></span></a>
            <a href="<?php echo osc_item_delete_url(); ?>"" onclick="return confirm('<?php _e('Are you sure you want to delete this listing? This action cannot be undone.', 'delta'); ?>?')"><span><?php _e('Remove', 'delta'); ?></span></a>

            <?php if(osc_item_is_inactive()) { ?>
              <?php if((function_exists('iv_add_item') && osc_get_preference('enable','plugin-item_validation') <> 1) || !function_exists('iv_add_item')) { ?>
                <a class="activate" target="_blank" href="<?php echo osc_item_activate_url(); ?>"><?php _e('Validate', 'delta'); ?></a>
              <?php } ?>
            <?php } ?>
          </div>
        <?php } ?>

        <div id="report" class="noselect">
          <a href="#" onclick="return false;">
            <i class="fas fa-exclamation-circle"></i>
            <?php _e('Report listing', 'delta'); ?>
          </a>

          <div class="cont-wrap">
            <div class="cont">
              <a id="item_spam" class="reports" href="<?php echo osc_item_link_spam() ; ?>" rel="nofollow"><?php _e('Spam', 'delta') ; ?></a>
              <a id="item_bad_category" class="reports" href="<?php echo osc_item_link_bad_category() ; ?>" rel="nofollow"><?php _e('Misclassified', 'delta') ; ?></a>
              <a id="item_repeated" class="reports" href="<?php echo osc_item_link_repeated() ; ?>" rel="nofollow"><?php _e('Duplicated', 'delta') ; ?></a>
              <a id="item_expired" class="reports" href="<?php echo osc_item_link_expired() ; ?>" rel="nofollow"><?php _e('Expired', 'delta') ; ?></a>
              <a id="item_offensive" class="reports" href="<?php echo osc_item_link_offensive() ; ?>" rel="nofollow"><?php _e('Offensive', 'delta') ; ?></a>
            </div>
          </div>
        </div>

      </div>    
      
      <?php echo del_banner('item_sidebar_bottom'); ?>
      <?php osc_run_hook('item_sidebar_bottom'); ?>
    </div>



    <?php echo del_banner('item_bottom'); ?>

  </div>

  <?php 
    if(del_param('related') == 1) {
      del_related_ads('category', del_param('related_design'), del_param('related_count'));
    }
  ?>
  
  <?php osc_run_hook('item_bottom'); ?>
  
  <div id="item-summary" class="isMobile <?php if($mobile_found) { ?>c3<?php } else { ?>c2<?php } ?>">
    <?php osc_get_item_resources(); ?>
    <?php if(osc_images_enabled_at_items() && osc_count_item_resources() > 0) { ?> 
      <div class="img">
        <?php osc_reset_resources(); ?>

        <?php for($i = 0;osc_has_item_resources(); $i++) { ?>
          <img class="<?php echo (del_is_lazy() ? 'lazy' : ''); ?>" src="<?php echo (del_is_lazy() ? del_get_noimage() : osc_resource_thumbnail_url()); ?>" data-src="<?php echo osc_resource_thumbnail_url(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?>" />
        <?php } ?>
      </div>
    <?php } ?>
    
    <div class="data">
      <?php if(del_check_category_price(osc_item_category_id())) { ?>
        <strong class="pr mbCl3"><?php echo osc_item_formated_price(); ?></strong>
      <?php } ?>
      
      <strong><?php echo osc_item_title(); ?></strong>
      <span>
        <?php _e('by', 'delta'); ?> 
        
        <?php if(osc_item_user_id()) { ?>
          <a href="<?php echo osc_user_public_profile_url(osc_item_user_id()); ?>"><?php echo $contact_name; ?></a>
        <?php } else { ?>
          <u><?php echo $contact_name; ?></u>
        <?php } ?>
      </span>
    </div>
    
    <div class="cnt">
      <?php if($mobile_found) { ?>
        <a href="#" class="mbBg b1 show-item-phone <?php if($mobile_login_required) { ?>login-required<?php } ?>"><i class="fas fa-phone-alt"></i></a>
      <?php } ?>
      
      <a href="#contact" class="mbBg2 b2"><i class="far fa-envelope"></i></a>
      
      <a href="#" class="mbBg3 shr">
        <svg aria-hidden="true" focusable="false" dxmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="20px" height="20px"><path fill="currentColor" d="M352 320c-25.6 0-48.9 10-66.1 26.4l-98.3-61.5c5.9-18.8 5.9-39.1 0-57.8l98.3-61.5C303.1 182 326.4 192 352 192c53 0 96-43 96-96S405 0 352 0s-96 43-96 96c0 9.8 1.5 19.6 4.4 28.9l-98.3 61.5C144.9 170 121.6 160 96 160c-53 0-96 43-96 96s43 96 96 96c25.6 0 48.9-10 66.1-26.4l98.3 61.5c-2.9 9.4-4.4 19.1-4.4 28.9 0 53 43 96 96 96s96-43 96-96-43-96-96-96zm0-272c26.5 0 48 21.5 48 48s-21.5 48-48 48-48-21.5-48-48 21.5-48 48-48zM96 304c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm256 160c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48z" class=""></path></svg>
      </a>
    </div>
    
    <?php if(!$mobile_login_required) { ?>
      <div class="mobile-item-data" style="display:none">
        <a href="tel:<?php echo $mobile; ?>" class="mbBg"><?php echo sprintf(__('Call %s', 'delta'), $mobile); ?></a>
        <a href="sms:<?php echo $mobile; ?>" class="mbBg"><?php echo __('Send SMS', 'delta'); ?></a>
        <a href="<?php echo $mobile; ?>" class="copy-number mbBg" data-done="<?php echo osc_esc_html(__('Copied!', 'delta')); ?>"><?php echo __('Copy number', 'delta'); ?></a>
      </div>
    <?php } else { ?>
      <div class="mobile-item-data" style="display:none">
        <a href="<?php echo osc_user_login_url(); ?>"><?php _e('Login to show phone number', 'delta'); ?></a>
      </div>
    <?php } ?>
    
    <div class="share-item-data" style="display:none">
      <a class="whatsapp" href="whatsapp://send?text=<?php echo urlencode(osc_item_url()); ?>" data-action="share/whatsapp/share"><i class="fab fa-whatsapp"></i> <?php _e('Share on Whatsapp', 'delta'); ?></a></span>
      <a class="facebook" title="<?php echo osc_esc_html(__('Share on Facebook', 'delta')); ?>" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(osc_item_url()); ?>"><i class="fab fa-facebook"></i> <?php _e('Share on Facebook', 'delta'); ?></a> 
      <a class="twitter" title="<?php echo osc_esc_html(__('Share on Twitter', 'delta')); ?>" target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo urlencode(osc_item_title()); ?>&url=<?php echo urlencode(osc_item_url()); ?>"><i class="fab fa-twitter"></i> <?php _e('Share on Twitter', 'delta'); ?></a> 
      <a class="pinterest" title="<?php echo osc_esc_html(__('Share on Pinterest', 'delta')); ?>" target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(osc_item_url()); ?>&media=<?php echo urlencode($resource_url); ?>&description=<?php echo htmlspecialchars(osc_item_title()); ?>"><i class="fab fa-pinterest"></i> <?php _e('Share on Pinterest', 'delta'); ?></a> 

      <?php if(!osc_item_send_friend_form_disabled()) { ?>
        <a class="friend open-form" href="<?php echo del_item_fancy_url('friend'); ?>" data-type="friend"><i class="fas fa-user-friends"></i> <?php _e('Share with friend', 'delta'); ?></a>
      <?php } ?>
    </div>
  </div>


  <script type="text/javascript">
    $(document).ready(function(){
      $('input[name="yourName"]').attr('placeholder', '<?php echo osc_esc_js(__('First name, Last name', 'delta')); ?>');
      $('input[name="yourEmail"]').attr('placeholder', '<?php echo osc_esc_js(__('your.email@dot.com', 'delta')); ?>');
      $('input[name="phoneNumber"]').attr('placeholder', '<?php echo osc_esc_js(__('+XXX XXX XXX', 'delta')); ?>');
      $('#contact textarea[name="message"]').val('<?php echo osc_esc_js(sprintf(__('Dear %s,<br/><br/>I am interested in your offer %s, <br/>Please contact me back.<br/><br/>With best regards,<br/>%s', 'delta'), $contact_name, osc_highlight(osc_item_title(), 50), osc_logged_user_name())); ?>');

      // SHOW PHONE NUMBER
      $('body').on('click', '.mobile', function(e) {
        if($(this).attr('href') == '#' && !$(this).hasClass('login-required')) {
          e.preventDefault()

          var phoneNumber = $(this).attr('data-phone');
          $(this).text(phoneNumber);
          $(this).attr('href', 'tel:' + phoneNumber);
          $(this).attr('title', '<?php echo osc_esc_js(__('Click to call', 'delta')); ?>');
        }        
      });


      // SHOW EMAIL
      $('body').on('click', '.email', function(e) {
        if($(this).attr('href') == '#') {
          e.preventDefault()

          var email = $(this).attr('data-email');
          $(this).text(email);
          $(this).attr('href', 'mailto:' + email);
          $(this).attr('title', '<?php echo osc_esc_js(__('Click to send mail', 'delta')); ?>');
        }        
      });


      // SHARE BUTTON
      $('#item-summary .cnt > a.shr').on('click', () => {
        if (navigator.share) {
          navigator.share({
              title: '<?php echo osc_esc_js(osc_highlight(osc_item_title(), 30) . ' - ' . osc_item_formated_price()); ?>',
              text: '<?php echo osc_esc_js(osc_highlight(osc_item_title(), 30) . ' - ' . osc_item_formated_price()); ?>',
              url: '<?php echo osc_esc_js(osc_item_url()); ?>',
            })
            .catch((error) => console.log('ER', error));
        } else {
          $('.share-item-data').stop(false, false).fadeToggle(200);
        }
        
        return false;
      });
      
      
      $('.main-data > .img .mlink.share').on('click', () => {
        if (navigator.share) {
          navigator.share({
              title: '<?php echo osc_esc_js(osc_highlight(osc_item_title(), 30) . ' - ' . osc_item_formated_price()); ?>',
              text: '<?php echo osc_esc_js(osc_highlight(osc_item_title(), 30) . ' - ' . osc_item_formated_price()); ?>',
              url: '<?php echo osc_esc_js(osc_item_url()); ?>',
            })
            .catch((error) => console.log('ER', error));
        } else {
          if(($('#item-summary').is(':hidden') || $('.share-item-data').is(':hidden')) && $('.main-data > .img .mlink.share').hasClass('shown')) {
            $('.main-data > .img .mlink.share').removeClass('shown');
          }
          
          if(!$('.main-data > .img .mlink.share').hasClass('shown')) {
            $('.share-item-data').fadeIn(200);
            
            if(!$('#item-summary').hasClass('shown')) {
              $('#item-summary').addClass('shown').show(0).css('overflow', 'visible').css('bottom', '-100px').css('opacity', '0').stop(false, false).animate( {bottom:'8px', opacity:1}, 250);
            }
          } else {
            $('.share-item-data').fadeOut(200);

            if($('#listing .item .data').offset().top - 50 > $(window).scrollTop()) {
              $('#item-summary').removeClass('shown').stop(false, false).animate( {bottom:'-100px', opacity:0}, 250, function() {$('#item-summary').hide(0);});
            }
          }
            
          $('.main-data > .img .mlink.share').toggleClass('shown');
        }
        
        return false;
      });
      

    });
  </script>

  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>				