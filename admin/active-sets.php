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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'check_lots':
            for($i=5;$i <= 50; $i=$i+5) {
                $set = getSets($i, 6, 1, 1, 25, 'active')[0];
                if(!$set) {
                    createSet($i, 6, 1, 3600);
                }
            }
            break;
    }
}
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 10;
$total_items = (int) getAllSetsAmountAdmin($_POST['filter'])[0]['amount'];
$total_pages = ceil($total_items / $per_page);
$current_page = max(1, min($total_pages, $current_page));
$sets = getAllSetsAdmin($current_page, $per_page, $_POST['filter']);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="stylesheet" href="../inc/assets/css/adm-active-sets.css">
    <style>
        .count {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 24px;
            width: 35px;
            height: 35px;
            border: 2px solid;
            border-radius: 50%;
            text-align: center;
            vertical-align: middle;
            line-height: 35px;
        }

        .container-images {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            max-width: 800px;
            padding: 20px;
        }

        .image-wrapper {
            position: relative;
            /* margin: 20px; */
        }

        .image {
            /* display: block; */
            width: 400px;
            height: 400px;
            object-fit: contain;
            transition: transform 0.3s ease-in-out;
        }

        .button-wrapper {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 200px;
        }

        .button {
            background-color: #555;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease-in-out;
        }

        .button:hover {
            background-color: #333;
        }

        input[type=submit] {
            background-color: #555;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease-in-out;
        }

        input[type=submit]:hover {
            background-color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
            /* width: 200px; */
        }

        label {
            margin-bottom: 10px;
        }

        input[type=file] {
            margin-bottom: 10px;
        }

        input[type=text] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            /* width: 100%; */
            margin-bottom: 20px;
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
            cursor: pointer;
        }

        tr:hover {
            background: #d0cfcf;
        }

        .clicked-row {
            background-color: #f9ecec;
            /* Замените этот цвет на желаемый */
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
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

        .f1 {
            display: flex;
            /*            flex-direction: column;*/
            align-items: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f2f2f2;
            gap: 10px;
        }
    </style>
</head>

<body>
    <h1 class="adm_act_sets__h1">Активные сеты</h1>
    <!-- <form class="f1" method="POST" style="width: 200px;">
        <div>
            <select name='filter'>
                <option value='' <?= $_POST['filter'] == '' ? 'selected' : '' ?>>все сеты</option>
                <option value='1' <?= $_POST['filter'] == '1' ? 'selected' : '' ?>>реальные сеты</option>
                <option value='2' <?= $_POST['filter'] == '2' ? 'selected' : '' ?>>фиктивные сеты</option>
            </select>
        </div>
        <div>
            <input type="hidden" name="action" value="filters">
            <button type="submit">Применить</button>
        </div>
    </form> -->
    <form class="f1" method="POST" style="width: 200px;">
        <div>
            <input type="hidden" name="action" value="check_lots">
            <button class="button" type="submit">Создать лоты</button>
        </div>
    </form>
    <table id="myTable">
        <tr class="clicked-row">
            <th data-type="number">ID</th>
            <th data-type="number">A</th>
            <th data-type="number">B</th>
            <th data-type="number">C</th>
            <th data-type="string">Number of users</th>
            <th data-type="number">Time</th>
            <th data-type="string">Fake</th>
        </tr>

        <?php foreach ($sets as $set) { ?>
        <?php
            if (count(explode(';', $set['users_photos'])) == $set['total_photos']) {
                $end_set = "background-color: #c9f5e0;";
            } else {
                $end_set = "";
            }
            ?>
        <tr style="cursor:<?= (int) $set['fiction'] ? 'not-allowed' : 'pointer' ?>;<?= $end_set ?>"
            onclick='showSetImages(<?= $set["id"] ?>)'>
            <td>
                <?= $set['id'] ?>
            </td>
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
                <?= count($set['users_photos'] ? explode(';', $set['users_photos']) : []) ?>/
                <?= $set['total_photos'] ?>
            </td>
            <td>
                <?= $set['time'] ?>
            </td>
            <td>
                <?= (int) $set['fiction'] ? 'Yes' : 'No' ?>
            </td>
        </tr>
        <?php } ?>
    </table>
    <ul class="pagination">
        <?php if ($total_pages > 1) { ?>

        <?php if ($current_page != 1) { ?>
        <li><a href="?page=1">&laquo;</a></li>
        <? } ?>

        <?php for ($i = max(1, $current_page - 2); $i <= min($current_page + 2, $total_pages); $i++) { ?>
        <li class="<?= $i == $current_page ? 'active' : '' ?>"><a href="?page=<?= $i ?>"><?= $i ?></a></li>
        <?php } ?>

        <?php if ($current_page != $total_pages) { ?>
        <li><a href="?page=<?= $total_pages ?>">&raquo;</a></li>
        <?php } ?>

        <?php } ?>
    </ul>
    <div class="container"></div>
    <script>
        function showSetImages(set_id) {
            let form_data = new FormData();
            form_data.append('set_id', set_id);
            form_data.append('action', 'set_images');
            let xhr = new XMLHttpRequest();
            xhr.open("POST", 'ajax/set_images.php', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.querySelector('.container').innerHTML = xhr.responseText;
                }
            };
            xhr.send(form_data);
        }

        function endSet(set_id) {
            let checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            let images = [];
            for (let i = 0; i < checkboxes.length; i++) {
                images.push(Number(checkboxes[i].value));
            }
            let form_data = new FormData();
            form_data.append('images', JSON.stringify(images));
            form_data.append('set', set_id);
            let xhr = new XMLHttpRequest();
            xhr.open("POST", 'ajax/end_set.php', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.querySelector('.container').innerHTML = xhr.responseText;
                    setTimeout(() => { reloadCurrentPage() }, 3000);
                } else if (xhr.readyState === 4 && xhr.status === 400) {
                    document.querySelector('.container').innerHTML = xhr.responseText;
                }
            };
            xhr.send(form_data);
        }

        function reloadCurrentPage() {
            window.location.href = document.URL;
        }

        function countChecked() {
            let checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            document.querySelector('.checked-image').innerHTML = checkboxes.length;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableRows = document.querySelectorAll('tr');
            let currentClickedRow = null;

            tableRows.forEach((row) => {
                row.addEventListener('click', function () {
                    if (currentClickedRow) {
                        currentClickedRow.classList.remove('clicked-row');
                    }

                    if (currentClickedRow === row) {
                        currentClickedRow = null;
                    } else {
                        row.classList.add('clicked-row');
                        currentClickedRow = row;
                    }
                });
            });
        });
    </script>
    <script>
        function sortTable(table, columnIndex, dataType, ascending) {
            const compare = (a, b) => {
                let cellA = a.cells[columnIndex].innerText;
                let cellB = b.cells[columnIndex].innerText;

                if (dataType === 'number') {
                    cellA = parseFloat(cellA) || 0;
                    cellB = parseFloat(cellB) || 0;
                } else {
                    cellA = cellA.toLowerCase();
                    cellB = cellB.toLowerCase();
                }

                const comparison = cellA < cellB ? -1 : (cellA > cellB ? 1 : 0);
                return ascending ? comparison : -comparison;
            }

            const sortedRows = Array.from(table.rows)
                .slice(1) // Исключить строку заголовка
                .sort(compare);

            sortedRows.forEach(row => table.appendChild(row));
        }

        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('myTable');
            const headers = table.getElementsByTagName('th');

            Array.from(headers).forEach((header, index) => {
                let ascending = true;
                header.addEventListener('click', () => {
                    const dataType = header.getAttribute('data-type') || 'string';
                    sortTable(table, index, dataType, ascending);
                    ascending = !ascending; // Переключить направление сортировки
                });
            });
        });
    </script>

</body>

</html>