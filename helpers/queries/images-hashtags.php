<?php
function getPopularSameHashtags($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $name = mysqli_real_escape_string($db, $filters['search']);
        $query = "SELECT hashtags.name FROM hashtags, images_hashtags WHERE hashtags.id=images_hashtags.hashtag_id AND name LIKE '$name%' AND name <> '$name' GROUP BY images_hashtags.hashtag_id ORDER BY count(images_hashtags.hashtag_id) DESC LIMIT 10;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getImageHashtags($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $image_id;
        $query = "SELECT hashtags.name FROM hashtags, images_hashtags WHERE hashtags.id=images_hashtags.hashtag_id AND images_hashtags.image_id=$id ORDER BY hashtags.id DESC;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function addHashtagToImage($image_id, $hashtag_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $hashtag_id = (int) $hashtag_id;
        $query = "INSERT INTO images_hashtags(image_id, hashtag_id) VALUES($image_id, $hashtag_id);";
        $db->query($query);
        return $db->insert_id;
    }
}
?>