<?php
session_start();
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';
require __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
include_once 'helpers/phoneVerification.php';
if (isLogin()) {
    header('Location: ' . '/profile.php');
}

function addSubscriptionTimeAfterRegistration() {
    $fields = [];
    $fields['user_id'] = getLoginUserId();
    $fields['subscription'] = ECommerceLogic::getDateOfSubEndBonus();
    setUserSubscription($fields);
}

function balanceBonusForRegistration()
{
    $bonus = (float) getBonusReg()[0]['amount'];
    $balance = getUserBalance(['user_id' => COMPANY_ACCOUNT_ID])[0];
    if ($bonus && ((float) $balance['balance'] >= $bonus)) {
        $user = getUserBalance(['user_id' => getLoginUserId()])[0];
        $user['balance'] = (float) $user['balance'] + (float) $bonus;
        $res = changeUserBalance($user);
        if ($res) {
            ECommerceLogic::updateBalance();
            $balance['balance_old'] = $balance['balance'];
            $balance['balance'] = (float) $balance['balance'] - $bonus;
            $balance['balance'] = round($balance['balance'], 2);
            $res = changeUserBalance($balance);
            if ($res) {
                ECommerceLogic::addBalanceLog($balance, 'balance', 1, 'Bonus for registration');
            } else {
                ECommerceLogic::addBalanceLog($balance, 'balance', 0, 'Bonus for registration');
            }
        }
    }
}

function demoBalance() {
    $demo_sum = getDemoSum()[0]['amount'];
    $res = updateUserDemoBalance(getLoginUserId(), $demo_sum);
    if ($res) {
        ECommerceLogic::addBalanceLog(['id' => getLoginUserId(), 'balance_old' => 0, 'balance' => $demo_sum], 'demo_balance', 1, "demo balance");
    } else {
        ECommerceLogic::addBalanceLog(['id' => getLoginUserId(), 'balance_old' => 0, 'balance' => $demo_sum], 'demo_balance', 0, "demo balance");
    }
}

$_SESSION['error_type'] = false;
$_SESSION['fields'] = [
    'nickname' => '',
    'email' => '',
    'password' => '',
    'phone' => '',
];
if (!isset($_SESSION['is_google_aoth'])) {
    $_SESSION['is_google_aoth'] = false;
}

if (!isset($_SESSION['google_data'])) {
    $_SESSION['google_data'] = false;
}

if ($_SESSION['google_data'] && $_SESSION['is_google_aoth']) {
    $_SESSION['fields'] = $_SESSION['google_data'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'registration':
            $fields = [
                'nickname' => $_POST['nickname'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'phone' => $_POST['phone'],
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
            $is_unique_fields = checkUniqueFields($fields);
            if (!$is_unique_fields) {
                break;
            }
            $fields['token'] = bin2hex(openssl_random_pseudo_bytes(32));
            $fields['login_token'] = bin2hex(openssl_random_pseudo_bytes(32));
            if(isset($_SESSION['referrer'])) {
                $fields['referrer'] = $_SESSION['referrer'];
            } else {
                $fields['referrer'] = '';
            }
            $fields['country'] = $_SERVER['HTTP_GEOIP_COUNTRY_CODE'];
            $fields['phone_verification_code'] = random_int(100000, 999999);
            $respond = registerUser($fields);
            if ($respond) {
                ECommerceLogic::sendEmailConfirm($fields['email'], $fields['token']);
                sendSMS($fields['phone'], $fields['phone_verification_code']);
                $fields['id'] = $respond;
                $fields['avatar'] = '';
                $balance = getUserBalanceAfterReg($respond);
                $balance = $balance[0]['balance'];
                $fields['balance'] = $balance;
                login($fields);
                //addSubscriptionTimeAfterRegistration();
                //balanceBonusForRegistration();
                //demoBalance();
                header('Location: ' . '/trade.php');
            } else {
                $_SESSION['error_type'] = "db_error";
            }
            break;
        case 'google_aoth':
            if (!isset($_SESSION['google_data']) || !isset($_SESSION['google_data']['email'])) {
                $_SESSION['is_google_aoth'] = false;
                $_SESSION['google_data'] = false;
                $_SESSION['error_type'] = "email";
                break;
            } else if (!$_SESSION['google_data']) {
                $_SESSION['is_google_aoth'] = false;
                $_SESSION['google_data'] = false;
                $_SESSION['error_type'] = "email";
                break;
            }
            $fields = [
                'nickname' => $_POST['nickname'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
            ];
            $_SESSION['fields'] = $fields;
            if ($fields['email'] != $_SESSION['google_data']['email']) {
                $_SESSION['is_google_aoth'] = false;
                $_SESSION['google_data'] = false;
                $_SESSION['error_type'] = "email";
                break;
            }
            $_SESSION['is_google_aoth'] = true;
            $validation = Validation::validate_fields($fields);
            if (!$validation) {
                break;
            }
            $fields['email'] = $_SESSION['google_data']['email'];
            $fields['avatar'] = $_SESSION['google_data']['avatar'];
            $is_unique_fields = checkUniqueFields($fields);
            if (!$is_unique_fields) {
                break;
            }
            $fields['login_token'] = bin2hex(openssl_random_pseudo_bytes(32));
            if(isset($_SESSION['referrer'])) {
                $fields['referrer'] = $_SESSION['referrer'];
            } else {
                $fields['referrer'] = '';
            }
            $fields['country'] = $_SERVER['HTTP_GEOIP_COUNTRY_CODE'];
            $fields['phone_verification_code'] = random_int(100000, 999999);
            $respond = registerGoogleUser($fields);
            if ($respond) {
                $_SESSION['is_google_aoth'] = false;
                $_SESSION['google_data'] = false;
                $fields['id'] = $respond;
                $balance = getUserBalanceAfterReg($respond);
                $balance = $balance[0]['balance'];
                $fields['balance'] = $balance;
                login($fields);
                //addSubscriptionTimeAfterRegistration();
                //balanceBonusForRegistration();
                //demoBalance();
                header('Location: ' . '/trade.php');
            } else {
                $_SESSION['is_google_aoth'] = false;
                $_SESSION['google_data'] = false;
                $_SESSION['error_type'] = "db_error";
            }
            break;
    }
}
$err_message = false;
$err_field = false;
$is_google_aoth = $_SESSION['is_google_aoth'];
$_SESSION['is_google_aoth'] = false;
include_once('./inc/template/header.php');
if ($_SESSION['error_type']) {
    $err_field = $_SESSION['is_google_aoth'] ? 'nickname' : Validation::$errors[$_SESSION['error_type']]['field'];
    $err_message = Validation::$errors[$_SESSION['error_type']]['message'];
    $err_message = $fs[$err_message];
}
?>
<section class="registration__section">
    <div class="registration-full__block">
        <h1 class="registration__header">
            <?= $fs['registration'] ?>
        </h1>
        <form class="registration__form" method="post">
            <div class="form-group-registration-nickname">
                <label class="form-label-registration" for="nickname">
                    <?= $fs['Nickname'] ?><span class="span__ruquired">*</span>
                </label>
                <input type="text" id="nickname"
                    class="form-control-registration-name <?= $err_field == 'nickname' ? 'is-invalid' : '' ?>"
                    name="nickname" value="<?= $_SESSION['fields']['nickname'] ?>" required>
                <div class="invalid-feedback">
                    <?= $err_field == 'nickname' ? $err_message : '' ?>
                </div>
            </div>
            <div class="form-group-registration-email" style="<?= $is_google_aoth ? 'display:none;' : '' ?>">
                <label class="form-label-registration" for="email">
                    <?= $fs['Email'] ?><span class="span__ruquired">*</span>
                </label>
                <input type="email" id="email" class="form-control <?= $err_field == 'email' ? 'is-invalid' : '' ?>"
                    name="email" value="<?= $_SESSION['fields']['email'] ?>" <?=
                          $is_google_aoth ? '' : 'required' ?>>
                <div class="invalid-feedback">
                    <?= $err_field == 'email' ? $err_message : '' ?>
                </div>
            </div>
            <div class="form-group-registration-phone">
                <label class="form-label-registration" for="phone"><?= $fs['Phone number'] ?><span
                        class="span__ruquired">*</span></label>
                <input type="tel" id="phone"
                    class="tel form-control-registration-phone <?= $err_field == 'phone' ? 'is-invalid' : '' ?>"
                    name="phone" value="" inputmode='tel' required>
                <div class="invalid-feedback">
                    <?= $err_field == 'phone' ? $err_message : '' ?>
                </div>
            </div>
            <div class="form-group-registration-password" style="<?= $is_google_aoth ? 'display:none;' : '' ?>">
                <label class="form-label-registration" for="password">
                    <?= $fs['Password'] ?><span class="span__ruquired">*</span>
                </label>
                <div class="form-control registration-password-block <?= $err_field == 'password' ? 'is-invalid' : '' ?>">
                    <div class="registration-password-input">
                        <input type="password" id="password"
                            class="password-registration"
                            name="password" <?= $is_google_aoth ? 'disabled' : 'required' ?>>
                    </div>
                    <div class="show-pass" id="showPass">
                    </div>
                </div>
                <?php if ($err_field != 'password') { ?>
                    <small id="passwordHelpInline" class="text-muted">
                        <?= $fs['Password must be 6 or more symbols.'] ?>
                    </small>
                <?php } ?>
                <div class="invalid-feedback">
                    <?= $err_field == 'password' ? $err_message : '' ?>
                </div>
            </div>
            <div class="form-group-registration-password" style="<?= $is_google_aoth ? 'display:none;' : '' ?>">
                <label class="form-label-registration" for="confirm_password">
                    <?= $fs['Confirm Password'] ?><span class="span__ruquired">*</span>
                </label>
                <div class="form-control registration-password-block <?= $err_field == 'confirm_password' ? 'is-invalid' : '' ?>">
                    <div class="registration-password-input">
                        <input type="password" id="confirm_password"
                            class="password-registration"
                            name="confirm_password" <?= $is_google_aoth ? 'disabled' : 'required' ?>>
                    </div>
                    <div class="show-pass" id="showPassConfirm">
                    </div>
                </div>
                <div class="invalid-feedback">
                    <?= $err_field == 'confirm_password' ? $err_message : '' ?>
                </div>
            </div>
            <div>
            <div class="invalid-feedback">
                    <?= $is_google_aoth ? ($err_field == 'email' ? $err_message : '') : '' ?>
                </div>
            </div>
            <div class="form-group__policy">
                <input type="checkbox" name="privacy_policy" onchange="checkRegTerms()"
                    class="input-privacy_policy<?= $err_field == 'privacy_policy' ? 'is-invalid' : '' ?>"
                    id="privacy-policy" value="1">
                <label class="privacy-form-label-registration" for="privacy_policy">
                    <?= $fs['I accept'] ?> <a href="/inc/documents/terms_of_use.pdf" target="_blank">
                        <?= $fs['Terms and Conditions'] ?>,
                    </a>
                    <a href="/inc/documents/privacy_policy.pdf" target="_blank">
                        <?= $fs['Privacy Policy'] ?></a>
                </label>
                <div class="invalid-feedback">
                </div>
            </div>
            <div class="form-group__full-age">
                <input type="checkbox" onchange="checkRegTerms()" name="full_age" id="full-age"
                    class="full_age<?= $err_field == 'full_age' ? 'is-invalid' : '' ?>" value="1">
                <label class="full-age-form-label-registration" for="full_age">
                    <?= $fs['I am 18 years old and over'] ?>
                </label>
                <div class="invalid-feedback">
                </div>
            </div>
            <input type="hidden" id="full-phone" name="phone" value="">
            <input type="hidden" name="action" value="<?= $is_google_aoth ? 'google_aoth' : 'registration' ?>">
            <input type="submit" value="<?= $fs['registration'] ?>" name="submit" id="registration-confirm"
                class="registration__button" disabled>
        </form>
        <div class="" style="margin-top:10px;">
                <a href="/new_password.php" class="login-forgot"><?= $fs['Forgot password'] ?>?</a>
        </div>
    </div>
</section>

<?php
include_once('./inc/template/footer.php');
?>