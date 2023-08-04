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
        case 'upload':
            $image_name = FileCommander::upload_image(true);
            if ($image_name) {
                setBackgroundImage($image_name);
            }
            break;
        case 'delete':
            $image_id = (int) $_POST['image_id'];
            $image = getBackgroundImage($image_id);
            if (count($image) > 0) {
                $image = $image[0];
                if ($image['name'] !== '') {
                    $respond = deleteBackgroundImage($image_id);
                    if ($respond) {
                        $full_path = '../inc/assets/img/' . $image['name'];
                        if (FileCommander::is_image($full_path))
                            unlink($full_path);
                    }
                }
            }
            break;
    }
}
$images = getBackgroundImages();
$err_message = isset($_SESSION['error_type']) ? $_SESSION['error_type'] : false;
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
            <div class="d-flex justify-content-center m-1 p-1">
                <form style="display:inline-block;" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label <?= $err_message ? 'is-invalid' : '' ?>" for="fileToUpload">Select
                            image to
                            upload:</label>
                        <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
                        <div class="invalid-feedback">
                            <?= $err_message ?>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="upload">
                    <input type="submit" value="Upload" name="submit" class="btn btn-primary">
                </form>
            </div>
            <div class="justify-content-center m-1 p-1">
                <?php foreach ($images as $image) { ?>
                    <div class="m-1 p-1" style="display:inline-block;">
                        <img style="width:400px;height:400px;object-fit:contain;" class="m-1 p-1"
                            src="../inc/assets/img/<?= $image['name'] ?>" />
                        <div style="text-align:center;">
                            <form class="m-1 p-1" method="post">
                                <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="submit" class="btn btn-danger" value="Delete" style="width:200px;">
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>