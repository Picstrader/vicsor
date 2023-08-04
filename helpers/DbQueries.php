<?php
include 'AuthQueries.php';
include 'TradeQueries.php';
include 'AdminQueries.php';
include 'GalleryQueries.php';
include 'ComplainQueries.php';
include 'UserLogsQueries.php';
include 'PurchaseImageQueries.php';
include 'SubscriptionQueries.php';
include 'PreFavoriteQueries.php';
include 'StatisticsQueries.php';
include 'TelegramBotQueries.php';
include 'TransactionQueries.php';
include 'queries/users.php';
include 'queries/settings.php';
include 'queries/trade.php';
include 'queries/trade_actions.php';
include 'queries/images.php';
include 'queries/hashtags.php';
include 'queries/images-hashtags.php';

function isLogin()
{
    // $user_data_cookie = json_decode($_COOKIE['user_data'], true);
    if (! (int) $_SESSION['user_data']['id']) {
        $try_login = login_from_cookie();
        if(!$try_login) {
            return false;
        }
    }
    // if (!isset($user_data_cookie['id'])) {
    //     return false;
    // } else if (!$user_data_cookie['id']) {
    //     return false;
    // }

    return true;

}

function login_from_cookie()
{
    $user_data_cookie = json_decode($_COOKIE['user_data'], true);
    $login_data = checkLogInCookie($user_data_cookie['login_token']);
    if (count($login_data)) {
        $_SESSION['user_data'] = [];
        $_SESSION['user_data']['id'] = $login_data[0]['id'];
        $_SESSION['user_data']['firstname'] = $login_data[0]['firstname'];
        $_SESSION['user_data']['surname'] = $login_data[0]['surname'];
        $_SESSION['user_data']['nickname'] = $login_data[0]['nickname'];
        $_SESSION['user_data']['avatar'] = $login_data[0]['avatar'];
        $_SESSION['user_data']['balance'] = $login_data[0]['balance'];
        $_SESSION['user_data']['email'] = $login_data[0]['email'];
    } else {
        return false;
    }
    return true;
}
function login($user_data)
{
    $_SESSION['user_data'] = [];
    $_SESSION['user_data']['id'] = $user_data['id'];
    $_SESSION['user_data']['firstname'] = $user_data['firstname'];
    $_SESSION['user_data']['surname'] = $user_data['surname'];
    $_SESSION['user_data']['nickname'] = $user_data['nickname'];
    $_SESSION['user_data']['avatar'] = $user_data['avatar'];
    $_SESSION['user_data']['balance'] = $user_data['balance'];
    $_SESSION['user_data']['email'] = $user_data['email'];
    $_SESSION['user_data']['login_token'] = $user_data['login_token'];
    $user_data_json = json_encode($_SESSION['user_data']);
    setcookie("user_data", $user_data_json, time() + (7 * 24 * 60 * 60), "/");


}
function logout()
{
    $_SESSION['user_data'] = [];
    setcookie("user_data", "", time() - 3600, "/");

}
function getLoginUserId()
{
    return $_SESSION['user_data']['id'];
}

function getLoginUserEmail()
{
    return $_SESSION['user_data']['email'];
}

function setLoginUserEmail($email)
{
    $_SESSION['user_data']['email'] = $email;
}
function getLoginUserData()
{
    return $_SESSION['user_data'];
}

function getLoginUserAvatar()
{
    return $_SESSION['user_data']['avatar'];
}

function setLoginUserAvatar($avatar)
{
    $_SESSION['user_data']['avatar'] = $avatar;
}

function getLoginUserNickname()
{
    return $_SESSION['user_data']['nickname'];
}

function setLoginUserNickname($avatar)
{
    $_SESSION['user_data']['nickname'] = $avatar;
}

function setLoginUserBalance($balance)
{
    $_SESSION['user_data']['balance'] = $balance;
}

function getLoginUserBalance()
{
    return $_SESSION['user_data']['balance'];
}
?>