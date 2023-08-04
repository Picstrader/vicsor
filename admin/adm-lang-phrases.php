<?php
session_start();
if(!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../inc/template/adm-lang-phrases-header.php';


$c = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($c, "utf8");

            echo('<br><br><br>');


function ex_ln_of_n($ln_n, $c){
    $sql_str = "SELECT COUNT(*) AS `count` FROM `languages` WHERE `lang_name`='$ln_n'";
    $r = mysqli_query($c, $sql_str);
    $ar = mysqli_fetch_assoc($r);
    if ($ar['count'] == 0) {
        return false;
    }
    return true;
}

function ln_ad($ln_n, $c) {
    $sql_str = "INSERT INTO `languages` SET `lang_name`='$ln_n'";
    mysqli_query($c, $sql_str);
}


function ln_e($ln_id, $ln_n, $en_ln_id, $c) {
    if ($ln_id == $en_ln_id) {
        return "may_not_edit_en_lang";
    } else {
        $sql_str = "UPDATE `languages` SET `lang_name` = '$ln_n' WHERE `lang_id`=$ln_id";
        $r = mysqli_query($c, $sql_str);
    }
}


function ln_d($ln_id, $en_ln_id, $c){
    if ($ln_id == $en_ln_id) {
        return "may_not_del_en_lang";
    } else {
        $sql_str = "DELETE FROM `languages` WHERE `lang_id`=$ln_id";
        $r = mysqli_query($c, $sql_str);
        return "lang_deleted";
    }
}


function ex_f_of_f_id($f_id, $c){
    $sql = "SELECT COUNT(*) AS 'count' FROM `phrases` WHERE `phrase_id`=$f_id";
    $r = mysqli_query($c, $sql);
    $ar = mysqli_fetch_assoc($r);
    if ($ar['count'] != 0) return true;
    return false;
}


function ex_f_of_n($f_n, $en_ln_id, $c){
    $sql_str = "SELECT COUNT(*) AS 'count' FROM `phrases` WHERE `phrase_name`='$f_n' AND `phrase_lang_id`=$en_ln_id";
    $r = mysqli_query($c, $sql_str);
    $arr = mysqli_fetch_assoc($r);
    if ($arr['count'] != 0) return true;
    return false;
}


function ex_f_of_f_id_and_f_ln_id($f_id, $f_ln_id, $c){
    $sql = "SELECT COUNT(*) AS 'count' FROM `phrases` WHERE `phrase_id`=$f_id AND `phrase_lang_id`=$f_ln_id";
    $r = mysqli_query($c, $sql);
    $ar = mysqli_fetch_assoc($r);
    if ($ar['count'] != 0) {
        return true;
    }   return false;
}


function f_ad($f_n, $f_v, $ln_id, $c){
    $sql_str = "INSERT INTO `phrases` (`phrase_name`, `phrase_value`, `phrase_lang_id`) VALUES
    ('$f_n', '$f_v', $ln_id)";
    $r = mysqli_query($c, $sql_str);
}


function upd_f_v_by_f_id($f_id, $f_v, $c) {
    $sql = "UPDATE `phrases` SET `phrase_value` = '$f_v' WHERE `phrase_id`=$f_id";
    $r = mysqli_query($c, $sql);
}


function upd_f_n_by_old_f_n($f_n_old, $f_n_new, $c) {
    $sql = "UPDATE `phrases` SET `phrase_name`='$f_n_new' WHERE `phrase_name`='$f_n_old'";
    mysqli_query($c, $sql);
}


function del_fs_by_n($f_n, $c) {
    $sql = "DELETE FROM `phrases` WHERE `phrase_name`='$f_n'";
    mysqli_query($c, $sql);
}


function get_ln_id_by_ln_n($ln_n, $c){
    $sql_str = "SELECT `lang_id` FROM `languages` WHERE `lang_name`='$ln_n'";
    $r = mysqli_query($c, $sql_str);
    $arr = mysqli_fetch_assoc($r);
    if (isset($arr['lang_id'])) {
        return $arr['lang_id'];
    }
    return false;
}


function get_ln_n_by_ln_id($ln_id, $c) {
    $sql = "SELECT `lang_name` FROM `languages` WHERE `lang_id`=$ln_id";
    $r = mysqli_query($c, $sql);
    $ar = mysqli_fetch_assoc($r);
    return($ar['lang_name']);
}


function get_ar_lns($c) {
    $sql_str = "SELECT * FROM `languages`";
    $r = mysqli_query($c, $sql_str);
    while ($arr[] = mysqli_fetch_assoc($r)) {
        $arr2 = $arr;
    }
    if (isset($arr2)) {
        return $arr2;
    }
    return false;
}


function get_f_n_by_f_id($f_id, $c) {
    $sql = "SELECT `phrase_name` FROM `phrases` WHERE `phrase_id`=$f_id";
    $r = mysqli_query($c, $sql);
    $ar = mysqli_fetch_assoc($r);
    return($ar['phrase_name']);
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

function get_ar_fs_by_f_n($f_n, $c) {
    $sql = "SELECT * FROM `phrases` WHERE `phrase_name`='$f_n'";
    $r = mysqli_query($c, $sql);
    while ($ar[] = mysqli_fetch_assoc($r)) {
        $ar2 = $ar;
    }
    if (isset($ar2)) {
        return $ar2;
    }   return false;
}



function get_ar_fs_by_f_v($f_v, $c){
     $sql = "SELECT * FROM `phrases` WHERE `phrase_value`='$f_v'";
     $r = mysqli_query($c, $sql);
     while ($ar[] = mysqli_fetch_assoc($r)) {
         $ar2 = $ar;
     }
     if (isset($ar2)) {
        return $ar2;
     }  return false;
}

function ar_fs_mix($ar_fs_en, $ar_fs_cur, $cur_ln) {
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

function ad_ln_n2ar_found_fs_from_ar_lns($ar_found_fs, $ar_lns){
    for ($k_f=0; $k_f < count($ar_found_fs); $k_f++) { 
        for ($k_l=0; $k_l < count($ar_lns); $k_l++) { 
            if ($ar_found_fs[$k_f]['phrase_lang_id'] == $ar_lns[$k_l]['lang_id']) {
                $ar_found_fs[$k_f]['f_ln_n'] = $ar_lns[$k_l]['lang_name'];
            }
        }
    }
    return $ar_found_fs;
}

$en_ln_id = get_ln_id_by_ln_n('en', $c);

if (isset($_POST['cur_ln'])) {
    $cur_ln = $_POST['cur_ln'];
} else {
    $cur_ln = $en_ln_id;
}

$ar_lns = get_ar_lns($c);

if (isset($_POST['ln_ad']) && !empty($_POST['ln_n'])) {
    $ln_n = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['ln_n']);
    if (!ex_ln_of_n($ln_n, $c)) {
        ln_ad($ln_n, $c);
    }
}



if (isset($_POST['ln_e']) && !empty($_POST['ln_id']) && !empty($_POST['ln_n'])) {
    $ln_n = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['ln_n']);
    $ln_id = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['ln_id']);
  
    ln_e($ln_id, $ln_n, $en_ln_id, $c);
}



if (isset($_POST['ln_d']) && !empty($_POST['ln_id'])) {
    $ln_n = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['ln_n']);
    $ln_id = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['ln_id']);
    ln_d($ln_id, $en_ln_id, $c);
}



if (isset($_POST['f_ad']) && !empty($_POST['f_v']) && !empty($_POST['f_n'])) {
    $f_n = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_n']);
    $f_v = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_v']);
    if (!ex_f_of_n($f_n, $en_ln_id, $c)) {
        f_ad($f_n, $f_v, $en_ln_id, $c);
    }
}

if (isset($_POST['f_n_e']) && !empty($_POST['f_id']) && !empty($_POST['f_n'])) {
    $f_id = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_id']);
    $f_n_new = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_n']);
    $f_n_old = get_f_n_by_f_id($f_id, $c);
    upd_f_n_by_old_f_n($f_n_old, $f_n_new, $c);
}

if (isset($_POST['f_v_e']) && !empty($_POST['f_v']) && !empty($_POST['f_id']) && !empty($_POST['cur_ln'])) {
    $f_v = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_v']);
    $f_id = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_id']);
    $f_ln_id = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_ln_id']);                                      
    if ($cur_ln == $f_ln_id) {
         upd_f_v_by_f_id($f_id, $f_v, $c);
     } else {
        $f_n_old = get_f_n_by_f_id($f_id, $c);
        f_ad($f_n_old, $f_v, $cur_ln, $c);
     }
}



if (isset($_POST['f_d']) && !empty($_POST['f_id'])) {
    $f_id = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_id']);
    if (ex_f_of_f_id($f_id, $c)) {
        $f_n_old = get_f_n_by_f_id($f_id, $c);
        del_fs_by_n($f_n_old, $c);
    }

}

if (isset($_POST['find_f_by_n']) && !empty($_POST['f_n'])) {
    $f_n = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_n']);
    $ar_found_fs = get_ar_fs_by_f_n($f_n, $c);
    if ($ar_found_fs) {
        $ar_found_fs = ad_ln_n2ar_found_fs_from_ar_lns($ar_found_fs, $ar_lns);
    }
}

if (isset($_POST['find_f_by_v']) && !empty($_POST['f_v'])) {
    $f_v = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['f_v']);
    $ar_found_fs = get_ar_fs_by_f_v($f_v, $c);
    if ($ar_found_fs) {
        $ar_found_fs = ad_ln_n2ar_found_fs_from_ar_lns($ar_found_fs, $ar_lns);
    }
}

$ar_lns = get_ar_lns($c);

$ar_fs_en = get_ar_fs_by_ln_id($en_ln_id, $c);

if (isset($cur_ln) && $cur_ln != $en_ln_id && $ar_fs_en) {
    $ar_fs_cur = get_ar_fs_by_ln_id($cur_ln, $c);
} else {
    $ar_fs_cur = false;
}

if ($ar_fs_en && $ar_fs_cur) {
    $ar_fs_mix = ar_fs_mix($ar_fs_en, $ar_fs_cur, $cur_ln);
} elseif ($ar_fs_en) {
    $ar_fs_mix = $ar_fs_en;
} else {
    $ar_fs_mix = false;
}

?>

<section class="langs">
    <h2>Existing languages</h2>
<?php foreach ($ar_lns as $k => $v) { ?>
    <form class="lang" method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>">
        <input type="text" class="lang__name" name="ln_n" value="<?= $v['lang_name'] ?>">
<?php if ($v['lang_id'] != $en_ln_id) { ?>
        <input type="submit" name="ln_e" value="Edit" class="lang_edit">
<?php } if (isset($cur_ln)) { ?>
        <input type="hidden" name="cur_ln" value="<?= $cur_ln ?>">
<?php } ?>
        <input type="hidden" name="ln_id" value="<?= $v['lang_id'] ?>">
    </form>

<?php } ?>
    <hr class="lang__hr">
    <form class="lang__add-sect" method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>">
        <input type="text" name="ln_n" class="ad_ln_ln_nm">
        <input type="submit" name="ln_ad" value="Add new language" class="lang_new_submit">
<?php if (isset($cur_ln)) { ?>
        <input type="hidden" name="cur_ln" value="<? $cur_ln ?>">
<?php } ?>
    </form>
</section>


<section class="adm_select_lng2show_phrs">
    <form method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>" class="adm_select_lng2show_phrs-form">
        <p class="adm_select_lng2show_phrs-form-label">Select_phrases_lang:</p>
        <select name="cur_ln" class="adm_select_lng2show_phrs-form-sel">
<?php foreach ($ar_lns as $k => $v) { 
        if (isset($cur_ln) && $cur_ln == $v['lang_id'] ) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
    ?>
            <option value="<?= $v['lang_id'] ?>" <?= $selected ?> class="adm_select_lng2show_phrs-form-opt">
               <?= $v['lang_name'] ?>
            </option>
<?php } ?>
        </select>
        <input type="submit" name="" value="show_phrases" class="adm_select_lng2show_phrs-form-submit">
    </form>
</section>


<section class="adm_phrase_edit" id="find_n">
    <h2>Search phrases (all languages)</h2>
    <p class="adm_phrase_edit__name">find phrase by name:</p>
    <p class="adm_phrase_edit__value">find phrase by value: </p><br>
    <form method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>" class="adm_f_l__find_f_by_n-form">
            <input type="text" name="f_n" class="adm_f_l__find_f_by_n-form-inp">
            <input type="submit" name="find_f_by_n" value="find phrase by name" class="adm_phrase_edit__form_submit-edit">
            <input type="hidden" name="cur_ln" value="<?= $cur_ln ?>">
    </form>

    <form method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>" class="adm_f_l__find_f_by_v-form">
            <textarea name="f_v" class="adm_f_l__find_f_by_v-form-inp"></textarea>
            <input type="submit" name="find_f_by_v" value="find phrase by value" class="adm_f_l__find_f_by_v-form-submit">
            <input type="hidden" name="cur_ln" value="<?= $cur_ln ?>">
            <input type="hidden" name="cur_ln" value="<?= $cur_ln ?>">
    </form>
</section>

<?php 
if (isset($_POST['find_f_by_n']) || isset($_POST['find_f_by_v'])) {
?>
<section class="adm_phrase_edit">
<h2>Found phrases:</h2>
<?php 
    if (isset($ar_found_fs) && $ar_found_fs) { ?>
<p class="adm_phrase_edit__name">phrase_name:</p>
<p class="adm_phrase_edit__value">phrase_value: </p>
<?php


         foreach ($ar_found_fs as $k => $v) { ?>

    <form method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>" class="adm_phrase_edit__form">
            <input type="text" name="f_n" class="adm_phrase_edit__form-name-inp" value="<?= $v['phrase_name'] ?>">
            <input type="submit" name="f_n_e" value="edit name" class="adm_phrase_edit__form_submit-edit">
            <textarea name="f_v" class="adm_phrase_edit__form-value-inp"><?= $v['phrase_value'] ?></textarea>
            <span class="found_f_ln_n"><?= $v['f_ln_n'] ?></span>
            <input type="submit" name="f_v_e" value="edit value" class="adm_phrase_edit__form_submit-edit">
            <input type="submit" name="f_d" value="delete" class="adm_phrase_edit__form_submit-del">
            <input type="hidden" name="f_id" value="<?= $v['phrase_id'] ?>">
            <input type="hidden" name="cur_ln" value="<?= $cur_ln ?>">
            <input type="hidden" name="f_ln_id" value="<?= $v['phrase_lang_id'] ?>">
    </form>
 
<?php
        }
    } else { ?>
    <p class="mess_found_phrases">No phrases found</p>
<?php   
    } ?>

</section>
<?php
} ?>

<section class="phrase_add" id="new">
    <h2>New prase creating in English language</h2>
    <form method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>" class="phrase_add_en-form">
            <p class="phrase_add_en-name-label">phrase_name:</p>
            <p class="phrase_add_en-value-label">phrase_value_EN:</p><br>
            <input type="text" name="f_n" class="phrase_add_en-name-inp">
            <textarea name="f_v" class="phrase_add_en-value-inp"></textarea>
        <input type="submit" name="f_ad" value="create" class="phrase_add_en-submit">
        <input type="hidden" name="cur_ln" value="<?= $cur_ln ?>">
    </form>
    <hr class="hr_w_90">
</section>


<section class="adm_phrase_edit" id="f_e_d">
<h2 tabindex="1">Edit phrases in chosen language. Or delete phrase in all languages</h2>
<p class="adm_phrase_edit__name">phrase_name:</p>
<p class="adm_phrase_edit__value">phrase_value: </p>
<?php
if ($ar_fs_mix) {

    foreach ($ar_fs_mix as $k => $v) {
        if ($cur_ln != $en_ln_id) {
            

            if ($cur_ln != $v['phrase_lang_id']) {
                $mes_f_in_cur_ln_no_exist = "<p class='message_ph_in_cur_ln_not_exists'> ! </p>";
            } else {
                $mes_f_in_cur_ln_no_exist = '';
            }



        } else {
            $mes_f_in_cur_ln_no_exist = '';
        }
?>

    <form method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>" class="adm_phrase_edit__form">
            <input type="text" name="f_n" class="adm_phrase_edit__form-name-inp" value="<?= $v['phrase_name'] ?>">
            <input type="submit" name="f_n_e" value="edit name" class="adm_phrase_edit__form_submit-edit">
            <textarea name="f_v" class="adm_phrase_edit__form-value-inp"><?= $v['phrase_value'] ?></textarea>
            <?= $mes_f_in_cur_ln_no_exist ?>
            <input type="submit" name="f_v_e" value="edit value" class="adm_phrase_edit__form_submit-edit">
            <input type="submit" name="f_d" value="delete" class="adm_phrase_edit__form_submit-del">
            <input type="hidden" name="f_id" value="<?= $v['phrase_id'] ?>">
            <input type="hidden" name="cur_ln" value="<?= $cur_ln ?>">
            <input type="hidden" name="f_ln_id" value="<?= $v['phrase_lang_id'] ?>">
    </form>
 
<?php
    }
}
?>

</section>


<?php
include_once('../inc/template/adm-lang-phrases-footer.php');
?>