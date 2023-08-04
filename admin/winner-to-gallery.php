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
        case 'add':
            $image = getImageOfSpecialAccount($_POST['image_id'])[0];
            unsetImageOfSpecialAccount($_POST['image_id']);
            $image['winner_image'] = $image['id'];
            $image['id'] = $image['set_id'];
            addImageToGallery($image);
            forcePurchaseWonImagePart(['image_id' => $_POST['image_id'], 'user_id' => COMPANY_ACCOUNT_ID, 'price' => 0.1]);
            break;
        case 'delete':
            $image_id = (int) $_POST['image_id'];
            $image = getModeratedImage($image_id);
            if (count($image) > 0) {
                deleteImageHashtags(['image_id' => $image_id]);
                $image = $image[0];
                if ($image['status'] === 'winning' && $image['name'] !== '' && $image['name_original'] != '' && $image['name_thumbnail'] != '') {
                    deleteImageActions($image_id);
                    $respond = deleteImageOfSpecialAccount($image_id);
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
    }
}
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 4;
$total_items = (int) getImagesOfSpecialAccountAmount()[0]['amount'];
$total_pages = ceil($total_items / $per_page);
$current_page = max(1, min($total_pages, $current_page));
$images = getImagesOfSpecialAccount($current_page, $per_page);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Фотогалерея</title>
    <style>
        .container {
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
    <div class="container">
        <?php foreach ($images as $image) { ?>
            <div class="image-wrapper">
                <img class="image" src="../inc/assets/img/<?= $image['name'] ?>" alt="Image 1">
                <div class="button-wrapper">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                        <input type="submit" value="Add">
                    </form>
                    <form method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
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
</body>

</html>