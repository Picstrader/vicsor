<?php
include_once('config.php');
include_once('chat-fns.php');

$mess_id = $_GET['mess_id'];

$c_chat = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($c_chat, "utf8");

$s = "UPDATE `chat` SET `mess_read`=1 WHERE `mess_id`=$mess_id";
mysqli_query($c_chat, $s);





?>