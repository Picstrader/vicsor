<?php
include_once 'config.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/Validation.php';
include_once 'helpers/ECommerceLogic.php';

$_SESSION['error_type'] = false;
$_SESSION['fields'] = [
];
$_SESSION['message'] = '';
function transferValidate($user, $recipient)
{
    if ((float) $user['balance'] < (float) $_POST['amount']) {
        $_SESSION['message'] = 'Not enough money';
        return false;
    }
    $recipient = getUserByEmail($_POST['email'])[0];
    if (!$recipient) {
        $_SESSION['message'] = "User does not exist";
        return false;
    }
    if((int) $recipient['id'] == (int) getLoginUserId()) {
        $_SESSION['message'] = "The sender cannot be the recipient";
        return false;
    }
    return true;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'transfer':
            if (!Validation::validate_fields($_POST)) {
                break;
            }
            $user = getUserBalance(['user_id' => getLoginUserId()])[0];
            $recipient = getUserByEmail($_POST['email'])[0];
            if (!transferValidate($user, $recipient)) {
                break;
            }
            $res = ECommerceLogic::balanceOperationDeduct($user, $_POST['amount']);
            if ($res) {
                $res = ECommerceLogic::balanceOperationAdd($recipient, (float) $_POST['amount']);
                if ($res) {
                    $_SESSION['message'] = 'Operation successful';
                }
                ECommerceLogic::updateBalance();
            } else {
                $_SESSION['message'] = 'Something went wrong, please try again later';
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
        <div class="breadcrumbs-page">
            <?= $fs['Transfer money'] ?>
        </div>
    </div>
</section>
<section class="profile-block">
    <div class="profile-block-main">
        <div class="profile-block-inner">
            <form class="personal_account_change_password__form" method="POST">
                <div class="form-group">
                    <p class="pers_acc__profile-data-other_items-title">
                        <?= $fs['Enter amount in'] . ' ' . $fs['main_currency'] ?>
                    </p>
                    <div class="<?= $err_field == 'amount' ? 'is-invalid' : '' ?> passsword-input-block">
                        <div>
                            <input type="number" id="amount" class="pers_acc__ch_pas_inp" name="amount" step="0.01"
                                required>
                        </div>
                    </div>
                    <div class="invalid-feedback">
                        <?= $err_field == 'amount' ? $fs[$err_message] : '' ?>
                    </div>
                </div>
                <div class="form-group">
                    <p class="pers_acc__profile-data-other_items-title">
                        <?= $fs['User email'] ?>
                    </p>
                    <div class="passsword-input-block <?= $err_field == 'email' ? 'is-invalid' : '' ?>">
                        <div>
                            <input type="email" id="email" class="pers_acc__ch_pas_inp" name="email" required>
                        </div>
                    </div>
                    <div class="invalid-feedback">
                        <?= $err_field == 'email' ? $fs[$err_message] : '' ?>
                    </div>
                </div>
                <div class="personal_account_change_password_success">
                    <?= isset($_SESSION['message']) ? $fs[$_SESSION['message']] : '' ?>
                </div>
                <input type="hidden" name="action" value="transfer">
                <div class="button__block">
                    <input type="submit" value="<?= $fs['Submit'] ?>" name="submit"
                        class="personal_account_change_password__form-submit">
                </div>
            </form>
        </div>
    </div>
</section>