<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');?>
<div id="settings_form" >
  <div style="">
    <div style="float: left; width: 100%;">
      <fieldset>
        <h2><?php _e('QR Code Help', 'qrcode'); ?></h2>
        <h3><?php _e('What does QR Code plugin do?', 'qrcode');?></h3>
        <p><?php _e('It display a QR code with the URL of the item. Useful to quick share items with other people, print it and place anywhere.', 'qrcode');?></p>
        <br/>
        <h3><?php _e('IMPORTANT', 'qrcode');?></h3>
        <p><?php _e('In order to work, you will need to place the following lines whereever you want to display the QR. The QR code will be placed inside a <img/> tag at that place.', 'qrcode');?></p>
        <pre>&lt;?php show_qrcode(); ?&gt;</pre>
        <br />
        <p><?php _e('It is also important that you have the GD extension enabled in your server.', 'qrcode');?></p>
        <br />
      </fieldset>
    </div>
    <div style="clear: both;"></div>										
  </div>
</div>
