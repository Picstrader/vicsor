<?php
include_once 'config.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/Validation.php';
include_once 'helpers/ECommerceLogic.php';

$_SESSION['error_type'] = false;
$_SESSION['fields'] = [
    'current_password' => '',
    'new_password' => '',
    'confirm_password' => ''
];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(isset($_GET['change_password_token']) && isset($_GET['id'])) {
        $fields = [];
        $fields['id'] = $_GET['id'];
        $fields['token'] = $_GET['change_password_token'];
        $fields['new_password'] = getNewPassword($fields['token'], $fields['id']);
        $fields['new_password'] = $fields['new_password'][0]['new_password'];
        $respond = setNewPasswordToDefault($fields);
        if($respond) {
            logout();
            header('Location: ' . '/login.php');
            exit();
        } else {
            $success_message = $fs['Failed to change password'];
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'new_password':
            $fields = [
                'password' => $_POST['password']
            ];
            $_SESSION['fields'] = $fields;
            $validation = Validation::validate_fields($fields);
            if (!$validation) {
                break;
            }
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $_SESSION['error_type'] = "password_confirmation";
                break;
            }
            $user_data_arr = getUserData(getLoginUserId());
            $user_data = $user_data_arr[0];
            $fields['id'] = getLoginUserId();
            if($user_data['password'] == '') {
                $fields['token'] = bin2hex(openssl_random_pseudo_bytes(32));
                $respond = prepareNewPassword($fields);
                if($respond) {
                    ECommerceLogic::sendChangePassword($user_data['email'], $fields['token'], $user_data['id']);
                    $success_message = 'Confirm password change by email';
                }
                //setUserPassword($fields);
                //$success_message = $fs['The password has been changed'];
            } else if($user_data['password'] == md5($_POST['current_password'])) {
                $fields['token'] = bin2hex(openssl_random_pseudo_bytes(32));
                $respond = prepareNewPassword($fields);
                if($respond) {
                    ECommerceLogic::sendChangePassword($user_data['email'], $fields['token'], $user_data['id']);
                    $success_message = 'Confirm password change by email';
                }
                //setUserPassword($fields);
                //$success_message = $fs['The password has been changed'];
            } else {
                $_SESSION['error_type'] = "password_recognition";
                break;
            }
            break;
    }
}
$err_message = false;
$err_field = false;
if ($_SESSION['error_type']) {
    $err_field = Validation::$errors[$_SESSION['error_type']]['field'];
    $err_message = Validation::$errors[$_SESSION['error_type']]['message'];
}
?>
<section class="trade__heading_section">
    <div class="breadcrumbs">
        <div class="breadcrumbs-home" onClick="location.href='/'"></div>
        <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
        <div class="breadcrumbs-page"><?= $fs['Change password'] ?></div>
    </div>
</section>
<section class="profile-block">
<div class="profile-block-main">
    <div class="profile-block-inner">
        <form class="personal_account_change_password__form" method="POST">
            <div class="form-group">
                <p class="pers_acc__profile-data-other_items-title"><?= $fs['Current password'] ?></p>
                <div class="<?= $err_field == 'current_password' ? 'is-invalid' : '' ?> passsword-input-block">
                    <div>
                        <input type="password" id="current_password"
                        class="pers_acc__ch_pas_inp"
                        name="current_password" data-type="cur" data-act="">
                    </div>
                    <div class="personal_account_change_password-show" data-type="cur" data-act="show" id="pers_acc_password-show" onClick="pers_acc_pass_show_hide('cur', 'show')">
                    </div>
                    <div class="personal_account_change_password-hide" data-type="cur" data-act="hide" id="pers_acc_password-hide" onClick="pers_acc_pass_show_hide('cur', 'hide')">
                    </div>
                </div>
                <div class="invalid-feedback">
                    <?= $err_field == 'current_password' ? $fs[$err_message] : '' ?>
                </div>
            </div>
            <div class="form-group">
                <p class="pers_acc__profile-data-other_items-title"><?= $fs['New password'] ?></p>
                <div class="passsword-input-block <?= $err_field == 'password' ? 'is-invalid' : '' ?>">
                    <div>
                        <input data-type="new" data-act="" type="password" id="password"
                            class="pers_acc__ch_pas_inp" name="password"
                            required>
                    </div>
                    <div class="personal_account_change_password-show" data-type="new" data-act="show" id="pers_acc_password-show" onClick="pers_acc_pass_show_hide('new', 'show')">
                    </div>
                    <div class="personal_account_change_password-hide" data-type="new" data-act="hide" id="pers_acc_password-hide" onClick="pers_acc_pass_show_hide('new', 'hide')">
                    </div>
                </div>

                <div class="invalid-feedback">
                <?= $err_field == 'password' ? $fs[$err_message] : '' ?>
                </div>
  
            </div>
            <div class="form-group">
                <p class="pers_acc__profile-data-other_items-title"><?= $fs['Repeat new password'] ?></p>
                <div class="passsword-input-block <?= $err_field == 'confirm_password' ? 'is-invalid' : '' ?>">
                    <div>
                        <input data-type="new" data-act="" type="password" id="confirm_password"
                    class="pers_acc__ch_pas_inp"
                    name="confirm_password" required>
                    </div>
                </div>


                <div class="invalid-feedback">
                <?= $err_field == 'confirm_password' ? $fs[$err_message] : '' ?>
                </div>
            </div>
            <div class="personal_account_change_password_success">
                <?= isset($success_message) ? $success_message : '' ?>
            </div>
            <input type="hidden" name="action" value="new_password">
            <input type="hidden" name="tab" value="change-password">
            <div class="button__block">
                <input type="submit" value="<?= $fs['Change password'] ?>" name="submit" class="personal_account_change_password__form-submit">
            </div>
        </form>
    </div>
</div>
</section>