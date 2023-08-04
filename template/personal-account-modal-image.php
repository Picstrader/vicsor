<?php
$image = getImages(null, null, null, getLoginUserId(), $_POST['image_id'])[0];
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/gallery.php";
?>
<section id="modalGallery" class="modal" style="display:none;">
    <div class="modal-content-gallery">
        <div class="gallery-images__modal-img">
            <img
                style="content: url(./inc/assets/img/<?= $image['name_original'] ?>);width: 100%;height: 100%;object-fit: contain;">
            <div class="gallery-bloc-info">
                <div class="gallery-author-modal-block">
                    <?php if ($image['status'] != 'trading') { ?>
                        <div class="personal_account_purchased_image__price">
                            <span id='change-price-text' class="personal-account-price-text"></span>
                        </div>
                        <div class="personal_account_purchased_image__price">
                            <input type="number" class="pers_acc__purchased_input" value="<?= $image['price'] ?>"
                                placeholder="<?= $fs['Enter price'] ?>">
                            <button onclick="sellInGallery(this, <?= $image['id'] ?>)"
                                class="personal_account__purchased_button_set_price">Sell in Gallery
                            </button>
                        </div>
                    <?php } ?>
                    <?php if ($image['status'] == 'trading') { ?>
                        <div class="personal_account_purchased_image__price">
                            <span class="personal-account-price-text">Image in lot</span>
                        </div>
                    <?php } ?>
                    <?php if ($image['status'] == 'ready') { ?>
                        <div class="personal_account_buttons_action">
                            <button onclick="sellWallpapers(<?= $image['id'] ?>)"
                                class="personal_account__purchased_button_download">
                                Sell Wallpapers
                            </button>
                            <button onclick="deleteGalleryImage(<?= $image['id'] ?>)"
                                class="personal_account__purchased_button_delete_image"><?= 'Delete' ?>
                            </button>
                        </div>
                    <?php } ?>
                    <?php if ($image['status'] == 'gallery') { ?>
                        <div class="personal_account_buttons_promotion">
                            <button onclick="openPromotionModal(<?= $image['id']?>, 'pin_to_top')" class="personal_account__purchased_button_set_price">Pin to top</button>
                            <button onclick="openPromotionModal(<?= $image['id']?>, 'lift_up')" class="personal_account__purchased_button_set_price">Lift up</button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="gallery-images__modal-close-button-cover">
            <a class="close-gallery" onclick="hideUserImageModal()"><img class="img__del-gallery"
                    src='/inc/assets/img/closebuttong.svg'></a>
        </div>
    </div>
</section>