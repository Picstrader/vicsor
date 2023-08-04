<?php
session_start();
if (!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../../config.php';
include_once '../../helpers/multilang.php';
include_once '../../helpers/DbQueries.php';
include_once '../../helpers/Validation.php';
include_once '../../helpers/ECommerceLogic.php';
include_once '../../helpers/setParsing.php';

$images = json_decode($_POST['images']);
$set_id = (int) $_POST['set'];
$set = getSetById($set_id)[0];
if(!isFullSet($set)) {
    http_response_code(400);
    echo 'set not full';
    exit();
}
if(!checkFakeWinParams($set, $images)) {
    http_response_code(400);
    echo 'images amount not = purchasable amount';
    exit();
}
$params_end = getLotParams()[0]['amount'];
$set['users_photos'] = makeFakeWin($set, $images, $params_end);
$res = likeImage(['set_id' => $set_id, 'users_photos' => $set['users_photos']]);
if($res) {
    gameFakeEnd($set);
    echo 'set was ended';
} else {
    http_response_code(400);
    echo 'set was not ended';
}
?>