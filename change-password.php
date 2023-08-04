<?php
session_start();
include_once 'config.php';
include_once 'helpers/DbQueries.php';
if (!isLogin()) {
    header('Location: ' . '/login.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(isset($_GET['change_password_token']) && isset($_GET['id'])) {
        $fields = [];
        $fields['id'] = $_GET['id'];
        $fields['token'] = $_GET['change_password_token'];
        $fields['new_password'] = getNewPassword($fields['token'], $fields['id']);
        $fields['new_password'] = $fields['new_password'][0]['new_password'];
        $respond = setNewPasswordToDefault($fields);
        if($respond) {
            logout();
            header('Location: ' . '/login.php');
            exit();
        } else {
            //$success_message = $fs['Failed to change password'];
        }
    }
}
include_once('./inc/template/header.php');
include_once('./inc/template/personal-account-change-password.php');
include_once('./inc/template/footer.php');
?>