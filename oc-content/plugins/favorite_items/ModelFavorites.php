<?php
/**
 * ModelFavorites
 *
 * Data-access class for the favorite_items plugin.
 * Extends Osclass core DAO to reuse connection & query helpers.
 */

if (!defined('ABS_PATH')) {
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

class ModelFavorites extends DAO
{
    private static $instance;

    public static function newInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setTableName(DB_TABLE_PREFIX . 't_item_favorite');
        $this->setPrimaryKey('fk_i_item_id');
        $this->setFields(array('fk_i_item_id', 'fk_i_user_id', 'dt_added'));
    }

    public function getTableName()
    {
        return DB_TABLE_PREFIX . 't_item_favorite';
    }

    /**
     * Check if the given item is favorited by the user.
     */
    public function isFavorite($userId, $itemId)
    {
        $this->dao->select('1');
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_user_id', (int) $userId);
        $this->dao->where('fk_i_item_id', (int) $itemId);
        $this->dao->limit(1);
        $result = $this->dao->get();
        if ($result === false) return false;
        return $result->numRows() > 0;
    }

    /**
     * Add favorite; ignore duplicates.
     * Returns true if inserted or already existed.
     */
    public function add($userId, $itemId)
    {
        if ($this->isFavorite($userId, $itemId)) return true;
        $data = array(
            'fk_i_user_id' => (int) $userId,
            'fk_i_item_id' => (int) $itemId,
            'dt_added'     => date('Y-m-d H:i:s'),
        );
        return (bool) $this->dao->insert($this->getTableName(), $data);
    }

    /**
     * Remove favorite.
     */
    public function remove($userId, $itemId)
    {
        return (bool) $this->dao->delete($this->getTableName(), array(
            'fk_i_user_id' => (int) $userId,
            'fk_i_item_id' => (int) $itemId,
        ));
    }

    /**
     * Count favorites of an item.
     */
    public function countByItem($itemId)
    {
        $this->dao->select('COUNT(*) AS total');
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_item_id', (int) $itemId);
        $result = $this->dao->get();
        if ($result === false) return 0;
        $row = $result->row();
        return (int) $row['total'];
    }

    /**
     * Count favorites of a user.
     */
    public function countByUser($userId)
    {
        $this->dao->select('COUNT(*) AS total');
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_user_id', (int) $userId);
        $result = $this->dao->get();
        if ($result === false) return 0;
        $row = $result->row();
        return (int) $row['total'];
    }

    /**
     * Get all favorite items for a user with full item data + main image.
     */
    public function getUserFavorites($userId, $limit = 50, $offset = 0)
    {
        $prefix = DB_TABLE_PREFIX;
        $sql = "SELECT i.*, f.dt_added AS dt_favorited,
                       (SELECT COUNT(*) FROM {$prefix}t_item_favorite WHERE fk_i_item_id = i.pk_i_id) AS favorites_count
                FROM {$prefix}t_item_favorite f
                INNER JOIN {$prefix}t_item i ON i.pk_i_id = f.fk_i_item_id
                WHERE f.fk_i_user_id = " . (int) $userId . "
                  AND i.b_active = 1
                  AND i.b_enabled = 1
                ORDER BY f.dt_added DESC
                LIMIT " . (int) $offset . ", " . (int) $limit;
        $result = $this->dao->query($sql);
        if ($result === false) return array();
        return $result->result();
    }

    /**
     * Top favorited items — full data for public rendering
     * (joins item + descriptions in current locale, only active/enabled/non-expired items).
     */
    public function topFavoritedItemsFull($limit = 8)
    {
        $prefix = DB_TABLE_PREFIX;
        $locale = function_exists('osc_current_user_locale') ? osc_current_user_locale() : 'en_US';
        $locale = preg_replace('/[^a-zA-Z0-9_\-]/', '', $locale);

        $sql = "SELECT i.*,
                       COUNT(f.fk_i_item_id) AS favorites_count,
                       d.s_title  AS s_title,
                       d.s_description AS s_description,
                       loc.s_city AS s_city,
                       loc.s_region AS s_region
                FROM {$prefix}t_item_favorite f
                INNER JOIN {$prefix}t_item i ON i.pk_i_id = f.fk_i_item_id
                LEFT JOIN  {$prefix}t_item_description d
                       ON d.fk_i_item_id = i.pk_i_id AND d.fk_c_locale_code = '{$locale}'
                LEFT JOIN  {$prefix}t_item_location loc ON loc.fk_i_item_id = i.pk_i_id
                WHERE i.b_active  = 1
                  AND i.b_enabled = 1
                  AND (i.b_spam IS NULL OR i.b_spam = 0)
                  AND (i.dt_expiration IS NULL OR i.dt_expiration = '0000-00-00 00:00:00' OR i.dt_expiration >= NOW())
                GROUP BY i.pk_i_id
                ORDER BY favorites_count DESC, i.dt_pub_date DESC
                LIMIT " . (int) $limit;
        $result = $this->dao->query($sql);
        if ($result === false) return array();
        return $result->result();
    }

    /**
     * Top favorited items (for admin stats).
     */
    public function topItems($limit = 10)
    {
        $prefix = DB_TABLE_PREFIX;
        $sql = "SELECT i.pk_i_id, i.s_title, COUNT(f.fk_i_item_id) AS total
                FROM {$prefix}t_item_favorite f
                INNER JOIN {$prefix}t_item i ON i.pk_i_id = f.fk_i_item_id
                LEFT JOIN {$prefix}t_item_description d
                       ON d.fk_i_item_id = i.pk_i_id AND d.fk_c_locale_code = '" . osc_current_user_locale() . "'
                GROUP BY i.pk_i_id
                ORDER BY total DESC
                LIMIT " . (int) $limit;
        $result = $this->dao->query($sql);
        if ($result === false) return array();
        return $result->result();
    }

    /**
     * Top users by number of favorites (for admin stats).
     */
    public function topUsers($limit = 10)
    {
        $prefix = DB_TABLE_PREFIX;
        $sql = "SELECT u.pk_i_id, u.s_name, u.s_email, COUNT(f.fk_i_user_id) AS total
                FROM {$prefix}t_item_favorite f
                INNER JOIN {$prefix}t_user u ON u.pk_i_id = f.fk_i_user_id
                GROUP BY u.pk_i_id
                ORDER BY total DESC
                LIMIT " . (int) $limit;
        $result = $this->dao->query($sql);
        if ($result === false) return array();
        return $result->result();
    }

    /**
     * Global stats (totals).
     */
    public function globalStats()
    {
        $prefix = DB_TABLE_PREFIX;
        $sql = "SELECT
                    COUNT(*) AS total_favorites,
                    COUNT(DISTINCT fk_i_user_id) AS unique_users,
                    COUNT(DISTINCT fk_i_item_id) AS unique_items
                FROM {$prefix}t_item_favorite";
        $result = $this->dao->query($sql);
        if ($result === false) {
            return array('total_favorites' => 0, 'unique_users' => 0, 'unique_items' => 0);
        }
        return $result->row();
    }

    public function deleteByItem($itemId)
    {
        return $this->dao->delete($this->getTableName(), array('fk_i_item_id' => (int) $itemId));
    }

    public function deleteByUser($userId)
    {
        return $this->dao->delete($this->getTableName(), array('fk_i_user_id' => (int) $userId));
    }
}
