<?php
session_start();
include_once 'config.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/multilang.php';
include_once 'helpers/Statistics.php';
if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
    $action = $_POST['action'];
    switch ($action) {
        case 'search_sets':
            $filters = [];
            $filters['search'] = filter_input(INPUT_POST, 'search');
            http_response_code(200);
            include './inc/template/rate-sets.php';
            break;
        case 'load_images':
            $_SESSION['shown_images'] = [];
            $filters = [];
            $filters['search'] = filter_input(INPUT_POST, 'search');
            $shown_images = count($_SESSION['shown_images']) > 0 ? implode(',', $_SESSION['shown_images']) : '0';
            if(isLogin()) {
                $images = getRateImages($shown_images, 5);
            } else {
                $images = getRateImagesUnlogged($shown_images, 5, getUserIP());
            }
            foreach($images as $img) {
                array_push($_SESSION['shown_images'], $img['id']);
            }
            $json = json_encode($images);
            http_response_code(200);
            echo $json;
            break;
        case 'load_image':
            $fields = [];
            $filters = [];
            $statistics = [];
            $fields['image_id'] = filter_input(INPUT_POST, 'image_id');
            $fields['dislike'] = filter_input(INPUT_POST, 'dislike');
            $fields['rate'] = filter_input(INPUT_POST, 'rate');
            $fields['next'] = filter_input(INPUT_POST, 'next');
            $fields['user_id'] = getLoginUserId();
            if(!isLogin()) {
                $fields['user_ip'] = getUserIP();
            }
            $filters['search'] = filter_input(INPUT_POST, 'search');
            if ((int) $fields['rate'] != 0 && (int) $fields['rate'] != 1) {
                if((int) $fields['rate'] < 0) {
                    $fields['rate'] = 0;
                } else {
                    $fields['rate'] = 1;
                }
            }
            if (!isLogin() && !((int) $fields['next'])) {
                $statistics = $fields;
                $rated_image = getUserRateOfThisImageUnlogged($fields);
                if (count($rated_image) > 0) {
                    $fields['old_rate'] = $rated_image[0]['rate'];
                    updateUserRateOfThisImageUnlogged($fields);
                } else {
                    setUserRateOfThisImageUnlogged($fields);
                }
                // if (isConsiderVoiceUser()) {
                //     $set = getSetOfImage($fields['image_id'])[0];
                //     $fields['set_id'] = $set['id'];
                //     rateImage($fields, $set);
                //     if($set) {
                //         $statistics = array_merge($statistics, $set);
                //         $statistics['action'] = (int) $statistics['dislike'] == 1 ? 'dislike' : 'like';
                //         saveStatistics($statistics);
                //     }
                // }
                if($set) {
                    checkGameEndOfSet($set);
                }
            } else if (isLogin() && !((int) $fields['next'])) {
                $statistics = $fields;
                $rated_image = getUserRateOfThisImage($fields);
                if (count($rated_image) > 0) {
                    $fields['old_rate'] = $rated_image[0]['rate'];
                    updateUserRateOfThisImage($fields);
                } else {
                    setUserRateOfThisImage($fields);
                }
                if (isConsiderVoiceUser()) {
                    $set = getSetOfImage($fields['image_id'])[0];
                    $fields['set_id'] = $set['id'];
                    rateImage($fields, $set);
                    if($set) {
                        $statistics = array_merge($statistics, $set);
                        $statistics['action'] = (int) $statistics['dislike'] == 1 ? 'dislike' : 'like';
                        saveStatistics($statistics);
                    }
                }
                if($set) {
                    checkGameEndOfSet($set);
                }
            }
            $shown_images = count($_SESSION['shown_images']) > 0 ? implode(',', $_SESSION['shown_images']) : '0';
            if($filters['search'] == '') {
                $images = getRateImages($shown_images, 1);
            } else {
                $images = getRateImagesByFilters($shown_images, 1, $filters['search']);
            }
            foreach($images as $img) {
                array_push($_SESSION['shown_images'], $img['id']);
            }
            $json = json_encode($images);
            http_response_code(200);
            echo $json;
            break;
        case 'back_image':
            if ( /*$_SESSION['move_back']*/true) {
                $_SESSION['move_back'] = false;
                $answer = [];
                $answer['status'] = true;
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
        case 'pagination_sets':
            $page = (int) filter_input(INPUT_POST, 'page');
            http_response_code(200);
            include './inc/template/rate-sets.php';
            break;
        case 'send_complain':
            $fields['image_id'] = filter_input(INPUT_POST, 'image_id');
            $fields['set_id'] = filter_input(INPUT_POST, 'set');
            $fields['type'] = filter_input(INPUT_POST, 'complain');
            $fields['user_id'] = isLogin() ? getLoginUserId() : 0;
            $fields['owner_id'] = getOwnerOfImage($fields);
            $fields['owner_id'] = $fields['owner_id'][0]['user_id'];
            addComplain($fields);
            break;
        case 'check_favorite':
            if (!isLogin()) {
                $answer = [];
                $answer['status'] = false;
                $json = json_encode($answer);
                http_response_code(400);
                echo $json;
                break;
            }
            $fields = [];
            $fields['user_id'] = getLoginUserId();
            $fields['image_id'] = filter_input(INPUT_POST, 'image_id');
            $prefavorite = getPreFavoriteImage($fields);
            if (count($prefavorite) > 0) {
                $answer = [];
                $answer['status'] = true;
            } else {
                $answer = [];
                $answer['status'] = false;
            }
            $json = json_encode($answer);
            http_response_code(200);
            echo $json;
            break;
        case 'set_favorite':
            if (!isLogin()) {
                $answer = [];
                $answer['status'] = false;
                $json = json_encode($answer);
                http_response_code(400);
                echo $json;
                break;
            }
            $fields = [];
            $fields['user_id'] = getLoginUserId();
            $fields['image_id'] = filter_input(INPUT_POST, 'image_id');
            $this_image = checkImageTrading($fields);
            if (count($this_image) <= 0) {
                $answer = [];
                $answer['status'] = false;
                $json = json_encode($answer);
                http_response_code(409);
                echo $json;
                break;
            }
            $respond = addToPreFavorite($fields);
            if ($respond) {
                $answer = [];
                $answer['status'] = true;
            } else {
                $answer = [];
                $answer['status'] = false;
            }
            $json = json_encode($answer);
            http_response_code(200);
            echo $json;
            break;
        case 'set_unfavorite':
            if (!isLogin()) {
                $answer = [];
                $answer['status'] = false;
                $json = json_encode($answer);
                http_response_code(400);
                echo $json;
                break;
            }
            $fields = [];
            $fields['user_id'] = getLoginUserId();
            $fields['image_id'] = filter_input(INPUT_POST, 'image_id');
            $respond = removeFromPreFavorite($fields);
            if ($respond) {
                $answer = [];
                $answer['status'] = true;
            } else {
                $answer = [];
                $answer['status'] = false;
            }
            $json = json_encode($answer);
            http_response_code(200);
            echo $json;
            break;
    }
}

function rateImage($fields, $set)
{
    if(!(int)$set['id'] || (int) $set['fiction']) {
        return;
    }
    $players = explode(';', $set['users_photos']);
    foreach ($players as $key => $player) {
        $player_data = explode(':', $player);
        if ((int) $player_data[1] == (int) $fields['image_id']) {
            if (isset($fields['old_rate'])) {
                if((int) $fields['old_rate']) {
                    $player_data[2] = (int) $player_data[2] - 1;
                } else {
                    $player_data[3] = (int) $player_data[3] - 1;
                }
            }
            if ((int) $fields['dislike']) {
                $player_data[3] = (int) $player_data[3] + 1;
            } else {
                $player_data[2] = (int) $player_data[2] + 1;
            }
            $player = implode(':', $player_data);
            $players[$key] = $player;
            break;
        }
    }
    $set['users_photos'] = implode(';', $players);
    $fields['users_photos'] = $set['users_photos'];
    likeImage($fields);
}

function isConsiderVoiceUser()
{
    $voice = (int) getUserVoice(getLoginUserId())[0]['voice'];
    return (bool) $voice;
}

function checkGameEndOfSet($set)
{
    if ((int) $set['fiction']) {
        return; //for test lots
    }
    $params_end = getLotParams();
    $params_end = isset($params_end[0]['amount']) ? $params_end[0]['amount'] : 3;
    $players = explode(';', $set['users_photos']);
    if (count($players) != (int) $set['total_photos']) {
        return;
    }
    foreach ($players as $player) {
        $player_data = explode(':', $player);
        if (!isset($player_data[2])) {
            $player_data[2] = 0;
        }
        if (!isset($player_data[3])) {
            $player_data[3] = 0;
        }
        $count_reactions = (int) $player_data[2] + (int) $player_data[3];
        if ($count_reactions < (int) $params_end) {
            return;
        }
    }
    gameEnd($set);
}

function gameEnd($set)
{
    $players = explode(';', $set['users_photos']);
    usort($players, function ($player1, $player2) {
        $player_data1 = explode(':', $player1);
        $player_data2 = explode(':', $player2);
        if ((int) $player_data1[2] < (int) $player_data2[2]) {
            return 1;
        }
        if ((int) $player_data1[2] == (int) $player_data2[2]) {
            return 0;
        }
        if ((int) $player_data1[2] > (int) $player_data2[2]) {
            return -1;
        }
    });
    $profit_usdt = ECommerceLogic::getProfitUSDT($set);
    for ($i = 0; $i < (int) $set['pur_photos']; $i++) {
        $player_data = explode(':', $players[$i]);
        $user = getUserBalance(['user_id' => $player_data[0]]);
        if(count($user) <= 0) {
            continue;
        }
        $user = $user[0];
        $user['balance_old'] = $user['balance'];
        $user['balance'] = (float) $user['balance'] + (int) $set['cost'] + $profit_usdt;
        $user['set_id'] = $set['id'];
        $user['cost'] = $set['cost'];
        $user['photos'] = $set['total_photos'];
        $user['purchasable'] = $set['pur_photos'];
        $user['image_id'] = $player_data[1];
        $respond_purchase = changeUserBalance($user);
        if ($respond_purchase) {
            ECommerceLogic::addBalanceLog($user, 'balance', 1, 'won game');
        } else {
            ECommerceLogic::addBalanceLog($user, 'balance', 0, 'won game');
        }
        ECommerceLogic::updateBalance();
        addToWinnersPopap(['user_id' => $player_data[0], 'profit' => $profit_usdt, 'win' => 1, 'bg' => $player_data[1]]);
    }
    //calculateSiteBenefit($set);
    $all_images = [];
    $winnig_images = [];
    $losing_users = [];
    $winners = array_slice($players, 0, (int) $set['pur_photos']);
    foreach ($winners as $winner) {
        $winner_data = explode(':', $winner);
        $fields = [];
        $fields['user_id'] = $winner_data[0];
        $fields['image_id'] = $winner_data[1];
        array_push($all_images, $winner_data[1]);
        //unsetUserOfImage($fields);
        array_push($winnig_images, $fields['image_id']);
        $set['winner_image'] = $fields['image_id'];
        //$set['likes'] = $winner_data[2];
        $set['likes'] = getAllLikesOfImage($fields['image_id'])[0]['likes'];
        //addImageToGallery($set);
        addImageToSpecialAccount($set);
    }
    $losers = array_slice($players, (int) $set['pur_photos']);
    foreach ($losers as $loser) {
        $loser_data = explode(':', $loser);
        $fields = [];
        $fields['user_id'] = $loser_data[0];
        $fields['image_id'] = $loser_data[1];
        array_push($all_images, $loser_data[1]);
        $fields['image_status'] = 'ready';
        array_push($losing_users, $fields['user_id']);
        deleteImageHashtags($fields);
        changeImageStatus($fields);
        $user = getUserBalance($fields);
        if(count($user) <= 0) {
            continue;
        }
        $user = $user[0];
        $user['balance_old'] = $user['balance'];
        $user['set_id'] = $set['id'];
        $user['cost'] = $set['cost'];
        $user['photos'] = $set['total_photos'];
        $user['purchasable'] = $set['pur_photos'];
        ECommerceLogic::addBalanceLog($user, 'balance', 1, 'lost game');
        addToWinnersPopap(['user_id' => $loser_data[0], 'profit' => '', 'win' => 0, 'bg' => $loser_data[1] /*getImageForLoseModal($winnig_images)*/]);
    }
    $purchase_data = [];
    $purchase_data['winnig_images'] = $winnig_images;
    $purchase_data['losing_users'] = $losing_users;
    //makePurchaseOfWonImages($purchase_data);
    $all_images = count($all_images) > 0 ? implode(',', $all_images) : '0';
    deleteImagesActions($all_images);
    deleteSetById($set['id']);
}

function makePurchaseOfWonImages($purchase_data)
{
    foreach ($purchase_data['winnig_images'] as $image_id) {
        $image = getImageName(['id' => $image_id]);
        if (count($image) <= 0) {
            continue;
        }
        $part = 1;
        $fields = [];
        $fields['user_id'] = COMPANY_ACCOUNT_ID;
        $fields['image_id'] = $image_id;
        $fields['part'] = $part;
        purchaseWonImagePart($fields);
        // foreach ($purchase_data['losing_users'] as $user_id) {
        //     $part = 1 / count($purchase_data['losing_users']);
        //     $part = round($part, 4);
        //     $fields = [];
        //     $fields['user_id'] = $user_id;
        //     $fields['image_id'] = $image_id;
        //     $fields['part'] = $part;
        //     purchaseWonImagePart($fields);
        // }
    }
}


function getImageForLoseModal($winnig_images)
{
    $image_index = array_rand($winnig_images);
    if (isset($winnig_images[$image_index])) {
        return $winnig_images[$image_index];
    }
    return 0;
}

function calculateSiteBenefit($set)
{
    $fields = [];
    $fields['description'] = 'Commission per set';
    $fields['percentage'] = 10;
    $fields['user_id'] = 0;
    $fields['amount'] = ECommerceLogic::getSiteBenefit($set);
    $res = setSiteBenefit($fields['amount']);
    if ($res) {
        createSiteLog($fields);
    }
}

function getUserIP() {
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}