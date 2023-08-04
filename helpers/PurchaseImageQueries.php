<?php

function getBuyerOfImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $image_id = (int) $fields['image_id'];
        $query = "SELECT id FROM user_buyers WHERE user_id=$user_id AND image_id=$image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setBuyerOfImage($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "INSERT INTO user_buyers(image_id, user_id) VALUES($image_id, $user_id);";
        $db->query($query);
        return $db->insert_id;
    }
}
function purchaseWonImagePart($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $part = (float) $fields['part'];
        $price = 0.1;
        $query = "INSERT INTO user_owners(image_id, user_id, part, price) VALUES($image_id, $user_id, $part, $price);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getUserPurchasedImages($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $page = (int) $fields['page'];
        $row = ($page - 1) * 35; 
        $query = "SELECT user_owners.image_id, user_owners.part, user_owners.price, images.name_original FROM user_owners, images WHERE user_owners.image_id=images.id AND user_owners.user_id=$user_id LIMIT $row, 35;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserPurchasedImagesAmount($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int) $fields['user_id'];
        $query = "SELECT count(id) as amount FROM user_owners WHERE user_id=$user_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}


function getUserPurchasedImage($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user_id = (int)$fields['user_id'];
        $image_id = (int)$fields['image_id'];
        $query = "SELECT part FROM user_owners WHERE user_id=$user_id AND image_id=$image_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllPartsOfPurchasedImage($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int)$fields['image_id'];
        $query = "SELECT id, user_id, image_id, part, price FROM user_owners WHERE image_id=$image_id AND part>0;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllPartsOfPurchasedImageExceptUser($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int)$fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "SELECT user_id, image_id, part, price FROM user_owners WHERE image_id=$image_id AND price>0 AND part>0 AND user_id<>$user_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
function getAllOwnersOfPurchasedImage($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int)$fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "SELECT user_id FROM user_owners WHERE image_id=$image_id AND part>0 AND user_id<>$user_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}



function updateBuyFullPurchasedImage($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "UPDATE user_owners SET part=1, price=0.1 WHERE image_id=$image_id AND user_id=$user_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateOwnerPart($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $part = (float) $fields['part'];
        $query = "UPDATE user_owners SET part=$part WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function insertBuyFullPurchasedImage($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $query = "INSERT INTO user_owners(image_id, user_id, part, price) VALUES($image_id, $user_id, 1, 0.1);";
        $db->query($query);
        return $db->insert_id;
    }
}

function deleteOwnerOfPurchasedImage($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['old_user_id'];
        $query = "DELETE FROM user_owners WHERE image_id=$image_id AND user_id=$user_id";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function updatePriceOfPurchasedImage($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $price = (float) $fields['price'];
        $query = "UPDATE user_owners SET price=$price WHERE image_id=$image_id AND user_id=$user_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function createBalanceOrder($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (float) $fields['amount'];
        $user_id = (int) $fields['user_id'];
        $token = mysqli_real_escape_string($db, $fields['token']);
        $pay_url = mysqli_real_escape_string($db, $fields['pay_url']);
        $invoice_id = mysqli_real_escape_string($db, $fields['invoice_id']);
        $query = "INSERT INTO balance_replenishment(user_id, amount, order_token, pay_url, invoice_id) VALUES($user_id, $amount, '$token', '$pay_url', '$invoice_id');";
        $db->query($query);
        return $db->insert_id;
    }
}

function getBalanceOrder($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $order_token = mysqli_real_escape_string($db, $fields['order_token']);
        $invoice_id = mysqli_real_escape_string($db, $fields['invoice_id']);
        $query = "SELECT id, user_id, amount, order_token, pay_url, invoice_id, status FROM balance_replenishment WHERE order_token='$order_token' AND invoice_id='$invoice_id'";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBalanceOrderStripe($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $order_token = mysqli_real_escape_string($db, $fields['order_token']);
        // $invoice_id = mysqli_real_escape_string($db, $fields['invoice_id']);
        $query = "SELECT id, user_id, amount, order_token, status FROM balance_replenishment WHERE order_token='$order_token'";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setStatusBalanceOrder($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "UPDATE balance_replenishment SET status=1 WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function createWithdrawFundsOrder($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (float) $fields['amount'];
        $user_id = (int) $fields['user_id'];
        $query = "INSERT INTO withdraw_funds(user_id, amount) VALUES($user_id, $amount);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getWithdrawFundsOrders() {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM withdraw_funds;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setStatusWithdrawFundsOrder($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['order_id'];
        $status = (int) $fields['status'];
        $query = "UPDATE withdraw_funds SET status=$status WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}
?>