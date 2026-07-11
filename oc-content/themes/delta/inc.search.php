<?php if(osc_is_home_page() || osc_is_search_page()) { ?>
  <?php osc_run_hook('home_search_pre'); ?>
  
  <div id="home-search">
    <div class="inside">

      <div class="box">
        <div class="wrap">
          <form action="<?php echo osc_base_url(true); ?>" method="GET" class="nocsrf" id="home-form" >
            <input type="hidden" name="page" value="search" />
            <input type="hidden" name="sCategory" id="sCategory" value="<?php echo Params::getParam('sCategory'); ?>"/>
            <input type="hidden" name="sCountry" id="sCountry" value="<?php echo Params::getParam('sCountry'); ?>"/>
            <input type="hidden" name="sRegion" id="sRegion" value="<?php echo Params::getParam('sRegion'); ?>"/>
            <input type="hidden" name="sCity" id="sCity" value="<?php echo Params::getParam('sCity'); ?>"/>

            <?php osc_run_hook('home_search_top'); ?>
            
            <div class="col c1">
              <?php if(osc_is_home_page()) { ?>
                <strong><h1><?php _e('What are you looking for?', 'delta'); ?></h1></strong>
              <?php } else { ?>
                <strong><?php _e('What are you looking for?', 'delta'); ?></strong>
              <?php } ?>
              
              <div id="query-picker" class="query-picker">
                <svg viewBox="0 0 32 32" color="#999" height="18px" width="18px"><defs><path id="mbIconSearch" d="M12.618 23.318c-6.9 0-10.7-3.8-10.7-10.7 0-6.9 3.8-10.7 10.7-10.7 6.9 0 10.7 3.8 10.7 10.7 0 3.458-.923 6.134-2.745 7.955-1.821 1.822-4.497 2.745-7.955 2.745zm17.491 5.726l-7.677-7.678c1.854-2.155 2.804-5.087 2.804-8.748C25.236 4.6 20.636 0 12.618 0S0 4.6 0 12.618c0 8.019 4.6 12.618 12.618 12.618 3.485 0 6.317-.85 8.44-2.531l7.696 7.695 1.355-1.356z"></path></defs><use fill="currentColor" xlink:href="#mbIconSearch" fill-rule="evenodd"></use></svg>
                <input type="text" name="sPattern" class="pattern" placeholder="<?php echo osc_esc_html(__('Ex.: iPhone, car, house…', 'delta')); ?>" value="<?php echo Params::getParam('sPattern'); ?>" autocomplete="off"/>

                <div class="shower-wrap">
                  <div class="shower"></div>
                </div>

                <div class="loader"></div>
              </div>
            </div>


            <div class="col c2">
              <strong><?php _e('In which category?', 'delta'); ?></strong>
              
              <?php
                $category_name = '';
                
                if(osc_is_search_page()) {
                  $search_cat_id = osc_search_category_id();
                  $search_cat_id = isset($search_cat_id[0]) ? $search_cat_id[0] : '';

                  if($search_cat_id > 0) {
                    $category = Category::newInstance()->findByPrimaryKey($search_cat_id);
                    $category_name = (isset($category['s_name']) ? $category['s_name'] : '');
                  }
                }
              ?>

              <div id="category-picker" class="cat-picker">
                <div class="mini-box">
                  <svg class="svg-left" fill="#999" width="18px" height="18px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <g> <g> <path d="M176.792,0H59.208C26.561,0,0,26.561,0,59.208v117.584C0,209.439,26.561,236,59.208,236h117.584 C209.439,236,236,209.439,236,176.792V59.208C236,26.561,209.439,0,176.792,0z M196,176.792c0,10.591-8.617,19.208-19.208,19.208 H59.208C48.617,196,40,187.383,40,176.792V59.208C40,48.617,48.617,40,59.208,40h117.584C187.383,40,196,48.617,196,59.208 V176.792z"/> </g> </g> <g> <g> <path d="M452,0H336c-33.084,0-60,26.916-60,60v116c0,33.084,26.916,60,60,60h116c33.084,0,60-26.916,60-60V60 C512,26.916,485.084,0,452,0z M472,176c0,11.028-8.972,20-20,20H336c-11.028,0-20-8.972-20-20V60c0-11.028,8.972-20,20-20h116 c11.028,0,20,8.972,20,20V176z"/> </g> </g> <g> <g> <path d="M176.792,276H59.208C26.561,276,0,302.561,0,335.208v117.584C0,485.439,26.561,512,59.208,512h117.584 C209.439,512,236,485.439,236,452.792V335.208C236,302.561,209.439,276,176.792,276z M196,452.792 c0,10.591-8.617,19.208-19.208,19.208H59.208C48.617,472,40,463.383,40,452.792V335.208C40,324.617,48.617,316,59.208,316h117.584 c10.591,0,19.208,8.617,19.208,19.208V452.792z"/> </g> </g> <g> <g> <path d="M452,276H336c-33.084,0-60,26.916-60,60v116c0,33.084,26.916,60,60,60h116c33.084,0,60-26.916,60-60V336 C512,302.916,485.084,276,452,276z M472,452c0,11.028-8.972,20-20,20H336c-11.028,0-20-8.972-20-20V336c0-11.028,8.972-20,20-20 h116c11.028,0,20,8.972,20,20V452z"/> </g> </g> </svg>
                  <input type="text" class="term2 category" readonly placeholder="<?php _e('All the categories', 'delta'); ?>" value="<?php echo $category_name; ?>" autocomplete="off" />
                  <svg class="svg-right" viewBox="0 0 32 32" color="#696766" width="12px" height="12px"><defs><path id="mbIconAngle" d="M12.147 25.2c-.462 0-.926-.185-1.285-.556L.57 14.024A2.05 2.05 0 010 12.586c0-.543.206-1.061.571-1.436L10.864.553a1.765 1.765 0 012.62.06c.71.795.683 2.057-.055 2.817l-8.9 9.16 8.902 9.183c.738.76.761 2.024.052 2.815a1.78 1.78 0 01-1.336.612"></path></defs><use fill="currentColor" transform="matrix(0 -1 -1 0 29 24)" xlink:href="#mbIconAngle" fill-rule="evenodd"></use></svg>
                </div>
                
                <div class="shower-wrap">
                  <div class="shower" id="shower">
                  </div>
                </div>

                <div class="loader"></div>
              </div>
            </div>
            
            <div class="col cx isMobile">
              <a href="#" class="btn mbBg3 filter-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="20px" height="20px"><path fill="currentColor" d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm16 400c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h352c8.8 0 16 7.2 16 16v352zm-92-272H224v-40c0-13.2-10.8-24-24-24h-48c-13.2 0-24 10.8-24 24v40H92c-6.6 0-12 5.4-12 12v8c0 6.6 5.4 12 12 12h36v40c0 13.2 10.8 24 24 24h48c13.2 0 24-10.8 24-24v-40h100c6.6 0 12-5.4 12-12v-8c0-6.6-5.4-12-12-12zm-132 64h-32v-96h32v96zm148 96h-20v-40c0-13.2-10.8-24-24-24h-48c-13.2 0-24 10.8-24 24v40H108c-6.6 0-12 5.4-12 12v8c0 6.6 5.4 12 12 12h116v40c0 13.2 10.8 24 24 24h48c13.2 0 24-10.8 24-24v-40h20c6.6 0 12-5.4 12-12v-8c0-6.6-5.4-12-12-12zm-52 64h-32v-96h32v96z" class=""></path></svg>
                <span><?php _e('Filters', 'delta'); ?></span>
              </a>
            </div>
            
            <div class="col c3">
              <strong><?php _e('Where is it?', 'delta'); ?></strong>

              <div id="location-picker" class="loc-picker ctr-<?php echo (del_count_countries() == 1 ? 'one' : 'more'); ?>">
                <div class="mini-box">
                  <svg class="svg-left" viewBox="0 0 32 32" color="#999" width="18px" height="18px"><defs><path id="mbIconMarker" d="M13.457 0c7.918 0 12.457 4.541 12.457 12.457C25.915 19.928 17.53 32 13.457 32 9.168 32 1 20.317 1 12.457 1 4.541 5.541 0 13.457 0zm0 30c2.44 0 10.457-10.658 10.457-17.543C23.915 5.616 20.299 2 13.457 2 6.617 2 3 5.616 3 12.457 3 19.649 10.802 30 13.457 30zm0-13.309a4.38 4.38 0 01-4.375-4.375 4.38 4.38 0 014.375-4.376 4.38 4.38 0 014.375 4.376 4.38 4.38 0 01-4.375 4.375zm0-10.75a6.382 6.382 0 00-6.375 6.375 6.382 6.382 0 006.375 6.375 6.382 6.382 0 006.375-6.375 6.382 6.382 0 00-6.375-6.376"></path></defs><use fill="currentColor" xlink:href="#mbIconMarker" fill-rule="evenodd" transform="translate(3)"></use></svg>
                  <input type="text" class="term location" placeholder="<?php _e('Country, region or city', 'delta'); ?>" value="<?php echo del_get_term(Params::getParam('term'), Params::getParam('sCountry'), Params::getParam('sRegion'), Params::getParam('sCity')); ?>" autocomplete="off" data-alt-placeholder="<?php echo osc_esc_html(__('Type to filter results', 'delta')); ?>"/>
                  <svg class="svg-right" viewBox="0 0 32 32" color="#696766" width="12px" height="12px"><defs><path id="mbIconAngle" d="M12.147 25.2c-.462 0-.926-.185-1.285-.556L.57 14.024A2.05 2.05 0 010 12.586c0-.543.206-1.061.571-1.436L10.864.553a1.765 1.765 0 012.62.06c.71.795.683 2.057-.055 2.817l-8.9 9.16 8.902 9.183c.738.76.761 2.024.052 2.815a1.78 1.78 0 01-1.336.612"></path></defs><use fill="currentColor" transform="matrix(0 -1 -1 0 29 24)" xlink:href="#mbIconAngle" fill-rule="evenodd"></use></svg>
                </div>
                
                <div class="shower-wrap">
                  <div class="shower" id="shower">
                    <?php echo del_def_location(); ?>
                  </div>
                </div>

                <div class="loader"></div>
              </div>
            </div>

            <div class="col c4">
              <strong>&nbsp;</strong>
              <button type="submit" class="btn mbBg">
                <svg fill="#fff" width="18px" height="18px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve"> <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23 s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92 c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17 s-17-7.626-17-17S14.61,6,23.984,6z"/> </svg>
                <span class="isMobile"><?php _e('Search', 'delta'); ?></span>
              </button>
            </div>

            <?php osc_run_hook('home_search_bottom'); ?>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <?php osc_run_hook('home_search_after'); ?>
  

  <div class="xmodal category" style="display:none;">
    <div class="xclose"><svg height="329pt" viewBox="0 0 329.26933 329" width="329pt" xmlns="http://www.w3.org/2000/svg"><path d="m194.800781 164.769531 128.210938-128.214843c8.34375-8.339844 8.34375-21.824219 0-30.164063-8.339844-8.339844-21.824219-8.339844-30.164063 0l-128.214844 128.214844-128.210937-128.214844c-8.34375-8.339844-21.824219-8.339844-30.164063 0-8.34375 8.339844-8.34375 21.824219 0 30.164063l128.210938 128.214843-128.210938 128.214844c-8.34375 8.339844-8.34375 21.824219 0 30.164063 4.15625 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921875-2.089844 15.082031-6.25l128.210937-128.214844 128.214844 128.214844c4.160156 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921874-2.089844 15.082031-6.25 8.34375-8.339844 8.34375-21.824219 0-30.164063zm0 0"/></svg></div>
    
    <div id="cat-box" class="xcontent">
      <div class="side">
        <div class="wrap">
          <?php 
            osc_goto_first_category(); 
            $i = 0;
          ?>
          <?php while(osc_has_categories()) { ?>
            <?php 
              $search_params['sCategory'] = osc_category_id(); 
              $color = del_get_cat_color(osc_category_id());
              $i++;
            ?>
     
            <a href="<?php echo osc_search_url($search_params); ?>" class="cat1<?php if($i == 1) { ?> active<?php } ?>" data-id="<?php echo osc_category_id(); ?>">
              <h3><?php echo osc_category_name(); ?></h3>
              <span style="background:<?php echo ($color != '' ? $color : '#333'); ?>;"></span>
            </a>
          <?php } ?>
        </div>
        
        <?php unset($search_params['sCategory']); ?>
        <a href="<?php echo osc_search_url($search_params); ?>" class="allcat"><?php _e('Search in all categories', 'delta'); ?></a>
      </div>
      
      <div class="box">
        <?php osc_goto_first_category(); ?>
        <?php while(osc_has_categories()) { ?>
          <?php 
            $search_params['sCategory'] = osc_category_id(); 
            $color = del_get_cat_color(osc_category_id());
          ?>
   
          <a href="<?php echo osc_search_url($search_params); ?>" class="cat1" data-id="<?php echo osc_category_id(); ?>" data-color="<?php echo $color; ?>">
            <div>
              <?php if(del_param('cat_icons') == 1) { ?>
                <i class="fas <?php echo del_get_cat_icon(osc_category_id(), true); ?>" <?php if($color <> '') { ?>style="color:<?php echo $color; ?>;"<?php } ?>></i>
              <?php } else { ?>
                <?php echo del_get_cat_icon(osc_category_id()); ?>
              <?php } ?>
            </div>

            <h3><?php echo osc_category_name(); ?></h3>
          </a>
          
          <div class="sub-box">
            <?php while(osc_has_subcategories()) { ?>
              <?php $search_params['sCategory'] = osc_category_id(); ?>
              <div class="link"><a href="<?php echo osc_search_url($search_params); ?>" class="cat2" data-id="<?php echo osc_category_id(); ?>"><?php echo osc_category_name(); ?></a></div>
            <?php } ?>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
<?php } ?>	