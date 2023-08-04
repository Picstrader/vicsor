<?php
function addToPreFavorite($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "INSERT INTO users_images_prefavorites(image_id, user_id) VALUES($image_id, $user_id);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getPreFavoriteImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "SELECT id FROM users_images_prefavorites WHERE user_id=$user_id AND image_id=$image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function removeFromPreFavorite($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "DELETE FROM users_images_prefavorites WHERE user_id=$user_id AND image_id=$image_id;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function removePreFavoritesOfImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $query = "DELETE FROM users_images_prefavorites WHERE image_id=$image_id;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function checkImageTrading($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $query = "SELECT id FROM images WHERE id=$image_id AND status='trading';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getPreFavoriteUserImages($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $query = "SELECT users_images_prefavorites.image_id, gallery.id FROM users_images_prefavorites, gallery WHERE user_id=$user_id AND users_images_prefavorites.image_id=gallery.image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
?>