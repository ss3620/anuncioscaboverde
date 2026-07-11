<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()) ; ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
</head>

<body id="body-home" class="layout-<?php echo del_param('home_layout'); ?><?php if(del_device() <> '') { echo ' dvc-' . del_device(); } ?>">
  <?php osc_current_web_theme_path('header.php') ; ?>

  <?php osc_run_hook('home_top'); ?>
  
  <?php if(del_banner('home_top') !== false) { ?>
    <div class="home-container banner-box<?php if(del_is_demo()) { ?> is-demo<?php } ?>"><div class="inside"><?php echo del_banner('home_top'); ?></div></div>
  <?php } ?>

  <?php osc_get_premiums(del_param('premium_home_count')); ?>

  <?php if(del_param('premium_home') == 1 && osc_count_premiums() > 0) { ?>
    <div class="home-container premium">
      <div class="inner">

        <!-- PREMIUMS BLOCK -->
        <div id="premium" class="products grid">
          <h2><?php _e('Featured listings', 'delta'); ?></h2>

          <div class="block">
            <div class="prod-wrap">
              <?php $c = 1; ?>
              <?php while( osc_has_premiums() ) { ?>
                <?php del_draw_item($c, true, del_param('premium_home_design')); ?>
                  
                <?php $c++; ?>
              <?php } ?>

              <?php if(osc_count_premiums() <= 0) { ?>
                <div class="home-empty">
                  <img src="<?php echo osc_current_web_theme_url('images/home-empty.png'); ?>" />
                  <strong><?php _e('No premium listing yet', 'delta'); ?></strong>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  
  <?php osc_run_hook('home_premium'); ?>
  
  <?php if(del_param('promote_home') == 1) { ?>
    <div class="home-container promo">
      <div class="inner">
        <div id="home-pub">
          <div class="info">
            <h3><?php _e('Earn money right now', 'delta'); ?></h3>
            <div><?php _e('Give your used items second chance, sell what you no longer use, immediatelly, easily.', 'delta'); ?></div>
          </div>
          
          <div class="buttons">
            <a class="publish btn mbBg2" href="<?php echo osc_item_post_url(); ?>">
              <span class="mbCl2">
                <svg version="1.1" widt="18px" height="18px" fill="<?php echo del_param('color2'); ?>" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 328.911 328.911" style="enable-background:new 0 0 328.911 328.911;" xml:space="preserve"> <g> <g> <path d="M310.199,18.71C297.735,6.242,282.65,0.007,264.951,0.007H63.954c-17.703,0-32.79,6.235-45.253,18.704 C6.235,31.177,0,46.261,0,63.96v200.991c0,17.515,6.232,32.552,18.701,45.11c12.467,12.566,27.553,18.843,45.253,18.843h201.004 c17.699,0,32.777-6.276,45.248-18.843c12.47-12.559,18.705-27.596,18.705-45.11V63.96 C328.911,46.261,322.666,31.177,310.199,18.71z M292.362,264.96c0,7.614-2.673,14.089-8.001,19.414 c-5.324,5.332-11.799,7.994-19.41,7.994H63.954c-7.614,0-14.082-2.662-19.414-7.994c-5.33-5.325-7.992-11.8-7.992-19.414V63.965 c0-7.613,2.662-14.086,7.992-19.414c5.327-5.327,11.8-7.994,19.414-7.994h201.004c7.61,0,14.086,2.663,19.41,7.994 c5.325,5.328,7.994,11.801,7.994,19.414V264.96z"/> <path d="M246.683,146.189H182.73V82.236c0-2.667-0.855-4.854-2.573-6.567c-1.704-1.714-3.895-2.568-6.564-2.568h-18.271 c-2.667,0-4.854,0.854-6.567,2.568c-1.714,1.713-2.568,3.903-2.568,6.567v63.954H82.233c-2.664,0-4.857,0.855-6.567,2.568 c-1.711,1.713-2.568,3.903-2.568,6.567v18.271c0,2.666,0.854,4.855,2.568,6.563c1.712,1.708,3.903,2.57,6.567,2.57h63.954v63.953 c0,2.666,0.854,4.855,2.568,6.563c1.713,1.711,3.903,2.566,6.567,2.566h18.271c2.67,0,4.86-0.855,6.564-2.566 c1.718-1.708,2.573-3.897,2.573-6.563V182.73h63.953c2.662,0,4.853-0.862,6.563-2.57c1.712-1.708,2.563-3.897,2.563-6.563v-18.271 c0-2.664-0.852-4.857-2.563-6.567C251.536,147.048,249.345,146.189,246.683,146.189z"/> </g> </g> </svg>
                <span><?php _e('Post an ad', 'delta'); ?></span>
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  
  <?php if(del_banner('home_middle') !== false) { ?>
    <div class="home-container banner-box<?php if(del_is_demo()) { ?> is-demo<?php } ?>"><div class="inside"><?php echo del_banner('home_middle'); ?></div></div>
  <?php } ?>
  
  <?php if(function_exists('blg_param') && del_param('blog_home') == 1) { ?>
    <?php
      $blogs = ModelBLG::newInstance()->getActiveBlogs();
    ?>

    <?php if(count($blogs) > 0) { ?>
      <?php $i = 1; ?>
      <?php $blog_limit = del_param('blog_home_count'); ?>

      <div class="home-container bg-gray" id="home-blog">
        <div class="inner">

          <!-- BLOG WIDGET -->
          <div id="blog" class="products">
            <a class="h2" href="<?php echo blg_home_link(); ?>"><?php _e('Latest articles on blog', 'delta'); ?></a>

            <div class="box <?php echo (del_param('blog_home_design') <> 'grid' ? 'list' : 'grid'); ?>">
              <div class="wrap">
                <?php foreach($blogs as $b) { ?>
                  <?php if($i <= $blog_limit) { ?>
                    <a href="<?php echo osc_route_url('blg-post', array('blogSlug' => osc_sanitizeString(blg_get_slug($b)), 'blogId' => $b['pk_i_id'])); ?>">
                      <div class="img">
                        <div style="background-image:url('<?php echo blg_img_link($b['s_image']); ?>');"></div>
                      </div>

                      <div class="data">
                        <h3><?php echo strip_tags(blg_get_title($b)); ?></h3>
                        <div class="desc"><?php echo strip_tags(osc_highlight(blg_get_subtitle($b) <> '' ? blg_get_subtitle($b) : blg_get_description($b), 250)); ?></div>
                      </div>
                    </a>
                  <?php } ?>

                  <?php $i++; ?>

                <?php } ?>
              </div>
            </div>

          </div>
        </div>
      </div>
    <?php } ?>
  <?php } ?>


  <?php if(function_exists('osc_slider')) { ?>

    <!-- Slider Block -->
    <div class="home-container slider">
      <div class="inner">
        <div id="home-slider">
          <?php osc_slider(); ?>
        </div>
      </div>
    </div>
  <?php } ?>



  <div class="home-container latest">
    <div class="inner">

      <!-- LATEST LISTINGS BLOCK -->
      <div id="latest" class="products grid">
        <h2><?php _e('Recently added on our classifieds', 'delta'); ?></h2>

        <?php View::newInstance()->_exportVariableToView('latestItems', del_random_items()); ?>

        <?php if( osc_count_latest_items() > 0) { ?>
          <div class="block">
            <div class="prod-wrap">
              <?php $c = 1; ?>
              <?php while( osc_has_latest_items() ) { ?>
                <?php del_draw_item($c, false, del_param('latest_design')); ?>
                
                <?php $c++; ?>
              <?php } ?>
            </div>
          </div>
        
        <?php } else { ?>
          <div class="home-empty">
            <img src="<?php echo osc_current_web_theme_url('images/home-empty.png'); ?>" />
            <strong><?php _e('No latest listing yet', 'delta'); ?></strong>
          </div>
        <?php } ?>

        <?php View::newInstance()->_erase('items') ; ?>
      </div>
    </div>
  </div>
  
  <?php osc_run_hook('home_latest'); ?>


  <?php if(function_exists('bpr_companies_block') && del_param('company_home') == 1 && count($sellers = ModelBPR::newInstance()->getSellers(1, -1, -1, 8, '', '', '', 'NEW')) > 0) { ?>
    <div class="home-container business">
      <div class="inner">

        <!-- BUSINESS PROFILE WIDGET -->
        <div id="company" class="products grid">
          <a class="h2" href="<?php echo bpr_companies_url(); ?>"><?php _e('Our partners', 'delta'); ?></a>

          <div class="relative">
            <div class="nice-scroll-left"></div>
            <div class="nice-scroll-right"></div>

            <div class="bpr-outer-box nice-scroll">
              <?php echo bpr_companies_block(del_param('company_home_count'), 'NEW'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  
  

  <?php if(function_exists('fi_most_favorited_items') && del_param('favorite_home') == 1) { ?>
    <!-- MOST FAVORITED -->

    <?php
      $limit = (del_param('favorite_count') > 0 ? del_param('favorite_count') : 8);

      // EXPORT FAVORITE ITEMS TO VARIABLE
      GLOBAL $fi_global_items2;
      $fi_global_items2 = View::newInstance()->_get('items');

      // Custom Favorite Items plugin (t_item_favorite) — preferred when present
      if (class_exists('ModelFavorites')) {
        $top_rows = ModelFavorites::newInstance()->topFavoritedItemsFull($limit);
        $list_items = array();
        if (is_array($top_rows)) {
          foreach ($top_rows as $row) {
            if (empty($row['pk_i_id'])) {
              continue;
            }
            $full = Item::newInstance()->findByPrimaryKey((int) $row['pk_i_id']);
            if ($full) {
              $list_items[] = $full;
            }
          }
        }
      } else {
        // Official MB Themes Favorite Items plugin tables
        $aSearch = new Search();
        $aSearch->addField(sprintf('count(%st_item.pk_i_id) as count_id', DB_TABLE_PREFIX) );
        $aSearch->addConditions(sprintf("%st_favorite_list.list_id = %st_favorite_items.list_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
        $aSearch->addConditions(sprintf("%st_favorite_items.item_id = %st_item.pk_i_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
        $aSearch->addConditions(sprintf("%st_favorite_list.user_id <> coalesce(%st_item.fk_i_user_id, 0)", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
        $aSearch->addTable(sprintf("%st_favorite_items", DB_TABLE_PREFIX));
        $aSearch->addTable(sprintf("%st_favorite_list", DB_TABLE_PREFIX));
        $aSearch->addGroupBy(DB_TABLE_PREFIX.'t_item.pk_i_id');
        $aSearch->order('count(*)', 'DESC');
        $aSearch->limit(0, $limit);
        $list_items = $aSearch->doSearch();
      }

      View::newInstance()->_exportVariableToView('items', $list_items);
    ?>

    <?php if(osc_count_items() > 0) { ?>
      <div class="home-container favorite">
        <div class="inner">

          <!-- MOST FAVORITED LISTINGS BLOCK -->
          <div id="favorite" class="products grid">
            <h2><?php _e('Most favorited listings by users', 'delta'); ?></h2>

            <div class="block">
              <div class="prod-wrap">
                <?php $c = 1; ?>
                <?php while( osc_has_items() ) { ?>
                  <?php del_draw_item($c, false, del_param('favorite_design')); ?>
                  <?php $c++; ?>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>

    <?php
      GLOBAL $fi_global_items2; 
      View::newInstance()->_exportVariableToView('items', $fi_global_items2);  
    ?>
  <?php } ?>


  <?php if(del_banner('home_bottom') !== false) { ?>
    <div class="home-container banner-box<?php if(del_is_demo()) { ?> is-demo<?php } ?>"><div class="inside"><?php echo del_banner('home_bottom'); ?></div></div>
  <?php } ?>

  <?php osc_run_hook('home_bottom'); ?>
  
  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>	