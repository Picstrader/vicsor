<?php

function likeImagePrepare($set, $image_id, $rate) {
    $rate = $rate ? 1 : -1;
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    foreach ($players as $key => $player) {
        $player_data = explode(':', $player);
        if ((int) $player_data[1] == (int) $image_id) {
            $player_data[2] = ((int) $player_data[2]) + $rate;
            $player = implode(':', $player_data);
            $players[$key] = $player;
            break;
        }
    }
    return implode(';', $players);
}
function getImagesLikes($set, $image) {
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    foreach($players as $player) {
        $player_data = explode(':', $player);
        if((int) $player_data[1] == (int) $image) {
            return $player_data[2];
        }
    }
    return 0;
}
function getSetImagesIdString($set)
{
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    if(!$players) {
        return '0';
    }
    $images_id = [];
    foreach ($players as $player) {
        $player_data = explode(':', $player);
        array_push($images_id, $player_data[1]);
    }
    return implode(',', $images_id);
}
function usersInSet($set)
{
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    $occupancy = count($players);
    return $occupancy;
}
function isFullSet($set)
{
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    if (count($players) === (int) $set['total_photos']) {
        return true;
    } else {
        return false;
    }
}

function isUserInSet($set, $user_id)
{
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    foreach ($players as $player) {
        $player_data = explode(':', $player);
        if ((int) $player_data[0] == (int) $user_id) {
            return true;
        }
    }
    return false;
}
function isHashtagInSetImages($set, $searched_hashtag)
{
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    $searched_hashtag = strtolower($searched_hashtag);
    foreach ($players as $player) {
        $player_data = explode(':', $player);
        $hashtags = getImageHashtags($player_data[1]);
        foreach ($hashtags as $hashtag) {
            if ($hashtag['name'] == $searched_hashtag) {
                return true;
            }
        }
    }
    return false;
}

function makeFakeWin($set, $images, $min_likes)
{
    $max_likes = 0;
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    foreach ($players as $player) {
        $player_data = explode(':', $player);
        if ((int) $player_data[2] > $max_likes) {
            $max_likes = $player_data[2];
        }
    }
    if ($max_likes < $min_likes) {
        $max_likes = $min_likes;
    }
    foreach ($players as &$player) {
        $player_data = explode(':', $player);
        if (in_array((int) $player_data[1], $images)) {
            $player_data[2] = $max_likes + 1;
            $player = implode(':', $player_data);
        }
    }
    $set['users_photos'] = implode(';', $players);
    return $set['users_photos'];
}

function checkFakeWinParams($set, $images)
{
    $images_in_set = 0;
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    foreach ($players as $player) {
        $player_data = explode(':', $player);
        if (in_array((int) $player_data[1], $images)) {
            $images_in_set++;
        }
    }
    if ($images_in_set != $set['pur_photos']) {
        return false;
    }
    return true;
}

function gameFakeEnd($set)
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
        if (count($user) <= 0) {
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
?>