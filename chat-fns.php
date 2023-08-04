<?php
function get_user_id_from_session() {
	if (isset($_SESSION['user_data']['id'])) {
		return $_SESSION['user_data']['id'];
	}	return false;
}

function get_user_id_from_get() {
	if (isset($_GET['cur_user_id'])) {
		return $_GET['cur_user_id'];
	}	return false;
}

function get_user_name_by_id_from_db($c, $id) {
	if ($id == false) {
		return false;
	}
	$s = "SELECT `nickname` FROM `users` WHERE `id`=$id";
	$r = mysqli_query($c, $s);
	if ($ar0 = mysqli_fetch_assoc($r)) {
		$ar1 = $ar0;
	}
	if (!isset($ar1)) {
		return false;
	}
	return $ar1['nickname'];
}

function get_ar_all_users_from_db($c) {
	$s = "SELECT `id`, `firstname`, `surname`, `nickname`, `email`, `phone`, `birth`, `avatar` FROM `users`";
	$r = mysqli_query($c, $s);
	while ($ar[] = mysqli_fetch_assoc($r)) {
		$ar2 = $ar;
	}
	if (isset($ar2)) {
		return $ar2;
	}	return false;
}

function get_chat_with_user_id_from_get() {
	if (isset($_GET['chat_with_user_id'])) {
		return $_GET['chat_with_user_id'];
	}	return false;
}


function get_ar_companions_from_db($c, $user_id) {
	$s = "SELECT `mess_from_user_id`, `mess_to_user_id` FROM `chat` WHERE `mess_from_user_id`=$user_id OR `mess_to_user_id`=$user_id";
	$r = mysqli_query($c, $s);
	while ($ar[] = mysqli_fetch_assoc($r)) {
		$ar2 = $ar;
	}
	if (!isset($ar2)) {
		return false;
	}

	
	$ar3 = [];
	foreach ($ar2 as $v) {
		if ($v['mess_from_user_id'] != $user_id) {
			$ar3[] = $v['mess_from_user_id'];
		} else {
			$ar3[] = $v['mess_to_user_id'];
		}
	}
	
	$ar3 = array_unique($ar3);

	$ar4 = [];
	$ar5 = [];
	foreach ($ar3 as $v) {
		$s_u = "SELECT `id`, `firstname`, `surname`, `nickname`, `email`, `phone`, `birth`, `avatar` FROM `users` WHERE `id`=$v";
		$r = mysqli_query($c, $s_u);
		if ($ar4 = mysqli_fetch_assoc($r)) {
			
			$s_m_q = "SELECT COUNT(*) AS count FROM `chat` WHERE `mess_from_user_id`=$v AND `mess_to_user_id`=$user_id AND `mess_sent`=0";
			$r2 = mysqli_query($c, $s_m_q);
			$m_c = mysqli_fetch_assoc($r2);
			$ar4['not_sent_q'] = $m_c['count'];
			$ar5[] = $ar4;
		} else {
			$ar5[] = false;
		}
	}
	return $ar5;
}

function get_msgs_of_2_users_from_db($c, $id1, $id2) {
	$s = "SELECT * FROM `chat` WHERE (`mess_from_user_id`=$id1 AND `mess_to_user_id`=$id2) OR (`mess_from_user_id`=$id2 AND `mess_to_user_id`=$id1)";
	$r = mysqli_query($c, $s);
	while ($ar[] = mysqli_fetch_assoc($r)) {
		$ar2 = $ar;
	}
	if (isset($ar2)) {
		return $ar2;
	} else {
		return false;
	}
}



function add_msg_2_db($c, $msg_2_add, $from_id, $to_id, $time) {
	$s = "INSERT INTO `chat` (`mess_value`, `mess_from_user_id`, `mess_to_user_id`, `mess_time_unix`, `mess_sent`, `mess_read`)
	VALUES ('$msg_2_add', $from_id, $to_id, '$time', 0, 0)";
	mysqli_query($c, $s);
}

function upd_msgs_from_u1_to_u2_sent($c, $id1, $id2) {
	$s = "UPDATE `chat` SET `mess_sent`=1 WHERE `mess_from_user_id`=$id1 AND `mess_to_user_id`=$id2";
	mysqli_query($c, $s);
}


?>