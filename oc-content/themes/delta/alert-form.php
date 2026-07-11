<script type="text/javascript">
$(document).ready(function(){
  <?php if(!osc_is_web_user_logged_in()) { ?>$('#alert_email').val('');<?php } ?>
  $('#alert_email').attr('placeholder', '<?php echo osc_esc_js(__('Email', 'delta')) ; ?>');

  $('body').on('click', 'button.alert-notify, button.alert-notify2', function(e){
    e.preventDefault();

    if($('#alert_email').val() == '' && $("#alert_userId").val() <= 0) {
      delAddFlash('<?php echo osc_esc_js(__('Please enter your email address!', 'delta')); ?>', 'error');
      return false;
    }

    $.post(
      '<?php echo osc_base_url(true); ?>', 
      {
        email: $("#alert_email").val(), 
        userid: $("#alert_userId").val(), 
        alert: $("#alert").val(), 
        page:"ajax", 
        action:"alerts"
      }, 
      function(data){
        if(data==1) {
          delAddFlash('<?php echo osc_esc_js(__('You have successfully subscribed to alert!', 'delta')); ?>', 'ok');

        } else if(data==-1) { 
          delAddFlash('<?php echo osc_esc_js(__('There was error during subscription process - incorrect email address format!', 'delta')); ?>', 'error');

        } else if(data==0) { 
          delAddFlash('<?php echo osc_esc_js(__('You have already subscribed to this search!', 'delta')); ?>', 'info');

        }
    });

    $('.alert-box').hide(0);
    $('a.alert-notify').show(0);
    $('a.alert-notify > span > span').text('<?php echo osc_esc_js(__('Submitted!', 'delta')); ?>');

    return false;
  });
});
</script>

<div id="n-block" class="block <?php if(osc_is_web_user_logged_in()) { ?>is-logged<?php } else { ?>not-logged<?php } ?>">
  <div class="n-wrap">
    <form action="<?php echo osc_base_url(true); ?>" method="post" name="sub_alert" id="sub_alert" class="nocsrf">
      <?php AlertForm::page_hidden(); ?>
      <?php AlertForm::alert_hidden(); ?>
      <?php AlertForm::user_id_hidden(); ?>


      <?php if(osc_is_web_user_logged_in()) { ?>
        <?php AlertForm::email_hidden(); ?>

        <button class="alert-notify subscribe btn mbBg2" href="#">
          <span class="mbCl2">
            <svg version="1.1" width="18px" height="18px" fill="<?php echo del_param('color2'); ?>" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 148.961 148.961" style="enable-background:new 0 0 148.961 148.961;" xml:space="preserve"> <g> <path d="M146.764,17.379c-2.93-2.93-7.679-2.929-10.606,0.001L68.852,84.697L37.847,53.691c-2.93-2.929-7.679-2.93-10.606-0.001 c-2.93,2.929-2.93,7.678-0.001,10.606l36.309,36.311c1.407,1.407,3.314,2.197,5.304,2.197c1.989,0,3.897-0.79,5.304-2.197 l72.609-72.622C149.693,25.057,149.693,20.308,146.764,17.379z"/> <path d="M130.57,65.445c-4.142,0-7.5,3.357-7.5,7.5v55.57H15V20.445h85.57c4.143,0,7.5-3.357,7.5-7.5c0-4.142-3.357-7.5-7.5-7.5 H7.5c-4.142,0-7.5,3.357-7.5,7.5v123.07c0,4.143,3.358,7.5,7.5,7.5h123.07c4.143,0,7.5-3.357,7.5-7.5v-63.07 C138.07,68.803,134.713,65.445,130.57,65.445z"/> </g> </svg>
            <span><?php _e('Save search', 'delta'); ?></span>
          </span>
        </button>

      <?php } else { ?>
        <a class="alert-notify subscribe btn mbBg2" href="#">
          <span class="mbCl2">
            <svg version="1.1" width="18px" height="18px" fill="<?php echo del_param('color2'); ?>" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 148.961 148.961" style="enable-background:new 0 0 148.961 148.961;" xml:space="preserve"> <g> <path d="M146.764,17.379c-2.93-2.93-7.679-2.929-10.606,0.001L68.852,84.697L37.847,53.691c-2.93-2.929-7.679-2.93-10.606-0.001 c-2.93,2.929-2.93,7.678-0.001,10.606l36.309,36.311c1.407,1.407,3.314,2.197,5.304,2.197c1.989,0,3.897-0.79,5.304-2.197 l72.609-72.622C149.693,25.057,149.693,20.308,146.764,17.379z"/> <path d="M130.57,65.445c-4.142,0-7.5,3.357-7.5,7.5v55.57H15V20.445h85.57c4.143,0,7.5-3.357,7.5-7.5c0-4.142-3.357-7.5-7.5-7.5 H7.5c-4.142,0-7.5,3.357-7.5,7.5v123.07c0,4.143,3.358,7.5,7.5,7.5h123.07c4.143,0,7.5-3.357,7.5-7.5v-63.07 C138.07,68.803,134.713,65.445,130.57,65.445z"/> </g> </svg>
            <span><?php _e('Save search', 'delta'); ?></span>
          </span>
        </a>
        
        <div class="alert-box">
          <?php AlertForm::email_text(); ?>
          <button type="button" class="btn btn-primary mbBg2 alert-notify2"><?php _e('Ok', 'delta'); ?></button>
        </div>
      <?php } ?>

    </form>
  </div>
</div>