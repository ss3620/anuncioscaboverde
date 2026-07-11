<?php

  // Create menu
  $title = __('Blog categories', 'blog');
  blg_menu($title);

  $locale = (Params::getParam('fk_c_locale_code') <> '' ? Params::getParam('fk_c_locale_code') : Params::getParam('blgLocale'));
  $locale = ($locale <> '' ? $locale : blg_get_locale());


  if((Params::getParam('editId') > 0 || Params::getParam('editId') == -1) && Params::getParam('plugin_action') == 'done') {
    $id = Params::getParam('editId');

    if($id == -1) {
      $id = ModelBLG::newInstance()->insertCategory(array('s_color' => '#60c29a'));
      Params::setParam('editId', $id);
    }

    $category = ModelBLG::newInstance()->getCategoryDetail($id);


    $data_cat = array(
      'pk_i_id' => $id,
      's_color' => Params::getParam('s_color')
    );


    $data_locale = array(
      'fk_i_category_id' => $id,
      'fk_c_locale_code' => Params::getParam('blgLocale'),
      's_name' => Params::getParam('s_name'),
      's_description' => Params::getParam('s_description')
    );


    ModelBLG::newInstance()->updateCategory($data_cat, $data_locale);

    message_ok( __('Category successfully updated', 'blog') );
  }


  if(Params::getParam('deleteId') > 0) {
    ModelBLG::newInstance()->removeCategory(Params::getParam('deleteId'));
    message_ok( __('Category removed successfully', 'blog') );
  }

  $categories = ModelBLG::newInstance()->getCategories();
?>


<div class="mb-body">

  <div class="mb-message-js"></div>


  <?php if(Params::getParam('editId') > 0 || Params::getParam('editId') == -1) { ?>
    <?php 
      $category = ModelBLG::newInstance()->getCategoryDetail(Params::getParam('editId'));
    ?>

    <div class="mb-box">
      <div class="mb-head">
        <i class="fa fa-plus-circle"></i> <?php _e('Add/edit category', 'blog'); ?>
        <?php echo blg_locale_box('category.php&editId=' . Params::getParam('editId')); ?>
      </div>

      <div class="mb-inside mb-category-edit">
        <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
          <input type="hidden" name="page" value="plugins" />
          <input type="hidden" name="action" value="renderplugin" />
          <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>category.php" />
          <input type="hidden" name="plugin_action" value="done" />
          <input type="hidden" name="blgLocale" value="<?php echo (@$category['fk_c_locale_code'] <> '' ? $category['fk_c_locale_code'] : $locale); ?>" />
          <input type="hidden" name="editId" value="<?php echo Params::getParam('editId'); ?>" />

          <div class="mb-row">
            <label for="s_name"><?php _e('Name', 'blog'); ?></label>
            <input type="text" id="s_name" name="s_name" size="30" value="<?php echo @$category['s_name']; ?>" />

            <div class="mb-explain"><?php _e('Short name or title of category.', 'blog'); ?></div>
          </div>

          <div class="mb-row">
            <label for="s_description"><?php _e('Description', 'blog'); ?></label>
            <textarea id="s_description" name="s_description"><?php echo @$category['s_description']; ?></textarea>

            <div class="mb-explain"><?php _e('Detail description or summary of category.', 'blog'); ?></div>
          </div>

          <div class="mb-row">
            <label for="s_color"><?php _e('Color', 'blog'); ?></label>
            <input type="color" id="s_color" name="s_color" value="<?php echo (@$category['s_color'] <> '' ? @$category['s_color'] : '#60c29a'); ?>" />

            <div class="mb-explain"><?php _e('Primary category color.', 'blog'); ?></div>
          </div>

          <div class="mb-row">&nbsp;</div>

          <div class="mb-foot">
            <?php if(!blg_is_demo()) { ?><button type="submit" class="mb-button"><?php _e('Save', 'blog');?></button><?php } ?>
          </div>
        </form>
      </div>
    </div>
  <?php } ?>



  <!-- CATEGORIES SECTION -->
  <div class="mb-box">
    <div class="mb-head">
      <i class="fa fa-list"></i> <?php echo $title; ?>
      <?php echo blg_locale_box('category.php'); ?>
    </div>

    <div class="mb-inside mb-blog-category">

      <div class="mb-row mb-notes">
        <div class="mb-line"><?php _e('List of categories to which blog posts can be assigned.', 'blog'); ?></div>
      </div>

      <div class="mb-table mb-table-category" id="mb-blog-categories">
        <div class="mb-table-head">
          <div class="mb-col-1"><span>&nbsp;</span></div>
          <div class="mb-col-1"><span><?php _e('ID', 'blog');?></span></div>
          <div class="mb-col-4 mb-align-left"><span><?php _e('Name', 'blog');?></span></div>
          <div class="mb-col-9 mb-align-left"><span><?php _e('Description', 'blog');?></span></div>
          <div class="mb-col-3 mb-align-left"><span><?php _e('Color', 'blog');?></span></div>
          <div class="mb-col-4 mb-align-left"><span><?php _e('Blog posts', 'blog');?></span></div>
          <div class="mb-col-2"><span>&nbsp;</span></div>
        </div>

        <?php if(count($categories) <= 0) { ?>
          <div class="mb-table-row mb-row-empty">
            <i class="fa fa-warning"></i><span><?php _e('No blog categories has been found', 'blog'); ?></span>
          </div>
        <?php } else { ?>
          <?php foreach($categories as $c) { ?>
            <div class="mb-table-row" id="cat_<?php echo $c['pk_i_id']; ?>" data-id="<?php echo $c['pk_i_id']; ?>">
              <div class="mb-col-1 ccolor" <?php echo ($c['s_color'] <> '' ? 'style="border-left-color:' . $c['s_color'] . '"' : ''); ?>><i class="fa fa-arrows mb-has-tooltip move" title="<?php echo osc_esc_html(__('Reorder posts', 'blog')); ?>"></i></div>
              <div class="mb-col-1"><?php echo $c['pk_i_id']; ?></div>
              <div class="mb-col-4 mb-align-left"><?php echo ($c['s_name'] <> '' ? $c['s_name'] : __('No name', 'blog') . ' (' . blg_get_locale() . ')'); ?></div>
              <div class="mb-col-9 mb-align-left"><?php echo ($c['s_description'] <> '' ? $c['s_description'] : '-'); ?></div>
              <div class="mb-col-3 mb-align-left"><?php echo ($c['s_color'] <> '' ? $c['s_color'] : '-'); ?></div>
              <div class="mb-col-4 mb-align-left"><a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/list.php&categoryId=<?php echo $c['pk_i_id']; ?>"><?php echo ($c['i_blog_count'] == 1 ? __('1 post', 'blog') : sprintf(__('%d posts', 'blog'), $c['i_blog_count'])); ?></a></div>
              <div class="mb-col-2">
                <?php if(!blg_is_demo()) { ?>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/category.php&editId=<?php echo $c['pk_i_id']; ?>&blgLocale=<?php echo blg_get_locale(); ?>" class="mb-btn mb-button-white"><i class="fa fa-pencil"></i></a>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/category.php&deleteId=<?php echo $c['pk_i_id']; ?>&blgLocale=<?php echo blg_get_locale(); ?>" class="mb-btn mb-button-red" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this category? Action cannot be undone', 'blog')); ?>')"><i class="fa fa-trash-o"></i></a>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        <?php } ?>
      </div>

      <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=blog/admin/category.php&editId=-1&blgLocale=<?php echo blg_get_locale(); ?>" class="mb-add-user mb-add-category"><i class="fa fa-plus-circle"></i><?php _e('Add new category', 'blog'); ?></a>

      <div class="mb-row">&nbsp;</div>

    </div>
  </div>
</div>


<script type="text/javascript">
  var blg_position_url = "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=runhook&hook=blg_cat_position";

  var blg_message_ok = "<?php echo osc_esc_html(__('Success!', 'blog')); ?>";
  var blg_message_wait = "<?php echo osc_esc_html(__('Updating, please wait...', 'blog')); ?>";
  var blg_message_error = "<?php echo osc_esc_html(__('Error!', 'blog')); ?>";


  $(document).ready(function(){
    var blg_list = '';

    $('#mb-blog-categories').sortable({
      axis: "y",
      forcePlaceholderSize: true,
      handle: '.move',
      helper: 'clone',
      items: '.mb-table-row',
      opacity: .8,
      placeholder: 'placeholder',
      revert: 100,
      tabSize: 5,
      tolerance: 'intersect',
      start: function(event, ui) {
        blg_list = $(this).sortable('serialize');
      },
      stop: function (event, ui) {
        var c_blg_list = $(this).sortable('serialize');

        blg_message(blg_message_wait, 'info');

        if(blg_list != c_blg_list) {
          $.ajax({
            url: blg_position_url,
            type: "GET",
            data: c_blg_list,
            success: function(response){
              //console.log(response);
              blg_message(blg_message_ok, 'ok');
            },
            error: function(response) {
              blg_message(blg_message_error, 'error');
              console.log(response);
            }
          });
        }
      }
    });

  });
</script>


<?php echo blg_footer(); ?>