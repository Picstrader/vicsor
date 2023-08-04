<?php
function getSubscriptionOptions()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT id, amount, price, color FROM subscription_options ORDER BY amount;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getSubscriptionOption($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['sub_id'];
        $query = "SELECT id, amount, price, color FROM subscription_options WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function addSubscriptionOption($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $fields['amount'];
        $price = (int) $fields['price'];
        $query = "INSERT INTO subscription_options(amount, price) VALUES($amount, $price);";
        $db->query($query);
        return $db->insert_id;
    }
}

function editUserSubscription($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $sub_id = (int) $fields['sub_id'];
        $price = (int) $fields['price'];
        $color = mysqli_real_escape_string($db, $fields['color']);
        $query = "UPDATE subscription_options SET price=$price, color='$color' WHERE id=$sub_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function deleteSubscriptionOption($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "DELETE FROM subscription_options WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function getUserSubscription($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['user_id'];
        $query = "SELECT subscription FROM users WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setUserSubscription($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $subscription = mysqli_real_escape_string($db, $fields['subscription']);
        $query = "UPDATE users SET subscription='$subscription' WHERE id=$user_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}
?>