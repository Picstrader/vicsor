<?php
session_start();
// ini_set('display_errors', 0);
include_once 'config.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/multilang.php';
include_once 'helpers/setParsing.php';
include_once 'helpers/Statistics.php';
if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
    $action = $_POST['action'];
    switch ($action) {
        case 'upload':
            if (!isLogin()) {
                break;
            }
            try {
                $fields['image_name_original'] = FileCommander::upload_image();
                $fields['image_name'] = FileCommander::create_watermark_image($fields['image_name_original']);
                $fields['image_name_thumbnail'] = FileCommander::create_thumbnail_image($fields['image_name']);
            } catch (\Throwable $th) {
                //throw $th;
                $fields = [];
            }
            if ($fields['image_name']) {
                $fields['user_id'] = getLoginUserId();
                addUserImage($fields);
                http_response_code(200);
                include './inc/template/trade-slider.php';
            } else {
                $respond = [];
                $respond['message'] = Validation::$errors[$_SESSION['error_type']]['message'];
                $json = json_encode($respond);
                http_response_code(400);
                echo $json;
            }
            break;
        case 'delete':
            if (!isLogin()) {
                break;
            }
            $fields['image_id'] = $_POST['id'];
            $fields['image_status'] = $_POST['status'];
            $fields['user_id'] = getLoginUserId();
            if ($fields['image_status'] === 'trading') {
                break;
            }
            $image_name_data = getUserImageName($fields);
            if (isset($image_name_data[0]['name'])) {
                $image_name = $image_name_data[0]['name'];
                if ($image_name != '') {
                    removePreFavoritesOfImage($fields);
                    deleteImageActions($fields['image_id']);
                    $is_deleted = deleteUserImage($fields);
                    if ($is_deleted) {
                        $full_path = 'inc/assets/img/' . $image_name;
                        if (FileCommander::is_image($full_path))
                            unlink($full_path);
                    }
                }
            }
            if (isset($image_name_data[0]['name_original'])) {
                $image_name = $image_name_data[0]['name_original'];
                if ($image_name != '') {
                    $full_path = 'inc/assets/img/' . $image_name;
                    if (FileCommander::is_image($full_path))
                        unlink($full_path);
                }
            }
            if (isset($image_name_data[0]['name_thumbnail'])) {
                $image_name = $image_name_data[0]['name_thumbnail'];
                if ($image_name != '') {
                    $full_path = 'inc/assets/img/' . $image_name;
                    if (FileCommander::is_image($full_path))
                        unlink($full_path);
                }
            }
            http_response_code(200);
            include './inc/template/trade-slider.php';
            break;
        case 'update_sets':
            $filters = [];
            $filters['cost'] = filter_input(INPUT_POST, 'cost');
            $filters['photos'] = filter_input(INPUT_POST, 'photos');
            $filters['purchasable'] = filter_input(INPUT_POST, 'purchasable');
            $searched_hashtag = filter_input(INPUT_POST, 'hashtag');
            $page = $page_demo = (int) filter_input(INPUT_POST, 'page');
            $update_all = (bool) ((int) filter_input(INPUT_POST, 'update_all'));
            http_response_code(200);
            if ($_POST['demo']) {
                include './inc/template/trade-demo-sets.php';
            } else {
                include './inc/template/trade-sets.php';
            }
            break;
        case 'update_create_button':
            $filters = [];
            $filters['cost'] = filter_input(INPUT_POST, 'cost');
            $filters['photos'] = filter_input(INPUT_POST, 'photos');
            $filters['purchasable'] = filter_input(INPUT_POST, 'purchasable');
            $searched_hashtag = filter_input(INPUT_POST, 'hashtag');
            $is_new_set = false;
            $is_all_params = false;
            $is_user_in_set = false;
            $existing_set_data = getSet($filters);
            $existing_set = [];
            foreach ($existing_set_data as $item_set) {
                if (isFullSet($item_set)) {
                    continue;
                } else {
                    array_push($existing_set, $item_set);
                    break;
                }
            }
            $existing_set_data = $existing_set;
            if (count($existing_set_data) > 0) {
                $existing_set = $existing_set_data[0];
            }
            if (isLogin()) {
                if (count($existing_set_data) > 0) {
                    if (isUserInSet($existing_set, getLoginUserId()) && !isFullSet($existing_set)) {
                        $is_user_in_set = true;
                    }
                }
            }
            if (count($existing_set_data) <= 0) {
                $new_set = [
                    'users_photos' => '',
                    'cost' => (float) $filters['cost'],
                    'pur_photos' => (int) $filters['purchasable'],
                    'total_photos' => (int) $filters['photos'],
                    'time' => 0
                ];
                $existing_set = $new_set;
                $is_new_set = true;
            }
            $existing_set['profit_usdt'] = ECommerceLogic::getProfitUSDT($existing_set);
            $existing_set['profit_percent'] = ECommerceLogic::getProfitPercent($existing_set);
            if (
                ($filters['cost'] !== '') && ($filters['purchasable'] !== '') && ($filters['photos'] !== '') &&
                ((int) $filters['cost'] > 0) && ((int) $filters['purchasable'] > 0) && ((int) $filters['photos'] > 0)
            ) {
                $is_all_params = true;
            } else {
                $is_all_params = false;
            }
            $existing_set['is_all_params'] = $is_all_params;
            $existing_set['is_user_in_set'] = $is_user_in_set;
            $existing_set['in_set'] = false;
            if ($is_user_in_set) {
                $existing_set['lab'] = $fs['You are in lot'];
            } else if (!$is_new_set) {
                $existing_set['lab'] = 'Join lot';
            } else if ($is_all_params) {
                $existing_set['lab'] = 'Create lot';
            } else {
                $existing_set['lab'] = 'Enter params';
            }
            http_response_code(200);
            $json = json_encode($existing_set);
            echo $json;
            break;
        case 'create_set':
            if (!isLogin()) {
                break;
            }
            // if (!ECommerceLogic::checkSubscription()) {
            //     $answer = [];
            //     $answer['status'] = false;
            //     $answer['message'] = $fs['You dont have the subscription'];
            //     $json = json_encode($answer);
            //     http_response_code(400);
            //     echo $json;
            //     break;
            // }
            $fields = [];
            $fields['cost'] = abs((float) filter_input(INPUT_POST, 'cost'));
            $fields['photos'] = abs((int) filter_input(INPUT_POST, 'photos'));
            $fields['purchasable'] = abs((int) filter_input(INPUT_POST, 'purchasable'));
            $fields['time'] = /*abs((float) filter_input(INPUT_POST, 'time'))*/24;
            $fields['image_id'] = $image_id = (int) filter_input(INPUT_POST, 'image_id');
            $fields['hashtags'] = filter_input(INPUT_POST, 'hashtags');
            if (!Validation::check_out_of_range_number($fields['cost'])) {
                http_response_code(400);
                break;
            }
            if (!Validation::check_out_of_range_number($fields['photos'])) {
                http_response_code(400);
                break;
            }
            if (!Validation::check_out_of_range_number($fields['purchasable'])) {
                http_response_code(400);
                break;
            }
            $hashtags = json_decode($fields['hashtags']);
            foreach ($hashtags as $hashtag) {
                if (!Validation::check_out_of_range_string($hashtag)) {
                    http_response_code(400);
                    break 2;
                }
            }
            $fields['user_id'] = $user_id = getLoginUserId();
            if ($fields['purchasable'] > $fields['photos']) {
                http_response_code(400);
                echo 'error purchasable';
                break;
            }
            if ((int) $fields['time'] > 168 || (float) $fields['time'] <= /*1*/0) {
                http_response_code(400);
                echo $fields['time'];
                echo 'time must be from 1 to 168 11';
                break;
            }
            $image = getUserImage($fields);
            if (count($image) != 0) {
                $image = $image[0];
                if ($image['status'] !== 'ready') {
                    http_response_code(400);
                    echo 'image is not ready for trading';
                    break;
                }
            } else {
                http_response_code(400);
                echo 'image was not found';
                break;
            }
            $user = getUserBalance($fields);
            if (count($user) == 0) {
                http_response_code(400);
                echo 'user does not exist';
                break;
            }
            $user = $user[0];
            if ((float) $user['balance'] < (float) $fields['cost']) {
                $answer = [];
                $answer['status'] = false;
                $answer['message'] = $fields['cost'];
                $json = json_encode($answer);
                http_response_code(409);
                echo $json;
                break;
            }
            $existing_sets = getSet($fields);
            $existing_set = [];
            foreach ($existing_sets as $item_set) {
                if (isFullSet($item_set)) {
                    continue;
                } else {
                    if ((int) $item_set['fiction']) {
                        deleteFictionSet($item_set['id']);
                    } else {
                        array_push($existing_set, $item_set);
                    }
                    break;
                }
            }
            if (count($existing_set) != 0) {
                $existing_set = $existing_set[0];
                $players = $existing_set['users_photos'] === '' ? [] : explode(';', $existing_set['users_photos']);
                $user['balance_old'] = $user['balance'];
                $user['balance'] = (float) $user['balance'] - (float) $fields['cost'];
                $user['image_id'] = $fields['image_id'];
                $respond_purchase = changeUserBalance($user);
                foreach ($players as $player) {
                    $player_data = explode(':', $player);
                    if ((int) $player_data[0] == (int) $user_id) {
                        http_response_code(400);
                        echo 'user already in set';
                        break 2;
                    }
                    if ((int) $player_data[1] == (int) $image_id) {
                        http_response_code(400);
                        echo 'image already in set';
                        break 2;
                    }
                }
                $current_likes = (int) getAllConsiderLikesOfImage($image_id)[0]['likes'];
                $current_dislikes = (int) getAllConsiderDislikesOfImage($image_id)[0]['dislikes'];
                $player = [];
                array_push($player, $user_id);
                array_push($player, $image_id);
                array_push($player, $current_likes);
                array_push($player, $current_dislikes);
                array_push($players, implode(':', $player));
                $fields['users_photos'] = implode(';', $players);
                $fields['set_id'] = $existing_set['id'];
                $fields['time'] = ((int) $fields['time']) * 60 * 60;
                $respond = addUserToSet($fields);
                $user['set_id'] = $fields['set_id'];
                $user['cost'] = $fields['cost'];
                $user['photos'] = $fields['photos'];
                $user['purchasable'] = $fields['purchasable'];
                ECommerceLogic::updateBalance();
                if ($respond_purchase < 1) {
                    ECommerceLogic::addBalanceLog($user, 'balance', 0, 'Sending photo in set');
                    http_response_code(400);
                    echo 'failed to get purchasment';
                    break;
                } else {
                    ECommerceLogic::addBalanceLog($user, 'balance', 1, 'Sending photo in set');
                }
            } else {
                $user['balance_old'] = $user['balance'];
                $user['balance'] = (float) $user['balance'] - (float) $fields['cost'];
                $user['image_id'] = $fields['image_id'];
                $respond_purchase = changeUserBalance($user);
                $current_likes = (int) getAllConsiderLikesOfImage($image_id)[0]['likes'];
                $current_dislikes = (int) getAllConsiderDislikesOfImage($image_id)[0]['dislikes'];
                $player = [];
                array_push($player, $user_id);
                array_push($player, $image_id);
                array_push($player, $current_likes);
                array_push($player, $current_dislikes);
                $fields['users_photos'] = implode(':', $player);
                if ((int) $fields['time'] > 168 || (int) $fields['time'] < 1) {
                    http_response_code(400);
                    echo 'time must be from 1 to 168 22';
                    break;
                }
                $fields['time'] = ((int) $fields['time']) * 60 * 60;
                $respond = createSet($fields);
                $fields['set_id'] = $respond;
                $user['set_id'] = $fields['set_id'];
                $user['cost'] = $fields['cost'];
                $user['photos'] = $fields['photos'];
                $user['purchasable'] = $fields['purchasable'];
                ECommerceLogic::updateBalance();
                if ($respond_purchase < 1) {
                    ECommerceLogic::addBalanceLog($user, 'balance', 0, 'Sending photo in set');
                    http_response_code(400);
                    echo 'failed to get purchasment';
                    break;
                } else {
                    ECommerceLogic::addBalanceLog($user, 'balance', 1, 'Sending photo in set');
                }
            }
            if ($respond) {
                $fields['image_status'] = 'trading';
                changeImageStatus($fields);
                $hashtags = json_decode($fields['hashtags']);
                foreach ($hashtags as $hashtag) {
                    $hashtag = prepareHashtag($hashtag);
                    if ($hashtag === '')
                        continue;
                    $exist_hash = getHashtagByName($hashtag);
                    if (count($exist_hash) == 0) {
                        addHashtag($hashtag);
                    }
                    $hashtag_data = getHashtagByName($hashtag);
                    if (count($hashtag_data) > 0) {
                        $res2 = addHashtagToImage($fields['image_id'], $hashtag_data[0]['id']);
                    }
                }
                $statistics = [];
                $statistics = $fields;
                $statistics['action'] = 'create/join lot';
                saveStatistics($statistics);
            } else {
            }
            http_response_code(200);
            include './inc/template/trade-sets.php';
            break;
        case 'my_set':
            if (!isLogin()) {
                echo '';
                break;
            }
            http_response_code(200);
            include './inc/template/trade-my-sets.php';
            break;
        case 'images':
            if (!isLogin()) {
                break;
            }
            http_response_code(200);
            include './inc/template/trade-slider.php';
            break;
        case 'wallets':
            break;
        case 'check_won':
            if (!isLogin()) {
                break;
            }
            $wins = getUserWins(getLoginUserId());
            if (count($wins) > 0) {
                $wins = $wins[0];
                deleteUserWins($wins['id']);
                $bg_data = getImageNameOriginal(['id' => $wins['bg']]);
                if (count($bg_data) > 0) {
                    $wins['bg_name'] = $bg_data[0]['name_original'];
                } else {
                    $wins['bg_name'] = null;
                }
                http_response_code(200);
                include './inc/template/winner-popap.php';
            }
            break;
        case 'popular_hashtags':
            $fields = [];
            $fields['search'] = filter_input(INPUT_POST, 'search');
            $hashtags = getPopularSameHashtags($fields);
            $json = json_encode($hashtags);
            http_response_code(200);
            echo $json;
            break;
        case 'get_balance':
            if (!isLogin()) {
                $answer = [];
                $answer['status'] = false;
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
                break;
            }
            $fields = [];
            $fields['user_id'] = getLoginUserId();
            $balance = getUserBalance($fields);
            if (count($balance) > 0) {
                $balance = $balance[0];
                setLoginUserBalance($balance['balance']);
                $answer = [];
                $answer['status'] = true;
                $answer['balance'] = getLoginUserBalance();
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
            } else {
                $answer = [];
                $answer['status'] = false;
                $json = json_encode($answer);
                http_response_code(200);
                echo $json;
            }
            break;
        case 'background_update_images':
            if (isLogin()) {
                $images = getUserImages(getLoginUserId());
            } else {
                $images = [];
            }
            $answer = [];
            $answer['status'] = true;
            $answer['images'] = $images;
            $json = json_encode($answer);
            http_response_code(200);
            echo $json;
            break;
        case 'user_images':
            include './inc/template/trade-modal-user-images.php';
            break;
        case 'chosen_image':
            include './inc/template/trade-modal-chosen-image.php';
            break;
        case 'add_to_lot':
            $image = getImages(null, null, 'ready', getLoginUserId(), $_POST['image_id'], null)[0];
            if (!$image) {
                response(200, false, 'no_image');
                break;
            }
            $set = getSets(null, null, null, 1, 1, 'active', getLoginUserId(), $_POST['set_id'])[0];
            if (!$set) {
                response(200, false, 'set_full');
                break;
            }
            $balance = getUserBalance(['user_id' => getLoginUserId()])[0];
            if (!$balance) {
                response(200, false, 'login');
                break;
            }
            if ((float) $balance['balance'] < (float) $set['cost']) {
                response(200, false, 'no_money');
                break;
            }
            $balance['balance_old'] = $balance['balance'];
            $balance['balance'] = round((float) $balance['balance'] - (float) $set['cost'], 2);
            $res = changeUserBalance($balance);
            ECommerceLogic::addBalanceLog($balance, 'balance', $res ? 1 : 0, 'Photo purchased');
            if(!$res) {
                response(200, false, 'balance_not_change');
                break;
            }
            $res = updateSet(getLoginUserId(), $image['id'], $set['users_photos'], $set['id']);
            if(!$res) {
                response(200, false, 'add_to_set');
                break;
            }
            updateImage($image['id'], getLoginUserId(), $image['price'], 'trading');
            if(usersInSet($set) + 1 >= $set['total_photos']) {
                createSet($set['cost'], $set['total_photos'], $set['pur_photos'], 3600);
            }
            response(200, true);
            break;
        case 'like_image':
            $rate = 1;
            $action = getTradeAction($_POST['image_id'], getLoginUserId(), $_POST['set_id'])[0];
            if ($action) {
                $rate = ((int) $action['rate']) ? 0 : 1;
                updateTradeAction($action['id'], $rate);
            } else {
                createTradeAction($_POST['image_id'], getLoginUserId(), $_POST['set_id'], $rate);
            }
            $set = getSets(null, null, null, null, null, 'all', null, $_POST['set_id'])[0];
            $res = updateSet(null, null, likeImagePrepare($set, $_POST['image_id'], $rate), $set['id']);
            $data = [];
            $data['status'] = $res ? true : false;
            $data['likes'] = (int) getImagesLikes($set, $_POST['image_id']) + ($rate ? 1 : -1);
            $json = json_encode($data);
            http_response_code(200);
            echo $json;
            break;
        case 'previous_user_images':
            if($_POST['type'] == 'back') {
                $page = (int) $_POST['page'] - 1;
            } else if($_POST['type'] == 'next') {
                $page = (int) $_POST['page'] + 1;
            }
            include './inc/template/trade-modal-user-images.php';
            break;
    }
}

function prepareHashtag($hashtag)
{

    for ($i = 0; $i < strlen($hashtag); $i++) {
        if ($hashtag[$i] === '#') {
            continue;
        } else {
            return substr($hashtag, $i);
        }
    }
    return '';
}

function response($code, $status = null, $error_type = null)
{
    $res = [];
    if (!is_null($status)) {
        $res['status'] = $status;
    }
    if (!is_null($error_type)) {
        $res['error_type'] = $error_type;
    }
    $json = json_encode($res);
    http_response_code($code);
    echo $json;
}