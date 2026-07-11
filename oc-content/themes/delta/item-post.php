<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo del_language_dir(); ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">

<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />

  <?php if(osc_images_enabled_at_items()) { ItemForm::photos_javascript(); } ?>
</head>

<?php
  $action = 'item_add_post';
  $edit = false;

  if(Params::getParam('action') == 'item_edit') {
    $action = 'item_edit_post';
    $edit = true;
  }
  
  if(method_exists('ItemForm', 'contact_phone_text')) {
    $phone_name = 'contactPhone';
  } else {
    $phone_name = 'sPhone';
  }

  $countries = Country::newInstance()->listAll();
  $user = osc_user();

  if(!$edit) {
    $prepare = array();
    $prepare['s_contact_name'] = osc_user_name();
    $prepare['s_contact_email'] = osc_user_email();
    $prepare['s_zip'] = osc_user_zip();
    $prepare['s_city_area'] = osc_user_city_area();
    $prepare['s_address'] = osc_user_address();
    $prepare['i_country'] = del_get_session('sCountry') <> '' ? del_get_session('sCountry') : osc_user_field('fk_c_country_code');
    $prepare['i_region'] = del_get_session('sRegion') <> '' ? del_get_session('sRegion') : osc_user_region_id();
    $prepare['i_city'] = del_get_session('sCity') <> '' ? del_get_session('sCity') : osc_user_city_id();
    $prepare['s_phone'] = del_get_session($phone_name) <> '' ? del_get_session($phone_name) : osc_user_phone();
    $prepare['s_contact_phone'] = $prepare['s_phone'];
    $prepare['i_category'] = del_get_session('catId') <> '' ? del_get_session('catId') : '';
    
  } else {

    $item_extra = del_item_extra(osc_item_id());

    $prepare = osc_item();
    $prepare['i_country'] = del_get_session('sCountry') <> '' ? del_get_session('sCountry') : osc_item_country_code();
    $prepare['i_region'] = del_get_session('sRegion') <> '' ? del_get_session('sRegion') : osc_item_region_id();
    $prepare['i_city'] = del_get_session('sCity') <> '' ? del_get_session('sCity') : osc_item_city_id();
    $prepare['s_phone'] = del_get_session($phone_name) <> '' ? del_get_session($phone_name) : @$item_extra['s_phone'];
    $prepare['i_category'] = del_get_session('catId') <> '' ? del_get_session('catId') : osc_item_category_id();

  }

  if($prepare['i_category'] > 0) {
    $cat = Category::newInstance()->findByPrimaryKey($prepare['i_category']);
  }

  $required_fields = strtolower(del_param('post_required'));


  $price_type = '';

  if($edit) {
    if(osc_item_price() === null) {
      $price_type = 'CHECK';
    } else if(osc_item_price() == 0) {
      $price_type = 'FREE';
    } else {
      $price_type = 'PAID';
    }
  }
?>



<body id="body-item-post">
  <?php osc_current_web_theme_path('header.php') ; ?>

  <div class="inside add_item post-edit">
    <h1><?php echo (!$edit ? __('Publish a new listing', 'delta') : __('Edit listing', 'delta')); ?></h1>

    <ul id="error_list" class="new-item"></ul>

    <form name="item" action="<?php echo osc_base_url(true);?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="<?php echo $action; ?>" />
      <input type="hidden" name="page" value="item" />
      <?php if($edit) { ?><input type="hidden" name="id" value="<?php echo osc_item_id(); ?>" /><?php } ?>
      <?php if($edit) { ?><input type="hidden" name="secret" value="<?php echo osc_item_secret(); ?>" /><?php } ?>
      <input type="hidden" name="countryId" id="sCountry" class="sCountry" value="<?php echo $prepare['i_country']; ?>"/>
      <input type="hidden" name="regionId" id="sRegion" class="sRegion" value="<?php echo $prepare['i_region']; ?>"/>
      <input type="hidden" name="cityId" id="sCity" class="sCity" value="<?php echo $prepare['i_city']; ?>"/>

      <?php osc_run_hook('item_publish_top'); ?>

      <fieldset class="s1">
        <h2><?php _e('Category', 'delta'); ?></h2>
 
        <div class="in">
          <!-- CATEGORY -->
          <?php $category_type = (del_param('publish_category') == '' ? 1 : del_param('publish_category')); ?>

          <?php if($category_type == 1) { ?>

            <div class="row category flat">
              <label for="catId"><span><?php _e('Select a category', 'delta'); ?></span><span class="req">*</span></label>
              <div class="input-box"><?php echo del_simple_category(false, 3, 'catId'); ?></div>
            </div>

          <?php } else if($category_type == 2) { ?>

            <div class="row category multi">
              <label for="catId"><span><?php _e('Category', 'delta'); ?></span><span class="req">*</span></label>
              <?php ItemForm::category_multiple_selects(null, Params::getParam('sCategory'), __('Select a category', 'delta')); ?>
            </div>

          <?php } else if($category_type == 3) { ?>

            <div class="row category simple">
              <label for="catId"><span><?php _e('Category', 'delta'); ?></span><span class="req">*</span></label>
              <?php ItemForm::category_select(null, Params::getParam('sCategory'), __('Select a category', 'delta')); ?>
            </div>

          <?php } else if ($category_type == 4) { ?>
            <input type="hidden" id="catId" name="catId" value="<?php echo $prepare['i_category']; ?>"/>

            <div id="category-picker" class="cat-picker picker-v2">
              <label for="term3"><span><?php _e('Select a category', 'delta'); ?></span><span class="req">*</span></label>

              <div class="mini-box">
                <input type="text" class="term3" id="term3" placeholder="<?php _e('Category', 'delta'); ?>"  autocomplete="off" value="<?php echo (@$cat['s_name'] <> '' ? $cat['s_name'] : ''); ?>" readonly/>
                <i class="fa fa-angle-down"></i>
              </div>

              <div class="shower-wrap">
                <div class="shower" id="shower">
                  <?php echo del_catbox_short($prepare['i_category']); ?>
                  <a href="#" class="btn btn-primary mbBg cat-confirm isMobile"><i class="far fa-check-square"></i> <?php _e('Submit', 'delta'); ?></a>

                  <div class="button-wrap isTablet isDesktop">
                    <a href="#" class="btn btn-primary mbBg cat-confirm"><i class="far fa-check-square"></i> <?php _e('Submit', 'delta'); ?></a>
                  </div>
                </div>
              </div>

              <div class="loader"></div>
            </div>
          <?php } ?>
          
          <?php osc_run_hook('item_publish_category'); ?>
        </div>
      </fieldset>


      <fieldset class="s2">
        <h2><?php _e('Location', 'delta'); ?></h2>

        <div class="in">
          <div id="location-picker" class="loc-picker picker-v2 ctr-<?php echo (del_count_countries() == 1 ? 'one' : 'more'); ?>">
            <label for="term2"><span><?php _e('Where is your item located?', 'delta'); ?></span><span class="req">*</span></label>

            <div class="mini-box">
              <input type="text" id="term2" class="term2" placeholder="<?php _e('City/Region', 'delta'); ?>" value="<?php echo del_get_term('', $prepare['i_country'], $prepare['i_region'], $prepare['i_city']); ?>" autocomplete="off" readonly/>
              <i class="fa fa-angle-down"></i>
            </div>
            
            <?php $countries = Country::newInstance()->listAll(); ?>

            <div class="shower-wrap">
              <div id="shower" class="shower <?php if(is_array($countries) && count($countries) > 1) { ?>multi-country<?php } ?>">
                <?php echo del_locbox_short($prepare['i_country'], $prepare['i_region'], $prepare['i_city']); ?>
                <a href="#" class="btn btn-primary mbBg loc-confirm isMobile"><i class="far fa-check-square"></i> <?php _e('Submit', 'delta'); ?></a>

                <div class="button-wrap isTablet isDesktop">
                  <a href="#" class="btn btn-primary mbBg loc-confirm"><i class="far fa-check-square"></i> <?php _e('Submit', 'delta'); ?></a>
                </div>
              </div>
            </div>

            <div class="loader"></div>
          </div>


          <div class="loc-more">
            <div class="row city-area">
              <label for="address"><?php _e('City Area', 'delta'); ?></label>
              <div class="input-box"><?php ItemForm::city_area_text($prepare); ?></div>
            </div>

            <div class="row address">
              <label for="address"><?php _e('Address', 'delta'); ?></label>
              <div class="input-box"><?php ItemForm::address_text($prepare); ?></div>
            </div>

            <div class="row zip">
              <label for="address"><?php _e('ZIP', 'delta'); ?></label>
              <div class="input-box"><?php ItemForm::zip_text($prepare); ?></div>
            </div>
          </div>
          
          <?php osc_run_hook('item_publish_location'); ?>
        </div>
      </fieldset>

      <fieldset class="s3">
        <h2>
          <?php _e('About you', 'delta'); ?>
        </h2>

        <div class="in">
          <div class="seller<?php if(osc_is_web_user_logged_in() ) { ?> logged<?php } ?>">
            <div class="row name">
              <label for="contactName"><?php _e('Your Name', 'delta'); ?><?php if(strpos($required_fields, 'name') !== false) { ?><span class="req">*</span><?php } ?></label>
              <div class="input-box"><?php ItemForm::contact_name_text($prepare); ?></div>
            </div>
          
            <div class="row phone">
              <label for="phone"><?php _e('Mobile Phone', 'delta'); ?><?php if(strpos($required_fields, 'phone') !== false) { ?><span class="req">*</span><?php } ?></label>
              <div class="input-box">
                <?php if(method_exists('ItemForm', 'contact_phone_text')) { ?>
                  <?php ItemForm::contact_phone_text($prepare); ?>
                <?php } else { ?>
                  <input type="tel" id="sPhone" name="sPhone" value="<?php echo $prepare['s_phone']; ?>" />
                <?php } ?>
              </div>
              
              <?php if(method_exists('ItemForm', 'show_phone_checkbox')) { ?>
                <div class="mail-show">
                  <div class="input-box-check">
                    <?php ItemForm::show_phone_checkbox() ; ?>
                    <label for="showPhone" class="label-mail-show"><?php _e('Phone visible on ad', 'delta'); ?></label>
                  </div>
                </div>
              <?php } ?>
            </div>

            <div class="row user-email">
              <label for="contactEmail"><span><?php _e('E-mail', 'delta'); ?></span><span class="req">*</span></label>
              <div class="input-box"><?php ItemForm::contact_email_text($prepare); ?></div>

              <div class="mail-show">
                <div class="input-box-check">
                  <?php ItemForm::show_email_checkbox() ; ?>
                  <label for="showEmail" class="label-mail-show"><?php _e('Email visible on ad', 'delta'); ?></label>
                </div>
              </div>
            </div>
          </div>

          <div class="row user-link">
            <?php if(osc_is_web_user_logged_in()) { ?>
              <?php _e('You can update your data in', 'delta'); ?> <a target="_blank" href="<?php echo osc_user_profile_url(); ?>"><?php _e('Your Profile', 'delta'); ?></a> <?php _e('section', 'delta'); ?>
            <?php } else { ?>
              <?php _e('Not registered yet?', 'delta'); ?> <a target="_blank" href="<?php echo osc_register_account_url(); ?>"><?php _e('Sign-up', 'delta'); ?></a> <?php _e('and publish listings faster', 'delta'); ?>
            <?php } ?>
          </div>
          
          <?php osc_run_hook('item_publish_seller'); ?>
        </div>
      </fieldset>


      <fieldset class="s4">
        <div class="in">
          <!-- PRICE -->
          <?php if(osc_price_enabled_at_items()) { ?>
            <label for="price"><?php _e('Price', 'delta'); ?></label>

            <div class="price-wrap">
              <div class="inside">
                <div class="enter<?php if($price_type == 'FREE' || $price_type == 'CHECK') { ?> disable<?php } ?>">
                  <div class="input-box">
                    <?php ItemForm::price_input_text(); ?>
                    <?php echo del_simple_currency(); ?>
                  </div>

                  <div class="or"><?php _e('or', 'delta'); ?></div>
                </div>
                
                <div class="selection">
                  <a href="#" data-price="0" <?php if($price_type == 'FREE') { ?>class="active"<?php } ?> title="<?php echo osc_esc_html(__('Item is offered for free', 'delta')); ?>"><span class="isTablet isDesktop"><?php _e('Free', 'delta'); ?></span><span class="isMobile"><?php _e('Item for free', 'delta'); ?></span></a>
                  <a href="#" data-price="" <?php if($price_type == 'CHECK') { ?>class="active"<?php } ?> title="<?php echo osc_esc_html(__('Based on agreement with seller', 'delta')); ?>"><span class="isTablet isDesktop"><?php _e('Deal', 'delta'); ?></span><span class="isMobile"><?php _e('Check with seller', 'delta'); ?></span></a>
                </div>
              </div>
            </div>
          <?php } ?>


          <!-- CONDITION & TRANSACTION -->
          <div class="status-wrap">
            <div class="transaction">
              <label for="sTransaction"><?php _e('Transaction', 'delta'); ?></label>
              <?php echo del_simple_transaction(); ?>
            </div>

            <div class="condition">
              <label for="sCondition"><?php _e('Condition', 'delta'); ?></label>
              <?php echo del_simple_condition(); ?>
            </div>
          </div>
          
          <?php osc_run_hook('item_publish_price'); ?>
        </div>
      </fieldset>


      <fieldset class="s5">
        <h2><?php _e('Description', 'delta'); ?></h2>

        <div class="in">
          <div class="title-desc-box">
            <label for="title[<?php echo osc_current_user_locale(); ?>]"><?php _e('Title', 'delta'); ?> *</label>
            <div class="td-wrap t1 row">
              <?php ItemForm::title_input('title', osc_current_user_locale(), osc_esc_html(del_post_item_title())); ?>
            </div>
            
            <label for="description[<?php echo osc_current_user_locale(); ?>]"><?php _e('Description', 'delta'); ?> *</label>
            <div class="td-wrap d1 row">
              <?php ItemForm::description_textarea('description', osc_current_user_locale(), osc_esc_html(del_post_item_description())); ?>
            </div>
          </div>
          
          <?php osc_run_hook('item_publish_description'); ?>
        </div>
      </fieldset>


      <fieldset class="photos">

        <div class="box photos photoshow drag_drop in">
          <div id="photos">
            <label><?php _e('Photos', 'delta'); ?></label>

            <div class="sub-label"><?php echo sprintf(__('You can upload up to %d pictures per listing', 'delta'), osc_max_images_per_item()); ?></div>

            <?php 
              if(osc_images_enabled_at_items()) { 
                if(del_ajax_image_upload()) { 
                  ItemForm::ajax_photos();
                } 
              } 
            ?>
          </div>
          
          <?php osc_run_hook('item_publish_images'); ?>
        </div>
      </fieldset>
 

      <fieldset class="hook-block">
        <h2><?php _e('Listing details & attributes', 'delta'); ?></h2>

        <div id="post-hooks" class="in">
          <?php
            if($edit) {
              ItemForm::plugin_edit_item();
            } else {
              ItemForm::plugin_post_item();
            }
          ?>
          
          <?php osc_run_hook('item_publish_hook'); ?>
        </div>
      </fieldset>


      <div class="buttons-block">
        <div class="inside">
          <div class="box">
            <div class="row">
              <?php osc_run_hook('item_publish_bottom'); ?>
              <?php del_show_recaptcha(); ?>
            </div>
          </div>

          <div class="clear"></div>

          <button type="submit" class="btn mbBg"><?php echo (!$edit ? __('Publish item', 'delta') : __('Save changes', 'delta')); ?></button>

          <?php osc_run_hook('item_publish_buttons'); ?>
        </div>
      </div>
      
      <?php osc_run_hook('item_publish_after'); ?>
    </form>
  </div>


  <script type="text/javascript">
  $(document).ready(function(){  
    $('body').on('click', '.qq-upload-rotate', function(e){
      e.preventDefault();


      var img = $(this).parent().find('.ajax_preview_img img');
      var url = '<?php echo osc_current_web_theme_url('ajax-rotate.php'); ?>',
      angle = parseInt(img.attr('data-angle'));

      var imgWidth = img.width();
      var imgHeight = img.height();
      var ratio = imgWidth/imgHeight;
      var boxWidth = img.parent().width();

      if(!img.hasClass('disabled')) {

        if(isNaN(angle)){
          angle = 0
        }
         
        angle += 90;
      
        img.addClass('disabled');

        img.rotate({ 
          animateTo: angle,
          duration: 300,
          callback: function() {
            $.ajax({
              url: url,
              type: 'POST',
              data: {
                'action': 'rotate', 
                'file_name' : img.attr('alt')
              },
              success: function(response) {
                //console.log(response);
                img.removeClass('disabled');
              },
              error: function(response) {
                //console.log(response);
                img.removeClass('disabled');
              }
            });
          }
        });
      }
      
      img.attr('data-angle', angle);
    });
  });
  </script>

  <script type="text/javascript">
  $(document).ready(function(){
    $('.add_item input[name^="title"]').attr('placeholder', '<?php echo osc_esc_js(__('Summarize your offer', 'delta')); ?>');
    $('.add_item textarea[name^="description"]').attr('placeholder', '<?php echo osc_esc_js(__('Detail description of your offer', 'delta')); ?>');
    $('.add_item input[name="contactPhone"]').prop('type', 'tel');

    // HIDE THEME EXTRA FIELDS (Transaction, Condition, Status) ON EXCLUDED CATEGORIES 
    var catExtraAlpHide = new Array();
    <?php 
      $e_array = del_extra_fields_hide();

      if(!empty($e_array) && count($e_array) > 0) {
        foreach($e_array as $e) {
          if(is_numeric($e)) {
            echo 'catExtraAlpHide[' . $e . '] = 1;';
          }
        }
      }
    ?>

    <?php if(!$edit) { ?>
      $('input[name="showPhone"]').prop('checked', true);
    <?php } ?>
    
    <?php if(osc_is_web_user_logged_in()) { ?>
      // SET READONLY FOR EMAIL AND NAME FOR LOGGED IN USERS
      $('input[name="contactName"]').attr('readonly', true);
      $('input[name="contactEmail"]').attr('readonly', true);
    <?php } ?>


    <?php if ($edit && !osc_item_category_price_enabled(osc_item_category_id())) { ?>
       $('.post-edit .price-wrap').fadeOut(200).addClass('hidden');
       $('#price').val('') ;
    <?php } ?>



    // JAVASCRIPT FOR PRICE ALTERNATIVES
    $('input#price').attr('autocomplete', 'off');         // Disable autocomplete for price field
    $('input#price').attr('placeholder', '<?php echo osc_esc_js(__('Price', 'delta')); ?>');         


    // LANGUAGE TABS
    tabberAutomatic();

    // Trigger click when category selected via flat category select
    $('body').on('click change', 'input[name="catId"], select#catId', function() {
      var cat_id = $(this).val();
      var url = '<?php echo osc_base_url(); ?>index.php';
      var result = '';

      if(cat_id > 0) {
        if(catPriceEnabled[cat_id] == 1) {
          $('.post-edit .price-wrap').fadeIn(200).removeClass('hidden');
        } else {
          $('.post-edit .price-wrap').fadeOut(200).addClass('hidden');
          $('#price').val('') ;
        }
       

        if(catExtraAlpHide[cat_id] == 1) {
          $(".add_item .status-wrap").fadeOut(200).addClass('hidden');
          $('input[name="sTransaction"], input[name="sCondition"]').val('');
          $('#sTransaction option, #sCondition option').prop('selected', function() {
            return this.defaultSelected;
          });

          $('.simple-transaction span.text span').text($('.simple-transaction .list .option.bold').text());
          $('.simple-condition span.text span').text($('.simple-condition .list .option.bold').text());
          $('.simple-transaction .list .option, .simple-condition .list .option').removeClass('selected');
          $('.simple-transaction .list .option.bold, .simple-condition .list .option.bold').addClass('selected');

        } else {
          $(".add_item .status-wrap").fadeIn(200).removeClass('hidden');
        }


        if(!$('.post-edit .price-wrap').hasClass('hidden') || !$('.post-edit .status-wrap').hasClass('hidden')) {
          $('.post-edit fieldset.s4').show(0).css('overflow', 'initial');
        } else {
          $('.post-edit fieldset.s4').hide(0);
        }


        // REPLACEMENT FOR AJAX
        window.setTimeout(function() {
          // unify selected locale for plugin data
          var elem = $('.locale-links a.active');
          var locale = elem.attr('data-locale');
          var localeText = elem.attr('data-name');

          if($('#plugin-hook .tabbertab').length > 0) {
            $('#plugin-hook .tabbertab').each(function() {
              if($(this).find('[id*="' + locale + '"]').length || $(this).find('h2').text() == localeText) {
                $(this).removeClass('tabbertabhide').show(0);
              } else {
                $(this).addClass('tabbertabhide').hide(0);
              }
            });
          }
        }, 250);


        // AJAX CALLS HANDLED BY OSCLASS
        // DISABLED
        if(1==2) {
          $.ajax({
            type: "POST",
            url: url,
            data: 'page=ajax&action=runhook&hook=item_form&catId=' + cat_id,
            dataType: 'html',
            success: function(data){
              $('#plugin-hook').html(data);

              // unify selected locale for plugin data
              var elem = $('.locale-links a.active');
              var locale = elem.attr('data-locale');
              var localeText = elem.attr('data-name');

              if($('#plugin-hook .tabbertab').length > 0) {
                $('#plugin-hook .tabbertab').each(function() {
                  if($(this).find('[id*="' + locale + '"]').length || $(this).find('h2').text() == localeText) {
                    $(this).removeClass('tabbertabhide').show(0);
                  } else {
                    $(this).addClass('tabbertabhide').hide(0);
                  }
                });
              }
            }
          });
        }
      }
    });



    // FIX QQ FANCY IMAGE UPLOADER BUGS
    setInterval(function(){ 
      $('input[name="qqfile"]').prop('accept', 'image/*');
      $("img[src$='uploads/temp/']").closest('.qq-upload-success').remove();
        
      $('#restricted-fine-uploader li.qq-upload-success').each(function() {
        if(!$(this).find('.qq-upload-rotate').length) {
          $(this).append('<a class="qq-upload-rotate mbBg" href="#" title="<?php echo osc_esc_js(__('Rotate image', 'delta')); ?>"><i class="fas fa-undo fa-flip-horizontal"></i></a>');
        }
      })

      if( !$('#photos > .qq-upload-list > li').length ) {
        $('#photos > .qq-upload-list').remove();
        $('#photos > h3').remove();
      }

      if($('#post-hooks #plugin-hook').text().trim() == '') {
        $('fieldset.hook-block').hide(0);
      } else {
        $('fieldset.hook-block').show(0);
      }
      
      $('#error_list li label[for="qqfile"]').each(function() {
        if($(this).text().trim() == '<?php echo osc_esc_js(__('Please enter a value with a valid extension.')); ?>') {
          $(this).parent().remove();
        }
      });
    }, 250);



    // CATEGORY CHECK IF PARENT
    <?php if(!osc_selectable_parent_categories()) { ?>
      if(typeof window['categories_' + $('#catId').val()] !== 'undefined'){
        if(eval('categories_' + $('#catId').val()) != '') {
          $('#catId').val('');
        }
      }

      $('body').on('change', '#catId', function(){
        if(typeof window['categories_' + $(this).val()] !== 'undefined'){
          if(eval('categories_' + $(this).val()) != '') {
            $(this).val('');
          }
        }
      });
    <?php } ?>



    // Set forms to active language
    var post_timer = setInterval(del_check_lang, 250);

    function del_check_lang() {
      if($('.tabbertab').length > 1 && $('.tabbertab.tabbertabhide').length) {
        var l_active = delCurrentLocale;
        l_active = l_active.trim();

        $('.tabbernav > li > a:contains("' + l_active + '")').click();

        clearInterval(post_timer);
        return;
      }
    }



    // Code for form validation
    $("form[name=item]").validate({
      rules: {
        "title[<?php echo osc_current_user_locale(); ?>]": {
          required: true,
          minlength: 5
        },

        "description[<?php echo osc_current_user_locale(); ?>]": {
          required: true,
          minlength: 10
        },

        <?php if(strpos($required_fields, 'location') !== false) { ?>
        term: {
          required: true,
          minlength: 3
        },
        <?php } ?>


        <?php if(strpos($required_fields, 'country') !== false) { ?>
        countryId: {
          required: true
        },
        <?php } ?>

        <?php if(strpos($required_fields, 'region') !== false) { ?>
        regionId: {
          required: true
        },
        <?php } ?>

        <?php if(strpos($required_fields, 'city') !== false) { ?>
        cityId: {
          required: true
        },
        <?php } ?>

        <?php if(function_exists('ir_get_min_img')) { ?>
        ir_image_check: {
          required: true,
          min: <?php echo ir_get_min_img(); ?>
        },
        <?php } ?>

        catId: {
          required: true,
          digits: true
        },

        "photos[]": {
          accept: "png,gif,jpg,jpeg"
        },

        <?php if(strpos($required_fields, 'name') !== false) { ?>
        contactName: {
          required: true,
          minlength: 3
        },
        <?php } ?>

        <?php if(strpos($required_fields, 'phone') !== false) { ?>
        <?php echo $phone_name; ?>: {
          required: true,
          minlength: 6
        },
        <?php } ?>

        contactEmail: {
          required: true,
          email: true
        }
      },

      messages: {
        "title[<?php echo osc_current_user_locale(); ?>]": {
          required: '<?php echo osc_esc_js(__('Title: this field is required.', 'delta')); ?>',
          minlength: '<?php echo osc_esc_js(__('Title: enter at least 5 characters.', 'delta')); ?>'
        },

        "description[<?php echo osc_current_user_locale(); ?>]": {
          required: '<?php echo osc_esc_js(__('Description: this field is required.', 'delta')); ?>',
          minlength: '<?php echo osc_esc_js(__('Description: enter at least 10 characters.', 'delta')); ?>'
        },

        <?php if(strpos($required_fields, 'location') !== false) { ?>
        term: {
          required: '<?php echo osc_esc_js(__('Location: select country, region or city.', 'delta')); ?>',
          minlength: '<?php echo osc_esc_js(__('Location: enter at least 3 characters to get list.', 'delta')); ?>'
        },
        <?php } ?>

        <?php if(strpos($required_fields, 'country') !== false) { ?>
        countryId: {
          required: '<?php echo osc_esc_js(__('Location: select country from location field.', 'delta')); ?>'
        },
        <?php } ?>

        <?php if(strpos($required_fields, 'region') !== false) { ?>
        regionId: {
          required: '<?php echo osc_esc_js(__('Location: select region from location field.', 'delta')); ?>'
        },
        <?php } ?>

        <?php if(strpos($required_fields, 'city') !== false) { ?>
        cityId: {
          required: '<?php echo osc_esc_js(__('Location: select city from location field.', 'delta')); ?>'
        },
        <?php } ?>

        <?php if(function_exists('ir_get_min_img')) { ?>
        ir_image_check: {
          required: '<?php echo osc_esc_js(__('Pictures: you need to upload pictures.', 'delta')); ?>',
          min: '<?php echo osc_esc_js(__('Pictures: upload at least.', 'delta') . ' ' . ir_get_min_img() . ' ' . __('picture(s).', 'delta')); ?>'
        },
        <?php } ?>

        catId: '<?php echo osc_esc_js(__('Category: this field is required.', 'delta')); ?>',

        "photos[]": {
           accept: '<?php echo osc_esc_js(__('Photo: must be png,gif,jpg,jpeg.', 'delta')); ?>'
        },

        <?php if(strpos($required_fields, 'phone') !== false) { ?>
        <?php echo $phone_name; ?>: {
          required: '<?php echo osc_esc_js(__('Phone: this field is required.', 'delta')); ?>',
          minlength: '<?php echo osc_esc_js(__('Phone: enter at least 6 characters.', 'delta')); ?>'
        },
        <?php } ?>

        <?php if(strpos($required_fields, 'name') !== false) { ?>
        contactName: {
          required: '<?php echo osc_esc_js(__('Your Name: this field is required.', 'delta')); ?>',
          minlength: '<?php echo osc_esc_js(__('Your Name: enter at least 3 characters.', 'delta')); ?>'
        },
        <?php } ?>

        contactEmail: {
          required: '<?php echo osc_esc_js(__('Email: this field is required.', 'delta')); ?>',
          email: '<?php echo osc_esc_js(__('Email: invalid format of email address.', 'delta')); ?>'
        }
      },

      ignore: ":disabled, :hidden, .ignore",
      ignoreTitle: false,
      errorLabelContainer: "#error_list",
      wrapper: "li",
      invalidHandler: function(form, validator) {
        $('html,body').animate({ scrollTop: $('body').offset().top}, { duration: 250, easing: 'swing'});
      },
      submitHandler: function(form){
        $('button[type=submit], input[type=submit]').attr('disabled', 'disabled');
        form.submit();
      }
    });
  });
  </script>


  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>	