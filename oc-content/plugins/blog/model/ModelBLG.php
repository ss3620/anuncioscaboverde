<?php
class ModelBLG extends DAO {
private static $instance;

public static function newInstance() {
  if( !self::$instance instanceof self ) {
    self::$instance = new self;
  }
  return self::$instance;
}

function __construct() {
  parent::__construct();
}

public function getTable_blog() {
  return DB_TABLE_PREFIX.'t_blog';
}

public function getTable_blog_locale() {
  return DB_TABLE_PREFIX.'t_blog_locale';
}

public function getTable_blog_comment() {
  return DB_TABLE_PREFIX.'t_blog_comment';
}

public function getTable_blog_user() {
  return DB_TABLE_PREFIX.'t_blog_user';
}

public function getTable_blog_category() {
  return DB_TABLE_PREFIX.'t_blog_category';
}

public function getTable_blog_category_locale() {
  return DB_TABLE_PREFIX.'t_blog_category_locale';
}

public function getTable_user() {
  return DB_TABLE_PREFIX.'t_user';
}

public function getTable_category() {
  return DB_TABLE_PREFIX.'t_category';
}


public function import($file) {
  $path = osc_plugin_resource($file);
  $sql = file_get_contents($path);

  if(!$this->dao->importSQL($sql) ){
    throw new Exception("Error importSQL::ModelBLG<br>" . $file . "<br>" . $this->dao->getErrorLevel() . " - " . $this->dao->getErrorDesc() );
  }
}


// INSTALL PLUGIN
public function install() {
  $this->import('blog/model/struct.sql');
}


// EXECUTE QUERIES ON VERSION UPDATE
public function versionUpdate($ignore_error = false) {
  $version = (int)blg_param('version');     // v100 is initial
  $version = ($version >= 100 ? $version : 0);
  $plugin = 'blog';
  
  // Version not yet available - it's installation process now
  if($version == 0) {
    return true;
  }
  
  $queries = array(
    //array('version' => 104, 'query' => sprintf("ALTER TABLE %st_user_blog ADD COLUMN s_legal_notice VARCHAR(2000);", DB_TABLE_PREFIX))
  );
  
  if(is_array($queries) && count($queries) > 0) {
    foreach($queries as $query) {
      if($version < $query['version'] && $query['version'] <= BLG_VERSION_ID) {
        $result = $this->dao->query($query['query']);
        
        if($result === false && $ignore_error !== true) {
          $message  = sprintf(__('Update of plugin "%s" failed on DB version "%s". Please enable %s to see error details. Failed query is listed below.', 'blog'), __('Blog Plugin', 'blog'), $query['version'], '<a href="https://docs.osclasspoint.com/debug-mode" target="_blank">' . __('DB debug mode', 'blog') . '</a>');
          $message .= '<pre style="font-size:11px;">' . $query['query'] . '</pre>';
          $message .= '<a href="' . osc_admin_base_url(true) . '?page=plugins&forceupdateplugin=' . $plugin . '">' . __('Ignore error and force plugin update', 'blog') . '</a>. ';
          $message .= __('Never force update plugin until you are sure that your database structure match to model/struct.sql file! It may lead to unexpected plugin functionality. Try to reinstall plugin.', 'blog');

          osc_add_flash_error_message($message, 'admin');
          return false;
        }
      }
    }
  }
  
  return true;
}


// UNINSTALL PLUGIN
public function uninstall() {
  // DELETE ALL TABLES
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_blog()));
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_blog_locale()));
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_blog_comment()));
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_blog_user()));
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_blog_category()));
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_blog_category_locale()));


  // DELETE ALL PREFERENCES
  $db_prefix = DB_TABLE_PREFIX;
  $query = "DELETE FROM {$db_prefix}t_preference WHERE s_section = 'plugin-blog'";
  $this->dao->query($query);
}



// GET BLOGS
public function getBlogs($status = -1, $category_id = -1, $author_id = -1, $type = '', $keyword = '', $view = '', $limit = array()) {
  $this->dao->select('DISTINCT b.*, bl.s_title, bl.s_subtitle, bl.s_description, bl.s_seo_title, bl.s_seo_description, bl.fk_c_locale_code');
  $this->dao->from($this->getTable_blog() . ' as b');
  $this->dao->join($this->getTable_blog_locale() . ' as bl', '(bl.fk_i_blog_id = b.pk_i_id AND bl.fk_c_locale_code = "' . blg_get_locale() . '")', 'LEFT OUTER');

  if($type == 'SEARCH') {
    //$this->dao->join($this->getTable_blog_locale() . ' as bls', '(bls.fk_i_blog_id = b.pk_i_id AND bls.fk_c_locale_code = "' . osc_current_user_locale() . '")', 'INNER');
    $this->dao->join($this->getTable_blog_locale() . ' as bls', '(bls.fk_i_blog_id = b.pk_i_id)', 'INNER');
  }

  if($type == 'POPULAR') {
    $this->dao->orderby('b.i_view DESC, b.dt_pub_date DESC');
  } else if($type == 'AUTHOR') {
    $this->dao->orderby('b.dt_pub_date DESC');
  } else if($type == 'SEARCH') {
    $this->dao->orderby('b.dt_pub_date DESC');
  } else {
    if(blg_param('blog_order') == 1) {
      $this->dao->orderby('b.i_order ASC, b.dt_pub_date DESC');
    } else {
      $this->dao->orderby('b.dt_pub_date DESC');
    }
  }

  if($status == 0) {
    $this->dao->where('b.i_status', 0);
  } else if ($status > 0) {
    $this->dao->where('b.i_status > 0');
  }

  if($category_id > 0) {
    $this->dao->where('b.i_category', $category_id);
  }

  if($author_id >= 0) {
    $this->dao->where('b.fk_i_user_id', $author_id);
  }

  if($type == 'SEARCH' && $keyword <> '') {
    //$this->dao->where('bls.s_title like "%' . $keyword . '%" OR bls.s_description like "%' . $keyword . '%"');
    $clause = blg_search_clause($keyword, 'bls');
    $this->dao->where($clause);
  }
  

  // $limit[0] == limit; $limit[1] == page
  $page = (isset($limit[1]) ? $limit[1] : 0);
  $per_page = (isset($limit[0]) ? $limit[0] : -1);

  if($per_page < 0) {
    $per_page = blg_get_limits($type);
    
    // if($type == 'POPULAR') {
      // $per_page = (blg_param('popular_limit') > 0 ? blg_param('popular_limit') : 8);
    // } else if($type == 'SEARCH') {
      // $per_page = (blg_param('search_limit') > 0 ? blg_param('search_limit') : 30);
    // } else if($type == 'ACTIVE') {
      // $per_page = (blg_param('home_limit') > 0 ? blg_param('home_limit') : 15);
    // } else if($type == 'WIDGET') {
      // $per_page = ($view == 'grid' ? 5 : blg_param('widget_limit'));
      // $per_page = ($per_page > 5 ? 5 : $per_page);
    // }
  }
    
  if($page > 0) {
    $this->dao->limit(($page-1)*$per_page, $per_page);
  } else if($per_page > 0) {
    $this->dao->limit($per_page);
  }

  $result = $this->dao->get();
  
  if($result) {
    $blogs = $result->result();

    $j = 0;
    if(count($blogs) > 0) {
      foreach($blogs as $b) {
        $blogs[$j]['locales'] = $this->getBlogLocales($b['pk_i_id']);
        $blogs[$j]['comments_count'] = $this->countComments($b['pk_i_id'], 1);
        $j++;
      }
    }

    return $blogs;
  }

  return array();
}


// GET WIDGET BLOGS
public function getWidgetBlogs($view, $limit = array()) {
  return $this->getBlogs(1, blg_param('widget_category'), -1, 'WIDGET', '', $view, $limit);
}


// GET POPULAR BLOGS
public function getPopularBlogs($limit = array()) {
  return $this->getBlogs(1, -1, -1, 'POPULAR', '', '', $limit);
}


// GET AUTHOR BLOGS
public function getAuthorBlogs($author_id, $limit = array()) {
  return $this->getBlogs(1, -1, $author_id, 'AUTHOR', '', '', $limit);
}


// GET CATEGORY BLOGS
public function getCategoryBlogs($category_id, $limit = array()) {
  return $this->getBlogs(1, $category_id, -1, 'CATEGORY', '', '', $limit);
}

// GET ACTIVE BLOGS
public function getActiveBlogs($limit = array()) {
  return $this->getBlogs(1, -1, -1, 'ACTIVE', '', '', $limit);
}


// GET SEARCH BLOGS
public function getSearchBlogs($keyword, $limit = array()) {
  return $this->getBlogs(1, -1, -1, 'SEARCH', $keyword, '', $limit);
}




// COUNT BLOGS
public function countBlogs($status = -1, $category_id = -1, $author_id = -1, $type = '', $keyword = '') {
  $this->dao->select('count(*) as i_count');
  $this->dao->from($this->getTable_blog() . ' as b');


  if($type == 'SEARCH') {
    //$this->dao->join($this->getTable_blog_locale() . ' as bls', '(bls.fk_i_blog_id = b.pk_i_id AND bls.fk_c_locale_code = "' . osc_current_user_locale() . '")', 'INNER');
    $this->dao->join($this->getTable_blog_locale() . ' as bls', '(bls.fk_i_blog_id = b.pk_i_id)', 'INNER');
  }
  
  if($status == 0) {
    $this->dao->where('b.i_status', 0);
  } else if ($status > 0) {
    $this->dao->where('b.i_status > 0');
  }

  if($category_id >= 0) {
    $this->dao->where('b.i_category', $category_id);
  }

  if($author_id >= 0) {
    $this->dao->where('b.fk_i_user_id', $author_id);
  }
  
  if($type == 'SEARCH' && $keyword <> '') {
    //$this->dao->where('bls.s_title like "%' . $keyword . '%" OR bls.s_description like "%' . $keyword . '%"');
    $clause = blg_search_clause($keyword, 'bls');
    $this->dao->where($clause);
  }

  $result = $this->dao->get();
  
  if($result) {
    $blogs = $result->row();
    return $blogs['i_count'];
  }

  return 0;
}



// COUNT WIDGET BLOGS
public function countWidgetBlogs($view) {
  return $this->countBlogs(1, blg_param('widget_category'), -1, 'WIDGET', '', $view);
}


// COUNT POPULAR BLOGS
public function countPopularBlogs() {
  return $this->countBlogs(1, -1, -1, 'POPULAR');
}


// COUNT AUTHOR BLOGS
public function countAuthorBlogs($author_id) {
  return $this->countBlogs(1, -1, $author_id, 'AUTHOR');
}


// COUNT CATEGORY BLOGS
public function countCategoryBlogs($category_id) {
  return $this->countBlogs(1, $category_id, -1, 'CATEGORY');
}

// COUNT ACTIVE BLOGS
public function countActiveBlogs() {
  return $this->countBlogs(1, -1, -1, 'ACTIVE');
}


// COUNT SEARCH BLOGS
public function countSearchBlogs($keyword = '') {
  return $this->countBlogs(1, -1, -1, 'SEARCH', $keyword);
}



// COUNT COMMENTS BY BLOG ID
public function countComments($blog_id, $enabled = -1) {
  $this->dao->select('count(*) as i_count');
  $this->dao->from($this->getTable_blog_comment() . ' as c');
  $this->dao->where('c.fk_i_blog_id', $blog_id);

  if($enabled >= 0) {
    $this->dao->where('c.b_enabled', $enabled);
  }


  $result = $this->dao->get();
  
  if($result) {
    $comments = $result->row();
    return $comments['i_count'];
  }

  return 0;
}


// COUNT COMMENTS
public function countCommentsByType($enabled = -1) {
  $this->dao->select('count(*) as i_count');
  $this->dao->from($this->getTable_blog_comment() . ' as c');

  if($enabled >= 0) {
    $this->dao->where('c.b_enabled', $enabled);
  }

  $result = $this->dao->get();
  
  if($result) {
    $comments = $result->row();
    return $comments['i_count'];
  }

  return 0;
}


// GET BLOG ROW
public function getBlog($blog_id) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_blog() . ' as b');
  $this->dao->where('b.pk_i_id', $blog_id);

  $result = $this->dao->get();
  
  if($result) {
    $blog = $result->row();
    return $blog;
  }

  return array();
}


// GET BLOG WITH DETAILS
public function getBlogDetail($blog_id) {
  $this->dao->select('b.*, bl.s_title, bl.s_subtitle, bl.s_description, bl.s_seo_title, bl.s_seo_description, bl.fk_c_locale_code');
  $this->dao->from($this->getTable_blog() . ' as b');
  $this->dao->join($this->getTable_blog_locale() . ' as bl', '(bl.fk_i_blog_id = b.pk_i_id AND bl.fk_c_locale_code = "' . blg_get_locale() . '")', 'LEFT OUTER');

  $this->dao->where('b.pk_i_id', $blog_id);

  $result = $this->dao->get();
  
  if($result) {
    $blog = $result->row();

    if(isset($blog['pk_i_id'])) {
      $blog['locales'] = $this->getBlogLocales($blog['pk_i_id']);
    }

    return $blog;
  }

  return array();
}



// GET BLOG COMMENTS
public function getBlogComments($blog_id, $status = -1) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_blog_comment() . ' as c');

  $this->dao->where('c.fk_i_blog_id', $blog_id);

  if($status >= 0) {
    $this->dao->where('c.i_status', $status);
  }

  $this->dao->orderby('c.dt_pub_date DESC');

  $result = $this->dao->get();
  
  if($result) {
    $comments = $result->result();
    return $comments;
  }

  return array();
}



// COUNT BLOG COMMENTS
public function countBlogComments($blog_id, $status = -1) {
  $this->dao->select('count(*) as i_count');
  $this->dao->from($this->getTable_blog_comment() . ' as c');

  $this->dao->where('c.fk_i_blog_id', $blog_id);

  if($status >= 0) {
    $this->dao->where('c.i_status', $status);
  }

  $result = $this->dao->get();
  
  if($result) {
    $comments = $result->row();
    return $comments['i_count'];
  }

  return 0;
}




// GET CATEGORIES
public function getCategories($author_id = -1) {
  if($author_id > 0) {
    $author = $this->countBlogComments($author_id);
  }

  $this->dao->select('DISTINCT c.*, cl.s_name, cl.s_description, cl.fk_c_locale_code');
  $this->dao->from($this->getTable_blog_category() . ' as c');
  $this->dao->join($this->getTable_blog_category_locale() . ' as cl', '(cl.fk_i_category_id = c.pk_i_id AND cl.fk_c_locale_code = "' . blg_get_locale() . '")', 'LEFT OUTER');

  if(isset($author['pk_i_id']) && trim((string)($author['s_category_id'] ?? '')) <> '') {
    $this->dao->where('c.pk_i_id in (' . $author['s_category_id'] . ')');
  }


  $this->dao->orderby('c.i_order ASC, c.pk_i_id DESC');

  $result = $this->dao->get();
  
  if($result) {
    $categories = $result->result();

    if(count($categories) > 0) {
      $i = 0;
      foreach($categories as $c) { 
        $categories[$i]['locales'] = $this->getCategoryLocales($c['pk_i_id']);
        $categories[$i]['i_blog_count'] = $this->countBlogs(-1, $c['pk_i_id']);
        $i++;
      }
    }

    return $categories;
  } else {

  }

  return array();
}


// GET AUTHOR CATEGORIES
public function getAuthorCategories($author_id) {
  return $this->getCategories($author_id);
}


// GET CATEGORY
public function getCategory($id) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_blog_category());

  $this->dao->where('pk_i_id', $id);

  $result = $this->dao->get();
  
  if($result) {
    $categories = $result->row();
    return $categories;
  }

  return array();
}


// GET COMMENT
public function getComment($id) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_blog_comment());

  $this->dao->where('pk_i_id', $id);

  $result = $this->dao->get();
  
  if($result) {
    $comment = $result->row();
    return $comment;
  }

  return array();
}


// GET COMMENTS
public function getComments($blog_id = -1, $enabled = -1, $type = '') {
  $this->dao->select('*');
  $this->dao->from($this->getTable_blog_comment());

  if($blog_id<> -1) {
    $this->dao->where('fk_i_blog_id', $blog_id);
  }

  if($enabled <> -1) {
    $this->dao->where('b_enabled', $enabled);
  }

  if($type == 'LATEST') {
    $this->dao->limit(8);
    $this->dao->orderby('pk_i_id DESC');
  }

  $result = $this->dao->get();
  
  if($result) {
    $comments = $result->result();

    if($type == 'LATEST' && count($comments) > 0) {
      $i = 0;

      foreach($comments as $c) {
        $comments[$i]['blog'] = $this->getBlogDetail($c['fk_i_blog_id']);
        $i++;
      }
    }

    return $comments;
  }

  return array();
}


// GET LATEST COMMENTS
public function getLatestComments($blog_id = -1, $enabled = -1, $type = '') {
  return $this->getComments(-1, 1, 'LATEST');
}



// GET CATEGORY WITH DETAILS
public function getCategoryDetail($category_id) {
  $this->dao->select('DISTINCT c.*, cl.s_name, cl.s_description, cl.fk_c_locale_code');
  $this->dao->from($this->getTable_blog_category() . ' as c');
  $this->dao->join($this->getTable_blog_category_locale() . ' as cl', '(cl.fk_i_category_id = c.pk_i_id AND cl.fk_c_locale_code = "' . blg_get_locale() . '")', 'LEFT OUTER');

  $this->dao->where('c.pk_i_id', $category_id);

  $result = $this->dao->get();
  
  if($result) {
    $category = $result->row();

    if(isset($category['pk_i_id'])) {
      $category['locales'] = $this->getCategoryLocales($category['pk_i_id']);
      $category['i_blog_count'] = $this->countBlogs(-1, $category['pk_i_id']);
    }

    return $category;
  }

  return array();
}


// GET USERS
public function getUsers($category_id = -1, $type = '') {
  $this->dao->select('u.*, d.s_name as s_os_name, d.s_email as s_os_email');
  $this->dao->from($this->getTable_blog_user() . ' as u');
  $this->dao->join($this->getTable_user() . ' as d', 'd.pk_i_id = u.fk_i_user_id', 'LEFT OUTER');

  if($category_id >= 0) {
    $this->dao->where('u.fk_i_category_id', $category_id);
  }

  $result = $this->dao->get();
  
  if($result) {
    $users = $result->result();

    if(count($users) > 0 && $type == 'AUTHOR') {
      $i = 0;
      foreach($users as $u) {
        $users[$i]['blog_count'] = $this->countBlogs(1, -1, $u['pk_i_id']);
        $i++;
      }
    }

    return $users;
  }

  return array();
}


// GET AUTHORS FOR FRONT
public function getAuthors($category_id = -1) {
  return $this->getUsers($category_id, 'AUTHOR');
}


// GET AUTHOR FOR FRONT
public function getAuthor($id) {
  return $this->getUser($id, 'AUTHOR');
}


// GET BLOG USER
public function getUser($id, $type = '') {
  $this->dao->select('u.*, d.s_name as s_os_name, d.s_email as s_os_email');
  $this->dao->from($this->getTable_blog_user() . ' as u');
  $this->dao->join($this->getTable_user() . ' as d', 'd.pk_i_id = u.fk_i_user_id', 'LEFT OUTER');

  $this->dao->where('u.pk_i_id', $id);

  $result = $this->dao->get();
  
  if($result) {
    $user = $result->row();

    if($type == 'AUTHOR') {
      $user['blog_count'] = $this->countBlogs(1, -1, $id);
    }

    return $user;
  }

  return array();
}


// GET BLOG USER BASED ON OSCLASS ID
public function getUserByOsclassId($osclass_id) {
  $this->dao->select('u.*, d.s_name as s_os_name, d.s_email as s_os_email');
  $this->dao->from($this->getTable_blog_user() . ' as u');
  $this->dao->join($this->getTable_user() . ' as d', 'd.pk_i_id = u.fk_i_user_id', 'LEFT OUTER');

  $this->dao->where('u.fk_i_user_id', $osclass_id);
  $this->dao->limit(1);

  $result = $this->dao->get();
  
  if($result) {
    $user = $result->row();
    return $user;
  }

  return false;
}





// REMOVE BLOG
public function removeBlog($id) {
  return $this->dao->delete($this->getTable_blog(), array('pk_i_id' => $id));
}


// REMOVE BLOG
public function removeCategory($id) {
  return $this->dao->delete($this->getTable_blog_category(), array('pk_i_id' => $id));
}

// REMOVE COMMENT
public function removeComment($id) {
  return $this->dao->delete($this->getTable_blog_comment(), array('pk_i_id' => $id));
}

// REMOVE BLOG COMMENT
public function removeBlogComment($id) {
  return $this->dao->delete($this->getTable_blog_comment(), array('pk_i_id' => $id));
}

// REMOVE BLOG USER
public function removeBlogUser($id) {
  return $this->dao->delete($this->getTable_blog_user(), array('pk_i_id' => $id));
}

// REMOVE USER
public function removeUser($id) {
  return $this->dao->delete($this->getTable_blog_user(), array('pk_i_id' => $id));
}

// INSERT NEW BLOG COMMENT
public function insertComment($values) {
  $this->dao->insert($this->getTable_blog_comment(), $values);
  return $this->dao->insertedId();
}


// INSERT NEW BLOG USER
public function insertUser($values) {
  $this->dao->insert($this->getTable_blog_user(), $values);
  return $this->dao->insertedId();
}


// UPDATE BLOG VIEWS
public function updateBlogViews($id) {
  if($id > 0) {
    return $this->dao->query('UPDATE '.$this->getTable_blog() . ' SET i_view=coalesce(i_view, 0)+1 WHERE pk_i_id='.$id);
  }
}


// UPDATE OLD IMAGE STRUCTURE TO NEW STRUCTURE (images in uploads folder)
public function updateBlogImageStructure() {
  return $this->dao->query('UPDATE '.$this->getTable_blog_locale() . ' SET s_description = REPLACE(s_description, "/oc-content/plugins/blog/img/", "/oc-content/uploads/blog/")');
}


// UPDATE BLOG USER
public function updateUser($values) {
  $this->dao->update($this->getTable_blog_user(), $values, array('pk_i_id' => $values['pk_i_id']));
}


// UPDATE BLOG IMAGE
public function updateBlogImage($id, $image) {
  $this->dao->update($this->getTable_blog(), array('s_image' => $image), array('pk_i_id' => $id));
}


// UPDATE BLOG COMMENT
public function updateComment($values) {
  $this->dao->update($this->getTable_blog_comment(), $values, array('pk_i_id' => $values['pk_i_id']));
}


// APPROVE BLOG COMMENT
public function approveComment($id) {
  $this->dao->update($this->getTable_blog_comment(), array('b_enabled' => 1), array('pk_i_id' => $id));
}



// INSERT NEW BLOG CATEGORY
public function insertCategory($values) {
  $this->dao->insert($this->getTable_blog_category(), $values);
  return $this->dao->insertedId();
}



// UPDATE CATEGORY POSITION
public function updateCategoryPosition($id, $order) {
  $this->dao->update($this->getTable_blog_category(), array('i_order' => $order), array('pk_i_id' => $id));
}


// UPDATE BLOG POSITION
public function updateBlogPosition($id, $order) {
  $this->dao->update($this->getTable_blog(), array('i_order' => $order), array('pk_i_id' => $id));
}


// UPDATE BLOG USER
public function updateBlogUser($data) {
  $this->dao->update($this->getTable_blog_user(), $data, array('pk_i_id' => $data['pk_i_id']));
}


// UPDATE BLOG COMMENT
public function updateBlogComment($data) {
  $this->dao->update($this->getTable_blog_comment(), $data, array('pk_i_id' => $data['pk_i_id']));
}


// UPDATE BLOG
public function updateBlog($data_blog, $data_locale) {
  if(!isset($data_blog['pk_i_id']) || $data_blog['pk_i_id'] <= 0) {
    if(isset($data_blog['pk_i_id'])) {
      unset($data_blog['pk_i_id']);
    }


    $this->dao->insert($this->getTable_blog(), $data_blog);
    $id = $this->dao->insertedId();

    Params::setParam('blogId', $id);
    $data_locale['fk_i_blog_id'] = $id;

    $this->updateBlogLocale($data_locale);

  } else {
    Params::setParam('blogId', $data_blog['pk_i_id']);

    $where = array('pk_i_id' => $data_blog['pk_i_id']);

    $this->dao->update($this->getTable_blog(), $data_blog, $where);

    $this->updateBlogLocale($data_locale);
  }
}


// UPDATE BLOG LOCALE
public function updateBlogLocale($data) {
  $where = array('fk_i_blog_id' => $data['fk_i_blog_id'], 'fk_c_locale_code' => $data['fk_c_locale_code']);

  $check = $this->getBlogLocale($data['fk_i_blog_id'], $data['fk_c_locale_code']);


  if(isset($check['pk_i_id']) && $check['pk_i_id'] > 0) {
    $this->dao->update($this->getTable_blog_locale(), $data, $where);
  } else {
    $this->dao->insert($this->getTable_blog_locale(), $data);
  }
}



// UPDATE CATEGORY 
public function updateCategory($data_cat, $data_locale) {
  if(!isset($data_cat['pk_i_id']) || $data_cat['pk_i_id'] <= 0) {
    if(isset($data_cat['pk_i_id'])) {
      unset($data_cat['pk_i_id']);
    }

    $this->dao->insert($this->getTable_blog_category(), $data_cat);
    $id = $this->dao->insertedId();

    Params::setParam('editId', $id);
    $data_locale['fk_i_category_id'] = $id;

    $this->updateCategoryLocale($data_locale);

  } else {
    Params::setParam('editId', $data_cat['pk_i_id']);

    $where = array('pk_i_id' => $data_cat['pk_i_id']);

    $this->dao->update($this->getTable_blog_category(), $data_cat, $where);
    $this->updateCategoryLocale($data_locale);
  }
}


// UPDATE CATEGORY LOCALE
public function updateCategoryLocale($data) {
  $where = array('fk_i_category_id' => $data['fk_i_category_id']);

  $check = $this->getCategoryLocale($data['fk_i_category_id'], $data['fk_c_locale_code']);
  if(@$check['pk_i_id'] > 0) {
    $this->dao->update($this->getTable_blog_category_locale(), $data, $where);
  } else {
    $this->dao->insert($this->getTable_blog_category_locale(), $data);
  }
}




// GET BLOG LOCALE
public function getBlogLocale($blog_id, $locale = '') {
  $this->dao->select('*');
  $this->dao->from($this->getTable_blog_locale());
  $this->dao->where('fk_i_blog_id', $blog_id);

  if($locale <> '') {
    $this->dao->where('fk_c_locale_code', $locale);
  }

  $result = $this->dao->get();
  
  if($result) {
    return $result->row();
  }

  return false;
}



// GET CATEGORY LOCALE
public function getCategoryLocale($category_id, $locale = '') {
  $this->dao->select('*');
  $this->dao->from($this->getTable_blog_category_locale());
  $this->dao->where('fk_i_category_id', $category_id);

  if($locale <> '') {
    $this->dao->where('fk_c_locale_code', $locale);
  }

  $this->dao->limit(1);

  $result = $this->dao->get();
  
  if($result) {
    return $result->row();
  }

  return false;
}



// GET BLOG LOCALES
public function getBlogLocales($blog_id) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_blog_locale());
  $this->dao->where('fk_i_blog_id', $blog_id);

  $result = $this->dao->get();
  $array = array();

  if($result) {
    $locales = $result->result();

    if(count($locales) > 0) {
      foreach($locales as $l) {
        $array[$l['fk_c_locale_code']] = array('title' => $l['s_title'], 'subtitle' => $l['s_subtitle'], 'description' => $l['s_description'], 'seo_title' => $l['s_seo_title'], 'seo_description' => $l['s_seo_description']);
      }
    }
  }

  return $array;
}


// GET CATEGORY LOCALES
public function getCategoryLocales($category_id) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_blog_category_locale());
  $this->dao->where('fk_i_category_id', $category_id);

  $result = $this->dao->get();
  $array = array();

  if($result) {
    $locales = $result->result();

    if(count($locales) > 0) {
      foreach($locales as $l) {
        $array[$l['fk_c_locale_code']] = array('name' => $l['s_name'], 'description' => $l['s_description']);
      }
    }
  }

  return $array;
}


}
?>