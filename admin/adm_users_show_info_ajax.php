<?php
session_start();
if(!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../helpers/multilang.php';
include_once '../helpers/ECommerceLogic.php';

if (!isset($_GET['id'])) { echo 'user id undefined'; exit(); }

$id = $_GET['id']; 

$c = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); mysqli_set_charset($c, "utf8"); 

$s1 = "SELECT `nickname` FROM `users` WHERE `id`=$id";
$r1 = mysqli_query($c, $s1);
while ($ar_tmp1[] = mysqli_fetch_assoc($r1)) { $ar1 = $ar_tmp1; }

$user_name = $ar1[0]['nickname'];

$s = "SELECT * FROM `user_logs` WHERE `user_id`=$id ORDER BY id DESC";
$r = mysqli_query($c, $s);
while ($ar_tmp[] = mysqli_fetch_assoc($r)) {$ar = $ar_tmp;}

if (!isset($ar)) {echo '<h2>У пользователя <span class="adm_users__span">' . $user_name . '</span> нет логов.</h2>'; exit();}

?>
<h2>Логи пользователя<span class="adm_users__span"> <?= $user_name ?>:</span></h2>
<table class="adm_users__user_info-tb">
	<thead class="adm_users__user_info-tb-thead">
		<tr class="adm_users__user_info-tb-thead-tr">
			<td class="adm_users__user_info-tb-thead-tr-td">Log id</td>
			<td class="adm_users__user_info-tb-thead-tr-td">User id</td>
			<!-- <td class="adm_users__user_info-tb-thead-tr-td">type</td> -->
			<td class="adm_users__user_info-tb-thead-tr-td">Operation</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Balance before</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Balance after</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Operation sum</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Lot id</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Submission fee (A)</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Lot images total (B)</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Top-liked files in lot (C)</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Operation status</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Created time</td>
		</tr>
	</thead>
	<tbody class="adm_users__user_info-tb-tbody">
<?php
foreach ($ar as $i => $row) {
?>
		<tr class="adm_users__user_info-tb-tbody-tr">
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= $row['id'] ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= $row['user_id'] ?></td>
			<!-- <td class="adm_users__user_info-tb-tbody-tr-td"><?= $row['type'] ?></td> -->
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= /*$row['action']*/ECommerceLogic::prepareBalanceEmailLog($row) ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= (!$row['balance_old'] && !$row['balance_new']) ? '-' : $row['balance_old'] ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= (!$row['balance_old'] && !$row['balance_new']) ? '-' : $row['balance_new'] ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= (!$row['balance_old'] && !$row['balance_new']) ? '-' : abs($row['balance_new'] - $row['balance_old']) ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= $row['set_id'] ? $row['set_id'] : '-' ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= $row['cost'] ? $row['cost'] : '-' ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= $row['total_photos'] ? $row['total_photos'] : '-'  ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= $row['pur_photos'] ? $row['pur_photos'] : '-'  ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= $row['status'] ? 'Success' : 'Fail'  ?></td>
			<td class="adm_users__user_info-tb-tbody-tr-td"><?= $row['cur_time'] ?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>