<?php
function getAllLikesOfImage($image)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image = (int) $image;
        $query = "SELECT COUNT(*) as likes FROM trade_actions WHERE image_id=$image AND rate=1";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllDislikesOfImage($image)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image = (int) $image;
        $query = "SELECT COUNT(*) as dislikes FROM trade_actions WHERE image_id=$image AND rate=0";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllConsiderLikesOfImage($image)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image = (int) $image;
        $query = "SELECT COUNT(*) as likes FROM trade_actions as TA WHERE rate=1 AND image_id=$image AND EXISTS (SELECT 1 FROM users WHERE id=TA.user_id AND voice=1)";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllConsiderDislikesOfImage($image)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image = (int) $image;
        $query = "SELECT COUNT(*) as dislikes FROM trade_actions as TA WHERE rate=0 AND image_id=$image AND EXISTS (SELECT 1 FROM users WHERE id=TA.user_id AND voice=1)";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
function getUserImages($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT id, name, name_thumbnail, status FROM images WHERE user_id=$id AND status<>'winning'";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserImagesAdmin($id, $page, $per_page)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $page = (int) $page ? (int) $page : 1;
        $per_page = (int) $per_page;
        $row = ($page - 1) * $per_page;
        $query = "SELECT id, name, name_thumbnail, status FROM images WHERE user_id=$id AND (status='ready' OR status='moderating') LIMIT $row,$per_page";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserImagesAmount($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT count(*) as amount FROM images WHERE user_id=$id AND status<>'winning'";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserImageName($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "SELECT name, name_original, name_thumbnail FROM images WHERE images.id=$image_id AND images.user_id=$user_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserImagesToDelete($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT id, name, name_thumbnail, status FROM images WHERE user_id=$id AND (status='ready' OR status='moderating')";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getImageToDelete($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "SELECT * FROM images WHERE id=$image_id AND user_id=$user_id AND (status='trading' OR status='moderating');";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllImages()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT id, name, name_original, name_thumbnail FROM images;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getRateImages($shown_images, $limit)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $limit = (int) $limit;
        $current_user = (int) getLoginUserId();
        $shown_images = mysqli_real_escape_string($db, $shown_images);
        $query = "SELECT id, status, name, name_thumbnail FROM images as I WHERE (status='ready' OR status='trading') AND (user_id is not NULL AND user_id<>$current_user) AND id NOT IN ($shown_images) AND NOT EXISTS (SELECT 1 FROM trade_actions WHERE I.id=trade_actions.image_id AND trade_actions.user_id=$current_user) ORDER BY RAND() LIMIT $limit;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getRateImagesUnlogged($shown_images, $limit, $user_ip)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $limit = (int) $limit;
        $current_user = mysqli_real_escape_string($db, $user_ip);
        $shown_images = mysqli_real_escape_string($db, $shown_images);
        $query = "SELECT id, status, name, name_thumbnail FROM images as I WHERE (status='ready' OR status='trading') AND (user_id is not NULL) AND id NOT IN ($shown_images) AND NOT EXISTS (SELECT 1 FROM trade_actions WHERE I.id=trade_actions.image_id AND trade_actions.user_ip='$current_user') ORDER BY RAND() LIMIT $limit;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getRateImagesByFilters($shown_images, $limit, $search)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $limit = (int) $limit;
        $current_user = (int) getLoginUserId();
        $shown_images = mysqli_real_escape_string($db, $shown_images);
        $search = mysqli_real_escape_string($db, $search);
        $query = "SELECT I.id, I.status, I.name, I.name_thumbnail FROM images as I, images_hashtags, hashtags WHERE (status='ready' OR status='trading') AND (user_id is not NULL AND user_id<>$current_user) AND I.id NOT IN ($shown_images) AND NOT EXISTS (SELECT 1 FROM trade_actions WHERE I.id=trade_actions.image_id AND trade_actions.user_id=$current_user) AND I.id=images_hashtags.image_id AND images_hashtags.hashtag_id=hashtags.id AND hashtags.name='$search' ORDER BY RAND() LIMIT $limit;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setImageThumbnail($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['image_id'];
        $name_thumbnail = mysqli_real_escape_string($db, $fields['name_thumbnail']);
        $query = "UPDATE images SET name_thumbnail='$name_thumbnail' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getImageName($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['id'];
        $query = "SELECT name, name_thumbnail FROM images WHERE id=$image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getImageNameOriginal($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['id'];
        $query = "SELECT name, name_thumbnail, name_original FROM images WHERE id=$image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getImagesName($images_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $params = [];
        foreach ($images_id as $image_id) {
            array_push($params, 'id=' . (int) $image_id);
        }
        $search = implode(' OR ', $params);
        $query = "SELECT images.name FROM images WHERE " . $search;
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

// function getSets($filters)
// {
//     $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
//     $db->set_charset("utf8");
//     if ($db->connect_errno === 0) {
//         $filters_query = "";
//         if ($filters['cost'] || $filters['purchasable'] || $filters['photos'] || $filters['cost'] === "0" || $filters['purchasable'] === "0" || $filters['photos'] === "0") {
//             $filters_query = " WHERE ";
//             if ($filters['cost'] || $filters['cost'] === "0") {
//                 $filters_query .= "cost=" . (float) $filters['cost'];
//             }
//             if ($filters['photos'] || $filters['photos'] === "0") {
//                 $filters_query .= ($filters['cost'] || $filters['cost'] === "0") ? " AND " : " ";
//                 $filters_query .= "total_photos=" . (int) $filters['photos'];
//             }
//             if ($filters['purchasable'] || $filters['purchasable'] === "0") {
//                 $filters_query .= (($filters['photos'] || $filters['photos'] === "0") || ($filters['cost'] || $filters['cost'] === "0")) ? " AND " : " ";
//                 $filters_query .= "pur_photos=" . (int) $filters['purchasable'];
//             }
//         }
//         $query = "SELECT * FROM trade $filters_query ORDER BY cost ASC, id DESC";
//         $res = $db->query($query);
//         if ($res && $res->num_rows) {
//             return $res->fetch_all(MYSQLI_ASSOC);
//         } else {
//             return [];
//         }
//     }
// }

function getAllSets()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT id, users_photos, total_photos, cost, fiction, time, pur_photos, demo FROM trade ORDER BY cost DESC";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllSetsByFilter($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $filters['search'];
        $query = "SELECT trade.id, trade.users_photos, trade.total_photos, trade.cost, fiction FROM trade WHERE id LIKE '$id%' ORDER BY cost DESC";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllSetsByCost($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $cost_min = (int) $filters['cost_min'];
        $cost_max = (int) $filters['cost_max'];
        $query = '';
        if ($filters['cost_min'] != '' && $filters['cost_max'] != '') {
            $query = "SELECT id, users_photos, total_photos, cost, fiction FROM trade WHERE cost>=$cost_min AND cost<=$cost_max;";
        } else if ($filters['cost_min'] != '') {
            $query = "SELECT id, users_photos, total_photos, cost, fiction FROM trade WHERE cost>=$cost_min;";
        } else if ($filters['cost_max'] != '') {
            $query = "SELECT id, users_photos, total_photos, cost, fiction FROM trade WHERE cost<=$cost_max;";
        }
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

// function getSetsAmount($filters)
// {
//     $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
//     $db->set_charset("utf8");
//     if ($db->connect_errno === 0) {
//         $filters_query = "";
//         if ($filters['cost'] || $filters['purchasable'] || $filters['photos'] || $filters['cost'] === "0" || $filters['purchasable'] === "0" || $filters['photos'] === "0") {
//             $filters_query = " WHERE ";
//             if ($filters['cost'] || $filters['cost'] === "0") {
//                 $filters_query .= "cost=" . (float) $filters['cost'];
//             }
//             if ($filters['photos'] || $filters['photos'] === "0") {
//                 $filters_query .= ($filters['cost'] || $filters['cost'] === "0") ? " AND " : " ";
//                 $filters_query .= "total_photos=" . (int) $filters['photos'];
//             }
//             if ($filters['purchasable'] || $filters['purchasable'] === "0") {
//                 $filters_query .= (($filters['photos'] || $filters['photos'] === "0") || ($filters['cost'] || $filters['cost'] === "0")) ? " AND " : " ";
//                 $filters_query .= "pur_photos=" . (int) $filters['purchasable'];
//             }
//         }
//         $query = "SELECT count(id) as amount FROM trade $filters_query;";
//         $res = $db->query($query);
//         if ($res && $res->num_rows) {
//             return $res->fetch_all(MYSQLI_ASSOC);
//         } else {
//             return [];
//         }
//     }
// }

function getSet($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $cost = (float) $fields['cost'];
        $photos = (int) $fields['photos'];
        $purchasable = (int) $fields['purchasable'];
        $query = "SELECT * FROM trade WHERE cost=$cost AND total_photos=$photos AND pur_photos=$purchasable;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getSetById($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT * FROM trade WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getSetOfImage($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "SELECT * FROM trade WHERE users_photos LIKE '%:$image_id:%:%';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function deleteSetById($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "DELETE FROM trade WHERE id=$id";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function deleteImagesActions($images_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $images_id = mysqli_real_escape_string($db, $images_id);
        $query = "DELETE FROM trade_actions WHERE image_id IN ($images_id)";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function deleteImageActions($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "DELETE FROM trade_actions WHERE image_id=$image_id";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function getImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "SELECT images.name FROM images WHERE id=$image_id AND user_id=$user_id AND status='trading';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getImageById($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $id;
        $query = "SELECT * FROM images WHERE id=$image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $image_id = (int) $fields['image_id'];
        $query = "SELECT images.status FROM images WHERE id=$image_id AND user_id=$user_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserWallets($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['user_id'];
        $query = "SELECT wallet FROM users WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserWallet($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['user_id'];
        $query = "SELECT wallet FROM users WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserIBAN($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['user_id'];
        $query = "SELECT iban FROM users WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserBalance($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['user_id'];
        $query = "SELECT id, balance, referrer, email FROM users WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function changeUserBalance($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $balance = (float) $fields['balance'];
        $query = "UPDATE users SET balance=$balance WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateSetTime($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $time = (int) $fields['time'];
        $query = "UPDATE trade SET time=$time WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function deleteUserImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "DELETE FROM images WHERE images.id=$image_id AND images.user_id=$user_id";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function unsetUserOfImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $query = "UPDATE images SET user_id=NULL WHERE id=$image_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function deleteImageHashtags($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $query = "DELETE FROM images_hashtags WHERE images_hashtags.image_id=$image_id";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function addUserImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_name = mysqli_real_escape_string($db, $fields['image_name']);
        $image_name_original = mysqli_real_escape_string($db, $fields['image_name_original']);
        $image_name_thumbnail = mysqli_real_escape_string($db, $fields['image_name_thumbnail']);
        $user_id = (int) $fields['user_id'];
        $query = "INSERT INTO images(name, name_original, name_thumbnail, user_id) VALUES('$image_name', '$image_name_original', '$image_name_thumbnail', $user_id);";
        $db->query($query);
        return $db->insert_id;
    }
}

function addUserImageMulti($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_name = mysqli_real_escape_string($db, $fields['image_name']);
        $image_name_original = mysqli_real_escape_string($db, $fields['image_name_original']);
        $image_name_thumbnail = mysqli_real_escape_string($db, $fields['image_name_thumbnail']);
        $user_id = (int) $fields['user_id'];
        $query = "INSERT INTO images(name, name_original, name_thumbnail, user_id, status) VALUES('$image_name', '$image_name_original', '$image_name_thumbnail', $user_id, 'ready');";
        $db->query($query);
        return $db->insert_id;
    }
}

function addUserToSet($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['set_id'];
        $users_photos = mysqli_real_escape_string($db, $fields['users_photos']);
        $query = "UPDATE trade SET users_photos='$users_photos' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function changeImageStatus($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $image_status = mysqli_real_escape_string($db, $fields['image_status']);
        $query = "UPDATE images SET status='$image_status' WHERE id=$image_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function likeImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $set_id = (int) $fields['set_id'];
        $users_photos = mysqli_real_escape_string($db, $fields['users_photos']);
        $query = "UPDATE trade SET users_photos='$users_photos' WHERE id=$set_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getUserRateOfThisImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $image_id = (int) $fields['image_id'];
        $query = "SELECT rate FROM trade_actions WHERE user_id=$user_id AND image_id=$image_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setUserRateOfThisImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $image_id = (int) $fields['image_id'];
        $rate = (int) $fields['rate'];
        $query = "INSERT INTO trade_actions(user_id, image_id, rate) VALUES($user_id, $image_id, $rate);";
        $db->query($query);
        return $db->insert_id;
    }
}

function updateUserRateOfThisImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $image_id = (int) $fields['image_id'];
        $rate = (int) $fields['rate'];
        $query = "UPDATE trade_actions SET rate=$rate WHERE user_id=$user_id AND image_id=$image_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getUserRateOfThisImageUnlogged($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_ip = mysqli_real_escape_string($db, $fields['user_ip']);
        $image_id = (int) $fields['image_id'];
        $query = "SELECT rate FROM trade_actions WHERE user_ip='$user_ip' AND image_id=$image_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setUserRateOfThisImageUnlogged($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_ip = mysqli_real_escape_string($db, $fields['user_ip']);
        $image_id = (int) $fields['image_id'];
        $rate = (int) $fields['rate'];
        $query = "INSERT INTO trade_actions(user_ip, image_id, rate) VALUES('$user_ip', $image_id, $rate);";
        $db->query($query);
        return $db->insert_id;
    }
}

function updateUserRateOfThisImageUnlogged($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_ip = mysqli_real_escape_string($db, $fields['user_ip']);
        $image_id = (int) $fields['image_id'];
        $rate = (int) $fields['rate'];
        $query = "UPDATE trade_actions SET rate=$rate WHERE user_ip='$user_ip' AND image_id=$image_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}
function getRatedImagesByUser($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT image_id as id FROM trade_actions WHERE user_id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
function addToWinnersPopap($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $profit = mysqli_real_escape_string($db, $fields['profit']);
        $win = (int) $fields['win'];
        $bg = (int) $fields['bg'];
        $query = "INSERT INTO popap_winners(user_id, profit, win, bg) VALUES($user_id, '$profit', $win, $bg);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getUserWins($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT id, profit, win, bg FROM popap_winners WHERE user_id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function deleteUserWins($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "DELETE FROM popap_winners WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}
?>