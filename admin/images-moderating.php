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
        case 'approve':
            $image_id = (int) $_POST['image_id'];
            $image = getModeratedImage($image_id);
            if (count($image) > 0) {
                $image = $image[0];
                approveModeratedImage($image_id);
            }
            break;
        case 'delete':
            $image_id = (int) $_POST['image_id'];
            $image = getModeratedImage($image_id);
            if (count($image) > 0) {
                $image = $image[0];
                if ($image['status'] === 'moderating' && $image['name'] !== '' && $image['name_original'] != '' && $image['name_thumbnail'] != '') {
                    deleteImageActions($image_id);
                    $respond = deleteModeratedImage($image_id);
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
$per_page = 6;
$total_items = (int) getModeratingImagesAmount()[0]['amount'];
$total_pages = ceil($total_items / $per_page);
$current_page = max(1, min($total_pages, $current_page));
$images = getModeratingImages($current_page, $per_page);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PicsTrader</title>
    <link rel="stylesheet" href="../inc/assets/css/core.min.css">
    <link rel="stylesheet" href="../inc/assets/css/fontawesome.min.css">
    <style>
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
    <div id="wrapper" class="wrapper">
        <div class="m-1 p-1">
            <div class="justify-content-center m-1 p-1">
                <?php foreach ($images as $image) { ?>
                    <div class="m-1 p-1" style="display:inline-block;">
                        <img style="width:400px;height:400px;object-fit:contain;" class="m-1 p-1"
                            src="../inc/assets/img/<?= $image['name_original'] ?>" />
                        <div style="display: flex; justify-content: space-between;">
                            <div style="text-align:center;">
                                <form class="m-1 p-1" method="post">
                                    <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="submit" class="btn btn-primary" value="Approve"
                                        style="width:180px; height: 40px;">
                                </form>
                            </div>
                            <div style="text-align:center;">
                                <form class="m-1 p-1" method="post">
                                    <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="submit" class="btn btn-danger" value="Delete"
                                        style="width:180px; height: 40px;">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
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