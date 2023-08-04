<?php
session_start();
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/FileCommander.php';
include_once('./inc/template/header.php');
//include_once('./inc/template/rate-search.php');
include_once('./inc/template/rate-slider.php');
include_once('./inc/template/rate-modal-complain.php');
include_once('./inc/template/footer.php');
?>