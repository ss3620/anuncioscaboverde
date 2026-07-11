</section>

<?php osc_run_hook('footer_pre'); ?>

<footer>
  <div class="inside">
    <?php osc_run_hook('footer_top'); ?>
    
    <div class="pub-box-line">
      <div class="pub-box">
        <div class="wrap">
          <div class="info">
            <h3><?php _e('The Leading Online Classifieds Platform', 'delta'); ?></h3>
            <div class="desc"><?php _e('Our mission is to empower individuals across the country by facilitating seamless connections between buyers and sellers. We are committed to providing a trusted marketplace that enables you to achieve your goals through secure and efficient transactions.', 'delta'); ?></div>
          </div>

          <div class="button">
            <a href="<?php echo osc_item_post_url(); ?>"><?php _e('Add a new listing', 'delta'); ?></a>
          </div>
        </div>
      </div>
    </div>
    
    
    <?php if(del_param('site_phone') != '' || del_param('site_email') != '') { ?>
      <div class="line1">
        <?php if(del_param('site_phone') != '') { ?>
          <div class="one">
            <span><?php _e('Free support', 'delta'); ?>:</span><strong><?php echo del_param('site_phone'); ?></strong>
          </div>
        <?php } ?>
        
        <?php if(del_param('site_phone') != '' && del_param('site_email') != '') { ?>
          <div class="one del">|</div>
        <?php } ?>

        <?php if(del_param('site_email') != '') { ?>
          <div class="one">
            <span><?php _e('Email', 'delta'); ?>:</span><strong><?php echo del_param('site_email'); ?></strong>
          </div>
        <?php } ?>        
      </div>
    <?php } ?>
    
    <div class="line2">
      <div class="box b1">
        <h4><?php _e('Trending categories', 'delta'); ?></h4>
        
        <?php 
          osc_goto_first_category();
          $i = 1;
        ?>

        <ul>
          <?php while(osc_has_categories()) { ?>
            <?php if($i <= 10) { ?>
              <li><a href="<?php echo osc_search_url(array('page' => 'search', 'sCategory' => osc_category_id())); ?>"><?php echo osc_category_name();?></a></li>
            <?php } ?>

            <?php $i++; ?>
          <?php } ?>
        </ul>
      </div>

      <div class="box b2">
        <h4><?php _e('Popular locations', 'delta'); ?></h4>
        
        <?php 
          $regions = RegionStats::newInstance()->listRegions('%%%%', '>', 'i_num_items DESC'); 
          $i = 1;
        ?>

        <ul>
          <?php if(is_array($regions) && count($regions) > 0) { ?>
            <?php foreach($regions as $r) { ?>
              <?php if($i <= 10) { ?>
                <li><a href="<?php echo osc_search_url(array('page' => 'search', 'sRegion' => $r['pk_i_id']));?>"><?php echo $r['s_name']; ?></a></li>
                <?php $i++; ?>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
      
      <div class="box b3">
        <h4><?php _e('Help & Support', 'delta'); ?></h4>
        
        <ul>
          <li><a href="<?php echo osc_contact_url(); ?>"><?php _e('Contact us', 'delta'); ?></a></li>
          
          <?php if(osc_is_web_user_logged_in()) { ?>
            <li><a href="<?php echo osc_user_dashboard_url(); ?>"><?php _e('My Account', 'delta'); ?></a></li>
          <?php } else { ?>
            <li><a href="<?php echo del_reg_url('register'); ?>"><?php _e('Sign up', 'delta'); ?></a></li>
          <?php } ?>
          
          <?php if(del_param('footer_link')) { ?>
            <li><a href="https://osclasspoint.com">Osclass Market</a></li>
          <?php } ?> 
      
          <?php 
            $pages = Page::newInstance()->listAll($indelible = 0, $b_link = 1, $locale = null, $start = null, $limit = 10); 
            $i = 1;
          ?>

          <?php if(is_array($pages) && count($pages) > 0) { ?>
            <?php foreach($pages as $p) { ?>
              <?php if($i <= 10) { ?>
                <?php View::newInstance()->_exportVariableToView('page', $p); ?>
                <li><a href="<?php echo osc_static_page_url(); ?>"><?php echo osc_static_page_title();?></a></li>
                <?php $i++; ?>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
    </div>
    

    <div class="line2 o2">
      <div class="box address">
        <h4><?php _e('Get in Touch', 'delta'); ?></h4>
        
        <ul>
          <?php if(del_param('contact_address') != '') { ?>
            <li><?php _e('Address', 'delta'); ?>: <?php echo del_param('contact_address'); ?></li>
          <?php } ?>

          <?php if(del_param('site_email') != '') { ?>
            <li><?php _e('Email', 'delta'); ?>: <?php echo del_param('site_email'); ?></li>
          <?php } ?>

          <?php if(del_param('site_phone') != '') { ?>
            <li><?php _e('Phone', 'delta'); ?>: <?php echo del_param('site_phone'); ?></li>
          <?php } ?>          
        </ul>
      </div>
      
      
      <div class="box lang">
        <h4><?php _e('Select language', 'delta'); ?></h4>
        
        <ul>
          <?php if(osc_count_web_enabled_locales() > 1 || 1==1) { ?>
            <?php 
              $current_locale = mb_get_current_user_locale();
              osc_goto_first_locale(); 
            ?>

            <?php while (osc_has_web_enabled_locales()) { ?>
              <li><a class="lang <?php if (osc_locale_code() == $current_locale['pk_c_code']) { ?>active<?php } ?>" href="<?php echo osc_change_language_url(osc_locale_code()); ?>"><img src="<?php echo del_country_flag_image(strtolower(substr(osc_locale_code(), 3))); ?>" alt="<?php echo osc_esc_html(__('Country flag', 'delta')); ?>" /><span><?php echo osc_locale_name(); ?>&#x200E;</span></a></li>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
      
      
      <div class="box b3 share">
        <h4><?php _e('Social media', 'delta'); ?></h4>
        
        <?php
          osc_reset_resources();

          if(osc_is_ad_page()) {
            $share_url = osc_item_url();
          } else {
            $share_url = osc_base_url();
          }

          $share_url = urlencode($share_url);
        ?>
        
        <ul>
          <?php if(del_get_social_link('whatsapp') !== false) { ?>
            <li class="whatsapp"><a href="<?php echo del_get_social_link('whatsapp'); ?>" data-action="share/whatsapp/share"><i class="fab fa-whatsapp"></i></a></li>
          <?php } ?>
          
          <?php if(del_get_social_link('facebook') !== false) { ?>
            <li class="facebook"><a href="<?php echo del_get_social_link('facebook'); ?>" title="<?php echo osc_esc_html(__('Share us on Facebook', 'delta')); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
          <?php } ?>
          
          <?php if(del_get_social_link('pinterest') !== false) { ?>
            <li class="pinterest"><a href="<?php echo del_get_social_link('pinterest'); ?>" title="<?php echo osc_esc_html(__('Share us on Pinterest', 'delta')); ?>" target="_blank"><i class="fab fa-pinterest-p"></i></a></li>
          <?php } ?>
          
          <?php if(del_get_social_link('twitter') !== false) { ?>
            <li class="twitter"><a href="<?php echo del_get_social_link('twitter'); ?>" title="<?php echo osc_esc_html(__('Tweet us', 'delta')); ?>" target="_blank"><i class="fab fa-twitter"></i></a></li>
          <?php } ?>
          
          <?php if(del_get_social_link('linkedin') !== false) { ?>
            <li class="linkedin"><a href="<?php echo del_get_social_link('linkedin'); ?>" title="<?php echo osc_esc_html(__('Share us on LinkedIn', 'delta')); ?>" target="_blank"><i class="fab fa-linkedin"></i></a></li>
          <?php } ?>

          <?php if(del_get_social_link('instagram') !== false) { ?>
            <li class="instagram"><a href="<?php echo del_get_social_link('instagram'); ?>" title="<?php echo osc_esc_html(__('Share us on Instagram', 'delta')); ?>" target="_blank"><i class="fab fa-instagram"></i></a></li>
          <?php } ?>
          
          <?php if(del_get_social_link('x') !== false) { ?>
            <li class="twitter">
              <a href="<?php echo del_get_social_link('x'); ?>" title="<?php echo osc_esc_html(__('Share us on X (Twitter)', 'delta')); ?>" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15px" height="15px"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
              </a>
            </li>
          <?php } ?>
        
        </ul>
      </div>
    </div>

    <div class="line3">
      <ul>
        <li><a href="<?php echo osc_search_url(array('page' => 'search')); ?>"><?php _e('Search', 'delta'); ?></a></li>
        <li><a href="<?php echo osc_item_post_url(); ?>"><?php _e('Add a new listing', 'delta'); ?></a></li>
        
        <?php if(function_exists('faq_home_link')) { ?>
          <li><a href="<?php echo faq_home_link(); ?>"><?php _e('Frequently Asked Questions', 'delta'); ?></a></li>
        <?php } ?>

        <?php if(function_exists('blg_home_link')) { ?>
          <li><a href="<?php echo blg_home_link(); ?>"><?php _e('Blog', 'delta'); ?></a></li>
        <?php } ?>
        
        <?php if(function_exists('bpr_companies_url')) { ?>
          <li><a href="<?php echo bpr_companies_url(); ?>"><?php _e('Companies', 'delta'); ?></a></li>
        <?php } ?>
        
        <?php if(function_exists('frm_home')) { ?>
          <li><a href="<?php echo frm_home(); ?>"><?php _e('Forums', 'delta'); ?></a></li>
        <?php } ?>
        
        <?php osc_run_hook('footer_links'); ?>
      </ul>
    </div>
    
    <div class="footer-hook"><?php osc_run_hook('footer'); ?></div>
    <div class="footer-widgets"><?php osc_show_widgets('footer'); ?></div>
  </div>
  
  <div class="inside copyright">
    <?php _e('Copyright', 'delta'); ?> &copy; <?php echo date('Y'); ?> <?php echo del_param('website_name'); ?> <?php _e('All rights reserved', 'delta'); ?>.
    <?php if(del_param('footer_link')) { ?><?php _e('Powered by', 'delta'); ?> <a href="https://osclass-classifieds.com">Osclass classifieds script</a>.<?php } ?>
  </div>
</footer>

<?php osc_run_hook('footer_after'); ?>

<div id="mmenu" class="isMobile c5">
  <div class="wrap">
    <a href="<?php echo osc_base_url(); ?>" class="l1 <?php if(osc_is_home_page()) { ?>active<?php } ?>">
      <i class="fas fa-home"></i>
      <span><?php _e('Home', 'delta'); ?></span>
    </a>
    
    <a href="<?php echo osc_search_url(array('page' => 'search')); ?>" class="l2 <?php if(osc_is_search_page()) { ?>active<?php } ?>">
      <i class="fas fa-search"></i>
      <span><?php _e('Search', 'delta'); ?></span>
    </a>
    
    <a href="<?php echo osc_item_post_url(); ?>" class="l3 mmenu-publish <?php if(osc_is_publish_page() || osc_is_edit_page()) { ?>active<?php } ?>">
      <i class="fas fa-plus-circle"></i>
      <span><?php _e('Publish', 'delta'); ?></span>
    </a>
    
    <?php if(function_exists('im_messages')) { ?>
      <a href="<?php echo osc_route_url('im-threads'); ?>" class="l4 <?php if(osc_get_osclass_location() == 'im') { ?>active<?php } ?>">
        <i class="far fa-comment-alt">
          <?php $mes_counter = del_count_messages(osc_logged_user_id()); ?>
          <?php if($mes_counter > 0) { ?>
            <span class="circle"></span>
          <?php } ?>        
        </i>
        <span><?php _e('Messages', 'delta'); ?></span>
      </a>
      
    <?php } else if(function_exists('fi_make_favorite')) { ?>
      <a href="<?php echo osc_route_url('favorite-lists'); ?>" class="l4 <?php if(Params::getParam('route') == 'favorite-lists' || Params::getParam('route') == 'favorite-items-user-favorites' || osc_get_osclass_location() == 'fi') { ?>active<?php } ?>">
        <i class="far fa-heart"></i>
        <span><?php _e('Favorite', 'delta'); ?></span>
      </a>

    <?php } else if(function_exists('svi_save_btn')) { ?>
      <a href="#" class="l4 svi-show-saved">
        <i class="far fa-heart"></i>
        <span><?php _e('Saved', 'delta'); ?></span>
      </a>
      
    <?php } else { ?>
      <a href="<?php echo osc_contact_url(); ?>" class="l4 <?php if(osc_is_contact_page()) { ?>active<?php } ?>">
        <i class="far fa-envelope"></i>
        <span><?php _e('Contact us', 'delta'); ?></span>
      </a>
    <?php } ?>
    
    <a href="<?php echo osc_user_list_items_url(); ?>" class="l5 mmenu-open <?php if(osc_get_osclass_location() == 'user' && osc_get_osclass_section() != 'pub_profile' || osc_is_login_page() || osc_is_register_page()) { ?>active<?php } ?>">
      <i class="far fa-user">
        <span class="circle"></span>
      </i>
      <span><?php _e('Account', 'delta'); ?></span>
    </a>
  </div>
</div>

<?php if(del_banner('body_left') !== false) { ?>
  <div id="body-banner" class="bleft">
    <?php echo del_banner('body_left'); ?>
  </div>
<?php } ?>

<?php if(del_banner('body_right') !== false) { ?>
  <div id="body-banner" class="bright">
    <?php echo del_banner('body_right'); ?>
  </div>
<?php } ?>


<?php if(del_param('scrolltop') == 1) { ?>
  <a id="scroll-to-top"><img src="<?php echo osc_current_web_theme_url('images/scroll-to-top.png'); ?>"/></a>
<?php } ?>


<?php if ( OSC_DEBUG || OSC_DEBUG_DB ) { ?>
  <div id="debug-mode" class="noselect"><?php _e('Debug mode enabled in config.php.', 'delta'); ?></div>
<?php } ?>


<!-- MOBILE BLOCKS -->
<div id="menu-cover" class="mobile-box"></div>


<div id="menu-options" class="mobile-box">
  <div class="head <?php if(osc_is_web_user_logged_in()) { ?>logged<?php } ?>">
    <?php if(!osc_is_web_user_logged_in()) { ?>
      <strong><?php _e('Welcome!', 'delta'); ?></strong>
    <?php } else { ?>
      <div class="image">
        <img src="<?php echo del_profile_picture(osc_logged_user_id(), 'small'); ?>" />
      </div>
      
      <strong><?php echo sprintf(__('Hi %s', 'delta'), osc_logged_user_name()); ?></strong>
    <?php } ?>
    
    <a href="#" class="close">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="30px" height="30px"><path fill="currentColor" d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z" class=""></path></svg>
    </a>
  </div>
  
  <div class="body">
    <a href="<?php echo osc_user_list_items_url(); ?>"><i class="fab fa-stack-overflow"></i> <?php _e('My listings', 'delta'); ?></a>
    <a href="<?php echo osc_user_profile_url(); ?>"><i class="fas fa-user-cog"></i> <?php _e('Profile', 'delta'); ?></a>
    <a href="<?php echo osc_user_alerts_url(); ?>"><i class="fas fa-check-double"></i> <?php _e('Subscriptions', 'delta'); ?></a>

    <?php if(function_exists('fi_make_favorite')) { ?>
      <a href="<?php echo osc_route_url('favorite-lists'); ?>"><i class="far fa-heart"></i> <?php _e('Favorite items', 'delta'); ?></a>
    <?php } ?>

    <?php if(function_exists('svi_save_btn')) { ?>
      <a href="#" class="svi-show-saved"><i class="far fa-heart"></i> <?php _e('Saved items', 'delta'); ?></a>
    <?php } ?>
    
    <?php if(function_exists('im_messages')) { ?>
      <a href="<?php echo osc_route_url('im-threads'); ?>"><i class="far fa-comment-alt"></i> <?php _e('Messages', 'delta'); ?></a>
    <?php } ?>

    <?php if(function_exists('osp_user_sidebar')) { ?>
      <a href="<?php echo osc_route_url('osp-item'); ?>"><i class="fas fa-award"></i> <?php _e('Promotions', 'delta'); ?></a>
    <?php } ?>
    
    <?php if(osc_is_web_user_logged_in()) { ?>
      <a href="<?php echo osc_user_public_profile_url(osc_logged_user_id()); ?>"><i class="far fa-address-card"></i> <?php _e('Public profile', 'delta'); ?></a>
    <?php } ?>
  </div>

  <div class="foot">
    <?php if(!osc_is_web_user_logged_in()) { ?>
      <a href="<?php echo osc_user_login_url(); ?>" class="btn mbBg3"><?php _e('Log in', 'delta'); ?></a>
      <div class="row">
        <span><?php _e('Do not have account yet?', 'delta'); ?></span>
        <a href="<?php echo osc_register_account_url(); ?>"><?php _e('Register', 'delta'); ?></a>
      </div>
    <?php } else { ?>
      <a class="logout btn mbBg3" href="<?php echo osc_user_logout_url(); ?>" class="btn mbBg3"><?php _e('Log out', 'delta'); ?></a>
    <?php } ?>

  </div>
</div>

<div id="menu-user" class="mobile-box">
  <div class="body">
    <?php echo del_user_menu(); ?>
  </div>
</div>

<div id="overlay" class="black"></div>

<?php if(del_is_demo()) { ?>
  <div id="showcase-box" class="isDesktop isTablet">
    <a target="_blank" href="<?php echo osc_admin_render_theme_url('oc-content/themes/delta/admin/configure.php'); ?>"><em><?php _e('Go to', 'delta'); ?></em> <strong><?php _e('OC-Admin', 'delta'); ?></strong></a>
    <a href="#" class="show-banners"><em><?php _e('Show', 'delta'); ?></em> <strong><?php _e('Banners', 'delta'); ?></strong></a>
  </div>
<?php } ?>


<style>
.loc-picker .region-tab:empty:after, .loc-picker .region-tab > .filter:after {content:"<?php echo osc_esc_html(__('Select country first to get list of regions', 'delta')); ?>";}
.loc-picker .city-tab:empty:after, .loc-picker .city-tab > .filter:after {content:"<?php echo osc_esc_html(__('Select region first to get list of regions', 'delta')); ?>";}
.cat-picker .wrapper:after {content:"<?php echo osc_esc_html(__('Select main category first to get list of subcategories', 'delta')); ?>";}
a.fi_img-link.fi-no-image > img {content:url("<?php echo osc_base_url(); ?>/oc-content/themes/delta/images/no-image.png");}
</style>


<?php if(Params::getParam('type') != 'itemviewer') { ?>
<script>
  $(document).ready(function(){

    // JAVASCRIPT AJAX LOADER FOR LOCATIONS 
    var termClicked = false;
    var currentCountry = "<?php echo del_ajax_country(); ?>";
    var currentRegion = "<?php echo del_ajax_region(); ?>";
    var currentCity = "<?php echo del_ajax_city(); ?>";
  

    // Create delay
    var delay = (function(){
      var timer = 0;
      return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
      };
    })();


    $(document).ajaxSend(function(evt, request, settings) {
      var url = settings.url;

      if (url.indexOf("ajaxLoc") >= 0) {
        $(".loc-picker, .location-picker").addClass('searching');
      }
    });

    $(document).ajaxStop(function() {
      $(".loc-picker, .location-picker").removeClass('searching');
    });



    $('body').on('keyup', '.loc-picker .term', function(e) {

      delay(function(){
        var min_length = 1;
        var elem = $(e.target);
        var term = encodeURIComponent(elem.val());

        // If comma entered, remove characters after comma including
        if(term.indexOf(',') > 1) {
          term = term.substr(0, term.indexOf(','));
        }

        // If comma entered, remove characters after - including (because city is shown in format City - Region)
        if(term.indexOf(' - ') > 1) {
          term = term.substr(0, term.indexOf(' - '));
        }

        var block = elem.closest('.loc-picker');
        var shower = elem.closest('.loc-picker').find('.shower');

        shower.html('');

        if(term != '' && term.length >= min_length) {
          // Combined ajax for country, region & city
          $.ajax({
            type: "POST",
            url: baseAjaxUrl + "&ajaxLoc=1&term=" + term,
            dataType: 'json',
            success: function(data) {
              var length = data.length;
              var result = '';
              var result_first = '';
              var countCountry = 0;
              var countRegion = 0;
              var countCity = 0;
              var countCountryAll = <?php echo del_count_countries(); ?>;

              if(shower.find('.service.min-char').length <= 0) {
                for(key in data) {

                  // Prepare location IDs
                  var id = '';
                  var country_code = '';
                  if( data[key].country_code ) {
                    country_code = data[key].country_code;
                    id = country_code;
                  }

                  var region_id = '';
                  if( data[key].region_id ) {
                    region_id = data[key].region_id;
                    id = region_id;
                  }

                  var city_id = '';
                  if( data[key].city_id ) {
                    city_id = data[key].city_id;
                    id = city_id;
                  }

                  // Count cities, regions & countries
                  if (data[key].type == 'city') {
                    countCity = countCity + 1;
                  } else if (data[key].type == 'region') {
                    countRegion = countRegion + 1;
                  } else if (data[key].type == 'country') {
                    countCountry = countCountry + 1;
                  }


                  // Find currently selected element
                  var selectedClass = '';
                  if( 
                    data[key].type == 'country' && parseInt(currentCountry) == parseInt(data[key].country_code) 
                    || data[key].type == 'region' && parseInt(currentRegion) == parseInt(data[key].region_id) 
                    || data[key].type == 'city' && parseInt(currentCity) == parseInt(data[key].city_id) 
                  ) { 
                    selectedClass = ' selected'; 
                  }


                  // For cities, get region name
                  var nameTop = data[key].name_top;

                  if(nameTop != '' && nameTop != 'null' && nameTop !== null && nameTop !== undefined) {
                    nameTop = nameTop.replace(/'/g, '');
                  } else {
                    nameTop = '';
                  }

                  if(data[key].type != 'city_more') {

                    // When classic city, region or country in loop and same does not already exists
                    if(shower.find('div[data-code="' + data[key].type + data[key].id + '"]').length <= 0) {
                      result += '<div class="option ' + data[key].type + selectedClass + '" data-country="' + country_code + '" data-region="' + region_id + '" data-city="' + city_id + '" data-code="' + data[key].type + id + '" id="' + id + '" title="' + nameTop + '">';
                      result += '<strong>' + data[key].name + '</strong>';
                      
                      if(data[key].type == 'city' && nameTop != '') {
                        result += '<span>' + nameTop + '</span>';
                      }
                      
                      if(data[key].type == 'region' && nameTop != '' && countCountryAll > 1) {
                        result += '<span>' + nameTop + '</span>';
                      }
                      
                      result += '</div>';
                    }
                  }
                }


                // No city, region or country found
                if( countCity == 0 && countRegion == 0 && countCountry == 0 && shower.find('.empty-loc').length <= 0 && shower.find('.service.min-char').length <= 0) {
                  shower.find('.option').remove();
                  result_first += '<div class="option service empty-pick empty-loc"><?php echo osc_esc_js(__('No location match to your criteria', 'delta')); ?></div>';
                }
              }

              shower.html(result_first + result);
            }
          });

        } else {
          // Term is not length enough, show default content
          //shower.html('<div class="option service min-char"><?php echo osc_esc_js(__('Enter at least', 'delta')); ?> ' + (min_length - term.length) + ' <?php echo osc_esc_js(__('more letter(s)', 'delta')); ?></div>');

          shower.html('<?php echo osc_esc_js(del_def_location()); ?>');
        }
      }, 500 );
    });
  });
</script>
<?php } ?>