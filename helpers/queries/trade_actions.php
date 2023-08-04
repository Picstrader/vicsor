<?php
function getTradeAction($image_id, $user_id, $set_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $set_id = (int) $set_id;
        $user_id = (int) $user_id;
        $query = "SELECT * FROM trade_actions WHERE image_id=$image_id AND user_id=$user_id AND trade_id=$set_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateTradeAction($id, $rate)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $rate = (int) $rate;
        $query = "UPDATE trade_actions SET rate=$rate WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function createTradeAction($image_id, $user_id, $set_id, $rate)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $user_id = (int) $user_id;
        $set_id = (int) $set_id;
        $rate = (int) $rate;
        $query = "INSERT INTO trade_actions(image_id, user_id, trade_id, rate) VALUES($image_id, $user_id, $set_id, $rate);";
        $db->query($query);
        return $db->insert_id;
    }
}
?>