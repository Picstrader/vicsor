<?php
session_start();
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';

include_once('./inc/template/header.php');
$message = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $action = $_POST['action'];
    switch ($action) {
        case 'generate':
            $user_id = checkUserExists($_POST['email'])[0]['id'];
            if (!$user_id) {
                $message = $fs['Email not registered'];
                break;
            }
            $password = bin2hex(openssl_random_pseudo_bytes(5));
            $respond = setUserPassword($user_id, $password);
            if ($respond) {
                $success = true;
                ECommerceLogic::sendNewPassword($password, $_POST['email']);
                $message = $fs['A new password has been sent to your email'];
            } else {
                $message = $fs['Failed to change password'];
            }
            break;
    }
}
?>
<section class="login__section">
    <div class="login-full__block-activate">
        <h1 class="login__header" style="<?= $success ? 'display:none;' : '' ?>">
            <?= $fs['Generate new password'] ?>
        </h1>
        <form class="login__form" method="post" style="<?= $success ? 'display:none;' : '' ?>">
            <div class="form-group">
                <label class="form-label__login" for="email">
                    <?= $fs['Enter your email'] ?>
                </label>
                <input type="text" id="login" class="form-control" name="email" required>
            </div>
            <input type="hidden" name="action" value="generate">
            <input type="submit" value="<?= $fs['Enter'] ?>" class="login__button">
        </form>
        <div class="login__header" style="margin:10px;">
            <?= $message ?>
        </div>
    </div>
</section>
<?php
include_once('./inc/template/footer.php');
?>