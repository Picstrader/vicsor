<?php
function changeUserBalanceLog($user)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $user['id'];
        $balance_old = (float) $user['balance_old'];
        $balance_new = (float) $user['balance'];
        $sub_amount = (int) (isset($user['sub_amount']) ? $user['sub_amount'] : '0');
        $image_id = (int) (isset($user['image_id']) ? $user['image_id'] : '0');
        $cost = (float) (isset($user['cost']) ? $user['cost'] : '0');
        $total_photos = (int) (isset($user['photos']) ? $user['photos'] : '0');
        $pur_photos = (int) (isset($user['purchasable']) ? $user['purchasable'] : '0');
        $set_id = (int) (isset($user['set_id']) ? $user['set_id'] : '0');
        $wallet = isset($user['wallet']) ? mysqli_real_escape_string($db, $user["wallet"]) : '';
        $action = mysqli_real_escape_string($db, $user["log_action"]);
        $status = (int) $user["log_status"];
        $type = mysqli_real_escape_string($db, $user["log_type"]);
        $time = mysqli_real_escape_string($db, $user["log_time"]);
        $query = "INSERT INTO user_logs(user_id, type, action, balance_old, balance_new, status, cur_time, image_id, set_id, cost, total_photos, pur_photos, sub_amount, wallet) VALUES($user_id, '$type', '$action', $balance_old, $balance_new, $status, '$time', $image_id, $set_id, $cost, $total_photos, $pur_photos, $sub_amount, '$wallet');";
        $db->query($query);
        return $db->insert_id;
    }
}

function addUserToSetLog($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $set_id = (int) $fields['set_id'];
        $image_id = (int) $fields['image_id'];
        $cost = (float) $fields['cost'];
        $total_photos = (int) $fields['photos'];
        $pur_photos = (int) $fields['purchasable'];
        $time = (int) $fields['time'];
        $action = mysqli_real_escape_string($db, $fields["log_action"]);
        $status = (int) $fields["log_status"];
        $type = mysqli_real_escape_string($db, 'set');
        $cur_time = mysqli_real_escape_string($db, $fields["log_time"]);
        $query = "INSERT INTO user_logs(user_id, type, action, set_id, cost, total_photos, pur_photos, time, status, cur_time, image_id) VALUES($user_id, '$type', '$action', $set_id, $cost, $total_photos, $pur_photos, $time, $status, '$cur_time', $image_id);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getUserLogs($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $page = (int) $fields['page'];
        $row = ($page - 1) * 20; 
        $query = "SELECT * FROM user_logs WHERE user_id=$user_id ORDER BY id DESC LIMIT $row, 20;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserLogsByFilters($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $filters['user_id'];
        $type = mysqli_real_escape_string($db, $filters['type']);
        $page = (int) $filters['page'];
        $row = ($page - 1) * 20; 
        $query = "SELECT * FROM user_logs WHERE (type='balance' OR type='demo_balance') AND user_id=$user_id ORDER BY id DESC LIMIT $row, 20;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserLogsByPeriodFilters($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $filters['user_id'];
        $type = mysqli_real_escape_string($db, $filters['type']);
        $period = mysqli_real_escape_string($db, $filters['period']);
        $page = (int) $filters['page'];
        $row = ($page - 1) * 20; 
        $query = "SELECT * FROM user_logs WHERE type='$type' AND cur_time>='$period' AND user_id=$user_id ORDER BY id DESC LIMIT $row, 20;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserLogsByDayFilters($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $filters['user_id'];
        $type = mysqli_real_escape_string($db, $filters['type']);
        $day = mysqli_real_escape_string($db, $filters['day']);
        $day_next = mysqli_real_escape_string($db, $filters['day_next']);
        $page = (int) $filters['page'];
        $row = ($page - 1) * 20; 
        $query = "SELECT * FROM user_logs WHERE type='$type' AND cur_time>='$day' AND cur_time<'$day_next' AND user_id=$user_id ORDER BY id DESC LIMIT $row, 20;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserLogsAmount($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $query = "SELECT count(id) as amount FROM user_logs WHERE user_id=$user_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserLogsAmountByFilters($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $filters['user_id'];
        $type = mysqli_real_escape_string($db, $filters['type']);
        $query = "SELECT count(id) as amount FROM user_logs WHERE type='$type' AND user_id=$user_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserLogsAmountByPeriodFilters($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $filters['user_id'];
        $type = mysqli_real_escape_string($db, $filters['type']);
        $period = mysqli_real_escape_string($db, $filters['period']);
        $query = "SELECT count(id) as amount FROM user_logs WHERE type='$type' AND cur_time>='$period' AND user_id=$user_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserLogsAmountByDayFilters($filters)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $filters['user_id'];
        $type = mysqli_real_escape_string($db, $filters['type']);
        $day = mysqli_real_escape_string($db, $filters['day']);
        $day_next = mysqli_real_escape_string($db, $filters['day_next']);
        $query = "SELECT count(id) as amount FROM user_logs WHERE type='$type' AND cur_time>='$day' AND cur_time<'$day_next' AND user_id=$user_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function createSiteLog($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $percentage = (float) $fields['percentage'];
        $amount = (float) $fields['amount'];
        $description = mysqli_real_escape_string($db, $fields['description']);
        $user_id = (int) $fields['user_id'];
        $query = "INSERT INTO site_logs(percentage, amount, created, description, user_id) VALUES($percentage, $amount, UTC_TIMESTAMP(), '$description', $user_id);";
        $db->query($query);
        return $db->insert_id;
    }
}

function createWithdrawSiteLog($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $percentage = (float) 0;
        $amount = (float) $amount;
        $query = "INSERT INTO site_logs(percentage, amount, created, withdraw, description, user_id) VALUES($percentage, $amount, UTC_TIMESTAMP(), 1, 'Withdraw', 0);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getSiteLogs()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        // $user_id = (int) $fields['user_id'];
        // $page = (int) $fields['page'];
        // $row = ($page - 1) * 20; 
        $query = "SELECT * FROM site_logs ORDER BY id DESC;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
?>