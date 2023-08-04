<?php
function getSets($cost = null, $total = null, $purchasable = null, $page = 1, $per_page = 25, $type = 'active', $user_id = 0, $set_id = null)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = $user_id ? (int) $user_id : -1;
        $page = (int) $page ? (int) $page : 1;
        $per_page = (int) $per_page ? (int) $per_page : 1;
        $row = ($page - 1) * $per_page;
        $filter = [];
        if ($cost) {
            array_push($filter, 'cost=' . (int) $cost);
        }
        if ($total) {
            array_push($filter, 'total_photos=' . (int) $total);
        }
        if ($purchasable) {
            array_push($filter, 'pur_photos=' . (int) $purchasable);
        }
        if ($set_id) {
            array_push($filter, 'id=' . (int) $set_id);
        }
        $filter = count($filter) ? "AND " . implode(" AND ", $filter) : '';
        switch ($type) {
            case 'my':
                $type_q = "WHERE (users_photos LIKE '$user_id:%:%:%' OR users_photos LIKE '%;$user_id:%:%:%')";
                break;
            case 'active':
                $type_q = "WHERE (users_photos NOT LIKE '$user_id:%:%:%' AND users_photos NOT LIKE '%;$user_id:%:%:%') AND demo=0";
                break;
            case 'demo':
                $type_q = "WHERE (users_photos NOT LIKE '$user_id:%:%:%' AND users_photos NOT LIKE '%;$user_id:%:%:%') AND demo=1";
                break;
            case 'all':
                $type_q = "WHERE demo=0";
                break;
            default:
                $type_q = "WHERE (users_photos NOT LIKE '$user_id:%:%:%' AND users_photos NOT LIKE '%;$user_id:%:%:%') AND demo=0";
                break;
        }
        $not_fill = ($type != 'all') ? "AND (LENGTH(users_photos) - LENGTH(REPLACE(users_photos, ';', ''))) < total_photos - 1" : "";
        $query = "SELECT * FROM trade $type_q $not_fill $filter ORDER BY cost ASC, id DESC LIMIT $row,$per_page";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getSetsAmount($cost = null, $total = null, $purchasable = null, $type = 'active', $user_id = 0)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = $user_id ? (int) $user_id : -1;
        $filter = [];
        if ($cost) {
            array_push($filter, 'cost=' . (int) $cost);
        }
        if ($total) {
            array_push($filter, 'total_photos=' . (int) $total);
        }
        if ($purchasable) {
            array_push($filter, 'pur_photos=' . (int) $purchasable);
        }
        $filter = count($filter) ? "AND " . implode(" AND ", $filter) : '';
        switch ($type) {
            case 'my':
                $type = "WHERE (users_photos LIKE '$user_id:%:%:%' OR users_photos LIKE '%;$user_id:%:%:%')";
                break;
            case 'active':
                $type = "WHERE (users_photos NOT LIKE '$user_id:%:%:%' AND users_photos NOT LIKE '%;$user_id:%:%:%') AND demo=0";
                break;
            case 'demo':
                $type = "WHERE (users_photos NOT LIKE '$user_id:%:%:%' AND users_photos NOT LIKE '%;$user_id:%:%:%') AND demo=1";
                break;
            default:
                $type = "WHERE (users_photos NOT LIKE '$user_id:%:%:%' AND users_photos NOT LIKE '%;$user_id:%:%:%') AND demo=0";
                break;
        }
        $not_fill = "AND (LENGTH(users_photos) - LENGTH(REPLACE(users_photos, ';', ''))) < total_photos - 1";
        $query = "SELECT COUNT(*) as amount FROM trade $type $not_fill $filter ORDER BY cost ASC, id DESC";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateSet($user_id, $image_id, $users_photos, $set_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        if ($user_id) {
            $user_id = (int) $user_id;
            $image_id = (int) $image_id;
            $users_photos = $users_photos ? $users_photos . ";$user_id:$image_id:0:0" : "$user_id:$image_id:0:0";
        }
        $set_id = (int) $set_id;
        $users_photos = mysqli_real_escape_string($db, $users_photos);
        $query = "UPDATE trade SET users_photos='$users_photos' WHERE id=$set_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function createSet($cost, $photos, $purchasable, $time)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $cost = (int) $cost;
        $photos = (int) $photos;
        $purchasable = (int) $purchasable;
        $time = (int) $time;
        $query = "INSERT INTO trade(cost, pur_photos, total_photos, time) VALUES($cost, $purchasable, $photos, $time);";
        $db->query($query);
        return $db->insert_id;
    }
}
?>