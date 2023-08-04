<?php
session_start();
include_once('config.php');
include_once('chat-fns.php');

$cur_user_id = get_user_id_from_get();

$chat_with_user_id = get_chat_with_user_id_from_get();

if (!$cur_user_id || !$chat_with_user_id) {
	echo '';
	exit();
}

$c_chat = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($c_chat, "utf8");

$ar_msgs = get_msgs_of_2_users_from_db($c_chat, $cur_user_id, $chat_with_user_id);




if ($ar_msgs) {
	foreach ($ar_msgs as $ar_msg) {
		$mess_time_unix = date("Y-m-d", $ar_msg['mess_time_unix']);
		if ($ar_msg['mess_from_user_id'] == $cur_user_id) {
?>
			<div class="chat__communication-msg">
				<span class="chat__communication-msg-from_you">You:</span>
				<p class="chat__communication-msg-you"><?= $ar_msg['mess_value'] ?></p>
				<p class="chat__communication-msg-time"><?= $mess_time_unix ?></p>
			</div>
<?php
		} elseif ($ar_msg['mess_read'] == 0) {
?>
			<div class="chat__communication-msg" onclick="chat_make_msg_read(<?= $ar_msg['mess_id'] ?>)">
				<span class="chat__communication-msg-from_he">he:</span>
				<div class="chat__communication-msg-he-circle"></div>
				<p class="chat__communication-msg-he"><?= $ar_msg['mess_value'] ?></p>
				<p class="chat__communication-msg-time"><?= $mess_time_unix ?></p>
			</div>
<?php
		} else {
?>
			<div class="chat__communication-msg">
				<span class="chat__communication-msg-from_he">he:</span>
				<p class="chat__communication-msg-he"><?= $ar_msg['mess_value'] ?></p>
				<p class="chat__communication-msg-time"><?= $mess_time_unix ?></p>
			</div>
<?php
		}
	}
}















// $str_msgs_json = json_encode($ar_msgs);
// echo($str_msgs_json);






























?>