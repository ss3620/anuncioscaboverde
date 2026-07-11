<?php
define('DELTA_THEME_VERSION', '1.6.8');
define('USER_MENU_ICONS', 1);
define('THEME_COMPATIBLE_WITH_OSCLASS_HOOKS', 820);       // Compatibility with new hooks up to version


require_once osc_base_path() . 'oc-content/themes/delta/model/ModelDEL.php';

function del_theme_info() {
  return array(
    'name' => 'Delta Osclass Theme',
    'version' => '1.6.8',
    'description' => 'Premium front-end theme for classifieds script Osclass',
    'author_name' => 'MB Themes',
    'author_url' => 'https://osclasspoint.com',
    'support_uri' => 'https://forums.osclasspoint.com/delta-osclass-theme/',
    'locations' => array('header', 'footer')
  );
}


// GET FOOTER SOCIAL LINK
function del_get_social_link($type) {
  $url = '';
  
  if(del_param('footer_social_define') == 1) {
    switch($type) {
      case 'whatsapp': $url = del_param('footer_social_whatsapp'); break;
      case 'facebook': $url = del_param('footer_social_facebook'); break;
      case 'pinterest': $url = del_param('footer_social_pinterest'); break;
      case 'instagram': $url = del_param('footer_social_instagram'); break;
      case 'x': $url = del_param('footer_social_x'); break;
      case 'linkedin': $url = del_param('footer_social_linkedin'); break;
    }
    
  } else {
    $share_url = urlencode(osc_is_ad_page() ? osc_item_url() : osc_base_url());

    switch($type) {
      case 'whatsapp': $url = 'whatsapp://send?text=' . $share_url; break;
      case 'facebook': $url = 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url; break;
      case 'pinterest': $url = 'https://pinterest.com/pin/create/button/?url=' . $share_url . '&media=' . del_logo(true) . '&description='; break;
      case 'instagram': $url = del_param('footer_social_instagram'); break;
      case 'x': $url = 'https://twitter.com/home?status=' . $share_url . '%20-%20' . urlencode(del_param('website_name')); break;
      case 'linkedin': $url = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $share_url . '&title=' . urlencode(del_param('website_name')) . '&summary=&source='; break;
    }
  }

  if(trim($url) != '') {
    return $url;
  }
  
  return false;  
}

// RTL LANGUAGE SUPPORT
function del_is_rtl() {
  $current_lang = strtolower(osc_current_user_locale());
  $locale = osc_get_current_user_locale();
  
  if(isset($locale['b_rtl']) && $locale['b_rtl'] == 1) {
    return true;
  } else if(in_array(osc_current_user_locale(), del_rtl_languages())) {
    return true;
  } else {
    return false;
  }
}

// GET DIRECTION STRING
function del_language_dir() {
  return del_is_rtl() ? 'rtl' : 'ltr';
}

// LIST ALL RTL LANGUAGES/LOCALES FOR OLDER OSCLASS VERSIONS
function del_rtl_languages() {
  $langs = array('ar_LB','ar_DZ','ar_BH','ar_EG','ar_IQ','ar_JO','ar_KW','ar_LY','ar_MA','ar_OM','ar_SA','ar_SY','fa_IR','ar_TN','ar_AE','ar_YE','ar_TD','ar_CO','ar_DJ','ar_ER','ar_MR','ar_SD');
  return $langs;
}

// AJAX REQUESTS MANAGEMENT
function del_ajax_manage() {
  if(Params::getParam('ajaxRequest') == 1) {
    error_reporting(0);
    ob_clean();
    osc_current_web_theme_path('ajax.php');
    exit;
  }
}

osc_add_hook('init', 'del_ajax_manage');

// REMOVE OLD FONT AWESOME (V4)
function del_clean_old_fonts() {
  osc_remove_style('font-open-sans');
  osc_remove_style('open-sans');
  osc_remove_style('fi_font-awesome');
  osc_remove_style('font-awesome44');
  osc_remove_style('font-awesome45');
  osc_remove_style('font-awesome47');
  osc_remove_style('cookiecuttr-style');
  osc_remove_style('responsiveslides');
  osc_remove_style('font-awesome');
}

osc_add_hook('init', 'del_clean_old_fonts');
osc_add_hook('header', 'del_clean_old_fonts');


// OSCLASS 4.1 COMPATIBILITY
if(!function_exists('osc_can_deactivate_items')) {
  function osc_can_deactivate_items() {
    return false;
  }
}

if(!function_exists('osc_item_can_renew')) {
  function osc_item_can_renew() {
    return false;
  }
}

if(!function_exists('osc_profile_img_users_enabled')) {
  function osc_profile_img_users_enabled() {
    return false;
  }
}

if(!function_exists('osc_item_show_phone')) {
  function osc_item_show_phone() {
    return true;
  }
}

if(!function_exists('osc_get_current_user_locations_native')) {
  function osc_get_current_user_locations_native() {
    return false;
  }
}

if(!function_exists('osc_location_native_name_selector')) {
  function osc_location_native_name_selector($array, $column = 's_name') {
    return @$array[$column];
  }
}

// SAVE SEARCH SECTION
function del_save_search_section($position) {
?>
  <div id="search-pub" class="pos-<?php echo $position; ?>">
    <div class="info">
      <h3><?php _e('Save this search', 'delta'); ?></h3>
      <div><?php _e('Save this search and get notified when new offers are posted.', 'delta'); ?></div>
    </div>
    
    <div class="buttons">
      <?php osc_alert_form(); ?>
    </div>
  </div>
<?php
}  

// ONLINE CHAT
function del_chat_button($user_id = '') {
  if(function_exists('oc_chat_button')) {
    $html = '';
    $user_name = '';
    $text = '';
    $title = '';

    if((osc_is_ad_page() || osc_is_search_page()) && $user_id == '') {
      $user_id = osc_item_user_id();
      $user_name = osc_item_contact_name();
    }

    if($user_id <> '' && $user_id > 0) {
      $registered = 1;
      $last_active = ModelOC::newInstance()->getUserLastActive($user_id);
      $user = User::newInstance()->findByPrimaryKey($user_id);
      $user_name = @$user['s_name'];

      $active_limit = osc_get_preference('refresh_user', 'plugin-online_chat');
      $active_limit = ($active_limit > 0 ? $active_limit : 120);
      $active_limit = $active_limit + 10;

      $limit_datetime = date('Y-m-d H:i:s', strtotime(' -' . $active_limit . ' seconds', time()));
    } else {
      $registered = 0;
    }

    if($registered == 1 && $user_id <> osc_logged_user_id() && !oc_check_bans($user_id)) {
      $class = ' oc-active';
    } else {
      $class = ' oc-disabled';
    }

    if(isset($limit_datetime) && $limit_datetime <> '' && $last_active >= $limit_datetime) {
      $class .= ' oc-online';
      $title .= __('User is online', 'delta');
    } else {
      $class .= ' oc-offline';
      $title .= __('User is offline', 'delta');
    }


    //$html .= '<div class="row mob oc-chat-box' . $class . '" data-user-id="' . $user_id . '">';
    //$html .= '<i class="fa fa-comment"></i>';



    if($registered == 0) {
      $text .=  __('Chat unavailable', 'delta');
      $title .= ', ' . __('User is not registered', 'delta');
    } else {
      if($user_id == osc_logged_user_id()) {
        $text .= __('Chat unavailable', 'delta');
        $title .= ', ' . __('It\'s your ad', 'delta');
      } else if (oc_check_bans($user_id)) {
        $text .= __('Chat unavailable', 'delta');
        $title .= ', ' . __('User has blocked you', 'delta');
      } else {
        //$text .= '<span class="oc-user-top oc-status-offline">' . __('Chat unavailable', 'delta') . '</span>';
        $text .= '<span class="oc-user-top oc-status-online">' . __('Start chat', 'delta') . '</span>';
      }
    }


    $html .= '<a href="#" class="btn mbBg3 oc-start-chat' . $class . '" data-to-user-id="' . $user_id . '" data-to-user-name="' . osc_esc_html($user_name) . '" data-to-user-image="' . oc_get_picture( $user_id ) . '" title="' . osc_esc_html($title) . '">';
    //$html .= '<svg height="24" viewBox="0 0 512 512" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m512 346.5c0-74.628906-50.285156-139.832031-121.195312-159.480469-4.457032-103.878906-90.347657-187.019531-195.304688-187.019531-107.800781 0-195.5 87.699219-195.5 195.5 0 35.132812 9.351562 69.339844 27.109375 99.371094l-26.390625 95.40625 95.410156-26.386719c27.605469 16.324219 58.746094 25.519531 90.886719 26.90625 19.644531 70.914063 84.851563 121.203125 159.484375 121.203125 29.789062 0 58.757812-7.933594 84.210938-23.007812l80.566406 22.285156-22.285156-80.566406c15.074218-25.453126 23.007812-54.421876 23.007812-84.210938zm-411.136719-15.046875-57.117187 15.800781 15.800781-57.117187-3.601563-5.632813c-16.972656-26.554687-25.945312-57.332031-25.945312-89.003906 0-91.257812 74.242188-165.5 165.5-165.5s165.5 74.242188 165.5 165.5-74.242188 165.5-165.5 165.5c-31.671875 0-62.445312-8.972656-89.003906-25.945312zm367.390625 136.800781-42.382812-11.726562-5.660156 3.683594c-21.941407 14.253906-47.433594 21.789062-73.710938 21.789062-58.65625 0-110.199219-37.925781-128.460938-92.308594 89.820313-10.355468 161.296876-81.832031 171.65625-171.65625 54.378907 18.265625 92.304688 69.808594 92.304688 128.464844 0 26.277344-7.535156 51.769531-21.789062 73.710938l-3.683594 5.660156zm0 0"/><path d="m180.5 271h30v30h-30zm0 0"/><path d="m225.5 150c0 8.519531-3.46875 16.382812-9.765625 22.144531l-35.234375 32.25v36.605469h30v-23.394531l25.488281-23.328125c12.398438-11.347656 19.511719-27.484375 19.511719-44.277344 0-33.085938-26.914062-60-60-60s-60 26.914062-60 60h30c0-16.542969 13.457031-30 30-30s30 13.457031 30 30zm0 0"/></svg>';
    $html .= '<i class="fas fa-comment-alt"></i>';
    $html .= '<span>' . $text . '</span>';
    $html .= '<em class="' . $class . '"></em>';
    $html .= '</a>';

    //$html .= '</div>';

    return $html;
  } else {
    return false;
  }
}


// ON POST/EDIT PAGE TO GET SESSION
function del_post_item_title() {
  $title = osc_item_title();
  foreach( osc_get_locales() as $locale ) {
    if( Session::newInstance()->_getForm('title') != "" ) {
      $title_ = Session::newInstance()->_getForm('title');
      if( @$title_[$locale['pk_c_code']] != "" ){
        $title = $title_[$locale['pk_c_code']];
      }
    }
  }
  return $title;
}


// ON POST/EDIT PAGE TO GET SESSION
function del_post_item_description() {
  $description = osc_item_description();
  foreach( osc_get_locales() as $locale ) {
    if( Session::newInstance()->_getForm('description') != "" ) {
      $description_ = Session::newInstance()->_getForm('description');
      if( @$description_[$locale['pk_c_code']] != "" ){
        $description = $description_[$locale['pk_c_code']];
      }
    }
  }
  return $description;
}


// IDENTIFY DEVICE TYPE
function del_device() {
  if(!isset($_SERVER['HTTP_USER_AGENT'])) {
    return '';
  }
  
  $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
  $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
  $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
  $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
  $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

  //do something with this information
  if($iPod || $iPhone || $iPad) {
    return 'ios';
  } else if($Android) {
    return 'android';
  } else if($webOS) {
    return 'webos';
  }
}


// MASK EMAIL
function del_mask_email($email) {
  $em = explode('@',$email);
  $name = implode('@', array_slice($em, 0, count($em)-1));
  $domain = end($em);

  $len_name = strlen($name)-2;
  $mask_name = substr($name,0, strlen($name) - $len_name) . str_repeat('*', $len_name);
 
  $len_domain = strlen($domain) - 4;
  $mask_domain = str_repeat('*', $len_domain) . substr($domain, $len_domain, strlen($domain));

  return  $mask_name . '@' . $mask_domain;   
}


// PUBLIC PROFILE ITEMS
function del_public_profile_items() {
  $section = osc_get_osclass_section();  
  if(osc_get_osclass_location() == 'user' && ($section == 'items' || $section == 'pub_profile')) {
    Params::setParam('itemsPerPage', del_param('public_items'));
  }
}

osc_add_hook('init', 'del_public_profile_items');


// Osclass 8.3 filter
osc_add_filter('user_public_profile_items_per_page', function($per_page) {
  return del_param('public_items') > 0 ? del_param('public_items') : $per_page;
});


// CHECK IF LAZY LOAD ENABLED
function del_is_lazy($disabled = false) {
  if($disabled === true) {
    return false; 
  } else if(del_param('lazy_load') == 1 && osc_get_preference('force_aspect_image', 'osclass') == 0) {
    return true;
  }

  return false;
}


// GET NO IMAGE LINK
function del_get_noimage($type = 'thumb') {
  if($type == 'thumb') {
    $dim = osc_get_preference('dimThumbnail', 'osclass'); 
  } else if($type == 'large') {
    $dim = '560x420';
  }
  
  if(file_exists(WebThemes::newInstance()->getCurrentThemePath() . 'images/no-image-' . $dim . '.png')) {
    return osc_current_web_theme_url('images/no-image-' . $dim . '.png');
  }

  return osc_current_web_theme_url('images/no-image.png');
}


// HEX TO RGBA COLOR
function del_hex_to_rgb($colour, $opacity = 1) {
  if ( $colour[0] == '#' ) {
    $colour = substr( $colour, 1 );
  }
  
  if (strlen( $colour ) == 6) {
    list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
  } elseif ( strlen( $colour ) == 3 ) {
    list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
  } else {
    return false;
  }
  
  $r = hexdec($r);
  $g = hexdec($g);
  $b = hexdec($b);
  
  return 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $opacity . ')'; 
}


// NEXT - PREV ITEM LINKS
function del_next_prev_item($type, $category_id, $item_id) {
  $mSearch = new Search();
  $mSearch->addCategory($category_id);
  $mSearch->limit(0, 1);
  

  if($type == 'next') {
    $mSearch->addItemConditions(sprintf("%st_item.pk_i_id > %d", DB_TABLE_PREFIX, $item_id));
    $mSearch->order(sprintf("%st_item.pk_i_id", DB_TABLE_PREFIX), 'ASC');
  } else {
    $mSearch->addItemConditions(sprintf("%st_item.pk_i_id < %d", DB_TABLE_PREFIX, $item_id));
    $mSearch->order(sprintf("%st_item.pk_i_id", DB_TABLE_PREFIX), 'DESC');
  }
  
  $aItems = $mSearch->doSearch();

  if(isset($aItems[0])) {
    $item = $aItems[0];
    
    if(isset($item['pk_i_id']) && $item['pk_i_id'] > 0) {
      return osc_item_url_from_item($item);
    }
  }
  
  return false;
}


function del_next_prev_user($type, $user_id) {
  $db_prefix = DB_TABLE_PREFIX;
  
  if($type == 'prev') {
    $cond = '<';
    $order = 'pk_i_id DESC';
  } else if($type == 'next') {
    $cond = '>';
    $order = 'pk_i_id ASC';
  }
  
  $query = "SELECT * FROM {$db_prefix}t_user WHERE pk_i_id {$cond} {$user_id} AND b_enabled = 1 AND b_active = 1 ORDER BY {$order} LIMIT 0,1;";
  $result = User::newInstance()->dao->query($query);
  
  if($result) { 
    $user = $result->row();
    
    if(isset($user['pk_i_id']) && $user['pk_i_id'] > 0) {
      return osc_user_public_profile_url($user['pk_i_id']);
    }
  }
    
  return false;
}


// RELATED ADS
function del_related_ads($by = 'category', $card_type = 'tiny', $limit = 0, $class = '') {
  if($limit <= 0) {
    $limit = (del_param('related_count') > 0 ? del_param('related_count') : 12);
  }
  
  if($limit <= 0) {
    $limit = 12; 
  }
  
  $mSearch = new Search();
  
  $id = 'rel-block';
  
  if($by == 'category') {
    $title = __('Related items', 'delta');
    $mSearch->addCategory(osc_item_category_id());
  } else if ($by == 'user') {
    $title = __('Other items from this seller', 'delta');
    
    if(osc_item_user_id() > 0) {
      $mSearch->fromUser(osc_item_user_id());
    } else {
      $mSearch->addContactEmail(osc_item_contact_email());
    }
    
    $id = 'rel-user-block';
  } else if ($by == 'user-pb-premium') {
    $title = __('Premium', 'delta');
    $mSearch->fromUser(osc_user_id());
    $mSearch->onlyPremium(true);
    $id = 'rel-user-pb-block';
  }  
  
  //$mSearch->withPicture(true); 
  $mSearch->limit(0, $limit);
  
  if ($by != 'user-pb-premium') {
    $mSearch->addItemConditions(sprintf("%st_item.pk_i_id <> %d", DB_TABLE_PREFIX, osc_item_id()));
  }
  
  $aItems = $mSearch->doSearch(); 

  GLOBAL $global_items;
  $global_items = View::newInstance()->_get('items');
  View::newInstance()->_exportVariableToView('items', $aItems); 

  if(osc_count_items() > 0) {
  ?>
    <div id="<?php echo $id; ?>" class="related products grid type-<?php echo $by; ?> <?php echo $class; ?>">
      <div class="inside">
        <h3><?php echo $title; ?></h3>
        <div class="block">
          <div class="nice-scroll-left"><span class="mover"><i class="fas fa-angle-left"></i></span></div>
          <div class="nice-scroll-right"><span class="mover"><i class="fas fa-angle-right"></i></span></div>

          <div class="wrap nice-scroll">
            <?php
              $c = 1;
              while(osc_has_items()) {
                del_draw_item($c, false, $card_type);
                $c++;
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  <?php
  }

  GLOBAL $stored_items;
  View::newInstance()->_exportVariableToView('items', $global_items);
}


// GET LOCALE SELECT FOR PUBLISH PAGE
function del_locale_post_links() {
  $c = osc_current_user_locale();

  $html = '';
  $locales = osc_get_locales();

  if(count($locales) > 0) {
    $html .= '<div class="locale-links">';

    foreach($locales as $l) {
      $html .= '<a href="#" data-locale="' . $l['pk_c_code'] . '" data-name="' . $l['s_name'] . '" class="mbBg3Active' . ($c == $l['pk_c_code'] ? ' active' : '') . '">' . $l['s_short_name'] . '</a>';
    }

    $html .= '</div>';
  }

  return $html;
}


// GET PROPER PROFILE IMAGE

// GET PROPER PROFILE IMAGE
function del_profile_picture($user_id = NULL, $size = 'small') {
  $user_id = ($user_id > 0 ? $user_id : 0);
  
  if(View::newInstance()->_exists('del_profile_picture_url_' . $user_id)) {
    return View::newInstance()->_get('del_profile_picture_url_' . $user_id);
  }
 
  // if($user_id === NULL) {
    // $user_id = osc_item_user_id();
    // $user_id = ($user_id > 0 ? $user_id : osc_premium_user_id());
  // }

  if($size == 'small') {
    $dimension = 36;
  } else if($size == 'medium') {
    $dimension = 128;
  } else {
    $dimension = 256;
  }

  $img = '';


  // GET IMAGE FROM PROFILE PICTURE FIRST
  if($user_id > 0) {
    if(function_exists('profile_picture_show')) {
      $conn = getConnection();
      $result = $conn->osc_dbFetchResult("SELECT user_id, pic_ext FROM %st_profile_picture WHERE user_id = '%d' ", DB_TABLE_PREFIX, $user_id);

      if($result > 0) { 
        $path = osc_plugins_path().'profile_picture/images/';

        if(file_exists($path . 'profile' . $user_id . $result['pic_ext'])) { 
          $img = osc_base_url() . 'oc-content/plugins/profile_picture/images/' . 'profile' . $user_id . $result['pic_ext'];
        }
      }
    } else if(osc_profile_img_users_enabled()) {
      $img = del_user_profile_img_url($user_id);
    }
  }

  if($img == '') {
    $img = osc_current_web_theme_url('images/default-user-image.png');
  }

  View::newInstance()->_exportVariableToView('del_profile_picture_url_' . $user_id, $img);
  
  return $img;
}


// CUSTOMIZED USER PROFILE IMG URL FUNCTION
function del_user_profile_img_url($id = null) {
  return (string) osc_apply_filter('user_profile_img_url', osc_base_url(). 'oc-content/uploads/user-images/' . del_user_profile_img($id));
}


// CUSTOMIZED USER PROFILE IMG FUNCTION
function del_user_profile_img($id = null) {
  if($id === 0) {
    $img = 'default-user-image.png';
  } else if($id !== null) {
    $user = del_get_user($id);
    $img = isset($user['s_profile_img']) ? $user['s_profile_img'] : '';
  } else {
    $img = osc_user_field("s_profile_img");
  }

  if($img === NULL || trim($img) == '') {
    $img = 'default-user-image.png';
  }

  return (string) $img;
}


// CHECK IF USER HAS PROFILE PICTURE
function del_has_profile_picture($user_id) {
  $img = del_profile_picture($user_id);
  
  if(strpos($img, 'no-user') !== false || strpos($img, 'default-user-image') !== false || strpos($img, 'no-image') !== false) {
    return false;
  }
  
  return true;  
}


// GET USER DATA AND STORE INTO SESSION
function del_get_user($id) {
  if($id > 0) {
    if(!View::newInstance()->_exists('del_user_' . $id)) {
      View::newInstance()->_exportVariableToView('del_user_' . $id, User::newInstance()->findByPrimaryKey($id));
    }
    
    return View::newInstance()->_get('del_user_' . $id);
  }
  
  return false;
}


// GET SEARCH PARAMS FOR REMOVE
function del_search_param_remove() {
  $params = Params::getParamsAsArray();
  $output = array();

  foreach($params as $n => $v) {
    if(!in_array($n, array('page','sLocation')) && ((int)$v > 0 || $v <> '')) {
      // Skip if value is set to default
      if(in_array($n, array('bPic','bPremium','bPhone','sCondition','sTransaction','iRadius','sPeriod')) && (int)$v == 0) {
        continue;
      }
      
      if($n == 'meta') {
        $meta_values = $v;
        
        if(is_array($meta_values) && count($meta_values) > 0) {
          foreach($meta_values as $field_id => $meta_val) {
            $fields = del_get_custom_fields();
            
            if(isset($fields[$field_id])) {
              $field = $fields[$field_id];
              
              if(isset($field['b_searchable']) && $field['b_searchable'] == 1) {
                $meta_val_name = $meta_val;
                
                if($field['e_type'] == 'CHECKBOX') {
                  $meta_val_name = ($meta_val == 1 ? __('Yes', 'zeta') : __('No', 'zeta'));
                }
                
                $output['meta' . $field_id] = array(
                  'value' => $meta_val, 
                  'param' => 'meta' . $field_id,
                  'title' => $field['s_name'],
                  'type' => $field['e_type'],
                  'is_meta' => true,
                  'field_id' => $field_id,
                  'name' => $meta_val_name,
                  'to_remove' => true
                );
              }
            }
          }
        }
        
      } else {
        $output[$n] = array(
          'value' => $v, 
          'param' => $n,
          'title' => del_param_name($n),
          'type' => 'STANDARD',
          'is_meta' => false,
          'field_id' => null,
          'name' => del_remove_value_name($v, $n),
          'to_remove' => true  //(in_array($n, array('sCompany')) ? false : true)
        );
      }
    }
  }

  return $output;
}



// GET NAME FOR REMOVE PARAMETER
function del_remove_value_name($value, $type) {
  $def_cur = (del_param('def_cur') <> '' ? del_param('def_cur') : '$');

  if($type == 'sPeriod') {  
    return del_get_simple_name($value, 'period');

  } else if($type == 'sTransaction') {  
    return del_get_simple_name($value, 'transaction');

  } else if($type == 'sCondition') {  
    return del_get_simple_name($value, 'condition');

  } else if ($type == 'sCategory' || $type == 'category') {
    if(@osc_search_category_id()[0] > 0) {
      $category = Category::newInstance()->findByPrimaryKey(osc_search_category_id()[0]);
      return $category['s_name'];
    }

  } else if ($type == 'sCountry' || $type == 'country') {
    return osc_search_country();

  } else if ($type == 'sRegion' || $type == 'region') {
    return osc_search_region();

  } else if ($type == 'sCity' || $type == 'city') {
    return osc_search_city();
  
  } else if ($type == 'sPriceMin' || $type == 'sPriceMax') {
    return $value . ' ' . $def_cur;

  } else if ($type == 'sPattern') {
    return $value;

  }  else if ($type == 'user' || $type == 'sUser' || $type == 'userId') {
    if(is_numeric($value)) {
      $usr = User::newInstance()->findByPrimaryKey($value);
      return (@$usr['s_name'] <> '' ? @$usr['s_name'] : $value);
    } else {
      return $value;
    }

  }  else if ($type == 'bPic') {
    return ($value == 1 ? __('Yes', 'delta') : __('No', 'delta'));

  }  else if ($type == 'bPremium') {
    return ($value == 1 ? __('Yes', 'delta') : __('No', 'delta'));

  }
}


// GET PARAMETER NICE NAME
function del_param_name($param) {
  if($param == 'sTransaction') {
    return __('Transaction', 'delta');

  } else if($param == 'sCondition') {
    return __('Condition', 'delta');

  } else if($param == 'user' || $param == 'sUser' || $param == 'userId') {
    return __('User', 'delta');

  } else if($param == 'sCategory' || $param == 'category') {
    return __('Category', 'delta');

  } else if($param == 'sPeriod') {
    return __('Age', 'delta');

  } else if($param == 'sCountry' || $param == 'country') {
    return __('Country', 'delta');

  } else if($param == 'sRegion' || $param == 'region') {
    return __('Region', 'delta');

  } else if($param == 'sCity' || $param == 'city') {
    return __('City', 'delta');

  } else if($param == 'bPic') {
    return __('Picture', 'delta');

  } else if($param == 'bPremium') {
    return __('Premium', 'delta');

  } else if($param == 'sPriceMin') {
    return __('Min', 'delta');

  } else if($param == 'sPriceMax') {
    return __('Max', 'delta');

  } else if($param == 'sPattern') {
    return __('Keyword', 'delta');

  } 

  return '';
}


// LIST AVAILABLE OPTIONS
function del_list_options($type) {
  $opt = array();

  if($type == 'condition') {
    $opt[0] = __('All', 'delta');
    $opt[1] = __('New', 'delta');
    $opt[2] = __('Used', 'delta');

  } else if($type == 'transaction') {
    $opt[0] = __('All', 'delta');
    $opt[1] = __('Sell', 'delta');
    $opt[2] = __('Buy', 'delta');
    $opt[3] = __('Rent', 'delta');
    $opt[4] = __('Exchange', 'delta');

  } else if ($type == 'period') {
    $opt[0] = __('All', 'delta');
    $opt[1] = __('Yesterday', 'delta');
    $opt[7] = __('Last week', 'delta');
    //$opt[14] = __('Last 2 weeks', 'delta');
    $opt[31] = __('Last month', 'delta');
    //$opt[365] = __('Last year', 'delta');

  } else if ($type == 'seller_type') {
    $opt[0] = __('All', 'delta');
    $opt[1] = __('Personal', 'delta');
    $opt[2] = __('Company', 'delta');
  }

  return $opt;
}


// GET SIMPLE OPTION NAME
function del_get_simple_name($id, $type, $include_null = true) {
  if($include_null === false && ($id == '' || $id == 0)) {
    return '';
  }
  
  $options = del_list_options($type);
  return @$options[$id];
}


// GET ALL CUSTOM FIELDS
function del_get_custom_fields() {
  if(View::newInstance()->_exists('del_custom_fields')) {
    return View::newInstance()->_get('del_custom_fields');
  }
  
  $output = array();
  $fields = Field::newInstance()->listAll();
  
  if(is_array($fields) && count($fields) > 0) {
    foreach($fields as $field) {
      $output[$field['pk_i_id']] = $field;
    }
  }
  
  View::newInstance()->_exportVariableToView('del_custom_fields', $output);

  return $output;
}


// DEFAULT LOCATION PICKER CONTENT
function del_def_location() {
  $html = '';

  $type = (del_param('def_locations') == '' ? 'region' : del_param('def_locations'));

  $countries = Country::newInstance()->listAll();
  $limit = 60;
  $city_not_empty = 0;   // set to 0 to include also cities with no listings

  if($type == 'region') {
    $regions_cities = Region::newInstance()->listAll();
  }

  $type_name = ($type == 'region' ? __('region', 'delta') : __('city', 'delta'));


  foreach($countries as $c) {
    //$html .= '<div class="option country init" data-country="' . $c['pk_c_code'] . '" data-region="" data-city="" data-code="country' . $c['pk_c_code'] . '" id="' . $c['pk_c_code'] . '"><strong>' . osc_esc_js($c['s_name']) . '</strong></div>';

    if($type == 'city') {
      $regions_cities = ModelDEL::newInstance()->getCities($c['pk_c_code'], $limit, $city_not_empty);
    }

    $counter = 0;
    foreach($regions_cities as $r) {
      if($counter < $limit) {
        if(strtoupper($r['fk_c_country_code']) == strtoupper($c['pk_c_code'])) {
          if($type == 'region') {
            $html .= '<div class="option region init" data-country="' . $r['fk_c_country_code'] . '" data-region="' . $r['pk_i_id'] . '" data-city="" data-code="region' . $r['pk_i_id'] . '" id="' . $r['pk_i_id'] . '" title="' . osc_esc_js(osc_location_native_name_selector($c, 's_name')) . '">';
            $html .= '<strong>' . osc_esc_js(osc_location_native_name_selector($r, 's_name')) . '</strong>';

            if(count($countries) > 1) {
              $html .= '<span>' . osc_esc_js(osc_location_native_name_selector($c, 's_name')) . '</span>';
            }

            $html .= '</div>';
            
          } else { 
            $html .= '<div class="option region init" data-country="' . $r['fk_c_country_code'] . '" data-region="' . $r['fk_i_region_id'] . '" data-city="' . $r['pk_i_id'] . '" data-code="city' . $r['pk_i_id'] . '" title="' . osc_esc_js(osc_location_native_name_selector($r, 's_region_name')) . '" id="' . $r['pk_i_id'] . '">';
            $html .= '<strong>' . osc_esc_js(osc_location_native_name_selector($r, 's_name')) . '</strong>';
            $html .= '<span>' . osc_esc_js(osc_location_native_name_selector($r, 's_region_name')) . '</span>';
            $html .= '</div>';
          }
        }
      }

      $counter++;
    }

    if($counter == $limit*count($countries)) {
      $html .= '<div class="option service empty-pick default" data-country="" data-region="" data-city="" data-code="" id=""><em>' . osc_esc_js(sprintf(__('... and %d more %s, enter your %s name to refine results', 'delta'), $limit, $type_name, $type_name)) . '</em></div>';
    }
  }

  echo $html;
}


// GET COUNTRY FLAG, IF EXISTS
function del_country_flag_image($code) {
  if($code != '' && file_exists(osc_base_path() . 'oc-content/themes/delta/images/country_flags/large/' . strtolower($code) . '.png')) {
    return osc_current_web_theme_url() . 'images/country_flags/large/' . strtolower($code) . '.png';
  } 
  
  return osc_current_web_theme_url() . 'images/country_flags/large/default.png';
}


// DEFAULT LOCATION PICKER CONTENT
function del_locbox_short($country = '', $region = '', $city = '', $level = 'all') {
  $html = '';


  $countries = Country::newInstance()->listAll();
  $box_width = 140;

  // COUNTRIES
  if(count($countries) > 1 && ($level == 'all' || $level == 'country')) {
    //$html .= '<div class="loc-tab country-tab count' . count($countries) . (del_param('loc_one_row') == 1 ? ' one-row' : '') . '">';

    $html .= '<div class="relative">';
    $html .= '<div class="nice-scroll-left ns-white"></div>';
    $html .= '<div class="nice-scroll-right ns-white"></div>';

    $html .= '<div class="loc-tab country-tab nice-scroll">';
    //$html .= '<div class="loc-in" style="' . (del_param('loc_one_row') == 1 ? 'width:' . count($countries)*$box_width . 'px;' : '') . '">';

    foreach($countries as $c) {
      $html .= '<div class="elem country ' . (strtoupper($c['pk_c_code']) == strtoupper($country) ? 'active' : '') . '" data-country="' . $c['pk_c_code'] . '" data-region="" data-city="" style="' . (del_param('loc_one_row') == 1 ? 'width:' . ($box_width + 1) . 'px;'  : '') . '"><img src="' . del_country_flag_image($c['pk_c_code']) . '"/><strong>' . osc_location_native_name_selector($c, 's_name') . '</strong></div>';
    }

    //$html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
  } 

  
  // REGIONS
  if($level == 'all' || $level == 'region') {
    $html .= '<div class="loc-tab region-tab' . (del_param('loc_box_region_search') == 1 ? ' has-filter' : '') . '">';

    if(del_param('loc_box_region_search') == 1) {
      $html .= '<div class="filter"><input type="text" class="region-filter" placeholder="' . osc_esc_html(__('Filter regions...', 'delta')) . '"/></div>';
    }
    
    if(count($countries) <= 1 || $country <> '') {
      if($country <> '') {
        $regions = Region::newInstance()->findByCountry($country);
      } else {
        $regions = Region::newInstance()->listAll();
      }

      if(count($regions) > 0) {
        foreach($regions as $r) {
          $html .= '<div class="elem region ' . ($r['pk_i_id'] == $region ? 'active' : '') . '" data-country="' . $r['fk_c_country_code'] . '" data-region="' . $r['pk_i_id'] . '" data-city="">' . osc_location_native_name_selector($r, 's_name') . ' <i class="fa fa-angle-right"></i></div>';
        }
      }
    }

    $html .= '</div>';
  }


  // CITIES
  if($level == 'all' || $level == 'city') {
    $html .= '<div class="loc-tab city-tab' . (del_param('loc_box_city_search') == 1 ? ' has-filter' : '') . '">';
    
    if(del_param('loc_box_city_search') == 1) {
      $html .= '<div class="filter"><input type="text" class="city-filter" placeholder="' . osc_esc_html(__('Filter cities...', 'delta')) . '"/></div>';
    }
    
    if($region <> '') {
      $cities = City::newInstance()->findByRegion($region);

      if(count($cities) > 0) {
        foreach($cities as $ct) {
          $html .= '<div class="elem city ' . ($ct['pk_i_id'] == $city ? 'active' : '') . '" data-country="' . $ct['fk_c_country_code'] . '" data-region="' . $ct['fk_i_region_id'] . '" data-city="' . $ct['pk_i_id'] . '">' . osc_location_native_name_selector($ct, 's_name') . ' <i class="fa fa-angle-right"></i></div>';
        }
      }
    }

    $html .= '</div>';
  }


  echo $html;
}


// DEFAULT LOCATION PICKER CONTENT
function del_catbox_short($cat_id = '') {
  $html = '';
  $level = 1;

  $hierarchy = Category::newInstance()->hierarchy($cat_id);
  $hierarchy = array_column($hierarchy, 'pk_i_id');

  $hierarchy_last_subs = Category::newInstance()->findSubcategoriesEnabled(isset($hierarchy[0]) ? $hierarchy[0] : null);

  if(count($hierarchy_last_subs) > 0) {
    $hierarchy[] = -1;   // add one fake id to increase number of columns shown
  }

  $categories = Category::newInstance()->toTree();

  del_catbox_loop($categories, $hierarchy, $level);


  echo $html;
}


function del_catbox_loop($categories, $hierarchy, $level) {
  $html = '';

  if(count($categories) > 0) {
    $one_row = false;

    if($categories[0]['fk_i_parent_id'] <= 0 && del_param('cat_one_row') == 1) {
      $one_row = true;
    }

    $box_width = 140;


    $html .= '<div class="cat-tab ' . ($categories[0]['fk_i_parent_id'] <= 0 ? 'root ' . (empty($hierarchy) ? 'active' : '') : 'sub') . ' ' . (in_array($categories[0]['fk_i_parent_id'], $hierarchy) ? 'active' : '') . ($one_row === true ? 'one-row' : '') . '" data-parent="' . $categories[0]['fk_i_parent_id'] . '" data-level="' . $level . '">';
    $html .= '<div class="cat-in" style="' . ($one_row === true ? 'width:' . count($categories)*$box_width . 'px;' : '') . '">';

    foreach($categories as $c) {
      $html .= '<div class="elem category ' . (in_array($c['pk_i_id'], $hierarchy) ? 'active' : '') . ' ' . (count($c['categories']) > 0 ? 'has' : 'blank') . '" data-category="' . $c['pk_i_id'] . '" style="' . ($one_row === true ? 'width:' . ($box_width + 1) . 'px;'  : '') . '">';

      if($c['fk_i_parent_id'] <= 0) {
        $html .= '<div class="img" style="color:' . del_get_cat_color($c['pk_i_id']) . ';">' . del_get_cat_icon( $c['pk_i_id'] ) . '</div> <strong><span>' . $c['s_name'] . '</span></strong>';
      } else {
        $html .= $c['s_name'] . '<i class="fa fa-angle-right"></i>';
      }


      $html .= '</div>';
    }

    $html .= '</div>';
    $html .= '</div>';

    echo $html;

    if($level == 1) {
      echo '<div class="wrapper" data-columns="' . max(count($hierarchy) - 1, 0) . '">';
    }

    // loop for children separately
    foreach($categories as $c) {
      if(count($c['categories']) > 0) {                // && $level + 1 <= 4
        del_catbox_loop($c['categories'], $hierarchy, $level + 1);
      }
    }

    if($level == 1) {
      echo '</div>';  // end wrapper
    }
  }



}



// COUNT COUNTRIES
function del_count_countries() {
  $countries = Country::newInstance()->listAll();
  return count($countries);
}


// GET CORRECT FANCYBOX URL
function del_item_fancy_url($type, $params = array()) {
  $url = del_item_form_ajax_url($type, osc_item_id());
  
  if(osc_rewrite_enabled()) {
    // $url = '?type=' . $type;
    $login_url = osc_user_login_url() . '?loginRequired=1&type='. $type;
  } else {
    // $url = '&type=' . $type;
    $login_url = osc_user_login_url() . '&loginRequired=1&type=' . $type;
  }

  $extra = '';
  
  if($type == 'contact_public') {
    if(osc_get_preference('web_contact_form_disabled', 'osclass') == 1) {
      return '#';
    }
    
  } else if($type == 'contact') {
    if(osc_get_preference('item_contact_form_disabled', 'osclass') == 1) {
      return '#';
      
    } else if(osc_reg_user_can_contact() && !osc_is_web_user_logged_in()) {
      return $login_url;
    }
    
  } else if($type == 'comment') {
    if(osc_get_preference('enabled_comments', 'osclass') == 0) {
      return '#';
      
    } else if(osc_reg_user_post_comments() && !osc_is_web_user_logged_in()) {
      return $login_url;
    }

  } else if($type == 'friend') {
    if(osc_get_preference('item_send_friend_form_disabled', 'osclass') == 1) {
      return '#';
    }
  }

  if(!empty($params) && is_array($params)) {
    foreach($params as $n => $v) {
      $extra .= '&' . $n . '=' . $v;
    }
  }

  // return del_item_send_friend_url() . $url . $extra;
  return $url . $extra;
}

// RETRO COMPATIBILITY
function del_fancy_url($type, $params = array()) {
  return del_item_fancy_url($type, $params);
}


// CUSTOM SEND FRIEND URL
function del_item_send_friend_url($item_id = '') {
  if($item_id <= 0) {
    $item_id = (osc_item_id() > 0 ? osc_item_id() : osc_premium_id());
  }

  if(osc_rewrite_enabled()) {
    return osc_base_url() . osc_get_preference('rewrite_item_send_friend') . '/' . $item_id;
  } else {
    return osc_base_url(true)."?page=item&action=send_friend&id=" . $item_id;
  }
}


// GET CORRECT BLOCK ON REGISTER PAGE
function del_reg_url($type) {
  if(osc_rewrite_enabled()) {
    $reg_url = '?move=' . $type;
  } else {
    $reg_url = '&move=' . $type;
  }

 return osc_register_account_url() . $reg_url;
}


// UPDATE PAGINATION ARROWS
function del_fix_arrow($data) {
  //$data = str_replace('&lt;', '<i class="fa fa-angle-left"></i>', $data);
  //$data = str_replace('&gt;', '<i class="fa fa-angle-right"></i>', $data);
  $data = str_replace('&laquo;', '&lt;&lt;', $data);
  $data = str_replace('&raquo;', '&gt;&gt;', $data);

  return $data;
}


// GET THEME PARAM
function del_param($name) {
  return osc_get_preference($name, 'theme-delta');
}


// CHECK IF PRICE ENABLED ON CATEGORY
function del_check_category_price($id) {
  if(!osc_price_enabled_at_items()) {
    return false;
  } else if(!isset($id) || $id == '' || $id <= 0) {
    return true;
  } else {
    $category = Category::newInstance()->findByPrimaryKey($id);
    if(isset($category['b_price_enabled'])) {
      return ($category['b_price_enabled'] == 0 ? false : true);
    }
    
    return true;
  }
}



// FLAT CATEGORIES CONTENT (Publish)
function del_flat_categories() {
  return '<div id="flat-cat-fancy" style="display:none;overflow:hidden;">' . del_category_loop() . '</div>';
}


// SMART DATE
function del_smart_date( $time ) {
  $time_diff = round(abs(time() - strtotime( $time )) / 60);
  $time_diff_h = floor($time_diff/60);
  $time_diff_d = floor($time_diff/1440);
  $time_diff_w = floor($time_diff/10080);
  $time_diff_m = floor($time_diff/43200);
  $time_diff_y = floor($time_diff/518400);


  if($time_diff < 2) {
    $time_diff_name = __('minute ago', 'delta');
  } else if ($time_diff < 60) {
    $time_diff_name = sprintf(__('%d minutes ago', 'delta'), $time_diff);
  } else if ($time_diff < 120) {
    $time_diff_name = sprintf(__('%d hour ago', 'delta'), $time_diff_h);
  } else if ($time_diff < 1440) {
    $time_diff_name = sprintf(__('%d hours ago', 'delta'), $time_diff_h);
  } else if ($time_diff < 2880) {
    $time_diff_name = sprintf(__('%d day ago', 'delta'), $time_diff_d);
  } else if ($time_diff < 10080) {
    $time_diff_name = sprintf(__('%d days ago', 'delta'), $time_diff_d);
  } else if ($time_diff < 20160) {
    $time_diff_name = sprintf(__('%d week ago', 'delta'), $time_diff_w);
  } else if ($time_diff < 43200) {
    $time_diff_name = sprintf(__('%d weeks ago', 'delta'), $time_diff_w);
  } else if ($time_diff < 86400) {
    $time_diff_name = sprintf(__('%d month ago', 'delta'), $time_diff_m);
  } else if ($time_diff < 518400) {
    $time_diff_name = sprintf(__('%d months ago', 'delta'), $time_diff_m);
  } else if ($time_diff < 1036800) {
    $time_diff_name = sprintf(__('%d year ago', 'delta'), $time_diff_y);
  } else {
    $time_diff_name = sprintf(__('%d years ago', 'delta'), $time_diff_y);
  }

  return $time_diff_name;
}


// SMART DATE2
function del_smart_date2( $time ) {
  $time_diff = round(abs(time() - strtotime( $time )) / 60);
  $time_diff_h = floor($time_diff/60);
  $time_diff_d = floor($time_diff/1440);
  $time_diff_w = floor($time_diff/10080);
  $time_diff_m = floor($time_diff/43200);
  $time_diff_y = floor($time_diff/518400);


  if ($time_diff < 10080) {
    $time_diff_name = sprintf(__('%d+ days', 'delta'), $time_diff_d);
  } else if ($time_diff < 20160) {
    $time_diff_name = sprintf(__('%d+ week', 'delta'), $time_diff_w);
  } else if ($time_diff < 43200) {
    $time_diff_name = sprintf(__('%d+ weeks', 'delta'), $time_diff_w);
  } else if ($time_diff < 86400) {
    $time_diff_name = sprintf(__('%d+ month', 'delta'), $time_diff_m);
  } else if ($time_diff < 518400) {
    $time_diff_name = sprintf(__('%d+ months', 'delta'), $time_diff_m);
  } else if ($time_diff < 1036800) {
    $time_diff_name = sprintf(__('%d+ year', 'delta'), $time_diff_y);
  } else {
    $time_diff_name = sprintf(__('%d+ years', 'delta'), $time_diff_y);
  }

  return $time_diff_name;
}




// CHECK IF ITEM MARKED AS SOLD-UNSOLD
function del_check_sold(){
  $conn = DBConnectionClass::newInstance();
  $data = $conn->getOsclassDb();
  $comm = new DBCommandClass($data);

  $status = Params::getParam('markSold');
  $id = Params::getParam('itemId');
  $secret = Params::getParam('secret');
  $item_type = Params::getParam('itemType');

  if($status <> '' && $id <> '' && $id > 0) {
    $item = Item::newInstance()->findByPrimaryKey($id);

    if( $secret == $item['s_secret'] ) {
      //Item::newInstance()->dao->update(DB_TABLE_PREFIX.'t_item_delta', array('i_sold' => $status), array('fk_i_item_id' => $item['pk_i_id']));
      $comm->update(DB_TABLE_PREFIX.'t_item_delta', array('i_sold' => $status), array('fk_i_item_id' => $item['pk_i_id']));
 
      if (osc_rewrite_enabled()) {
        $item_type_url = '?itemType=' . $item_type;
      } else {
        $item_type_url = '&itemType=' . $item_type;
      }

      header('Location: ' . osc_user_list_items_url() . $item_type_url);
    }
  }
}

osc_add_hook('header', 'del_check_sold');



// HELP FUNCTION TO GET CATEGORIES
function del_category_loop( $parent_id = NULL, $parent_color = NULL ) {
  $parent_color = isset($parent_color) ? $parent_color : NULL;

  if(Params::getParam('sCategory') <> '') {
    $id = Params::getParam('sCategory');
  } else if (del_get_session('sCategory') <> '' && (osc_is_publish_page() || osc_is_edit_page())) {
    $id = del_get_session('sCategory');
  } else if (osc_item_category_id() <> '') {
    $id = osc_item_category_id();
  } else {
    $id = '';
  }


  if($parent_id <> '' && $parent_id > 0) {
    $categories = Category::newInstance()->findSubcategoriesEnabled( $parent_id );
  } else {
    $parent_id = 0;
    $categories = Category::newInstance()->findRootCategoriesEnabled();
  }

  $html = '<div class="flat-wrap' . ($parent_id == 0 ? ' root' : '') . '" data-parent-id="' . $parent_id . '">';
  $html .= '<div class="single info">' . __('Select category', 'delta') . ' ' . ($parent_id <> 0 ? '<span class="back tr1 round2"><i class="fa fa-angle-left"></i> ' . __('Back', 'delta') . '</span>' : '') . '</div>';

  foreach( $categories as $c ) {
    if( $parent_id == 0) {
      $parent_color = del_get_cat_color( $c['pk_i_id'] );
      $icon = '<div class="parent-icon" style="background:' . del_get_cat_color( $c['pk_i_id'] ) . ';">' . del_get_cat_icon( $c['pk_i_id'] ) . '</div>';
    } else {
      $icon = '<div class="parent-icon children" style="background: ' . $parent_color . '">' . del_get_cat_icon( $c['pk_i_id'] ) . '</div>';
    }
    
    $html .= '<div class="single tr1' . ($c['pk_i_id'] == $id ? ' selected' : '') . '" data-id="' . $c['pk_i_id'] . '"><span>' . $icon . $c['s_name'] . '</span></div>';

    $subcategories = Category::newInstance()->findSubcategoriesEnabled( $c['pk_i_id'] );
    if(isset($subcategories[0])) {
      $html .= del_category_loop( $c['pk_i_id'], $parent_color );
    }
  }
  
  $html .= '</div>';
  return $html;
}



// FLAT CATEGORIES SELECT (Publish)
function del_flat_category_select(){  
  $root = Category::newInstance()->findRootCategoriesEnabled();

  $html = '<div class="category-box tr1">';
  foreach( $root as $c ) {
    $html .= '<div class="option tr1" style="background:' . del_get_cat_color( $c['pk_i_id'] ) . ';">' . del_get_cat_icon( $c['pk_i_id'] ) . '</div>';
  }
 
  $html .= '</div>';
  return $html;
}



// GET CITY, REGION, COUNTRY FOR AJAX LOADER
function del_ajax_city() {
  $user = osc_user();
  $item = osc_item();

  if(Params::getParam('sCity') <> '') {
    return Params::getParam('sCity');
  } else if (isset($item['fk_i_city_id']) && $item['fk_i_city_id'] <> '') {
    return $item['fk_i_city_id'];
  } else if (isset($user['fk_i_city_id']) && $user['fk_i_city_id'] <> '') {
    return $user['fk_i_city_id'];
  }
}


function del_ajax_region() {
  $user = osc_user();
  $item = osc_item();

  if(Params::getParam('sRegion') <> '') {
    return Params::getParam('sRegion');
  } else if (isset($item['fk_i_region_id']) && $item['fk_i_region_id'] <> '') {
    return $item['fk_i_region_id'];
  } else if (isset($user['fk_i_region_id']) && $user['fk_i_region_id'] <> '') {
    return $user['fk_i_region_id'];
  }
}


function del_ajax_country() {
  $user = osc_user();
  $item = osc_item();

  if(Params::getParam('sCountry') <> '') {
    return Params::getParam('sCountry');
  } else if (isset($item['fk_c_country_code']) && $item['fk_c_country_code'] <> '') {
    return $item['fk_c_country_code'];
  } else if (isset($user['fk_c_country_code']) && $user['fk_c_country_code'] <> '') {
    return $user['fk_c_country_code'];
  }
}



// USER ACCOUNT - TOP MENU
function del_user_menu_top() {

  if(isset($_SERVER['HTTPS'])) {
    $protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
  } else {
    $protocol = 'http';
  }

  $current_url =  $protocol.'://'.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI'];


  $options = array();
  $options[] = array('name' => __('My listings', 'delta'), 'url' => osc_user_list_items_url(), 'class' => 'opt_items', 'icon' => 'fa-folder-o', 'section' => 1, 'count' => 0);
  //$options[] = array('name' => __('Active', 'delta'), 'url' => osc_user_list_items_url() . $s_active, 'class' => 'opt_active_items', 'icon' => 'fa-check-square-o', 'section' => 1, 'count' => $c_active, 'is_active' => $yes_active);
  //$options[] = array('name' => __('Not Validated', 'delta'), 'url' => osc_user_list_items_url() . $s_pending, 'class' => 'opt_not_validated_items', 'icon' => 'fa-stack-overflow', 'section' => 1, 'count' => $c_pending, 'is_active' => $yes_pending);
  //$options[] = array('name' => __('Expired', 'delta'), 'url' => osc_user_list_items_url() . $s_expired, 'class' => 'opt_expired_items', 'icon' => 'fa-times-circle', 'section' => 1, 'count' => $c_expired, 'is_active' => $yes_expired);
  //$options[] = array('name' => __('Dashboard', 'delta'), 'url' => osc_user_dashboard_url(), 'class' => 'opt_dashboard', 'icon' => 'fa-dashboard', 'section' => 2);
  $options[] = array('name' => __('Subscriptions', 'delta'), 'url' => osc_user_alerts_url(), 'class' => 'opt_alerts', 'icon' => 'fa-bullhorn', 'section' => 2);
  $options[] = array('name' => __('My profile', 'delta'), 'url' => osc_user_profile_url(), 'class' => 'opt_account', 'icon' => 'fa-file-text-o', 'section' => 2);
  $options[] = array('name' => __('Public profile', 'delta'), 'url' => osc_user_public_profile_url(), 'class' => 'opt_publicprofile', 'icon' => 'fa-picture-o', 'section' => 3);
  $options[] = array('name' => __('Logout', 'delta'), 'url' => osc_user_logout_url(), 'class' => 'opt_logout', 'icon' => 'fa-sign-out', 'section' => 3);

  $options = osc_apply_filter('user_menu_filter', $options);


  echo '<div class="user-top-menu">';
  echo '<div class="nice-scroll-left wh"><span class="mover2"><i class="fas fa-angle-left"></i></span></div>'; 
  echo '<div class="nice-scroll-right wh"><span class="mover2"><i class="fas fa-angle-right"></i></span></div>'; 
  
  echo '<ul class="umenu nice-scroll">';

  foreach($options as $o) {
    if($o['section'] == 1) {
      $o['icon'] = isset($o['icon']) ? ($o['icon'] <> '' ? $o['icon'] : 'fa-dot-circle-o') : 'fa-dot-circle-o';

      if( isset($o['is_active']) && $o['is_active'] == 1 || $current_url == $o['url'] || strpos($current_url, osc_user_list_items_url()) !== false ) {
        $active_class =  ' active';
      } else {
        $active_class = '';
      }

      echo '<li class="' . $o['class'] . $active_class . '" ><a href="' . $o['url'] . '" >' . $o['name'] . '</a></li>';
    }
  }

  osc_run_hook('user_menu_items');



  foreach($options as $o) {
    if($o['section'] == 2) {
      $active_class = ($current_url == $o['url'] ? ' active' : '');
      echo '<li class="' . $o['class'] . $active_class . '" ><a href="' . $o['url'] . '" >' . $o['name'] . '</a></li>';
    }
  }

  osc_run_hook('user_menu');


  foreach($options as $o) {
    if($o['section'] == 3) {
      $active_class = ($current_url == $o['url'] ? ' active' : '');
      echo '<li class="' . $o['class'] . $active_class . '" ><a href="' . $o['url'] . '" >' . $o['name'] . '</a></li>';
    }
  }
  
  echo '</ul>';
  echo '</div>';
}



// USER ACCOUNT - MENU ELEMENTS
function del_user_menu() {
  $sections = array('items', 'profile', 'logout');

  if(isset($_SERVER['HTTPS'])) {
    $protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
  } else {
    $protocol = 'http';
  }

  $current_url =  $protocol.'://'.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI'];



  $options = array();
  $options[] = array('name' => __('My listings', 'delta'), 'url' => osc_user_list_items_url(), 'class' => 'opt_items', 'icon' => 'far fa-folder', 'section' => 1, 'count' => 0);
  //$options[] = array('name' => __('Active', 'delta'), 'url' => osc_user_list_items_url() . $s_active, 'class' => 'opt_active_items', 'icon' => 'far fa-check-square', 'section' => 1, 'count' => $c_active, 'is_active' => $yes_active);
  //$options[] = array('name' => __('Not Validated', 'delta'), 'url' => osc_user_list_items_url() . $s_pending, 'class' => 'opt_not_validated_items', 'icon' => 'fab fa-stack-overflow', 'section' => 1, 'count' => $c_pending, 'is_active' => $yes_pending);
  //$options[] = array('name' => __('Expired', 'delta'), 'url' => osc_user_list_items_url() . $s_expired, 'class' => 'opt_expired_items', 'icon' => 'fas fa-times-circle', 'section' => 1, 'count' => $c_expired, 'is_active' => $yes_expired);
  //$options[] = array('name' => __('Dashboard', 'delta'), 'url' => osc_user_dashboard_url(), 'class' => 'opt_dashboard', 'icon' => 'fas fa-dashboard', 'section' => 2);
  $options[] = array('name' => __('Alerts', 'delta'), 'url' => osc_user_alerts_url(), 'class' => 'opt_alerts', 'icon' => 'far fa-bell', 'section' => 2);
  $options[] = array('name' => __('My profile', 'delta'), 'url' => osc_user_profile_url(), 'class' => 'opt_account', 'icon' => 'far fa-edit', 'section' => 2);
  $options[] = array('name' => __('Public profile', 'delta'), 'url' => osc_user_public_profile_url(), 'class' => 'opt_publicprofile', 'icon' => 'far fa-address-card', 'section' => 2);
  $options[] = array('name' => __('Logout', 'delta'), 'url' => osc_user_logout_url(), 'class' => 'opt_logout', 'icon' => 'fas fa-sign-out', 'section' => 3);

  $options = osc_apply_filter('user_menu_filter', $options);


  // SECTION 1 - LISTINGS

  foreach($options as $o) {
    if($o['section'] == 1) {
      $o['icon'] = isset($o['icon']) ? ($o['icon'] <> '' ? $o['icon'] : 'far fa-dot-circle') : 'far fa-dot-circle';

      if( isset($o['is_active']) && $o['is_active'] == 1 || $current_url == $o['url'] ) {
        $active_class =  ' active';
      } else {
        $active_class = '';
      }

      echo '<a href="' . $o['url'] . '" class="' . $o['class'] . ' ' . $active_class . '" ><i class="' . $o['icon'] . '"></i>' . $o['name'] . '</a>';
    }
  }

  osc_run_hook('user_menu_items');



  // SECTION 2 - PROFILE & USER

  foreach($options as $o) {
    if($o['section'] == 2) {
      $active_class = ($current_url == $o['url'] ? ' active' : '');
      $o['icon'] = isset($o['icon']) ? ($o['icon'] <> '' ? $o['icon'] : 'far fa-dot-circle') : 'far fa-dot-circle';
      echo '<a href="' . $o['url'] . '" class="' . $o['class'] . ' ' . $active_class . '" ><i class="' . $o['icon'] . '"></i>' . $o['name'] . '</a>';
    }
  }


  echo '<div class="hook-options">';
    osc_run_hook('user_menu');
  echo '</div>';


  

  // SECTION 3 - LOGOUT
  foreach($options as $o) {
    if($o['section'] == 3) {
      $o['icon'] = isset($o['icon']) ? ($o['icon'] <> '' ? $o['icon'] : 'far fa-dot-circle') : 'far fa-dot-circle';
      echo '<a href="' . $o['url'] . '" class="' . $o['class'] . ' ' . $active_class . '" ><i class="' . $o['icon'] . '"></i>' . $o['name'] . '</a>';
    }
  }
}



// GET TERM NAME BASED ON COUNTRY, REGION & CITY
function del_get_term($term = '', $country = '', $region = '', $city = ''){
  if( $term == '') {
    if( $city <> '' && is_numeric($city) ) {
      $city_info = City::newInstance()->findByPrimaryKey( $city );
      return (osc_location_native_name_selector($city_info, 's_name') <> '' ? osc_location_native_name_selector($city_info, 's_name') : $city);
    }
 
    if( $region <> '' && is_numeric($region) ) {
      $region_info = Region::newInstance()->findByPrimaryKey( $region );
      return (osc_location_native_name_selector($region_info, 's_name') <> '' ? osc_location_native_name_selector($region_info, 's_name') : $region);
    }

    if( $country <> '' && strlen($country) == 2 ) {
      $country_info = Country::newInstance()->findByCode( $country );
      return (osc_location_native_name_selector($country_info, 's_name') <> '' ? osc_location_native_name_selector($country_info, 's_name') : $country);
    }

    $array = array_filter(array($city, $region, $country));
    return @$array[0]; // if all fail, return first non-empty

  } else {
    return $term;
  }
}


// GET LOCATION FULL NAME BASED ON COUNTRY, REGION & CITY
function del_get_full_loc($country = '', $region = '', $city = ''){
  if( $city <> '' && is_numeric($city) ) {
    $city_info = City::newInstance()->findByPrimaryKey( $city );
    $region_info = Region::newInstance()->findByPrimaryKey( $city_info['fk_i_region_id'] );
    $country_info = Country::newInstance()->findByCode( $city_info['fk_c_country_code'] );
    return osc_location_native_name_selector($city_info, 's_name') . ', ' . osc_location_native_name_selector($region_info, 's_name') . ', ' . osc_location_native_name_selector($country_info, 's_name');
  }

  if( $region <> '' && is_numeric($region) ) {
    $region_info = Region::newInstance()->findByPrimaryKey( $region );
    $country_info = Country::newInstance()->findByCode( $region_info['fk_c_country_code'] );

    return osc_location_native_name_selector($region_info, 's_name') . ', ' . osc_location_native_name_selector($country_info, 's_name');
  }

  if( $country <> '' && strlen($country) == 2 ) {
    $country_info = Country::newInstance()->findByCode( $country );
    return osc_location_native_name_selector($country_info, 's_name');
  }

  return '';
}



// ADD TRANSACTION AND CONDITION TO OC-ADMIN EDIT ITEM
function del_extra_add_admin( $catId = null, $item_id = null ){
  if(defined('OC_ADMIN') && OC_ADMIN === true) {
    // if($item_id > 0) {
      $item = Item::newInstance()->findByPrimaryKey($item_id);
      $item_extra = del_item_extra($item_id);
      ?>
      
      <div class="control-group">
        <label class="control-label" for="sTransaction"><?php _e('Transaction', 'delta'); ?></label>
        <div class="controls"><?php echo del_simple_transaction(true, $item_id <> '' ? $item_id : false); ?></div>
      </div>

      <div class="control-group">
        <label class="control-label" for="sCondition"><?php _e('Condition', 'delta'); ?></label>
        <div class="controls"><?php echo del_simple_condition(true, $item_id <> '' ? $item_id : false); ?></div>
      </div>

      <?php if(!method_exists('ItemForm', 'contact_phone_text')) { ?>
        <div class="control-group">
        <label class="control-label" for="sPhone"><?php _e('Phone', 'delta'); ?></label>
        <div class="controls"><input type="text" name="sPhone" id="sPhone" value="<?php echo osc_esc_html(@$item_extra['s_phone']); ?>" /></div>
        </div>
      <?php } ?>
      
      <div class="control-group">
        <label class="control-label" for="sSold"><?php _e('Status', 'delta'); ?></label>
        <div class="controls">
          <select name="sSold">
            <option value="" <?php if(@$item_extra['i_sold'] == '') { ?>selected="selected"<?php } ?>><?php _e('Select a status...', 'delta'); ?></option>
            <option value="2" <?php if(@$item_extra['i_sold'] == 2) { ?>selected="selected"<?php } ?>><?php _e('Reserved', 'delta'); ?></option>
            <option value="1" <?php if(@$item_extra['i_sold'] == 1) { ?>selected="selected"<?php } ?>><?php _e('Sold', 'delta'); ?></option>
          </select>
        </div>
      </div>
      
      <?php
    // }
  }
}

osc_add_hook('item_form', 'del_extra_add_admin');
osc_add_hook('item_edit', 'del_extra_add_admin');



function del_extra_edit( $item ) {
  $item['pk_i_id'] = isset($item['pk_i_id']) ? $item['pk_i_id'] : 0;
  $detail = ModelAisItem::newInstance()->findByItemId( $item['pk_i_id'] );

  if( isset($detail['fk_i_item_id']) ) {
    ModelAisItem::newInstance()->updateItemMeta( $item['pk_i_id'], Params::getParam('ais_meta_title'), Params::getParam('ais_meta_description') );
  } else {
    ModelAisItem::newInstance()->insertItemMeta( $item['pk_i_id'], Params::getParam('ais_meta_title'), Params::getParam('ais_meta_description') );
  } 
}


// SIMPLE SEARCH SORT
function del_simple_sort() {
  $type = Params::getParam('sOrder');           // date - price
  $order = Params::getParam('iOrderType');      // asc - desc

  $orders = osc_list_orders();


  //$html  = '<input type="hidden" name="sOrder" id="sOrder" val="' . $type . '"/>';
  //$html  = '<input type="hidden" name="iOrderType" id="iOrderType" val="' . $order . '"/>';

  $html  = '<select class="orderSelect" id="orderSelect" name="orderSelect">';
  
  foreach($orders as $label => $spec) {

    $selected = '';
    if( $spec['sOrder'] == $type && $spec['iOrderType'] == $order ) {
      $selected = ' selected="selected"';
    }
 
    $html .= '<option' . $selected . ' data-type="' . $spec['sOrder'] . '" data-order="' . $spec['iOrderType'] . '">' . $label . '</option>';
  }

  $html .= '</select>';

  return $html;
}


// SIMPLE CATEGORY SELECT
function del_simple_category($select = false, $level = 3, $id = 'sCategory') {
  $categories = Category::newInstance()->toTree();
  $current = @osc_search_category_id()[0];
  $allow_parent = ($id == 'catId' ? osc_get_preference('selectable_parent_categories', 'osclass') : 1);

  if($id == 'catId') {   // publish-edit listing page
    $current = osc_item_category_id();
  }

  $c_category = Category::newInstance()->findByPrimaryKey($current);
  
  if(isset($c_category['s_name'])) {
    $c_category = array('s_name' => '');
  }
  
  $root = Category::newInstance()->toRootTree($current);
  $root = isset($root[0]) ? $root[0] : array('pk_i_id' => $current, 's_name' => (isset($c_category['s_name']) ? $c_category['s_name'] : ''));


  if(!$select) {
    $html  = '<div class="simple-cat simple-select level' . $level . '">';
    $html .= '<input type="hidden" id="' . $id . '" name="' . $id . '" class="input-hidden ' . $id . '" value="' . $current . '"/>';
    $html .= '<span class="text round3 tr1"><span>' . ($c_category['s_name'] <> '' ? $c_category['s_name'] : __('Category', 'delta')) . '</span> <i class="fa fa-angle-down"></i></span>';
    $html .= '<div class="list">';
    $html .= '<div class="option info">' . __('Choose one category', 'delta') . '</div>';

    if($id <> 'catId') {
      $html .= '<div class="option bold' . ($root['pk_i_id'] == "" ? ' selected' : '') . '" data-id="">' . __('All', 'delta') . '</div>';
    }

    // Root cat
    foreach($categories as $c) {
      $disable = false;
      if($allow_parent == 0 && count(@$c['categories']) > 0) { $disable = true; }

      $html .= '<div class="option ' . ($disable ? 'nonclickable' : '') . ' root' . ($root['pk_i_id'] == $c['pk_i_id'] ? ' selected' : '') . '" data-id="' . $c['pk_i_id'] . '">' . $c['s_name'] . '</span></div>';

      // Sub cat level 1
      if(count(@$c['categories']) > 0 && $level >= 1) { 
        foreach($c['categories'] as $s1) {
          $disable = false;
          if($allow_parent == 0 && count($s1['categories']) > 0) { $disable = true; }

          $html .= '<div class="option ' . ($disable ? 'nonclickable' : '') . ' sub1' . ($current == $s1['pk_i_id'] ? ' selected' : '') . '" data-id="' . $s1['pk_i_id'] . '">' . $s1['s_name'] . '</span></div>';

          // Sub cat level 2
          if(count($s1['categories']) > 0 && $level >= 2) { 
            foreach($s1['categories'] as $s2) {
              $disable = false;
              if($allow_parent == 0 && count($s2['categories']) > 0) { $disable = true; }

              $html .= '<div class="option ' . ($disable ? 'nonclickable' : '') . ' sub2' . ($current == $s2['pk_i_id'] ? ' selected' : '') . '" data-id="' . $s2['pk_i_id'] . '">' . $s2['s_name'] . '</span></div>';

              // Sub cat level 3
              if(count($s2['categories']) > 0 && $level >= 3) { 
                foreach($s2['categories'] as $s3) {
                  $html .= '<div class="option sub3' . ($current == $s3['pk_i_id'] ? ' selected' : '') . '" data-id="' . $s3['pk_i_id'] . '">' . $s3['s_name'] . '</span></div>';
                }
              }

            }
          }
        }
      }
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;

  } else {
    $html  = '<select class="' . $id . '" id="' . $id . '" name="' . $id . '">';
    $html .= '<option value="" ' . ($root['pk_i_id'] == "" ? ' selected="selected"' : '') . '>' . __('All categories', 'delta') . '</option>';

    foreach($categories as $c) {
      $html .= '<option ' . ($root['pk_i_id'] == $c['pk_i_id'] ? ' selected="selected"' : '') . ' value="' . $c['pk_i_id'] . '">' . $c['s_name'] . '</option>';

      // Sub cat level 1
      if(count(@$c['categories']) > 0 && $level >= 1) { 
        foreach($c['categories'] as $s1) {
          $html .= '<option ' . ($current == $s1['pk_i_id'] ? ' selected="selected"' : '') . ' value="' . $s1['pk_i_id'] . '">- ' . $s1['s_name'] . '</option>';

          // Sub cat level 2
          if(count($s1['categories']) > 0 && $level >= 2) { 
            foreach($s1['categories'] as $s2) {
              $html .= '<option ' . ($current == $s2['pk_i_id'] ? ' selected="selected"' : '') . ' value="' . $s2['pk_i_id'] . '">-- ' . $s2['s_name'] . '</option>';

              // Sub cat level 3
              if(count($s2['categories']) > 0 && $level >= 3) { 
                foreach($s2['categories'] as $s3) {
                  $html .= '<option ' . ($current == $s3['pk_i_id'] ? ' selected="selected"' : '') . ' value="' . $s3['pk_i_id'] . '">--- ' . $s3['s_name'] . '</option>';
                }
              }

            }
          }
        }
      }
    }

    $html .= '</select>';

    return $html;

  }
}



// SIMPLE SELLER TYPE SELECT
function del_simple_seller( $select = false ) {
  $id = Params::getParam('sCompany');

  if($id !== '' && $id !== null) {
    $id_mod = $id + 1;
  } else {
    $id_mod = 0;
  }

  $name = del_get_simple_name($id_mod, 'seller_type');
  $name = ($name == '' ? __('Seller type', 'delta') : $name);


  if( !$select ) {
    $html  = '<div class="simple-seller simple-select">';
    $html .= '<input type="hidden" name="sCompany" class="input-hidden" value="' . Params::getParam('sCompany') . '"/>';
    $html .= '<span class="text round3 tr1"><span>' . $name . '</span> <i class="fa fa-angle-down"></i></span>';
    $html .= '<div class="list">';
    $html .= '<div class="option info">' . __('Choose seller type', 'delta') . '</div>';
    $html .= '<div class="option bold' . ($id_mod == 0 ? ' selected' : '') . '" data-id="">' . __('All', 'delta') . '</div>';

    $html .= '<div class="option' . ($id_mod == "1" ? ' selected' : '') . '" data-id="0">' . __('Personal', 'delta') . '</span></div>';
    $html .= '<div class="option' . ($id_mod == "2" ? ' selected' : '') . '" data-id="1">' . __('Company', 'delta') . '</span></div>';

    $html .= '</div>';
    $html .= '</div>';

    return $html;

  } else {

    $html  = '<select class="sCompany" id="sCompany" name="sCompany">';
    $html .= '<option value="" ' . ($id_mod == "0" ? ' selected="selected"' : '') . '>' . __('All sellers', 'delta') . '</option>';
    $html .= '<option value="0" ' . ($id_mod == "1" ? ' selected="selected"' : '') . '>' . __('Personal', 'delta') . '</option>';
    $html .= '<option value="1" ' . ($id_mod == "2" ? ' selected="selected"' : '') . '>' . __('Company', 'delta') . '</option>';
    $html .= '</select>';

    return $html;

  }
}



// SIMPLE TRANSACTION TYPE SELECT
function del_simple_transaction( $select = false, $item_id = false ) {
  if((osc_is_publish_page() || osc_is_edit_page()) && del_get_session('sTransaction') <> '') {
    $id = del_get_session('sTransaction');
  } else {
    $id = Params::getParam('sTransaction');
  }

  if( $item_id == '' ) {
    $item_id = osc_item_id();
  }

  if( $item_id > 0 ) {
    $id = del_item_extra( $item_id );
    $id = $id['i_transaction'];
  }

  $name = del_get_simple_name($id, 'transaction');
  $name = ($name == '' ? __('Transaction', 'delta') : $name);

  $options =  del_list_options('transaction');


  if( !$select ) {
    $html  = '<div class="simple-transaction simple-select">';
    $html .= '<input type="hidden" name="sTransaction" class="input-hidden" value="' . $id . '"/>';
    $html .= '<span class="text round3 tr1"><span>' . $name . '</span> <i class="fa fa-angle-down"></i></span>';
    $html .= '<div class="list">';
    $html .= '<div class="option info">' . __('Choose transaction type', 'delta') . '</div>';

    foreach($options as $n => $v) {
      $html .= '<div class="option ' . ($n == 0 ? 'bold' : '') . ($id == $n ? ' selected' : '') . '" data-id="' . $n . '">' . $v . '</span></div>';
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;

  } else {

    $html  = '<select class="sTransaction" id="sTransaction" name="sTransaction">';

    foreach($options as $n => $v) {
      $html .= '<option value="' . $n . '" ' . ($id == $n ? ' selected="selected"' : '') . '>' . $v . '</option>';
    }

    $html .= '</select>';

    return $html;

  }
}



// SIMPLE OFFER TYPE SELECT
function del_simple_condition( $select = false, $item_id = false ) {
  if((osc_is_publish_page() || osc_is_edit_page()) && del_get_session('sCondition') <> '') {
    $id = del_get_session('sCondition');
  } else {
    $id = Params::getParam('sCondition');
  }

  if( $item_id == '' ) {
    $item_id = osc_item_id();
  }

  if( $item_id > 0 ) {
    $id = del_item_extra( $item_id );
    $id = $id['i_condition'];
  }

  $name = del_get_simple_name($id, 'condition');
  $name = ($name == '' ? __('Condition', 'delta') : $name);

  $options =  del_list_options('condition');


  if( !$select ) {
    $html  = '<div class="simple-condition simple-select">';
    $html .= '<input type="hidden" name="sCondition" class="input-hidden" value="' . $id . '"/>';
    $html .= '<span class="text round3 tr1"><span>' . $name . '</span> <i class="fa fa-angle-down"></i></span>';
    $html .= '<div class="list">';
    $html .= '<div class="option info">' . __('Choose condition of item', 'delta') . '</div>';

    foreach($options as $n => $v) {
      $html .= '<div class="option ' . ($n == 0 ? 'bold' : '') . ($id == $n ? ' selected' : '') . '" data-id="' . $n . '">' . $v . '</span></div>';
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;

  } else {

    $html  = '<select class="sCondition" id="sCondition" name="sCondition">';

    foreach($options as $n => $v) {
      $html .= '<option value="' . $n . '" ' . ($id == $n ? ' selected="selected"' : '') . '>' . $v . '</option>';
    }

    $html .= '</select>';

    return $html;

  }
}



// SIMPLE CURRENCY SELECT (publish)
function del_simple_currency() {
  $currencies = osc_get_currencies();
  $item = osc_item(); 

  if((osc_is_publish_page() || osc_is_edit_page()) && del_get_session('currency') <> '') {
    $id = del_get_session('currency');
  } else {
    $id = Params::getParam('currency');
  }

  $currency = $id <> '' ? $id : osc_get_preference('currency', 'osclass');

  if( isset($item['fk_c_currency_code']) ) {
    $default_key = $item['fk_c_currency_code'];
  } elseif( isset( $currency ) && $currency <> '' ) {
    $default_key = $currency;
  } else {
    $default_key = $currencies[0]['pk_c_code'];
  }

  if($default_key <> '') {
    $default_currency = Currency::newInstance()->findByPrimaryKey($default_key);
  } else {
    $default_currency = array('pk_c_code' => '', 's_description' => '');
  }

  $html  = '<div class="simple-currency simple-select">';
  $html .= '<input type="hidden" name="currency" id="currency" class="input-hidden" value="' . $default_currency['pk_c_code'] . '"/>';
  $html .= '<span class="text round3 tr1"><span>' . $default_currency['pk_c_code'] . ' (' . $default_currency['s_description'] . ')</span> <i class="fa fa-angle-down"></i></span>';
  $html .= '<div class="list">';
  $html .= '<div class="option info">' . __('Currency', 'delta') . '</div>';

  foreach($currencies as $c) {
    $html .= '<div class="option' . ($c['pk_c_code'] == $default_key ? ' selected' : '') . '" data-id="' . $c['pk_c_code'] . '">' . $c['pk_c_code'] . ' (' . $c['s_description'] . ')</span></div>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}



// SIMPLE PRICE TYPE SELECT (publish)
function del_simple_price_type() {
  $item = osc_item(); 

  // Item edit
  if( isset($item['i_price']) ) {
    if( $item['i_price'] > 0 ) {
      $default_key = 0;
      $default_name = '<i class="fa fa-pencil help"></i> ' . __('Enter price', 'delta');
    } else if( $item['i_price'] == 0 ) {
      $default_key = 1;
      $default_name = '<i class="fa fa-cut help"></i> ' . __('Free', 'delta');
    } else if( $item['i_price'] == '' ) {
      $default_key = 2;
      $default_name = '<i class="fa fa-phone help"></i> ' . __('Check with seller', 'delta');
    } 
  
  // Item publish
  } else {
    $default_key = 0;
    $default_name = '<i class="fa fa-pencil help"></i> ' . __('Enter price', 'delta');
  }


  $html  = '<div class="simple-price-type simple-select">';
  $html .= '<span class="text round3 tr1"><span>' . $default_name . '</span> <i class="fa fa-angle-down"></i></span>';
  $html .= '<div class="list">';
  $html .= '<div class="option info">' . __('Choose price type', 'delta') . '</div>';

  $html .= '<div class="option' . ($default_key == 0 ? ' selected' : '') . '" data-id="0"><i class="fa fa-pencil help"></i> ' . __('Enter price', 'delta') . '</span></div>';
  $html .= '<div class="option' . ($default_key == 1 ? ' selected' : '') . '" data-id="1"><i class="fa fa-cut help"></i> ' . __('Free', 'delta') . '</span></div>';
  $html .= '<div class="option' . ($default_key == 2 ? ' selected' : '') . '" data-id="2"><i class="fa fa-phone help"></i> ' . __('Check with seller', 'delta') . '</span></div>';

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}


// SIMPLE PERIOD SELECT (search only)
function del_simple_period( $select = false ) {
  $id = Params::getParam('sPeriod');

  $name = del_get_simple_name($id, 'period');
  $name = ($name == '' ? __('Age', 'delta') : $name);

  $options =  del_list_options('period');


  if( !$select ) {
    $html  = '<div class="simple-period simple-select">';
    $html .= '<input type="hidden" name="sPeriod" class="input-hidden" value="' . $id . '"/>';
    $html .= '<span class="text round3 tr1"><span>' . $name . '</span> <i class="fa fa-angle-down"></i></span>';
    $html .= '<div class="list">';
    $html .= '<div class="option info">' . __('Choose period', 'delta') . '</div>';

    foreach($options as $n => $v) {
      $html .= '<div class="option ' . ($n == 0 ? 'bold' : '') . ($id == $n ? ' selected' : '') . '" data-id="' . $n . '">' . $v . '</span></div>';
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;

  } else {

    $html  = '<select class="sPeriod" id="sPeriod" name="sPeriod">';

    foreach($options as $n => $v) {
      $html .= '<option value="" ' . ($id == $n ? ' selected="selected"' : '') . '>' . $v . '</option>';
    }

    $html .= '</select>';

    return $html;

  }
}


// SIMPLE PERIOD LIST
function del_simple_period_list() {
  $id = Params::getParam('sPeriod');

  $name = del_get_simple_name($id, 'period');
  $name = ($name == '' ? __('Age', 'delta') : $name);

  $options =  del_list_options('period');
  $params = del_search_params_all();


  $html  = '<div class="simple-period simple-list">';
  $html .= '<input type="hidden" name="sPeriod" class="input-hidden" value="' . $id . '"/>';

  $html .= '<div class="list link-check-box">';

  foreach($options as $n => $v) {
    $params['sPeriod'] = $n;
    $html .= '<a href="' . osc_search_url($params) . '" ' . ($id == $n ? 'class="active"' : '') . ' data-name="sPeriod" data-val="' . $n . '">' . $v . '</a>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}


// SIMPLE TRANSACTION LIST
function del_simple_transaction_list() {
  $id = Params::getParam('sTransaction');

  $name = del_get_simple_name($id, 'transaction');
  $name = ($name == '' ? __('Transaction', 'delta') : $name);

  $options =  del_list_options('transaction');
  $params = del_search_params_all();


  $html  = '<div class="simple-transaction simple-list">';
  $html .= '<input type="hidden" name="sTransaction" class="input-hidden" value="' . $id . '"/>';

  $html .= '<div class="list link-check-box">';

  foreach($options as $n => $v) {
    $params['sTransaction'] = $n;
    $html .= '<a href="' . osc_search_url($params) . '" ' . ($id == $n ? 'class="active"' : '') . ' data-name="sTransaction" data-val="' . $n . '">' . $v . '</a>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}


// SIMPLE CONDITION LIST
function del_simple_condition_list() {
  $id = Params::getParam('sCondition');

  $name = del_get_simple_name($id, 'condition');
  $name = ($name == '' ? __('Condition', 'delta') : $name);

  $options =  del_list_options('condition');
  $params = del_search_params_all();


  $html  = '<div class="simple-condition simple-list">';
  $html .= '<input type="hidden" name="sCondition" class="input-hidden" value="' . $id . '"/>';

  $html .= '<div class="list link-check-box">';

  foreach($options as $n => $v) {
    $params['sCondition'] = $n;
    $html .= '<a href="' . osc_search_url($params) . '" ' . ($id == $n ? 'class="active"' : '') . ' data-name="sCondition" data-val="' . $n . '">' . $v . '</a>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}




// Cookies work
if(!function_exists('mb_set_cookie')) {
  function mb_set_cookie($name, $val) {
    Cookie::newInstance()->set_expires( 86400 * 30 );
    Cookie::newInstance()->push($name, $val);
    Cookie::newInstance()->set();
  }
}

if(!function_exists('mb_get_cookie')) {
  function mb_get_cookie($name) {
    return Cookie::newInstance()->get_value($name);
  }
}

if(!function_exists('mb_drop_cookie')) {
  function mb_drop_cookie($name) {
    Cookie::newInstance()->pop($name);
  }
}


// FIND ROOT CATEGORY OF SELECTED
function del_category_root( $category_id ) {
  $category = Category::newInstance()->findRootCategory( $category_id );
  return $category;
}


// CHECK IF THEME IS DEMO
function del_is_demo() {
  if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'],'abprofitrade') !== false)) {
    return true;
  } else {
    return false;
  }
}


// CREATE ITEM (in loop)
function del_draw_item($c = NULL, $premium = false, $class = false) {
  if($premium){
    $filename ='loop-single-premium.php';
  } else {
    $filename = 'loop-single.php';
  }

  if(function_exists('osc_current_web_theme_path_value')) {
    include osc_current_web_theme_path_value($filename);
  } else {
    include $filename;
  }
}



// RANDOM LATEST ITEMS ON HOME PAGE
function del_random_items($numItems = 10, $category = array(), $withPicture = false) {
  $max_items = osc_get_preference('maxLatestItems@home', 'osclass');

  if($max_items == '' or $max_items == 0) {
    $max_items = 24;
  }

  $numItems = $max_items;

  $withPicture = del_param('latest_picture');
  $randomOrder = del_param('latest_random');
  $premiums = del_param('latest_premium');
  $category = del_param('latest_category');



  $randSearch = Search::newInstance();
  $randSearch->dao->select(DB_TABLE_PREFIX.'t_item.* ');
  $randSearch->dao->from( DB_TABLE_PREFIX.'t_item use index (PRIMARY)' );

  // where
  $whe  = DB_TABLE_PREFIX.'t_item.b_active = 1 AND ';
  $whe .= DB_TABLE_PREFIX.'t_item.b_enabled = 1 AND ';
  $whe .= DB_TABLE_PREFIX.'t_item.b_spam = 0 AND ';

  if($premiums == 1) {
    $whe .= DB_TABLE_PREFIX.'t_item.b_premium = 1 AND ';
  }

  $whe .= '('.DB_TABLE_PREFIX.'t_item.b_premium = 1 || '.DB_TABLE_PREFIX.'t_item.dt_expiration >= \''. date('Y-m-d H:i:s').'\') ';

  if( $category <> '' and $category > 0 ) {
    $subcat_list = Category::newInstance()->findSubcategories( $category );
    $subcat_id = array();
    $subcat_id[] = $category;

    foreach( $subcat_list as $s) {
      $subcat_id[] = $s['pk_i_id'];
    }

    $listCategories = implode(', ', $subcat_id);

    $whe .= ' AND '.DB_TABLE_PREFIX.'t_item.fk_i_category_id IN ('.$listCategories.') ';
  }



  if($withPicture) {
    $prem_where = ' AND ' . $whe;

    $randSearch->dao->from( '(' . sprintf("select %st_item.pk_i_id FROM %st_item, %st_item_resource WHERE %st_item_resource.s_content_type LIKE '%%image%%' AND %st_item.pk_i_id = %st_item_resource.fk_i_item_id %s GROUP BY %st_item.pk_i_id ORDER BY %st_item.dt_pub_date DESC LIMIT %s", DB_TABLE_PREFIX, DB_TABLE_PREFIX, DB_TABLE_PREFIX, DB_TABLE_PREFIX, DB_TABLE_PREFIX, DB_TABLE_PREFIX, $prem_where, DB_TABLE_PREFIX, DB_TABLE_PREFIX, $numItems) . ') AS LIM' );
  } else {
    $prem_where = ' WHERE ' . $whe;

    $randSearch->dao->from( '(' . sprintf("select %st_item.pk_i_id FROM %st_item %s ORDER BY %st_item.dt_pub_date DESC LIMIT %s", DB_TABLE_PREFIX, DB_TABLE_PREFIX, $prem_where, DB_TABLE_PREFIX, $numItems) . ') AS LIM' );
  }

  $randSearch->dao->where(DB_TABLE_PREFIX.'t_item.pk_i_id = LIM.pk_i_id');
  

  // group by & order & limit
  $randSearch->dao->groupBy(DB_TABLE_PREFIX.'t_item.pk_i_id');

  if(!$randomOrder) {
    $randSearch->dao->orderBy(DB_TABLE_PREFIX.'t_item.dt_pub_date DESC');
  } else {
    $randSearch->dao->orderBy('RAND()');
  }

  $randSearch->dao->limit($numItems);

  $rs = $randSearch->dao->get();

  if($rs === false){
    return array();
  }
  if( $rs->numRows() == 0 ) {
    return array();
  }

  $items = $rs->result();
  return Item::newInstance()->extendData($items);
}



// ITEM LOOP FORMAT LOCATION
function del_item_location($premium = false, $long_format = false) {
  if(!$premium) {
    if($long_format) {
      $loc = array_filter(array(osc_item_city(), osc_item_region(), osc_item_country_code()));
    } else {
      $loc = array_filter(array(osc_item_city(), (osc_item_city() == '' ? osc_item_region() : ''), osc_item_country_code()));
    }
  } else {
    $loc = array_filter(array(osc_premium_city(), (osc_premium_city() == '' ? osc_premium_region() : ''), osc_premium_country_code()));
  }

  return implode(', ', $loc);
}

function del_item_location_url($premium = false) {
  $array = array('page' => 'search');
  
  if(!$premium) {
    if(osc_item_city_id() > 0) {
      $array['sCity'] = osc_item_city_id();
    } else {
      $array['sCity'] = osc_item_city();
      
      if(osc_item_region_id() > 0) {
        $array['sRegion'] = osc_item_region_id();
      } else {
        $array['sRegion'] = osc_item_region();
        
        if(osc_item_country_code() > 0) {
          $array['sCountry'] = osc_item_country_code();
        } else {
          $array['sCountry'] = osc_item_country();
        }
      }
    }
  } else {
    if(osc_premium_city_id() > 0) {
      $array['sCity'] = osc_premium_city_id();
    } else {
      $array['sCity'] = osc_premium_city();
      
      if(osc_premium_region_id() > 0) {
        $array['sRegion'] = osc_premium_region_id();
      } else {
        $array['sRegion'] = osc_premium_region();
        
        if(osc_premium_country_code() > 0) {
          $array['sCountry'] = osc_premium_country_code();
        } else {
          $array['sCountry'] = osc_premium_country();
        }
      }
    }
  }

  return osc_search_url($array);
}




// LOCATION FORMATER - USED ON SEARCH LIST
function del_location_format($country = null, $region = null, $city = null) { 
  if($country <> '') {
    if(strlen($country) == 2) {
      $country_full = Country::newInstance()->findByCode($country);
    } else {
      $country_full = Country::newInstance()->findByName($country);
    }

    if($region <> '') {
      if($city <> '') {
        return $city . ' ' . __('in', 'delta') . ' ' . $region . (osc_location_native_name_selector($country_full, 's_name') <> '' ? ' (' . osc_location_native_name_selector($country_full, 's_name') . ')' : '');
      } else {
        return $region . ' (' . osc_location_native_name_selector($country_full, 's_name') . ')';
      }
    } else { 
      if($city <> '') {
        return $city . ' ' . __('in', 'delta') . ' ' . osc_location_native_name_selector($country_full, 's_name');
      } else {
        return osc_location_native_name_selector($country_full, 's_name');
      }
    }
  } else {
    if($region <> '') {
      if($city <> '') {
        return $city . ' ' . __('in', 'delta') . ' ' . $region;
      } else {
        return $region;
      }
    } else { 
      if($city <> '') {
        return $city;
      } else {
        return __('Location not entered', 'delta');
      }
    }
  }
}



function mb_filter_extend() {
  // SEARCH - ALL - INDIVIDUAL - COMPANY TYPE
  Search::newInstance()->addJoinTable( DB_TABLE_PREFIX.'t_item_delta.fk_i_item_id', DB_TABLE_PREFIX.'t_item_delta', DB_TABLE_PREFIX.'t_item.pk_i_id = '.DB_TABLE_PREFIX.'t_item_delta.fk_i_item_id', 'LEFT OUTER' ) ; // Mod


  // SEARCH - TRANSACTION
  if(Params::getParam('sTransaction') > 0) {
    Search::newInstance()->addConditions(sprintf("%st_item_delta.i_transaction = %d", DB_TABLE_PREFIX, Params::getParam('sTransaction')));
  }


  // SEARCH - CONDITION
  if(Params::getParam('sCondition') > 0) {
    Search::newInstance()->addConditions(sprintf("%st_item_delta.i_condition = %d", DB_TABLE_PREFIX, Params::getParam('sCondition')));
  }


  // SEARCH - PERIOD
  if(Params::getParam('sPeriod') > 0) {
    $date_from = date('Y-m-d', strtotime(' -' . Params::getParam('sPeriod') . ' day', time()));
    Search::newInstance()->addConditions(sprintf('cast(%st_item.dt_pub_date as date) > "%s"', DB_TABLE_PREFIX, $date_from));
  }

  // SEARCH - USER ID
  if(Params::getParam('userId') > 0) {
    Search::newInstance()->addConditions(sprintf("%st_item.fk_i_user_id = %d", DB_TABLE_PREFIX, Params::getParam('userId')));
  }


  // SEARCH - COMPANY
  if(Params::getParam('sCompany') <> '' and Params::getParam('sCompany') <> null) {
    Search::newInstance()->addJoinTable( DB_TABLE_PREFIX.'t_user.pk_i_id', DB_TABLE_PREFIX.'t_user', DB_TABLE_PREFIX.'t_item.fk_i_user_id = '.DB_TABLE_PREFIX.'t_user.pk_i_id', 'LEFT OUTER' ) ; // Mod

    if(Params::getParam('sCompany') == 1) {
      Search::newInstance()->addConditions(sprintf("%st_user.b_company = 1", DB_TABLE_PREFIX));
    } else {
      Search::newInstance()->addConditions(sprintf("coalesce(%st_user.b_company, 0) <> 1", DB_TABLE_PREFIX));
    }
  }
}

osc_add_hook('search_conditions', 'mb_filter_extend');



// GET SELECTED SEARCH PARAMETERS
function del_search_params() {
 return array(
   'sCategory' => Params::getParam('sCategory'),
   'sCountry' => Params::getParam('sCountry'),
   'sRegion' => Params::getParam('sRegion'),
   'sCity' => Params::getParam('sCity'),
   //'sPriceMin' => Params::getParam('sPriceMin'),
   //'sPriceMin' => Params::getParam('sPriceMax'),
   'sCompany' => Params::getParam('sCompany'),
   'sShowAs' => Params::getParam('sShowAs'),
   'sOrder' => Params::getParam('sOrder'),
   'iOrderType' => Params::getParam('iOrderType')
  );
}


// GET ALL PARAMS
function del_search_params_all() {
  $params = Params::getParamsAsArray();
  unset($params['iPage']);
  return $params;
}


// FIND MAXIMUM PRICE
function del_max_price($cat_id = null, $country_code = null, $region_id = null, $city_id = null) {
  // Search by all parameters
  $allSearch = new Search();
  $allSearch->addCategory($cat_id);
  $allSearch->addCountry($country_code);
  $allSearch->addRegion($region_id);
  $allSearch->addCity($city_id);
  $allSearch->order('i_price', 'DESC');
  $allSearch->limit(0, 1);

  $result = $allSearch->doSearch();
  $result = $result[0];

  $max_price = isset($result['i_price']) ? $result['i_price'] : 0;


  // FOLLOWING BLOCK LOOKS FOR MAX-PRICE IF IT IS 0
  // City is set, find max price by Region
  if($max_price <= 0 && isset($city_id) && $city_id <> '') {
    $regSearch = new Search();
    $regSearch->addCategory($cat_id);
    $regSearch->addCountry($country_code);
    $regSearch->addRegion($region_id);
    $regSearch->order('i_price', 'DESC');
    $regSearch->limit(0, 1);

    $result = $regSearch->doSearch();
    $result = $result[0];

    $max_price = isset($result['i_price']) ? $result['i_price'] : 0;
  }


  // Region is set, find max price by Country
  if($max_price <= 0 && isset($region_id) && $region_id <> '') {
    $regSearch = new Search();
    $regSearch->addCategory($cat_id);
    $regSearch->addCountry($country_code);
    $regSearch->order('i_price', 'DESC');
    $regSearch->limit(0, 1);

    $result = $regSearch->doSearch();
    $result = $result[0];

    $max_price = isset($result['i_price']) ? $result['i_price'] : 0;
  }


  // Country is set, find max price WorldWide
  if($max_price <= 0 && isset($country_code) && $country_code <> '') {
    $regSearch = new Search();
    $regSearch->addCategory($cat_id);
    $regSearch->order('i_price', 'DESC');
    $regSearch->limit(0, 1);

    $result = $regSearch->doSearch();
    $result = $result[0];

    $max_price = isset($result['i_price']) ? $result['i_price'] : 0;
  }


  // Category is set, find max price in all Categories
  if($max_price <= 0 && isset($region_id) && $region_id <> '') {
    $regSearch = new Search();
    $regSearch->addCategory($cat_id);
    $regSearch->order('i_price', 'DESC');
    $regSearch->limit(0, 1);

    $result = $regSearch->doSearch();
    $result = $result[0];

    $max_price = isset($result['i_price']) ? $result['i_price'] : 0;
  }


  // If max_price is still 0, set it to 1 to avoid slider defect
  if($max_price <= 0) {
    $max_price = 1000000;
  }


  return array(
    'max_price' => $max_price/1000000,
    'max_currency' => del_param('def_cur')
  );
}


// CHECK IF AJAX IMAGE UPLOAD ON PUBLISH-EDIT PAGE CAN BE USED (from osclass 3.3)
function del_ajax_image_upload() {
  if(class_exists('Scripts')) {
    return Scripts::newInstance()->registered['jquery-fineuploader'] && method_exists('ItemForm', 'ajax_photos');
  }
}


// CLOSE BUTTON RETRO-COMPATIBILITY
if( !OC_ADMIN ) {
  if( !function_exists('add_close_button_action') ) {
    function add_close_button_action(){
      echo '<script type="text/javascript">';
      echo '$(".flashmessage .ico-close").click(function(){';
      echo '$(this).parent().hide();';
      echo '});';
      echo '</script>';
    }
    osc_add_hook('footer', 'add_close_button_action') ;
  }
}


if(!function_exists('message_ok')) {
  function message_ok( $text ) {
    $final  = '<div style="padding: 1%;width: 98%;margin-bottom: 15px;" class="flashmessage flashmessage-ok flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


if(!function_exists('message_error')) {
  function message_error( $text ) {
    $final  = '<div style="padding: 1%;width: 98%;margin-bottom: 15px;" class="flashmessage flashmessage-error flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


// RETRO COMPATIBILITY IF FUNCTION DOES NOT EXIST
if(!function_exists('osc_count_countries')) {
  function osc_count_countries() {
    if ( !View::newInstance()->_exists('contries') ) {
      View::newInstance()->_exportVariableToView('countries', Search::newInstance()->listCountries( ">=", "country_name ASC" ) );
    }
    return View::newInstance()->_count('countries');
  }
}



// GET USER FROM SESSION
if(!function_exists('osc_get_user_row')) {
  function osc_get_user_row($id) {
    if($id <= 0) {
      return false;
    }

    if(View::newInstance()->_exists('user_' . $id)) {
      return View::newInstance()->_get('user_' . $id);
    }
    
    $user = User::newInstance()->findByPrimaryKey($id);
    View::newInstance()->_exportVariableToView('user_' . $id, $user);
    
    return $user;
  }
}

// GET ITEM FROM SESSION
if(!function_exists('osc_get_item_row')) {
  function osc_get_item_row($id) {
    if($id <= 0) {
      return false;
    }

    if(View::newInstance()->_exists('item_' . $id)) {
      return View::newInstance()->_get('item_' . $id);
    }
    
    $item = Item::newInstance()->findByPrimaryKey($id);
    View::newInstance()->_exportVariableToView('item_' . $id, $item);
    
    return $item;
  }
}


// GET CATEGORY FROM SESSION
if(!function_exists('osc_get_category_row')) {
  function osc_get_category_row($id) {
    if($id <= 0) {
      return false;
    }

    if(View::newInstance()->_exists('category_' . $id)) {
      return View::newInstance()->_get('category_' . $id);
    }
    
    $category = Category::newInstance()->findByPrimaryKey($id);
    View::newInstance()->_exportVariableToView('category_' . $id, $category);
    
    return $category;
  }
}


// GET COUNTRY FROM SESSION
if(!function_exists('osc_get_country_row')) {
  function osc_get_country_row($code) {
    if($code == '') {
      return false;
    }

    if(View::newInstance()->_exists('country_' . $code)) {
      return View::newInstance()->_get('country_' . $code);
    }
    
    $country = Country::newInstance()->findByCode($code);
    View::newInstance()->_exportVariableToView('country_' . $code, $country);
    
    return $country;
  }
}


// GET REGION FROM SESSION
if(!function_exists('osc_get_region_row')) {
  function osc_get_region_row($id) {
    if($id <= 0) {
      return false;
    }

    if(View::newInstance()->_exists('region_' . $id)) {
      return View::newInstance()->_get('region_' . $id);
    }
    
    $region = Region::newInstance()->findByPrimaryKey($id);
    View::newInstance()->_exportVariableToView('region_' . $id, $region);
    
    return $region;
  }
}


// GET CITY FROM SESSION
if(!function_exists('osc_get_city_row')) {
  function osc_get_city_row($id) {
    if($id <= 0) {
      return false;
    }

    if(View::newInstance()->_exists('city_' . $id)) {
      return View::newInstance()->_get('city_' . $id);
    }
    
    $city = City::newInstance()->findByPrimaryKey($id);
    View::newInstance()->_exportVariableToView('city_' . $id, $city);
    
    return $city;
  }
}


// GET LOGGED USER ROW FROM SESSION
if(!function_exists('osc_logged_user')) {
  function osc_logged_user() {
    if(osc_is_web_user_logged_in()) {
      if(View::newInstance()->_exists('_loggedUser')) {
        return View::newInstance()->_get('_loggedUser');
      }
    }
    
    return false;
  }
}


// GET CURRENT LANGUAGE OF USER
function mb_get_current_user_locale() {
  return OSCLocale::newInstance()->findByPrimaryKey(osc_current_user_locale());
}



// FIX PRICE FORMAT OF PREMIUM ITEMS
function del_premium_formated_price($price = null) {
  if($price == '') {
    $price = osc_premium_price();
  }

  return (string) del_premium_format_price($price);
}

function del_premium_format_price($price, $symbol = null) {
  if ($price === null) return osc_apply_filter ('item_price_null', __('Check with seller', 'delta') );
  if ($price == 0) return osc_apply_filter ('item_price_zero', __('Free', 'delta') );

  if($symbol==null) { $symbol = osc_premium_currency_symbol(); }

  $price = $price/1000000;

  $currencyFormat = osc_locale_currency_format();
  $currencyFormat = str_replace('{NUMBER}', number_format($price, osc_locale_num_dec(), osc_locale_dec_point(), osc_locale_thousands_sep()), $currencyFormat);
  $currencyFormat = str_replace('{CURRENCY}', $symbol, $currencyFormat);
  return osc_apply_filter('premium_price', $currencyFormat );
}


function del_ajax_item_format_price($price, $symbol_code) {
  if ($price === null) return __('Check with seller', 'delta');
  if ($price == 0) return __('Free', 'delta');
  return round($price/1000000, 2) . ' ' . $symbol_code;
}


AdminMenu::newInstance()->add_menu(__('Theme Setting', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/configure.php'), 'delta_menu');
AdminMenu::newInstance()->add_submenu('delta_menu', __('Configure', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/configure.php'), 'settings_delta1');
AdminMenu::newInstance()->add_submenu('delta_menu', __('Advertisement', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/banner.php'), 'settings_delta2');
AdminMenu::newInstance()->add_submenu('delta_menu', __('Category Icons', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/category.php'), 'settings_delta3');
AdminMenu::newInstance()->add_submenu('delta_menu', __('Logo', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/header.php'), 'settings_delta4');
AdminMenu::newInstance()->add_submenu('delta_menu', __('Plugins', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/plugins.php'), 'settings_delta5');


function del_admin_toolbar() {
  AdminMenu::newInstance()->add_submenu_divider('appearance', __('Delta Theme Settings', 'delta'), 'delta_submenu');

  AdminMenu::newInstance()->add_submenu('appearance', __('Configure', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/configure.php'), 'settings_delta1');
  AdminMenu::newInstance()->add_submenu('appearance', __('Advertisement', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/banner.php'), 'settings_delta2');
  AdminMenu::newInstance()->add_submenu('appearance', __('Category Icons', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/category.php'), 'settings_delta3');
  AdminMenu::newInstance()->add_submenu('appearance', __('Logo', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/header.php'), 'settings_delta4');
  AdminMenu::newInstance()->add_submenu('appearance', __('Plugins', 'delta'), osc_admin_render_theme_url('oc-content/themes/delta/admin/plugins.php'), 'settings_delta5');
}

osc_add_hook('add_admin_toolbar_menus', 'del_admin_toolbar');

// GET SITE LOGO
function del_logo($image_only = false) {
  $src = '';
  $name = 'logo';
  $url = osc_apply_filter('logo_url', osc_current_web_theme_url('images/'));
  $path = osc_apply_filter('logo_path', WebThemes::newInstance()->getCurrentThemePath() . 'images/');


  if(del_param('default_logo') == 1 && file_exists($path . $name . '-default.png')) {
    $src = $url . $name . '-default.png';
  }

  // Check in theme folder
  if($src == '') {
    foreach(del_logo_extensions() as $ext) {
      if(file_exists($path . $name . '.' . $ext)) {
        $src = $url . $name . '.' . $ext;
        break;
      }
    }
  }
  
  // If it's child theme, check in parent theme folder
  if($src == '' && osc_current_web_theme_is_child() != '') {
    $url = str_replace('_child', '', $url);
    
    foreach(del_logo_extensions() as $ext) {
      if(file_exists($path . $name . '.' . $ext)) {
        $src = $url . $name . '.' . $ext;
        break;
      }
    }
  }
  
  // No logo found, use default anyway - never from child theme
  if($src == '') {
    $url = (osc_current_web_theme_is_child() != '' ? str_replace('_child', '', $url) : $url);
    $src = $url . $name . '-default.png';
  }

  // Need just pure image link
  if($image_only === true) {
    return $src;
  }

  //return '<img src="' . (del_is_lazy() ? del_get_load_image('transparent') : $src) . '" data-src="' . $src . '" alt="' . osc_esc_html(osc_page_title()) . '" class="' . (del_is_lazy() ? 'lazy' : '') . '"/>';
  return '<img src="' . $src . '" alt="' . osc_esc_html(osc_page_title()) . '"/>';
}


// LIST ALL POSSIBLE LOGO EXTENSIONS (png first so transparency is preferred)
function del_logo_extensions() {
  return array('png','webp','gif','jpg','jpeg');
}


// INSTALL & UPDATE OPTIONS
if(!function_exists('del_theme_install')) {
  $themeInfo = del_theme_info();

  function del_theme_install() {
    osc_set_preference('version', DELTA_THEME_VERSION, 'theme-delta');
    osc_set_preference('color', '#0B3A6E', 'theme-delta');
    osc_set_preference('color2', '#1B6B4A', 'theme-delta');
    osc_set_preference('color3', '#E31C23', 'theme-delta');
    osc_set_preference('site_phone', '+1 (800) 228-5651', 'theme-delta');
    osc_set_preference('site_email', 'support@dot.com', 'theme-delta');
    osc_set_preference('date_format', 'mm/dd', 'theme-delta');
    osc_set_preference('cat_icons', '1', 'theme-delta');
    osc_set_preference('footer_social_define', '0', 'theme-delta');
    osc_set_preference('footer_link', '1', 'theme-delta');
    osc_set_preference('default_logo', '1', 'theme-delta');
    osc_set_preference('def_cur', '$', 'theme-delta');
    osc_set_preference('def_view', '1', 'theme-delta');
    osc_set_preference('def_design', '', 'theme-delta');
    osc_set_preference('website_name', 'DeltaTheme', 'theme-delta');
    osc_set_preference('latest_picture', '0', 'theme-delta');
    osc_set_preference('latest_random', '1', 'theme-delta');
    osc_set_preference('latest_premium', '0', 'theme-delta');
    osc_set_preference('latest_category', '', 'theme-delta');
    osc_set_preference('latest_design', 'compact', 'theme-delta');
    osc_set_preference('publish_category', '4', 'theme-delta');
    osc_set_preference('premium_home', '1', 'theme-delta');
    osc_set_preference('premium_search', '1', 'theme-delta');
    osc_set_preference('premium_home_count', '4', 'theme-delta');
    osc_set_preference('premium_search_count', '12', 'theme-delta');
    osc_set_preference('premium_home_design', '', 'theme-delta');
    osc_set_preference('premium_search_design', 'compact', 'theme-delta');
    osc_set_preference('search_ajax', '1', 'theme-delta');
    osc_set_preference('forms_ajax', '1', 'theme-delta');
    osc_set_preference('post_required', '', 'theme-delta');
    osc_set_preference('post_extra_exclude', '', 'theme-delta');
    osc_set_preference('favorite_home', '1', 'theme-delta');
    osc_set_preference('favorite_count', '8', 'theme-delta');
    osc_set_preference('favorite_design', 'compact', 'theme-delta');
    osc_set_preference('blog_home', '1', 'theme-delta');
    osc_set_preference('blog_home_count', '4', 'theme-delta');
    osc_set_preference('blog_home_design', 'list', 'theme-delta');
    osc_set_preference('company_home', '1', 'theme-delta');
    osc_set_preference('company_home_count', '8', 'theme-delta');
    osc_set_preference('banners', '0', 'theme-delta');
    osc_set_preference('banner_optimize_adsense', '0', 'theme-delta');
    osc_set_preference('lazy_load', '0', 'theme-delta');
    osc_set_preference('public_items', '24', 'theme-delta');
    osc_set_preference('preview', '1', 'theme-delta');
    osc_set_preference('related', '1', 'theme-delta');
    osc_set_preference('related_count', '12', 'theme-delta');
    osc_set_preference('related_design', 'tiny', 'theme-delta');
    osc_set_preference('user_items', '1', 'theme-delta');
    osc_set_preference('user_items_count', '12', 'theme-delta');
    osc_set_preference('user_items_design', 'compact', 'theme-delta');
    osc_set_preference('def_locations', 'region', 'theme-delta');
    osc_set_preference('promote_home', 1, 'theme-delta');
    osc_set_preference('save_search_position', '', 'theme-delta');
    osc_set_preference('loc_box_region_search', 1, 'theme-delta');
    osc_set_preference('loc_box_city_search', 1, 'theme-delta');
    osc_set_preference('sample_favicons', 1, 'theme-delta');


    /* Banners */
    if(function_exists('delta_banner_list')) {
      foreach(del_banner_list() as $b) {
        osc_set_preference($b['id'], '', 'theme-delta');
      }
    }

    osc_reset_preferences();

    del_add_item_fields();  // add extra item fiels into database
  }
}


if(!function_exists('check_install_del_theme')) {
  function check_install_del_theme() {
    $current_version = del_param('version');

    if( !$current_version ) {
      del_theme_install();
    }
  }
}

check_install_del_theme();

// One-time Anuncios Cabo Verde visual brand alignment (colors + category icons)
function del_acv_brand_align() {
  if(osc_get_preference('acv_brand_v1', 'theme-delta') == '1') {
    return;
  }
  osc_set_preference('color', '#0B3A6E', 'theme-delta');
  osc_set_preference('color2', '#1B6B4A', 'theme-delta');
  osc_set_preference('color3', '#E31C23', 'theme-delta');
  osc_set_preference('cat_icons', '1', 'theme-delta');
  osc_set_preference('acv_brand_v1', '1', 'theme-delta');
  osc_reset_preferences();
}
del_acv_brand_align();


// WHEN NEW LISTING IS CREATED, ADD IT TO DELTA EXTRA TABLE
function del_new_item_extra($item) {
  $conn = DBConnectionClass::newInstance();
  $data = $conn->getOsclassDb();
  $comm = new DBCommandClass($data);
  $db_prefix = DB_TABLE_PREFIX;

  $query = "INSERT INTO {$db_prefix}t_item_delta (fk_i_item_id) VALUES ({$item['pk_i_id']})";
  $result = $comm->query($query);
}

osc_add_hook('posted_item', 'del_new_item_extra', 1);


// WHEN NEW CATEGORY IS CREATED, ADD IT TO DELTA EXTRA TABLE
function del_new_category_extra() {

  $conn = DBConnectionClass::newInstance();
  $data = $conn->getOsclassDb();
  $comm = new DBCommandClass($data);
  $db_prefix = DB_TABLE_PREFIX;

  $query = "INSERT INTO {$db_prefix}t_category_delta (fk_i_category_id) 
            SELECT c.pk_i_id FROM {$db_prefix}t_category c WHERE c.pk_i_id NOT IN (SELECT d.fk_i_category_id FROM {$db_prefix}t_category_delta d)";
  $result = $comm->query($query);
}

osc_add_hook('footer', 'del_new_category_extra');



// USER MENU FIX
function del_user_menu_fix() {
  $user = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
  View::newInstance()->_exportVariableToView('user', $user);
}

osc_add_hook('header', 'del_user_menu_fix');



// ADD THEME COLUMNS INTO ITEM TABLE
function del_add_item_fields() {
  ModelDEL::newInstance()->install();
}



// UPDATE THEME COLS ON ITEM POST-EDIT
function del_update_fields($item) {
  if(!isset($item['pk_i_id']) || $item['pk_i_id'] <= 0) {
    return false;
  }
  
  $fields = array(
    's_phone' => (Params::getParam('contactPhone') <> '' ? Params::getParam('contactPhone') : Params::getParam('sPhone')),
    'i_condition' => Params::getParam('sCondition'),
    'i_transaction' => Params::getParam('sTransaction'),
    'i_sold' => Params::getParam('sSold')
  );

  Item::newInstance()->dao->update(DB_TABLE_PREFIX.'t_item_delta', $fields, array('fk_i_item_id' => $item['pk_i_id']));
}

osc_add_hook('posted_item', 'del_update_fields', 1);
osc_add_hook('edited_item', 'del_update_fields', 1);


// GET DELTA ITEM EXTRA VALUES
function del_item_extra($item_id) {
  if($item_id > 0) {
    $db_prefix = DB_TABLE_PREFIX;
    $query = "SELECT * FROM {$db_prefix}t_item_delta s WHERE fk_i_item_id = " . $item_id . ";";
    $result = Item::newInstance()->dao->query($query);
    
    if($result) { 
      $prepare = $result->row();
      
      if(isset($prepare['fk_i_item_id']) && $prepare['fk_i_item_id'] > 0) {
        return $prepare;
      }
    }
  }
  
  return array(
    'fk_i_item_id' => $item_id,
    's_phone' => '',
    'i_condition' => null,
    'i_transaction' => null,
    'i_sold' => null
  );    
}



// FAVORITE ITEMS SUPPORT
function del_make_favorite($item_id = NULL) {
  $item_id = ($item_id === NULL ? osc_item_id() : $item_id);

  // SAVED ITEMS PLUGIN
  if(function_exists('svi_save_btn')) {
    $options = array();     // Let's keep options to be defined in plugin settings

    echo svi_save_btn($item_id, $options);  
  
  // FAVORITE ITEMS PLUGIN
  } else if(function_exists('fi_save_favorite')) {
    $options = array();
    
    echo '<div class="favorite">' . fi_save_favorite($item_id, $options) . '</div>';
  }
}


// COUNT INSTANT MESSENGER MESSAGES
function del_count_messages($user_id) {
  if($user_id > 0 && class_exists('ModelIM')) {
    $mes_counter = ModelIM::newInstance()->countMessagesByUserId($user_id); 
    $mes_counter = (isset($mes_counter['i_count']) ? $mes_counter['i_count'] : 0);
    return $mes_counter;
  } else {
    return 0;   
  }
}
  

// COUNT FAVORITE ITEMS
function del_count_favorite($user_id = NULL) {
  if($user_id !== NULL) { 
    // nothing
  } else if(osc_is_web_user_logged_in()) {
    $user_id = osc_logged_user_id();
    
  } else if(function_exists('fi_save_favorite')) {
    $user_id = mb_get_cookie('fi_user_id');
  }


  if($user_id > 0) {
    if(function_exists('svi_save_btn')) {
      return svi_count_user_items($user_id);
      
    } else if(class_exists('ModelFI')) {
      $db_prefix = DB_TABLE_PREFIX;

      $query = "SELECT count(*) as count FROM {$db_prefix}t_favorite_items i, {$db_prefix}t_favorite_list l WHERE l.list_id = i.list_id AND l.user_id = " . $user_id . ";";
      $result = Item::newInstance()->dao->query($query);
      if( !$result ) { 
        $prepare = array();
        return 0;
      } else {
        $prepare = @$result->row()['count'];
        return $prepare;
      }
    }
  }

  return 0;
}


// GET DELTA CATEGORY EXTRA VALUES
function del_category_extra($category_id) {
  if($category_id > 0) {
    $db_prefix = DB_TABLE_PREFIX;

    $query = "SELECT * FROM {$db_prefix}t_category_delta s WHERE fk_i_category_id = " . $category_id . ";";
    $result = Category::newInstance()->dao->query($query);
    if( !$result ) { 
      $prepare = array();
      return false;
    } else {
      $prepare = $result->row();
      return $prepare;
    }
  }
}






// KEEP VALUES OF INPUTS ON RELOAD
function del_post_preserve() {
  Session::newInstance()->_setForm('sPhone', Params::getParam('sPhone'));
  Session::newInstance()->_setForm('contactPhone', Params::getParam('contactPhone'));
  Session::newInstance()->_setForm('term', Params::getParam('term'));
  Session::newInstance()->_setForm('zip', Params::getParam('zip'));
  Session::newInstance()->_setForm('sCondition', Params::getParam('sCondition'));
  Session::newInstance()->_setForm('sTransaction', Params::getParam('sTransaction'));

  Session::newInstance()->_keepForm('sPhone');
  Session::newInstance()->_keepForm('contactPhone');
  Session::newInstance()->_keepForm('term');
  Session::newInstance()->_keepForm('zip');
  Session::newInstance()->_keepForm('sCondition');
  Session::newInstance()->_keepForm('sTransaction');
}

osc_add_hook('pre_item_post', 'del_post_preserve');


// DROP VALUES OF INPUTS ON SUCCESSFUL PUBLISH
function del_post_drop() {
  Session::newInstance()->_dropKeepForm('sPhone');
  Session::newInstance()->_dropKeepForm('contactPhone');
  Session::newInstance()->_dropKeepForm('term');
  Session::newInstance()->_dropKeepForm('zip');
  Session::newInstance()->_dropKeepForm('sCondition');
  Session::newInstance()->_dropKeepForm('sTransaction');

  Session::newInstance()->_clearVariables();
}

osc_add_hook('posted_item', 'del_post_drop');



// GET VALUES FROM SESSION ON PUBLISH PAGE
function del_get_session( $param ) {
  return Session::newInstance()->_getForm($param);
}


// COMPATIBILITY FUNCTIONS
if(!function_exists('osc_is_register_page')) {
  function osc_is_register_page() {
    return osc_is_current_page("register", "register");
  }
}

if(!function_exists('osc_is_edit_page')) {
  function osc_is_edit_page() {
    return osc_is_current_page('item', 'item_edit');
  }
}


// DEFAULT ICONS ARRAY
function del_default_icons() {
  $icons = array(
    1 => 'fa-newspaper', 2 => 'fa-motorcycle', 3 => 'fa-graduation-cap', 4 => 'fa-home', 5 => 'fa-wrench', 6 => 'fa-users', 7 => 'fa-venus-mars', 8 => 'fa-briefcase', 9 => 'fa-paw', 
    10 => 'fa-paint-brush', 11 => 'fa-exchange', 12 => 'fa-newspaper', 13 => 'fa-camera', 14 => 'fa-tablet', 15 => 'fa-mobile', 16 => 'fa-shopping-bag', 
    17 => 'fa-laptop', 18 => 'fa-mobile', 19 => 'fa-lightbulb-o', 20 => 'fa-soccer-ball-o', 21 => 'fa-s15', 22 => 'fa-medkit', 23 => 'fa-home', 24 => 'fa-clock-o', 
    25 => 'fa-microphone', 26 => 'fa-bicycle', 27 => 'fa-ticket', 28 => 'fa-plane', 29 => 'fa-television', 30 => 'fa-ellipsis-h', 31 => 'fa-car', 32 => 'fa-gears', 
    33 => 'fa-motorcycle', 34 => 'fa-ship', 35 => 'fa-bus', 36 => 'fa-truck', 37 => 'fa-ellipsis-h', 38 => 'fa-laptop', 39 => 'fa-language', 40 => 'fa-microphone', 
    41 => 'fa-graduation-cap', 42 => 'fa-ellipsis-h', 43 => 'fa-building-o', 44 => 'fa-building', 45 => 'fa-refresh', 46 => 'fa-exchange', 47 => 'fa-plane', 48 => 'fa-car', 
    49 => 'fa-window-minimize', 50 => 'fa-suitcase', 51 => 'fa-shopping-basket', 52 => 'fa-child', 53 => 'fa-microphone', 54 => 'fa-laptop', 55 => 'fa-music', 
    56 => 'fa-stethoscope', 57 => 'fa-star', 58 => 'fa-home', 59 => 'fa-truck', 60 => 'fa-wrench', 61 => 'fa-pencil', 62 => 'fa-ellipsis-h', 63 => 'fa-refresh', 
    64 => 'fa-sun-o', 65 => 'fa-star', 66 => 'fa-music', 67 => 'fa-wheelchair', 68 => 'fa-key', 69 => 'fa-venus', 70 => 'fa-mars', 71 => 'fa-mars-double', 
    72 => 'fa-venus-double', 73 => 'fa-genderless', 74 => 'fa-phone', 75 => 'fa-money', 76 => 'fa-television', 77 => 'fa-paint-brush', 78 => 'fa-book', 79 => 'fa-headphones', 
    80 => 'fa-graduation-cap', 81 => 'fa-paper-plane-o', 82 => 'fa-medkit', 83 => 'fa-users', 84 => 'fa-internet-explorer', 85 => 'fa-gavel', 86 => 'fa-wrench', 
    87 => 'fa-industry', 88 => 'fa-newspaper', 89 => 'fa-wheelchair', 90 => 'fa-home', 91 => 'fa-spoon', 92 => 'fa-exchange', 93 => 'fa-gavel', 94 => 'fa-microchip', 
    95 => 'fa-ellipsis-h', 999 => 'fa-newspaper'
  );

  return $icons;
}


function del_default_colors() {
  $colors = array(1 => '#F44336', 2 => '#00BCD4', 3 => '#009688', 4 => '#FDE74C', 5 => '#8BC34A', 6 => '#D32F2F', 7 => '#2196F3', 8 => '#777', 999 => '#F44336');
  return $colors;
}


function del_get_cat_icon($id, $string = false) {
  $category = Category::newInstance()->findByPrimaryKey($id);
  $category_extra = del_category_extra($id);
  $default_icons = del_default_icons();
  $url = osc_apply_filter('category_icon_url', osc_current_web_theme_url('images/'));
  $path = osc_apply_filter('category_icon_path', WebThemes::newInstance()->getCurrentThemePath() . 'images/');
  $url_parent = (osc_current_web_theme_is_child() != '' ? str_replace('_child', '', $url) : '');
  $path_parent = (osc_current_web_theme_is_child() != '' ? str_replace('_child', '', $path) : '');
  
  
  
  if(del_param('cat_icons') == 1) { 
    if($category_extra['s_icon'] <> '') {
      $icon_code = $category_extra['s_icon'];
    } else {
      if(isset($default_icons[$id]) && $default_icons[$id] <> '') {
        $icon_code = $default_icons[$id];
      } else {
        $icon_code = $default_icons[999];
      }
    }

    if($string) {
      return $icon_code;
    } else {
      return '<i class="fa ' . $icon_code . '"></i>';
    }
    
  } else {
    if($string) {
      if(file_exists($path . 'small_cat/' . $id . '.png')) {
        return $url . 'small_cat/' . $id . '.png';
        
      } else if($path_parent != '' && file_exists($path_parent . 'small_cat/' . $id . '.png')) {
        return $url_parent . 'small_cat/' . $id . '.png';

      } else if(del_param('sample_images') == 1 && file_exists($path_parent . 'small_cat/sample/' . $id . '.png')) {
        return $url_parent . 'small_cat/sample/' . $id . '.png';

      } else if(file_exists($path . 'small_cat/default.png')) {
        return $url . 'small_cat/default.png';
        
      } else {
        return $url_parent . 'small_cat/default.png';
      }
      
    } else {
      if(file_exists($path . 'small_cat/' . $id . '.png')) {
        return '<img src="' . $url . 'small_cat/' . $id . '.png" alt="' . osc_esc_html($category['s_name']) . '" />';
        
      } else if($path_parent != '' && file_exists($path_parent . 'small_cat/' . $id . '.png')) {
        return '<img src="' . $url_parent . 'small_cat/' . $id . '.png" alt="' . osc_esc_html($category['s_name']) . '" />';

      } else if(del_param('sample_images') == 1 && file_exists($path_parent . 'small_cat/sample/' . $id . '.png')) {
        return '<img src="' . $url_parent . 'small_cat/sample/' . $id . '.png" alt="' . osc_esc_html($category['s_name']) . '" />';

      } else if(file_exists($path . 'small_cat/default.png')) {
        return '<img src="' . $url . 'small_cat/default.png" alt="' . osc_esc_html($category['s_name']) . '" />';

      } else {
        return '<img src="' . $url_parent . 'small_cat/default.png" alt="' . osc_esc_html($category['s_name']) . '" />';
      }
    }
  }

  if(!$string) {
    return $icon;
  }
}


function del_get_cat_color( $id ) {
  $category = Category::newInstance()->findByPrimaryKey( $id );
  $category_extra = del_category_extra($id);
  $default_colors = del_default_colors();

  if(isset($category_extra['s_color']) && $category_extra['s_color'] <> '') {
    $color_code = $category_extra['s_color'];                        
  } else {
    if(isset($default_colors[$id]) && $default_colors[$id] <> '') {
      $color_code = $default_colors[$id];
    } else {
      $color_code = $default_colors[999];
    }
  }

  return $color_code;
}


// GET PROPER CATEGORY IMAGE (ICON)
function del_get_cat_image($category_id) {
  if(file_exists(WebThemes::newInstance()->getCurrentThemePath() . 'images/small_cat/' . $category_id . '.png')) {
    return osc_current_web_theme_url() . 'images/small_cat/' . $category_id . '.png';
  } else {
    return osc_current_web_theme_url() . 'images/small_cat/default.png';
  }
}


// INCREASE PHONE CLICK VIEWS
function del_increase_clicks($itemId, $itemUserId = NULL) {
  if($itemId > 0) {
    if($itemUserId == '' || $itemUserId == 0 || ($itemUserId <> '' && $itemUserId > 0 && $itemUserId <> osc_logged_user_id())) {
      $db_prefix = DB_TABLE_PREFIX;
      $query = 'INSERT INTO ' . $db_prefix . 't_item_stats_delta (fk_i_item_id, dt_date, i_num_phone_clicks) VALUES (' . $itemId . ', "' . date('Y-m-d') . '", 1) ON DUPLICATE KEY UPDATE  i_num_phone_clicks = i_num_phone_clicks + 1';
      return ItemStats::newInstance()->dao->query($query);
    }
  }
}


// FIX ADMIN MENU LIST WITH THEME OPTIONS
function del_admin_menu_fix(){
  echo '<style>' . PHP_EOL;
  echo 'body.compact #delta_menu .ico-delta_menu {bottom:-6px!important;width:50px!important;height:50px!important;margin:0!important;background:#fff url(' . osc_base_url() . 'oc-content/themes/delta/images/favicons/favicon-32x32.png) no-repeat center center !important;}' . PHP_EOL;
  echo 'body.compact #delta_menu .ico-delta_menu:hover {background-color:rgba(255,255,255,0.95)!important;}' . PHP_EOL;
  echo 'body.compact #menu_delta_menu > h3 {bottom:0!important;}' . PHP_EOL;
  echo 'body.compact #menu_delta_menu > ul {border-top-left-radius:0px!important;margin-top:1px!important;}' . PHP_EOL;
  echo 'body.compact #menu_delta_menu.current:after {content:"";display:block;width:6px;height:6px;border-radius:10px;box-shadow:1px 1px 3px rgba(0,0,0,0.1);position:absolute;left:3px;bottom:3px;background:#03a9f4}' . PHP_EOL;
  echo 'body:not(.compact) #delta_menu .ico-delta_menu {background:transparent url(' . osc_base_url() . 'oc-content/themes/delta/images/favicons/favicon-32x32.png) no-repeat center center !important;}' . PHP_EOL;
  echo '</style>' . PHP_EOL;
}

osc_add_hook('admin_header', 'del_admin_menu_fix');



// BACKWARD COMPATIBILITY FUNCTIONS
if(!function_exists('osc_is_current_page')){
  function osc_is_current_page($location, $section) {
    if( osc_get_osclass_location() === $location && osc_get_osclass_section() === $section ) {
      return true;
    }

    return false;
  }
}


// CREATE URL FOR THEME AJAX REQUESTS
function del_ajax_url() {
  return osc_base_url(true) . '?ajaxRequest=1';
}


// GET AJAX FORM URL (FOR ITEMS)
function del_item_form_ajax_url($type, $item_id) {
  return del_ajax_url() . '&ajaxItemForm=1&type=' . osc_esc_html($type) . '&itemId=' . osc_esc_html($item_id);
}


// COUNT PHONE CLICKS ON ITEM
function del_phone_clicks( $item_id ) {
  if( $item_id <> '' ) {
    $db_prefix = DB_TABLE_PREFIX;

    $query = "SELECT sum(coalesce(i_num_phone_clicks, 0)) as phone_clicks FROM {$db_prefix}t_item_stats_delta s WHERE fk_i_item_id = " . $item_id . ";";
    $result = ItemStats::newInstance()->dao->query( $query );
    if( !$result ) { 
      $prepare = array();
      return '0';
    } else {
      $prepare = $result->row();

      if($prepare['phone_clicks'] <> '') {
        return $prepare['phone_clicks'];
      } else {
        return '0';
      }
    }
  }
}


// NO CAPTCHA RECAPTCHA CHECK
function del_show_recaptcha( $section = '' ){
  if(function_exists('anr_get_option')) {
    if(anr_get_option('site_key') <> '') {
      if($section == 'contact_listing') {
        if(anr_get_option('contact_listing') == '1') {
          osc_run_hook("anr_captcha_form_field");
        }
      } else if($section == 'login') {
        if(anr_get_option('login') == '1') {
          osc_run_hook("anr_captcha_form_field");
        }
      } else {
        // plugin sections are: login, registration, new, contact, contact_listing, send_friend, comment
        osc_run_hook("anr_captcha_form_field");
      }
    }
  } else {
    if(osc_recaptcha_public_key() <> '') {
      if(((osc_is_publish_page() || osc_is_edit_page()) && osc_recaptcha_items_enabled()) || (!osc_is_publish_page() && !osc_is_edit_page()) ) {
        osc_show_recaptcha($section);
      }
    }
  }
}


// SHOW BANNER
function del_banner($location) {
  $html = '';

  if(del_param('banners') == 1) {
    if( del_is_demo() ) {
      $class = ' is-demo';
    } else {
      $class = '';
    }
    
    if(del_param('banner_optimize_adsense') == 1) {
      $class .= ' opt-adsense';
    }

    if(del_param('banner_' . $location) == '') {
      $blank = ' blank';
    } else {
      $blank = '';
    }

    if( del_is_demo() && del_param('banner_' . $location) == '' ) {
      $title = ' title="' . __('You can define your own banner code from theme settings', 'delta') . '"';
    } else {
      $title = '';
    }

    $html .= '<div id="banner-theme" class="banner-theme banner-' . $location . ' not767' . $class . $blank . '"' . $title . '><div class="myad"><div class="text">';


    // BANNER ADS PLUGIN SUPPORT
    if (function_exists('ba_show_banner') && strpos(strtoupper(del_param('banner_' . $location)), 'BANNER-ADS-PLUGIN') !== false) {
      $xdata = strtoupper(trim(del_param('banner_' . $location)));

      if(strpos(del_param('banner_' . $location), 'BANNER-ADS-PLUGIN-HOOK')) {
        $hook = trim(str_replace(array(' ', '  ', '{', '{{', '{{{', '}', '}}', '}}}', 'BANNER-ADS-PLUGIN-HOOK', ':'), '', $xdata));

        if(trim($hook) <> '') {
          $html .= ba_hook($hook, false);
        }
      } else if(strpos(del_param('banner_' . $location), 'BANNER-ADS-PLUGIN-BANNER')) {
        $banner_id = trim(str_replace(array(' ', '  ', '{', '{{', '{{{', '}', '}}', '}}}', 'BANNER-ADS-PLUGIN-BANNER', ':'), '', $xdata));

        if(is_numeric($banner_id) && $banner_id > 0) {
          $html .= ba_show_banner($banner_id, false);
        }
      } else if(strpos(del_param('banner_' . $location), 'BANNER-ADS-PLUGIN-ADVERT')) {
        $advert_id = trim(str_replace(array(' ', '  ', '{', '{{', '{{{', '}', '}}', '}}}', 'BANNER-ADS-PLUGIN-ADVERT', ':'), '', $xdata));

        if(is_numeric($advert_id) && $advert_id > 0) {
          $html .= ba_show_advert($advert_id);
        }
      }
    } else {
      $html .= del_param('banner_' . $location);
    }


    if(del_is_demo() && del_param('banner_' . $location) == '') {
      $html .= '<div class="demo-text"><span>' . __('Banner space', 'delta') . '</span><strong>[' .  str_replace('_', ' ', $location) . ']</strong></div>';
    }

    $html .= '</div></div></div>';

    if(!del_is_demo() && trim(del_param('banner_' . $location)) == '') {
      return '';
    } else {
      return $html;
    }
  } else {
    return false;
  }
}


function del_banner_list() {
  $list = array(
    array('id' => 'banner_home_top', 'position' => __('Top of home page', 'delta')),
    array('id' => 'banner_home_middle', 'position' => __('Middle of home page', 'delta')),
    array('id' => 'banner_home_bottom', 'position' => __('Bottom of home page', 'delta')),
    array('id' => 'banner_search_sidebar', 'position' => __('Bottom of search sidebar', 'delta')),
    array('id' => 'banner_search_top', 'position' => __('Top of search page', 'delta')),
    array('id' => 'banner_search_bottom', 'position' => __('Bottom of search page', 'delta')),
    array('id' => 'banner_search_middle', 'position' => __('Between listings', 'delta')),
    array('id' => 'banner_item_top', 'position' => __('Top of item page', 'delta')),
    array('id' => 'banner_item_bottom', 'position' => __('Bottom of item page', 'delta')),
    array('id' => 'banner_item_sidebar', 'position' => __('Middle of item sidebar', 'delta')),
    array('id' => 'banner_item_sidebar_bottom', 'position' => __('Bottom of item sidebar', 'delta')),
    array('id' => 'banner_item_description', 'position' => __('Under item description', 'delta')),
    array('id' => 'banner_public_profile_sidebar_middle', 'position' => __('Public profile sidebar middle', 'delta')),
    array('id' => 'banner_public_profile_sidebar_bottom', 'position' => __('Public profile sidebar bottom', 'delta')),
    array('id' => 'banner_public_profile_top', 'position' => __('Public profile above items', 'delta')),
    array('id' => 'banner_public_profile_bottom', 'position' => __('Public profile under items', 'delta')),
    array('id' => 'banner_body_left', 'position' => __('All pages on left from body', 'delta')),
    array('id' => 'banner_body_right', 'position' => __('All pages on right from body', 'delta'))
  );

  return $list;
}


function del_extra_fields_hide() {
  $list = trim(del_param('post_extra_exclude'));
  $array = explode(',', $list);
  $array = array_map('trim', $array);
  $array = array_filter($array);

  if(!empty($array) && count($array) > 0) {
    return $array;
  } else {
    return array();
  }
}


// DISABLE ERROR404 ON SEARCH PAGE WHEN NO ITEMS FOUND
function del_disable_404() {
  if(osc_is_search_page() && osc_count_items() <= 0) {
    if(http_response_code() == 404) {
      http_response_code(200);
    }
  }
}

osc_add_hook('header', 'del_disable_404');


// THEME PARAMS UPDATE
if(!function_exists('del_param_update')) {
  function del_param_update($param_name, $update_param_name, $type = NULL, $plugin_var_name = null) {
    $val = '';
    if($type == 'check') {

      // Checkbox input
      if(Params::getParam($param_name) == 'on') {
        $val = 1;
      } else {
        if(Params::getParam($update_param_name) == 'done') {
          $val = 0;
        } else {
          $val = (osc_get_preference($param_name, $plugin_var_name) != '') ? osc_get_preference($param_name, $plugin_var_name) : '';
        }
      }

    } else if ($type == 'code') {

      if(Params::getParam($update_param_name) == 'done' && Params::existParam($param_name)) {
        $val = stripslashes(Params::getParam($param_name, false, false));
      } else {
        $val = (osc_get_preference($param_name, $plugin_var_name) != '') ? stripslashes(osc_get_preference($param_name, $plugin_var_name)) : '';
      }

    } else {

      // Other inputs (text, password, ...)
      if(Params::getParam($update_param_name) == 'done' && Params::existParam($param_name)) {
        $val = Params::getParam($param_name);
      } else {
        $val = (osc_get_preference($param_name, $plugin_var_name) != '') ? osc_get_preference($param_name, $plugin_var_name) : '';
      }
    }


    // If save button was pressed, update param
    if(Params::getParam($update_param_name) == 'done') {

      if(osc_get_preference($param_name, $plugin_var_name) == '') {
        if ($type == 'code') {
          osc_set_preference($param_name, stripslashes($val), $plugin_var_name, 'STRING');
        } else {
          osc_set_preference($param_name, $val, $plugin_var_name, 'STRING');  
        }
      } else {
        $dao_preference = new Preference();

        if ($type == 'code') {
          $dao_preference->update(array("s_value" => stripslashes($val)), array("s_section" => $plugin_var_name, "s_name" => $param_name));
        } else {
          $dao_preference->update(array("s_value" => $val), array("s_section" => $plugin_var_name, "s_name" => $param_name));
        }

        osc_reset_preferences();
        unset($dao_preference);
      }
    }

    return $val;
  }
}


// MULTI-LEVEL CATEGORIES SELECT
function del_cat_tree() {
  $array = array();
  $root = Category::newInstance()->findRootCategoriesEnabled();

  $i = 0;
  foreach($root as $c) {
    $array[$i] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name']);
    $array[$i]['sub'] = del_cat_sub($c['pk_i_id']);
    $i++;
  }

  return $array;
}


function del_cat_sub($id) {
  $array = array();
  $cats = Category::newInstance()->findSubcategories($id);

  if($cats && count($cats) > 0) {
    $i = 0;
    foreach($cats as $c) {
      $array[$i] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name']);
      $array[$i]['sub'] = del_cat_sub($c['pk_i_id']);
      $i++;
    }
  }
      
  return $array;
}


function del_cat_list($selected = array(), $categories = '', $level = 0) {
  if($categories == '') {
    $categories = del_cat_tree();
  }

  foreach($categories as $c) {
    echo '<option value="' . $c['pk_i_id'] . '" ' . (in_array($c['pk_i_id'], $selected) ? 'selected="selected"' : '') . '>' . str_repeat('-', $level) . ($level > 0 ? ' ' : '') . $c['s_name'] . '</option>';

    if(isset($c['sub']) && count($c['sub']) > 0) {
      del_cat_list($selected, $c['sub'], $level + 1);
    }
  }
}


if (!function_exists('array_column')) {
  function array_column(array $input, $columnKey, $indexKey = null) {
    $array = array();
    foreach ($input as $value) {
      if ( !array_key_exists($columnKey, $value)) {
        trigger_error("Key \"$columnKey\" does not exist in array");
        return false;
      }
      if (is_null($indexKey)) {
        $array[] = $value[$columnKey];
      }
      else {
        if ( !array_key_exists($indexKey, $value)) {
          trigger_error("Key \"$indexKey\" does not exist in array");
          return false;
        }
        if ( ! is_scalar($value[$indexKey])) {
          trigger_error("Key \"$indexKey\" does not contain scalar value");
          return false;
        }
        $array[$value[$indexKey]] = $value[$columnKey];
      }
    }
    return $array;
  }
}


if (!function_exists('osc_count_cities')) {
  function osc_count_cities($region = '%%%%') {
    if ( !View::newInstance()->_exists('cities') ) {
      View::newInstance()->_exportVariableToView('cities', Search::newInstance()->listCities($region, ">=", "city_name ASC" ) ) ;
    }

    return View::newInstance()->_count('cities') ;
  }
}


if(!function_exists('osc_static_page_indexable')) {
  function osc_static_page_indexable() {
    return 1;
  }
}


if(!function_exists('osc_item_send_friend_form_disabled')) {
  function osc_item_send_friend_form_disabled() {
    return false;
  }
}

?>