<?php
session_start();
if (!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../helpers/multilang.php';
include_once '../helpers/FileCommander.php';
include_once '../helpers/DbQueries.php';
include_once '../helpers/Validation.php';
include_once '../helpers/ECommerceLogic.php';
include_once '../helpers/setParsing.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['fake_error'] = '';
    $action = $_POST['action'];
    switch ($action) {
        case 'set_pin':
            $new_pin = implode(
                ';',
                [
                    implode(':', [(int) $_POST['days1'], (float) $_POST['price1']]),
                    implode(':', [(int) $_POST['days2'], (float) $_POST['price2']]),
                    implode(':', [(int) $_POST['days3'], (float) $_POST['price3']])
                ]
            );
            updateParam('pin_to_top', $new_pin);
            break;
        case 'set_lift':
            $new_lift = implode(
                ';',
                [
                    implode(':', [(int) $_POST['days1'], (float) $_POST['price1']]),
                    implode(':', [(int) $_POST['days2'], (float) $_POST['price2']]),
                    implode(':', [(int) $_POST['days3'], (float) $_POST['price3']])
                ]
            );
            updateParam('lift_up', $new_lift);
            break;
    }
}
$pin_to_top = explode(';', getParam('pin_to_top')[0]['value']);
$lift_up = explode(';', getParam('lift_up')[0]['value']);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Fake Sets</title>
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
            padding: 1px;
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
            width: 600px;
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
            padding: 6px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
        }

        input[type=submit]:hover {
            background-color: #45a049;
        }

        .pagination {
            display: flex;
            list-style: none;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a {
            color: #333;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            text-decoration: none;
        }

        .pagination li a:hover {
            background-color: #eee;
        }

        .pagination .active a {
            background-color: #333;
            color: #fff;
        }

        .row {
            display: flex;
            align-items: center;
            justify-content:
                /*flex-end*/
                space-between;
        }

        .input-field {
            margin-left: 1em;
            padding: .5em;
            margin-bottom: .5em;
        }
    </style>
</head>

<body>
    <h1>Promotion</h1>
    <h3>
        <?= $_SESSION['fake_error'] ?>
    </h3>
    <button onclick="showModal()">Pin To Top</button>
    <button onclick="showBot()">Lift Up</button>

    <!-- Модальное окно -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Pin to Top</h2>
            <form method="POST">
                <div class="row"> Type 1:
                    <label for="days1">days</label>
                    <input type="number" class="input-field" id="days1" name="days1" step="1"
                        value="<?= explode(':', $pin_to_top[0])[0] ?>" required>
                    <label for="price1">price</label>
                    <input type="number" class="input-field" id="price1" name="price1" step="0.01"
                        value="<?= explode(':', $pin_to_top[0])[1] ?>" required>
                </div>
                <div class="row"> Type 2:
                    <label for="days2">days</label>
                    <input type="number" class="input-field" id="days2" name="days2" step="2"
                        value="<?= explode(':', $pin_to_top[1])[0] ?>" required>
                    <label for="price2">price</label>
                    <input type="number" class="input-field" id="price2" name="price2" step="0.01"
                        value="<?= explode(':', $pin_to_top[1])[1] ?>" required>
                </div>
                <div class="row"> Type 3:
                    <label for="days3">days</label>
                    <input type="number" class="input-field" id="days3" name="days3" step="3"
                        value="<?= explode(':', $pin_to_top[2])[0] ?>" required>
                    <label for="price3">price</label>
                    <input type="number" class="input-field" id="price3" name="price3" step="0.01"
                        value="<?= explode(':', $pin_to_top[2])[1] ?>" required>
                </div>
                <div class="row">
                    <input type="hidden" id="action" name="action" value="set_pin">
                    <input type="submit" id="submit" value="Set">
                </div>
            </form>
        </div>
    </div>

    <div id="bot" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBot()">&times;</span>
            <h2>Lift up</h2>
            <form method="POST">
                <div class="row"> Type 1:
                    <label for="days1">days</label>
                    <input type="number" class="input-field" id="days1" name="days1" step="1"
                        value="<?= explode(':', $lift_up[0])[0] ?>" required>
                    <label for="price1">price</label>
                    <input type="number" class="input-field" id="price1" name="price1" step="0.01"
                        value="<?= explode(':', $lift_up[0])[1] ?>" required>
                </div>
                <div class="row"> Type 2:
                    <label for="days2">days</label>
                    <input type="number" class="input-field" id="days2" name="days2" step="2"
                        value="<?= explode(':', $lift_up[1])[0] ?>" required>
                    <label for="price2">price</label>
                    <input type="number" class="input-field" id="price2" name="price2" step="0.01"
                        value="<?= explode(':', $lift_up[1])[1] ?>" required>
                </div>
                <div class="row"> Type 3:
                    <label for="days3">days</label>
                    <input type="number" class="input-field" id="days3" name="days3" step="3"
                        value="<?= explode(':', $lift_up[2])[0] ?>" required>
                    <label for="price3">price</label>
                    <input type="number" class="input-field" id="price3" name="price3" step="0.01"
                        value="<?= explode(':', $lift_up[2])[1] ?>" required>
                </div>
                <div class="row">
                    <input type="hidden" id="action" name="action" value="set_lift">
                    <input type="submit" id="submit" value="Set">
                </div>
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

        // Открыть модальное окно
        function showBot() {
            document.getElementById("bot").style.display = "block";
        }

        // Закрыть модальное окно
        function closeBot() {
            document.getElementById("bot").style.display = "none";
        }

        function switchBot(status) {
            let form_data = new FormData();
            form_data.append('status', status);
            let xhr = new XMLHttpRequest();
            xhr.open("POST", 'ajax/switch_bot.php', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    reloadCurrentPage();
                }
            };
            xhr.send(form_data);
        }

        function reloadCurrentPage() {
            window.location.href = document.URL;
        }
    </script>

</body>

</html>