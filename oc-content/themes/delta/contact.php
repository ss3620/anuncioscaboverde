<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
  <script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('jquery.validate.min.js') ; ?>"></script>
</head>

<body id="body-contact" class="lrf">
  <?php osc_current_web_theme_path('header.php') ; ?>

  <div class="content contact">
    <div class="inside">

      <!-- LOGIN FORM -->
      <div id="contact" class="box">
        <div class="wrap">
          <h1><?php _e('Contact us', 'delta'); ?></h1>
          <h2><?php _e('We will reply in 48 hours. Please use our forums and FAQ section before messaging us.', 'delta'); ?></h2>

          <div class="user_forms login">
            <div class="inner">
              <form action="<?php echo osc_base_url(true) ; ?>" method="post" name="contact_form" <?php if(osc_contact_attachment()) { ?>enctype="multipart/form-data"<?php } ?>>
                <input type="hidden" name="page" value="contact" />
                <input type="hidden" name="action" value="contact_post" />

                <ul id="error_list"></ul>

                <div class="row r1">
                  <label for="yourName"><span><?php _e('Your name', 'delta'); ?></span><span class="req">*</span></label> 
                  <div class="input-box">
                    <input type="text" name="yourName" <?php if(osc_is_web_user_logged_in()) { ?>readonly<?php } ?> required value="<?php echo osc_esc_html( osc_logged_user_name() ); ?>" />
                  </div>
                </div>

                <div class="row r2">
                  <label for="yourName"><span><?php _e('Email', 'delta'); ?></span><span class="req">*</span></label> 
                  <div class="input-box">
                    <input type="email" name="yourEmail" <?php if(osc_is_web_user_logged_in()) { ?>readonly<?php } ?> required value="<?php echo osc_logged_user_email();?>" />
                  </div>
                </div>

     
                <div class="row r3">
                  <label for="subject"><span><?php _e('Subject', 'delta'); ?></span><span class="req">*</span></label>
                  <span class="input-box"><?php ContactForm::the_subject(); ?></span>
                </div>

                <div class="row r4">
                  <label for="message"><span><?php _e('Message', 'delta'); ?></span><span class="req">*</span></label>
                  <span class="input-box last"><?php ContactForm::your_message(); ?></span>
                </div>

                <?php if(osc_contact_attachment()) { ?>
                  <div class="attachment att-box">
                    <div class="att-wrap">
                      <label class="att-label">
                        <span class="att-btn"><?php _e('Choose a file', 'delta'); ?></span>
                        <span class="att-text"><?php _e('No file selected', 'delta'); ?></span>
                        <?php ContactForm::your_attachment(); ?>
                      </label>
                    </div>
                  </div>
                <?php } ?>

                <?php osc_run_hook('contact_form'); ?>
                <?php del_show_recaptcha(); ?>

                <button type="submit" class="btn complete-contact mbBg2"><?php _e('Send message', 'delta'); ?></button>

                <?php osc_run_hook('admin_contact_form'); ?>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php ContactForm::js_validation() ; ?>
  <?php osc_current_web_theme_path('footer.php') ; ?>
  
  <script type="text/javascript">
    $(document).ready(function(){
      $('input[name="yourName"]').attr('placeholder', '<?php echo osc_esc_js(__('First name, Last name', 'delta')); ?>');
      $('input[name="yourEmail"]').attr('placeholder', '<?php echo osc_esc_js(__('your.email@dot.com', 'delta')); ?>');
      $('input[name="subject"]').attr('placeholder', '<?php echo osc_esc_js(__('Summarize your question', 'delta')); ?>');
      $('textarea[name="message"]').attr('placeholder', '<?php echo osc_esc_js(__('Your question with all relevant details ...', 'delta')); ?>');
    });
  </script>
</body>
</html>