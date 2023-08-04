<?php
session_start();
include_once 'config.php';
include_once 'helpers/DbQueries.php';
if (!isLogin()) {
    header('Location: ' . '/login.php');
}
include_once('./inc/template/header.php');
include_once('./inc/template/purchased-wallpapers-images.php');
include_once('./inc/template/purchased-wallpapers-modal-image.php');
include_once('./inc/template/personal-account-promotion.php');
// include_once('./inc/template/purchased-wallpapers-modal.php');
include_once('./inc/template/modal-result.php');
include_once('./inc/template/footer.php');
?>