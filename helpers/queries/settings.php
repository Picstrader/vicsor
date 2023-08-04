<?php
function getDemoSum()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM settings WHERE name='demo-sum';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateDemoSum($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (float) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='demo-sum';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getParam($param)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $param = mysqli_real_escape_string($db, $param);
        $query = "SELECT value FROM settings WHERE name='$param';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateParam($param, $value)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $param = mysqli_real_escape_string($db, $param);
        $value = mysqli_real_escape_string($db, $value);
        $query = "UPDATE settings SET value='$value' WHERE name='$param';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}
?>