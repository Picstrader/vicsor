<?php
session_start();
include_once 'config.php';
include_once 'helpers/DbQueries.php';
if (!isLogin()) {
    header('Location: ' . '/login.php');
}
include_once('./inc/template/header.php');
include_once('./inc/template/personal-account-balance.php');
include_once('./inc/template/personal-account-modal-subscribe-respond.php');
include_once('./inc/template/personal-account-modal-withdraw.php');
include_once('./inc/template/personal-account-modal-topup.php');
include_once('./inc/template/personal-account-modal-topup-result.php');
include_once('./inc/template/personal-account-modal-withdraw-action.php');
include_once('./inc/template/personal-account-modal-withdraw-phone.php');
include_once('./inc/template/personal-account-modal.php');
include_once('./inc/template/personal-account-modal-subscribe.php');
include_once('./inc/template/footer.php');
?>