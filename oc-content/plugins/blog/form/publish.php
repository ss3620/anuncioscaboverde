<?php
  $user_id = osc_logged_user_id();
  $blog_id = Params::getParam('blogId');
  $blog = ModelBLG::newInstance()->getBlogDetail($blog_id);

  $author = ModelBLG::newInstance()->getUserByOsclassId($user_id);

  if(!$author || !isset($author['pk_i_id']) || $author['pk_i_id'] <= 0) {
    osc_add_flash_error_message(__('Error - you are not allowed to create new article', 'blog'));
    header('Location:' . osc_route_url('blg-home'));
    exit;
  }

  $author_id = $author['pk_i_id'];
  $categories = ModelBLG::newInstance()->getAuthorCategories($author_id);
?>

<script type="text/javascript" src="<?php echo osc_assets_url('js/tinymce/tinymce.min.js'); ?>"></script>

<script type="text/javascript">
  tinyMCE.init({
    selector: "textarea#s_description",
    mode : "textareas",
    width: "100%",
    height: "440px",
    language: "en",
    //content_css : ["//fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext"],
    //content_style: ".mce-content-body {font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;}",
    theme_advanced_toolbar_align : "left",
    theme_advanced_toolbar_location : "top",
    plugins : [
      "advlist autolink lists link image charmap preview anchor imagetools",
      "searchreplace visualblocks codesample fullscreen",
      "insertdatetime media table contextmenu paste directionality"
    ],
    entity_encoding : "raw",
    theme_advanced_buttons1_add : "forecolorpicker,fontsizeselect",
    theme_advanced_buttons2_add: "media",
    theme_advanced_buttons3: "",
    theme_advanced_disable : "styleselect,anchor",
    toolbar: "undo redo | styleselect | bold italic | link image | alignleft aligncenter alignright | ltr rtl | codesample",
    relative_urls : false,
    remove_script_host : false,
    convert_urls : false
  });

</script>


<div id="blg-body" class="blg-theme-<?php echo osc_current_web_theme(); ?>">
  <div id="blg-main">
    <div class="blg-publish">
      <h1><?php echo ($blog_id > 0 ? __('Edit article', 'blog') : __('Add new article', 'blog')); ?></h1>

      <form class="nocsrf" method="POST" name="blg_new_post" action="<?php echo osc_route_url('blg-action', array('blgPage' => 'blog')); ?>" enctype="multipart/form-data">
        <?php if($blog_id > 0) { ?><input type="hidden" name="pk_i_id" value="<?php echo $blog_id; ?>" /><?php } ?>
        <?php if($blog_id > 0) { ?><input type="hidden" name="i_status" value="<?php echo @$blog['i_status']; ?>" /><?php } ?>
        <input type="hidden" name="fk_i_user_id" value="<?php echo $author_id; ?>" />
        <input type="hidden" name="fk_c_locale_code" value="<?php echo osc_current_user_locale(); ?>" />

        <?php if(count($categories) > 0) { ?>
          <div class="blg-row">
            <label for="i_category"><?php _e('Category', 'blog'); ?></label>

            <select name="i_category" id="i_category">
              <option value="" <?php if(@$blog['i_category'] <= 0) { ?>selected="selected"<?php } ?>><?php _e('Uncategorized', 'blog'); ?></option>

              <?php foreach($categories as $c) { ?>
                <option value="<?php echo $c['pk_i_id']; ?>" <?php if(@$blog['i_category'] == $c['pk_i_id']) { ?>selected="selected"<?php } ?>><?php echo $c['pk_i_id']; ?> - <?php echo blg_get_cat_name($c); ?></option>
              <?php } ?>
            </select>
          </div>
        <?php } ?>

        <?php if(blg_param('blog_validate') <> 1) { ?>
          <div class="blg-row">
            <label for="i_status"><?php _e('Status', 'blog'); ?></label>

            <select name="i_status" id="i_status">
              <option value="0" <?php if(@$blog['i_status'] <= 0) { ?>selected="selected"<?php } ?>><?php _e('Private', 'blog'); ?></option>
              <option value="1" <?php if(@$blog['i_status'] == 1) { ?>selected="selected"<?php } ?>><?php _e('Public', 'blog'); ?></option>
              <option value="2" <?php if(@$blog['i_status'] == 2) { ?>selected="selected"<?php } ?>><?php _e('Premium', 'blog'); ?></option>
            </select>
          </div>
        <?php } ?>


        <div class="blg-row blg-row-image">
          <label for="image"><span><?php _e('Image', 'blog'); ?></span></label> 
 
          <?php $img = blg_img($blog_id, '', 1); ?>
          <?php if($img) { ?>
            <a class="blg-img-preview" href="<?php echo $img; ?>" target="_blank"><img class="blg-blog-img" src="<?php echo $img; ?>" /></a>
          <?php } ?>

          <div class="blg-att">
            <label class="file-label">
              <span class="wrap"><i class="fa fa-paperclip"></i> <span><?php echo (@$blog['s_image'] == '' ? __('Upload image', 'blog') : __('Replace image', 'blog')); ?></span></span>
              <input type="file" id="image" name="image" />
            </label>

            <div class="blg-explain"><?php _e('Allowed extensions', 'blog'); ?>: .png, .jpg, .jpeg, .gif, .webp</div>
          </div>
        </div>

        <div class="blg-row">
          <label for="s_slug"><?php _e('Slug', 'blog'); ?></label>
          <input type="text" id="s_slug" name="s_slug" size="60" value="<?php echo @$blog['s_slug']; ?>" <?php if(@$blog['s_slug'] == '') { ?>class="is_blank"<?php } ?> />

          <div class="blg-explain"><?php _e('Slug is used to construct URL of post.', 'blog'); ?></div>
        </div>

        <div class="blg-row">
          <label for="s_title"><?php _e('Title', 'blog'); ?></label>
          <input type="text" id="s_title" name="s_title" size="80" value="<?php echo @$blog['s_title']; ?>" />
        </div>

        <div class="blg-row">
          <label for="s_subtitle"><?php _e('Sub-title', 'blog'); ?></label>
          <textarea id="s_subtitle" name="s_subtitle"><?php echo @$blog['s_subtitle']; ?></textarea>

          <div class="blg-explain"><?php _e('Short summary of post, maximum 300 char. length.', 'blog'); ?></div>
        </div>

        <div class="blg-row">
          <label for="s_description"><?php _e('Description', 'blog'); ?></label>
          <textarea id="s_description" name="s_description"><?php echo @$blog['s_description']; ?></textarea>
        </div>

        <div class="blg-row blg-seo-row">
          <div class="blg-row">
            <label for="s_seo_title"><?php _e('Seo Title', 'blog'); ?></label>
            <input type="text" id="s_seo_title" name="s_seo_title" size="60" value="<?php echo @$blog['s_seo_title']; ?>" />

            <div class="blg-explain"><?php _e('Title that should show search engine. Max 60 char. length.', 'blog'); ?></div>
          </div>

          <div class="blg-row">
            <label for="s_seo_description"><?php _e('Seo Description', 'blog'); ?></label>
            <textarea id="s_seo_description" name="s_seo_description"><?php echo @$blog['s_seo_description']; ?></textarea>

            <div class="blg-explain"><?php _e('Description that should show search engine. Max 300 char. length.', 'blog'); ?></div>
          </div> 
        </div>


        <?php if(!blg_is_demo()) { ?>
          <button class="blg-btn blg-btn-primary" type="submit" title="<?php echo osc_esc_html(__('Submit', 'blog')); ?>"><i class="fa fa-check"></i> <?php echo osc_esc_html(__('Submit', 'blog')); ?></button>
        <?php } else { ?>
          <a class="blg-btn blg-btn-primary disabled" style="opacity:0.4;cursor:not-allowed;" disabled type="submit" title="<?php echo osc_esc_html(__('You cannot do this, it is demo site', 'blog')); ?>"><i class="fa fa-check"></i> <?php echo osc_esc_html(__('Submit', 'blog')); ?></a>
        <?php } ?> 

      </form>
    </div>
  </div>

  <?php require_once 'sidebar.php'; ?>

</div>