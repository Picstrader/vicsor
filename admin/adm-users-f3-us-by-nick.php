<?php
session_start();
function getSort($type) {
	$order_by = '';
	switch($type) {
		case '1':
			$order_by = "ORDER BY nickname ASC";
			break;
		case '2':
			$order_by = "ORDER BY nickname DESC";
			break;
		case '3':
			$order_by = "ORDER BY reg_date DESC";
			break;
		case '4':
			$order_by = "ORDER BY reg_date ASC";
			break;
		case '5':
			$order_by = "ORDER BY balance ASC";
			break;
		case '6':
			$order_by = "ORDER BY balance DESC";
			break;
		default:
		    $order_by = "ORDER BY nickname";
		    break;
	}
	return $order_by;
}
if(!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once('../config.php');

function get_col_recs($c, $tb, $nick) {
	$s = "SELECT COUNT(*) AS count FROM `$tb` WHERE `nickname` LIKE '%$nick%'";
	$r = mysqli_query($c, $s);
	$ar = mysqli_fetch_assoc($r);
	return $ar['count'];
}
$sort = getSort($_POST['sort']);
$nick = /*$_GET['nick']*/$_POST['nick'];
if (stripos($nick, "'") !== false) { echo $not_found_request; exit(); }

$p = /*$_GET['p']*/$_POST['page'];

$uspp = 50;

$lim_from = ($p - 1) * $uspp;

$db_connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); mysqli_set_charset($db_connect, "utf8"); 



$kol_recs = get_col_recs($db_connect, 'users', $nick);
$kol_pages = ceil($kol_recs / $uspp);

$l = 5;
$r = 5;

$d = $kol_pages;
$c = $p;
$db = 1;
$de = $db + $d - 1;
$lb = $c-$l; if ($lb < $db) { $lb = $db; }
$re = $c + $r; if ($re > $de) {$re = $de; }
$prev = $c - 1;
$next = $c + 1;
$first = $db;
$last = $de;

$not_found_request = '<h2>not found</h2><hr>';


// $s1 = "SELECT * FROM `users` WHERE `nickname` LIKE '%$nick%' ORDER BY `nickname` ASC LIMIT $lim_from, $uspp";
$s1 = "SELECT * FROM `users` WHERE `nickname` LIKE '%$nick%' $sort LIMIT $lim_from, $uspp";
$r1 = mysqli_query($db_connect, $s1);
while ($ar_tmp1[] = mysqli_fetch_assoc($r1)) { $ar1 = $ar_tmp1; }

if (!isset($ar1)) { echo $not_found_request; exit(); }

?>


<div class="adm_users__block">
<?php
foreach ($ar1 as $i => $v) {
?>
<a href="javascript:void(0)" 
	class="adm_users__list-user" 
	title=" пользователь: <?= $v['nickname'] ?> " 
	onclick="adm_get_user_info_by_id(<?= $v['id'] ?>);user_private_info(<?= $v['id'] ?>);">
		<?= $v['nickname'] ?>
</a>
<br>
<?php
}
?>

</div>


<div class="adm_users__pagin">
	


<?php

if ($prev < $db) { echo("<a href='javascript:void(0)' class='not_activ'>prev</a>"); }
else{ 			   echo("<a href='javascript:void(0)' class='previous' onclick='admFindUsersByNick($prev)'>prev</a>"); }


if ($first !== $lb) { echo("<a href='javascript:void(0)' class='first' onclick='admFindUsersByNick($first)'> $first </a>"); }

if (($lb - $first) > 1) { echo('...'); }


for ($i=$lb; $i <= $re; $i++) {
	if ($i == $c) {
		echo("<a href='javascript:void(0)' class='c' onclick='admFindUsersByNick($i)'>$i</a>");
	}else {
		echo("<a href='javascript:void(0)' onclick='admFindUsersByNick($i)'>$i</a>");
	}
}

if (($last - $re) > 1) { echo('...'); }


if ($last !== $re) { echo("<a href='javascript:void(0)' class='last' onclick='admFindUsersByNick($last)'> $last </a>"); }


if ($next > $de) { echo("<a href='javascript:void(0)' class='not_activ'>next</a>"); }
else { 			   echo("<a href='javascript:void(0)' class='next' onclick='admFindUsersByNick($next)'>next</a>"); }
?>



</div>


