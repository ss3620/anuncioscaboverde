<?php
  $id = Params::getParam('blogId') > 0 ? Params::getParam('blogId') : Params::getParam('pk_i_id');
  $locale = Params::getParam('blgLocale');
  $is_new = ($id > 0 ? 0 : 1);

  if($id > 0) {
    $blog = ModelBLG::newInstance()->getBlogDetail($id);
  }

  // Create menu
  if($id > 0) {
    $title = __('Update post', 'blog');
  } else {
    $title = __('Create new post', 'blog');
  }

  blg_menu($title);

  $categories = ModelBLG::newInstance()->getCategories();
  $authors = ModelBLG::newInstance()->getUsers();
  $extensions = array('jpg', 'jpeg', 'png', 'gif', 'svg', 'webp');


  if(Params::getParam('plugin_action') == 'done') {
    $data_blog = array(
      'pk_i_id' => Params::getParam('pk_i_id'),
      's_slug' => Params::getParam('s_slug'),
      'i_status' => Params::getParam('i_status'),
      'i_category' => Params::getParam('i_category'),
      'fk_i_user_id' => Params::getParam('fk_i_user_id'),
      'dt_pub_date' => Params::getParam('dt_pub_date'),
      'i_order' => Params::getParam('i_order')
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

    $id = Params::getParam('blogId');
    $blog = ModelBLG::newInstance()->getBlogDetail($id);


    // UPLOAD IMAGE
    $upload_status = false;
    if(isset($_FILES['image']) && $_FILES['image']['name'] <> ''){
      // $upload_dir = osc_plugins_path() . 'blog/img/blog/';
      $upload_dir = blg_file_path('', 'blog');

      if(@$blog['s_image'] <> '') {
        if(file_exists($upload_dir . $blog['s_image'])) {
          unlink($upload_dir . $blog['s_image']);
        }
      }


      $file_ext = $ext = strtolower(pathinfo($_FILES['image']['name'])['extension']);
      $file_name  = $id . '.' . $file_ext;
      $file_tmp   = $_FILES['image']['tmp_name'];
      $file_type  = $_FILES['image']['type'];   

      if(in_array($file_ext,$extensions) === false) {
        $errors = sprintf(__('extension not allowed, allowed image extensions are %s', 'blog'), implode(', ', $extensions));
      }

      if(empty($errors)==true){
        move_uploaded_file($file_tmp, $upload_dir.$file_name);
        $upload_status = true;
      } else {
        message_error(__('There was error when uploading image', 'blog') . ': ' . $errors);
      }
    }


    if($upload_status) {
      ModelBLG::newInstance()->updateBlogImage($id, $file_name);
    }


    if($is_new) {
      osc_run_hook('blg_article_posted', $blog);
    } else {
      osc_run_hook('blg_article_edited', $blog);
    }

    message_ok( __('Updates were successfully saved', 'blog') );
  }
?>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.9/tinymce.min.js"></script>

<script type="text/javascript">
  tinyMCE.init({
    selector: "textarea#s_description",
    mode: "textareas",
    width: "100%",
    height: "560px",
    language: "en",
    content_style: "body {font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,'Helvetica Neue',sans-serif;font-size:14px;}",
    contextmenu: "link image editimage table",
    plugins: "paste print preview importcss searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media code codesample table charmap emoticons hr pagebreak nonbreaking toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help quickbars",
    menubar: "file edit view insert format tools table help",
    toolbar1: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect formatselect",
    toolbar2: "outdent indent | numlist bullist checklist | forecolor backcolor removeformat | link image media blockquote | ltr rtl | pagebreak codesample charmap emoticons | print code fullscreen",
    image_caption: true,
    quickbars_selection_toolbar: "bold italic underline strikethrough | quicklink h2 h3 h4 | blockquote quickimage quicktable",
    toolbar_mode: "wrap",
    entity_encoding: "raw",
    relative_urls: false,
    remove_script_host: false,
    convert_urls: false,
    paste_data_images: true,
    images_upload_url: "<?php echo osc_base_url(); ?>oc-content/plugins/blog/tinyMceImageUploader.php",
    images_upload_base_path: "<?php echo blg_file_path('', 'tinymce'); ?>",
    images_upload_credentials: true,
    images_upload_handler: function(blobInfo, success, failure) {
      var xhr, formData, fileName;
      xhr = new XMLHttpRequest();
      xhr.withCredentials = false;
      xhr.open("POST", "<?php echo osc_base_url(); ?>oc-content/plugins/blog/tinyMceImageUploader.php");
      xhr.onload = function() {
        var json;

        if(xhr.status != 200) {
          failure("HTTP Error: " + xhr.status);
          return;
        }

        json = JSON.parse(xhr.responseText);

        if(!json || typeof json.location != "string") {
          failure("Invalid JSON: " + xhr.responseText);
          return;
        }

        success(json.location);
      };

      formData = new FormData();

      if(typeof(blobInfo.blob().name) !== undefined) {
        fileName = blobInfo.blob().name;
      } else {
        fileName = blobInfo.filename();
      }

      formData.append("file", blobInfo.blob(), fileName);
      xhr.send(formData);
    },
    mobile: {
      menubar: true,
      toolbar_mode: true
    }
  });
</script>

<div class="mb-body">

  <!-- NEW POST SECTION -->
  <div class="mb-box">
    <div class="mb-head">
      <i class="fa fa-plus-circle"></i> <?php echo $title; ?>
      <?php echo blg_locale_box('blog.php', 'blogId', $id); ?>
    </div>

    <div class="mb-inside mb-blog">
      <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>blog.php" />
        <input type="hidden" name="plugin_action" value="done" />
        <input type="hidden" name="blgLocale" value="<?php echo blg_get_locale(); ?>" />
        <input type="hidden" name="fk_c_locale_code" value="<?php echo blg_get_locale(); ?>" />
        <input type="hidden" name="pk_i_id" value="<?php echo $id; ?>" />

        <?php if(count($categories) > 0) { ?>
          <div class="mb-row w20">
            <label for="i_category"><?php _e('Category', 'blog'); ?></label>

            <select name="i_category" id="i_category">
              <option value="" <?php if(@$blog['i_category'] <= 0) { ?>selected="selected"<?php } ?>><?php _e('Uncategorized', 'blog'); ?></option>

              <?php foreach($categories as $c) { ?>
                <option value="<?php echo $c['pk_i_id']; ?>" <?php if(@$blog['i_category'] == $c['pk_i_id']) { ?>selected="selected"<?php } ?>><?php echo ($c['s_name'] <> '' ? $c['s_name'] : sprintf(__('Category #%s (%s)', 'blog'), $c['pk_i_id'], blg_get_locale())); ?></option>
              <?php } ?>
            </select>
          </div>
        <?php } ?>

        <div class="mb-row w20">
          <label for="i_status"><?php _e('Status', 'blog'); ?></label>

          <select name="i_status" id="i_status">
            <option value="0" <?php if(@$blog['i_status'] <= 0) { ?>selected="selected"<?php } ?>><?php _e('Private', 'blog'); ?></option>
            <option value="1" <?php if(@$blog['i_status'] == 1) { ?>selected="selected"<?php } ?>><?php _e('Public', 'blog'); ?></option>
            <option value="2" <?php if(@$blog['i_status'] == 2) { ?>selected="selected"<?php } ?>><?php _e('Premium', 'blog'); ?></option>
          </select>
        </div>


        <div class="mb-row w20">
          <label for="fk_i_user_id"><?php _e('Author', 'blog'); ?></label>

          <select name="fk_i_user_id" id="fk_i_user_id">
            <option value="" <?php if(@$blog['fk_i_user_id'] <= 0) { ?>selected="selected"<?php } ?>><?php _e('No author', 'blog'); ?></option>

            <?php if(count($authors) > 0) { ?>
              <?php foreach($authors as $a) { ?>
                <option value="<?php echo $a['pk_i_id']; ?>" <?php if(@$blog['fk_i_user_id'] == $a['pk_i_id']) { ?>selected="selected"<?php } ?>><?php echo $a['s_name']; ?></option>
              <?php } ?>
            <?php } ?>
          </select>
        </div>

        <div class="mb-row w10 float-right">
          <label for="i_order"><?php _e('Position', 'blog'); ?></label>
          <input type="text" name="i_order" value="<?php echo @$blog['i_order'] > 0 ? $blog['i_order'] : 0; ?>"/>
        </div>
        
        <div class="mb-row mb-row-del"></div>

        <div class="mb-blog-seo">
          <div class="mb-row">
            <label for="s_seo_title"><?php _e('Seo Title', 'blog'); ?></label>
            <input type="text" id="s_seo_title" name="s_seo_title" size="60" value="<?php echo @$blog['s_seo_title']; ?>" />

            <div class="mb-explain"><?php _e('Title that should show search engine. Max 60 char. length.', 'blog'); ?></div>
          </div>

          <div class="mb-row">
            <label for="s_seo_description"><?php _e('Seo Description', 'blog'); ?></label>
            <textarea id="s_seo_description" name="s_seo_description"><?php echo @$blog['s_seo_description']; ?></textarea>

            <div class="mb-explain"><?php _e('Description that should show search engine. Max 300 char. length.', 'blog'); ?></div>
          </div> 
        </div>

        <div class="mb-row image">
          <label for="image"><span><?php _e('Image', 'blog'); ?></span></label> 
 
          <?php $img = blg_img($id, '', 1); ?>
          <?php if($img) { ?>
            <a class="mb-img-preview" href="<?php echo $img; ?>" target="_blank"><img class="mb-blog-img" src="<?php echo $img; ?>" /></a>
          <?php } ?>

          <div class="mb-file">
            <label class="file-label">
              <span class="wrap"><i class="fa fa-paperclip"></i> <span><?php echo (@$blog['s_image'] == '' ? __('Upload image', 'blog') : __('Replace image', 'blog')); ?></span></span>
              <input type="file" id="image" name="image" />
            </label>

            <div class="file-text"><?php _e('Allowed extensions', 'blog'); ?>: <?php echo implode(', ', $extensions); ?></div>
          </div>
        </div>

        <div class="mb-row">
          <label for="dt_pub_date"><?php _e('Publish Date', 'blog'); ?></label>
          <input type="date" id="dt_pub_date" name="dt_pub_date" size="30" value="<?php echo @$blog['dt_pub_date'] <> '' ? date("Y-m-d", strtotime(@$blog['dt_pub_date'])): date("Y-m-d"); ?>" />
        </div>

        <div class="mb-row">
          <label for="s_slug"><?php _e('Slug', 'blog'); ?></label>
          <input type="text" id="s_slug" name="s_slug" size="60" value="<?php echo @$blog['s_slug']; ?>" <?php if(@$blog['s_slug'] == '') { ?>class="is_blank"<?php } ?> />

          <div class="mb-explain"><?php _e('Slug is used to construct URL of post.', 'blog'); ?></div>
        </div>

        <div class="mb-row">
          <label for="s_title"><?php _e('Title', 'blog'); ?></label>
          <input type="text" id="s_title" name="s_title" size="80" value="<?php echo @$blog['s_title']; ?>" required />
        </div>

        <div class="mb-row">
          <label for="s_subtitle"><?php _e('Sub-title', 'blog'); ?></label>
          <textarea id="s_subtitle" name="s_subtitle"><?php echo @$blog['s_subtitle']; ?></textarea>

          <div class="mb-explain"><?php _e('Short summary of post, maximum 300 char. length.', 'blog'); ?></div>
        </div>

        <div class="mb-row">
          <label for="s_description"><?php _e('Description', 'blog'); ?></label>
          <textarea id="s_description" name="s_description"><?php echo htmlentities(isset($blog['s_description']) ? $blog['s_description'] : ''); ?></textarea>
        </div>


        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <?php if(!blg_is_demo()) { ?><button type="submit" class="mb-button"><?php _e('Save', 'blog');?></button><?php } ?>
        </div>
      </form>
    </div>
  </div>


</div>



<?php echo blg_footer(); ?>