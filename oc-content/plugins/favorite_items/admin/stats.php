<?php
/**
 * Admin: Favorites Statistics dashboard
 */
if (!defined('ABS_PATH')) { exit('ABS_PATH is not loaded. Direct access is not allowed.'); }

require_once FAVORITE_ITEMS_PATH . 'ModelFavorites.php';

$model     = ModelFavorites::newInstance();
$global    = $model->globalStats();
$topItems  = $model->topItems(15);
$topUsers  = $model->topUsers(15);

$iconColor = osc_get_preference('icon_color', 'favorite_items') ?: '#e11d48';
?>
<link rel="stylesheet" href="<?php echo FAVORITE_ITEMS_URL; ?>assets/css/favorite.css?v=<?php echo FAVORITE_ITEMS_VERSION; ?>">

<div class="fi-admin" style="--fi-color: <?php echo osc_esc_html($iconColor); ?>;">
    <h1 class="fi-admin__title">Favorite Items — Statistics</h1>

    <section class="fi-stats-cards" data-testid="admin-stats-cards">
        <div class="fi-stats-card">
            <div class="fi-stats-card__value" data-testid="stat-total-favorites"><?php echo (int) ($global['total_favorites'] ?? 0); ?></div>
            <div class="fi-stats-card__label">Total favorites</div>
        </div>
        <div class="fi-stats-card">
            <div class="fi-stats-card__value" data-testid="stat-unique-users"><?php echo (int) ($global['unique_users'] ?? 0); ?></div>
            <div class="fi-stats-card__label">Users with favorites</div>
        </div>
        <div class="fi-stats-card">
            <div class="fi-stats-card__value" data-testid="stat-unique-items"><?php echo (int) ($global['unique_items'] ?? 0); ?></div>
            <div class="fi-stats-card__label">Items favorited</div>
        </div>
    </section>

    <section class="fi-stats-grid">
        <article class="fi-stats-panel">
            <h2 class="fi-stats-panel__title">Most-favorited listings</h2>
            <?php if (empty($topItems)): ?>
                <p class="fi-stats-panel__empty" data-testid="admin-top-items-empty">No favorites recorded yet.</p>
            <?php else: ?>
                <table class="fi-stats-table" data-testid="admin-top-items-table">
                    <thead>
                        <tr><th>#</th><th>Item</th><th style="text-align:right">Favorites</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topItems as $i => $row):
                            $link = osc_base_url() . 'oc-admin/index.php?page=items&action=item_edit&id=' . (int) $row['pk_i_id'];
                        ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td>
                                    <a href="<?php echo osc_esc_html($link); ?>" target="_blank">
                                        <?php echo osc_esc_html($row['s_title'] ?: 'Item #' . $row['pk_i_id']); ?>
                                    </a>
                                </td>
                                <td style="text-align:right"><strong><?php echo (int) $row['total']; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </article>

        <article class="fi-stats-panel">
            <h2 class="fi-stats-panel__title">Top users by favorites</h2>
            <?php if (empty($topUsers)): ?>
                <p class="fi-stats-panel__empty" data-testid="admin-top-users-empty">No user activity yet.</p>
            <?php else: ?>
                <table class="fi-stats-table" data-testid="admin-top-users-table">
                    <thead>
                        <tr><th>#</th><th>User</th><th>Email</th><th style="text-align:right">Favorites</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topUsers as $i => $row):
                            $link = osc_base_url() . 'oc-admin/index.php?page=users&action=edit&id=' . (int) $row['pk_i_id'];
                        ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><a href="<?php echo osc_esc_html($link); ?>" target="_blank"><?php echo osc_esc_html($row['s_name'] ?: 'User #' . $row['pk_i_id']); ?></a></td>
                                <td><?php echo osc_esc_html($row['s_email']); ?></td>
                                <td style="text-align:right"><strong><?php echo (int) $row['total']; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </article>
    </section>
</div>
