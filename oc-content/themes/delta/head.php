<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title><?php echo meta_title(); ?></title>
<meta name="title" content="<?php echo osc_esc_html(meta_title()); ?>" />
<?php if(meta_description() != '') { ?><meta name="description" content="<?php echo osc_esc_html(meta_description()); ?>" /><?php } ?>
<?php if(osc_get_canonical() != '') { ?><link rel="canonical" href="<?php echo osc_get_canonical(); ?>"/><?php } ?>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="Mon, 01 Jul 1970 00:00:00 GMT" />
<?php if(!osc_is_search_page() && !osc_is_static_page()) { ?><meta name="robots" content="index, follow" /><?php } ?>
<?php if(!osc_is_search_page() && !osc_is_static_page()) { ?><meta name="googlebot" content="index, follow" /><?php } ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<?php 
  if(!function_exists('pwa_call_after_install')) {
    osc_current_web_theme_path('head-favicon.php');
  }
  
  osc_current_web_theme_path('head-color.php');

  $current_locale = osc_get_current_user_locale();
  $dimNormal = explode('x', osc_get_preference('dimNormal', 'osclass')); 
  
  if (!defined('JQUERY_VERSION') || JQUERY_VERSION == '1') {
    $jquery_version = '1';
  } else {
    $jquery_version = JQUERY_VERSION;
  }
?>
<script type="text/javascript">
  var delCurrentLocale = '<?php echo osc_esc_js($current_locale['s_name'] ?? 'English'); ?>';
  var fileDefaultText = '<?php echo osc_esc_js(__('No file selected', 'delta')); ?>';
  var fileBtnText     = '<?php echo osc_esc_js(__('Choose File', 'delta')); ?>';
  var baseDir = "<?php echo osc_base_url(); ?>";
  var baseSearchUrl = '<?php echo osc_search_url(array('page' => 'search')); ?>';
  var baseAjaxUrl = '<?php echo del_ajax_url(); ?>';
  var baseAdminDir = '<?php echo osc_admin_base_url(true); ?>';
  var currentLocation = '<?php echo osc_get_osclass_location(); ?>';
  var currentSection = '<?php echo osc_get_osclass_section(); ?>';
  var adminLogged = '<?php echo osc_is_admin_user_logged_in() ? 1 : 0; ?>';
  var delLazy = '<?php echo (del_is_lazy() ? 1 : 0); ?>';
  var delMasonry = '<?php echo osc_get_preference('force_aspect_image', 'osclass') == 1 ? 1 : 0; ?>';
  var imgPreviewRatio= <?php echo round($dimNormal[0]/$dimNormal[1], 3); ?>;
  var searchRewrite = '/<?php echo osc_get_preference('rewrite_search_url', 'osclass'); ?>';
  var ajaxSearch = '<?php echo (del_param('search_ajax') == 1 ? '1' : '0'); ?>';
  var ajaxForms = '<?php echo (del_param('forms_ajax') == 1 ? '1' : '0'); ?>';
  var locationPick = '<?php echo (del_param('location_pick') == 1 ? '0' : '0'); ?>';
  var delTitleNc = '<?php echo osc_esc_js(__('Parent category cannot be selected', 'delta')); ?>';
  var jqueryVersion = '<?php echo $jquery_version; ?>';
  var isRtl = <?php echo (del_is_rtl() ? 'true' : 'false'); ?>;
</script>
<?php

osc_enqueue_style('style', osc_current_web_theme_url('css/style.css?v=' . date('YmdHis')));
osc_enqueue_style('responsive', osc_current_web_theme_url('css/responsive.css?v=' . date('YmdHis')));
?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<?php
osc_enqueue_style('font-awesome5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css');

if ($jquery_version == '1') {
  osc_enqueue_style('jquery-ui', osc_current_web_theme_url('css/jquery-ui.min.css'));
} else {
  osc_enqueue_style('jquery-ui', osc_assets_url('js/jquery3/jquery-ui/jquery-ui.min.css'));
}

if(del_is_rtl()) {
  osc_enqueue_style('rtl', osc_current_web_theme_url('css/rtl.css') . '?v=' . date('YmdHis'));
}

if(osc_is_ad_page() || (osc_get_osclass_location() == 'item' && osc_get_osclass_section() == 'send_friend')) {
  osc_enqueue_style('swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.5.8/swiper-bundle.min.css');
  osc_enqueue_style('lightgallery', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.10.0/css/lightgallery.min.css');
}

if(del_ajax_image_upload() && (osc_is_publish_page() || osc_is_edit_page())) {
  osc_enqueue_style('fine-uploader-css', osc_assets_url('js/fineuploader/fineuploader.css'));
}
osc_register_script('global', osc_current_web_theme_js_url('global.js?v=' . date('YmdHis')), array('jquery'));

if ($jquery_version == '1') {
  osc_register_script('validate', osc_current_web_theme_js_url('jquery.validate.min.js'), array('jquery'));
} else {
  osc_register_script('validate', osc_assets_url('js/jquery.validate.min.js'), array('jquery'));
 // osc_register_script('jquery-validate', osc_assets_url('js/jquery.validate.min.js'), array('jquery'));

}

osc_register_script('rotate', osc_current_web_theme_js_url('jquery.rotate.js'), array('jquery'));
osc_register_script('date', osc_base_url() . 'oc-includes/osclass/assets/js/date.js');
osc_register_script('swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.5.8/swiper-bundle.min.js');
osc_register_script('lazyload', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js');
osc_register_script('google-maps', 'https://maps.google.com/maps/api/js?key='.osc_get_preference('maps_key', 'google_maps'));
osc_register_script('images-loaded', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.min.js');
osc_register_script('masonry', 'https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js');
osc_register_script('lightbox', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.10.0/js/lightgallery-all.min.js');
osc_register_script('mousewheel', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js');

osc_enqueue_script('jquery');

if(del_param('lazy_load') == 1) {
  osc_enqueue_script('lazyload');
}

if(!osc_is_search_page() && !osc_is_home_page()) {
  osc_enqueue_script('validate');
}

if(osc_is_publish_page() || osc_is_edit_page() || osc_is_search_page()) {
  osc_enqueue_script('date');
}


if(osc_is_ad_page() || (osc_get_osclass_location() == 'item' && osc_get_osclass_section() == 'send_friend')) {
  osc_enqueue_script('swiper');
  osc_enqueue_script('lightbox');
}

if( osc_get_preference('force_aspect_image', 'osclass') == 1 ) {
  osc_enqueue_script('masonry');
  osc_enqueue_script('images-loaded');
}

if(!osc_is_search_page() && !osc_is_home_page() && !osc_is_ad_page()) {
  osc_enqueue_script('tabber');
}

if(del_ajax_image_upload() && (osc_is_publish_page() || osc_is_edit_page())) {
  osc_enqueue_script('jquery-fineuploader');
  osc_enqueue_script('rotate');
}

osc_enqueue_script('jquery-ui');
osc_enqueue_script('global');

osc_run_hook('header'); 
?>