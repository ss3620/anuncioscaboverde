<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
</head>

<body id="body-user-forgot" class="lrf">
  <?php osc_current_web_theme_path('header.php'); ?>

  <div id="i-forms" class="content forgot">
    <div class="inside">
      <!-- FORGOT PASSWORD FORM -->
      <div id="forgot" class="box">
        <div class="wrap">
          <h1><?php _e('Reset password', 'delta'); ?></h1>
          <h2><?php _e('Enter your new password', 'delta'); ?></h2>

          <div class="user_forms forgot">
            <div class="inner">
              <form action="<?php echo osc_base_url(true); ?>" method="post">
                <input type="hidden" name="page" value="login" />
                <input type="hidden" name="action" value="forgot_post" />
                <input type="hidden" name="userId" value="<?php echo osc_esc_html(Params::getParam('userId')); ?>" />
                <input type="hidden" name="code" value="<?php echo osc_esc_html(Params::getParam('code')); ?>" />
                
                <fieldset>
                  <div class="row">
                    <label for="new_email"><?php _e('New password', 'delta') ; ?></label>
                    <span class="input-box"><input type="password" name="new_password" value="" />
                  </div>
                  
                  <div class="row">
                    <label for="new_email"><?php _e('Repeat password', 'delta') ; ?></label>
                    <span class="input-box"><input type="password" name="new_password2" value="" />
                  </div>

                  <button type="submit" class="mbBg2"><?php _e('Submit', 'delta') ; ?></button>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>