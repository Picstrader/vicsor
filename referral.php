<?php
session_start();
include_once 'config.php';
include_once 'helpers/DbQueries.php';
if (isLogin()) {
    header('Location: ' . '/index.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['referrer'])) {
        $referrer = getUserData($_GET['referrer']);
        if($referrer) {
            $_SESSION['referrer'] = $_GET['referrer'];
            header('Location: ' . '/registration.php');
        }
    }
    echo 'referrer not found';
}
 ?>