<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
</head>

<?php
  $path = __get('file'); 
  $path = explode('/', $path);

  $plugin = @$path[0];
  $file = str_replace('.php', '', end($path));
?>


<body id="body-user-custom" class="body-ua plugin-<?php echo $plugin; ?> file-<?php echo $file; ?>">
  <?php osc_current_web_theme_path('header.php'); ?>

  <?php echo del_user_menu_top(); ?>

  <div class="inside user_account">
    <div id="main" class="ad_list">
      <div class="inside">
        <div class="inner-box">
          <?php osc_render_file(); ?>
        </div>
      </div>
    </div>
  </div>

  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>