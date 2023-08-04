<?php

function getPhoneVerificationCode($user_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $user_id;
        $query = "SELECT phone_verification_code FROM users WHERE id=$user_id AND ABS(TIMESTAMPDIFF(MINUTE, phone_verification_expire, UTC_TIMESTAMP())) <= 10";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setPhoneVerificationCode($user_id, $phone_verification_code)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $user_id;
        $phone_verification_code = mysqli_real_escape_string($db, $phone_verification_code);
        $query = "UPDATE users SET phone_verification_code=$phone_verification_code, phone_verification_expire=UTC_TIMESTAMP() WHERE id=$user_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateUserDemoBalance($user_id, $demo_balance)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $user_id;
        $demo_balance = (float) $demo_balance;
        $query = "UPDATE users SET demo_balance=$demo_balance WHERE id=$user_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}