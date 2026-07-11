<?php
  $page = Params::getParam('blgPage');


  if($page == 'search') {
    // SEARCH PARAMETERS PROCESS

    $keyword = Params::getParam('blgSearch');
    $keyword = urlencode($keyword);

    if($keyword == '') {
      header('Location:' . osc_route_url('blg-home'));
      exit;
    } else {
      header('Location:' . osc_route_url('blg-search', array('keyword' => $keyword)));
      exit;
    }

    exit;

  } else if($page == 'comment') {
    // NEW COMMENT CREATED
 
    $blog_id = Params::getParam('fk_i_blog_id');
    $blog = ModelBLG::newInstance()->getBlog($blog_id);
    $name = osc_logged_user_name();
    $text = nl2br(htmlspecialchars(Params::getParam('s_comment', false, false)));
    $enabled = (blg_param('comment_validate') == 1 ? 0 : 1);


    if($blog_id <= 0) {
      osc_add_flash_error_message(__('Error - it was not possible to identify article ID', 'blog'));
      header('Location:' . osc_route_url('blg-home'));
      exit;
    }

    if(blg_param('comment_enabled') <> 1) {
      osc_add_flash_error_message(__('Error - comments are not enabled', 'blog'));
      header('Location:' . osc_route_url('blg-post', array('blogSlug' => blg_get_slug($blog), 'blogId' => $blog_id)));
      exit;
    }

    if(!osc_is_web_user_logged_in()) {
      osc_add_flash_error_message(__('In order to add new comment you must be logged in', 'blog'));
      header('Location:' . osc_route_url('blg-post', array('blogSlug' => blg_get_slug($blog), 'blogId' => $blog_id)));
      exit;
    }

    $values = array(    
      'fk_i_blog_id' => $blog_id,
      'fk_i_os_user_id' => osc_logged_user_id(),
      's_comment' => $text,
      'b_enabled' => $enabled,
      'dt_pub_date' => date("Y-m-d H:i:s")
    );

    ModelBLG::newInstance()->insertComment($values);

    if($enabled == 0) {
      osc_add_flash_ok_message(__('Comment successfully added, it will be visible once our team validate it.', 'blog'));
    } else {
      osc_add_flash_ok_message(__('Comment successfully added and visible on article.', 'blog'));
    }

    header('Location:' . osc_route_url('blg-post', array('blogSlug' => blg_get_slug($blog), 'blogId' => $blog_id)));
    exit;


  } else if($page == 'blog') {
    // NEW BLOG ARTICLE CREATED 
    $validate = (blg_param('blog_validate') == 1 ? 0 : Params::getParam('i_status'));
    $is_new = (Params::getParam('pk_i_id') > 0 ? 0 : 1);

    $data_blog = array(
      'pk_i_id' => Params::getParam('pk_i_id'),
      's_slug' => Params::getParam('s_slug'),
      'i_status' => ($is_new == 1 ? $validate : Params::getParam('i_status')),
      'i_category' => Params::getParam('i_category'),
      'fk_i_user_id' => Params::getParam('fk_i_user_id'),
      'dt_pub_date' => date("Y-m-d H:i:s")
    );


    $data_locale = array(
      'fk_i_blog_id' => Params::getParam('pk_i_id'),
      'fk_c_locale_code' => Params::getParam('fk_c_locale_code'),
      's_title' => Params::getParam('s_title'),
      's_subtitle' => Params::getParam('s_subtitle'),
      's_description' => Params::getParam('s_description', false, false),
      's_seo_title' => Params::getParam('s_seo_title'),
      's_seo_description' => Params::getParam('s_seo_description')
    );

    ModelBLG::newInstance()->updateBlog($data_blog, $data_locale);

    $blog_id = Params::getParam('blogId');


    // UPLOAD IMAGE
    $upload_status = false;echo 'afdsf';
    if(isset($_FILES['image']) && $_FILES['image']['name'] <> ''){
      // $upload_dir = osc_plugins_path() . 'blog/img/blog/';
      $upload_dir = blg_file_path('', 'blog');

      if(@$blog['s_image'] <> '') {
        if(file_exists($upload_dir . $blog['s_image'])) {
          unlink($upload_dir . $blog['s_image']);
        }
      }

      $file_ext = $ext = pathinfo($_FILES['image']['name'])['extension'];
      $file_name  = $blog_id . '.' . $file_ext;
      $file_tmp   = $_FILES['image']['tmp_name'];
      $file_type  = $_FILES['image']['type'];   
      $extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

      if(in_array($file_ext,$extensions) === false) {
        $errors = __('extension not allowed, only allowed extension are jpg, jpeg, png, webp or gif!', 'blog');
      }

      if(empty($errors)==true){
        move_uploaded_file($file_tmp, $upload_dir.$file_name);
        $upload_status = true;
      } else {
        osc_add_flash_error_message(__('There was error when uploading image', 'blog') . ': ' . $errors);
      }
    }

    if($upload_status) {
      ModelBLG::newInstance()->updateBlogImage($blog_id, $file_name);
    }

    $blog = ModelBLG::newInstance()->getBlog($blog_id);

    if($is_new == 1) {
      $message = __('Article successfully published.', 'blog');
    } else { 
      $message = __('Article successfully udpated.', 'blog');
    }

    if(blg_param('blog_validate') == 1) {
      $message .= ' ' . __('It will be visible once it is validated by admin.', 'blog');
    }

    osc_add_flash_ok_message($message);
    
    if($is_new) {
      osc_run_hook('blg_article_posted', $blog);
    } else {
      osc_run_hook('blg_article_edited', $blog);
    }    

    header('Location:' . osc_route_url('blg-post', array('blogSlug' => blg_get_slug($blog), 'blogId' => $blog_id)));
    exit;
  }
?>