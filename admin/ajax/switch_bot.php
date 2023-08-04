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

$res = setBotFakeStatus((int) $_POST['status']);
if($res) {
    http_response_code(200);
} else {
    http_response_code(400);
}
?>