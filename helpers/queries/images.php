<?php
function getImages($page = null, $per_page = null, $status = null, $user_id = null, $image_id = null, $images = '', $order = false, $hashtag = null)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $limit = '';
        $filters = [];
        if ($page) {
            $page = (int) $page;
            $per_page = (int) $per_page;
            $row = ($page - 1) * $per_page;
            $limit = "LIMIT $row,$per_page";
        }
        if($status || $user_id || $image_id || $images != '' || $hashtag) {
            $where = "WHERE ";
            if($status) {
                $status = mysqli_real_escape_string($db, $status);
                $where .= "status='$status'";
            }
            if($user_id) {
                $user_id = (int) $user_id;
                $where .= ($status ? " AND " : "") . "user_id=$user_id";
            }
            if($image_id) {
                $image_id = (int) $image_id;
                $where .= ($status || $user_id ? " AND " : "") . "id=$image_id";
            }
            if($images != '') {
                $images = mysqli_real_escape_string($db, $images);
                $where .= ($status || $user_id || $image_id ? " AND " : "") . "id IN ($images)";
            }

            if ($hashtag) {
                $hashtag = mysqli_real_escape_string($db, $hashtag);
                $tables = ", images_hashtags, hashtags";
                $where .= ($status || $user_id || $image_id || $images ? " AND " : "") . "images.id=images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND hashtags.name='$hashtag'";
            }
        }
        if($order) {
            $order_by = "ORDER BY priority DESC, showed DESC";
        }
        $query = "SELECT images.* FROM images $tables $where $order_by $limit";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getImagesAmount($status = 'gallery', $hashtag = null, $user_id = null)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $status = mysqli_real_escape_string($db, $status);
        $where = '';
        if ($hashtag) {
            $hashtag = mysqli_real_escape_string($db, $hashtag);
            $tables = ", images_hashtags, hashtags";
            $where = ($status ? " AND " : "") . "images.id=images_hashtags.image_id AND hashtags.id=images_hashtags.hashtag_id AND hashtags.name='$hashtag'";
        }
        if ($user_id) {
            $user_id = (int) $user_id;
            $where .= ($status || $hashtag ? " AND " : "") . "images.user_id=$user_id";
        }
        $query = "SELECT COUNT(*) as amount FROM images $tables WHERE status='$status' $where";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getPurchasedImages($user_id, $image_id = null)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $user_id;
        if($image_id) {
            $image_id = (int) $image_id;
            $image = "AND id=$image_id";
        }
        $query = "SELECT * FROM images WHERE (owners LIKE '$user_id:%' OR owners LIKE '%;$user_id:%') $image";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getPurchasedImage($user_id, $image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $user_id;
        $image_id = (int) $image_id;
        $query = "SELECT * FROM images WHERE (owners LIKE '$user_id:%' OR owners LIKE '%;$user_id:%') AND id=$image_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getImageByActualPriority($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "SELECT * FROM images WHERE priority_end > UTC_TIMESTAMP() AND id=$image_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function addImage($name, $original, $thumbnail, $user_id, $status) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_name = mysqli_real_escape_string($db, $name);
        $image_name_original = mysqli_real_escape_string($db, $original);
        $image_name_thumbnail = mysqli_real_escape_string($db, $thumbnail);
        $status = mysqli_real_escape_string($db, $status);
        $user_id = (int) $user_id;
        $query = "INSERT INTO images(name, name_original, name_thumbnail, user_id, status) VALUES('$image_name', '$image_name_original', '$image_name_thumbnail', $user_id, 'ready')";
        $db->query($query);
        return $db->insert_id;
    }
}

function updateImage($image_id, $user_id, $price, $status, $owners = '', $showed = false)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $user_id = (int) $user_id;
        $price = (float) $price;
        $status = mysqli_real_escape_string($db, $status);
        $owners = mysqli_real_escape_string($db, $owners);
        if($showed) {
            $showed = ", showed=UTC_TIMESTAMP()";
        }
        $query = "UPDATE images SET price=$price, status='$status', owners='$owners' $showed WHERE id=$image_id AND user_id=$user_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateImagePriority($image_id, $user_id, $priority, $days, $actual_priority_date = false)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $user_id = (int) $user_id;
        $priority = (int) $priority;
        $days = (int) $days;
        if($actual_priority_date) {
            $start = "priority_end";
        } else {
            $start = "UTC_TIMESTAMP()";
        }
        $query = "UPDATE images SET priority=$priority, priority_end=DATE_ADD($start, INTERVAL $days DAY) WHERE id=$image_id AND (user_id=$user_id OR (owners LIKE '$user_id:%' OR owners LIKE '%;$user_id:%'));";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateImagePinToTop($image_id, $user_id, $days) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $user_id = (int) $user_id;
        $days = (int) $days;
        $query = "UPDATE images SET pin_days=pin_days+$days WHERE id=$image_id AND (user_id=$user_id OR (owners LIKE '$user_id:%' OR owners LIKE '%;$user_id:%'));";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updatePurchasedImage($image_id, $price, $owners = '')
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $price = (float) $price;
        $owners = mysqli_real_escape_string($db, $owners);
        $query = "UPDATE images SET price=$price, owners='$owners' WHERE id=$image_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updatePurchasedImageStatus($image_id, $user_id, $status)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $user_id = (int) $user_id;
        $status = mysqli_real_escape_string($db, $status);
        $query = "UPDATE images SET status='$status', showed=UTC_TIMESTAMP() WHERE id=$image_id AND (owners LIKE '$user_id:%' OR owners LIKE '%;$user_id:%');";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateImageSold($image_id, $status)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $status = mysqli_real_escape_string($db, $status);
        $query = "UPDATE images SET status='$status', user_id = NULL WHERE id=$image_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function deleteImage($image_id, $user_id = null, $admin = true)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $user_id = (int) $user_id;
        if(!$admin) {
            $filter = " AND user_id=$user_id";
        }
        $query = "DELETE FROM images WHERE id=$image_id AND status<>'trading' $filter";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}
?>