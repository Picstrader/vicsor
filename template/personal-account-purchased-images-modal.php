<?php
$price = 0;
if (isset($purchased_image_id)) {
    $fields = [];
    $fields['image_id'] = $purchased_image_id;
    $fields['user_id'] = isLogin() ? getLoginUserId() : 0;
    $purchased_image_data = getAllPartsOfPurchasedImage($fields);
    $owner_login = getUserNickname($purchased_image_data[0]['user_id']);
    $owner_login = $owner_login[0]['nickname'];
    $parts_amount = count($purchased_image_data);
    $logged_owner = null;
    if (count($purchased_image_data) > 0) {
        foreach ($purchased_image_data as $owner) {
            if ((int) $owner['user_id'] == (int) $fields['user_id']) {
                $price = (float) $owner['price'];
            }
        }
        $price = round($price, 2);
    }
}
if (isset($purchased_image_id)) {
    $image = getGalleryImageOriginal($purchased_image_id);
} else {
    $image = [];
}
if (count($image) > 0) {
    $image = $image[0];
    $image['profit_usdt'] = ECommerceLogic::getProfitUSDT($image);
    $image['profit_percent'] = ECommerceLogic::getProfitPercent($image);
    $image_file_data = FileCommander::get_image_file_data($image['name']);
    $fields['gallery_image_id'] = $image['id'];
    $favorite_image = isLogin() ? getFavoriteImage($fields) : [];
    if (count($favorite_image) > 0) {
        $is_favorite = true;
    } else {
        $is_favorite = false;
    }
} else {
    $image = [];
    $image['name'] = '';
    $image['profit_usdt'] = '';
    $image['profit_percent'] = '';
    $image_file_data = [];
    $image_file_data['width'] = 0;
    $image_file_data['height'] = 0;
    $image_file_data['size'] = 0;
    $is_favorite = false;
}
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/gallery.php";
?>
<section id="modalGallery" class="modal" style="display:none;">
    <div class="modal-content-gallery">
        <div class="gallery-images__modal-img" >
            <img style="content: url(./inc/assets/img/<?= $image['name_original'] ?>);width: 100%;height: 100%;object-fit: contain;">
            <div class="gallery-bloc-info">
                <div class="gallery-author-modal-block">
                <div class="personal_account_purchased_image__price">
                    <span id='change-price-text' style='color:#007AFF;font-weight: 600;font-size: 18px;line-height: 111.02%;letter-spacing: 0.025em;'></span>
                </div>
                <div class="personal_account_purchased_image__price">
                    <input type="number" class="pers_acc__purchased_input" value="<?= $price ?>"
                        placeholder="<?= $fs['Enter price'] ?>">
                    <button onclick="changePrice(this, <?= $image['image_id'] ?>)"
                        class="personal_account__purchased_button_set_price"><?= $fs['Set price'] ?>
                    </button>
                </div>
                <div class="personal_account_purchased_image__download">
                    <a href="inc/assets/img/<?= $image['name_original'] ?>" download><button
                            class="personal_account__purchased_button_download"><?= $fs['Download'] ?></button></a>
                            <button onclick="deleteGalleryImage(<?= $image['image_id'] ?>)"
                        class="personal_account__purchased_button_delete_image"><?= 'Delete' ?>
                    </button>
                </div>
                </div>
            </div>
        </div>
        <div class="gallery-images__modal-close-button-cover">
            <a class="close-gallery" onclick="hideGalleryModel()"><img class="img__del-gallery" src='/inc/assets/img/closebuttong.svg'></a>
        </div>
    </div>
</section>