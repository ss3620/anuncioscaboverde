<?php
  $user = osc_user();

  $location_array = array(osc_user_address(), osc_user_zip(), osc_user_city_area(), osc_user_city(), osc_user_region(), osc_user_country());
  $location_array = array_filter($location_array);
  $location = implode(', ', $location_array);

  $is_company = false;
  
  $user_item_count = $user['i_items'];

  if($user['b_company'] == 1) {
    $is_company = true;
  }

  $mobile_found_land = true;
  $mobile_found_mobile = true;
  
  $mobile_mobile = $user['s_phone_mobile'];
  $mobile_land = $user['s_phone_land'];

  $mobile_login_required_land = false;
  $mobile_login_required_mobile = false;

  if(osc_get_preference('reg_user_can_see_phone', 'osclass') == 1 && !osc_is_web_user_logged_in() && strlen(trim($mobile_mobile)) >= 4) {
    $mobile_mobile = __('Login to see phone number', 'delta');
    $mobile_found_mobile = true;
    $mobile_login_required_mobile = true;
  } else if(trim($mobile_mobile) == '' || strlen(trim($mobile_mobile)) < 4) { 
    $mobile_mobile = __('No phone number', 'delta');
    $mobile_found_mobile = false;
  } 
  
  if(osc_get_preference('reg_user_can_see_phone', 'osclass') == 1 && !osc_is_web_user_logged_in() && strlen(trim($mobile_land)) >= 4) {
    $mobile_land = __('Login to see phone number', 'delta');
    $mobile_found_land = true;
    $mobile_login_required_land = true;
  } else if(trim($mobile_land) == '' || strlen(trim($mobile_land)) < 4) { 
    $mobile_land = __('No phone number', 'delta');
    $mobile_found_land = false;
  } 

  // GET REGISTRATION DATE AND TYPE
  $reg_type = '';
  $last_online = '';

  if($user && $user['dt_reg_date'] <> '') { 
    $reg_type = sprintf(__('Registered for %s', 'delta'), del_smart_date2($user['dt_reg_date']));
  } else if ($user) { 
    $reg_type = __('Registered user', 'delta');
  } else {
    $reg_type = __('Unregistered user', 'delta');
  }

  if($user) {
    $last_online = sprintf(__('Last online %s', 'delta'), del_smart_date($user['dt_access_date']));
  }

  $user_about = nl2br(strip_tags(osc_user_info()));
  $contact_name = (osc_user_name() <> '' ? osc_user_name() : __('Anonymous', 'delta'));

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
  <script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('jquery.validate.min.js') ; ?>"></script>
</head>

<body id="body-user-public-profile">
  <?php View::newInstance()->_exportVariableToView('user', $user); ?>
  <?php osc_current_web_theme_path('header.php') ; ?>
  <?php View::newInstance()->_exportVariableToView('user', $user); ?>

  <div class="inside userpb" id="listing">

    <!-- LEFT BLOCK -->

    <div class="side">
      <?php osc_run_hook('user_public_profile_sidebar_top'); ?>
      
      <div class="wbox" id="seller">
        <h2><?php _e('Seller details', 'delta'); ?></h2>
        
        <div class="user-box">
          <div class="wrap">
            <div class="user-img">
              <img src="<?php echo del_profile_picture(osc_user_id(), 'small'); ?>" alt="<?php echo osc_esc_html($contact_name); ?>" />
            </div>
            
            <strong class="name"><?php echo $contact_name; ?></strong>
            
            <div class="counts">
              <?php echo sprintf(__('%d active listings', 'delta'), $user_item_count); ?>
            </div>

            <div class="company">
              <?php echo ($is_company ? __('Professional seller', 'delta') : __('Non-Professional seller', 'delta')); ?>
            </div>
            
            <div class="times">
              <?php echo implode('<br/>', array_filter(array($reg_type, $last_online))); ?>
            </div>

            <?php if(del_chat_button(osc_user_id())) { echo del_chat_button(osc_user_id()); } ?>
            
            <a href="#contact" class="contact btn mbBg2">
              <i class="fas fa-envelope"></i>
              <span><?php _e('Contact', 'delta'); ?></span>
            </a>
            
            <?php if(osc_user_id() > 0) { ?>
              <a href="<?php echo osc_search_url(array('page' => 'search', 'userId' => osc_user_id())); ?>" class="other btn">
                <?php _e('All items', 'delta'); ?>
              </a>
            <?php } ?>
          </div>
        </div>

        
        <div class="bottom-menu">
        
          <?php if($mobile_found_mobile) { ?>
            <div class="elem">
              <i class="fas fa-mobile"></i>

              <?php if($mobile_login_required_mobile) { ?>
                <a href="<?php echo osc_user_login_url(); ?>" class="mobile login-required" data-phone="" title="<?php echo osc_esc_html(__('Login to show number', 'delta')); ?>"><?php echo osc_esc_html(__('Login to show number', 'delta')); ?></a>
              <?php } else { ?>
                <a href="#" class="mobile" data-phone="<?php echo $mobile_mobile; ?>" title="<?php echo osc_esc_html(__('Click to show number', 'delta')); ?>">
                  <span><?php echo substr($mobile_mobile, 0, strlen($mobile_mobile) - 4) . 'xxxx'; ?></span>
                </a>
              <?php } ?>
            </div>
          <?php } ?>
          
          <?php if($mobile_found_land) { ?>
            <div class="elem">
              <i class="fas fa-phone"></i>

              <?php if($mobile_login_required_land) { ?>
                <a href="<?php echo osc_user_login_url(); ?>" class="mobile login-required" data-phone="" title="<?php echo osc_esc_html(__('Login to show number', 'delta')); ?>"><?php echo osc_esc_html(__('Login to show number', 'delta')); ?></a>
              <?php } else { ?>
                <a href="#" class="mobile" data-phone="<?php echo $mobile_land; ?>" title="<?php echo osc_esc_html(__('Click to show number', 'delta')); ?>">
                  <span><?php echo substr($mobile_land, 0, strlen($mobile_land) - 4) . 'xxxx'; ?></span>
                </a>
              <?php } ?>
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


      <?php echo del_banner('public_profile_sidebar_middle'); ?>


      <div class="wbox location">
        <h2><?php _e('Location', 'delta'); ?></h2>
        
        <?php if($location <> '') { ?>
          <div class="row">
            <span><?php echo $location; ?></span>
          </div>
          
          <?php if(osc_user_latitude() <> 0 && osc_user_longitude() <> 0) { ?>
            <div class="row latlong"><?php echo osc_user_latitude(); ?>, <?php echo osc_user_longitude(); ?></div> 
          <?php } ?>
          
          <div class="row">
            <a class="dir" target="_blank" href="https://www.google.com/maps?daddr=<?php echo urlencode($location); ?>">
              <i class="fas fa-map-marked-alt"></i>
              <?php _e('Get directions', 'delta'); ?>
            </a>
          </div>
        <?php } else { ?>
          <div class="row unknw"><?php _e('Unknown location', 'delta'); ?></div>
        <?php } ?>
        
        <div class="loc-hook">
          <?php osc_run_hook('user_public_profile_location'); ?>
        </div>
      </div>


      
      <!-- CONTACT BLOCK -->
      <div class="wbox upb" id="contact">
        <h2><?php echo sprintf(__('Contact %s', 'delta'), $contact_name); ?></h2>
        
        <div class="row">
          <form action="<?php echo osc_base_url(true) ; ?>" method="post" name="contact_form" id="contact_form">
            <input type="hidden" name="action" value="contact_post" />
            <input type="hidden" name="page" value="user" />
            <input type="hidden" name="id" value="<?php echo osc_user_id() ; ?>" />

            <?php ContactForm::js_validation(); ?>
            <ul id="error_list"></ul>

            <?php if(osc_user_id() == osc_logged_user_id() && osc_is_web_user_logged_in()) { ?>
              <div class="problem"><?php _e('This is your own profile!', 'delta'); ?></div>
            <?php } else if( osc_reg_user_can_contact() && !osc_is_web_user_logged_in() ) { ?>
              <div class="problem">
                <?php _e('You must log in or register a new account in order to contact the advertiser.', 'delta') ; ?>
              </div>
            <?php } else { ?> 
              <div class="lb">
                <div id="item-card">
                  <div class="img">
                    <img src="<?php echo del_profile_picture(osc_user_id(), 'small'); ?>" alt="<?php echo osc_esc_html($contact_name); ?>" />
                  </div>
                  
                  <div class="dsc">
                    <strong><?php echo $contact_name; ?></strong>
                    <div><?php echo ($last_online <> '' ? $last_online : $reg_type); ?></div>
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
                
                <?php del_show_recaptcha(); ?>

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
      </div>
      
      
      <div class="wbox safe-block">
        <h2><?php _e('Stay safe!', 'delta'); ?></h2>
        <div class="txt">
          <svg xmlns="http://www.w3.org/2000/svg" height="48" version="1.1" viewBox="-38 0 512 512.00142" width="48"> <g id="surface1"> <path d="M 217.996094 158.457031 C 164.203125 158.457031 120.441406 202.21875 120.441406 256.007812 C 120.441406 309.800781 164.203125 353.5625 217.996094 353.5625 C 271.785156 353.5625 315.546875 309.800781 315.546875 256.007812 C 315.546875 202.21875 271.785156 158.457031 217.996094 158.457031 Z M 275.914062 237.636719 L 206.027344 307.523438 C 203.09375 310.457031 199.246094 311.925781 195.402344 311.925781 C 191.558594 311.925781 187.714844 310.460938 184.78125 307.523438 L 158.074219 280.816406 C 152.207031 274.953125 152.207031 265.441406 158.074219 259.574219 C 163.9375 253.707031 173.449219 253.707031 179.316406 259.574219 L 195.402344 275.660156 L 254.671875 216.394531 C 260.535156 210.527344 270.046875 210.527344 275.914062 216.394531 C 281.78125 222.257812 281.78125 231.769531 275.914062 237.636719 Z M 275.914062 237.636719 " style=" stroke:none;fill-rule:nonzero;fill:<?php echo del_param('color'); ?>;fill-opacity:1;" /> <path d="M 435.488281 138.917969 L 435.472656 138.519531 C 435.25 133.601562 435.101562 128.398438 435.011719 122.609375 C 434.59375 94.378906 412.152344 71.027344 383.917969 69.449219 C 325.050781 66.164062 279.511719 46.96875 240.601562 9.042969 L 240.269531 8.726562 C 227.578125 -2.910156 208.433594 -2.910156 195.738281 8.726562 L 195.40625 9.042969 C 156.496094 46.96875 110.957031 66.164062 52.089844 69.453125 C 23.859375 71.027344 1.414062 94.378906 0.996094 122.613281 C 0.910156 128.363281 0.757812 133.566406 0.535156 138.519531 L 0.511719 139.445312 C -0.632812 199.472656 -2.054688 274.179688 22.9375 341.988281 C 36.679688 379.277344 57.492188 411.691406 84.792969 438.335938 C 115.886719 468.679688 156.613281 492.769531 205.839844 509.933594 C 207.441406 510.492188 209.105469 510.945312 210.800781 511.285156 C 213.191406 511.761719 215.597656 512 218.003906 512 C 220.410156 512 222.820312 511.761719 225.207031 511.285156 C 226.902344 510.945312 228.578125 510.488281 230.1875 509.925781 C 279.355469 492.730469 320.039062 468.628906 351.105469 438.289062 C 378.394531 411.636719 399.207031 379.214844 412.960938 341.917969 C 438.046875 273.90625 436.628906 199.058594 435.488281 138.917969 Z M 217.996094 383.605469 C 147.636719 383.605469 90.398438 326.367188 90.398438 256.007812 C 90.398438 185.648438 147.636719 128.410156 217.996094 128.410156 C 288.351562 128.410156 345.59375 185.648438 345.59375 256.007812 C 345.59375 326.367188 288.351562 383.605469 217.996094 383.605469 Z M 217.996094 383.605469 " style=" stroke:none;fill-rule:nonzero;fill:<?php echo del_param('color'); ?>;fill-opacity:1;" /> </g> </svg>
          <?php _e('Never pay down a deposit in a bank account until you have met the seller, seen signed a purchase agreement. No serious private advertisers ask for a down payment before you meet. Receiving an email with an in-scanned ID does not mean that you have identified the sender. You do this on the spot, when you sign a purchase agreement.', 'delta'); ?>
        </div>
      </div>
      

      <div class="wbox data upb" id="share">
        <h2><?php _e('Share this profile', 'delta'); ?></h2>

        <div class="item-share">
          <?php osc_reset_resources(); ?>
          <a class="facebook" title="<?php echo osc_esc_html(__('Share on Facebook', 'delta')); ?>" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(osc_user_public_profile_url(osc_user_id())); ?>"><i class="fa fa-facebook"></i> <?php _e('Facebook', 'delta'); ?></a> 
          <a class="twitter" title="<?php echo osc_esc_html(__('Share on Twitter', 'delta')); ?>" target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo urlencode(meta_title()); ?>&url=<?php echo urlencode(osc_user_public_profile_url(osc_user_id())); ?>"><i class="fa fa-twitter"></i> <?php _e('Twitter', 'delta'); ?></a> 
          <a class="pinterest" title="<?php echo osc_esc_html(__('Share on Pinterest', 'delta')); ?>" target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(osc_user_public_profile_url(osc_user_id())); ?>&media=<?php echo del_profile_picture(osc_user_id(), 'large'); ?>&description=<?php echo htmlspecialchars(meta_title()); ?>"><i class="fa fa-pinterest"></i> <?php _e('Pinterest', 'delta'); ?></a> 
        </div>
      </div>

      <?php echo del_banner('public_profile_sidebar_bottom'); ?>
      <?php osc_run_hook('user_public_profile_sidebar_bottom'); ?>
    </div>



    <!-- LISTINGS OF SELLER -->
    <div id="public-items" class="">
      <?php osc_run_hook('user_public_profile_items_top'); ?>
      <?php echo del_banner('public_profile_top'); ?>

      <h1><?php echo sprintf(__('%s\'s listings', 'delta'), $contact_name); ?></h1>

      <?php del_related_ads('user-pb-premium', 'compact', 12); ?>

      <?php if(osc_version() >= 830) { ?>
        <form name="user-public-profile-search" action="<?php echo osc_base_url(true); ?>" method="get" class="user-public-profile-search-form nocsrf">
          <input type="hidden" name="page" value="user"/>
          <input type="hidden" name="action" value="pub_profile"/>
          <input type="hidden" name="id" value="<?php echo osc_esc_html($user['pk_i_id']); ?>"/>

          <?php osc_run_hook('user_public_profile_search_form_top'); ?>
          
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

          <div class="control-group">
            <label class="control-label" for="sCity"><?php _e('City', 'delta'); ?></label>
            
            <div class="controls">
              <?php UserForm::search_city_select(); ?>
            </div>
          </div>
          
          <?php osc_run_hook('user_public_profile_search_form_bottom'); ?>
          
          <div class="actions">
            <button type="submit" class="btn btn-primary"><?php _e('Apply', 'delta'); ?></button>
          </div>
        </form>
      <?php } ?>

      <?php if(osc_count_items() > 0) { ?>
        <div class="block products list">
          <div class="wrap">
            <?php $c = 1; ?>
            <?php while( osc_has_items() ) { ?>
              <?php del_draw_item($c); ?>
        
              <?php $c++; ?>
            <?php } ?>
          </div>
        </div>
      <?php } else { ?>
        <div class="ua-items-empty"><img src="<?php echo osc_current_web_theme_url('images/ua-empty.jpg'); ?>"/> <span><?php _e('This seller has no active listings', 'delta'); ?></span></div>
      <?php } ?>

      <?php echo del_banner('public_profile_bottom'); ?>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function(){
      $('input[name="yourName"]').attr('placeholder', '<?php echo osc_esc_js(__('First name, Last name', 'delta')); ?>');
      $('input[name="yourEmail"]').attr('placeholder', '<?php echo osc_esc_js(__('your.email@dot.com', 'delta')); ?>');
      $('input[name="phoneNumber"]').attr('placeholder', '<?php echo osc_esc_js(__('+XXX XXX XXX', 'delta')); ?>');
      $('#contact textarea[name="message"]').val('<?php echo osc_esc_js(sprintf(__('Dear %s,<br/><br/>I am interested in your offer, <br/>Please contact me back.<br/><br/>With best regards,<br/>%s', 'delta'), $contact_name, osc_logged_user_name())); ?>');

      // SHOW PHONE NUMBER
      $('body').on('click', '.mobile', function(e) {
        if($(this).attr('href') == '#') {
          e.preventDefault()

          var phoneNumber = $(this).attr('data-phone');
          $(this).text(phoneNumber);
          $(this).attr('href', 'tel:' + phoneNumber);
          $(this).attr('title', '<?php echo osc_esc_js(__('Click to call', 'delta')); ?>');
        }        
      });
    });
  </script>

  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>