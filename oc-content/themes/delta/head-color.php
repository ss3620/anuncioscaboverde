<?php
  $color = '.mbCl,footer .cl .lnk:hover,a.toggle-pass:hover,#search-sort .user-type a.active,#search-sort .user-type a:hover,header .right a:hover, header .right a.publish:hover, body a, body a:hover, .banner-theme#banner-theme.is-demo .myad';
  $color2 = '.mbCl2';
  $color3 = '.mbCl3,header .right a.actv,header .right a.actv svg, .filter .wrap .box h2.split';
  $background = '.mbBg,a.mo-button,.swiper-pagination-bullets-dynamic .swiper-pagination-bullet-active-main,.pace .pace-progress,body #show-loan i, .im-body #uniform-undefined.frm-category,.frm-answer .frm-area .frm-buttons button,.paginate ul li span,#listing .data .connect-after a:hover,.paginate ul li a:hover,.blg-btn.blg-btn-primary,.bpr-prof .bpr-btn, .post-edit .price-wrap .selection a.active';
  $background2 = '.mbBg2, .im-button-green, .main-data > .thumbs li.active, .main-data > .thumbs li:hover';
  $background3 = '.mbBg3,#mmenu a .circle,.user-top-menu > .umenu li.active a,#photos .qq-upload-button, .tabbernav li.tabberactive a,.frm-title-right a.frm-new-topic,.im-user-account-count, .simple-prod .switch-bars:not([data-count="1"]) .bar:hover:after';
  $background_after = '.mbBgAf:after';
  $background_active = '.mbBgActive.active';
  $background2_active = '.mbBg2Active.active';
  $background3_active = '.mbBg3Active.active';
  $background_color = '';
  $border_color = '.mbBr,header .right a.publish:hover, #search-sort .list-grid a.active > div > span, .banner-theme#banner-theme.is-demo .myad';
  $border2_color = '.mbBr2, header .right a.publish:hover > span, #home-pub a.publish:hover > span, #search-pub .subscribe:hover > span';
  $border3_color = '.mbBr3,.user-top-menu > .umenu li.active a';
  $border_background = '#atr-search .atr-input-box input[type="checkbox"]:checked + label:before, #atr-search .atr-input-box input[type="radio"]:checked + label:before,#atr-form .atr-input-box input[type="checkbox"]:checked + label:before, #atr-form .atr-input-box input[type="radio"]:checked + label:before,.bpr-box-check input[type="checkbox"]:checked + label:before, #gdpr-check.styled .input-box-check input[type="checkbox"]:checked + label:before, .pol-input-box input[type="checkbox"]:checked + label:before, .pol-values:not(.pol-nm-star) .pol-input-box input[type="radio"]:checked + label:before';
  $border_bottom = '#search-sort .user-type a.active, #search-sort .user-type a:hover';
  $border2_top = '.mbBr2Top';
  $border3_top = '.mbBr3Top, body #fi_user_new_list';
?>

<style>
  <?php echo $color; ?> {color:<?php echo del_param('color'); ?>;}
  <?php echo $color2; ?> {color:<?php echo del_param('color2'); ?>;}
  <?php echo $color3; ?> {color:<?php echo del_param('color3'); ?>;}
  <?php echo $background; ?> {background:<?php echo del_param('color'); ?>!important;color:#fff!important;}
  <?php echo $background2; ?> {background:<?php echo del_param('color2'); ?>!important;color:#fff!important;}
  <?php echo $background3; ?> {background:<?php echo del_param('color3'); ?>!important;color:#fff!important;}
  <?php echo $background_after; ?> {background:<?php echo del_param('color'); ?>!important;}
  <?php echo $background_active; ?> {background:<?php echo del_param('color'); ?>!important;}
  <?php echo $background2_active; ?> {background:<?php echo del_param('color2'); ?>!important;}
  <?php echo $background3_active; ?> {background:<?php echo del_param('color3'); ?>!important;}
  <?php echo $background_color; ?> {background-color:<?php echo del_param('color'); ?>!important;}
  <?php echo $border_color; ?> {border-color:<?php echo del_param('color'); ?>!important;}
  <?php echo $border2_color; ?> {border-color:<?php echo del_param('color2'); ?>!important;}
  <?php echo $border3_color; ?> {border-color:<?php echo del_param('color3'); ?>!important;}
  <?php echo $border_background; ?> {border-color:<?php echo del_param('color'); ?>!important;background-color:<?php echo del_param('color'); ?>!important;}
  <?php echo $border_bottom; ?> {border-bottom-color:<?php echo del_param('color'); ?>!important;}
  <?php echo $border2_top; ?> {border-top-color:<?php echo del_param('color2'); ?>!important;}
  <?php echo $border3_top; ?> {border-top-color:<?php echo del_param('color3'); ?>!important;}
</style>

<script>
  var mbCl = '<?php echo $color; ?>';
  var mbCl2 = '<?php echo $color2; ?>';
  var mbCl3 = '<?php echo $color3; ?>';
  var mbBg = '<?php echo $background; ?>';
  var mbBg2 = '<?php echo $background2; ?>';
  var mbBg3 = '<?php echo $background3; ?>';
  var mbBgAf= '<?php echo $background_after; ?>';
  var mbBgAc= '<?php echo $background_active; ?>';
  var mbBg2Ac= '<?php echo $background2_active; ?>';
  var mbBg3Ac= '<?php echo $background3_active; ?>';
  var mbBr= '<?php echo $border_color; ?>';
  var mbBr2= '<?php echo @$border_color2; ?>';
  var mbBr3= '<?php echo @$border_color3; ?>';
  var mbBrBg= '<?php echo $border_background; ?>';
  var mbBrBt= '<?php echo $border_bottom; ?>';
  var mbBr2Top= '<?php echo $border2_top; ?>';
  var mbBr3Top= '<?php echo $border3_top; ?>';
</script>
