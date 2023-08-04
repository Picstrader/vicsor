<?php
$image = getImages(null, null, null, getLoginUserId(), $_POST['image_id'])[0];
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/gallery.php";
?>
<section id="modalGallery" class="modal" style="display:none;">
    <div class="modal-content-gallery">  
        <div class="gallery-images__modal-img" >
            <img style="content: url(./inc/assets/img/<?= $image['name_original'] ?>);width: 100%;height: 100%;object-fit: contain;">
            <div class="gallery-bloc-info">
                <div class="share-button-block" id="shareButtonBlock">
                    <button class="gallery__Twitter_button" title="Twitter" onclick="shareTwitter()"></button>
                    <button class="gallery__Facebook_button" title="Facebook" onclick="shareFacebook()"></button>
                    <button class="gallery__Telegram_button" title="Telegram" onclick="shareTelegram()"></button>
                    <button class="gallery__Whatsapp_button" title="Whatsapp" onclick="shareWhatsApp()" data-action="share/whatsapp/share"></button>
                    <button class="gallery__copylinkphoto_button" title="<?= $fs['copy link photo'] ?>" onclick="copySharedLink()"></button>
                    <input type="hidden" id="shared_link" value="<?= $actual_link . '?shared_link=' . $purchased_image_id ?>">
                </div>
                <div class="gallery-image-all-button">
                    <?php if (isLogin()) { ?>
                        <?php if ($is_favorite) { ?>
                            <button onclick="removeFromFavorite(<?= $image['id'] ?>)" id="favorite_button" class="gallery__favorite_button_svg_favorite" title="<?= $fs['Remove favorite'] ?>"></button>
                        <?php } else { ?>
                            <button onclick="addToFavorite(<?= $image['id'] ?>)" id="favorite_button" class="gallery__favorite_button_svg_notfavorite" title="<?= $fs['To favorites'] ?>"></button>
                        <?php } ?>
                    <?php } else { ?>
                        <button onclick="window.location.href = '/login.php';" class="gallery__favorite_button_svg_notfavorite" title="<?= $fs['To favorites'] ?>"></button>
                    <?php } ?>
                        <button class="icon-modal-gallery" id="shareButton" onclick="shareButton()" title="<?= $fs['Share with friends'] ?>"></button>
                </div>
                <div class="gallery-author-modal-block">
                    <div class="gallery-price-block">
                        <span class="gallery-images__modal-text-price">
                            <?= $fs['Likes'] ?>: <b><?= $image['likes'] ?></b>
                        </span>
                        <span class="gallery-images__modal-text-price">
                            <?= $image_file_data['width'] . 'x' . $image_file_data['height'] ?>
                        </span>
                        <span class="gallery-images__modal-text-price">
                            <?= round($image_file_data['size'] / 1000, 2) . ' KB' ?>
                        </span>
                    </div>
                    <div class="gallery-chat-modal-block">
                        <!--<span class="gallery-author-modal"><?= $fs['Author'] ?>: <?= $owner_login ?></span>-->
                        <span class="gallery-author-modal"><?= $fs['Profit'] ?>: <?= $image['profit_usdt'] ?> USD </span>
                        <span class="gallery-author-modal"><?= $image['profit_percent'] ?>%</span>
                        <!--<button class="gallery__chat_button" title="<?= $fs['Write to the author'] ?>"></button>-->
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="gallery-images__modal-close-button-cover">
            <a class="close-gallery" onclick="hideGalleryModel()"><img class="img__del-gallery" src='/inc/assets/img/closebuttong.svg'></a>
        </div>
        <?php if ($price > 0) { ?>
        <?php } ?>
    </div>
</section>