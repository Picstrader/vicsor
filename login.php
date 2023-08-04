<?php
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
require_once 'google/vendor/autoload.php';
session_start();
if (isLogin()) {
    header('Location: ' . '/profile.php');
}


$clientID = CLIENT_ID;
$clientSecret = CLIENT_SECRET;
$redirectUri = REDIRECT_URI;

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");


if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $respond = checkGoogleUserExists($google_account_info->email);
    if ($respond) {
        $user_data = [];
        $user_data['id'] = $respond[0]['id'];
        $user_data['firstname'] = $respond[0]['firstname'];
        $user_data['surname'] = $respond[0]['surname'];
        $user_data['nickname'] = $respond[0]['nickname'];
        $user_data['avatar'] = $respond[0]['avatar'];
        $user_data['balance'] = $respond[0]['balance'];
        $user_data['email'] = $respond[0]['email'];
        login($user_data);
        header('Location: ' . '/trade.php');
    } else {
        $_SESSION['is_google_aoth'] = true;
        $_SESSION['google_data'] = [
            'firstname' => '',
            'surname' => '',
            'nickname' => '',
            'email' => '',
            'password' => '',
            'phone' => '',
            'privacy_policy' => '',
            'full_age' => '',
            'avatar' => ''
        ];
        $_SESSION['google_data']['email'] = $google_account_info->email;
        $_SESSION['google_data']['firstname'] = $google_account_info->givenName;
        $_SESSION['google_data']['surname'] = $google_account_info->familyName;
        $_SESSION['google_data']['nickname'] = $google_account_info->givenName;
        if (isset($google_account_info->picture)) {
            $_SESSION['google_data']['avatar'] = $google_account_info->picture;
        } else {
            $_SESSION['google_data']['avatar'] = '';
        }
        header('Location: ' . '/registration.php');
    }

}

$_SESSION['error_type'] = false;
$_SESSION['fields'] = [
    'login' => '',
    'password' => ''
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'base_login':
            $fields = [
                'login' => $_POST['login'],
                'password' => $_POST['password'],
            ];
            $_SESSION['fields'] = $fields;
            $validation = Validation::validate_fields($fields);
            if (!$validation) {
                break;
            }
            $respond = checkLogIn($fields);
            if ($respond) {
                $user_data = [];
                $user_data['id'] = $respond[0]['id'];
                $user_data['firstname'] = $respond[0]['firstname'];
                $user_data['surname'] = $respond[0]['surname'];
                $user_data['nickname'] = $respond[0]['nickname'];
                $user_data['avatar'] = $respond[0]['avatar'];
                $user_data['balance'] = $respond[0]['balance'];
                $user_data['email'] = $respond[0]['email'];
                $user_data['login_token'] = bin2hex(openssl_random_pseudo_bytes(32));
                updateUserLoginToken($user_data['login_token'], $user_data['id']);
                login($user_data);
                header('Location: ' . '/trade.php');
            } else {
                $_SESSION['error_type'] = "login_error";
            }
            break;
    }
}

include_once('./inc/template/header.php');

$err_message = false;
$err_field = false;
if ($_SESSION['error_type']) {
    $err_field = Validation::$errors[$_SESSION['error_type']]['field'];
    $err_message = Validation::$errors[$_SESSION['error_type']]['message'];
    $err_message = $fs[$err_message];
}
?>
<section class="login__section">
    <div class="login-full__block">
        <h1 class="login__header">
            <?= $fs['Log in to your account'] ?>
        </h1>
        <form class="login__form" method="post">
            <div class="form-group">
                <label class="form-label__login" for="login">
                    <?= $fs['Email'] ?>
                </label>
                <input type="text" id="login" class="form-control <?= $err_field == 'login' ? 'is-invalid' : '' ?>"
                    name="login" value="<?= $_SESSION['fields']['login'] ?>" required>
                <div class="invalid-feedback">
                    <?= $err_field == 'login' ? $err_message : '' ?>
                </div>
            </div>

            <div class="form-group" data-password="">
                <label class="form-label__password" for="password">
                    <?= $fs['Password'] ?>
                </label>
                <div class="block-password-login">
                    <div class="<?= $err_field == 'current_password' ? 'is-invalid' : '' ?> passsword-input-block">
                        <div style="width: 100%;">
                            <input type="password" id="password" data-pass-target=""
                                class="form-control-password <?= $err_field == 'password' ? 'is-invalid' : '' ?>"
                                name="password" data-type="cur" required>
                        </div>
                        <div class="personal_account_change_password-show" data-type="cur" data-act="show"
                            id="pers_acc_password-show" onClick="pers_acc_pass_show_hide('cur', 'show')">
                        </div>
                        <div class="personal_account_change_password-hide" data-type="cur" data-act="hide"
                            id="pers_acc_password-hide" onClick="pers_acc_pass_show_hide('cur', 'hide')">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <input type="hidden" id="all" class="form-control <?= $err_field == 'all' ? 'is-invalid' : '' ?>"
                    name="all">
                <div class="invalid-feedback">
                    <?= $err_field == 'all' ? $err_message : '' ?>
                </div>
            </div>
            <div class="checkbox login-options">
                <label class="remember-me-label"><input type="checkbox" class="remember-me">
                    <?= $fs['Remember me'] ?>
                </label>
                <a href="/new_password.php" class="login-forgot">
                    <?= $fs['Forgot password'] ?>?
                </a>
            </div>
            <input type="hidden" name="action" value="base_login">
            <input type="submit" value="<?= $fs['Log in'] ?>" name="submit" class="login__button">
        </form>
        <div class="login__divide">
            <div class="hr__left">
                <hr style="width: 100%;">
            </div>
            <div class="hr__or">
                <?= $fs['or'] ?>
            </div>
            <div class="hr__right">
                <hr style="width: 100%;">
            </div>
        </div>
        <div class="button-login__google">
            <a class="login__google" href='<?= $client->createAuthUrl() ?>'><img src="/inc/assets/img/google1.svg"
                    class="google__logo"><?= $fs['Continue with Google'] ?></a>
        </div>
        <div class="block_login__new_acc">
            <a href='/registration.php' style="width: 100%;"><input type="button" class="login__new_acc"
                    value="<?= $fs['Create an account'] ?>"></input></a>
        </div>
    </div>
</section>

<?php
include_once('./inc/template/footer.php');
?>