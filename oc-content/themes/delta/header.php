<?php 
  osc_goto_first_locale(); 
  
  $mes_counter = del_count_messages(osc_logged_user_id()); 
  $fav_counter = del_count_favorite();
?>

<header>
  <div class="inside">
    <?php osc_run_hook('header_top'); ?>
    
    <div class="relative1">
      <?php if(function_exists('blg_home_link')) { ?>
        <a class="blog" href="<?php echo blg_home_link(); ?>"><?php _e('Blog', 'delta'); ?></a>
      <?php } ?>

      <?php if(function_exists('bpr_companies_url')) { ?>
        <a class="company" href="<?php echo bpr_companies_url(); ?>"><?php _e('Companies', 'delta'); ?></a>
      <?php } ?>

      <?php if(function_exists('frm_home')) { ?>
        <a class="forum" href="<?php echo frm_home(); ?>"><?php _e('Forums', 'delta'); ?></a>
      <?php } ?>

      <?php if(function_exists('fi_make_favorite')) { ?>
        <a class="favorite" href="<?php echo osc_route_url('favorite-lists'); ?>">
          <span><?php _e('Favorite', 'delta'); ?></span>

          <?php if($fav_counter > 0) { ?>
            <span class="counter mbBg3"><?php echo $fav_counter; ?></span>
          <?php } ?>
        </a>
        
      <?php } else if(function_exists('svi_save_btn')) { ?>
        <a class="favorite svi-show-saved" href="#">
          <span><?php _e('Saved', 'delta'); ?></span>

          <?php if($fav_counter > 0) { ?>
            <span class="counter mbBg3"><?php echo $fav_counter; ?></span>
          <?php } ?>
        </a>
        
      <?php } ?>
      
      <?php if(function_exists('im_messages')) { ?>
        <a href="<?php echo osc_route_url('im-threads'); ?>">
          <span><?php _e('Messages', 'delta'); ?></span>
        
          <?php if($mes_counter > 0) { ?>
            <span class="counter mbBg3"><?php echo $mes_counter; ?></span>
          <?php } ?>        
        </a>
      <?php } ?>
      
      <?php if(function_exists('faq_home_link')) { ?>
        <a href="<?php echo faq_home_link(); ?>"><?php _e('FAQ', 'delta'); ?></a>
      <?php } ?>
        
      <a href="<?php echo osc_contact_url(); ?>"><?php _e('Contact us', 'delta'); ?></a>

      <?php osc_run_hook('header_links'); ?>
    </div>
    
    <div class="relative2">
      <div class="left">
        <div class="logo">
          <a href="<?php echo osc_base_url(); ?>"><?php echo del_logo(); ?></a>
        </div>
      </div>


      <div class="right isDesktop isTablet">
        <a class="publish btn mbBg2 isDesktop isTablet" href="<?php echo osc_item_post_url(); ?>">
          <span class="mbCl2">
            <svg version="1.1" widt="18px" height="18px" fill="<?php echo del_param('color2'); ?>" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 328.911 328.911" style="enable-background:new 0 0 328.911 328.911;" xml:space="preserve"> <g> <g> <path d="M310.199,18.71C297.735,6.242,282.65,0.007,264.951,0.007H63.954c-17.703,0-32.79,6.235-45.253,18.704 C6.235,31.177,0,46.261,0,63.96v200.991c0,17.515,6.232,32.552,18.701,45.11c12.467,12.566,27.553,18.843,45.253,18.843h201.004 c17.699,0,32.777-6.276,45.248-18.843c12.47-12.559,18.705-27.596,18.705-45.11V63.96 C328.911,46.261,322.666,31.177,310.199,18.71z M292.362,264.96c0,7.614-2.673,14.089-8.001,19.414 c-5.324,5.332-11.799,7.994-19.41,7.994H63.954c-7.614,0-14.082-2.662-19.414-7.994c-5.33-5.325-7.992-11.8-7.992-19.414V63.965 c0-7.613,2.662-14.086,7.992-19.414c5.327-5.327,11.8-7.994,19.414-7.994h201.004c7.61,0,14.086,2.663,19.41,7.994 c5.325,5.328,7.994,11.801,7.994,19.414V264.96z"/> <path d="M246.683,146.189H182.73V82.236c0-2.667-0.855-4.854-2.573-6.567c-1.704-1.714-3.895-2.568-6.564-2.568h-18.271 c-2.667,0-4.854,0.854-6.567,2.568c-1.714,1.713-2.568,3.903-2.568,6.567v63.954H82.233c-2.664,0-4.857,0.855-6.567,2.568 c-1.711,1.713-2.568,3.903-2.568,6.567v18.271c0,2.666,0.854,4.855,2.568,6.563c1.712,1.708,3.903,2.57,6.567,2.57h63.954v63.953 c0,2.666,0.854,4.855,2.568,6.563c1.713,1.711,3.903,2.566,6.567,2.566h18.271c2.67,0,4.86-0.855,6.564-2.566 c1.718-1.708,2.573-3.897,2.573-6.563V182.73h63.953c2.662,0,4.853-0.862,6.563-2.57c1.712-1.708,2.563-3.897,2.563-6.563v-18.271 c0-2.664-0.852-4.857-2.563-6.567C251.536,147.048,249.345,146.189,246.683,146.189z"/> </g> </g> </svg>
            <span><?php _e('Post an ad', 'delta'); ?></span>
          </span>
        </a>
        
        <?php if(!osc_is_web_user_logged_in()) { ?>
          <a class="register" href="<?php echo osc_register_account_url(); ?>"><?php _e('Register', 'delta'); ?></a>
          <a class="login" href="<?php echo osc_user_login_url(); ?>"><?php _e('Login', 'delta'); ?></a>
        <?php } else { ?>
          <a class="logout" href="<?php echo osc_user_logout_url(); ?>"><?php _e('Log out', 'delta'); ?></a>

          <div class="link-box">
            <a class="my-account" href="<?php echo osc_user_dashboard_url(); ?>"><?php _e('My account', 'delta'); ?></a>
            
            <div class="user-menu" style="display:none;">
              <div class="ins">
                <strong class="ld">
                  <div class="image">
                    <img src="<?php echo del_profile_picture(osc_logged_user_id(), 'small'); ?>" />
                  </div>
                  
                  <span><?php echo sprintf(__('Hello %s!', 'delta'), osc_logged_user_name()); ?></span>
                </strong>

                <div class="line"></div>

                <a href="<?php echo osc_user_list_items_url(); ?>"><?php _e('My listings', 'delta'); ?></a>
                <a href="<?php echo osc_user_profile_url(); ?>"><?php _e('Profile', 'delta'); ?></a>
                <a href="<?php echo osc_user_alerts_url(); ?>"><?php _e('Subscriptions', 'delta'); ?></a>

                <?php if(function_exists('fi_make_favorite')) { ?>
                  <a href="<?php echo osc_route_url('favorite-lists'); ?>"><?php _e('Favorite items', 'delta'); ?></a>
                <?php } ?>

                <?php if(function_exists('im_messages')) { ?>
                  <a href="<?php echo osc_route_url('im-threads'); ?>"><?php _e('Messages', 'delta'); ?></a>
                <?php } ?>

                <?php if(function_exists('osp_user_sidebar')) { ?>
                  <a href="<?php echo osc_route_url('osp-item'); ?>"><?php _e('Promotions', 'delta'); ?></a>
                <?php } ?>
                
                <a href="<?php echo osc_user_public_profile_url(osc_logged_user_id()); ?>"><?php _e('Public profile', 'delta'); ?></a>


                <div class="line"></div>

                <a class="logout" href="<?php echo osc_user_logout_url(); ?>"><?php _e('Log out', 'delta'); ?></a>
              </div>
            </div> 
          </div>
        <?php } ?>
      </div>
    </div>
    
    <?php osc_run_hook('header_bottom'); ?>
  </div>
</header>

<?php osc_run_hook('header_after'); ?>

<?php 
  $loc = (osc_get_osclass_location() == '' ? 'home' : osc_get_osclass_location());
  $sec = (osc_get_osclass_section() == '' ? 'default' : osc_get_osclass_section());
?>

<section class="content loc-<?php echo $loc; ?> sec-<?php echo $sec; ?>">

<?php
  if(osc_is_home_page()) { 
    osc_current_web_theme_path('inc.search.php'); 
    osc_current_web_theme_path('inc.category.php');
  }
?>

<div class="flash-box">
  <div class="flash-wrap">
    <?php osc_show_flash_message(); ?>
  </div>
</div>


<?php
  osc_show_widgets('header');
  $breadcrumb = osc_breadcrumb('>', false);
  $breadcrumb = str_replace('<span itemprop="title">' . osc_page_title() . '</span>', '<span itemprop="title">' . del_param('website_name') . '</span>', $breadcrumb);
  $breadcrumb = str_replace('<span itemprop="name">' . osc_page_title() . '</span>', '<span itemprop="name">' . del_param('website_name') . '</span>', $breadcrumb);
?>

<?php if($breadcrumb != '') { ?>
  <div id="bread">
    <div class="inside">
      <div class="wrap">
        <?php if(osc_is_ad_page()) { ?>
          <?php $link_array = array('page' => 'search', 'sCategory' => osc_item_category_id(), 'sCountry' => osc_item_country_code(), 'sRegion' => osc_item_region_id(), 'sCity' => osc_item_city_id()); ?>
          <a href="<?php echo osc_search_url($link_array); ?>" class="goback" ><i class="fas fa-arrow-left"></i> <?php _e('Search', 'delta'); ?></a>
        <?php } ?>
        
        <div class="bread-text"><?php echo $breadcrumb; ?></div>
        
        <?php if(osc_is_ad_page()) { ?>
          <?php
            $next_link = del_next_prev_item('next', osc_item_category_id(), osc_item_id());
            $prev_link = del_next_prev_item('prev', osc_item_category_id(), osc_item_id());
          ?>
          
          <div class="navlinks">
            <?php if($prev_link !== false) { ?><a href="<?php echo $prev_link; ?>" class="prev"><i class="fas fa-angle-left"></i> <?php _e('Previous', 'delta'); ?></a><?php } ?>
            <?php if($next_link !== false) { ?><a href="<?php echo $next_link; ?>" class="next"><i class="fas fa-angle-right"></i> <?php _e('Next', 'delta'); ?></a><?php } ?>
          </div>
        <?php } else if(osc_get_osclass_location() == 'user' && osc_get_osclass_section() == 'pub_profile') { ?>
          <?php
            $next_link = del_next_prev_user('next', osc_user_id());
            $prev_link = del_next_prev_user('prev', osc_user_id());
          ?>
          
          <div class="navlinks">
            <?php if($prev_link !== false) { ?><a href="<?php echo $prev_link; ?>" class="prev"><i class="fas fa-angle-left"></i> <?php _e('Previous', 'delta'); ?></a><?php } ?>
            <?php if($next_link !== false) { ?><a href="<?php echo $next_link; ?>" class="next"><i class="fas fa-angle-right"></i> <?php _e('Next', 'delta'); ?></a><?php } ?>
          </div>
        <?php } ?>
        
      </div>
    </div>
  </div>
<?php } ?>