<?php
session_start();
include_once('config.php');
include_once('chat-fns.php');

$cur_user_id = get_user_id_from_session();

if (!$cur_user_id) {
	echo ('you must log in!');
	exit();
}

$c_chat = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($c_chat, "utf8");

$ar_all_users = get_ar_all_users_from_db($c_chat);

$chat_with_user_id = get_chat_with_user_id_from_get();

$chat_with_user_name = get_user_name_by_id_from_db($c_chat, $chat_with_user_id);

if (isset($_POST['chat_send_mess_submit']) && !empty($_POST['chat_send_mess_value']) && $chat_with_user_id) {
	$msg_2_add = str_replace(['\'', '"'], ['&#039', '&quot'], $_POST['chat_send_mess_value']);
	$mess_time_unix = $_SERVER['REQUEST_TIME'];
	add_msg_2_db($c_chat, $msg_2_add, $cur_user_id, $chat_with_user_id, $mess_time_unix);
}

$ar_companions = get_ar_companions_from_db($c_chat, $cur_user_id);

if ($chat_with_user_id) {
	$chat_with_user_id_js = $chat_with_user_id;
	$ar_msgs = get_msgs_of_2_users_from_db($c_chat, $cur_user_id, $chat_with_user_id);
} else {
	$chat_with_user_id_js = '';
	$ar_msgs = false;
}
if ($ar_msgs == false) {
}


if (isset($_GET['sent']) && $_GET['sent'] == 1) {
	upd_msgs_from_u1_to_u2_sent($c_chat, $chat_with_user_id, $cur_user_id);
}


?>







<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>chat</title>
	<link rel="stylesheet" href="inc/assets/css/chat.css">
</head>

<body>



	<?php
	if (!$ar_all_users) { ?>
		<div style="background: red;">No users found</div>
		<?php
	} else {
		foreach ($ar_all_users as $v) { ?>
			<a href="chat.php?chat_with_user_id=<?= $v['id'] ?>"><?= $v['nickname'] ?></a>
			<?php
		}
	}
	?>

	<br><br>

	<div class="chat__companions" id="chat__companions">

		<?php
		$companion_in_ar_companions_exists = false;
		if ($ar_companions) {
			foreach ($ar_companions as $companion) {
				if ($companion['id'] == $chat_with_user_id) {
					$a_class = 'chat__companions-a_chat_with';
					$companion_in_ar_companions_exists = true;
				} else {
					$a_class = 'chat__companions-a';
				}
				?>
				<a href="chat.php?chat_with_user_id=<?= $companion['id'] ?>&sent=1" class="<?= $a_class ?>">
					<?= $companion['nickname'] ?>
				</a>
				<?php
				if ($companion['not_sent_q'] != 0) {
					?>
					(<div class="chat__companions-quantity">
						<?= $companion['not_sent_q'] ?>
					</div>)
					<?php
				}
			}
		}
		if ($chat_with_user_id && $chat_with_user_name && !$companion_in_ar_companions_exists) {
			$a_class = 'chat__companions-a_chat_with';
			?>
			<a href="chat.php?chat_with_user_id=<?= $chat_with_user_id ?>&sent=1" class="chat__companions-a_chat_with">
				<?= $chat_with_user_name ?>
			</a>
			<?php
		}
		?>

	</div>

	<div class="chat__communication">

		<div class="chat__communication-msgs" id="chat__communication-msgs">
			<?php
			if ($ar_msgs) {
				foreach ($ar_msgs as $ar_msg) {

					$mess_time_unix = date("Y-m-d", $ar_msg['mess_time_unix']);
					if ($ar_msg['mess_from_user_id'] == $cur_user_id) {
						?>
						<div class="chat__communication-msg">
							<span class="chat__communication-msg-from_you">You:</span>
							<p class="chat__communication-msg-you">
								<?= $ar_msg['mess_value'] ?>
							</p>
							<p class="chat__communication-msg-time">
								<?= $mess_time_unix ?>
							</p>
						</div>
						<?php
					} elseif ($ar_msg['mess_read'] == 0) {
						?>
						<div class="chat__communication-msg" onclick="chat_make_msg_read(<?= $ar_msg['mess_id'] ?>)">
							<span class="chat__communication-msg-from_he">he:</span>
							<div class="chat__communication-msg-he-circle"></div>
							<p class="chat__communication-msg-he">
								<?= $ar_msg['mess_value'] ?>
							</p>
							<p class="chat__communication-msg-time">
								<?= $mess_time_unix ?>
							</p>
						</div>
						<?php
					} else {
						?>
						<div class="chat__communication-msg">
							<span class="chat__communication-msg-from_he">he:</span>

							<p class="chat__communication-msg-he">
								<?= $ar_msg['mess_value'] ?>
							</p>
							<p class="chat__communication-msg-time">
								<?= $mess_time_unix ?>
							</p>
						</div>
						<?php
					}
				}
			}
			?>
		</div>
		<form method="post" action="chat.php?chat_with_user_id=<?= $chat_with_user_id ?>">
			<textarea class="chat__communication-msg" name="chat_send_mess_value"></textarea>
			<input type="submit" name="chat_send_mess_submit" value="Send">
		</form>
	</div>

</body>
<?php if ($chat_with_user_id) { ?>
	<script type="text/javascript">
		const chat_with_user_id = <?= $chat_with_user_id_js ?>;
		const cur_user_id = <?= $cur_user_id ?>
	</script>
	<script src="inc/js/chat-msgs-auto-refresh.js"></script>
	<script src="inc/js/chat-companions-auto-refresh.js"></script>
<?php } ?>
<script type="text/javascript" src="inc/js/chat_make_msg_read.js"></script>

</html>