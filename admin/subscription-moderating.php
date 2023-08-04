<?php
session_start();
if (!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../helpers/FileCommander.php';
include_once '../helpers/DbQueries.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'update':
            updateSubParam(abs((float) $_POST['price']) );
            updateSubFreeParam(abs((int) $_POST['period']));
            break;
    }
}
$sub = getSubParam()[0]['amount'];
$sub_free = getSubFreeParam()[0]['amount'];
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>FAQs</title>
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
    <h1>Subscription</h1>
    <h3>Price: <?= $sub ?> USD</h3>
    <h3>Grace period: <?= $sub_free ?> days</h3>
    <button onclick="showModal()">Change</button>

    <!-- Модальное окно -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Sub Form</h2>
            <form method="POST">
                <label for="price">Price (USD)</label>
                <input type="text" id="price" name="price" placeholder="price">

                <label for="period">Grace period (days)</label>
                <input type="text" id="period" name="period" placeholder="period">

                <input type="hidden" id="action" name="action" value="update">
                <input type="submit" id="submit" value="Change">
            </form>
        </div>
    </div>

    <script>
        let sub = <?= $sub ?>;
        let sub_free = <?= $sub_free ?>;
        // Открыть модальное окно
        function showModal() {
            document.getElementById("price").value = sub;
            document.getElementById("period").value = sub_free;
            document.getElementById("myModal").style.display = "block";
        }

        // Закрыть модальное окно
        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        
    </script>

</body>

</html>