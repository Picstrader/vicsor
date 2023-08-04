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
        case 'update':
            updateBonusReg(abs((float) $_POST['sum']));
            updateDemoSum(abs((float) $_POST['demo-sum']));
            break;
    }
}
$bonus_reg = getBonusReg()[0]['amount'];
$company_acc_balance = getUserBalance(['user_id' => COMPANY_ACCOUNT_ID])[0]['balance'];
$demo_sum = getDemoSum()[0]['amount'];
// $logs = getBonusRegLogs();
// $total = getBonusRegSum()[0]['total'];
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
    <h1>Reg Benefits</h1>
    <h3>Денег на спец аккаунте:
        <?= (float) $company_acc_balance ?> USD
    </h3>
    <h3>Начисляемая сумма при регистрации:
        <?= (float) $bonus_reg ?> USD
    </h3>
    <h3>Демо сумма при регистрации:
        <?= (float) $demo_sum ?> USD
    </h3>
    <!-- <h3>Сумма всех начисленных бонусов:
        <?= (float) $total ?> USD
    </h3> -->
    <button onclick="showModal()">Изменить</button>
    <!-- <table>
        <tr>
            <th>Описание</th>
            <th>Сумма(USD)</th>
            <th>Дата</th>
        </tr>
        <?php foreach ($logs as $log) { ?>
            <tr>
                <td>
                    <?= $log['description'] . ((int) $log['user_id'] ? " (User ID: $log[user_id])" : "") ?>
                </td>
                <td>
                    <?= $log['amount'] ?>
                </td>
                <td>
                    <?= $log['created'] ?>
                </td>
            </tr>
        <?php } ?>
    </table> -->

    <!-- Модальное окно -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Form</h2>
            <form method="POST">
                <label for="sum">Сумма</label>
                <input type="number" step='0.01' id="sum" name="sum" placeholder="сумма" value="<?= $bonus_reg ?>">
                <label for="demo-sum">Демо сумма</label>
                <input type="number" step='0.01' id="demo-sum" name="demo-sum" placeholder="демо сумма" value="<?= $demo_sum ?>">
                <input type="hidden" id="action" name="action" value="update">
                <input type="submit" id="submit" value="Update">
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