<?php
function getSetImages($images_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $images_id = mysqli_real_escape_string($db, $images_id);
        $query = "SELECT id, name, name_thumbnail FROM images WHERE id IN ($images_id);";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
function setConsiderVoice($user, $voice)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $user = (int) $user;
        $voice = (int) $voice;
        $query = "UPDATE users SET voice=$voice WHERE id=$user";
        $res = $db->query($query);
        return $res;
    }
}
function checkAdmin($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $login = mysqli_real_escape_string($db, $fields['login']);
        $password = mysqli_real_escape_string($db, $fields['password']);
        $password = md5($password);
        $query = "SELECT id FROM admins WHERE password = '$password' AND login = '$login'";
        $res = $db->query($query);
        if ($res && ($res->num_rows === 1)) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}
function getMainSliderImages()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT settings.value FROM settings WHERE settings.name='main_slider'";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function setMainSliderImages($image_string)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_string = mysqli_real_escape_string($db, $image_string);
        $query = "UPDATE settings SET value='$image_string' WHERE name='main_slider'";
        $res = $db->query($query);
        return $res;
    }
}

function getModeratingImages($page, $per_page)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $page ? (int) $page : 1;
        $per_page = (int) $per_page;
        $row = ($page - 1) * $per_page;
        $query = "SELECT id, name, name_original, name_thumbnail, status FROM images WHERE status='moderating' LIMIT $row,$per_page";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getModeratingImagesAmount()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT count(*) as amount FROM images WHERE status='moderating'";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function approveModeratedImage($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "UPDATE images SET status='ready' WHERE id=$image_id AND status<>'trading';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getModeratedImage($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "SELECT id, name, name_original, name_thumbnail, status FROM images WHERE id=$image_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function deleteModeratedImage($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "DELETE FROM images WHERE images.id=$image_id AND status='moderating'";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function getComplainedImages()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT complains.*, images.name, images.name_original FROM complains, images WHERE images.id=complains.image_id GROUP BY image_id ORDER BY count(image_id) DESC;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getCountComplainsOfImageByType($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "SELECT type, count(*) as amount FROM complains WHERE image_id=$image_id GROUP BY type;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBackgroundImages()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT id, name FROM background_images;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBackgroundImage($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "SELECT id, name FROM background_images WHERE id=$image_id";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getRandomBackgroundImage()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT name FROM background_images ORDER BY RAND() LIMIT 1;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function deleteBackgroundImage($image_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $image_id;
        $query = "DELETE FROM background_images WHERE id=$image_id;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function setBackgroundImage($name)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $name = mysqli_real_escape_string($db, $name);
        $query = "INSERT INTO background_images(name) VALUES('$name');";
        $db->query($query);
        return $db->insert_id;
    }
}

function getReferralData()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT value FROM settings WHERE name='referral';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setReferralDefault()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "INSERT INTO settings(name, value) VALUES('referral', '0');";
        $db->query($query);
        return $db->insert_id;
    }
}

function setReferralData($percent)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $percent = (float) $percent;
        $query = "UPDATE settings SET value='$percent' WHERE name='referral';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function forceAddUserImageForGallery($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_name = mysqli_real_escape_string($db, $fields['image_name']);
        $image_name_original = mysqli_real_escape_string($db, $fields['image_name_original']);
        $image_name_thumbnail = mysqli_real_escape_string($db, $fields['image_name_thumbnail']);
        $query = "INSERT INTO images(name, name_original, name_thumbnail, user_id, status) VALUES('$image_name', '$image_name_original', '$image_name_thumbnail', NULL, 'trading');";
        $db->query($query);
        return $db->insert_id;
    }
}

function forceAddImageToGallery($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $likes = (int) $fields['likes'];
        $profit = (float) $fields['profit'];
        $percent = (float) $fields['percent'];
        $set_id = 0;
        $cost = 0;
        $pur_photos = 0;
        $total_photos = 0;
        $time = 0;
        $fiction = 1;
        $query = "INSERT INTO gallery(image_id, set_id, cost, pur_photos, total_photos, time, likes, profit, percent, fiction) VALUES($image_id, $set_id, $cost, $pur_photos, $total_photos, $time, $likes, $profit, $percent, $fiction);";
        $db->query($query);
        return $db->insert_id;
    }
}

function getGallery($page, $per_page, $filter = false, $type = 1)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $page ? (int) $page : 1;
        $per_page = (int) $per_page;
        $row = ($page - 1) * $per_page;
        $type = (int) $type;
        $filter_string = '';
        if($filter) {
            $filter_string = "AND fiction=$type";
        }
        $query = "SELECT gallery.slider, gallery.percent, gallery.profit, gallery.likes, gallery.id, images.name as name, images.name_thumbnail as name_thumbnail, gallery.image_id, gallery.cost, gallery.pur_photos, gallery.total_photos FROM gallery, images WHERE gallery.image_id=images.id $filter_string ORDER BY id DESC LIMIT $row,$per_page";// AND gallery.fiction=1;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateGallery($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $likes = (int) $fields['likes'];
        $profit = (int) $fields['profit'];
        $percent = (int) $fields['percent'];
        $slider = (int) $fields['slider'];
        $query = "UPDATE gallery SET likes=$likes, profit=$profit, percent=$percent, slider=$slider WHERE id=$id";
        $res = $db->query($query);
        return $res;
    }
}

function forcePurchaseWonImagePart($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $image_id = (int) $fields['image_id'];
        $user_id = (int) $fields['user_id'];
        $part = 1;
        $price = (float) $fields['price'] == 0 ? 0.1 : (float) $fields['price'];
        $query = "INSERT INTO user_owners(image_id, user_id, part, price) VALUES($image_id, $user_id, $part, $price);";
        $db->query($query);
        return $db->insert_id;
    }
}

function createFaq($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        // $question = mysqli_real_escape_string($db, $fields['question']);
        // $answer = mysqli_real_escape_string($db, $fields['answer']);
        $position = (int) $fields['position'];
        $query = "INSERT INTO faqs(position) VALUES($position);";
        $db->query($query);
        return $db->insert_id;
    }
}

function updateFaq($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        // $question = mysqli_real_escape_string($db, $fields['question']);
        // $answer = mysqli_real_escape_string($db, $fields['answer']);
        $position = (int) $fields['position'];
        $query = "UPDATE faqs SET position=$position WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateFaqsPositionAdd($id, $position)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $position = (int) $position;
        $query = "UPDATE faqs SET position=position+1 WHERE position >= $position AND id <> $id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateFaqsPositionDelete($id, $position)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $position = (int) $position;
        $query = "UPDATE faqs SET position=position-1 WHERE position > $position;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function deleteFaq($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "DELETE FROM faqs WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function getFaqs()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM faqs ORDER BY position;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getFaqQuestion($faq_id, $lang_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $question = 'faq_question_' . (int) $faq_id;
        $lang_id = (int) $lang_id;
        $query = "SELECT * FROM phrases WHERE phrase_name='$question' AND phrase_lang_id=$lang_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getFaqAnswer($faq_id, $lang_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $answer = 'faq_answer_' . (int) $faq_id;
        $lang_id = (int) $lang_id;
        $query = "SELECT * FROM phrases WHERE phrase_name='$answer' AND phrase_lang_id=$lang_id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateFaqQuestion($faq_id, $lang_id, $question_value)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $question = 'faq_question_' . (int) $faq_id;
        $question_value = mysqli_real_escape_string($db, $question_value);
        $lang_id = (int) $lang_id;
        $query = "UPDATE phrases SET phrase_value='$question_value' WHERE phrase_name='$question' AND phrase_lang_id=$lang_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateFaqAnswer($faq_id, $lang_id, $answer_value)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $answer = 'faq_answer_' . (int) $faq_id;
        $answer_value = mysqli_real_escape_string($db, $answer_value);
        $lang_id = (int) $lang_id;
        $query = "UPDATE phrases SET phrase_value='$answer_value' WHERE phrase_name='$answer' AND phrase_lang_id=$lang_id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function createFaqQuestion($faq_id, $lang_id, $question_value)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $question = 'faq_question_' . (int) $faq_id;
        $question_value = mysqli_real_escape_string($db, $question_value);
        $lang_id = (int) $lang_id;
        $query = "INSERT INTO phrases(phrase_name, phrase_value, phrase_lang_id) VALUES('$question', '$question_value', $lang_id);";
        $db->query($query);
        return $db->insert_id;
    }
}

function createFaqAnswer($faq_id, $lang_id, $answer_value)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $answer = 'faq_answer_' . (int) $faq_id;
        $answer_value = mysqli_real_escape_string($db, $answer_value);
        $lang_id = (int) $lang_id;
        $query = "INSERT INTO phrases(phrase_name, phrase_value, phrase_lang_id) VALUES('$answer', '$answer_value', $lang_id);";
        $db->query($query);
        return $db->insert_id;
    }
}

function deleteFaqQuestion($faq_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $question = 'faq_question_' . (int) $faq_id;
        $query = "DELETE FROM phrases WHERE phrase_name='$question';";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function deleteFaqAnswer($faq_id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $answer = 'faq_answer_' . (int) $faq_id;
        $query = "DELETE FROM phrases WHERE phrase_name='$answer';";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function setSiteBenefit($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (float) $amount;
        $query = "UPDATE settings SET amount=amount+$amount WHERE name='site-benefit';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getSiteBenefit()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT amount FROM settings WHERE name='site-benefit';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function withdrawSiteBenefit($new_amount) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $new_amount = (float) $new_amount;
        $query = "UPDATE settings SET amount=$new_amount WHERE name='site-benefit';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getLotParams()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM settings WHERE name='lot-end-reaction';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setLotParams($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='lot-end-reaction';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getAllBalances()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT balance FROM users;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllTradesCost()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT cost, users_photos FROM trade WHERE fiction <> 1;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllFictionTradesCost()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT cost, users_photos FROM trade WHERE fiction = 1;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getSubParam()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM settings WHERE name='subscription';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getSubFreeParam()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM settings WHERE name='subscription-free';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateSubParam($sub_price)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $sub_price = (float) $sub_price;
        $query = "UPDATE settings SET amount=$sub_price WHERE name='subscription';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateSubFreeParam($sub_free)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $sub_free = (int) $sub_free;
        $query = "UPDATE settings SET amount=$sub_free WHERE name='subscription-free';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getBonusReg()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM settings WHERE name='bonus_reg';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function updateBonusReg($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (float) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='bonus_reg';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function createBonusRegLog($params) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $description = mysqli_real_escape_string($db, $params['description']);
        $amount = (float) $params['amount'];
        $user_id = (int) $params['user_id'];
        $query = "INSERT INTO bonus_reg_logs(description, amount, user_id, created) VALUES('$description', $amount, $user_id, UTC_TIMESTAMP());";
        $db->query($query);
        return $db->insert_id;
    }   
}

function getBonusRegLogs()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM bonus_reg_logs;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBonusRegSum()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT SUM(amount) as total FROM bonus_reg_logs;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getFictionSetsAmount() {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT COUNT(*) as amount FROM trade WHERE fiction=1;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getFictionSets($page, $per_page) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $page ? (int) $page : 1;
        $per_page = (int) $per_page;
        $row = ($page - 1) * $per_page;
        $query = "SELECT * FROM trade WHERE fiction=1 LIMIT $row,$per_page;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function createFictionSet($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $cost = (int) $fields['cost'];
        $photos = (int) $fields['photos'];
        $purchasable = (int) $fields['purchasable'];
        $time = (int) $fields['time'];
        $users_photos = mysqli_real_escape_string($db, $fields['users_photos']);
        $fiction = 1;
        $query = "INSERT INTO trade(users_photos, cost, pur_photos, total_photos, time, fiction) VALUES('$users_photos', '$cost', '$purchasable', '$photos', '$time', $fiction);";
        $db->query($query);
        return $db->insert_id;
    }
}

function deleteFictionSet($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "DELETE FROM trade WHERE id=$id AND fiction=1;";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function deleteUser($id) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "DELETE FROM users WHERE id=$id";
        $res = $db->query($query);
        if ($res && $db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function getAllSetsAmountAdmin($filter)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $filter_str = '';
        if($filter) {
            switch($filter) {
                case '1':
                    $filter_str = 'WHERE fiction=0';
                    break;
                case '2':
                    $filter_str = 'WHERE fiction=1';
                    break;
            }
        }
        $query = "SELECT COUNT(*) as amount FROM trade $filter_str ORDER BY cost DESC";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getAllSetsAdmin($page, $per_page, $filter)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $page = (int) $page ? (int) $page : 1;
        $per_page = (int) $per_page;
        $row = ($page - 1) * $per_page;
        $filter_str = '';
        if($filter) {
            switch($filter) {
                case '1':
                    $filter_str = 'WHERE fiction=0';
                    break;
                case '2':
                    $filter_str = 'WHERE fiction=1';
                    break;
            }
        }
        $query = "SELECT id, users_photos, total_photos, cost, fiction, time, pur_photos FROM trade $filter_str ORDER BY cost DESC LIMIT $row,$per_page";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setBotFakeA($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='bot-fake-a';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function setBotFakeAStep($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='bot-fake-a-step';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function setBotFakeB($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='bot-fake-b';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function setBotFakeC($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='bot-fake-c';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function setBotFakeUsers($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='bot-fake-users';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function setBotFakeTime($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='bot-fake-time';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function setBotFakeStatus($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='bot-fake-status';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getBotFakeA()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT amount FROM settings WHERE name='bot-fake-a';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBotFakeAStep()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT amount FROM settings WHERE name='bot-fake-a-step';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBotFakeB()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT amount FROM settings WHERE name='bot-fake-b';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBotFakeC()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT amount FROM settings WHERE name='bot-fake-c';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBotFakeTime()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT amount FROM settings WHERE name='bot-fake-time';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBotFakeUsers()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT amount FROM settings WHERE name='bot-fake-users';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBotFakeStatus()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT amount FROM settings WHERE name='bot-fake-status';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getBotFakeAddUserTime()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT amount FROM settings WHERE name='bot-fake-add-user-time';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function setBotFakeAddUserTime($amount)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $amount = (int) $amount;
        $query = "UPDATE settings SET amount=$amount WHERE name='bot-fake-add-user-time';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}
?>