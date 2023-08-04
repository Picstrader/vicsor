<?php
function addImageToGallery($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['winner_image'];
        $set_id = (int) $fields['id'];
        $cost = (float) $fields['cost'];
        $pur_photos = (int) $fields['pur_photos'];
        $total_photos = (int) $fields['total_photos'];
        $time = (int) $fields['time'];
        $likes = (int) $fields['likes'];
        $query = "INSERT INTO gallery(image_id, set_id, cost, pur_photos, total_photos, time, likes) VALUES($image_id, $set_id, $cost, $pur_photos, $total_photos, $time, $likes);";
        $db->query($query);
        return $db->insert_id;
    }
}

function addImageToSpecialAccount($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['winner_image'];
        $set_id = (int) $fields['id'];
        $cost = (float) $fields['cost'];
        $pur_photos = (int) $fields['pur_photos'];
        $total_photos = (int) $fields['total_photos'];
        $time = (int) $fields['time'];
        $likes = (int) $fields['likes'];
        $user_id = (int) COMPANY_ACCOUNT_ID;
        $query = "UPDATE images SET set_id=$set_id, cost=$cost, pur_photos=$pur_photos, total_photos=$total_photos, time=$time, likes=$likes, user_id=$user_id, status='winning' WHERE id=$image_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getImagesOfSpecialAccountAmount()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = COMPANY_ACCOUNT_ID;
        $query = "SELECT COUNT(*) as amount FROM images WHERE user_id=$user_id AND status='winning'";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getImagesOfSpecialAccount($page, $per_page)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $page ? (int) $page : 1;
        $per_page = (int) $per_page;
        $row = ($page - 1) * $per_page;
        $user_id = COMPANY_ACCOUNT_ID;
        $query = "SELECT * FROM images WHERE user_id=$user_id AND status='winning' LIMIT $row,$per_page";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getImageOfSpecialAccount($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = COMPANY_ACCOUNT_ID;
        $image_id = (int) $image_id;
        $query = "SELECT * FROM images WHERE user_id=$user_id AND status='winning' AND id=$image_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function unsetImageOfSpecialAccount($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "UPDATE images SET user_id=NULL, status='trading' WHERE id=$image_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function deleteImageOfSpecialAccount($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "DELETE FROM images WHERE images.id=$image_id AND status='winning'";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function getGalleryImages($rand, $page = 1)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $page;
        $rand = (float) $rand;
        $row = ($page - 1) * 35;
        $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images WHERE gallery.image_id=images.id ORDER BY RAND($rand) LIMIT $row,35;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryImagesFavorite($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $filters['page'];
        $user_id = (int) $filters['user_id'];
        $row = ($page - 1) * 35;
        $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND users_gallery_favorites.user_id=$user_id ORDER BY RAND() LIMIT $row,35;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryImage($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $id;
        $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail,images.name_original as name_original, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images WHERE gallery.image_id=images.id AND gallery.image_id=$image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryImageOriginal($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $id;
        $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, images.name_original, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images WHERE gallery.image_id=images.id AND gallery.image_id=$image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryByCost($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $filters['page'];
        $cost_min = (int) $filters['cost_min'];
        $cost_max = (int) $filters['cost_max'];
        $row = ($page - 1) * 35;
        $query = '';
        if ($filters['cost_min'] != '' && $filters['cost_max'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images WHERE gallery.image_id=images.id AND cost>=$cost_min AND cost<=$cost_max ORDER BY gallery.id DESC LIMIT $row,35;";
        } else if ($filters['cost_min'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images WHERE gallery.image_id=images.id AND cost>=$cost_min ORDER BY gallery.id DESC LIMIT $row,35;";
        } else if ($filters['cost_max'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images WHERE gallery.image_id=images.id AND cost<=$cost_max ORDER BY gallery.id DESC LIMIT $row,35;";
        } else {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images WHERE gallery.image_id=images.id ORDER BY gallery.id DESC LIMIT $row,35;";
        }
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryByCostFavorite($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $filters['page'];
        $cost_min = (int) $filters['cost_min'];
        $cost_max = (int) $filters['cost_max'];
        $user_id = (int) $filters['user_id'];
        $row = ($page - 1) * 35;
        $query = '';
        if ($filters['cost_min'] != '' && $filters['cost_max'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND users_gallery_favorites.user_id=$user_id AND cost>=$cost_min AND cost<=$cost_max ORDER BY gallery.id DESC LIMIT $row,35;";
        } else if ($filters['cost_min'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND users_gallery_favorites.user_id=$user_id AND cost>=$cost_min ORDER BY gallery.id DESC LIMIT $row,35;";
        } else if ($filters['cost_max'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND users_gallery_favorites.user_id=$user_id AND cost<=$cost_max ORDER BY gallery.id DESC LIMIT $row,35;";
        } else {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND users_gallery_favorites.user_id=$user_id ORDER BY gallery.id DESC LIMIT $row,35;";
        }
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryByCostHash($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $filters['page'];
        $cost_min = (int) $filters['cost_min'];
        $cost_max = (int) $filters['cost_max'];
        $hashtag = mysqli_real_escape_string($db, $filters['hash']);
        $row = ($page - 1) * 35;
        $query = '';
        if ($filters['cost_min'] != '' && $filters['cost_max'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, images_hashtags, hashtags WHERE gallery.image_id=images.id AND images.id = images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND hashtags.name='$hashtag' AND cost>=$cost_min AND cost<=$cost_max ORDER BY gallery.id DESC LIMIT $row,35;";
        } else if ($filters['cost_min'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, images_hashtags, hashtags WHERE gallery.image_id=images.id AND images.id = images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND hashtags.name='$hashtag' AND cost>=$cost_min ORDER BY gallery.id DESC LIMIT $row,35;";
        } else if ($filters['cost_max'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, images_hashtags, hashtags WHERE gallery.image_id=images.id AND images.id = images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND hashtags.name='$hashtag' AND cost<=$cost_max ORDER BY gallery.id DESC LIMIT $row,35;";
        } else {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images WHERE gallery.image_id=images.id ORDER BY gallery.id DESC LIMIT $row,35;";
        }
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryByCostHashFavorite($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $filters['page'];
        $cost_min = (int) $filters['cost_min'];
        $cost_max = (int) $filters['cost_max'];
        $user_id = (int) $filters['user_id'];
        $hashtag = mysqli_real_escape_string($db, $filters['hash']);
        $row = ($page - 1) * 35;
        $query = '';
        if ($filters['cost_min'] != '' && $filters['cost_max'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, images_hashtags, hashtags, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND images.id = images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND users_gallery_favorites.user_id=$user_id AND hashtags.name='$hashtag' AND cost>=$cost_min AND cost<=$cost_max ORDER BY gallery.id DESC LIMIT $row,35;";
        } else if ($filters['cost_min'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, images_hashtags, hashtags, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND images.id = images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND users_gallery_favorites.user_id=$user_id AND hashtags.name='$hashtag' AND cost>=$cost_min ORDER BY gallery.id DESC LIMIT $row,35;";
        } else if ($filters['cost_max'] != '') {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, images_hashtags, hashtags, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND images.id = images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND users_gallery_favorites.user_id=$user_id AND hashtags.name='$hashtag' AND cost<=$cost_max ORDER BY gallery.id DESC LIMIT $row,35;";
        } else {
            $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND users_gallery_favorites.user_id=$user_id ORDER BY gallery.id DESC LIMIT $row,35;";
        }
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryByHash($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $filters['page'];
        $hashtag = mysqli_real_escape_string($db, $filters['hash']);
        $row = ($page - 1) * 35;
        $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, images_hashtags, hashtags WHERE gallery.image_id=images.id AND images.id = images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND hashtags.name='$hashtag' ORDER BY RAND() DESC LIMIT $row,35;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryByHashFavorite($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $filters['page'];
        $hashtag = mysqli_real_escape_string($db, $filters['hash']);
        $user_id = (int) $filters['user_id'];
        $row = ($page - 1) * 35;
        $query = "SELECT gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images, images_hashtags, hashtags, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND gallery.image_id=images.id AND images.id = images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND users_gallery_favorites.user_id=$user_id AND hashtags.name='$hashtag' ORDER BY RAND() DESC LIMIT $row,35;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryAmount($filter = false, $type = 1)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $type = (int) $type;
        $filter_string = '';
        if($filter) {
            $filter_string = "WHERE fiction=$type";
        }
        $query = "SELECT count(id) as amount FROM gallery $filter_string;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getGalleryAmountFavorite($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $id;
        $query = "SELECT count(gallery.id) as amount FROM gallery, users_gallery_favorites WHERE users_gallery_favorites.gallery_id=gallery.id AND users_gallery_favorites.user_id=$user_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function addToFavorite($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        //$image_name = mysqli_real_escape_string($db, $fields['winner_image']);
        $gallery_image_id = (int) $fields['gallery_image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "INSERT INTO users_gallery_favorites(gallery_id, user_id) VALUES($gallery_image_id, $user_id);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getFavoriteImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $gallery_image_id = (int) $fields['gallery_image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "SELECT id FROM users_gallery_favorites WHERE user_id=$user_id AND gallery_id=$gallery_image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getFavoriteImages($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $query = "SELECT gallery_id FROM users_gallery_favorites WHERE user_id=$user_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function removeFromFavorite($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $gallery_image_id = (int) $fields['gallery_image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "DELETE FROM users_gallery_favorites WHERE user_id=$user_id AND gallery_id=$gallery_image_id;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function removeGalleryFavorites($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $gallery_image_id = (int) $id;
        $query = "DELETE FROM users_gallery_favorites WHERE gallery_id=$gallery_image_id;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function deleteGalleryImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $query = "DELETE FROM gallery WHERE image_id=$image_id;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function getGalleryForSlider()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT images.name_original FROM gallery, images WHERE gallery.image_id=images.id AND gallery.slider=1;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
?>