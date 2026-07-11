<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php'); ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
  <script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('jquery.validate.min.js'); ?>"></script>
</head>

<body id="body-user-recover" class="lrf">
  <?php UserForm::js_validation(); ?>
  <?php osc_current_web_theme_path('header.php'); ?>

  <div id="i-forms" class="content recover">
    <div class="inside">

      <!-- RECOVER FORM -->
      <div id="recover" class="box">
        <div class="wrap">
          <h1><?php _e('Recover password', 'delta'); ?></h1>
          <h2><?php _e('We will send you new password to your email immediately', 'delta'); ?></h2>

          <div class="user_forms recover">
            <div class="inner">
              <form action="<?php echo osc_base_url(true) ; ?>" method="post" >
                <input type="hidden" name="page" value="login" />
                <input type="hidden" name="action" value="recover_post" />

                <fieldset>
                  <div class="row">
                    <label for="email"><?php _e('E-mail', 'delta') ; ?></label> 
                    <span class="input-box"><?php UserForm::email_text(); ?></span>
                  </div>
                  
                  <?php del_show_recaptcha('recover_password'); ?>

                  <button type="submit" class="complete-recover mbBg2"><?php _e('Send a new password', 'delta') ; ?></button>
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
      $('input[name="s_email"]').attr('placeholder', '<?php echo osc_esc_js(__('your.email@dot.com', 'delta')); ?>').attr('required', true).prop('type', 'email');
    });
  </script>
</body>
</html>