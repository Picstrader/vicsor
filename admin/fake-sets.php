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
function fillFakeSet($amount)
{
    $users_photos = [];
    for ($i = 0; $i < (int) $amount; $i++) {
        array_push($users_photos, '0:0');
    }
    return implode(';', $users_photos);
}
function validateFakeSet($fields)
{
    if (!Validation::check_out_of_range_number($fields['cost']) || (int) $fields['cost'] < 1) {
        $_SESSION['fake_error'] = 'invalid cost(A)';
        return false;
    }
    if (isset($fields['cost-step'])) {
        if (!Validation::check_out_of_range_number($fields['cost-step']) || (int) $fields['cost-step'] < 1 || (int) $fields['cost-step'] > 10) {
            $_SESSION['fake_error'] = 'invalid cost step, must be 1-10';
            return false;
        }
    }
    if ((int) $fields['cost-step'] > (int) $fields['cost']) {
        $_SESSION['fake_error'] = 'cost step can not be more than cost';
        return false;
    }
    if (!Validation::check_out_of_range_number($fields['photos']) || (int) $fields['photos'] < 2) {
        $_SESSION['fake_error'] = 'invalid photos(B)';
        return false;
    }
    if (!Validation::check_out_of_range_number($fields['purchasable']) || (int) $fields['purchasable'] < 1) {
        $_SESSION['fake_error'] = 'invalid purchasable(C)';
        return false;
    }
    if ((int) $fields['purchasable'] > (int) $fields['photos']) {
        $_SESSION['fake_error'] = 'invalid purchasable(C) > photos(B)';
        return false;
    }
    if ((int) $fields['amount'] >= (int) $fields['photos']) {
        $_SESSION['fake_error'] = 'invalid users in set >= total photos in set';
        return false;
    }
    // if (isset($fields['time'])) {
    //     if ((int) $fields['time'] <= 0 || (int) $fields['time'] > 24 * 60 * 60) {
    //         $_SESSION['fake_error'] = 'wrong time min 1 max 86400(1 day)';
    //         return false;
    //     }
    // }
    return true;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['fake_error'] = '';
    $action = $_POST['action'];
    switch ($action) {
        case 'create':
            if (!validateFakeSet($_POST)) {
                break;
            }
            $existing_sets = getSet($_POST);
            foreach ($existing_sets as $s) {
                if (isFullSet($s)) {
                    continue;
                } else {
                    $_SESSION['fake_error'] = 'set already created';
                    break 2;
                }
            }
            $fields = $_POST;
            $fields['users_photos'] = fillFakeSet($fields['amount']);
            $fields['time'] = 24 * 60 * 60;
            createFictionSet($fields);
            break;
        case 'delete':
            deleteFictionSet($_POST['id']);
            break;
        case 'bot':
            if (!validateFakeSet($_POST)) {
                break;
            }
            setBotFakeA($_POST['cost']);
            setBotFakeAStep($_POST['cost-step']);
            setBotFakeB($_POST['photos']);
            setBotFakeC($_POST['purchasable']);
            setBotFakeUsers($_POST['amount']);
            setBotFakeTime((int) $_POST['time'] >= 60 ? ((int) $_POST['time'] - 60) : 0 );
            setBotFakeAddUserTime((int) $_POST['add-user-time'] >= 60 ? ((int) $_POST['add-user-time'] - 60) : 0);
            break;
    }
}
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 10;
$total_items = (int) getFictionSetsAmount()[0]['amount'];
$total_pages = ceil($total_items / $per_page);
$current_page = max(1, min($total_pages, $current_page));
$sets = getFictionSets($current_page, $per_page);
$a = (int) getBotFakeA()[0]['amount'];
$a_step = (int) getBotFakeAStep()[0]['amount'];
$b = (int) getBotFakeB()[0]['amount'];
$c = (int) getBotFakeC()[0]['amount'];
$time = (int) getBotFakeTime()[0]['amount'] + 60;
$users = (int) getBotFakeUsers()[0]['amount'];
$status = (int) getBotFakeStatus()[0]['amount'];
$add_user = (int) getBotFakeAddUserTime()[0]['amount'] + 60;
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
            width: 400px;
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
            justify-content: flex-end;
        }

        .input-field {
            margin-left: 1em;
            padding: .5em;
            margin-bottom: .5em;
        }
    </style>
</head>

<body>
    <h1>Fake Sets</h1>
    <h3>
        <?= $_SESSION['fake_error'] ?>
    </h3>
    <button onclick="showModal()">Create</button>
    <button onclick="showBot()">Bot settings</button>
    <button style="background:<?= $status ? 'blue' : 'red' ?>" onclick="switchBot(<?= $status ? 0 : 1 ?>)"><?= $status ? 'Bot running' : 'Bot stopped' ?></button>
    <table>
        <tr>
            <th>A</th>
            <th>B</th>
            <th>C</th>
            <th>Number of users</th>
            <th>Delete</th>
        </tr>
        <?php foreach ($sets as $set) { ?>
        <tr>
            <td>
                <?= $set['cost'] ?>
            </td>
            <td>
                <?= $set['total_photos'] ?>
            </td>
            <td>
                <?= $set['pur_photos'] ?>
            </td>
            <td>
                <?= count(explode(';', $set['users_photos'])) ?>
            </td>
            <td>
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value='<?= $set["id"] ?>'>
                    <input type="submit" value="Delete">
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
    <ul class="pagination">
        <?php if ($total_pages > 1) { ?>

        <?php if ($current_page != 1) { ?>
        <li><a href="?page=1">&laquo;</a></li>
        <?php } ?>

        <?php for ($i = max(1, $current_page - 2); $i <= min($current_page + 2, $total_pages); $i++) { ?>
        <li class="<?= $i == $current_page ? 'active' : '' ?>"><a href="?page=<?= $i ?>"><?= $i ?></a></li>
        <?php } ?>

        <?php if ($current_page != $total_pages) { ?>
        <li><a href="?page=<?= $total_pages ?>">&raquo;</a></li>
        <?php } ?>

        <?php } ?>
    </ul>

    <!-- Модальное окно -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Create Form</h2>
            <form method="POST">
                <div class="row">
                    <label for="a">A</label>
                    <input type="number" class="input-field" id="a" name="cost" required>
                </div>
                <div class="row">
                    <label for="b">B</label>
                    <input type="number" class="input-field" id="b" name="photos" required>
                </div>
                <div class="row">
                    <label for="c">C</label>
                    <input type="number" class="input-field" id="c" name="purchasable" required>
                </div>
                <div class="row">
                    <label for="amount">Users in set</label>
                    <input type="number" class="input-field" id="amount" name="amount" required>
                </div>
                <div class="row">
                    <input type="hidden" id="action" name="action" value="create">
                    <input type="submit" id="submit" value="Create">
                </div>
            </form>
        </div>
    </div>

    <div id="bot" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBot()">&times;</span>
            <h2>Bot Form</h2>
            <form method="POST">
                <div class="row">
                    <label for="a">A</label>
                    <input type="number" class="input-field" id="a" name="cost" value="<?= $a ?>" required>
                </div>
                <div class="row">
                    <label for="a-step">A step</label>
                    <input type="number" min='1' max='10' class="input-field" id="a-step" name="cost-step"
                        value="<?= $a_step ?>" required>
                </div>
                <div class="row">
                    <label for="b">B</label>
                    <input type="number" class="input-field" id="b" name="photos" value="<?= $b ?>" required>
                </div>
                <div class="row">
                    <label for="c">C</label>
                    <input type="number" class="input-field" id="c" name="purchasable" value="<?= $c ?>" required>
                </div>
                <div class="row">
                    <label for="amount">Max users in set</label>
                    <input type="number" class="input-field" id="amount" name="amount" value="<?= $users ?>" required>
                </div>
                <div class="row">
                    <label for="time">Interval(in seconds)</label>
                    <input type="number" class="input-field" id="time" name="time" value="<?= $time ?>" required>
                </div>
                <div class="row">
                    <label for="time">Add user Interval(in seconds)</label>
                    <input type="number" class="input-field" id="time" name="add-user-time" value="<?= $add_user ?>"
                        required>
                </div>
                <div class="row">
                    <input type="hidden" id="action" name="action" value="bot">
                    <input type="submit" id="submit" value="Submit">
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