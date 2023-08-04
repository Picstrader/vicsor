<?php
session_start();
$c_ml = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($c_ml, "utf8");

function get_ln_id_by_ln_n($ln_n, $c){
    $sql_str = "SELECT `lang_id` FROM `languages` WHERE `lang_name`='$ln_n'";
    $r = mysqli_query($c, $sql_str);
    $arr = mysqli_fetch_assoc($r);
    if (isset($arr['lang_id'])) {
        return $arr['lang_id'];
    }
    return false;
}

function get_ar_lns($c) {
    $sql_str = "SELECT * FROM `languages` ORDER BY `languages`.`lang_name` ASC";
    $r = mysqli_query($c, $sql_str);
    while ($arr[] = mysqli_fetch_assoc($r)) {
        $arr2 = $arr;
    }
    if (isset($arr2)) {
        return $arr2;
    }
    return false;
}

function get_ar_fs_by_ln_id($ln_id, $c) {
    $sql_str = "SELECT * FROM `phrases` WHERE `phrase_lang_id`=$ln_id";
    $r = mysqli_query($c, $sql_str);
    while ($arr[] = mysqli_fetch_assoc($r)) {
        $arr2 = $arr;
    }
    if (isset($arr2)) {
        return $arr2;
    }
    return false;
}

function ar_fs_mix($ar_fs_en, $ar_fs_cur, $cur_ln) {
    // Замена в EN array на RU-эквиваленты 
    for ($k_en = 0; $k_en < count($ar_fs_en); $k_en++) {
        for ($k_cur = 0; $k_cur < count($ar_fs_cur); $k_cur++) { 
            if ($ar_fs_cur[$k_cur]['phrase_name'] == $ar_fs_en[$k_en]['phrase_name']) {
                $ar_fs_en[$k_en] = $ar_fs_cur[$k_cur];
                break;
            } 
        }
    }
    return $ar_fs_en;
}

function ar_fs_mix2ar_fs($ar_fs_mix){
	for ($i=0; $i < count($ar_fs_mix); $i++) { 
		$name = $ar_fs_mix[$i]['phrase_name'];
		$value = $ar_fs_mix[$i]['phrase_value'];
		$fs[$name] = $value;
	}
	return $fs;
}

$def_ln_id = get_ln_id_by_ln_n(DEFAULT_LANGUAGE_NAME, $c_ml);

if (isset($_SESSION['cur_ln_id'])) {
    $cur_ln_id = $_SESSION['cur_ln_id'];
} else {
    $cur_ln_id = $def_ln_id;
}

if (isset($_POST['cur_ln_id'])) {
    $cur_ln_id = $_POST['cur_ln_id'];
    $_SESSION['cur_ln_id'] = $cur_ln_id;
}

$ar_lns = get_ar_lns($c_ml);

$ar_fs_en = get_ar_fs_by_ln_id($def_ln_id, $c_ml);

if (isset($cur_ln_id) && $cur_ln_id != $def_ln_id && $ar_fs_en) {
    $ar_fs_cur = get_ar_fs_by_ln_id($cur_ln_id, $c_ml);
} else {
    $ar_fs_cur = false;
}

if ($ar_fs_en && $ar_fs_cur) {
    $ar_fs_mix = ar_fs_mix($ar_fs_en, $ar_fs_cur, $cur_ln_id);
} elseif ($ar_fs_en) {
    $ar_fs_mix = $ar_fs_en;
} else {
    $fs = false;
}

$fs = ar_fs_mix2ar_fs($ar_fs_mix);
$_SESSION['save_fs'] = $fs;

function setMainCurrencyInText($text) {
    $currency = isset($_SESSION['save_fs']['main_currency']) ? $_SESSION['save_fs']['main_currency'] : 'USDT';
    $text = str_replace("{{USD}}", $currency, $text);
    return $text;
}
?>