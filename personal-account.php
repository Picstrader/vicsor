<?php
session_start();
include_once 'config.php';
include_once 'helpers/DbQueries.php';
if (!isLogin()) {
    header('Location: ' . '/login.php');
}
include_once('./inc/template/header.php');
include_once('./inc/template/personal-account-add-image.php');
include_once('./inc/template/personal-account-images.php');
include_once('./inc/template/personal-account-modal-image.php');
include_once('./inc/template/personal-account-modal-delete-image-result.php');
include_once('./inc/template/personal-account-promotion.php');
include_once('./inc/template/modal-result.php');
include_once('./inc/template/footer.php');
?>