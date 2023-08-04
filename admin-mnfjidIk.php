<?php
session_start();
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'login' => $_POST['login'],
        'password' => $_POST['password'],
    ];
    $validation = true;
    if(!Validation::check_out_of_range_string($_POST['login'])) {
        $validation = false;
    }
    if(!Validation::check_out_of_range_string($_POST['password'])) {
        $validation = false;
    }
    if ($validation) {
        $respond = checkAdmin($fields);
        if ($respond) {
            $_SESSION['admin'] = $respond[0]['id'];
            header('Location: ' . '/admin/admin.php');
        }
    }
}
?>

<form method="POST">
    <label for="login">Login:</label>
    <input type="text" id="login" name="login">

    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password">

    <input type="submit" value="submit">
</form>