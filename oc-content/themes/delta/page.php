<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
  <head>
    <?php osc_current_web_theme_path('head.php'); ?>
    
    <?php if(osc_static_page_indexable() == 1) { ?>
      <meta name="robots" content="index, follow" />
      <meta name="googlebot" content="index, follow" />
    <?php } else { ?>
      <meta name="robots" content="noindex, nofollow" />
      <meta name="googlebot" content="noindex, nofollow" />
    <?php } ?>
  </head>
  <body id="body-page">
    <?php osc_current_web_theme_path('header.php') ; ?>
    <?php osc_reset_static_pages(); ?>

    <div class="page">
      <div class="inside round5">
        <div class="left">
          <h1 class="main-hdr"><?php echo osc_static_page_title(); ?></h1>
          <div class="page-body"><?php echo osc_static_page_text(); ?></div>

          <div class="bottom"><?php _e('Do you have more questions?', 'delta'); ?> <a href="<?php echo osc_contact_url(); ?>"><?php _e('Contact us', 'delta'); ?></a></div>
        </div>

        <div class="right">

          <h3><?php _e('Other articles', 'delta'); ?></h3>
          <?php 
            $current_id = osc_static_page_id();
            $i = 0;

            $pages = Page::newInstance()->listAll($indelible = 0, $b_link = null, $locale = null, $start = null, $limit = 10);
          ?>

          <?php foreach($pages as $p) { ?>
            <?php View::newInstance()->_exportVariableToView('page', $p); ?>
            <?php if($i < 10 && $current_id <> osc_static_page_id()) { ?>
              <a href="<?php echo osc_static_page_url(); ?>"><?php echo osc_static_page_title(); ?></a>
            <?php } ?>
 
            <?php $i++; ?>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php osc_current_web_theme_path('footer.php') ; ?>
  </body>
</html>