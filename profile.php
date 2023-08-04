<?php
session_start();
include_once 'config.php';
include_once 'helpers/DbQueries.php';
if (!isLogin()) {
    header('Location: ' . '/login.php');
}
include_once('./inc/template/header.php');
include_once('./inc/template/personal-account-profile.php');
include_once('./inc/template/personal-account-profile-modal-confirm.php');
include_once('./inc/template/footer.php');
?>