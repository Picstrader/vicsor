<?php
session_start();
if(!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../helpers/Validation.php';
include_once '../helpers/DbQueries.php';
include_once '../helpers/FileCommander.php';
?>
<section style="display: flex;">
    <div style="width: 200px;border-right: 1px solid;height: 97vh;display: flex;flex-direction: column;gap: 15px;">
        <!-- <a href="adm-lang-phrases.php" target="right-block">Перевод</a> -->
        <!-- <a href="background-moderating.php" target="right-block">Фон</a> -->
        <!-- <a href="complains.php" target="right-block">Жалобы</a> -->
        <!-- <a href="images-moderating.php" target="right-block">Модерация фото</a> -->
        <!-- <a href="subscription-moderating.php" target="right-block">Настройка подписки</a> -->
        <a href="withdraw-moderating.php" target="right-block">Вывод денег</a>
        <a href="active-sets.php" target="right-block">Сеты, активные</a>
        <a href="fake-sets.php" target="right-block">Сеты, фейковые</a>
        <a href="users.php" target="right-block">Список пользователей</a>
        <!-- <a href="referral-program.php" target="right-block">Реферальная подписка</a> -->
        <!-- <a href="add-to-gallery.php" target="right-block">Галерея, добавленные фото</a> -->
        <!-- <a href="winner-to-gallery.php" target="right-block">Галерея, победители</a> -->
        <!-- <a href="rating-moderating.php" target="right-block">Рейтинг, на голосование</a> -->
        <a href="faq-moderating.php" target="right-block">Модерация FAQ</a>
        <a href="promotion.php" target="right-block">Поднятие фото</a>
        <!-- <a href="website-benefit.php" target="right-block">Прибыль сайта</a> -->
        <!-- <a href="lot-ending-moderation.php" target="right-block">Лот, параметры завершения</a> -->
        <!-- <a href="reg-benefit.php" target="right-block">Бонус-сумма при регистрации</a> -->
    </div>
    <div style="padding-left: 20px;">
        <iframe name="right-block" style="width: 85vw;height: 97vh;border: none;">
    </div>
</section>
