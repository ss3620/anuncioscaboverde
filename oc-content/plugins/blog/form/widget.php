<?php
  if(!isset($type) || $type == '') {
    $type = 'blg-tp-' . (blg_param('widget_type') <> '' ? blg_param('widget_type') : 'grid');
  } else {
    $type = 'blg-tp-' . $type;
  }

  $blogs = ModelBLG::newInstance()->getWidgetBlogs($type);
?>

<div class="blg-widget-outer <?php echo $type; ?>">
  <div class="blg-widget-inner">
    <div id="blg-body" class="blg-theme-<?php echo osc_current_web_theme(); ?>">
      <div id="blg-main" class="blg-widget">
        <div class="blg-widget-result">
          <a class="h2" href="<?php echo osc_route_url('blg-home'); ?>"><?php _e('Latest on blog', 'blog'); ?></a>

          <div class="blg-wg-in">
            <?php 
              if(count($blogs) > 0) {
                $i = 1;

                foreach($blogs as $blog) {
                  $limit = 210;
                  $class = 'blg-i' . $i;
                  $class_alt = 'blg-t-' . ($i == 1 ? 'big' : 'small');

                  echo '<div class="blg-rw ' . $class . ' ' . $class_alt . '">';
                  blg_article($blog, '', $limit);
                  echo '</div>';

                  $i++;
                }
              } else { 
                echo '<div class="blg-row blg-empty">' . __('There are no articles yet', 'blog') . '</div>';
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>