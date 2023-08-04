<?php 
function setLastTelegramCommand($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $telegram_id =  mysqli_real_escape_string($db, $fields['telegram_id']);
        $last_command =  mysqli_real_escape_string($db, $fields['last_command']);
        $query = "INSERT INTO telegram(telegram_id, last_command, data) VALUES('$telegram_id', '$last_command', '');";
        $db->query($query);
        return $db->insert_id;
    }
}

function updateLastTelegramCommand($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $telegram_id =  mysqli_real_escape_string($db, $fields['telegram_id']);
        $last_command =  mysqli_real_escape_string($db, $fields['last_command']);
        $query = "UPDATE telegram SET last_command='$last_command', data='' WHERE telegram_id='$telegram_id';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getLastTelegramCommand($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $telegram_id =  mysqli_real_escape_string($db, $fields['telegram_id']);
        $query = "SELECT last_command, data FROM telegram WHERE telegram_id='$telegram_id';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateCommandData($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $telegram_id =  mysqli_real_escape_string($db, $fields['telegram_id']);
        $data =  mysqli_real_escape_string($db, $fields['data']);
        $query = "UPDATE telegram SET data='$data' WHERE telegram_id='$telegram_id';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function setUserTelegramId($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['user_id'];
        $telegram_id =  mysqli_real_escape_string($db, $fields['telegram_id']);
        $query = "UPDATE users SET telegram_id='$telegram_id' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function unsetUserTelegramId($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $telegram_id =  mysqli_real_escape_string($db, $fields['telegram_id']);
        $query = "UPDATE users SET telegram_id='' WHERE telegram_id='$telegram_id';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function checkTelegramId($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $telegram_id =  mysqli_real_escape_string($db, $fields['telegram_id']);
        $query = "SELECT id FROM users WHERE telegram_id='$telegram_id';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
?>