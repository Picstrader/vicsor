<?php
session_start();
if (!$_SESSION['admin']) {
	header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../helpers/FileCommander.php';
include_once '../helpers/DbQueries.php';
include_once '../helpers/Validation.php';
include_once '../helpers/ECommerceLogic.php';
$user = getUserData($_GET['id']);
if ($user) {
	$user = $user[0];
} else {
	$user = [];
}
$images = getUserImagesToDelete($_GET['id']);
?>
<h2>Данные пользователя<span class="adm_users__span">
		<?= $user_name ?>:
	</span></h2>
<table class="adm_users__user_info-tb">
	<thead class="adm_users__user_info-tb-thead">
		<tr class="adm_users__user_info-tb-thead-tr">
			<td class="adm_users__user_info-tb-thead-tr-td">id</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Имя</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Фамилия</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Ник</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Баланс</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Email</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Телефон</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Дата рождения</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Почта подтверждена</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Телефон подтвержден</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Последний вывод средств</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Страна</td>
			<td class="adm_users__user_info-tb-thead-tr-td">Дата регистрации</td>
		</tr>
	</thead>
	<tbody class="adm_users__user_info-tb-tbody">
		<tr class="adm_users__user_info-tb-tbody-tr">
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $user['id'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $user['firstname'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $user['surname'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $user['nickname'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= (float) $user['balance'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $user['email'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $user['phone'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $rouserw['birth'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= (int) $user['verification'] ? 'Да' : 'Нет' ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= (int) $user['phone_verification'] ? 'Да' : 'Нет' ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $user['withdraw_last'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $user['country'] ?>
			</td>
			<td class="adm_users__user_info-tb-tbody-tr-td">
				<?= $user['reg_date'] ?>
			</td>
		</tr>
	</tbody>
</table>
<div class="container">
        <?php foreach ($images as $image) { ?>
            <div class="image-wrapper">
                <img class="image" src="../inc/assets/img/<?= $image['name_thumbnail'] ?>" alt="Image 1">
                <div class="button-wrapper">
                    <form style="margin-block-end: 0;" method="POST">
                        <input type="hidden" name="action" value="delete_image">
                        <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                        <input type="submit" value="Delete">
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
<h2>Настройки</h2>
<div>
	<div>
		<input type="checkbox" class="consider-voice" onchange="setConsiderVoice()" <?= (int) $user['voice'] ? 'checked' : '' ?>> Учитывать голос
		<input type="hidden" id="user_id" value="<?= $user['id'] ?>">
	</div>
	<div style="margin-top:30px;">
		<button onclick="deleteUser(<?= $user['id'] ?>)" class="del-but" style="color:white; background:red;">Удалить пользователя</button>
		<span class="mes"></span>
	</div>
</div>