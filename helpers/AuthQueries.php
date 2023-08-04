<?php
function getCountries()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT countries.id, countries.name FROM countries";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function registerUser($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $firstname = mysqli_real_escape_string($db, $fields['firstname']);
        $surname = mysqli_real_escape_string($db, $fields['surname']);
        $nickname = mysqli_real_escape_string($db, $fields['nickname']);
        $email = mysqli_real_escape_string($db, $fields['email']);
        $password = mysqli_real_escape_string($db, $fields['password']);
        $password = md5($password);
        $token = mysqli_real_escape_string($db, $fields['token']);
        $login_token = mysqli_real_escape_string($db, $fields['login_token']);
        $phone = mysqli_real_escape_string($db, $fields['phone']);
        $birth = mysqli_real_escape_string($db, $fields['birth']);
        $referrer = mysqli_real_escape_string($db, $fields['referrer']);
        $country = mysqli_real_escape_string($db, $fields['country']);
        $phone_verification_code = mysqli_real_escape_string($db, $fields['phone_verification_code']);
        $google_auth = 0;
        $query = "INSERT INTO users(nickname, email, password, token, google_auth, firstname, surname, phone, birth, referrer, country, login_token, login_token_expire, phone_verification_code, phone_verification_expire) VALUES('$nickname', '$email', '$password', '$token', $google_auth, '$firstname', '$surname', '$phone', '$birth', '$referrer', '$country', '$login_token', UTC_TIMESTAMP(), '$phone_verification_code', UTC_TIMESTAMP());";
        $db->query($query);
        return $db->insert_id;
    }
}

function registerGoogleUser($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $firstname = mysqli_real_escape_string($db, $fields['firstname']);
        $surname = mysqli_real_escape_string($db, $fields['surname']);
        $nickname = mysqli_real_escape_string($db, $fields['nickname']);
        $email = mysqli_real_escape_string($db, $fields['email']);
        $phone = mysqli_real_escape_string($db, $fields['phone']);
        $avatar = mysqli_real_escape_string($db, $fields['avatar']);
        $birth = mysqli_real_escape_string($db, $fields['birth']);
        $referrer = mysqli_real_escape_string($db, $fields['referrer']);
        $country = mysqli_real_escape_string($db, $fields['country']);
        $login_token = mysqli_real_escape_string($db, $fields['login_token']);
        $phone_verification_code = mysqli_real_escape_string($db, $fields['phone_verification_code']);
        $google_auth = 1;
        $query = "INSERT INTO users(nickname, email, avatar, google_auth, firstname, surname, phone, birth, referrer, country, login_token, login_token_expire, phone_verification_code, phone_verification_expire) VALUES('$nickname', '$email', '$avatar', $google_auth, '$firstname', '$surname', '$phone', '$birth', '$referrer', '$country', '$login_token', UTC_TIMESTAMP(), '$phone_verification_code', UTC_TIMESTAMP());";
        $db->query($query);
        return $db->insert_id;
    }
}

function verifyAccount($token) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $token = mysqli_real_escape_string($db, $token);
        $query = "UPDATE users SET verification=1 WHERE token='$token';";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function verifyPhone($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['user_id'];
        $query = "UPDATE users SET phone_verification=1 WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function unsetVerifyPhone($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['user_id'];
        $query = "UPDATE users SET phone_verification=0 WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function deleteUserReferrer($id) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "UPDATE users SET referrer='' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateUserToken($fields) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $token = mysqli_real_escape_string($db, $fields['token']);
        $id = (int) $fields['user_id'];
        $query = "UPDATE users SET token='$token' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function updateUserLoginToken($token, $id) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $token = mysqli_real_escape_string($db, $token);
        $id = (int) $id;
        $query = "UPDATE users SET login_token='$token', login_token_expire=UTC_TIMESTAMP() WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function checkUniqueFields($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $nickname = mysqli_real_escape_string($db, $fields['nickname']);
        $email = mysqli_real_escape_string($db, $fields['email']);
        $phone = mysqli_real_escape_string($db, $fields['phone']);
        $query = "SELECT nickname, email, phone FROM users WHERE nickname = '$nickname' OR email = '$email' OR phone = '$phone';";
        $res = $db->query($query);
        if ($res) {
            $res->fetch_all(MYSQLI_ASSOC);
            foreach ($res as $user) {
                if ($user['nickname'] == $fields['nickname']) {
                    $_SESSION['error_type'] = "unique_nickname";
                    return false;
                }
                if ($user['email'] == $fields['email']) {
                    $_SESSION['error_type'] = "unique_email";
                    return false;
                }
                if ($user['phone'] == $fields['phone']) {
                    $_SESSION['error_type'] = "unique_phone";
                    return false;
                }
            }
            return true;
        } else {
            return true;
        }
    }
}

function checkLogIn($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $login = mysqli_real_escape_string($db, $fields['login']);
        $password = mysqli_real_escape_string($db, $fields['password']);
        $password = md5($password);
        $query = "SELECT id, email, firstname, surname, nickname, avatar, balance, verification FROM users WHERE password = '$password' AND email = '$login' and google_auth = 0";
        $res = $db->query($query);
        if ($res && ($res->num_rows === 1)) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function checkLogInCookie($login_token)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $login_token = mysqli_real_escape_string($db, $login_token);
        $query = "SELECT id, email, firstname, surname, nickname, avatar, balance, verification FROM users WHERE login_token = '$login_token' AND DATEDIFF(UTC_TIMESTAMP(), login_token_expire)<=7";
        $res = $db->query($query);
        if ($res && ($res->num_rows === 1)) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function checkUserExists($email)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $email = mysqli_real_escape_string($db, $email);
        $query = "SELECT users.id, users.email, users.firstname, users.surname, users.nickname, users.avatar, users.balance FROM users WHERE email = '$email';";
        $res = $db->query($query);
        if ($res && ($res->num_rows === 1)) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function checkGoogleUserExists($email)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $email = mysqli_real_escape_string($db, $email);
        $query = "SELECT users.id, users.email, users.firstname, users.surname, users.nickname, users.avatar, users.balance FROM users WHERE email = '$email' and google_auth=1;";
        $res = $db->query($query);
        if ($res && ($res->num_rows === 1)) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}
function getUserData($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT id, firstname, surname, nickname, password, email, phone, birth, verification, phone_verification, wallet, withdraw_last, country, reg_date, balance, voice FROM users WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function getUserVoice($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT voice FROM users WHERE id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function getUserAvatar($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT users.avatar FROM users WHERE users.id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function getUserNickname($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT nickname FROM users WHERE users.id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function getUserByNickname($nickname)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $nickname = mysqli_real_escape_string($db, $nickname);
        $query = "SELECT id FROM users WHERE nickname='$nickname';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserByEmail($email)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $email = mysqli_real_escape_string($db, $email);
        $query = "SELECT * FROM users WHERE email='$email';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

function getUserDataByToken($token)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $token = mysqli_real_escape_string($db, $token);
        $query = "SELECT * FROM users WHERE users.token='$token';";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function getUserBalanceAfterReg($id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $query = "SELECT users.balance FROM users WHERE users.id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

function checkUniqueFieldsRegisteredUser($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $nickname = mysqli_real_escape_string($db, $fields['nickname']);
        $email = mysqli_real_escape_string($db, $fields['email']);
        $phone = mysqli_real_escape_string($db, $fields['phone']);
        $id = (int) $fields['id'];
        $query = "SELECT nickname, email, phone FROM users WHERE (nickname = '$nickname' OR email = '$email' OR phone = '$phone') AND id <> $id;";
        $res = $db->query($query);
        if ($res) {
            $res->fetch_all(MYSQLI_ASSOC);
            foreach ($res as $user) {
                if ($user['nickname'] == $fields['nickname']) {
                    $_SESSION['error_type'] = "unique_nickname";
                    return false;
                }
                if ($user['email'] == $fields['email']) {
                    $_SESSION['error_type'] = "unique_email";
                    return false;
                }
                if ($user['phone'] == $fields['phone']) {
                    $_SESSION['error_type'] = "unique_phone";
                    return false;
                }
            }
            return true;
        } else {
            return true;
        }
    }
}

function updateUserData($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $firstname = mysqli_real_escape_string($db, $fields['firstname']);
        $surname = mysqli_real_escape_string($db, $fields['surname']);
        $nickname = mysqli_real_escape_string($db, $fields['nickname']);
        $email = mysqli_real_escape_string($db, $fields['email']);
        $phone = mysqli_real_escape_string($db, $fields['phone']);
        $birth = mysqli_real_escape_string($db, $fields['birth']);
        $query = "UPDATE users SET nickname='$nickname', email='$email', firstname='$firstname', surname='$surname', phone='$phone', birth='$birth' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function setUserPassword($id, $password)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $id;
        $password = mysqli_real_escape_string($db, $password);
        $password = md5($password);
        $query = "UPDATE users SET password='$password' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function prepareNewPassword($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $password = mysqli_real_escape_string($db, $fields['password']);
        $password = md5($password);
        $token = mysqli_real_escape_string($db, $fields['token']);
        $query = "UPDATE users SET new_password='$password', change_password_token='$token' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function prepareGeneratePassword($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $token = mysqli_real_escape_string($db, $fields['token']);
        $query = "UPDATE users SET change_password_token='$token' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function setNewPasswordToDefault($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $token = mysqli_real_escape_string($db, $fields['token']);
        $new_password = mysqli_real_escape_string($db, $fields['new_password']);
        $query = "UPDATE users SET users.password='$new_password' WHERE change_password_token='$token' AND id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getNewPassword($token, $id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $token = mysqli_real_escape_string($db, $token);
        $id = (int) $id;
        $query = "SELECT new_password FROM users WHERE change_password_token='$token' AND id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
    return false;
}

function getUserDataByRecoverPasswordToken($token, $id)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $token = mysqli_real_escape_string($db, $token);
        $id = (int) $id;
        $query = "SELECT id, email FROM users WHERE change_password_token='$token' AND id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
    return false;
}

function setUserAvatar($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $avatar = mysqli_real_escape_string($db, $fields['avatar']);
        $query = "UPDATE users SET avatar='$avatar' WHERE id=$id;";
        $res = $db->query($query);
        return mysqli_affected_rows($db);
    }
}

function getEmailVerification($fields)
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $id = (int) $fields['id'];
        $query = "SELECT users.email, users.verification FROM users WHERE users.id=$id;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
    return false;
}
?>