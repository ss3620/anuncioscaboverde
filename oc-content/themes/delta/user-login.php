<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php'); ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
  <script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('jquery.validate.min.js'); ?>"></script>
</head>

<body id="body-user-login" class="lrf">
  <?php UserForm::js_validation(); ?>
  <?php osc_current_web_theme_path('header.php'); ?>

  <div id="i-forms" class="content login">
    <div class="inside">
      <!-- LOGIN FORM -->
      <div id="login" class="box">
        <div class="wrap">
          <h1><?php _e('Enter immediately', 'delta'); ?></h1>
          <h2><?php _e('Discover a world of personalized content', 'delta'); ?></h2>

          <div class="user_forms login">
            <div class="inner">

              <?php if(function_exists('fl_call_after_install') || function_exists('gc_login_button') || function_exists('fjl_login_button')) { ?>
                <div class="social">
                  <?php if(function_exists('fl_call_after_install')) { ?>
                    <a class="facebook" href="<?php echo facebook_login_link(); ?>" title="<?php echo osc_esc_html(__('Connect with Facebook', 'delta')); ?>">
                      <i class="fab fa-facebook-square"></i>
                      <span><?php _e('Continue with Facebook', 'delta'); ?></span>
                    </a>
                  <?php } ?>

                  <?php if(function_exists('ggl_login_link')) { ?>
                    <a class="google" href="<?php echo ggl_login_link(); ?>" title="<?php echo osc_esc_html(__('Connect with Google', 'delta')); ?>">
                      <i class="fab fa-google"></i>
                      <span><?php _e('Continue with Google', 'delta'); ?></span>
                    </a>
                  <?php } ?>
                  
                  <?php if(function_exists('fjl_login_button')) { ?>
                    <a target="_top" href="javascript:void(0);" class="facebook fl-button fjl-button" onclick="fjlCheckLoginState();" title="<?php echo osc_esc_html(__('Connect with Facebook', 'delta')); ?>">
                      <i class="fab fa-facebook-square"></i>
                      <span><?php _e('Continue with Facebook', 'delta'); ?></span>
                    </a>
                  <?php } ?>
                </div>
              <?php } ?>


              <form action="<?php echo osc_base_url(true); ?>" method="post" >
                <input type="hidden" name="page" value="login" />
                <input type="hidden" name="action" value="login_post" />

                <?php osc_run_hook('user_pre_login_form'); ?>
                
                <fieldset>
                  <div class="row l1">
                    <label for="email"><span><?php _e('E-mail', 'delta'); ?></span></label>
                    <span class="input-box"><?php UserForm::email_login_text(); ?></span>
                  </div>

                  <div class="row p3">
                    <label for="password"><span><?php _e('Password', 'delta'); ?></span></label>
                    <span class="input-box">
                      <?php UserForm::password_login_text(); ?>
                      <a href="#" class="toggle-pass" title="<?php echo osc_esc_html(__('Show/hide password', 'delta')); ?>"><i class="fa fa-eye-slash"></i></a>
                    </span>
                  </div>

                  <div class="login-line">
                    <div class="input-box-check">
                      <?php UserForm::rememberme_login_checkbox();?>
                      <label for="remember"><?php _e('Remember me', 'delta'); ?></label>
                    </div>
                  </div>

                  <div class="user-reg-hook"><?php osc_run_hook('user_login_form'); ?></div>

                  <div class="row fr">
                    <a href="<?php echo osc_recover_user_password_url(); ?>"><?php _e('I forgot my password', 'delta'); ?></a>
                  </div>
                  
                  <?php del_show_recaptcha('login'); ?>

                  <button type="submit" class="complete-login mbBg2"><?php _e('Log in', 'delta');?></button>
                  
                  <div class="row bo">
                    <strong><?php _e('No account yet?', 'delta'); ?></strong>
                    <a href="<?php echo osc_register_account_url(); ?>"><?php _e('Register', 'delta'); ?></a>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php osc_current_web_theme_path('footer.php'); ?>
  
  <script type="text/javascript">
    $(document).ready(function(){
      $('input[name="email"]').attr('placeholder', '<?php echo osc_esc_js(__('your.email@dot.com', 'delta')); ?>').attr('required', true);
      $('input[name="password"]').attr('placeholder', '<?php echo osc_esc_js(__('YourPass123!', 'delta')); ?>').attr('required', true);
    });
  </script>
</body>
</html>