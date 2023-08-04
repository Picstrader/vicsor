<?php
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';
session_start();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $token = $_GET['token'];
    if ($token) {
        $res = verifyAccount($token);
        if ($res) {
            $user_data = getUserDataByToken($token);
            if (count($user_data) > 0) {
                $user_data = $user_data[0];
                $user_data['email'];
                ECommerceLogic::sendEmailSuccessConfirm($user_data['email']);
            }
            header('Location: ' . '/profile.php?success_confirm=1');
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $action = $_POST['action'];
    switch ($action) {
        case 'confirm':
            $token = $_POST['token'];
            if ($token) {
                $res = verifyAccount($token);
                if ($res) {
                    $user_data = getUserDataByToken($token);
                    if (count($user_data) > 0) {
                        $user_data = $user_data[0];
                        $message = $fs['You registered on'] . " https://picstrader.com . " . $fs['Your login is'] . " " . $user_data['email'];
                        mail($user_data['email'], 'Activated Account', $message, 'picstrader@gmail.com');
                    }
                    header('Location: ' . '/login.php');
                } else {
                    $message = $fs['Wrong token'];
                }
            }
            break;
        case 'new_token':
            $fields = [];
            $fields['token'] = bin2hex(openssl_random_pseudo_bytes(32));
            $fields['user_id'] = $_SESSION['verification_data']['id'];
            $fields['email'] = $_SESSION['verification_data']['email'];
            $res = updateUserToken($fields);
            if ($res) {
                $message = $fs['Copy this token'] . " " . $fields['token'] . " " . $fs['or follow the activation link'] . " https://picstrader.com/activate.php?token=" . $fields['token'];
                mail($fields['email'], 'Activate Account', $message, 'picstrader@gmail.com');
                $message = $fs['New token was generated'];
            } else {
                $message = $fs['Token was not generated'];
            }
            break;
    }
}
include_once('./inc/template/header.php');
?>
<section class="login__section">
    <div class="login-full__block-activate">
        <h1 class="login__header"><?= $fs['Confirm your email'] ?></h1>
        <div>
            <?= $message ?>
        </div>
        <form class="login__form" method="post">
            <div class="form-group">
                <label class="form-label__login" for="token"><?= $fs['Enter your token from email'] ?></label>
                <input type="token" id="login" class="form-control" name="token" required>
            </div>
            <input type="hidden" name="action" value="confirm">
            <input type="submit" value="Enter token" class="login__button">
        </form>
        <form class="login__form" method="POST">
            <input type="hidden" name="action" value="new_token">
            <input type="submit" value="Send new token" class="login__button">
        </form>
    </div>
</section>
<?php
include_once('./inc/template/footer.php');
?>