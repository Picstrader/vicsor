<?php
function saveStatistics($data)
{
    $fields = [];
    $fields['browser'] = getBrowser();
    $fields['country'] = getIPCountry();
    $fields['action'] = $data['action'];
    switch($fields['action']) {
        case 'like':
        case 'dislike':
            $fields['image_id'] = $data['image_id'];
            $fields['hashtags'] = prepareImageHashtags($data['image_id']);
            $fields['set_id'] = $data['id'];
            $fields['set_cost'] = $data['cost'];
            $fields['set_total'] = $data['total_photos'];
            $fields['set_purchasable'] = $data['pur_photos'];
            break;
        case 'create/join lot':
            $fields['image_id'] = $data['image_id'];
            $fields['hashtags'] = prepareImageHashtags($data['image_id']);
            $fields['set_id'] = $data['set_id'];
            $fields['set_cost'] = $data['cost'];
            $fields['set_total'] = $data['photos'];
            $fields['set_purchasable'] = $data['purchasable'];
            break;
        case 'buy image':
            $fields['image_id'] = $data['image_id'];
            $fields['image_price'] = $data['image_price'];
            $fields['hashtags'] = prepareImageHashtags($data['image_id']);
            $gallery_image = getGalleryImage($fields['image_id']);
            $gallery_image = $gallery_image[0];
            $fields['set_id'] = $gallery_image['set_id'];
            $fields['set_cost'] = $gallery_image['cost'];
            $fields['set_total'] = $gallery_image['total_photos'];
            $fields['set_purchasable'] = $gallery_image['pur_photos'];
            break;
    }
    if(!isset($fields['image_price'])) {
        $fields['image_price'] = 0;
    }
    $fields['created'] = ECommerceLogic::getTimeNow();
    addStatistics($fields);
}

function prepareImageHashtags($image_id) {
    $hashtags = getImageHashtags($image_id);
    $temp_array = [];
    foreach($hashtags as $hashtag) {
        array_push($temp_array, $hashtag['name']);
    }
    return implode(',', $temp_array);
}

function getIPCountry() {
    return $_SERVER['HTTP_GEOIP_COUNTRY_CODE'];
}

function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/OPR/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent) && !preg_match('/Edg/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent) && !preg_match('/Edg/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    } elseif (preg_match('/Edge/i', $u_agent) || preg_match('/Edg/i', $u_agent)) {
        $bname = 'Edge';
        $ub = "Edge";
    } elseif (preg_match('/Trident/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }
    return $bname;
}
?>