<?php
session_start();
if (!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../helpers/FileCommander.php';
include_once '../helpers/DbQueries.php';
include_once '../helpers/Validation.php';
include_once '../helpers/ECommerceLogic.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'confirm':
            $fields = [];
            $fields['order_id'] = (int) $_POST['order_id'];
            $fields['status'] = 1;
            setStatusWithdrawDetails($fields);
            break;
        case 'cancel':
            $fields = [];
            $fields['order_id'] = (int) $_POST['order_id'];
            $order = getWithdrawDetail($fields);
            if (count($order) <= 0) {
                break;
            }
            $order = $order[0];


            $fields['amount'] = $order['amount'];
            if ((float) $fields['amount'] <= 0 || !Validation::check_out_of_range_number($fields['amount'])) {
                break;
            }
            $fields['user_id'] = $order['user_id'];
            $balance = getUserBalance($fields);
            if (count($balance) <= 0) {
                break;
            }
            $balance = $balance[0];
            $balance['balance_old'] = $balance['balance'];
            $balance['balance'] = (float) $balance['balance'] + (float) $fields['amount'];
            $balance['balance'] = round($balance['balance'], 2);
            $balance['wallet'] = $user_wallet['wallet'];
            $respond_purchase = changeUserBalance($balance);
            if ($respond_purchase) {
                deleteStatusWithdrawDetails($fields);
                ECommerceLogic::addBalanceLog($balance, 'balance', 1, 'Withdraw funds, refund');

            }
            break;
    }
}
$orders = /*getWithdrawFundsOrders()*/getWithdrawDetails();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PicsTrader</title>
    <link rel="stylesheet" href="../inc/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../inc/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../inc/assets/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="../inc/assets/css/core.min.css">
    <link rel="stylesheet" href="../inc/assets/css/fontawesome.min.css">
    <style>
        .withdraw-moderating-table {
            border: none;
            width: 100%;
            max-width: 100%;
        }

        .confirm-button {
            width: 180px;
            height: 40px;
            background: #007AFF;
            border-radius: 5px;
            font-weight: 700;
            font-size: 16px;
            line-height: 111.02%;
            text-align: center;
            letter-spacing: 0.025em;
            color: #FFFFFF;
            cursor: pointer;
        }

        .cancel-button {
            width: 180px;
            height: 40px;
            background: red;
            border-radius: 5px;
            font-weight: 700;
            font-size: 16px;
            line-height: 111.02%;
            text-align: center;
            letter-spacing: 0.025em;
            color: #FFFFFF;
            cursor: pointer;
        }

        span {
            color: green;
        }

        tr {}

        td {
            text-align: center;
            padding: 5px;
            margin: 5px;
        }
    </style>
</head>

<body>
    <div id="wrapper" class="wrapper">
        <div class="m-1 p-1">
            <div class="justify-content-center m-1 p-1">
                <table class="withdraw-moderating-table">
                    <thead>
                        <tr>
                            <th>Withdraw amount</th>
                            <th>User wallet</th>
                            <th>User</th>
                            <th>Date/time</th>
                            <th>Confirm</th>
                            <th>Cancel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) {
                            $user_data = getUserData($order['user_id']);
                            $user_data = $user_data[0];
                            ?>
                            <tr>
                                <td>
                                    <?= $order['amount'] ?>
                                </td>
                                <td>
                                    <?= $order['wallet'] ?>
                                </td>
                                <td>
                                    <?= $user_data['email'] ?>
                                </td>
                                <td>
                                    <?= $order['cur_date'] ?>
                                </td>
                                <td>
                                    <?php if ((bool) $order['status']) { ?>
                                        <span>Confirmed</span>
                                    <?php } else { ?>
                                        <form method="POST">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <input type="hidden" name="action" value="confirm">
                                            <input type="submit" value="Confirm" class="confirm-button">
                                        </form>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ((bool) $order['status']) { ?>
                                        <span></span>
                                    <?php } else { ?>
                                        <form method="POST">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <input type="hidden" name="action" value="cancel">
                                            <input type="submit" value="Cancel" class="cancel-button">
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>