<?php
session_start();
include_once 'config.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/multilang.php';
include_once 'helpers/setParsing.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/Validation.php';

$payload = file_get_contents('php://input');
$event = json_decode($payload);

if ($event->event_type == 'PAYMENT.SALE.COMPLETED') {
    $purchase_units = $event->resource->payments[0]->sale->transaction->item_list->items[0]->unit_amount;
    $data = [];
    $data['currency'] = $purchase_units->currency;
    $data['paypal_fee'] = $purchase_units->breakdown->paypal_fee->value;
    $data['net_amount'] = $purchase_units->breakdown->net_amount->value;
    $data['transaction_id'] = $event->resource->id;
    $data['status'] = $event->resource->state;
    $data['payer_email'] = $event->resource->payer->email_address;
    $data['payer_first_name'] = $event->resource->payer->name->given_name;
    $data['payer_last_name'] = $event->resource->payer->name->surname;
    $data['payment_method'] = $event->resource->payer->payment_method;
    $data['create_time'] = $event->resource->create_time;
    $data['description'] = $event->resource->description;
    makePayment($data);
} elseif ($event->event_type == 'CHECKOUT.ORDER.APPROVED') {
    $purchase_units = $event->resource->purchase_units[0];
    $data = [];
    $data['currency'] = $purchase_units->payments->captures[0]->amount->currency_code;
    $data['paypal_fee'] = $purchase_units->payments->captures[0]->seller_receivable_breakdown->paypal_fee->value;
    $data['net_amount'] = $purchase_units->payments->captures[0]->seller_receivable_breakdown->net_amount->value;
    $data['transaction_id'] = $purchase_units->payments->captures[0]->id;
    $data['status'] = $purchase_units->payments->captures[0]->status;
    $data['payer_email'] = $event->resource->payer->email_address;
    $data['payer_first_name'] = $event->resource->payer->name->given_name;
    $data['payer_last_name'] = $event->resource->payer->name->surname;
    $data['payment_method'] = $event->resource->payer->payment_method;
    $data['create_time'] = $event->resource->create_time;
    $data['description'] = $purchase_units->description;
    makePayment($data);
} elseif ($event->event_type == 'PAYMENT.CAPTURE.COMPLETED') {
    $data = [];
    $data['currency'] = $event->resource->amount->currency_code;
    $data['paypal_fee'] = $event->resource->seller_receivable_breakdown->paypal_fee->value;
    $data['net_amount'] = $event->resource->seller_receivable_breakdown->net_amount->value;
    $data['transaction_id'] = $event->resource->id;
    $data['status'] = $event->resource->status;
    $data['description'] = $event->resource->description;
    $data['create_time'] = $event->resource->create_time;
    makePayment($data);
}
function makePayment($data)
{
    if ($data['status'] !== 'COMPLETED') {
        return;
    }
    if ($data['currency'] !== 'USD') {
        return;
    }
    $transaction = getTransactionPayPal($data['transaction_id'])[0];
    if (!$transaction) {
        return;
    }
    if ((int) $transaction['processed']) {
        return;
    }
    if ($transaction['type'] == 'buy_image') {
        $image = getImages(1, 1, 'gallery', null, $transaction['image_id'])[0];
        if ((float) $image['price'] > (float) $data['net_amount']) {
            return;
        }
        if ((int) $image['user_id']) {
            $owner_balance = getUserBalance(['user_id' => $image['user_id']])[0];
            $owner_balance['balance_old'] = $owner_balance['balance'];
            $owner_balance['balance'] = round((float) $owner_balance['balance'] + (float) $image['price'] * 0.9, 2);
            $res = changeUserBalance($owner_balance);
            ECommerceLogic::addBalanceLog($owner_balance, 'balance', $res ? 1 : 0, 'Photo sold');
            ECommerceLogic::sendEmailDownloadImages($transaction['email'], [$image]);
            updateImageSold($image['id'], 'sold');
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
            ECommerceLogic::sendEmailDownloadImages($transaction['email'], [$image]);
            updateImageSold($image['id'], 'sold');
        }
    } else if ($transaction['type'] == 'buy_slot') {
        $set = getSets(null, null, null, 1, 1, 'active', $transaction['user_id'], $transaction['set_id'])[0];
        if ((float) $set['cost'] > (float) $data['net_amount']) {
            return;
        }
        $image = getImages(1, 1, 'ready', $transaction['user_id'], $transaction['image_id'])[0];
        if (!$image) {
            return;
        }
        $user = getUserBalance(['user_id' => $transaction['user_id']])[0];
        if (!$user) {
            return;
        }
        $res = updateSet($transaction['user_id'], $image['id'], $set['users_photos'], $set['id']);
        if (!$res && $user) {
            $user['balance_old'] = $user['balance'];
            $user['balance'] = round((float) $user['balance'] + (float) $data['net_amount'], 2);
            $res = changeUserBalance($user);
            ECommerceLogic::addBalanceLog($user, 'balance', $res ? 1 : 0, 'buy slot');
        }
        if ($res) {
            if (usersInSet($set) + 1 >= $set['total_photos']) {
                createSet($set['cost'], $set['total_photos'], $set['pur_photos'], 3600);
            }
        }
    } else {
        $data['user_id'] = $data['description'] ? explode(" ", $data['description'])[2] : explode(" ", $transaction['description'])[2];
        $user = getUserBalance(['user_id' => $data['user_id']])[0];
        $user['balance_old'] = $user['balance'];
        $user['balance'] = (float) $user['balance'] + (float) $data['net_amount'];
        $user['balance'] = round($user['balance'], 2);
        $res = changeUserBalance($user);
        ECommerceLogic::addBalanceLog($user, 'balance', $res ? 1 : 0, 'Account top-up');
    }
    $data['id'] = $transaction['id'];
    $data['processed'] = 1;
    updateTransactionPayPal($data);


}