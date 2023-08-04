<?php
session_start();
if (!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../../config.php';
include_once '../../helpers/multilang.php';
include_once '../../helpers/FileCommander.php';
include_once '../../helpers/DbQueries.php';
include_once '../../helpers/Validation.php';
include_once '../../helpers/ECommerceLogic.php';

$set_id = (int) $_POST['set_id'];
$set = getSetById($set_id)[0];
$players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
$images_id = [];
foreach ($players as $player) {
    $player_data = explode(':', $player);
    array_push($images_id, $player_data[1]);
}
$images_id = implode(',', $images_id);
$images = getSetImages($images_id);
foreach ($images as &$image) {
    $image['current_likes'] = (int) getAllLikesOfImage($image['id'])[0]['likes'];
    $image['current_dislikes'] = (int) getAllDislikesOfImage($image['id'])[0]['dislikes'];
}
usort($images, function ($image1, $image2) {
    if ((int) $image1['current_likes'] < (int) $image2['current_likes']) {
        return 1;
    }
    if ((int) $image1['current_likes'] == (int) $image2['current_likes']) {
        return 0;
    }
    if ((int) $image1['current_likes'] > (int) $image2['current_likes']) {
        return -1;
    }
});
?>
<div class="container-images">
    <?php foreach ($images as $img) { ?>
        <div class="image-wrapper">
            <img class="image" src="../../inc/assets/img/<?= $img['name'] ?>" alt="Image 1">
            <div style="text-align: center; font-size: 24px;">
                <?= 'Likes: ' . $img['current_likes']; ?>
                <?= 'Dislikes: ' . $img['current_dislikes']; ?>
            </div>
            <div style="text-align: center;display: flex;justify-content: center;align-items: center; font-size: 24px;">
                <span>winner</span>
                <input type="checkbox" class="winners" onchange="countChecked()" value="<?= $img['id'] ?>">
            </div>
        </div>
    <?php } ?>
</div>
<div style="display: flex; justify-content: center;"><button class="button" onclick="endSet(<?= $set_id ?>)">End Set</button></div>
<div class="count"><span class="checked-image">0</span>/<?= $set['pur_photos'] ?></div>