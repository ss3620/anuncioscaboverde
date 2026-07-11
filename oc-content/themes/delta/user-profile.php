<?php
  $locales = __get('locales');
  $user = osc_user();

  if(osc_profile_img_users_enabled()) {
    osc_enqueue_script('cropper');
    osc_enqueue_style('cropper', osc_assets_url('js/cropper/cropper.min.css'));
  }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
</head>

<body id="body-user-profile" class="body-ua">
  <?php osc_current_web_theme_path('header.php') ; ?>

  <?php echo del_user_menu_top(); ?>

  <div class="inside user_account">
    <div class="usr-menu profile-menu">
      <div data-id="profile" class="active">
         <strong><?php _e('Profile', 'delta'); ?></strong>
         <span><?php _e('Your personal data', 'delta'); ?></span>
      </div>

      <div data-id="email">
         <strong><?php _e('Change email', 'delta'); ?></strong>
         <span><?php _e('If you wish to use different', 'delta'); ?></span>
      </div>

      <div data-id="password">
         <strong><?php _e('Change password', 'delta'); ?></strong>
         <span><?php _e('To secure your data', 'delta'); ?></span>
      </div>
    </div>

    <div id="main" class="profile">
      <div class="inside">

        <div class="box" data-id="profile">
          <?php osc_run_hook('user_profile_top'); ?>
          
          <h1><?php _e('Profile', 'delta'); ?></h1>
          <h2><?php _e('Enter details about you, your location and company', 'delta'); ?></h2>

          <form action="<?php echo osc_base_url(true); ?>" method="post">
            <input type="hidden" name="page" value="user" />
            <input type="hidden" name="action" value="profile_post" />

            <div id="left-user" class="line">
              <?php if(osc_profile_img_users_enabled()) { ?>
                <div class="control-group">
                  <label class="control-label" for="name"><?php _e('Picture', 'delta'); ?></label>
                  <div class="controls">
                    <div class="user-img">
                      <div class="img-preview">
                        <img src="<?php echo osc_user_profile_img_url(osc_logged_user_id()); ?>" alt="<?php echo osc_esc_html(osc_logged_user_name()); ?>"/>
                      </div> 
                    </div> 

                    <div class="user-img-button">
                      <?php UserForm::upload_profile_img(); ?>
                    </div>
                  </div>
                </div>
              <?php } ?>
      
              <div class="row">
                <label for="name"><span><?php _e('Name', 'delta'); ?></span><span class="req">*</span></label>
                <div class="input-box"><?php UserForm::name_text(osc_user()); ?></div>

                <?php if(function_exists('profile_picture_show') && !osc_profile_img_users_enabled()) { ?>
                  <a href="#" class="update-avatar"><?php _e('Update avatar', 'delta'); ?></a>
                  <?php echo profile_picture_show(); ?>
                <?php } ?>
              </div>



              <div class="row">
                <label for="email"><span><?php _e('E-mail', 'delta'); ?></span></label>
                <span class="update current_email">
                  <span><?php echo osc_user_email(); ?></span>
                </span>
              </div>

              <div class="row">
                <label for="phoneMobile"><span><?php _e('Mobile phone', 'delta'); ?></span><span class="req">*</span></label>
                <div class="input-box"><?php UserForm::mobile_text(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="phoneLand"><?php _e('Land Phone', 'delta'); ?></label>
                <div class="input-box"><?php UserForm::phone_land_text(osc_user()); ?></div>
              </div>                        

              <div class="row">
                <label for="info"><?php _e('About you', 'delta'); ?></label>
                <?php UserForm::multilanguage_info($locales, osc_user()); ?>
              </div>
            </div>

            <div id="right-user" class="line">
              <?php osc_run_hook('user_profile_sidebar'); ?>
              
              <div class="row">
                <input type="hidden" name="countryId" id="countryId" class="sCountry" value="<?php echo $user['fk_c_country_code']; ?>"/>
                <input type="hidden" name="regionId" id="regionId" class="sRegion" value="<?php echo $user['fk_i_region_id']; ?>"/>
                <input type="hidden" name="cityId" id="cityId" class="sCity" value="<?php echo $user['fk_i_city_id']; ?>"/>

                <label for="term"><?php _e('Location', 'delta'); ?></label>

                <div id="location-picker" class="loc-picker ctr-<?php echo (del_count_countries() == 1 ? 'one' : 'more'); ?>">
                  <input type="text" name="term" id="term" class="term" placeholder="<?php _e('Location', 'delta'); ?>" value="<?php echo del_get_term(Params::getParam('term'), del_ajax_country(), del_ajax_region(), del_ajax_city()); ?>" autocomplete="off" data-alt-placeholder="<?php echo osc_esc_html(__('Type to filter results', 'delta')); ?>"/>
                  <i class="fa fa-angle-down"></i>

                  <div class="shower-wrap">
                    <div class="shower" id="shower">
                      <?php echo del_def_location(); ?>
                    </div>
                  </div>

                  <div class="loader"></div>
                </div>
                
                <div class="input-help"><?php _e('Enter a location name to filter results', 'delta'); ?></div>
              </div>

              <div class="row">
                <label for="cityArea"><?php _e('City Area', 'delta'); ?></label>
                <div class="input-box"><?php UserForm::city_area_text(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="address"><?php _e('Street', 'delta'); ?></label>
                <div class="input-box"><?php UserForm::address_text(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="address"><?php _e('ZIP', 'delta'); ?></label>
                <div class="input-box"><?php UserForm::zip_text(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="user_type"><?php _e('User type', 'delta'); ?></label>
                <div class="input-box"><?php UserForm::is_company_select(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="webSite"><?php _e('Website', 'delta'); ?></label>
                <div class="input-box"><?php UserForm::website_text(osc_user()); ?></div>
              </div>

              <?php osc_run_hook('user_form'); ?>

              <div class="row user-buttons">
                <button type="submit" class="btn btn-primary mbBg"><?php _e('Save changes', 'delta'); ?></button>
              </div>
            </div>
          </form>
        </div>


        <!-- CHANGE EMAIL FORM -->
        <div class="box second" data-id="email" style="display:none">
          <h1><?php _e('Change email', 'delta'); ?></h1>
          <h2><?php _e('Change your email address to different one', 'delta'); ?></h2>

          <form action="<?php echo osc_base_url(true); ?>" method="post" id="user_email_change" class="user-change">
            <?php if(!del_is_demo()) { ?>
            <input type="hidden" name="page" value="user" />
            <input type="hidden" name="action" value="change_email_post" />
            <?php } ?>

            <div class="row">
              <label for="email"><?php _e('Current e-mail', 'delta'); ?></label>
              <span class="bold current_email"><?php echo osc_logged_user_email(); ?></span>
            </div>

            <div class="row">
              <label for="new_email"><?php _e('New e-mail', 'delta'); ?> *</label>
              <div class="input-box"><input type="text" name="new_email" id="new_email" value="" /></div>
            </div>

            <div class="row user-buttons">
              <?php if(del_is_demo()) { ?>
                <a class="btn mbBg disabled" onclick="return false;" title="<?php echo osc_esc_html(__('You cannot do this on demo site', 'delta')); ?>"><?php _e('Change email', 'delta'); ?></a>
              <?php } else { ?>
                <button type="submit" class="btn mbBg"><?php _e('Change email', 'delta'); ?></button>
              <?php } ?>
            </div>
          </form>
        </div>


        <!-- CHANGE PASSWORD FORM -->
        <div class="box third" data-id="password" style="display:none">
          <h1><?php _e('Change password', 'delta'); ?></h1>
          <h2><?php _e('Change your email password to different one', 'delta'); ?></h2>
          
          <form action="<?php echo osc_base_url(true); ?>" method="post" id="user_password_change" class="user-change">
            <?php if(!del_is_demo()) { ?>
            <input type="hidden" name="page" value="user" />
            <input type="hidden" name="action" value="change_password_post" />
            <?php } ?>

            <div class="row">
              <label for="password"><?php _e('Current password', 'delta'); ?> *</label>
              <div class="input-box"><input type="password" name="password" id="password" value="" /></div>
            </div>

            <div class="row">
              <label for="new_password"><?php _e('New password', 'delta'); ?> *</label>
              <div class="input-box"><input type="password" name="new_password" id="new_password" value="" /></div>
            </div>

            <div class="row">
              <label for="new_password2"><?php _e('Repeat new password', 'delta'); ?> *</label>
              <div class="input-box"><input type="password" name="new_password2" id="new_password2" value="" /></div>
            </div>


            <div class="row user-buttons">
              <?php if(del_is_demo()) { ?>
                <a class="btn mbBg disabled" onclick="return false;" title="<?php echo osc_esc_html(__('You cannot do this on demo site', 'delta')); ?>"><?php _e('Change password', 'delta'); ?></a>
              <?php } else { ?>
                <button type="submit" class="btn mbBg"><?php _e('Change password', 'delta'); ?></button>
              <?php } ?>
            </div>
          </form>
        </div>
      </div>

      <?php if(!del_is_demo()) { ?>
        <a class="btn-remove-account btn" href="<?php echo osc_base_url(true).'?page=user&action=delete&id='.osc_user_id().'&secret='.$user['s_secret']; ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to delete your account? This action cannot be undone', 'delta')); ?>?')"><span><?php _e('Delete account', 'delta'); ?></span></a>
      <?php } ?>
    </div>
  </div>

  <?php 
    $locale = osc_get_current_user_locale();
    $locale_code = $locale['pk_c_code'];
    $locale_name = $locale['s_name'];
  ?>

  <script>
    $(document).ready(function() {

      // Unify selected locale  
      function delUserLocCheck() {
        if($('.tabbernav li').length) {
          var localeText = "<?php echo trim(osc_esc_html($locale_name)); ?>";

          $('.tabbernav > li > a:contains("' + localeText+ '")').click();

          clearInterval(checkTimer);
          return;
        }
      }

      var checkTimer = setInterval(delUserLocCheck, 150);

    });
  </script>


  <?php 
    if(function_exists('profile_picture_upload') && !osc_profile_img_users_enabled()) { 
      profile_picture_upload(); 
    } 
  ?>


  <?php osc_current_web_theme_path('footer.php'); ?>
</body>
</html>