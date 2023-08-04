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
$lang_id = $cur_ln_id;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'create':
            if (!$_SESSION['admin']) {
                break;
            }
            $id = createFaq($_POST);
            if (!$id) {
                break;
            }
            if (count(getFaqQuestion($id, $lang_id))) {
                updateFaqQuestion($id, $lang_id, $_POST['question']);
            } else {
                createFaqQuestion($id, $lang_id, $_POST['question']);
            }
            if (count(getFaqAnswer($id, $lang_id))) {
                updateFaqAnswer($id, $lang_id, $_POST['answer']);
            } else {
                createFaqAnswer($id, $lang_id, $_POST['answer']);
            }
            // if($id) {
            //     updateFaqsPositionAdd($id, $_POST['position']);
            // }
            break;
        case 'update':
            if (!$_SESSION['admin']) {
                break;
            }
            $req = updateFaq($_POST);
            $id = $_POST['id'];
            if (count(getFaqQuestion($id, $lang_id))) {
                updateFaqQuestion($id, $lang_id, $_POST['question']);
            } else {
                createFaqQuestion($id, $lang_id, $_POST['question']);
            }
            if (count(getFaqAnswer($id, $lang_id))) {
                updateFaqAnswer($id, $lang_id, $_POST['answer']);
            } else {
                createFaqAnswer($id, $lang_id, $_POST['answer']);
            }
            // if($req) {
            //     updateFaqsPositionAdd($_POST['id'], $_POST['position']);
            // }
            break;
        case 'delete':
            if (!$_SESSION['admin']) {
                break;
            }
            $res = deleteFaq($_POST['id']);
            deleteFaqQuestion($_POST['id']);
            deleteFaqAnswer($_POST['id']);
            // if($res) {
            //     updateFaqsPositionDelete($_POST['id'], $_POST['position']);
            // }
            break;
    }
}
$faqs = getFaqs();
$max = 0;
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
    <h1>FAQs</h1>
    <form method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>" class="form-language">
                        <select name="cur_ln_id" onchange="this.form.submit()" class="language-select">
                            <?php foreach ($ar_lns as $k => $v) {
                                if (isset($cur_ln_id) && $cur_ln_id == $v['lang_id']) {
                                    $selected = ' selected';
                                    $lang_abbr_current = strtoupper($v['lang_name']);
                                } else {
                                    $selected = '';
                                }
                                ?>
                                <option value="<?= $v['lang_id'] ?>" <?= $selected ?>>
                                    <?= strtoupper($v['lang_name']) ?>
                                </option>
                            <?php } ?>
                        </select>
                        <input type="hidden" name="action" value="set_lang">
                    </form>
    <button onclick="createFaq()">Create</button>
    <table>
        <tr>
            <th>Question</th>
            <th>Answer</th>
            <th>Position</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>
        <?php foreach ($faqs as $faq) {
            if ((int) $faq['position'] > $max) {
                $max = (int) $faq['position'];
            }
            $faq['question'] = getFaqQuestion($faq['id'], $lang_id)[0]['phrase_value'];
            $faq['answer'] = getFaqAnswer($faq['id'], $lang_id)[0]['phrase_value'];
            ?>
            <tr>
                <td>
                    <?= $faq['question'] == '' ? 'no translation' : $faq['question'] ?>
                </td>
                <td>
                    <?= $faq['answer'] == '' ? 'no translation' : $faq['answer']  ?>
                </td>
                <td>
                    <?= $faq['position'] ?>
                </td>
                <td><button
                        onclick="updateFaq(<?= htmlspecialchars(json_encode($faq), ENT_QUOTES, 'UTF-8'); ?>)">Update</button>
                </td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value='<?= $faq["id"] ?>'>
                        <input type="hidden" name="position" value='<?= $faq["position"] ?>'>
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Модальное окно -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Create/Update Form</h2>
            <form method="POST">
                <label for="question">Question</label>
                <input type="text" id="question" name="question" placeholder="question">

                <label for="answer">Answer</label>
                <textarea name="answer" id="answer"></textarea>

                <label for="position">Position</label>
                <input type="number" id="position" name="position" placeholder="position">

                <input type="hidden" id="action" name="action" value="">
                <input type="hidden" id="id" name="id" value="">
                <input type="submit" id="submit" value="Create">
            </form>
        </div>
    </div>

    <script>
        let max_position = Number(<?= $max ?>);
        // Открыть модальное окно
        function showModal() {
            document.getElementById("myModal").style.display = "block";
        }

        // Закрыть модальное окно
        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        function createFaq() {
            document.getElementById('question').value = '';
            document.getElementById('answer').value = '';
            document.getElementById('action').value = 'create';
            document.getElementById('submit').value = 'Create';
            document.getElementById('position').value = max_position + 1;
            showModal();
        }

        function updateFaq(faq) {
            document.getElementById('question').value = faq.question;
            document.getElementById('answer').value = faq.answer;
            document.getElementById('id').value = faq.id;
            document.getElementById('position').value = faq.position;
            document.getElementById('action').value = 'update';
            document.getElementById('submit').value = 'Update';
            showModal();
        }

        function escapeHtml(text) {
            let map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function (m) { return map[m]; });
        }
    </script>

</body>

</html>