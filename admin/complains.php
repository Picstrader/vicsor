<?php
session_start();
if(!$_SESSION['admin']) {
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
                if ($image['status'] === 'moderating' && $image['name'] !== '' && $image['name_original'] != '') {
                    deleteImageActions($image_id);
                    $respond = deleteImage($image_id);
                    if ($respond) {
                        $full_path = '../inc/assets/img/' . $image['name'];
                        if (FileCommander::is_image($full_path))
                            unlink($full_path);
                        $full_path = '../inc/assets/img/' . $image['name_original'];
                        if (FileCommander::is_image($full_path))
                            unlink($full_path);
                    }
                }
            }
            break;
    }
}
$images = getComplainedImages();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PicsTrader</title>
    <link rel="stylesheet" href="../inc/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../inc/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../inc/assets/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="../inc/assets/css/core.min.css">
    <link rel="stylesheet" href="../inc/assets/css/fontawesome.min.css">
</head>

<body>
    <div id="wrapper" class="wrapper">
        <div class="m-1 p-1">
            <div class="justify-content-center m-1 p-1">
                <?php foreach ($images as $image) {
                    if ($image['name'] === '') {
                        continue;
                    }
                    $complains = getCountComplainsOfImageByType($image['image_id']);
                    $complain_type_1 = 0;
                    $complain_type_2 = 0;
                    $complain_type_3 = 0;
                    foreach ($complains as $complain) {
                        switch ($complain['type']) {
                            case '1':
                                $complain_type_1 = $complain['amount'];
                                break;
                            case '2':
                                $complain_type_2 = $complain['amount'];
                                break;
                            case '3':
                                $complain_type_3 = $complain['amount'];
                                break;
                        }
                    }
                    $total_complains = (int) $complain_type_1 + (int) $complain_type_2 + (int) $complain_type_3;
                    ?>
                    <div class="m-1 p-1" style="display:inline-block;">
                        <img style="width:400px;height:400px;object-fit:contain;" class="m-1 p-1"
                            src="../inc/assets/img/<?= $image['name'] ?>" />
                        <div style="text-align:center;">
                            Всего жалоб:
                            <?= $total_complains ?>
                        </div>
                        <div style="text-align:center;">
                            Фото оскорбительного характера:
                            <?= $complain_type_1 ?>
                        </div>
                        <div style="text-align:center;">
                            Насилие:
                            <?= $complain_type_2 ?>
                        </div>
                        <div style="text-align:center;">
                            Взрослый контент:
                            <?= $complain_type_3 ?>
                        </div>
                        <div style="text-align:center;">
                            id хозяина:
                            <?= $image['owner_id'] ?>
                        </div>
                        <div style="text-align:center;">
                            id сета:
                            <?= $image['set_id'] ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>