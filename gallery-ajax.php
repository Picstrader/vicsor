<?php
session_start();
include_once 'config.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/multilang.php';
include_once 'helpers/Validation.php';
include_once 'helpers/Statistics.php';
if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
    $action = $_POST['action'];
    switch ($action) {
        case 'open_image':
            $purchased_image_id = filter_input(INPUT_POST, 'image_id');
            http_response_code(200);
            include './inc/template/gallery-modal.php';
            break;
        case 'buy_image':
            break;
        case 'add_favorite':
            if (!isLogin()) {
                http_response_code(400);
                break;
            }
            $fields = [];
            $fields['gallery_image_id'] = filter_input(INPUT_POST, 'gallery_image_id');
            $fields['user_id'] = getLoginUserId();
            $respond = addToFavorite($fields);
            if ($respond) {
                $answer = [];
                $answer['status'] = true;
                $answer['style'] = "background-image: url(/inc/assets/img/favorite-not-empty.svg);";
            } else {
                $answer = [];
                $answer['status'] = false;
            }
            $json = json_encode($answer);
            http_response_code(200);
            echo $json;
            break;
        case 'remove_favorite':
            if (!isLogin()) {
                http_response_code(400);
                break;
            }
            $fields = [];
            $fields['gallery_image_id'] = filter_input(INPUT_POST, 'gallery_image_id');
            $fields['user_id'] = getLoginUserId();
            $respond = removeFromFavorite($fields);
            if ($respond) {
                $answer = [];
                $answer['status'] = true;
                $answer['style'] = "background-image: url(/inc/assets/img/favoritemodal.svg);";
            } else {
                $answer = [];
                $answer['status'] = false;
            }
            $json = json_encode($answer);
            http_response_code(200);
            echo $json;
            break;
        case 'update_favorite':
            $favorited_images = getGalleryAmountFavorite(getLoginUserId());
            $favorited_images = (int) $favorited_images[0]['amount'];
            $answer = [];
            $answer['status'] = true;
            $answer['amount'] = $favorited_images;
            $json = json_encode($answer);
            http_response_code(200);
            echo $json;
            break;
    }
}