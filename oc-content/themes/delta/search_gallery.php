<?php
  $def_view = del_param('def_view') == 0 ? 'grid' : 'list';
  $show = Params::getParam('sShowAs') == '' ? $def_view : Params::getParam('sShowAs');
  $show = ($show == 'gallery' ? 'grid' : $show);
?>

<div class="search-items-wrap">
  <div class="block">
    <div class="wrap">
      <?php osc_get_premiums(del_param('premium_search_count')); ?>

      <?php if(osc_count_premiums() > 0 && del_param('premium_search') == 1) { ?>

        <div class="premiums-block <?php echo (osc_count_premiums() % 2 == 1 ? 'odd' : 'even'); ?> products grid">
          <h3 class="premium-blck"><?php echo __('Premium listings', 'delta'); ?></h3>

          <div class="relative">
            <div class="nice-scroll-left"><span class="mover"><i class="fas fa-angle-left"></i></span></div>
            <div class="nice-scroll-right"><span class="mover"><i class="fas fa-angle-right"></i></span></div>

            <div class="ins nice-scroll">
              <?php 
                // PREMIUM ITEMS
                $c = 1;
    
                while(osc_has_premiums()) {
                  del_draw_item($c, true, 'premium-loop ' . del_param('premium_search_design'));
                  $c++;
                }
              ?>
            </div>
          </div>
        </div>
      <?php } ?>

      <?php echo del_banner('search_top'); ?>

      <div class="products standard <?php echo $show; ?>">
        <?php 
          $c = 1; 
          while( osc_has_items() ) {
            del_draw_item($c, false, del_param('def_design'));

            if($c == 3 && osc_count_items() > 3) {
              echo del_banner('search_middle');
            }

            $c++;
          } 
        ?>
      </div>

    </div>
  </div>
 
  <?php View::newInstance()->_erase('items') ; ?>
</div>