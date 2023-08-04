<?php
session_start();
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/FileCommander.php';
if (!isLogin()) {
    header('Location: ' . '/login.php');
}
include_once('./inc/template/header.php');
include_once('./inc/template/personal-account-purchased-images.php');
include_once('./inc/template/personal-account-purchased-images-modal.php');
include_once('./inc/template/personal-account-modal-delete-image-result.php');
include_once('./inc/template/personal-account-modal.php');
include_once('./inc/template/footer.php');
?>