<?php

function createTransaction($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $block_number =  mysqli_real_escape_string($db, $fields['blockNumber']);
        $from_wallet =  mysqli_real_escape_string($db, $fields['from']);
        $to_wallet =  mysqli_real_escape_string($db, $fields['to']);
        $transaction_hash =  mysqli_real_escape_string($db, $fields['hash']);
        $value = (float) $fields['value'];
        $token_name = mysqli_real_escape_string($db, $fields['tokeName']);
        $token_symbol = mysqli_real_escape_string($db, $fields['tokenSymbol']);
        $token_decimal = mysqli_real_escape_string($db, $fields['tokenDecimal']);
        $query = "INSERT INTO transactions(user_id, block_number, from_wallet, to_wallet, transaction_hash, value, token_name, token_symbol, token_decimal, status) VALUES($user_id, '$block_number', '$from_wallet', '$to_wallet', '$transaction_hash', $value, '$token_name', '$token_symbol', '$token_decimal', 1);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getTransaction($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $hash = mysqli_real_escape_string($db, $fields['hash']);
        $query = "SELECT id FROM transactions WHERE transaction_hash='$hash';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateTransaction($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $value = (float) $fields['value'];
        $token_name = mysqli_real_escape_string($db, $fields['token_name']);
        $token_symbol = mysqli_real_escape_string($db, $fields['token_symbol']);
        $token_decimal = mysqli_real_escape_string($db, $fields['token_decimal']);
        $query = "UPDATE transactions SET value=$value, token_name='$token_name', token_symbol='$token_symbol', token_decimal='$token_decimal', status=1 WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function createWithdrawDetails($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $wallet =  mysqli_real_escape_string($db, $fields['wallet']);
        $amount =  mysqli_real_escape_string($db, $fields['amount']);
        $query = "INSERT INTO withdraw_details(user_id, wallet, amount) VALUES($user_id, '$wallet', $amount);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getWithdrawDetails() {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM withdraw_details;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getWithdrawDetail($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['order_id'];
        $query = "SELECT * FROM withdraw_details WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setStatusWithdrawDetails($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['order_id'];
        $status = (int) $fields['status'];
        $query = "UPDATE withdraw_details SET status=$status WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function deleteStatusWithdrawDetails($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['order_id'];
        $query = "DELETE FROM withdraw_details WHERE id=$id";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function updateWithdrawLast($id) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "UPDATE users SET withdraw_last=UTC_TIMESTAMP() WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function createTransactionPayPal($data)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $currency = mysqli_real_escape_string($db, $data['currency']);
        $paypal_fee = (float) $data['paypal_fee'];
        $net_amount = (float) $data['net_amount'];
        $image_id = (int) $data['image_id'];
        $set_id = (int) $data['set_id'];
        $email = mysqli_real_escape_string($db, $data['email']);
        $type = mysqli_real_escape_string($db, $data['type']);
        $transaction_id = mysqli_real_escape_string($db, $data['transaction_id']);
        $id_internal = mysqli_real_escape_string($db, $data['id_internal']);
        $status = mysqli_real_escape_string($db, $data['status']);
        $payer_email = mysqli_real_escape_string($db, $data['payer_email']);
        $payer_first_name = mysqli_real_escape_string($db, $data['payer_first_name']);
        $payer_last_name = mysqli_real_escape_string($db, $data['payer_last_name']);
        $payment_method = mysqli_real_escape_string($db, $data['payment_method']);
        $create_time = mysqli_real_escape_string($db, $data['create_time']);
        $description = mysqli_real_escape_string($db, $data['description']);
        $user_id = (int) $data['user_id'];
        $processed = (int) $data['processed'];
        $query = "INSERT INTO transactions_paypal(currency, paypal_fee, net_amount, transaction_id, id_internal, status, payer_email, payer_first_name, payer_last_name, payment_method, create_time, description, user_id, processed, image_id, email, type, set_id) VALUES('$currency', $paypal_fee, $net_amount, '$transaction_id', '$id_internal', '$status', '$payer_email', '$payer_first_name', '$payer_last_name', '$payment_method', '$create_time', '$description', $user_id, $processed, $image_id, '$email', '$type', $set_id);";
        $db->query($query);
        return $db->insert_id;
    }
}

function updateTransactionPayPal($data)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $data['id'];
        $currency = mysqli_real_escape_string($db, $data['currency']);
        $paypal_fee = (float) $data['paypal_fee'];
        $net_amount = (float) $data['net_amount'];
        $transaction_id = mysqli_real_escape_string($db, $data['transaction_id']);
        $id_internal = mysqli_real_escape_string($db, $data['id_internal']);
        $status = mysqli_real_escape_string($db, $data['status']);
        $payer_email = mysqli_real_escape_string($db, $data['payer_email']);
        $payer_first_name = mysqli_real_escape_string($db, $data['payer_first_name']);
        $payer_last_name = mysqli_real_escape_string($db, $data['payer_last_name']);
        $payment_method = mysqli_real_escape_string($db, $data['payment_method']);
        $create_time = mysqli_real_escape_string($db, $data['create_time']);
        $description = mysqli_real_escape_string($db, $data['description']);
        $user_id = (int) $data['user_id'];
        $processed = (int) $data['processed'];
        $query = "UPDATE transactions_paypal SET currency='$currency', paypal_fee=$paypal_fee, net_amount=$net_amount, status='$status', payer_email='$payer_email', payer_first_name='$payer_first_name', payer_last_name='$payer_last_name', payment_method='$payment_method', create_time='$create_time', user_id=$user_id, processed=$processed WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getTransactionPayPal($transaction_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $transaction_id = mysqli_real_escape_string($db, $transaction_id);
        $query = "SELECT * FROM transactions_paypal WHERE transaction_id='$transaction_id';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
?>