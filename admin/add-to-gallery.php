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
        case 'download':
            $fields = [];
            $fields['user_id'] = COMPANY_ACCOUNT_ID;
            $fields['likes'] = abs((int) $_POST['likes']);
            $fields['profit'] = abs((float) $_POST['profit']);
            $fields['percent'] = abs((float) $_POST['percent']);
            $fields['image_name_original'] = FileCommander::upload_image(true);
            $fields['image_name'] = FileCommander::create_watermark_image($fields['image_name_original'], true);
            $fields['image_name_thumbnail'] = FileCommander::create_thumbnail_image($fields['image_name'], true);
            if ($fields['image_name']) {
                $fields['image_id'] = forceAddUserImageForGallery($fields);
                if ($fields['image_id']) {
                    $respond = forceAddImageToGallery($fields);
                    if ($respond) {
                        $res = forcePurchaseWonImagePart($fields);
                        if ($res) {
                            echo 'image was added, success';
                        } else {
                            echo 'owner was not set for image';
                        }
                    } else {
                        echo 'image was not added to gallery';
                    }
                } else {
                    echo 'image was not added';
                }
            } else {
                echo 'image was not upload';
            }
            break;
        case 'delete':
            deleteOwnerOfPurchasedImage(['image_id' => $_POST['image_id'], 'old_user_id' => COMPANY_ACCOUNT_ID]);
            deleteGalleryImage(['image_id' => $_POST['image_id']]);
            $image_id = (int) $_POST['image_id'];
            $image = getModeratedImage($image_id);
            if (count($image) > 0) {
                $image = $image[0];
                if ($image['name'] !== '' && $image['name_original'] != '' && $image['name_thumbnail'] != '') {
                    deleteImageActions($image_id);
                    $respond = deleteImage($image_id);
                    if ($respond) {
                        $full_path = '../inc/assets/img/' . $image['name'];
                        if (FileCommander::is_image($full_path))
                            unlink($full_path);
                        $full_path = '../inc/assets/img/' . $image['name_original'];
                        if (FileCommander::is_image($full_path))
                            unlink($full_path);
                        $full_path = '../inc/assets/img/' . $image['name_thumbnail'];
                        if (FileCommander::is_image($full_path))
                            unlink($full_path);
                    }
                }
            }
            break;
        case 'edit':
            updateGallery($_POST);
            break;
    }
}
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 36;
$total_items = (int) getGalleryAmount($_POST['filter'], $_POST['filter'] == '1' ? 1 : 0)[0]['amount'];
$total_pages = ceil($total_items / $per_page);
$current_page = max(1, min($total_pages, $current_page));
$images = getGallery($current_page, $per_page, $_POST['filter'], $_POST['filter'] == '1' ? 1 : 0);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Фотогалерея</title>
    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
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

        label {
            margin-bottom: 10px;
        }

        input[type="file"] {
            margin-bottom: 20px;
        }

        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        } */

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            max-width: 2000px;
            padding: 20px;
            gap: 10px;
        }

        .image-wrapper {
            position: relative;
            /* margin: 20px; */
        }

        .image {
            /* display: block; */
            width: 150px;
            height: 150px;
            object-fit: contain;
            transition: transform 0.3s ease-in-out;
        }

        .image-slide {
            border: 4px solid #ff0000;
        }

        .image:hover {
            transform: scale(1.05);
        }

        .button-wrapper {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 120px;
        }

        button {
            background-color: #555;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
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
            /* background-color: #fefefe; */
            margin: 15% auto;
            padding: 20px;
            /* border: 1px solid #888; */
            /* width: 80%; */
        }

        .close {
            color: #fff;
            float: right;
            font-size: 50px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
    </style>
</head>

<body>
    <div style="display:flex;justify-content:center;">
        <form class="f1" action="#" method="POST" enctype="multipart/form-data">
            <div>
                <label for="fileToUpload">Выберите фото:</label>
                <input type="file" id="fileToUpload" name="fileToUpload">
            </div>
            <div>
                <label for="text">Likes:</label>
                <input type="number" id="likes" name="likes" required>
            </div>
            <div>
                <label for="profit">Profit USD:</label>
                <input type="number" id="profit" name="profit" step="0.01" required>
            </div>
            <div>
                <label for="percent">Profit %:</label>
                <input type="number" id="percent" name="percent" step="0.01" required>
            </div>
            <div>
                <input type="hidden" name="action" value="download">
                <button type="submit">Загрузить</button>
            </div>
        </form>
        <form class="f1" method="POST">
            <div>
                <select name='filter'>
                    <option value='' <?= $_POST['filter'] == '' ? 'selected' : '' ?>>все</option>
                    <option value='1' <?= $_POST['filter'] == '1' ? 'selected' : '' ?>>добавленные</option>
                    <option value='2' <?= $_POST['filter'] == '2' ? 'selected' : '' ?>>победители</option>
                </select>
            </div>
            <div>
                <input type="hidden" name="action" value="filters">
                <button type="submit">Применить</button>
            </div>
        </form>
    </div>
    <div class="container">
        <?php foreach ($images as $image) { ?>
            <div class="image-wrapper">
                <img class="image <?= (int) $image['slider'] ? 'image-slide' : '' ?>"
                    src="../inc/assets/img/<?= $image['name_thumbnail'] ?>" alt="Image 1">
                <div class="button-wrapper">
                    <button onclick='showModal(<?= json_encode($image) ?>)'>Edit</button>
                    <form style="margin-block-end: 0;" method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="image_id" value="<?= $image['image_id'] ?>">
                        <input type="submit" value="Delete">
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
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
    <!-- Модальное окно -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div style="display:flex;justify-content:center;">
                <form class="f1" action="#" method="POST" enctype="multipart/form-data">
                    <label for="likes_edit">Likes:</label>
                    <input type="number" id="likes_edit" name="likes" required>
                    <label for="profit_edit">Profit USD:</label>
                    <input type="number" id="profit_edit" name="profit" step="0.01" required>
                    <label for="percent_edit">Profit %:</label>
                    <input type="number" id="percent_edit" name="percent" step="0.01" required>
                    <label for="slider">slider</label>
                    <input type="checkbox" id="slider" name="slider" value="1">
                    <input type="hidden" id="image_id" name="id" value="">
                    <input type="hidden" name="action" value="edit">
                    <button type="submit">Edit</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Открыть модальное окно
        function showModal(img) {
            document.querySelector('#image_id').value = Number(img.id);
            document.querySelector('#likes_edit').value = Number(img.likes);
            document.querySelector('#profit_edit').value = Number(img.profit);
            document.querySelector('#percent_edit').value = Number(img.percent);
            if (Number(img.slider)) {
                document.querySelector('#slider').checked = true;
            }
            document.getElementById("myModal").style.display = "block";
        }

        // Закрыть модальное окно
        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }
    </script>
</body>

</html>