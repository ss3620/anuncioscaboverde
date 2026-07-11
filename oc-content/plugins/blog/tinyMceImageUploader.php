<?php
define('ABS_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
require_once ABS_PATH . 'oc-load.php';

$accepted_origins = array("http://localhost/", "http://192.168.1.1/", "<?php echo osc_base_url(); ?>");

// $img_uploads_path = "img/tinymce/";
$img_uploads_path = blg_file_path('', 'tinymce');

reset($_FILES);
$temp = current($_FILES);
if (is_uploaded_file($temp['tmp_name'])){

  /*
  if (isset($_SERVER['HTTP_ORIGIN'])) {
    // same-origin requests won't set an origin. If the origin is set, it must be valid.
    if (in_array($_SERVER['HTTP_ORIGIN'] . '/', $accepted_origins)) {
      header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    } else {
      header("HTTP/1.1 403 Origin Denied");
      return;
    }
  }
  */


  /*
    If your script needs to receive cookies, set images_upload_credentials : true in
    the configuration and enable the following two headers.
  */
  // header('Access-Control-Allow-Credentials: true');
  // header('P3P: CP="There is no P3P policy."');

  // Sanitize input
  if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
      header("HTTP/1.1 400 Invalid file name.");
      return;
  }

  // Verify extension
  if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif","jpg","png","jpeg","webp"))) {
      header("HTTP/1.1 400 Invalid extension.");
      return;
  }

  // Accept upload if there was no origin, or if it is an accepted origin
  $file_name = date('YmdHis') . '_' . $temp['name'];
  move_uploaded_file($temp['tmp_name'], $img_uploads_path . $file_name);

  // Respond to the successful upload with JSON.
  // Use a location key to specify the path to the saved image resource.
  // { location : '/your/uploaded/image/file'}
  //echo json_encode(array('location' => osc_base_url() . 'oc-content/plugins/blog/' . $filetowrite));
  echo json_encode(array('location' => blg_file_url($file_name, 'tinymce')));
  exit;
  
} else {
  // Notify editor that the upload failed
  header("HTTP/1.1 500 Server Error");
  exit;
}

exit;
