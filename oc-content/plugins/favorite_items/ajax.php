<?php
/**
 * AJAX endpoint for toggling / adding / removing favorites.
 *
 * URL:  /index.php?page=custom&route=favorite-items-toggle
 * POST: item_id (int), action (toggle|add|remove) — action optional, defaults to toggle
 */

if (!defined('ABS_PATH')) {
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

header('Content-Type: application/json; charset=utf-8');

$response = array('ok' => false);

if (!osc_is_web_user_logged_in()) {
    http_response_code(401);
    echo json_encode(array(
        'ok' => false,
        'error' => 'login_required',
        'message' => 'You must be logged in to favorite items.',
        'login_url' => osc_user_login_url(),
    ));
    return;
}

$userId = (int) osc_logged_user_id();
$itemId = isset($_POST['item_id']) ? (int) $_POST['item_id'] : (isset($_GET['item_id']) ? (int) $_GET['item_id'] : 0);
$action = isset($_POST['fav_action']) ? $_POST['fav_action'] : (isset($_GET['fav_action']) ? $_GET['fav_action'] : 'toggle');

if ($itemId <= 0) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'error' => 'invalid_item', 'message' => 'Invalid item id.'));
    return;
}

// Verify item exists & is enabled
$item = Item::newInstance()->findByPrimaryKey($itemId);
if (!$item) {
    http_response_code(404);
    echo json_encode(array('ok' => false, 'error' => 'item_not_found', 'message' => 'Item not found.'));
    return;
}

$model = ModelFavorites::newInstance();
$isFav = $model->isFavorite($userId, $itemId);

$didAdd = false;
$didRemove = false;

switch ($action) {
    case 'add':
        if (!$isFav) { $model->add($userId, $itemId); $didAdd = true; }
        break;
    case 'remove':
        if ($isFav) { $model->remove($userId, $itemId); $didRemove = true; }
        break;
    case 'toggle':
    default:
        if ($isFav) { $model->remove($userId, $itemId); $didRemove = true; }
        else        { $model->add($userId, $itemId);    $didAdd = true; }
        break;
}

$newState = $model->isFavorite($userId, $itemId);
$count    = $model->countByItem($itemId);
$userCount = $model->countByUser($userId);

echo json_encode(array(
    'ok'         => true,
    'item_id'    => $itemId,
    'favorited'  => (bool) $newState,
    'added'      => $didAdd,
    'removed'    => $didRemove,
    'item_count' => (int) $count,
    'user_count' => (int) $userCount,
));
