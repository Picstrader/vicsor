<?php
session_start();
if(!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
$images_data = getMainSliderImages();
$image_string = is_array($images_data) ? $images_data[0]['value'] : '';
$images = $image_string == '' ? [] : explode('/', $image_string);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'upload':
            $image_name = FileCommander::upload_image();
            if ($image_name) {
                $position = $_POST['position'];
                if ((int) $position === 0) {
                    array_push($images, $image_name);
                } else {
                    array_splice($images, (int) $position - 1, 0, $image_name);
                }
            }
            break;
        case 'delete':
            for ($i = 0; $i < count($images); $i++) {
                if($images[$i] == $_POST['name']) {
                    array_splice($images, $i, 1);
                }
            }
            break;
        case 'update':
            $new_position = (int) $_POST['position'];
            $current_position = (int) $_POST['current_position'];
            if ($new_position != $current_position) {
                $delete_elements = array_splice($images, (int) $current_position - 1, 1);
                array_splice($images, (int) $new_position - 1, 0, $delete_elements[0]);
            }
            break;
    }
}
$supported_format = array('gif', 'jpg', 'jpeg', 'png');
for ($i = 0; $i < count($images); $i++) {
    $image_name = 'inc/assets/img/' . $images[$i];
    $img_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    $is_img = ($images[$i] && file_exists($image_name)) ? getimagesize($image_name) : false;
    if (!file_exists($image_name) || !in_array($img_ext, $supported_format) || ($is_img === false)) {
        array_splice($images, $i, 1);
        $i--;
        continue;
    }
}
setMainSliderImages(implode('/', $images));
$err_message = isset($_SESSION['message']) ? $_SESSION['message'] : false;
$_SESSION['message'] = false;
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
                        <label class="form-label <?= $err_message ? 'is-invalid' : '' ?>"
                            for="fileToUpload">Select image to
                            upload:</label>
                        <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
                        <div class="invalid-feedback">
                            <?= $err_message ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="position">Position:</label>
                        <select class="custom-select" name="position" id="position">
                            <option value="0">-</option>
                            <?php for ($i = 0; $i < count($images); $i++) { ?>
                            <option value="<?= $i + 1 ?>">
                                <?= $i + 1 ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <input type="hidden" name="action" value="upload">
                    <input type="submit" value="Upload" name="submit" class="btn btn-primary">
                </form>
            </div>
            <div class="justify-content-center m-1 p-1">
                <?php for ($i = 0; $i < count($images); $i++) { ?>
                <div class="m-1 p-1" style="display:inline-block;">
                    <img class="m-1 p-1" src="../inc/assets/img/<?= $images[$i] ?>" />
                    <form class="m-1 p-1" method="post">
                        <div class="form-group">
                            <label for="position">position:</label>
                            <select class="custom-select" name="position" id="position">
                                <?php for ($j = 0; $j < count($images); $j++) { ?>
                                <option value="<?= $j + 1 ?>" <?= $i == $j ? 'selected' : ''; ?>><?= $j + 1 ?>
                                </option>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="current_position" value="<?= $i + 1 ?>">
                            <input type="hidden" name="action" value="update">
                            <input type="submit" value="change" class="btn btn-primary">
                        </div>
                    </form>
                    <form class="m-1 p-1" method="post">
                        <input type="hidden" name="name" value="<?= $images[$i] ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="submit" class="btn btn-danger" value="delete">
                    </form>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>