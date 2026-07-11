<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
</head>

<body id="body-user-alerts" class="body-ua">
  <?php osc_current_web_theme_path('header.php') ; ?>

  <?php echo del_user_menu_top(); ?>

  <?php
    $alerts = array();

    while(osc_has_alerts()) { 
      $alert = View::newInstance()->_current('alerts');
      $alert_details = (array)json_decode($alert['s_search']);

      $alerts[$alert['pk_i_id']] = $alert;
      
      // CONNECTION & DB INFO
      $conn = DBConnectionClass::newInstance();
      $data = $conn->getOsclassDb();
      $comm = new DBCommandClass($data);
      $db_prefix = DB_TABLE_PREFIX;


      // COUNTRIES
      $c_filter = $alert_details['countries'];
      $c_filter = isset($c_filter[0]) ? $c_filter[0] : '';
      $c_filter = str_replace('item_location.fk_c_country_code', 'country.pk_c_code', $c_filter);

      $c_query = "SELECT * FROM {$db_prefix}t_country WHERE " . $c_filter;
      $c_result = $comm->query($c_query);

      if( !$c_result ) { 
        $c_prepare = array();
      } else {
        $c_prepare = $c_result->result();
      }


      // REGIONS
      $r_filter = $alert_details['regions'];
      $r_filter = isset($r_filter[0]) ? $r_filter[0] : '';
      $r_filter = str_replace('item_location.fk_i_region_id', 'region.pk_i_id', $r_filter);

      $r_query = "SELECT * FROM {$db_prefix}t_region WHERE " . $r_filter;
      $r_result = $comm->query($r_query);

      if( !$r_result ) { 
        $r_prepare = array();
      } else {
        $r_prepare = $r_result->result();
      }


      // CITIES
      $t_filter = $alert_details['cities'];
      $t_filter = isset($t_filter[0]) ? $t_filter[0] : '';
      $t_filter = str_replace('item_location.fk_i_city_id', 'city.pk_i_id', $t_filter);

      $t_query = "SELECT * FROM {$db_prefix}t_city WHERE " . $t_filter;
      $t_result = $comm->query($t_query);

      if( !$t_result ) { 
        $t_prepare = array();
      } else {
        $t_prepare = $t_result->result();
      }


      // CATEGORIES
      $cat_list = $alert_details['aCategories'];
      $cat_list = implode(', ', $cat_list);
      $locale = '"' . osc_current_user_locale() . '"';

      $cat_query = "SELECT * FROM {$db_prefix}t_category_description WHERE fk_i_category_id IN (" . $cat_list . ") AND fk_c_locale_code = " . $locale;
      $cat_result = $comm->query($cat_query);

      if( !$cat_result ) { 
        $cat_prepare = array();
      } else {
        $cat_prepare = $cat_result->result();
      }
      
      $country_name = @$c_prepare[0]['s_name'];
      $region_name = @$r_prepare[0]['s_name'];
      $city_name = @$t_prepare[0]['s_name'];
      $cat = @$cat_prepare[0]['s_name'];
      
      
      $loc = @array_filter(array($country_name, $region_name, $city_name))[0];
      
      if($loc == '' && $cat == '') {
        $name = sprintf(__('Alert #%s', 'delta'), $alert['pk_i_id']);
      } elseif ($loc == '' && $cat <> '') {
        $name = sprintf(__('%s results', 'delta'), $cat);
      } elseif ($loc <> '' && $cat == '') {
        $name = sprintf(__('%s results', 'delta'), $loc);
      } else {
        $name = sprintf(__('%s - %s', 'delta'), $cat, $loc);
      }

      $alerts[$alert['pk_i_id']]['name'] = $name;
      $alerts[$alert['pk_i_id']]['count'] = osc_count_items();
      $alerts[$alert['pk_i_id']]['unsubscribe_url'] = osc_user_unsubscribe_alert_url();

    }

  ?>


  <div class="inside user_account">
    <div class="usr-menu alerts-menu">
      <?php if(osc_count_alerts() <= 0) { ?>
        <div class="usr-empty"><?php _e('No alerts found', 'delta'); ?></div>
      <?php } else { ?>
        <?php $c = 0; ?>
        <?php foreach($alerts as $a) { ?>
          <div data-id="<?php echo $a['pk_i_id']; ?>" <?php if($c == 0) { ?>class="active"<?php } ?>>
            <strong><?php echo $a['name']; ?></strong>
            <span><?php echo sprintf(__('%s listings', 'delta'), $a['count']); ?></span>

            <a href="<?php echo $a['unsubscribe_url']; ?>" class="del" title="<?php echo osc_esc_html(__('Unsubscribe', 'delta')); ?>" onclick="javascript:return confirm('<?php echo osc_esc_js(__('This action can\'t be undone. Are you sure you want to continue?', 'delta')); ?>');"><i class="fas fa-trash"></i></a>
          </div>

          <?php $c++; ?>
        <?php } ?>
      <?php } ?>
    </div>


    <div id="main" class="alerts">
      <div class="inner-box">

        <div class="inside">
          <?php if(is_array($alerts) && count($alerts) > 0) { ?>
            <?php $c = 0; ?>

            <?php foreach($alerts as $a) { ?>
              <div class="alert-box" data-id="<?php echo $a['pk_i_id']; ?>" <?php if($c == 0){ ?>style="display:block;"<?php } ?>>
                <?php View::newInstance()->_exportVariableToView("items", isset($a['items']) ? $a['items'] : array()); ?>

                <h1>
                  <?php 
                    if(function_exists('osc_alert_name') && isset($a['s_name']) && $a['s_name'] != '') {
                      echo $a['s_name'];
                    } else {
                      echo sprintf(__('Subscription #%d', 'delta'), $a['pk_i_id']);
                    }
                  ?>
                </h1>

                <?php
                  if(function_exists('osc_alert_change_frequency')) {
                    echo osc_alert_change_frequency($a);
                  }
                ?>
                
                <h2>
                  <?php echo sprintf(__('Created on %s, %d items match criteria.', 'delta'), date('j. M Y', strtotime($a['dt_date'])), $a['count']); ?>
                  <a href="<?php echo $a['unsubscribe_url']; ?>"><?php _e('Unsubscribe', 'delta'); ?></a>  
                </h2>

                <?php if(osc_count_items() > 0) { ?>
                  <?php while(osc_has_items()) { ?>
                    <div class="uitem lazy<?php if(osc_item_is_inactive()) { ?> inactive<?php } ?><?php if(osc_item_is_expired()) { ?> expired<?php } ?>">
                      <?php if(osc_images_enabled_at_items()) { ?>
                        <div class="image">
                          <a href="<?php echo osc_item_url(); ?>">
                            <?php if(osc_count_item_resources() > 0) { ?>
                              <img src="<?php echo osc_resource_thumbnail_url(); ?>" title="<?php echo osc_esc_html(osc_item_title()); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?>" />
                            <?php } else { ?>
                              <img src="<?php echo del_get_noimage(); ?>" title="<?php echo osc_esc_html(osc_item_title()); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?>" />
                            <?php } ?>
                          </a>
                        </div>
                      <?php } ?>

                      <div class="body">
                        <?php
                          $loc = @array_filter(array(osc_item_city(), osc_item_region(), osc_item_country()))[0];
                        ?>

                        <div class="category">
                          <?php echo osc_item_category(); ?>
                          <?php if($loc <> '') { ?>
                            / <?php echo $loc; ?>
                          <?php } ?>
                        </div>

                        <div class="pub"><?php echo osc_format_date(osc_item_pub_date()); ?></div>

                        <div class="title">
                          <a href="<?php echo osc_item_url(); ?>"><?php echo osc_item_title(); ?></a>

                          <?php if(osc_item_is_inactive()) {?>
                            <div class="ua-premium inactive"><span><?php _e('Inactive', 'delta'); ?></span></div>
                          <?php } else if(osc_item_is_expired()) { ?>
                             <div class="ua-premium expired"><span><?php _e('Expired', 'delta'); ?></span></div>
                          <?php } else if(osc_item_is_premium()) { ?>
                            <div class="ua-premium mbBg3" title="<?php _e('This listing is premium', 'delta'); ?>"><?php _e('Premium', 'delta'); ?></div>
                          <?php } ?>
                        </div>

                        <?php if( osc_price_enabled_at_items() ) { ?>
                          <span class="price mbCl"><?php echo osc_item_formated_price(); ?></span>
                        <?php } ?>
                      </div>
                    </div>

                  <?php } ?>
                <?php } else { ?>
                  <div class="ua-items-empty"><img src="<?php echo osc_current_web_theme_url('images/ua-empty.jpg'); ?>"/> <span><?php _e('No listings match to this alert', 'delta'); ?></span></div>
                <?php } ?>
              </div>

              <?php $c++; ?>
            <?php } ?>

          <?php } else { ?>
            <h1><?php _e('Subscriptions', 'delta'); ?></h1>
            <h2><?php _e('Subscribe to search results to get new offers notificaitons immediately to your email', 'delta'); ?></h2>
            <div class="ua-items-empty"><img src="<?php echo osc_current_web_theme_url('images/ua-empty.jpg'); ?>"/> <span><?php _e('You have no active alerts', 'delta'); ?></span></div>
          <?php  } ?>
        </div>
      </div>
    </div>
  </div>

  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>