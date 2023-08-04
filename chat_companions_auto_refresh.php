<?php
session_start();
include_once('config.php');
include_once('chat-fns.php');

$cur_user_id = get_user_id_from_session();


$c_chat = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($c_chat, "utf8");


$chat_with_user_id = get_chat_with_user_id_from_get();

$chat_with_user_name = get_user_name_by_id_from_db($c_chat, $chat_with_user_id);

$ar_companions = get_ar_companions_from_db($c_chat, $cur_user_id);

?>

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
		(<div class="chat__companions-quantity"><?= $companion['not_sent_q'] ?></div>)
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