<?php
session_start();
include_once 'config.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/multilang.php';
include_once 'helpers/Validation.php';
include_once 'helpers/Statistics.php';
if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
    $action = $_POST['action'];
    switch ($action) {
        case 'update_gallery':
            $page = (int) $_POST['page'];
            $ajax = true;
            http_response_code(200);
            include './inc/template/main-gallery.php';
            break;
        case 'try_buy_image':
            $balance = getUserBalance(['user_id' => getLoginUserId()])[0];
            if (!$balance) {
                response(200, false, 'not_login');
                break;
            }
            $image = getImages(1, 1, 'gallery', null, $_POST['image_id'])[0];
            if (!$image) {
                response(200, false, 'no_image');
                break;
            }
            if ((float) $balance['balance'] < (float) $image['price']) {
                response(200, false, 'no_money');
                break;
            }
            $balance['balance_old'] = $balance['balance'];
            $balance['balance'] = round((float) $balance['balance'] - (float) $image['price'], 2);
            $res = changeUserBalance($balance);
            ECommerceLogic::addBalanceLog($balance, 'balance', $res ? 1 : 0, 'Photo purchased');
            if ($res) {
                ECommerceLogic::sendEmailDownloadImages($balance['email'], [$image]);
                updateImageSold($image['id'], 'sold');
                if ((int) $image['user_id']) {
                    $owner_balance = getUserBalance(['user_id' => $image['user_id']])[0];
                    $owner_balance['balance_old'] = $owner_balance['balance'];
                    $owner_balance['balance'] = round((float) $owner_balance['balance'] + (float) $image['price'] * 0.9, 2);
                    $owner_res = changeUserBalance($owner_balance);
                    ECommerceLogic::addBalanceLog($owner_balance, 'balance', $owner_res ? 1 : 0, 'Photo sold');
                } else {
                    $owners = explode(';', $image['owners']);
                    foreach ($owners as $owner) {
                        $owner_data = explode(':', $owner);
                        $owner_balance = getUserBalance(['user_id' => $owner_data[0]])[0];
                        $owner_balance['balance_old'] = $owner_balance['balance'];
                        $owner_balance['balance'] = round((float) $owner_balance['balance'] + ((float) $image['price'] / count($owners)) * 0.9, 2);
                        $res = changeUserBalance($owner_balance);
                        ECommerceLogic::addBalanceLog($owner_balance, 'balance', $res ? 1 : 0, 'Photo sold');
                    }
                }
            }
            response(200, $res ? true : false);
            break;
        case 'paypal':
            try {
                $order = json_decode($_POST['order']);
            } catch (\Throwable $th) {
                break;
            }
            $data = [];
            $data['image_id'] = $_POST['image_id'];
            $data['email'] = $_POST['email'];
            $data['type'] = $_POST['type'];
            $data['set_id'] = $_POST['set_id'];
            $data['user_id'] = getLoginUserId();
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