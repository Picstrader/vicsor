<?php
class ECommerceLogic
{
    public static function getProfitUSDT($set)
    {
        try {
            $cost = abs((float) $set['cost']);
            $total_photos = abs((int) $set['total_photos']);
            $purchasable_photos = abs((int) $set['pur_photos']);
            if ($purchasable_photos != 0) {
                $profit_usdt = (($cost * ($total_photos - $purchasable_photos)) / $purchasable_photos) * 0.9;
            } else {
                $profit_usdt = 0;
            }
        } catch (Exception $e) {
            $profit_usdt = 0;
        }
        return round($profit_usdt, 2);
    }

    public static function getSiteBenefit($set)
    {
        try {
            $cost = abs((float) $set['cost']);
            $total_photos = abs((int) $set['total_photos']);
            $purchasable_photos = abs((int) $set['pur_photos']);
            if ($purchasable_photos != 0) {
                $site_benefit = ($cost * ($total_photos - $purchasable_photos)) * 0;
            } else {
                $site_benefit = 0;
            }
        } catch (Exception $e) {
            $site_benefit = 0;
        }
        return round($site_benefit, 2);
    }

    public static function calculateSubBenefit($user_id, $sub)
    {
        $fields = [];
        $fields['user_id'] = $user_id;
        $fields['amount'] = $sub;
        $fields['description'] = 'Subscription';
        $fields['percentage'] = 0;
        $res = setSiteBenefit($fields['amount']);
        if ($res) {
            createSiteLog($fields);
        }
    }

    public static function getProfitPercent($set)
    {
        try {
            $cost = abs((float) $set['cost']);
            $profit_usdt = abs((float) $set['profit_usdt']);
            if ($cost != 0) {
                $profit_percent = ($profit_usdt / $cost) * 100;
            } else {
                $profit_percent = 0;
            }
        } catch (Exception $e) {
            $profit_percent = 0;
        }
        return round($profit_percent, 2);
    }

    public static function getDaysHours($seconds_amount, $fs)
    {
        $hours_amount = ((int) $seconds_amount) / (60 * 60);
        $minutes_amount = intdiv((int) $seconds_amount, 60);
        $days = intdiv($hours_amount, 24);
        if ($days >= 1) {
            $days_str = strlen((string) $days) > 1 ? $days : '0' . $days;
            $template = $days_str . ' ' . $fs['Days'];
            return $template;
        }
        $hours = (int) ($hours_amount % 24);
        if ($hours >= 1) {
            $hours_str = strlen((string) $hours) > 1 ? $hours : '0' . $hours;
            $template = $hours_str . ' ' . $fs['Hours'];
            return $template;
        }
        $minutes = (int) $minutes_amount;
        if ($minutes >= 1) {
            $minutes_str = strlen((string) $minutes) > 1 ? $minutes : '0' . $minutes;
            $template = $minutes_str . ' ' . $fs['Minutes'];
            return $template;
        }
        $seconds = (int) $seconds_amount;
        if ($seconds >= 1) {
            $seconds_str = strlen((string) $seconds) > 1 ? $seconds : '0' . $seconds;
            $template = $seconds_str . ' ' . $fs['Seconds'];
            return $template;
        }
        return 0;
    }

    public static function updateBalance()
    {
        if (!isLogin()) {
            return;
        }
        $balance = getUserBalance(['user_id' => getLoginUserId()]);
        if (count($balance) > 0) {
            $balance = $balance[0]['balance'];
            setLoginUserBalance($balance);
        }
    }

    public static function getDateOfSubEnd($days)
    {
        ECommerceLogic::checkSubscription();
        if (!isLogin()) {
            return false;
        }
        $fields = [];
        $fields['user_id'] = getLoginUserId();
        $user_sub_date = getUserSubscription($fields);
        $user_sub_date = $user_sub_date[0]['subscription'];
        $previous_date = $user_sub_date;
        $check_date = strtotime($previous_date);
        if ($check_date == strtotime('0000-00-00 00:00:00')) {
            $date = new DateTime('now', new DateTimeZone("UTC"));
        } else {
            $date = new DateTime($previous_date, new DateTimeZone("UTC"));
        }
        $date->modify('+' . (int) $days . ' day');
        $date = $date->format('Y-m-d H:i:s');
        return $date;
    }

    public static function getDateOfSubEndBonus()
    {
        ECommerceLogic::checkSubscription();
        if (!isLogin()) {
            return false;
        }
        $user_sub_date = getUserSubscription(['user_id' => getLoginUserId()])[0]['subscription'];
        $previous_date = $user_sub_date;
        $check_date = strtotime($previous_date);
        if ($check_date == strtotime('0000-00-00 00:00:00')) {
            $date = new DateTime('now', new DateTimeZone("UTC"));
        } else {
            $date = new DateTime($previous_date, new DateTimeZone("UTC"));
        }
        $sub_free = getSubFreeParam()[0]['amount'];
        $date->modify('+' . (int) $sub_free . ' day');
        $date = $date->format('Y-m-d H:i:s');
        return $date;
    }
    public static function checkSubscription()
    {
        if (!isLogin()) {
            return false;
        }
        $fields = [];
        $fields['user_id'] = getLoginUserId();
        $user_sub_date = getUserSubscription($fields);
        $user_sub_date = $user_sub_date[0]['subscription'];
        if ($user_sub_date === null) {
            return false;
        }
        $date_now = new DateTime('now', new DateTimeZone("UTC"));
        $date_sub = new DateTime($user_sub_date, new DateTimeZone("UTC"));

        if ($date_now > $date_sub) {
            $fields['subscription'] = null;
            setUserSubscription($fields);
            return false;
        } else {
            return true;
        }
    }

    public static function getSubscriptionLeft()
    {
        if (!isLogin()) {
            return false;
        }
        $fields = [];
        $fields['user_id'] = getLoginUserId();
        $user_sub_date = getUserSubscription($fields);
        $user_sub_date = $user_sub_date[0]['subscription'];
        if ($user_sub_date === null) {
            return false;
        }
        $date_now = new DateTime('now', new DateTimeZone("UTC"));
        $date_sub = new DateTime($user_sub_date, new DateTimeZone("UTC"));
        $answer = [];
        $answer['amount'] = 0;
        $answer['type'] = 'Days';
        if ($date_now > $date_sub) {
            return $answer;
        } else {
            $difference = $date_now->diff($date_sub);
            if ((int) $difference->days > 0) {
                $answer['amount'] = $difference->days;
                $answer['type'] = 'Days';
            } else if ((int) $difference->h > 0) {
                $answer['amount'] = $difference->h;
                $answer['type'] = 'Hours';
            } else if ((int) $difference->i > 0) {
                $answer['amount'] = $difference->i;
                $answer['type'] = 'Minutes';
            }
            return $answer;
        }
    }

    public static function getTimePeriod($minutes)
    {
        $date = new DateTime($minutes . ' minutes ago');
        $date = $date->format('Y-m-d H:i:s');
        return $date;
    }

    public static function getTimeNow()
    {
        $date = new DateTime('now');
        $date = $date->format('Y-m-d H:i:s');
        return $date;
    }

    public static function getNextDay($day)
    {
        $date = new DateTime($day);
        $date->modify('tomorrow');
        $date = $date->format('Y-m-d');
        return $date;
    }

    public static function checkWithdrawPeriod($last_date)
    {
        $last = new DateTime($last_date, new DateTimeZone("UTC"));
        $now = new DateTime('now', new DateTimeZone("UTC"));
        $interval = $last->diff($now);
        return ($interval->days * 24) + $interval->h;
    }

    public static function addBalanceLog($fields, $type, $status, $action)
    {
        $fields["log_action"] = $action;
        $fields["action"] = $action;
        $fields["log_status"] = $status;
        $fields["log_type"] = $type;
        $fields["log_time"] = ECommerceLogic::getTimeNow();
        switch ($type) {
            case 'balance':
                changeUserBalanceLog($fields);
                $email_log = ECommerceLogic::prepareBalanceEmailLog($fields);
                ECommerceLogic::sendEmailLog($fields['email'], $email_log, $fields['action']);
                break;
            case 'demo_balance':
                changeUserBalanceLog($fields);
                break;
            case 'set':
                addUserToSetLog($fields);
                $email_log = ECommerceLogic::prepareSetEmailLog($fields);
                ECommerceLogic::sendEmailLog($fields['email'], $email_log, $fields['action']);
                break;
        }
    }

    public static function prepareBalanceEmailLog($log)
    {
        $fs = $_SESSION['save_fs'];
        $title_log = '';
        switch ($log['action']) {
            case 'Sending photo in set':
                $title_log = $fs[$log['action']];
                $title_log = str_replace("{{image_id}}", $log['image_id'], $title_log);
                $set_params = 'A' . $log['cost'] . ', B' . (isset($log['photos']) ? $log['photos'] : $log['total_photos']) . ', C' . (isset($log['purchasable']) ? $log['purchasable'] : $log['pur_photos']);
                $title_log = str_replace("{{set_id}}", $set_params, $title_log);
                break;
            case 'Photo purchased':
                $title_log = $fs[$log['action']];
                $title_log = str_replace("{{image_id}}", $log['image_id'], $title_log);
                break;
            case 'Photo sold':
                $title_log = $fs[$log['action']];
                $title_log = str_replace("{{image_id}}", $log['image_id'], $title_log);
                break;
            case 'Photo returned':
                $title_log = $fs[$log['action']];
                $title_log = str_replace("{{image_id}}", $log['image_id'], $title_log);
                break;
            case 'Set canceled':
                $title_log = $fs[$log['action']];
                $title_log = str_replace("{{set_id}}", $log['set_id'], $title_log);
                break;
            case 'Account top-up':
                $title_log = $fs[$log['action']];
                $title_log .= ' ' . round(abs((float) (isset($log['balance']) ? $log['balance'] : $log['balance_new']) - (float) $log['balance_old']), 2) . ' ' . $fs['main_currency'];
                break;
            case 'Subscription':
                $title_log = $fs[$log['action']];
                $title_log = str_replace("{{sub_amount}}", $log['sub_amount'], $title_log);
                break;
            case 'Withdraw funds':
                $title_log = $fs[$log['action']];
                $title_log = ECommerceLogic::setMainCurrencyInText($title_log);
                $title_log = str_replace("{{amount}}", round(abs((float) (isset($log['balance']) ? $log['balance'] : $log['balance_new']) - (float) $log['balance_old']), 2), $title_log);
                $title_log = str_replace("{{wallet}}", $log['wallet'], $title_log);
                break;
            case 'Referral program':
                $title_log = $fs[$log['action']];
                $title_log = ECommerceLogic::setMainCurrencyInText($title_log);
                $title_log = str_replace("{{amount}}", round(abs((float) (isset($log['balance']) ? $log['balance'] : $log['balance_new']) - (float) $log['balance_old']), 2), $title_log);
                break;
            case 'won game':
                $title_log = $fs[$log['action']];
                $title_log = str_replace("{{image_id}}", $log['image_id'], $title_log);
                break;
            case 'demo balance':
                $title_log = $fs[$log['action']];
                $title_log = ECommerceLogic::setMainCurrencyInText($title_log);
                $title_log = str_replace("{{amount}}", round(abs((float) (isset($log['balance']) ? $log['balance'] : $log['balance_new']) - (float) $log['balance_old']), 2), $title_log);
                break;
            default:
                $title_log = $fs[$log['action']];
                break;
        }
        return $title_log;
    }

    public static function setMainCurrencyInText($text)
    {
        $currency = isset($_SESSION['save_fs']['main_currency']) ? $_SESSION['save_fs']['main_currency'] : 'USDT';
        $text = str_replace("{{USD}}", $currency, $text);
        return $text;
    }

    public static function prepareSetEmailLog($log)
    {
        $fs = $_SESSION['save_fs'];
        $title_log = '';
        switch ($log['action']) {
            case 'Sending photo in set':
                $title_log = $fs[$log['action']];
                $title_log = str_replace("{{image_id}}", $log['image_id'], $title_log);
                $set_params = 'A' . $log['cost'] . ', B' . (isset($log['photos']) ? $log['photos'] : $log['total_photos']) . ', C' . (isset($log['purchasable']) ? $log['purchasable'] : $log['pur_photos']);
                $title_log = str_replace("{{set_id}}", $set_params, $title_log);
                break;
            case 'Set canceled':
                $title_log = $fs[$log['action']];
                $title_log = str_replace("{{set_id}}", $log['set_id'], $title_log);
                break;
            default:
                $title_log = $fs[$log['action']];
                break;
        }
        return $title_log;
    }

    public static function getSubDiscount($one_month_price, $months_price)
    {
        $discount = 100 - (((float) $months_price['price'] / (float) $months_price['amount']) / (float) $one_month_price['price']) * 100;
        return round($discount, 2);
    }

    public static function checkNewFavoriteImages()
    {
        if (!isLogin()) {
            return;
        }
        $fields = [];
        $fields['user_id'] = getLoginUserId();
        $prefavorites = getPreFavoriteUserImages($fields);
        foreach ($prefavorites as $prefavorite) {
            $fields['gallery_image_id'] = $prefavorite['id'];
            $fields['image_id'] = $prefavorite['image_id'];
            addToFavorite($fields);
            removeFromPreFavorite($fields);
        }
    }

    public static function sendEmailConfirm($email, $token)
    {
        $html_message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/email_confirm.html');
        $link = "https://picstrader.com/activate.php?token=" . $token;
        $html_message = str_replace("{{url_confirm}}", $link, $html_message);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: support@picstrader.com";
        mail($email, 'Picstrader: activate account', $html_message, $headers, "-f " . "support@picstrader.com");
    }

    public static function sendEmailSuccessConfirm($email)
    {
        $html_message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/success_confirm.html');
        $html_message = str_replace("{{email}}", $email, $html_message);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: support@picstrader.com";
        mail($email, 'Picstrader: activate account', $html_message, $headers, "-f " . "support@picstrader.com");
    }

    public static function sendNewPassword($password, $email)
    {
        $html_message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/new_password.html');
        $html_message = str_replace("{{password}}", $password, $html_message);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: support@picstrader.com";
        mail($email, 'Picstrader: password', $html_message, $headers, "-f " . "support@picstrader.com");
    }

    public static function sendEmailLog($email, $log, $title_email)
    {
        $html_message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/send_log.html');
        $html_message = str_replace("{{log}}", $log, $html_message);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: support@picstrader.com";
        if ($title_email == 'Account top-up') {
            $title_email = 'Picstrader: balance top-up';
        } else {
            $title_email = 'Picstrader: your activity';
        }
        mail($email, $title_email, $html_message, $headers, "-f " . "support@picstrader.com");
    }

    public static function sendChangePassword($email, $token, $user_id)
    {
        $html_message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/change_password.html');
        $link = "https://picstrader.com/change-password.php?change_password_token=$token&id=$user_id";
        $html_message = str_replace("{{url_confirm}}", $link, $html_message);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: support@picstrader.com";
        mail($email, 'Picstrader: activate account', $html_message, $headers, "-f " . "support@picstrader.com");
    }

    public static function sendRecoverPassword($email, $token, $user_id)
    {
        $html_message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/change_password.html');
        $link = "https://picstrader.com/new_password.php?change_password_token=$token&id=$user_id";
        $html_message = str_replace("{{url_confirm}}", $link, $html_message);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: support@picstrader.com";
        mail($email, 'Picstrader: activate account', $html_message, $headers, "-f " . "support@picstrader.com");
    }

    public static function balanceOperationAdd($user, $sum)
    {
        //$user = getUserBalance(['user_id' => $user_id])[0];
        $user['balance_old'] = $user['balance'];
        $user['balance'] = (float) $user['balance'] + (float) $sum;
        $respond_purchase = changeUserBalance($user);
        if ($respond_purchase) {
            ECommerceLogic::addBalanceLog($user, 'balance', 1, 'Transfer');
            return true;
        } else {
            ECommerceLogic::addBalanceLog($user, 'balance', 0, 'Transfer');
            return false;
        }
    }

    public static function balanceOperationDeduct($user, $sum)
    {
        //$user = getUserBalance(['user_id' => $user_id])[0];
        $user['balance_old'] = $user['balance'];
        $user['balance'] = (float) $user['balance'] - (float) $sum;
        $respond_purchase = changeUserBalance($user);
        if ($respond_purchase) {
            ECommerceLogic::addBalanceLog($user, 'balance', 1, 'Transfer');
            return true;
        } else {
            ECommerceLogic::addBalanceLog($user, 'balance', 0, 'Transfer');
            return false;
        }
    }

    public static function sendEmailDownloadImages($email, $images)
    {
        $html_images = "";
        foreach ($images as $image) {
            $image_template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/image_link_template.html');
            $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/inc/assets/img/$image[name_original]";
            $image_template = str_replace("{{url_confirm}}", $link, $image_template);
            $html_images .= $image_template;
        }
        $html_message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/email_image.html');
        $html_message = str_replace("{{images_template}}", $html_images, $html_message);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: support@picstrader.com";
        mail($email, 'Picstrader: image', $html_message, $headers, "-f " . "support@picstrader.com");
    }
}