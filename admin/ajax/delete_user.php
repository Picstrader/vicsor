<?php
session_start();
if (!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../../config.php';
include_once '../../helpers/DbQueries.php';
include_once '../../helpers/Validation.php';
include_once '../../helpers/ECommerceLogic.php';
include_once '../../helpers/setParsing.php';

$user_id = (int) $_POST['id'];
$res = deleteUser($user_id);
if($res) {
    echo 'user was deleted';
} else {
    http_response_code(400);
    echo 'user was not deleted';
}
