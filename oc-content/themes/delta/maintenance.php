<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
  <head>
    <?php osc_current_web_theme_path('head.php') ; ?>
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, nofollow" />
  </head>

  <body>
    <?php osc_goto_first_locale(); ?>

    <header>
      <div class="inside">
        <div class="left">
          <div class="logo">
            <a href="<?php echo osc_base_url(); ?>"><?php echo del_logo(); ?></a>
          </div>
        </div>
    </header>

    <section class="content loc-error sec-default">
      <div class="inside">
        <div class="maintenance">
          <h1><?php _e('Maintenance', 'delta'); ?></h1>
          <h2><?php _e('OOOPS! We are sorry, page is undergoing maintenance.', 'delta'); ?></h2>
          <h3><?php _e('Please come back in few minutes. Thank you.', 'delta'); ?></h3>
        </div>
      </div>
    </section>

  </body>
</html>