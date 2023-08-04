<?php
 $cost_min = $_GET['cost_min'];
 $cost_max = $_GET['cost_max'];

 $ph_in_set_min = $_GET['ph_in_set_min'];
 $ph_in_set_max = $_GET['ph_in_set_max'];

 $pur_min = $_GET['pur_min'];
 $pur_max = $_GET['pur_max'];

 if (isset($_GET['page'])) {
 	$page = $_GET['page'];
 } else {
 	$page = 1;
 }


 define('HOST', 'kyivst16.mysql.tools');
 define('USER', 'kyivst16_simplesalenft');
 define('PASSWORD', '84V;z^tn5C');
 define('DATABASE', 'kyivst16_simplesalenft');

define('RECS_PER_PG', 20);
define('PGS_ASIDE', 5);

$table = 'trade';


$connect = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

$sql = "SELECT * FROM $table";
$f = 0;//flag
if ($cost_min !== '') {
	$cost_min--;
	$sql.=" WHERE (`cost` > $cost_min)";
	$f++;
}


if ($cost_max !== '') {
	if ($f == 0) {
		$cost_max++;
		$sql.=" WHERE (`cost` < $cost_max)";
		$f++;
	}else{
		$cost_max++;
		$sql.=" AND (`cost` < $cost_max)";
		$f++;
	}
}

if ($ph_in_set_min !== '') {
	$ph_in_set_min--;
	if ($f == 0) {
		$sql.=" WHERE (`total_photos` > $ph_in_set_min)";
		$f++;
	}else{
		$sql.=" AND (`total_photos` > $ph_in_set_min)";
		$f++;
	}
}

if ($ph_in_set_max !== '') {
	if ($f == 0) {
		$ph_in_set_max++;
		$sql.=" WHERE (`total_photos` < $ph_in_set_max)";
		$f++;
	}else{
		$ph_in_set_max++;
		$sql.=" AND (`total_photos` < $ph_in_set_max)";
		$f++;
	}
}

if ($pur_min !== '') {
	$pur_min--;
	if ($f == 0) {
		$sql.=" WHERE (`pur_photos` > $pur_min)";
		$f++;
	}else{
		$sql.=" AND (`pur_photos` > $pur_min)";
		$f++;
	}
}

if ($pur_max !== '') {
	if ($f == 0) {
		$pur_max++;
		$sql.=" WHERE (`pur_photos` < $pur_max)";
		$f++;
	}else{
		$pur_max++;
		$sql.=" AND (`pur_photos` < $pur_max)";
		$f++;
	}
}

$sql_count = $sql;
$sql_count = str_replace('*', 'COUNT(*) AS count ', $sql_count);

$miss_recs = ($page - 1) * RECS_PER_PG;
if ($miss_recs < 0) { $miss_recs = 0; }
$sql .= " ORDER BY `id` DESC LIMIT $miss_recs, " . RECS_PER_PG;

$res = mysqli_query($connect, $sql);

while ($arr[] = mysqli_fetch_assoc($res)) {
  $arr2 = $arr;
}
$ajax_tb_str = '';
if (isset($arr2)) {
	foreach ($arr2 as $index => $row) {
		//Пересортировать и нормально сгенерировать массив
	  $ajax_tb_str.= '<tr class="trade_table__body-row">';

      $count_users = explode(";", $row['users_photos']);
      $count_users = count($count_users);
      $profit_usd = ($row['cost']*($row['total_photos']-$row['pur_photos']))/$row['pur_photos'];
      $profit_usd = $profit_usd*0.9;
      $profit_percent = $profit_usd*100/$row['cost'];
	  $ajax_tb_str.= '<td>' . $row['cost'] . '</td>';
	  $ajax_tb_str.= '<td>' . $row['total_photos'] . '</td>';
	  $ajax_tb_str.= '<td>' . $row['pur_photos'] . '</td>';
	  $ajax_tb_str.= '<td>'.round($profit_percent, 0).'</td>';
	  $ajax_tb_str.= '<td>'.round($profit_usd, 0).'</td>';
	  $ajax_tb_str.= '<td>'.$count_users.'/'.$row['total_photos'].'</td>';
	  $ajax_tb_str.= '<td>' . $row['time'] . '</td>';

	  $ajax_tb_str.= '</tr>';
	}
}


$res = mysqli_query($connect, $sql_count);
$ar_count = mysqli_fetch_assoc($res);
$tb_recs_quantity = $ar_count['count'];
$kol_pages = ceil($tb_recs_quantity / RECS_PER_PG);
$kol_pages = intval($kol_pages);

$l = PGS_ASIDE;
$r = PGS_ASIDE;

$d = $kol_pages;
$c = $page;
$db = 1;
$de = $db + $d - 1;
$lb = $c-$l; if ($lb < $db) { $lb = $db; }
$re = $c + $r; if ($re > $de) {$re = $de; }
$prev = $c - 1; //Check if $prev<$db will later
$next = $c + 1; //Check if $next>$de will later
$first = $db;
$last = $de;

$ajax_pagin_str = '';
//Вывод ссылки "Предыдущая страница"
if ($prev < $db) { $ajax_pagin_str .= "<a href='#' class='active_sets__pagination-not_activ' data-page='no'><img class='active_sets__pagination-not_activ-prev_img' src='./inc/assets/img/main-not-active-pg.png'></a>"; }
else{              $ajax_pagin_str .= "<a href='#' class='active_sets__pagination-previous' data-page='$prev'><img class='active_sets__pagination-previous-img' src='./inc/assets/img/main-prev-next-pg.png'></a>"; }

//Вывод ссылки "Первая страница"
if ($first !== $lb) { $ajax_pagin_str .= "<a href='#' class='active_sets__pagination-first' data-page='$first'>$first </a>"; }
// $ajax_pagin_str .= "<a href='#' class='active_sets__pagination-first' data-page='$first'>first $first </a>";

if (($lb - $first) > 1) { $ajax_pagin_str .= '...'; }

//Вывод ссылок основного блока, включая текущую. Текущей ссылке присваивается класс "c".
for ($i=$lb; $i <= $re; $i++) {
    if ($i == $c) {
        $ajax_pagin_str .= "<a href='#' class='active_sets__pagination-current' data-page='no'>$i</a>";
    }else {
        $ajax_pagin_str .= "<a href='#' class='active_sets__pagination-a' data-page='$i'>$i</a>";
    }
}

if (($last - $re) > 1) { $ajax_pagin_str .= '...'; }

//Вывод ссылки "Последняя страница"
if ($last !== $re) { $ajax_pagin_str .= "<a href='#' class='active_sets__pagination-last' data-page='$last'>$last</a>"; }

//Вывод ссылки "Next"
if ($next > $de) { $ajax_pagin_str .= "<a href='#' class='active_sets__pagination-not_activ' data-page='no'><img class='active_sets__pagination-not_activ-next_img' src='./inc/assets/img/main-not-active-pg.png'></a>"; }
else {             $ajax_pagin_str .= "<a href='#' class='active_sets__pagination-next' data-page='$next'><img class='active_sets__pagination-next-img' src='./inc/assets/img/main-prev-next-pg.png'></a>"; }

$ar_ajax = array();
$ar_ajax[0] = $ajax_tb_str;
$ar_ajax[1] = $ajax_pagin_str;
$ajax_json  = json_encode($ar_ajax);

echo($ajax_json);

?>