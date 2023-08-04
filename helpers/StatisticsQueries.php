<?php
function addStatistics($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $browser = mysqli_real_escape_string($db, $fields['browser']);
        $country = mysqli_real_escape_string($db, $fields['country']);
        $action = mysqli_real_escape_string($db, $fields['action']);
        $image_id = (int) $fields['image_id'];
        $image_price = (float) $fields['image_price'];
        $hashtags = mysqli_real_escape_string($db, $fields['hashtags']);
        $set_id = (int) $fields['set_id'];
        $set_cost = (float) $fields['set_cost'];
        $set_total = (int) $fields['set_total'];
        $set_purchasable = (int) $fields['set_purchasable'];
        $created = mysqli_real_escape_string($db, $fields['created']);
        $query = "INSERT INTO statistics(browser, country, action, image_id, image_price, hashtags, set_id, set_cost, set_total, set_purchasable, created) VALUES('$browser', '$country', '$action', $image_id, $image_price, '$hashtags', $set_id, $set_cost, $set_total, $set_purchasable, '$created');";
        $db->query($query);
        return $db->insert_id;
    }
}
?>