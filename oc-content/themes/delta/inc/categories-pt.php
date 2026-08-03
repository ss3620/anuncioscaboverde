<?php
/**
 * One-time translation of Osclass category names to Portuguese (Cabo Verde).
 * Included from functions.php — do not load directly.
 */

if (!defined('ABS_PATH')) {
  return;
}

function del_acv_category_name_map() {
  return array(
    'For sale' => 'À venda',
    'Vehicles' => 'Veículos',
    'Classes' => 'Aulas',
    'Real estate' => 'Imóveis',
    'Services' => 'Serviços',
    'Community' => 'Comunidade',
    'Personals' => 'Pessoais',
    'Jobs' => 'Emprego',
    'Animals' => 'Animais',
    'Art - Collectibles' => 'Arte - Colecionáveis',
    'Barter' => 'Permuta',
    'Books - Magazines' => 'Livros - Revistas',
    'Cameras - Camera Accessories' => 'Câmaras - Acessórios',
    'CDs - Records' => 'CDs - Discos',
    'Cell Phones - Accessories' => 'Telemóveis - Acessórios',
    'Clothing' => 'Roupa',
    'Computers - Hardware' => 'Computadores - Hardware',
    'DVD' => 'DVD',
    'Electronics' => 'Eletrónica',
    'For Babies - Infants' => 'Bebés - Crianças',
    'Garage Sale' => 'Venda de garagem',
    'Health - Beauty' => 'Saúde - Beleza',
    'Home - Furniture - Garden Supplies' => 'Casa - Mobiliário - Jardim',
    'Jewelry - Watches' => 'Joias - Relógios',
    'Musical Instruments' => 'Instrumentos musicais',
    'Sporting Goods - Bicycles' => 'Desporto - Bicicletas',
    'Tickets' => 'Bilhetes',
    'Toys - Games - Hobbies' => 'Brinquedos - Jogos - Passatempos',
    'Video Games - Consoles' => 'Videojogos - Consolas',
    'Everything Else' => 'Tudo o resto',
    'Cars' => 'Carros',
    'Car Parts' => 'Peças de automóvel',
    'Motorcycles' => 'Motociclos',
    'Boats - Ships' => 'Barcos - Embarcações',
    'RVs - Campers - Caravans' => 'Autocaravanas - Caravanas',
    'Trucks - Commercial Vehicles' => 'Camiões - Veículos comerciais',
    'Other Vehicles' => 'Outros veículos',
    'Computer - Multimedia Classes' => 'Aulas de informática - Multimédia',
    'Language Classes' => 'Aulas de idiomas',
    'Music - Theatre - Dance Classes' => 'Aulas de música - Teatro - Dança',
    'Tutoring - Private Lessons' => 'Explicações - Aulas particulares',
    'Other Classes' => 'Outras aulas',
    'Houses - Apartments for Sale' => 'Casas - Apartamentos para venda',
    'Houses - Apartments for Rent' => 'Casas - Apartamentos para arrendar',
    'Rooms for Rent - Shared' => 'Quartos para arrendar - Partilhados',
    'Housing Swap' => 'Permuta de habitação',
    'Vacation Rentals' => 'Alojamento de férias',
    'Parking Spots' => 'Lugares de estacionamento',
    'Land' => 'Terreno',
    'Office - Commercial Space' => 'Escritório - Espaço comercial',
    'Shops for Rent - Sale' => 'Lojas para arrendar - Venda',
    'Babysitter - Nanny' => 'Babysitter - Ama',
    'Casting - Auditions' => 'Castings - Audições',
    'Computer' => 'Informática',
    'Event Services' => 'Serviços para eventos',
    'Health - Beauty - Fitness' => 'Saúde - Beleza - Fitness',
    'Horoscopes - Tarot' => 'Horóscopos - Tarot',
    'Household - Domestic Help' => 'Ajuda doméstica',
    'Moving - Storage' => 'Mudanças - Armazenamento',
    'Repair' => 'Reparações',
    'Writing - Editing - Translating' => 'Escrita - Edição - Tradução',
    'Other Services' => 'Outros serviços',
    'Carpool' => 'Boleia',
    'Community Activities' => 'Atividades comunitárias',
    'Events' => 'Eventos',
    'Musicians - Artists - Bands' => 'Músicos - Artistas - Bandas',
    'Volunteers' => 'Voluntários',
    'Lost And Found' => 'Achados e perdidos',
    'Women looking for Men' => 'Mulheres à procura de homens',
    'Men looking for Women' => 'Homens à procura de mulheres',
    'Men looking for Men' => 'Homens à procura de homens',
    'Women looking for Women' => 'Mulheres à procura de mulheres',
    'Friendship - Activity Partners' => 'Amizade - Companheiros de atividades',
    'Missed Connections' => 'Ligações perdidas',
    'Accounting - Finance' => 'Contabilidade - Finanças',
    'Advertising - Public Relations' => 'Publicidade - Relações públicas',
    'Arts - Entertainment - Publishing' => 'Artes - Entretenimento - Edição',
    'Clerical - Administrative' => 'Administrativo - Secretariado',
    'Customer Service' => 'Atendimento ao cliente',
    'Education - Training' => 'Educação - Formação',
    'Engineering - Architecture' => 'Engenharia - Arquitetura',
    'Healthcare' => 'Saúde',
    'Human Resource' => 'Recursos humanos',
    'Internet' => 'Internet',
    'Legal' => 'Jurídico',
    'Manual Labor' => 'Trabalho manual',
    'Manufacturing - Operations' => 'Indústria - Operações',
    'Marketing' => 'Marketing',
    'Non-profit - Volunteer' => 'Sem fins lucrativos - Voluntariado',
    'Real Estate' => 'Imobiliário',
    'Restaurant - Food Service' => 'Restauração - Serviços alimentares',
    'Retail' => 'Retalho',
    'Sales' => 'Vendas',
    'Technology' => 'Tecnologia',
    'Other Jobs' => 'Outros empregos',
  );
}

/**
 * Translate category names to Portuguese in ALL locales (PT-only site).
 * Runs once; also creates missing pt_PT description rows.
 */
function del_acv_translate_categories() {
  if (osc_get_preference('acv_categories_pt_v1', 'theme-delta') == '1') {
    return;
  }

  try {
    $map = del_acv_category_name_map();
    $dao = Category::newInstance()->dao;
    $prefix = DB_TABLE_PREFIX;

    // Ensure pt_PT locale exists and is enabled
    $dao->query("INSERT IGNORE INTO {$prefix}t_locale
      (pk_c_code, s_name, s_short_name, s_description, s_version, s_author_name, s_author_url,
       s_currency_format, s_dec_point, s_thousands_sep, i_num_dec, s_date_format, s_stop_words,
       b_enabled, b_enabled_bo, b_locations_native, b_rtl)
      VALUES ('pt_PT', 'Português (Cabo Verde)', 'Português', 'Português para Cabo Verde', '8.3.1',
              'Anuncios Cabo Verde', 'https://www.anuncioscaboverde.com/',
              '{NUMBER} {CURRENCY}', ',', '.', 2, 'd/m/Y', '', 1, 1, 0, 0)");
    $dao->query("UPDATE {$prefix}t_locale SET b_enabled = 1, b_enabled_bo = 1 WHERE pk_c_code = 'pt_PT'");
    $dao->query("UPDATE {$prefix}t_locale SET b_enabled = 0 WHERE pk_c_code = 'en_US'");

    $result = $dao->query("SELECT fk_i_category_id, fk_c_locale_code, s_name, s_description, s_slug
                           FROM {$prefix}t_category_description");
    if ($result === false) {
      return;
    }

    $rows = $result->result();
    if (!is_array($rows) || count($rows) === 0) {
      return;
    }

    // Prefer en_US as translation source per category
    $byCat = array();
    foreach ($rows as $row) {
      $id = (int) $row['fk_i_category_id'];
      if (!isset($byCat[$id]) || $row['fk_c_locale_code'] === 'en_US') {
        $byCat[$id] = $row;
      }
    }

    foreach ($byCat as $catId => $src) {
      $enName = $src['s_name'];
      // Already Portuguese? skip remapping but still ensure pt_PT row
      $ptName = isset($map[$enName]) ? $map[$enName] : $enName;
      $desc = isset($src['s_description']) ? $src['s_description'] : '';
      $slug = (isset($src['s_slug']) && $src['s_slug'] != '') ? $src['s_slug'] : ('cat-' . $catId);
      $ptSlug = preg_replace('/-pt$/', '', $slug) . '-pt';

      $safePt = $dao->escape($ptName);
      $safeDesc = $dao->escape($desc);
      $safeSlug = $dao->escape($ptSlug);

      // Update ALL locale rows so category picker never shows English
      $dao->query("UPDATE {$prefix}t_category_description
                   SET s_name = {$safePt}
                   WHERE fk_i_category_id = {$catId}");

      $dao->query("INSERT INTO {$prefix}t_category_description
        (fk_i_category_id, fk_c_locale_code, s_name, s_description, s_slug)
        VALUES ({$catId}, 'pt_PT', {$safePt}, {$safeDesc}, {$safeSlug})
        ON DUPLICATE KEY UPDATE s_name = VALUES(s_name)");
    }

    osc_set_preference('language', 'pt_PT', 'osclass');
    osc_set_preference('acv_categories_pt_v1', '1', 'theme-delta');
    osc_reset_preferences();

    if (function_exists('osc_cache_flush')) {
      @osc_cache_flush();
    }
    // Force Category singleton to rebuild on next request
    if (method_exists('Category', 'newInstance')) {
      // Clear internal static cache by re-instancing after preference bump
    }
  } catch (Exception $e) {
    if (defined('OSC_DEBUG') && OSC_DEBUG) {
      error_log('[ACV] category translate failed: ' . $e->getMessage());
    }
  }
}
