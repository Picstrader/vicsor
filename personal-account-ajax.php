<?php
session_start();
include_once 'config.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/multilang.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/Validation.php';
require __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
include_once 'helpers/phoneVerification.php';
require_once 'stripe-php/init.php';

if (($_SERVER['REQUEST_METHOD'] === 'GET') && isLogin()) {
    $routes = explode('/', $_SERVER['REQUEST_URI']);
    $content_title = $routes[2];
    $content_path = './inc/template/personal-account-' . $content_title . '.php';
    if (file_exists($content_path)) {
        http_response_code(200);
        include $content_path;
    }
}

if (($_SERVER['REQUEST_METHOD'] === 'POST') && isLogin()) {
    $action = $_POST['action'];
    switch ($action) {
        case 'withdraw_funds':
            $fields = [];
            $fields['amount'] = filter_input(INPUT_POST, 'amount');
            if ((float) $fields['amount'] <= 0 || !Validation::check_out_of_range_number($fields['amount'])) {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = $fs['wrong input value'];
                $json = json_encode($answer);
                http_response_code(400);
                echo $json;
                break;
            }
            $fields['user_id'] = getLoginUserId();
            $balance = getUserBalance($fields);
            if (count($balance) <= 0) {
                break;
            }
            $balance = $balance[0];
            if ((float) $balance['balance'] < (float) $fields['amount']) {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = $fs['not enough money'];
                $json = json_encode($answer);
                http_response_code(400);
                echo $json;
                break;
            }
            $user_wallet = getUserWallet($fields);
            if (count($user_wallet) <= 0) {
                break;
            }
            $user_wallet = $user_wallet[0];
            if ($user_wallet['wallet'] == '') {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = $fs['the wallet is not specified'];
                $json = json_encode($answer);
                http_response_code(403);
                echo $json;
                break;
            }
            $balance['balance_old'] = $balance['balance'];
            $balance['balance'] = (float) $balance['balance'] - (float) $fields['amount'];
            $balance['balance'] = round($balance['balance'], 2);
            $balance['wallet'] = $user_wallet['wallet'];
            $respond_purchase = changeUserBalance($balance);
            if ($respond_purchase) {
                createWithdrawFundsOrder($fields);
                ECommerceLogic::addBalanceLog($balance, 'balance', 1, 'Withdraw funds');
                $answer = [];
                $answer['status'] = true;
                $answer['message'] = $fs['the request has been processed'];
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;

            } else {
                ECommerceLogic::addBalanceLog($balance, 'balance', 0, 'Withdraw funds');
            }
            break;
        case 'subscription':
            $sub = getSubParam()[0]['amount'];
            $balance = getUserBalance(['user_id' => getLoginUserId()])[0];
            if ((float) $balance['balance'] < (float) $sub) {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = $fs['not enough money'];
                $json = json_encode($answer);
                http_response_code(400);
                echo $json;
                break;
            }
            $balance['balance_old'] = $balance['balance'];
            $balance['balance'] = (float) $balance['balance'] - (float) $sub;
            $balance['balance'] = round($balance['balance'], 2);
            $balance['sub_amount'] = 30;
            $balance['sub_price'] = $sub;
            $respond_purchase = changeUserBalance($balance);
            if ($respond_purchase) {
                setUserSubscription(['user_id' => getLoginUserId(), 'subscription' => ECommerceLogic::getDateOfSubEnd(30)]);
                ECommerceLogic::addBalanceLog($balance, 'balance', 1, 'Subscription');
                $referrer_money = checkReferralProgram($balance);
                sendMoneyForSubscribeToCompanyAccount($sub, $referrer_money);
                //ECommerceLogic::calculateSubBenefit(getLoginUserId(), $sub);
                $answer = [];
                $answer['status'] = true;
                $answer['message'] = 'You have subscribed' . ': ' . 30 . ' ' . 'days';
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
            } else {
                ECommerceLogic::addBalanceLog($balance, 'balance', 0, 'Subscription');
            }
            break;
        case 'update_logs':
            $page = (int) $_POST['page'];
            http_response_code(200);
            include './inc/template/personal-account-my-activity.php';
            break;
        case 'update_balance_logs':
            $minutes = '';
            $day = '';
            $page = filter_input(INPUT_POST, 'page');
            if (!$page) {
                $page = 1;
            }
            if ($minutes) {
                $filters = [];
                $filters['period'] = ECommerceLogic::getTimePeriod($minutes);
            } else if ($day) {
                $filters = [];
                $filters['day'] = $day;
                $filters['day_next'] = ECommerceLogic::getNextDay($day);
            } else {
                unset($minutes);
                unset($day);
            }
            http_response_code(200);
            include './inc/template/personal-account-balance.php';
            break;
        case 'update_profile_images':
            $page = (int) $_POST['page'];
            http_response_code(200);
            include './inc/template/personal-account-images.php';
            break;
        case 'update_sub_amount':
            $sub_data = ECommerceLogic::getSubscriptionLeft();
            $sub_data['status'] = true;
            $json = json_encode($sub_data);
            http_response_code(200);
            echo $json;
            break;
        case 'send_confirm_email':
            $fields = [];
            $fields['token'] = bin2hex(openssl_random_pseudo_bytes(32));
            $fields['user_id'] = getLoginUserId();
            $fields['email'] = getLoginUserEmail();
            $res = updateUserToken($fields);
            if ($res) {
                ECommerceLogic::sendEmailConfirm($fields['email'], $fields['token']);
                $answer = [];
                $answer['status'] = true;
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
            } else {
                $answer = [];
                $answer['status'] = false;
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
            }
            break;
        case 'send_confirm_phone':
            $checkCode = getPhoneVerificationCode(getLoginUserId());
            if (!count($checkCode)) {
                $phone = filter_input(INPUT_POST, 'phone');
                $code = random_int(100000, 999999);
                $res = setPhoneVerificationCode(getLoginUserId(), $code);
                if ($res) {
                    $result = sendSMS($phone, $code);
                }
                if (!$res || !$result) {
                    $answer = [];
                    $answer['status'] = false;
                    $json = json_encode($answer);
                    http_response_code(200);
                    echo $json;
                    break;
                }
            }
            $answer = [];
            $answer['status'] = true;
            $json = json_encode($answer);
            http_response_code(200);
            echo $json;
            break;
        case 'confirm_phone_verification':
            $code = filter_input(INPUT_POST, 'code');
            $v_code = getPhoneVerificationCode(getLoginUserId())[0]['phone_verification_code'];
            if ($v_code == $code) {
                $fields = [];
                $fields['user_id'] = getLoginUserId();
                $respond = verifyPhone($fields);
                if ($respond) {
                    $answer = [];
                    $answer['status'] = true;
                    $json = json_encode($answer);
                    http_response_code(200);
                    echo $json;
                    break;
                } else {
                    $answer = [];
                    $answer['status'] = false;
                    $json = json_encode($answer);
                    http_response_code(200);
                    echo $json;
                    break;
                }
            } else {
                $answer = [];
                $answer['status'] = false;
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
                break;
            }
            break;
        case 'start_phone_confirmation':
            $user_data = getUserData(getLoginUserId());
            if (!(int) $user_data[0]['phone_verification']) {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = 'You need to confirm your phone';
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
                break;
            }
            if (!ECommerceLogic::getSubscriptionLeft()['amount']) {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = 'Subscription required to withdraw funds';
                $json = json_encode($answer);
                http_response_code(203);
                echo $json;
                break;
            }
            $phone = $user_data[0]['phone'];
            $amount = filter_input(INPUT_POST, 'amount');
            $wallet = filter_input(INPUT_POST, 'wallet');
            if ((float) $amount <= 0) {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = 'The amount must be greater than 0';
                $json = json_encode($answer);
                http_response_code(203);
                echo $json;
                break;
            }
            // if ((float) $amount < 50) {
            //     $answer = [];
            //     $answer['status'] = false;
            //     $answer['message'] = $fs['The amount must be at least'] . ' 50 ' . $fs['main_currency'];
            //     $json = json_encode($answer);
            //     http_response_code(200);
            //     echo $json;
            //     break;
            // }
            if (ECommerceLogic::checkWithdrawPeriod($user_data[0]['withdraw_last']) < 1) {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = $fs['Only one withdrawal per hour'];
                $json = json_encode($answer);
                http_response_code(203);
                echo $json;
                break;
            }
            $_SESSION['withdraw_code'] = random_int(100000, 999999);
            $result = sendSMS($phone, $_SESSION['withdraw_code']);
            if ($result) {
                $_SESSION['withdraw_amount'] = $amount;
                $_SESSION['withdraw_wallet'] = $wallet;
                $answer = [];
                $answer['status'] = true;
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
                break;
            } else {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = 'Message was not sent';
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
                break;
            }
            break;
        case 'finish_phone_confirmation':
            $code = filter_input(INPUT_POST, 'code');
            if (!isset($_SESSION['withdraw_code'])) {
                break;
            }
            if ($_SESSION['withdraw_code'] == $code) {

                $fields = [];
                $fields['amount'] = $_SESSION['withdraw_amount'];
                $fields['wallet'] = $_SESSION['withdraw_wallet'];
                if ((float) $fields['amount'] <= 0 || !Validation::check_out_of_range_number($fields['amount'])) {
                    $answer = [];
                    $answer['status'] = false;
                    $answer['message'] = $fs['wrong input value'];
                    $json = json_encode($answer);
                    http_response_code(203);
                    echo $json;
                    break;
                }
                $fields['user_id'] = getLoginUserId();
                $balance = getUserBalance($fields);
                if (count($balance) <= 0) {
                    break;
                }
                $balance = $balance[0];
                if ((float) $balance['balance'] < (float) $fields['amount']) {
                    $answer = [];
                    $answer['status'] = false;
                    $answer['message'] = $fs['not enough money'];
                    $json = json_encode($answer);
                    http_response_code(203);
                    echo $json;
                    break;
                }
                $balance['balance_old'] = $balance['balance'];
                $balance['balance'] = (float) $balance['balance'] - (float) $fields['amount'];
                $balance['balance'] = round($balance['balance'], 2);
                $balance['wallet'] = $user_wallet['wallet'];
                $respond_purchase = changeUserBalance($balance);
                if ($respond_purchase) {
                    //createWithdrawFundsOrder($fields);
                    createWithdrawDetails($fields);
                    ECommerceLogic::addBalanceLog($balance, 'balance', 1, 'Withdraw funds');
                    $answer = [];
                    $answer['status'] = true;
                    $answer['message'] = $fs['the request has been processed'];
                    $json = json_encode($answer);
                    http_response_code(200);
                    echo $json;

                } else {
                    ECommerceLogic::addBalanceLog($balance, 'balance', 0, 'Withdraw funds');
                }
                break;
            } else {
                $answer = [];
                $answer['status'] = false;
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
                break;
            }
            break;
        case 'open_user_image':
            $image_id = filter_input(INPUT_POST, 'image_id');
            http_response_code(200);
            include './inc/template/personal-account-modal-image.php';
            break;
        case 'open_purchased_user_image':
            $image_id = filter_input(INPUT_POST, 'image_id');
            http_response_code(200);
            include './inc/template/purchased-wallpapers-modal-image.php';
            break;
        case 'delete_gallery_image':
            $res = deleteImage($_POST['image_id'], getLoginUserId(), false);
            break;
        case 'paypal':
            try {
                $order = json_decode($_POST['order']);
            } catch (\Throwable $th) {
                break;
            }
            $data = [];
            $data['id_internal'] = $order->id;
            $data['transaction_id'] = $order->purchase_units[0]->payments->captures[0]->id;
            $data['description'] = $order->purchase_units[0]->description;
            $check_transaction = getTransactionPayPal($data['transaction_id']);
            if (count($check_transaction) > 0) {
                //maybe if webhook came first also
                break;
            }
            createTransactionPayPal($data);
            break;
        case 'set_price':
            if (!Validation::check_out_of_range_number($_POST['price'])) {
                http_response_code(400);
                break;
            }
            if ((float) $_POST['price'] < 0.1) {
                http_response_code(400);
                break;
            }
            $image = getImages(null, null, 'gallery', null, $_POST['image_id'])[0];
            $respond = updateImage($_POST['image_id'], getLoginUserId(), $_POST['price'], 'gallery', "", $image ? false : true);
            $answer = [];
            $answer['status'] = $respond ? true : false;
            $answer['price'] = $_POST['price'];
            $json = json_encode($answer);
            http_response_code(200);
            echo $json;
            break;
        case 'trade_image':
            $image = getImages(null, null, 'ready', getLoginUserId(), $_POST['image_id'])[0];
            if ($image) {
                $_SESSION['trade_image'] = $_POST['image_id'];
            }
            break;
        case 'download':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'];
                switch ($action) {
                    case 'download':
                        $image_id = FileCommander::downloadUserImage(getLoginUserId(), 'ready');
                        if ($image_id) {
                            $hashtags = json_decode($_POST['hashtags'], true);
                            foreach ($hashtags as $hashtag) {
                                $hashtag = prepareHashtag($hashtag);
                                if ($hashtag) {
                                    $hashtag_id = (int) getHashtagByName($hashtag)[0]['id'];
                                    if (!$hashtag_id) {
                                        $hashtag_id = addHashtag($hashtag);
                                    }
                                    addHashtagToImage($image_id, $hashtag_id);
                                }
                            }
                        }
                        break;
                }
            }
            break;
        case 'check_image':
            $image = getImages(1, 1, 'gallery', null, $_POST['image_id'])[0];
            $respond_data = [];
            $respond_data['status'] = $image ? true : false;
            $respond_data['price'] = $image['price'];
            $json = json_encode($respond_data);
            http_response_code($image ? 200 : 400);
            echo $json;
            break;
        case 'check_set':
            $set = getSets(null, null, null, 1, 1, 'active', getLoginUserId(), $_POST['set_id'])[0];
            $respond_data = [];
            $respond_data['status'] = $set ? true : false;
            $respond_data['price'] = $set['cost'];
            $json = json_encode($respond_data);
            http_response_code($set ? 200 : 400);
            echo $json;
            break;
        case 'set_purchased_price':
            $image = getPurchasedImage(getLoginUserId(), $_POST['image_id'])[0];
            if (!$image) {
                http_response_code(400);
                break;
            }
            $owners = explode(';', $image['owners']);
            foreach ($owners as $key => $owner) {
                $owner_data = explode(':', $owner);
                if ((int) $owner_data[0] == getLoginUserId()) {
                    $owner_data[1] = abs((float) $_POST['price']);
                    $owners[$key] = implode(':', $owner_data);
                    break;
                }
            }
            $new_price = 0;
            foreach ($owners as $owner) {
                $owner_data = explode(':', $owner);
                $new_price += (float) $owner_data[1];
            }
            $new_price = round($new_price / count($owners), 2);
            updatePurchasedImage($image['id'], $new_price, implode(';', $owners));
            break;
        case 'sell_purchased_image':
            $res = updatePurchasedImageStatus($_POST['image_id'], getLoginUserId(), 'gallery');
            if ($res) {
                http_response_code(200);
            } else {
                http_response_code(400);
            }
            break;
        case 'promotion':
            $options = getParam($_POST['type_promotion'])[0]['value'];
            if(!$options) {
                response(200, false, 'invalid_option');
                break;
            }
            foreach(explode(';', $options) as $option) {
                if((int) $_POST['promotion'] == (int) explode(':', $option)[0]) {
                    $promo_cost = (float) explode(':', $option)[1];
                }
            }
            if(!$promo_cost) {
                response(200, false. 'promo_not_found');
                break;
            }
            $balance = getUserBalance(['user_id' => getLoginUserId()])[0];
            if ((float) $balance['balance'] < $promo_cost) {
                response(200, false, 'no_money');
                break;
            }
            $balance['balance_old'] = $balance['balance'];
            $balance['balance'] = round((float) $balance['balance'] - $promo_cost, 2);
            $res = changeUserBalance($balance);
            ECommerceLogic::addBalanceLog($balance, 'balance', $res ? 1 : 0, $_POST['type_promotion']);
            if (!$res) {
                response(200, false, 'error');
                break;
            }
            if ($_POST['type_promotion'] == 'lift_up') {
                $image = getImageByActualPriority($_POST['image_id'])[0];
                updateImagePriority($_POST['image_id'], getLoginUserId(), 2, $_POST['promotion'], $image ? true : false);
            }
            if ($_POST['type_promotion'] == 'pin_to_top') {
                updateImagePinToTop($_POST['image_id'], getLoginUserId(), $_POST['promotion']);
            }
            response(200, true);
            break;
        case 'promotion_info':
            $options = getParam($_POST['type'])[0]['value'];
            if(!$options) {
                response(200, false, 'invalid_option');
                break;
            }
            $res = [];
            $res['status'] = true;
            $res['options'] = $options;
            $json = json_encode($res);
            http_response_code(200);
            echo $json;
            break;
    }
}

function prepareHashtag($hashtag)
{

    for ($i = 0; $i < strlen($hashtag); $i++) {
        if ($hashtag[$i] === '#') {
            continue;
        } else {
            return substr($hashtag, $i);
        }
    }
    return '';
}

function checkReferralProgram($referral)
{
    if ($referral['referrer']) {
        $referral_data = getReferralData();
        if (count($referral_data) > 0) {
            $referral_data = $referral_data[0];
        } else {
            return 0;
        }
        $fields = [];
        $fields['user_id'] = $referral['referrer'];
        $fields['referrer_money'] = ((float) $referral['sub_price']) * (((float) $referral_data['value']) / 100);
        $balance = getUserBalance($fields);
        if (count($balance) <= 0) {
            deleteUserReferrer($referral['id']);
            return 0;
        }
        $balance = $balance[0];
        $balance['balance_old'] = $balance['balance'];
        $balance['balance'] = (float) $balance['balance'] + (float) $fields['referrer_money'];
        $balance['balance'] = round($balance['balance'], 2);
        $respond_ref = changeUserBalance($balance);
        if ($respond_ref) {
            deleteUserReferrer($referral['id']);
            ECommerceLogic::addBalanceLog($balance, 'balance', 1, 'Referral program');
            return ((float) $fields['referrer_money']);
        } else {
            ECommerceLogic::addBalanceLog($balance, 'balance', 0, 'Referral program');
        }
    }
    return 0;
}

function sendMoneyForSubscribeToCompanyAccount($money, $referral_money)
{
    $balance = getUserBalance(['user_id' => COMPANY_ACCOUNT_ID])[0];
    $balance['balance_old'] = $balance['balance'];
    $balance['balance'] = (float) $balance['balance'] + (float) $money - (float) $referral_money;
    $balance['balance'] = round($balance['balance'], 2);
    $res = changeUserBalance($balance);
    if ($res) {
        ECommerceLogic::addBalanceLog($balance, 'balance', 1, 'User subscribed');
    } else {
        ECommerceLogic::addBalanceLog($balance, 'balance', 0, 'User subscribed');
    }
}

function response($code, $status = null, $error_type = null)
{
    $res = [];
    if (!is_null($status)) {
        $res['status'] = $status;
    }
    if (!is_null($error_type)) {
        $res['error_type'] = $error_type;
    }
    $json = json_encode($res);
    http_response_code($code);
    echo $json;
}