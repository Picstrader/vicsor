<?php
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/ECommerceLogic.php';
if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
    if (count($_POST) > 0) {
        $status = $_POST['status'];
        $token = $_POST['token'];
        $order_id = $_POST['order_id'];
        $invoice_id = $_POST['invoice_id'];
        testPaymentBad('post array good'. $status . ' ' . $order_id . ' ' . $invoice_id);
        testPaymentBad('post array good token '. $token);
    } else {
        testPaymentBad('post array null');
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        $status = $data['status'];
        $token = $data['token'];
        $order_id = $data['order_id'];
        $invoice_id = $data['invoice_id'];
    }
    if ($status === 'success') {
        $token_parts = explode('.', $token);
        $key_shop_parts = explode('.', API_KEY_SHOP);
        if ($token_parts[0] === $key_shop_parts[0]) {
            $fields = [];
            $fields['order_token'] = $order_id;
            $fields['invoice_id'] = $invoice_id;
            $order = getBalanceOrder($fields);
            if (count($order) > 0) {
                $order = $order[0];
                if(!(int)$order['status']) {
                    $fields = [];
                    $fields['user_id'] = $order['user_id'];
                    $user = getUserBalance($fields);
                    $user = $user[0];
                    $user['balance_old'] = $user['balance'];
                    $user['balance'] = (float) $user['balance'] + (float) $order['amount'];
                    $user['balance'] = round($user['balance'], 2);
                    $respond_purchase = changeUserBalance($user);
                    if ($respond_purchase) {
                        setStatusBalanceOrder($order['id']);
                        ECommerceLogic::addBalanceLog($user, 'balance', 1, 'Account top-up');
                    } else {
                        ECommerceLogic::addBalanceLog($user, 'balance', 0, 'Account top-up');
                    }
                } else {
                    testPaymentBad('already got ' . $order_id);
                }
            } else {
                testPaymentBad('no order id ' . $order_id);
            }
        } else {
            testPaymentBad('bad token' . $token);
        }
    } else {
        testPaymentBad('status not succses');
    }
} else {
    testPaymentBad('request not post type');
}
function testPaymentBad($mes)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $mes = mysqli_real_escape_string($db, $mes);
        $query = "INSERT INTO test(name) VALUES('$mes');";
        $db->query($query);
        return $db->insert_id;
    }
}
?>