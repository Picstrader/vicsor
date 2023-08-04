<?php
function addHashtag($name)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $name = mysqli_real_escape_string($db, $name);
        $query = "INSERT INTO hashtags(name) VALUES('$name');";
        $db->query($query);
        return $db->insert_id;
    }
}

function getHashtagByName($name)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $name = mysqli_real_escape_string($db, $name);
        $query = "SELECT id FROM hashtags WHERE name='$name';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
?>