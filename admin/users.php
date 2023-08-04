<?php
session_start();
if(!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../helpers/FileCommander.php';
include_once '../helpers/DbQueries.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST['action'];
	switch ($action) {
		case 'alphabetically':
			break;
		case 'date':
			break;
		case 'balance':
			break;
		case 'delete_image':
			$image_id = (int) $_POST['image_id'];
			$image = getImageById($image_id);
			if (count($image) > 0) {
				$image = $image[0];
				if ($image['name'] !== '' && $image['name_original'] != '' && $image['name_thumbnail'] != '') {
					deleteImageActions($image_id);
					$respond = deleteImage($image_id);
					if ($respond) {
						$full_path = '../inc/assets/img/' . $image['name'];
						if (FileCommander::is_image($full_path))
							unlink($full_path);
						$full_path = '../inc/assets/img/' . $image['name_original'];
						if (FileCommander::is_image($full_path))
							unlink($full_path);
						$full_path = '../inc/assets/img/' . $image['name_thumbnail'];
						if (FileCommander::is_image($full_path))
							unlink($full_path);
					}
				}
			}
			break;
	}
}
if (!$_SESSION['admin']) {
	header('Location: ' . '/admin.php');
}
include_once('../config.php');

$uspp = 50;
$p = 1;
$lim_from = ($p - 1) * $uspp;

$c = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($c, "utf8");

function get_ar_all_users_from_db($c, $lim_from, $uspp)
{
	$s = "SELECT `id`, `firstname`, `surname`, `nickname`, `email`, `phone`, `birth`, `avatar` FROM `users` ORDER BY `nickname` ASC LIMIT $lim_from, $uspp";
	$r = mysqli_query($c, $s);
	while ($ar[] = mysqli_fetch_assoc($r)) {
		$ar2 = $ar;
	}
	if (isset($ar2)) {
		return $ar2;
	}
	return false;
}

function get_col_recs($c, $tb)
{
	$s = "SELECT COUNT(*) AS count FROM `$tb`";
	$r = mysqli_query($c, $s);
	$ar = mysqli_fetch_assoc($r);
	return $ar['count'];
}

$ar_all_users = get_ar_all_users_from_db($c, $lim_from, $uspp);

$kol_recs = get_col_recs($c, 'users');
$kol_pages = ceil($kol_recs / $uspp);

$l = 5;
$r = 5;

$d = $kol_pages;
$c = 1;
$db = 1;
$de = $db + $d - 1;
$lb = $c - $l;
if ($lb < $db) {
	$lb = $db;
}
$re = $c + $r;
if ($re > $de) {
	$re = $de;
}
$prev = $c - 1;
$next = $c + 1;
$first = $db;
$last = $de;
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../inc/assets/css/adm-users.css">
	<style>
		.container {
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			align-items: center;
			margin: 0 auto;
			max-width: 2000px;
			padding: 20px;
			gap: 10px;
		}

		.image-wrapper {
			position: relative;
			/* margin: 20px; */
		}

		.image {
			/* display: block; */
			width: 150px;
			height: 150px;
			object-fit: contain;
			transition: transform 0.3s ease-in-out;
		}

		.image:hover {
			transform: scale(1.05);
		}

		.button-wrapper {
			position: absolute;
			bottom: 10px;
			left: 50%;
			transform: translateX(-50%);
			display: flex;
			justify-content: space-between;
			align-items: center;
			width: 120px;
		}

		button {
			background-color: #555;
			color: #fff;
			border: none;
			padding: 10px;
			border-radius: 5px;
			cursor: pointer;
			font-size: 16px;
			transition: background-color 0.3s ease-in-out;
		}

		button:hover {
			background-color: #333;
		}

		input[type=submit] {
			background-color: #555;
			color: #fff;
			border: none;
			padding: 10px;
			border-radius: 5px;
			cursor: pointer;
			font-size: 16px;
			transition: background-color 0.3s ease-in-out;
		}

		input[type=submit]:hover {
			background-color: #333;
		}
	</style>
</head>

<body>

	<h1>Пользователи:</h1>
	<div>
		<form action="javascript:void(0)" style="display:inline-block;">
			<input type="text" placeholder="Найти пользователя по никнейму" id="adm_find_users _by_nick_inp">
			<input type="submit" value="Найти" onclick="admFindUsersByNick(1)">
		</form>
		<div style="display:inline-block;">
			<span>Sort by</span>
			<select id="sort" onchange='admFindUsersByNick()'>
				<option value="1" selected>Alphabetically</option>
				<option value="2">Alphabetically from end</option>
				<option value="3">Registration date</option>
				<option value="4">Registration date from end</option>
				<option value="5">Balance amount(min to max)</option>
				<option value="6">Balance amount(max to min)</option>
			</select>
		</div>
	</div>
	<hr>

	<div id="adm_users__block_h">
		<div class="adm_users__block">

			<?php
			if (!$ar_all_users) { ?>
				<div style="background: red;">No users found</div>
				<?php
			} else {
				foreach ($ar_all_users as $i => $v) {
					?>
					<a href="javascript:void(0)" title=" пользователь: <?= $v['nickname'] ?> " class="adm_users__list-user"
						onclick="adm_get_user_info_by_id(<?= $v['id'] ?>);user_private_info(<?= $v['id'] ?>);">
						<?= $v['nickname'] ?>
					</a>
					<br>
					<?php
				}
			}
			?>



		</div>


		<div class="adm_users__pagin">



			<?php
			if ($prev < $db) {
				echo ("<a href='javascript:void(0)' class='not_activ'>prev</a>");
			} else {
				echo ("<a href='javascript:void(0)' class='previous' onclick='admFindUsersByNick($prev)'>prev</a>");
			}

			if ($first !== $lb) {
				echo ("<a href='javascript:void(0)' class='first' onclick='admFindUsersByNick($first)'>first $first </a>");
			}

			if (($lb - $first) > 1) {
				echo ('...');
			}

			for ($i = $lb; $i <= $re; $i++) {
				if ($i == $c) {
					echo ("<a href='javascript:void(0)' class='c' onclick='admFindUsersByNick($i)'>$i</a>");
				} else {
					echo ("<a href='javascript:void(0)' onclick='admFindUsersByNick($i)'>$i</a>");
				}
			}

			if (($last - $re) > 1) {
				echo ('...');
			}

			if ($last !== $re) {
				echo ("<a href='javascript:void(0)' class='last' onclick='admFindUsersByNick($last)'>last $last </a>");
			}


			if ($next > $de) {
				echo ("<a href='javascript:void(0)' class='not_activ'>next</a>");
			} else {
				echo ("<a href='javascript:void(0)' class='next' onclick='admFindUsersByNick($next)'>next</a>");
			}
			?>



		</div>
	</div>

	<hr>
	<div class="adm_users__user_info" id="user_private_info"></div>
	<div class="adm_users__user_info" id="adm_users__user_info"></div>
</body>

<script type="text/javascript">
	function adm_get_user_info_by_id(id) {

		const request = new XMLHttpRequest();
		request.onreadystatechange = () => {
			if (request.readyState === 4 && request.status === 200) {
				document.getElementById('adm_users__user_info').innerHTML = request.responseText;
			} else {
			}
		};
		var url_str = 'adm_users_show_info_ajax.php?id=' + id;
		request.open('GET', url_str, true);
		request.send();

	}

	function user_private_info(id) {
		const request = new XMLHttpRequest();
		request.onreadystatechange = () => {
			if (request.readyState === 4 && request.status === 200) {
				console.log(request.responseText);
				document.getElementById('user_private_info').innerHTML = request.responseText;
			} else {
			}
		};
		var url_str = 'user_private_info.php?id=' + id;
		request.open('GET', url_str, true);
		request.send();
	}

	function admFindUsersByNick(page = undefined) {
		let nickname = document.getElementById('adm_find_users _by_nick_inp').value;
		let sort = document.getElementById('sort').value;
		if (page == undefined) {
			let current = document.querySelector('.c');
			if (current) {
				page = current.innerText;
			} else {
				page = '';
			}
		}
		let form_data = new FormData();
		form_data.append('page', page);
		form_data.append('nick', nickname);
		form_data.append('sort', sort);
		let xhr = new XMLHttpRequest();
		xhr.open("POST", 'adm-users-f3-us-by-nick.php', true);
		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4 && xhr.status === 200) {
				document.getElementById('adm_users__block_h').innerHTML = xhr.responseText;
			}
		};
		xhr.send(form_data);
	}

	function setConsiderVoice() {
		let voice = document.querySelector('.consider-voice').checked;
		let user_id = document.querySelector('#user_id').value;
		let form_data = new FormData();
		form_data.append('voice', Number(voice));
		form_data.append('user', user_id);
		form_data.append('action', 'voice');
		let xhr = new XMLHttpRequest();
		xhr.open("POST", 'user_ajax.php', true);
		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4 && xhr.status === 200) {
			}
		};
		xhr.send(form_data);
	}

	function deleteUser(id) {
		let form_data = new FormData();
		form_data.append('id', id);
		let xhr = new XMLHttpRequest();
		xhr.open("POST", 'ajax/delete_user.php', true);
		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4 && xhr.status === 200) {
				document.querySelector('.mes').innerHTML = xhr.responseText;
				document.querySelector('.del-but').remove();
				setTimeout(() => { reloadCurrentPage() }, 3000);
			} else if (xhr.readyState === 4 && xhr.status === 400) {
				document.querySelector('.mes').innerHTML = xhr.responseText;
			}
		};
		xhr.send(form_data);
	}

	function reloadCurrentPage() {
		window.location.href = document.URL;
	}
</script>

</html>