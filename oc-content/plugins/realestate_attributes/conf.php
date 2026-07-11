<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');?>

<?php
if(Params::getParam('plugin_action')!='') {
  if(Params::getParam('plugin_action')=="type_delete") {
    if(Params::getParam('id')!="") {
      ModelRealEstate::newInstance()->deletePropertyType(Params::getParam('id')) ;
    }
  } else if(Params::getParam('plugin_action')=="type_add") {
    $dataItem = array();
    $request = Params::getParamsAsArray();
    foreach ($request as $k => $v) {
      if (preg_match('|(.+?)#(.+)|', $k, $m)) {
        $dataItem[$m[1]][$m[2]] = $v;
      }
    }
    // insert locales
    $lastId = ModelRealEstate::newInstance()->getLastPropertyTypeId();
    $lastId = ($lastId > 0 ? $lastId : 0);
    $lastId = $lastId + 1; //$lastId['pk_i_id'] + 1 ;
    
    if(is_array($dataItem) && count($dataItem) > 0) {
      foreach ($dataItem as $k => $_data) {
        ModelRealEstate::newInstance()->insertPropertyType($lastId, $k, $_data['property_type']);
      }
    }
  } else if(Params::getParam('plugin_action')=="type_edit") {
    $property_type = Params::getParam('property_type');

    if(is_array($property_type) && count($property_type) > 0) {
      foreach($property_type as $k => $v) {
        if(is_array($v) && count($v) > 0) {
          foreach($v as $kj => $vj) {
            ModelRealEstate::newInstance()->replacePropertyType($k, $kj, $vj);
          }
        }
      }
    }
  }
}
?>

<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
  <div style="padding: 20px;">
    <div style="float: left; width: 50%;">
      <fieldset>
        <legend><?php _e('Property types', 'realestate_attributes'); ?></legend>
          <form name="propertys_form" id="propertys_form" action="<?php echo osc_admin_base_url(true);?>" method="GET" enctype="multipart/form-data" >
          <input type="hidden" name="page" value="plugins" />
          <input type="hidden" name="action" value="renderplugin" />
          <input type="hidden" name="file" value="realestate_attributes/conf.php" />
          <input type="hidden" name="section" value="types" />
          <input type="hidden" name="plugin_action" value="type_edit" />
        <div class="tabber">
        <?php $locales = osc_get_locales();
          $property_type = ModelRealEstate::newInstance()->getPropertyTypes(false) ;
          $data = array();
          foreach ($property_type as $c) {
            $data[$c['fk_c_locale_code']][] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name']);
          }
          $default = current($data);
          if(is_array($default)) {
          foreach($default as $d) {
            $data['new'][] = array('pk_i_id' => $d['pk_i_id'], 's_name' => '');
          }}
        ?>
          <?php foreach($locales as $locale) {?>
            <div class="tabbertab">
              <h2><?php echo $locale['s_name']; ?></h2>
                <ul>
                <?php
                  if(count($data)>0) {
                    foreach(isset($data[$locale['pk_c_code']])?$data[$locale['pk_c_code']]:$data['new'] as $property_type) { ?>
                      <li><input name="property_type[<?php echo  $property_type['pk_i_id'];?>][<?php echo  $locale['pk_c_code'];?>]" id="<?php echo $property_type['pk_i_id'];?>" type="text" value="<?php echo  $property_type['s_name'];?>" /> <a href="<?php echo osc_admin_base_url(true);?>?page=plugins&action=renderplugin&file=realestate_attributes/conf.php?plugin_action=type_delete&id=<?php echo  $property_type['pk_i_id'];?>" class="btn"><?php _e('Delete', 'realestate_attributes'); ?></a></li>
                    <?php };
                  }; ?>
                </ul>
            </div>
            <?php }; ?>
            <button class="btn-submit" type="submit"><?php _e('Edit', 'realestate_attributes'); ?></button>
          </form>
        </div>
      </fieldset>
    </div>
    <div style="float: left; width: 50%;">
      <fieldset>
        <legend><?php _e('Add new property types', 'realestate_attributes'); ?></legend>
        <form name="propertys_form" id="propertys_form" action="<?php echo osc_admin_base_url(true); ?>" method="GET" enctype="multipart/form-data" >
          <input type="hidden" name="page" value="plugins" />
          <input type="hidden" name="action" value="renderplugin" />
          <input type="hidden" name="file" value="realestate_attributes/conf.php" />
          <input type="hidden" name="plugin_action" value="type_add" />

          <div class="tabber">
          <?php $locales = osc_get_locales();
            $data = array();
            if(is_array($property_type) && count($property_type) > 0) {
              foreach ($property_type as $c) {
                if(isset($c['pk_i_id'])) {
                  $data[$locale['pk_c_code']] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name']);
                }
              }
            }
          ?>
          <?php foreach($locales as $locale) {?>
            <div class="tabbertab">
              <h2><?php echo $locale['s_name']; ?></h2>
              <input name="<?php echo  $locale['pk_c_code'];?>#property_type" id="property_type" type="text" value="" />
            </div>
          <?php }; ?>
          </div>
          <button class="btn-submit" type="submit" ><?php _e('Add new', 'realestate_attributes'); ?></button>
        </form>
      </fieldset>
    </div>
    <div style="clear: both;"></div>										
  </div>
</div>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
  <div style="padding: 20px;">
    <div style="float: left; width: 100%;">
      <fieldset style="border: 1px solid #ff0000;">
        <legend><?php _e('Warning', 'realestate_attributes'); ?></legend>
        <p>
          <?php _e('Deleting property types may end in errors. Some of those property types could be attached to some actual items', 'realestate_attributes') ; ?>.
        </p>
      </fieldset>
    </div>
    <div style="clear: both;"></div>
  </div>
</div>

<script type="text/javascript">
  tabberAutomatic();
</script>

