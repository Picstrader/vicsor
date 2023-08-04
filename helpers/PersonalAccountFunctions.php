<?php
function canChangeEmail($fields)
{
    $is_verified_data = getEmailVerification($fields);
    $is_verified = (int) $is_verified_data[0]['verification'];
    $user_email = $is_verified_data[0]['email'];
    if ($is_verified) {
        if ($user_email == $fields['email']) {
            return true;
        } else {
            return false;
        }
    } else {
        if ($user_email != $fields['email']) {
            sendTokenNewEmail($fields['email']);
            setLoginUserEmail($fields['email']);
            return true;
        }
    }
    return true;
}

function sendTokenNewEmail($new_email)
{
    $fields_new = [];
    $fields_new['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    $fields_new['user_id'] = getLoginUserId();
    $fields_new['email'] = $new_email;
    $res = updateUserToken($fields_new);
    ECommerceLogic::sendEmailConfirm($fields_new['email'], $fields_new['token']);
}

function isVerified($fields)
{
    $is_verified_data = getEmailVerification($fields);
    $is_verified = (int) $is_verified_data[0]['verification'];
    $user_email = $is_verified_data[0]['email'];
    if ($is_verified) {
        return true;
    } else {
        false;
    }
}
?>