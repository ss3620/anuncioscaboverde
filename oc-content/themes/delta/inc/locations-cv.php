<?php
/**
 * Cabo Verde location helpers and one-time seed for Osclass location tables.
 */

if (!defined('ABS_PATH')) {
  exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

function del_site_country_code() {
  return 'CV';
}

function del_acv_slug($name) {
  $s = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
  if ($s === false) {
    $s = $name;
  }
  $s = strtolower($s);
  $s = preg_replace('/[^a-z0-9]+/', '-', $s);
  return trim($s, '-');
}

function del_acv_cv_location_map() {
  return array(
    'Santiago' => array('Praia', 'Assomada', 'Tarrafal', 'Pedra Badejo', 'Cidade Velha', 'Sao Domingos', 'Calheta de Sao Miguel'),
    'Sao Vicente' => array('Mindelo'),
    'Sal' => array('Santa Maria', 'Espargos'),
    'Boa Vista' => array('Sal Rei'),
    'Fogo' => array('Sao Filipe', 'Mosteiros', 'Cova Figueira'),
    'Santo Antao' => array('Porto Novo', 'Ribeira Grande', 'Ponta do Sol', 'Pombas'),
    'Sao Nicolau' => array('Ribeira Brava', 'Tarrafal de Sao Nicolau'),
    'Maio' => array('Vila do Maio'),
    'Brava' => array('Nova Sintra', 'Furna'),
  );
}

function del_acv_seed_cv_locations() {
  if (!class_exists('Country') || !class_exists('Region') || !class_exists('City')) {
    return false;
  }

  $code = del_site_country_code();
  $country = Country::newInstance()->findByCode($code);

  if (empty($country)) {
    Country::newInstance()->dao->insert(DB_TABLE_PREFIX . 't_country', array(
      'pk_c_code' => $code,
      's_name' => 'Cabo Verde',
      's_name_native' => 'Cabo Verde',
      's_phone_code' => '238',
      's_currency' => 'CVE',
      's_slug' => 'cabo-verde',
    ));
  }

  $regionModel = Region::newInstance();
  $cityModel = City::newInstance();
  $prefix = DB_TABLE_PREFIX;
  $created = 0;

  foreach (del_acv_cv_location_map() as $regionName => $cities) {
    $region = $regionModel->dao->query(
      "SELECT pk_i_id FROM {$prefix}t_region WHERE fk_c_country_code = '{$code}' AND s_name = " . $regionModel->dao->escape($regionName) . " LIMIT 1"
    );

    $regionId = 0;
    if ($region && $region->numRows() > 0) {
      $regionId = (int) $region->row()['pk_i_id'];
    } else {
      $regionModel->dao->insert($prefix . 't_region', array(
        'fk_c_country_code' => $code,
        's_name' => $regionName,
        's_name_native' => $regionName,
        'b_active' => 1,
        's_slug' => del_acv_slug($regionName),
      ));
      $regionId = (int) $regionModel->dao->insertedId();
      $created++;
    }

    if ($regionId <= 0) {
      continue;
    }

    foreach ($cities as $cityName) {
      $city = $cityModel->dao->query(
        "SELECT pk_i_id FROM {$prefix}t_city WHERE fk_c_country_code = '{$code}' AND fk_i_region_id = {$regionId} AND s_name = " . $cityModel->dao->escape($cityName) . " LIMIT 1"
      );

      $cityId = 0;
      if ($city && $city->numRows() > 0) {
        $cityId = (int) $city->row()['pk_i_id'];
      } else {
        $cityModel->dao->insert($prefix . 't_city', array(
          'fk_i_region_id' => $regionId,
          'fk_c_country_code' => $code,
          's_name' => $cityName,
          's_name_native' => $cityName,
          'b_active' => 1,
          's_slug' => del_acv_slug($cityName),
        ));
        $cityId = (int) $cityModel->dao->insertedId();
        $created++;
      }

      if ($cityId > 0) {
        $cityModel->dao->query(
          "INSERT INTO {$prefix}t_city_stats (fk_i_city_id, i_num_items) VALUES ({$cityId}, 0) ON DUPLICATE KEY UPDATE fk_i_city_id = fk_i_city_id"
        );
      }
    }
  }

  if (class_exists('CityStats')) {
    CityStats::newInstance()->dao->query(
      "INSERT INTO {$prefix}t_country_stats (fk_c_country_code, i_num_items) VALUES ('{$code}', 0) ON DUPLICATE KEY UPDATE fk_c_country_code = fk_c_country_code"
    );
  }

  return $created >= 0;
}

function del_acv_locations_align() {
  if (osc_get_preference('acv_locations_v1', 'theme-delta') == '1') {
    return;
  }

  try {
    del_acv_seed_cv_locations();
    osc_set_preference('def_locations', 'city', 'theme-delta');
    osc_set_preference('acv_locations_v1', '1', 'theme-delta');
    osc_reset_preferences();
  } catch (Exception $e) {
    // Never break the frontend if location seed fails
    if (defined('OSC_DEBUG') && OSC_DEBUG && defined('OSC_DEBUG_LOG') && OSC_DEBUG_LOG) {
      error_log('[ACV] location seed failed: ' . $e->getMessage());
    }
  }
}

function del_acv_filtered_countries() {
  $countries = Country::newInstance()->listAll();
  $siteCode = strtoupper(del_site_country_code());
  $filtered = array();

  foreach ($countries as $c) {
    if (strtoupper($c['pk_c_code']) === $siteCode) {
      $filtered[] = $c;
    }
  }

  return count($filtered) > 0 ? $filtered : $countries;
}
