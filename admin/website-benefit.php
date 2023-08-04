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
        case 'withdraw':
            if ($_SESSION['admin']) {
                $total_benefit = getSiteBenefit();
                $total_benefit = $total_benefit[0]['amount'];
                if((float) $_POST['withdraw'] <= 0) {
                    break;
                }
                if((float) $total_benefit < (float) $_POST['withdraw']) {
                    break;
                }
                $total_benefit = (float) $total_benefit - (float) $_POST['withdraw'];
                withdrawSiteBenefit($total_benefit);
                createWithdrawSiteLog((float) $_POST['withdraw']);
            }
            break;
    }
}
$logs = getSiteLogs();
$total_benefit = getSiteBenefit();
$total_benefit = $total_benefit[0]['amount'];

$total_balances = 0;
$balances = getAllBalances();
foreach($balances as $balance) {
    $total_balances += (float) $balance['balance']; 
}

$total_sets_cost = 0;
$sets = getAllTradesCost();
$fiction_sets = getAllFictionTradesCost();
foreach($sets as $set) {
    $players = explode(';', $set['users_photos']);
    $total_sets_cost += ((int) $set['cost'] * (int) count($players));
}
$total_fiction_sets_cost = 0;
foreach($fiction_sets as $f) {
    $players = explode(';', $f['users_photos']);
    $total_fiction_sets_cost += ((int) $f['cost'] * (int) count($players));
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Benefits</title>
    <style>
        /* Стили для кнопки */
        button {
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        /* Стили для таблицы */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        /* Стили для заголовка страницы */
        h1 {
            text-align: center;
            font-size: 36px;
            margin-top: 50px;
        }

        /* Стили для модального окна */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Стили для формы */
        input[type=text],
        select {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type=submit] {
            background-color: #4CAF50;
            color: white;
            /* padding: 14px 20px; */
            /* margin: 8px 0; */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
        }

        input[type=submit]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <h1>Benefits</h1>
    <h3>Текущая прибыль:
        <?= $total_benefit ?> USD
    </h3>
    <h3>Всего на балансах:
        <?= $total_balances ?> USD
    </h3>
    <h3>Всего в лотах:
        <?= $total_sets_cost ?> USD
    </h3>
    <h3>Итого:
        <?= $total_sets_cost+$total_benefit+$total_balances ?> USD
    </h3>
    <br>
    <h3>Всего в фейковых лотах:
        <?= $total_fiction_sets_cost ?> USD
    </h3>
    <button onclick="showModal()">Вывести</button>
    <table>
        <tr>
            <th>Описание</th>
            <th>Процент</th>
            <th>Сумма(USD)</th>
            <th>Дата</th>
            <th>Вывод средств</th>
        </tr>
        <?php foreach ($logs as $log) { ?>
            <tr>
                <td>
                    <?= $log['description'] . ((int) $log['user_id'] ? " (User ID: $log[user_id])" : "") ?>
                </td>
                <td>
                    <?= $log['percentage'] ?>
                </td>
                <td>
                    <?= $log['amount'] ?>
                </td>
                <td>
                    <?= $log['created'] ?>
                </td>
                <td>
                    <?= (int) $log['withdraw'] ? 'Вывод средств' : '-' ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Модальное окно -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Withdraw Form</h2>
            <form method="POST">
                <label for="position">Сумма</label>
                <input type="text" id="position" name="withdraw" placeholder="сумма">
                <input type="hidden" id="action" name="action" value="withdraw">
                <input type="submit" id="submit" value="Withdraw">
            </form>
        </div>
    </div>

    <script>
        // Открыть модальное окно
        function showModal() {
            document.getElementById("myModal").style.display = "block";
        }

        // Закрыть модальное окно
        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }
    </script>

</body>

</html>