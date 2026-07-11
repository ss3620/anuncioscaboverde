<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="index, follow" />
  <meta name="googlebot" content="index, follow" />
</head>

<body id="body-search" class="<?php if(del_device() <> '') { echo 'dvc-' . del_device(); } ?>">
<?php osc_current_web_theme_path('header.php') ; ?>
<?php osc_current_web_theme_path('inc.search.php') ; ?>
<?php osc_current_web_theme_path('inc.category.php') ; ?>

<?php 
  $params_spec = del_search_params();
  $params_all = del_search_params_all();

  $search_cat_id = osc_search_category_id();
  $search_cat_id = isset($search_cat_id[0]) ? $search_cat_id[0] : '';

  $category = Category::newInstance()->findByPrimaryKey($search_cat_id);

  $def_cur = (del_param('def_cur') <> '' ? del_param('def_cur') : '$');

  $search_params_remove = del_search_param_remove();

  $exclude_tr_con = explode(',', del_param('post_extra_exclude'));

  $def_view = del_param('def_view') == 0 ? 'grid' : 'list';
  $show = Params::getParam('sShowAs') == '' ? $def_view : Params::getParam('sShowAs');
  $show = ($show == 'gallery' ? 'grid' : $show);

  // Get search hooks
  GLOBAL $search_hooks;
  ob_start(); 

  if(osc_search_category_id()) { 
    osc_run_hook('search_form', osc_search_category_id());
  } else { 
    osc_run_hook('search_form');
  }

  //$search_hooks = trim(ob_get_clean());
  //ob_end_flush();

  $search_hooks = trim(ob_get_contents());
  ob_end_clean();

  $search_hooks = trim($search_hooks);
?>


<div class="content">
  <div class="inside search">

    <div id="filter" class="filter">
      <?php osc_run_hook('search_sidebar_pre'); ?>
      
      <div class="wrap">
        <form action="<?php echo osc_base_url(true); ?>" method="get" class="search-side-form nocsrf" id="search-form">
          <input type="hidden" name="page" value="search" />
          <input type="hidden" name="ajaxRun" value="" />
          <input type="hidden" name="sOrder" value="<?php echo osc_search_order(); ?>" />
          <input type="hidden" name="iOrderType" value="<?php $allowedTypesForSorting = Search::getAllowedTypesForSorting(); echo isset($allowedTypesForSorting[osc_search_order_type()]) ? $allowedTypesForSorting[osc_search_order_type()] : ''; ?>" />
          <input type="hidden" name="sCompany" class="sCompany" id="sCompany" value="<?php echo osc_esc_html(Params::getParam('sCompany')); ?>" />
          <input type="hidden" name="sCountry" id="sCountry" value="<?php echo osc_esc_html(Params::getParam('sCountry')); ?>"/>
          <input type="hidden" name="sRegion" id="sRegion" value="<?php echo osc_esc_html(Params::getParam('sRegion')); ?>"/>
          <input type="hidden" name="sCity" id="sCity" value="<?php echo osc_esc_html(Params::getParam('sCity')); ?>"/>
          <input type="hidden" name="iPage" id="iPage" value=""/>
          <input type="hidden" name="sShowAs" id="sShowAs" value="<?php echo osc_esc_html(Params::getParam('sShowAs')); ?>"/>
          <input type="hidden" name="showMore" id="showMore" value="<?php echo osc_esc_html(Params::getParam('showMore')); ?>"/>
          <input type="hidden" name="locUpdate"/>
          <input type="hidden" name="sCategory" value="<?php echo $search_cat_id; ?>"/>
          <input type="hidden" name="userId" value="<?php echo osc_esc_html(Params::getParam('userId')); ?>"/>

          <div class="block">
            <div class="search-wrap">
              <?php osc_run_hook('search_sidebar_top'); ?>
 
              <!-- PATTERN AND LOCATION -->
              <div class="box isMobile">
                <h2 class="f1"><?php _e('Search', 'delta'); ?></h2>
                <h2 class="f2 isMobile"><?php _e('Advanced filters', 'delta'); ?></h2>

                <div class="row">
                  <label class="isMobile"><?php _e('Keyword', 'delta'); ?></label>

                  <div class="input-box">
                    <input type="text" name="sPattern" placeholder="<?php _e('What are you looking for?', 'delta'); ?>" value="<?php echo osc_esc_html(Params::getParam('sPattern')); ?>" autocomplete="off"/>
                  </div>
                </div>


                <div class="row">
                  <label for="term2" class="isMobile"><span><?php _e('Location', 'delta'); ?></span></label>

                  <div id="location-picker" class="loc-picker picker-v2 ctr-<?php echo (del_count_countries() == 1 ? 'one' : 'more'); ?>">

                    <div class="mini-box">
                      <input type="text" id="term2" class="term2" placeholder="<?php _e('City/Region', 'delta'); ?>" value="<?php echo osc_esc_html(del_get_term('', Params::getParam('sCountry'), Params::getParam('sRegion'), Params::getParam('sCity'))); ?>" autocomplete="off" readonly/>
                      <i class="fa fa-angle-down"></i>
                    </div>

                    <div class="shower-wrap">
                      <div class="shower" id="shower">
                        <?php echo del_locbox_short(Params::getParam('sCountry'), Params::getParam('sRegion'), Params::getParam('sCity')); ?>
                        <a href="#" class="btn btn-primary mbBg loc-confirm isMobile"><i class="far fa-check-square"></i> <?php _e('Submit', 'delta'); ?></a>

                        <div class="button-wrap isTablet isDesktop">
                          <a href="#" class="btn btn-primary mbBg loc-confirm"><?php _e('Ok', 'delta'); ?></a>
                        </div>
                      </div>
                    </div>

                    <div class="loader"></div>
                  </div>
                </div>
                
                <?php echo osc_run_hook('search_sidebar_location'); ?>


                <div class="row isMobile">
                  <label for="term3"><span><?php _e('Category', 'delta'); ?></span></label>

                  <div id="category-picker" class="cat-picker picker-v2">
                    <div class="mini-box">
                      <input type="text" class="term3" id="term3" placeholder="<?php _e('Category', 'delta'); ?>"  autocomplete="off" value="<?php echo @$category['s_name']; ?>" readonly/>
                      <i class="fa fa-angle-down"></i>
                    </div>

                    <div class="shower-wrap">
                      <div class="shower" id="shower">
                        <?php echo del_catbox_short($search_cat_id); ?>
                        <a href="#" class="btn btn-primary mbBg cat-confirm isMobile"><i class="far fa-check-square"></i> <?php _e('Submit', 'delta'); ?></a>

                        <div class="button-wrap isTablet isDesktop">
                          <a href="#" class="btn btn-primary mbBg cat-confirm"><i class="far fa-check-square"></i> <?php _e('Submit', 'delta'); ?></a>
                        </div>
                      </div>
                    </div>

                    <div class="loader"></div>
                  </div>
                </div>
              </div>

 
              <!-- CONDITION --> 
              <?php if($search_cat_id <= 0 || @!in_array($search_cat_id, $exclude_tr_con)) { ?>
                <div class="box cond">
                  <h2><?php _e('Condition', 'delta'); ?></h2>

                  <div class="row">
                    <?php echo del_simple_condition(); ?>
                  </div>
                </div>
              <?php } ?>

 
              <!-- TRANSACTION --> 
              <?php if($search_cat_id <= 0 || @!in_array($search_cat_id, $exclude_tr_con)) { ?>
                <div class="box tran">
                  <h2><?php _e('Transaction', 'delta'); ?></h2>

                  <div class="row">
                    <?php echo del_simple_transaction(); ?>
                  </div>
                </div>
              <?php } ?>


              <!-- PRICE -->
              <?php if( del_check_category_price($search_cat_id) ) { ?>
                <div class="box price-box">
                  <h2><?php _e('Price', 'delta'); ?></h2>

                  <div class="row price">
                    <div class="input-box">
                      <input type="number" class="priceMin" name="sPriceMin" value="<?php echo osc_esc_html(Params::getParam('sPriceMin')); ?>" size="6" maxlength="6" placeholder="<?php echo osc_esc_js(__('Min', 'delta')); ?>"/>
                      <span><?php echo $def_cur; ?></span>
                    </div>

                    <div class="input-box">
                      <input type="number" class="priceMax" name="sPriceMax" value="<?php echo osc_esc_html(Params::getParam('sPriceMax')); ?>" size="6" maxlength="6" placeholder="<?php echo osc_esc_js(__('Max', 'delta')); ?>"/>
                      <span><?php echo $def_cur; ?></span>
                    </div>
                  </div>
                </div>
              <?php } ?>

              <!-- PERIOD--> 
              <div class="box">
                <h2><?php _e('Period', 'delta'); ?></h2>

                <div class="row">
                  <?php echo del_simple_period(); ?>
                </div>
              </div>



              <?php if( osc_images_enabled_at_items() ) { ?>
                <fieldset class="img-check">
                  <div class="row checkboxes">
                    <div class="input-box-check">
                      <input type="checkbox" name="bPic" id="bPic" value="1" <?php echo (osc_search_has_pic() ? 'checked="checked"' : ''); ?> />
                      <label for="bPic" class="with-pic-label"><?php _e('Only items with picture', 'delta'); ?></label>
                    </div>
                  </div>
                </fieldset>
              <?php } ?>

              <fieldset class="prem-check">
                <div class="row checkboxes">
                  <div class="input-box-check">
                    <input type="checkbox" name="bPremium" id="bPremium" value="1" <?php echo (Params::getParam('bPremium') == 1 ? 'checked="checked"' : ''); ?> />
                    <label for="bPremium" class="only-prem-label"><?php _e('Only premium items', 'delta'); ?></label>
                  </div>
                </div>
              </fieldset>


              <?php if($search_hooks <> '') { ?>
                <div class="box sidehook">
                  <h2 class="split"><?php _e('Detailed search', 'delta'); ?></h2>

                  <div class="sidebar-hooks">
                    <?php echo $search_hooks; ?>
                  </div>
                </div>
              <?php } ?>
              
              <button type="submit" class="btn mbBg init-search" id="search-button">
                <svg fill="#fff" width="16px" height="16px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve"> <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23 s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92 c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17 s-17-7.626-17-17S14.61,6,23.984,6z"></path> </svg>
                <span><?php _e('Search', 'delta'); ?></span>
              </button>
            </div>
          </div>
          
          <?php osc_run_hook('search_sidebar_bottom'); ?>
        </form>
      </div>
      
      <div class="sbox cat">
        <h3><?php _e('Navigate', 'delta'); ?></h3>
        <div class="wrap">
          <?php
            $search_params = $params_spec;
            $only_root = false;

            if($search_cat_id <= 0) {
              $parent = false;
              $categories = Category::newInstance()->findRootCategoriesEnabled();
              $children = false;
            } else {
              $parent = Category::newInstance()->findByPrimaryKey($search_cat_id);
              $categories = Category::newInstance()->findSubcategoriesEnabled($search_cat_id);

              if(count($categories) <= 0) {
                if($parent['fk_i_parent_id'] > 0) {
                  $parent = Category::newInstance()->findByPrimaryKey($parent['fk_i_parent_id']);
                  $categories = Category::newInstance()->findSubcategoriesEnabled($parent['pk_i_id']);

                } else {  // only parent categories exists
                  $parent = false;
                  $categories = Category::newInstance()->findRootCategoriesEnabled();
                  $only_root = true;
                }
              }
            }          
          ?>




          <div class="inside <?php if($search_cat_id <= 0 || $only_root) { ?>root<?php } else { ?>notroot<?php } ?>">
            <?php if($parent) { ?>
              <?php 
                $search_params['sCategory'] = $parent['pk_i_id']; 
                unset($search_params['iPage']);
              ?>
              <a href="<?php echo osc_search_url($search_params); ?>" class="parent active" data-name="sCategory" data-val="<?php echo $parent['pk_i_id']; ?>">
                <?php $color = del_get_cat_color($parent['pk_i_id']); ?>
                
                <div class="icon">
                  <?php if(del_param('cat_icons') == 1) { ?>
                    <i class="fas <?php echo del_get_cat_icon($parent['pk_i_id'], true ); ?>" <?php if($color <> '') { ?>style="color:<?php echo $color; ?>;"<?php } ?>></i>
                  <?php } else { ?>
                    <img src="<?php echo del_get_cat_image($parent['pk_i_id']); ?>" alt="<?php echo osc_esc_html($parent['s_name']); ?>" />
                  <?php } ?>
                </div>

                <span class="name"><?php echo $parent['s_name']; ?></span><em><?php echo ($parent['i_num_items'] == '' ? 0 : $parent['i_num_items']); ?></em>
              </a>
            <?php } ?>

            <?php foreach($categories as $c) { ?>
              <?php 
                $search_params['sCategory'] = $c['pk_i_id']; 
                unset($search_params['iPage']);
              ?>

              <a href="<?php echo osc_search_url($search_params); ?>" class="child<?php if($c['pk_i_id'] == $search_cat_id) { ?> active<?php } ?>" data-name="sCategory" data-val="<?php echo $c['pk_i_id']; ?>">
                <?php if($search_cat_id <= 0 || $only_root) { ?>
                  <?php $color = del_get_cat_color($c['pk_i_id']); ?>
                
                  <div class="icon">
                    <?php if(del_param('cat_icons') == 1) { ?>
                      <i class="fas <?php echo del_get_cat_icon($c['pk_i_id'], true ); ?>" <?php if($color <> '') { ?>style="color:<?php echo $color; ?>;"<?php } ?>></i>
                    <?php } else { ?>
                      <img src="<?php echo del_get_cat_image($c['pk_i_id']); ?>" alt="<?php echo osc_esc_html($c['s_name']); ?>" />
                    <?php } ?>
                  </div>
                <?php } ?>
                
                <span class="name"><?php echo $c['s_name']; ?></span><em><?php echo ($c['i_num_items'] == '' ? 0 : $c['i_num_items']); ?></em>
              </a>
            <?php } ?>

          </div>
          
          <?php if($search_cat_id > 0 && !$only_root) { ?>  
            <?php 
              $search_params['sCategory'] = (@$parent['pk_i_id'] <> $search_cat_id ? @$parent['pk_i_id'] : @$parent['fk_i_parent_id']); 
              unset($search_params['iPage']);  
            ?>
            <a href="<?php echo osc_search_url($search_params); ?>" class="gotop" data-name="sCategory" data-val="<?php echo $parent['fk_i_parent_id']; ?>"><i class="fas fa-level-up-alt fa-flip-horizontal"></i> <?php _e('One level up', 'delta'); ?></a>
          <?php } ?>
        </div>
      </div>
      
      <?php osc_get_latest_searches() ?>
      <?php if(osc_count_latest_searches() > 0) { ?>
        <div class="sbox words">
          <h3><?php _e('Popular search', 'delta'); ?></h3>
          <div class="wrap">
            <?php $i = 0; ?>
            <?php while(osc_has_latest_searches()) { ?>
              <?php 
                if($i > 12) { break; } 
                $i++;
              ?>
             
              <a href="<?php echo osc_search_url(array('page' => 'search', 'sPattern' => osc_latest_search_text())); ?>"><?php echo osc_highlight(osc_latest_search_text(), 20); ?></a>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
      
      
      <?php
        if(del_param('save_search_position') == 'SIDE') { 
          del_save_search_section('side');
        }
      ?>
      
      <?php echo del_banner('search_sidebar'); ?>
      <?php osc_run_hook('search_sidebar_after'); ?>
    </div>


    <div id="main" class="<?php echo $show; ?>">
      <?php osc_run_hook('search_items_top'); ?>
      
      <div class="relative2">
        <div class="titles-top">
          <h1>
            <?php 
              $loc = @array_filter(array(osc_search_city(), osc_search_region(), osc_search_city()))[0];
              $cat = @$category['s_name'];

              if(osc_search_total_items() <= 0) { 
                echo __('No listings found', 'delta');

              } else {
                echo sprintf(__('%s results match your search criteria', 'delta'), osc_search_total_items());
              }
            ?>
          </h1>
        </div>
        
        <?php osc_run_hook('search_items_filter'); ?>
        
        <!-- REMOVE FILTER SECTION -->
        <?php  
          // count usable params
          $filter_check = 0;
          if(is_array($search_params_remove) && count($search_params_remove) > 0) {
            foreach($search_params_remove as $n => $v) { 
              if($v['name'] <> '' && $v['title'] <> '') { 
                $filter_check++;
              }
            }
          }
        ?>

        <?php if($filter_check > 0) { ?>
          <div class="filter-remove">
            <?php foreach($search_params_remove as $n => $v) { ?>
              <?php if($v['name'] <> '' && $v['title'] <> '') { ?>
                <?php
                  $rem_param = $params_all;

                  if($v['is_meta'] === true) {
                    unset($rem_param['meta'][$v['field_id']]);
                  } else {
                    unset($rem_param[$n]);
                  }
                ?>

                <a href="<?php echo osc_search_url($rem_param); ?>" data-type="<?php echo osc_esc_html(strtolower($v['type'])); ?>" data-param="<?php echo osc_esc_html($v['param']); ?>" title="<?php echo osc_esc_html($v['title'] . ': ' . $v['name']); ?>"><?php echo $v['title'] . ': ' . $v['name']; ?></a>
              <?php } ?>
            <?php } ?>

            <?php if($filter_check >= 2) { ?>
              <a class="bold remove-all-filters" href="<?php echo osc_search_url(array('page' => 'search')); ?>"><?php _e('Remove all', 'delta'); ?></a>
            <?php } ?>
          </div>
        <?php } ?>

        <?php
          $p1 = $params_all; $p1['sCompany'] = null;
          $p2 = $params_all; $p2['sCompany'] = 0;
          $p3 = $params_all; $p3['sCompany'] = 1;

          $us_type = Params::getParam('sCompany');
          
        ?>


        <!-- SEARCH FILTERS - SORT / COMPANY / VIEW -->
        <div id="search-sort" class="">
          <div class="user-type">
            <a class="all<?php if(Params::getParam('sCompany') === '' || Params::getParam('sCompany') === null) { ?> active<?php } ?>" href="<?php echo osc_search_url($p1); ?>"><?php _e('All listings', 'delta'); ?></a>
            <a class="personal<?php if(Params::getParam('sCompany') === '0') { ?> active<?php } ?>" href="<?php echo osc_search_url($p2); ?>"><?php _e('Personal', 'delta'); ?></a>
            <a class="company<?php if(Params::getParam('sCompany') === '1') { ?> active<?php } ?>" href="<?php echo osc_search_url($p3); ?>"><?php _e('Company', 'delta'); ?></a>
          </div>

          <?php if(osc_count_items() > 0) { ?>
            <div class="sort-it">
              <div class="sort-title">
                <div class="title-keep noselect">
                  <?php $orders = osc_list_orders(); ?>
                  <?php $current_order = osc_search_order(); ?>
                  <?php foreach($orders as $label => $params) { ?>
                    <?php $orderType = ($params['iOrderType'] == 'asc') ? '0' : '1'; ?>
                    <?php if(osc_search_order() == $params['sOrder'] && osc_search_order_type() == $orderType) { ?>
                      <span>
                        <span class="lab" style="display:none;"><?php _e('Sort by', 'delta'); ?></span>
                        <span class="kind"><?php echo $label; ?></span>
                        <svg style="display:none;" viewBox="0 0 32 32" color="#696766" width="14px" height="14px"><defs><path id="mbIconAngle" d="M12.147 25.2c-.462 0-.926-.185-1.285-.556L.57 14.024A2.05 2.05 0 010 12.586c0-.543.206-1.061.571-1.436L10.864.553a1.765 1.765 0 012.62.06c.71.795.683 2.057-.055 2.817l-8.9 9.16 8.902 9.183c.738.76.761 2.024.052 2.815a1.78 1.78 0 01-1.336.612"></path></defs><use fill="currentColor" transform="matrix(0 -1 -1 0 29 24)" xlink:href="#mbIconAngle" fill-rule="evenodd"></use></svg>
                      </span>
                    <?php } ?>
                  <?php } ?>
                </div>

                <div id="sort-wrap">
                  <div class="sort-content">
                    <?php $i = 0; ?>
                    <?php foreach($orders as $label => $params) { ?>
                      <?php $orderType = ($params['iOrderType'] == 'asc') ? '0' : '1'; ?>
                      <?php if(osc_search_order() == $params['sOrder'] && osc_search_order_type() == $orderType) { ?>
                        <a class="current" href="<?php echo osc_update_search_url($params) ; ?>"><span><?php echo $label; ?></span></a>
                      <?php } else { ?>
                        <a href="<?php echo osc_update_search_url($params) ; ?>"><span><?php echo $label; ?></span></a>
                      <?php } ?>
                      <?php $i++; ?>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="list-grid">
              <?php $show = Params::getParam('sShowAs') == '' ? $def_view : Params::getParam('sShowAs'); ?>
              <a href="<?php echo osc_update_search_url(array('sShowAs' => 'list')); ?>" title="<?php echo osc_esc_html(__('List view', 'delta')); ?>" class="lg<?php echo ($show == 'list' ? ' active' : ''); ?> list" data-view="list">
                <div class="lgicon list">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </a>
              
              <a href="<?php echo osc_update_search_url(array('sShowAs' => 'grid')); ?>" title="<?php echo osc_esc_html(__('Grid view', 'delta')); ?>" class="lg<?php echo ($show == 'grid' ? ' active' : ''); ?> grid" data-view="grid">
                <div class="lgicon grid">
                  <span></span>
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </a>
            </div>

          <?php } ?>
        </div>
        
      </div>
      

      <div id="search-items">     
        <?php if(osc_count_items() == 0) { ?>
          <div class="list-empty round3" >
            <span class="titles"><?php _e('We could not find any results for your search...', 'delta'); ?></span>

            <div class="tips">
              <div class="row"><?php _e('Following tips might help you to get better results', 'delta'); ?></div>
              <div class="row"><i class="fa fa-circle"></i><?php _e('Use more general keywords', 'delta'); ?></div>
              <div class="row"><i class="fa fa-circle"></i><?php _e('Check spelling of position', 'delta'); ?></div>
              <div class="row"><i class="fa fa-circle"></i><?php _e('Reduce filters, use less of them', 'delta'); ?></div>
              <div class="row last"><a href="<?php echo osc_search_url(array('page' => 'search'));?>"><?php _e('Reset filter', 'delta'); ?></a></div>
            </div>
          </div>

          <?php
            if(del_param('save_search_position') == 'TOP' || del_param('save_search_position') == '') { 
              del_save_search_section(strtolower(del_param('save_search_position')));
            }
          ?>
          
        <?php } else { ?>
          <?php
            if(del_param('save_search_position') == 'TOP') { 
              del_save_search_section('top');
            }

            if(function_exists('osc_current_web_theme_path_value')) {
              require_once osc_current_web_theme_path_value('search_gallery.php');
            } else {
              require_once 'search_gallery.php';
            }
          
            if(del_param('save_search_position') == '') { 
              del_save_search_section('bottom');
            }
          ?>
        <?php } ?>
        
        <?php echo del_banner('search_bottom'); ?>
        <?php osc_run_hook('search_items_bottom'); ?>

        <div class="paginate"><?php echo del_fix_arrow(osc_search_pagination()); ?></div>
      </div>
    </div>
  </div>
</div>

<?php osc_current_web_theme_path('footer.php') ; ?>

</body>
</html>