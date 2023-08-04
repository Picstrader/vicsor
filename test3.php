<?php
session_start();
include_once 'config.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/Validation.php';
include_once 'helpers/multilang.php';
include_once 'helpers/setParsing.php';
include_once 'helpers/PersonalAccountFunctions.php';
$image = ['name_original' => '16877119261814020130649870b6a0554.jpg', 'id' => '1339'];
ECommerceLogic::sendEmailDownloadImages('denisandwork@gmail.com', [$image]);
?>