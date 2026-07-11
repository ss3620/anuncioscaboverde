<?php
/*
Plugin Name: Profile Picture
Plugin URI: http://osclasspoint.com
Description: Allows users to upload a profile picture
Version: 5.1.0
Author: Jesse modified by osclasspoint.com
Author URI: http://osclasspoint.com/
Short Name: Profile_Picture
*/

function profile_picture_install() {
    $conn = getConnection();
    $conn->autocommit(false);
    try {
        $path = osc_plugin_resource('profile_picture/struct.sql');
        $sql = file_get_contents($path);
        $conn->osc_dbImportSQL($sql);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}



function profile_picture_uninstall() {
    $conn = getConnection();
    $conn->autocommit(false);
    try {
	$conn->osc_dbExec('DROP TABLE %st_profile_picture', DB_TABLE_PREFIX);
	$conn->commit();
	} catch (Exception $e) {
	    $conn->rollback();
	    echo $e->getMessage();
	}
    $conn->autocommit(true);
}



function profile_picture_upload(){ 
  $width='';
  $height='';
    echo '<div id="show-update-picture-content">';
    echo '<div id="upload_avatar" class="fw-box">';
    echo '<div class="head">';
    echo '<h2>' . __('Update profile picture', 'profile_picture') . '</h2>';
    echo '<a href="#" class="def-but fw-close-button round3 selected"><i class="fa fa-times"></i> ' . __('Close', 'profile_picture') . '</a>';
    echo '</div>';

    echo '<div class="middle">';
    echo '<div class="ins">';

   // Configuration - Your Options ///////////////////////////////////////////////////////

    // Specify display width of picture (height will be automatically calculated proprotionally)
    $maxwidth = '350';

    $allowed_filetypes = array('.jpg','.gif','.bmp','.png'); // These will be the types of file that will pass the validation.
    $max_filesize = 684288; // Maximum filesize in BYTES (currently 0.65MB).
    $max_image_width = 320;
    $max_image_height = 320;
    $upload_path = osc_plugins_path().'profile_picture/images/';

    $button_text = __('Upload picture', 'profile_picture');

    ////// ***** No modifications below here should be needed ***** /////////////////////

    // First, check to see if user has existing profile picture...
	$user_id = osc_logged_user_id(); // the user id of the user profile we're at
	$conn = getConnection();
	$result=$conn->osc_dbFetchResult("SELECT user_id, pic_ext FROM %st_profile_picture WHERE user_id = '%d' ", DB_TABLE_PREFIX, $user_id);

	if($result>0 && file_exists($upload_path.'profile'.$user_id.$result['pic_ext'])) //if picture exists
	{

	    list($width, $height, $type, $attr)= getimagesize($upload_path.'profile'.$user_id.$result['pic_ext']); 

	    // Calculate display heigh/width based on max size specified
	    $ratio = $width/$height;
	    $height = $maxwidth/$ratio;

	    echo '<script>$("#div_show").live("click", function() { $(".HiddenDiv").slideDown(200); });</script>';
	    echo '<script>function deletePhoto(){document.forms["deleteForm"].submit();}</script>';

	    $modtime = filemtime($upload_path.'profile'.$user_id.$result['pic_ext']); //ensures browser cache is refreshed if newer version of picture exists
	    echo '<img src="'.osc_base_url() . 'oc-content/plugins/profile_picture/images/profile'.$user_id.$result['pic_ext'].'?'.$modtime.'" width="'.$maxwidth.'" height="'.$height.'">'; // display picture
	}
	else { // show default photo since they haven't uploaded one
	    echo '<img src="'.osc_base_url() . 'oc-content/plugins/profile_picture/no-user.png" width="'.$width.'" height="'.$height.'">';
	} 

    if( osc_is_web_user_logged_in()){
	if($result>0 && file_exists($upload_path.'profile'.$user_id.$result['pic_ext'])){
	    echo '<div class="links"><a class="first" href="#" id="div_show">' . __('Upload new picture', 'profile_picture') . '</a> - <a class="second" href="javascript:deletePhoto();">' . __('Delete picture', 'profile_picture') . '</a></div>';
	    echo '<div id="HiddenDiv" class="HiddenDiv" style="display:none;">'; // hides form if user already has a profile picture and displays a link to form instead
	}
	$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	echo '
	    <form name="newpic" method="post" enctype="multipart/form-data"  action="'.$url.'">
	    <input type="file" name="userfile" id="file"><br>
	    <input name="Submit" type="submit" id="uniform-blue" class="btn btn-primary" value="'.$button_text.'">
	    </form>
	    <form name="deleteForm" method="POST" action="'.$url.'"><input type="hidden" name="deletePhoto"></form>
	'; //echo
    	if($result>0 && file_exists($upload_path.'profile'.$user_id.$result['pic_ext'])) echo '</div>';
    } //if logged-in


    if(isset($_POST['Submit'])) // Upload photo
    {
	$filename = $_FILES['userfile']['name']; // Get the name of the file (including file extension).
	$ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); // Get the extension from the filename.
        $fail = false;

	// Check if the filetype is allowed, if not DIE and inform the user.
	if(!in_array($ext,$allowed_filetypes)) {
	    osc_add_flash_error_message(__('The file you attempted to upload is not allowed', 'profile_picture'));
            $fail = true;
        }

	// Now check the filesize, if it is too large then DIE and inform the user.
        clearstatcache();

        if(isset($_FILES['userfile']['tmp_name']) && $_FILES['userfile']['tmp_name'] <> '') {
          $imagedata = getimagesize($_FILES['userfile']['tmp_name']);

          if(filesize($_FILES['userfile']['tmp_name']) > $max_filesize) {
	    osc_add_flash_error_message(__('The file you attempted to upload is too large', 'profile_picture'));
            $fail = true;
          } else if ($imagedata[0] > $max_image_width || $imagedata[1] > $max_image_height ) {
	    osc_add_flash_error_message(__('The dimension of file you attempted to upload is too large. Allowed image size is', 'profile_picture') . ': ' . $max_image_width .'x'.$max_image_height . 'px. ' . __('Your image size is', 'profile_picture') . ': ' .  $imagedata[0] . 'x' . $imagedata[1] . 'px.');
            $fail = true;
          }
        } else {
	    osc_add_flash_error_message(__('No picture uploaded', 'profile_picture'));
            $fail = true;
        }
 
	// Check if we can upload to the specified path, if not DIE and inform the user.
	if(!is_writable($upload_path)) {
	    osc_add_flash_error_message(__('You cannot upload to the specified directory, please CHMOD it to 777', 'profile_picture'));
            $fail = true;
	}

	// Upload the file to your specified path.
        if(!$fail) {
          if(move_uploaded_file($_FILES['userfile']['tmp_name'],$upload_path . 'profile'.$user_id.$ext)){
              if($result==0){
            $conn->osc_dbExec("INSERT INTO %st_profile_picture (user_id, pic_ext) VALUES ('%d', '%s')", DB_TABLE_PREFIX, $user_id, $ext);
              }
              else {
            $conn->osc_dbExec("UPDATE %st_profile_picture SET pic_ext = '%s' WHERE user_id = '%d' ", DB_TABLE_PREFIX, $ext, $user_id);
              }

              echo '<script type="text/javascript">window.location = document.URL;</script>';
          }

          else {
              osc_add_flash_error_message(__('There was an error during the file upload.  Please try again', 'profile_picture')); // It failed :(.
          }
        }
     }



    if(isset($_POST['deletePhoto'])) // Delete the photo
    {
        $user_img = $conn->osc_dbFetchResult("SELECT * FROM %st_profile_picture WHERE user_id = '%d' ", DB_TABLE_PREFIX, $user_id);
 
        if(isset($user_img['user_id'])){
          unlink(osc_base_path() . 'oc-content/plugins/profile_picture/images/profile' . $user_img['user_id'] . $user_img['pic_ext']);
        }

	$conn->osc_dbExec("DELETE FROM %st_profile_picture WHERE user_id = '%d' ", DB_TABLE_PREFIX, $user_id);
	echo '<script type="text/javascript">window.location = document.URL;</script>';
    }

    echo '<div class="text">' . __('- Picture should represent you or your company', 'profile_picture') . '</div>';
    echo '<div class="text">' . __('- Upload picture with maximum width of 250px and maximum height of 200px', 'profile_picture') . '</div>';
    echo '<div class="text">' . __('- Bigger pictures will slow down loading of your listings', 'profile_picture') . '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

} // end profile_picture_upload()





function profile_picture_show( $maxwidth = NULL, $what = NULL, $maxheight = NULL, $order = NULL, $user_id = NULL ){

   // Configuration - Your Options ///////////////////////////////////////////////////////

    // Specify display width of picture (height will be automatically calculated proprotionally)
    if($maxwidth == '') { $maxwidth = '120'; }
    $height = 120;

    ////// ***** No modifications below here should be needed ***** /////////////////////

    // First, check to see if user has existing profile picture...

    if(!isset($user_id) || $user_id == '') {
      if($what == 'item') {
        $user_id = osc_item_user_id();
      } else if ($what == 'comment') {
        $user_id = osc_comment_user_id();
      } else {
        $user_id = osc_user_id();

        if($user_id == '') { 
          $user_id = osc_logged_user_id(); 
        }
      }
    }

    $conn = getConnection();
    $result=$conn->osc_dbFetchResult("SELECT user_id, pic_ext FROM %st_profile_picture WHERE user_id = '%d' ", DB_TABLE_PREFIX, $user_id);

    if($result>0) //if picture exists
    {
	$upload_path = osc_plugins_path().'profile_picture/images/';

        if(file_exists($upload_path.'profile'.$user_id.$result['pic_ext'])) { 
        list($width, $height, $type, $attr)= getimagesize($upload_path.'profile'.$user_id.$result['pic_ext']); 

	// Calculate display heigh/width based on max size specified
	$ratio = $width/$height;
	$height = $maxwidth/$ratio;

        if($maxheight <> '') { $height = $maxheight; $maxwidth = 'auto'; }
	$modtime = filemtime($upload_path.'profile'.$user_id.$result['pic_ext']); //ensures browser cache is refreshed if newer version of picture exists
	// This is the picture HTML code displayed on page
	echo '<img id="profile_picture_img" src="'.osc_base_url() . 'oc-content/plugins/profile_picture/images/profile'.$user_id.$result['pic_ext'].'?'.$modtime.'" width="'.$maxwidth.'" height="'.$height.'" alt="' . __('Seller\'s picture', 'profile_picture') . '">'; // display picture
        } else {
        if($maxheight <> '') { $height = $maxheight; $maxwidth = 'auto'; }
          if($what == 'comment' && $order > 0) {
	    echo '<img id="profile_picture_img" src="'.osc_current_web_theme_url('images/profile-u') . $order . '.png" width="'.$maxwidth.'" height="'.$height.'" alt="' . __('Seller\'s picture', 'profile_picture') . '">';
          } else {
	    echo '<img id="profile_picture_img" src="'.osc_base_url() . 'oc-content/plugins/profile_picture/no-user.png" width="'.$maxwidth.'" height="'.$height.'" alt="' . __('Seller\'s picture', 'profile_picture') . '">';
          }
        }
    }
    else{
        if($maxheight <> '') { $height = $maxheight; $maxwidth = 'auto'; }
        if($what == 'comment' && $order > 0) {
	  echo '<img id="profile_picture_img" src="'.osc_current_web_theme_url('images/profile-u') . $order . '.png" width="'.$maxwidth.'" height="'.$height.'" alt="' . __('Seller\'s picture', 'profile_picture') . '">';
        } else {
	  echo '<img id="profile_picture_img" src="'.osc_base_url() . 'oc-content/plugins/profile_picture/no-user.png" width="'.$maxwidth.'" height="'.$height.'" alt="' . __('Seller\'s picture', 'profile_picture') . '">';
        }
    }
} //end profile_picture_show()






    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'profile_picture_install') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'profile_picture_uninstall') ;


?>