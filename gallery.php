<?php
session_start();
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/FileCommander.php';
include_once('./inc/template/header.php');
include_once('./inc/template/gallery-search.php');
include_once('./inc/template/gallery-images.php');
include_once('./inc/template/gallery-modal.php');
include_once('./inc/template/gallery-modal-alert-buy-image.php');
include_once('./inc/template/gallery-modal-result.php');
include_once('./inc/template/gallery-modal-success-subscription.php');
include_once('./inc/template/footer.php');
?>