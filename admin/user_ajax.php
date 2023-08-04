<?php
session_start();
if (!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../helpers/FileCommander.php';
include_once '../helpers/DbQueries.php';
include_once '../helpers/Validation.php';
include_once '../helpers/ECommerceLogic.php';
if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
    $action = $_POST['action'];
    switch ($action) {
        case 'voice':
            $res = setConsiderVoice($_POST['user'], $_POST['voice']);
            if($res) {
                http_response_code(200);
            } else {
                http_response_code(400);
            }
            break;
        }
}