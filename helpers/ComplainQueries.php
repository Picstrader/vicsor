<?php
function getOwnerOfImage($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['image_id'];
        $query = "SELECT user_id FROM images WHERE id=$id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getComplain($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $image_id = (int) $fields['image_id'];
        $query = "SELECT id FROM complains WHERE user_id=$user_id AND image_id=$image_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function addComplain($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $owner_id = (int) $fields['owner_id'];
        $set_id = (int) $fields['set_id'];
        $user_id = (int) $fields['user_id'];
        $type = (int) $fields['type'];
        $query = "INSERT INTO complains(image_id, owner_id, set_id, user_id, type) VALUES($image_id, $owner_id, $set_id, $user_id, $type);";
        $db->query($query);
        return $db->insert_id;
    }
}

function updateComplain($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $complain_id = (int) $fields['complain_id'];
        $type = (int) $fields['type'];
        $query = "UPDATE complains SET type=$type WHERE id=$complain_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}