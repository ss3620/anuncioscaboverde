<?php $item_extra = del_item_extra(osc_premium_id()); ?>

<div class="simple-prod <?php echo isset($c) ? 'o'. $c : ''; ?> is-premium<?php if(@$class <> '') { echo ' ' . $class; } ?><?php if(@$item_extra['i_sold'] == 1) { echo ' st-sold'; } else if(@$item_extra['i_sold'] == 2) { echo ' st-reserved'; } ?> <?php osc_run_hook("highlight_class"); ?>">
  <div class="simple-wrap">
    <?php osc_run_hook('item_loop_top', true); ?>
    
    <?php if(@$item_extra['i_sold'] == 1) { ?>
      <a class="label lab-sold" href="<?php echo osc_premium_url(); ?>"><?php _e('Sold', 'delta'); ?></a>
    <?php } else if(@$item_extra['i_sold'] == 2) { ?>
      <a class="label lab-res" href="<?php echo osc_premium_url(); ?>"><?php _e('Reserved', 'delta'); ?></a>
    <?php } else { ?>
      <a class="label lab-prem mbBg3" href="<?php echo osc_premium_url(); ?>"><?php _e('Premium', 'delta'); ?></a>
    <?php } ?>       

    <div class="img-wrap<?php if(osc_count_premium_resources() == 0) { ?> no-image<?php } ?>">
      <?php if(osc_count_premium_resources() > 0) { ?>
        <?php 
          $bar_count = min(osc_count_premium_resources(), 5); 
          $bar_width = floor((100/$bar_count)*100)/100;
        ?>
        
        <a class="switch-bars" href="<?php echo osc_premium_url(); ?>" data-count=<?php echo $bar_count; ?>>
          <?php for($i = 1;$i <= $bar_count; $i++) { ?>
            <div class="bar" data-id="<?php echo $i; ?>" style="width:<?php echo $bar_width; ?>%;left:<?php echo ($i-1)*$bar_width; ?>%;"></div>
          <?php } ?>
        </a>
        
        <?php for($i = 1;osc_has_premium_resources(); $i++) { ?>
          <?php if($i <= 5) { ?>
            <div class="img" data-id="<?php echo $i; ?>" <?php if($i > 1) { ?>style="display:none;"<?php } ?>>
              <img class="<?php echo (del_is_lazy() ? 'lazy' : ''); ?>" src="<?php echo (del_is_lazy() ? del_get_noimage() : osc_resource_thumbnail_url()); ?>" data-src="<?php echo osc_resource_thumbnail_url(); ?>" alt="<?php echo osc_esc_html(osc_premium_title()); ?>" />
              
              <?php if($i == 5 && osc_count_premium_resources() > 5) { ?>
                <div class="more-img">
                  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <g> <g> <path d="M437.333,117.333h-64c-4.587,0-8.661-2.923-10.112-7.296l-6.741-20.224c-7.275-21.824-27.605-36.48-50.603-36.48h-99.755 c-22.997,0-43.328,14.656-50.581,36.459l-6.741,20.245c-1.472,4.373-5.547,7.296-10.133,7.296h-64C33.493,117.333,0,150.827,0,192 v192c0,41.173,33.493,74.667,74.667,74.667h362.667C478.507,458.667,512,425.173,512,384V192 C512,150.827,478.507,117.333,437.333,117.333z M490.667,384c0,29.397-23.936,53.333-53.333,53.333H74.667 c-29.397,0-53.333-23.936-53.333-53.333V192c0-29.397,23.936-53.333,53.333-53.333h64c13.781,0,25.984-8.789,30.357-21.867 l6.763-20.267c4.352-13.077,16.555-21.867,30.336-21.867h99.755c13.781,0,25.984,8.789,30.357,21.888l6.741,20.245 c4.373,13.077,16.576,21.867,30.357,21.867h64c29.397,0,53.333,23.936,53.333,53.333V384z"/> </g> </g> <g> <g> <path d="M256,160c-70.592,0-128,57.408-128,128s57.408,128,128,128s128-57.408,128-128S326.592,160,256,160z M256,394.667 c-58.816,0-106.667-47.851-106.667-106.667c0-58.816,47.851-106.667,106.667-106.667S362.667,229.184,362.667,288 C362.667,346.816,314.816,394.667,256,394.667z"/> </g> </g> <g> <g> <path d="M256,202.667c-47.061,0-85.333,38.272-85.333,85.333c0,5.888,4.779,10.667,10.667,10.667S192,293.888,192,288 c0-35.285,28.715-64,64-64c5.888,0,10.667-4.779,10.667-10.667S261.888,202.667,256,202.667z"/> </g> </g> </svg>
                  <span>
                    <?php
                      if(osc_count_premium_resources() - 5 > 1) { 
                        echo sprintf(__('%s more pictures', 'delta'), osc_count_premium_resources() - 5);
                      } else {
                        echo _e('1 more picture', 'delta');
                      }
                    ?>
                  </span>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
        <?php } ?>
      <?php } else { ?>
        <a class="img" href="<?php echo osc_premium_url(); ?>"><img class="<?php echo (del_is_lazy() ? 'lazy' : ''); ?>" src="<?php echo del_get_noimage(); ?>" data-src="<?php echo del_get_noimage(); ?>" alt="<?php echo osc_esc_html(osc_premium_title()); ?>" /></a>
      <?php } ?>

      <div class="publish isGrid"><?php echo del_smart_date(osc_premium_pub_date()); ?></div>

      <?php del_make_favorite(osc_premium_id()); ?>
    </div>

    <div class="data">
      <?php if(del_check_category_price(osc_premium_category_id())) { ?>
        <div class="price isGrid"><span><?php echo del_premium_format_price(osc_premium_price()); ?></span></div>
      <?php } ?>

      <a class="title" href="<?php echo osc_premium_url(); ?>"><?php echo osc_highlight(osc_premium_title(), 100); ?></a>

      <?php osc_run_hook('item_loop_title', true); ?>
      
      <div class="details isGrid">
        <?php $transaction_condition = ''; ?>
        <?php if(!in_array(osc_premium_category_id(), del_extra_fields_hide())) { ?>
          <?php
            $transaction_condition = array_filter(array_map('trim', array_filter(array(del_get_simple_name($item_extra['i_condition'] ?? 0, 'condition', false), del_get_simple_name($item_extra['i_transaction'] ?? 0, 'transaction', false)))));
            $transaction_condition = implode('<br/>', $transaction_condition);
          ?>
          
          <?php if($transaction_condition <> '') { ?>
            <div class="cd"><span><?php echo $transaction_condition; ?></span></div>
          <?php } ?>
        <?php } ?>
        
        <?php if($transaction_condition == '') { ?>
          <?php
            $category_location = array_filter(array_map('trim', array_filter(array(del_item_location(true), osc_premium_category()))));
            $category_location = implode('<br/>', $category_location);
          ?>

          <?php if($category_location <> '') { ?>
            <div class="lc"><span><?php echo $category_location; ?></span></div>
          <?php } ?>
        <?php } ?>
        
        <div class="dt"><span><?php echo del_smart_date(osc_premium_pub_date()); ?></span></div>
        
        <div class="bt">
          <a class="btn mbBg" href="<?php echo osc_premium_url(); ?>#contact"><?php _e('Contact', 'delta'); ?></a>
        </div>
      </div>
      
      <div class="description isList"><?php echo osc_highlight(strip_tags(osc_premium_description()), 320); ?></div>

      <?php osc_run_hook('item_loop_description', true); ?>
      
      <div class="extra isList">
        <?php if(del_item_location(true, true) <> '') { ?>
          <span class="location"><i class="fas fa-map-marked-alt"></i> <span><?php echo __('Located:', 'delta'); ?></span> <strong><?php echo del_item_location(true, true); ?></strong></span>
        <?php } ?>
        
        <?php if(!in_array(osc_premium_category_id(), del_extra_fields_hide())) { ?>
          <?php if(del_get_simple_name($item_extra['i_condition'], 'condition', false) <> '') { ?>
            <span class="condition"><i class="fas fa-battery-half"></i> <span><?php echo __('Condition:', 'delta'); ?></span> <strong><?php echo del_get_simple_name($item_extra['i_condition'], 'condition', false); ?></strong></span>
          <?php } ?>

          <?php if(del_get_simple_name($item_extra['i_transaction'], 'transaction', false) <> '') { ?>
            <span class="transaction"><i class="fas fa-exchange-alt"></i> <span><?php echo __('Transaction:', 'delta'); ?></span> <strong><?php echo del_get_simple_name($item_extra['i_transaction'], 'transaction', false); ?></strong></span>
          <?php } ?>          
        <?php } ?>
      </div>
      
      <div class="action isList">
        <?php if(del_check_category_price(osc_premium_category_id())) { ?>
          <div class="price<?php if(osc_premium_price() <= 0) { ?> isstring<?php } ?>"><span><?php echo del_premium_format_price(osc_premium_price()); ?></span></div>
        <?php } ?>
        
        <div class="bt">
          <a class="btn mbBg" href="<?php echo osc_premium_url(); ?>#contact"><i class="fas fa-envelope"></i> <?php _e('Contact', 'delta'); ?></a>
        </div>
        
        <div class="hit"><?php echo sprintf(__('%s people viewed', 'delta'), osc_premium_views()); ?></div>
        <div class="time"><?php echo sprintf(__('Added %s', 'delta'), del_smart_date(osc_premium_pub_date())); ?></div>
      </div>
    </div>
    
    <?php osc_run_hook('item_loop_bottom', true); ?>
  </div>
</div>