<?php
$style = '';
if(isset($wins['bg_name'])) {
    $style = 'background-image: url(/inc/assets/img/'. $wins['bg_name'] .');';
} else {
    $style = ''; 
}
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/gallery.php";
?>
<section id="myModalWinnerPop" class="modal-winner">
    <div class="congratulation-block" style="<?= $style ?>">
        <a class="close" onclick="hideModalWinnerPopap()"><img class="img__del" src='/inc/assets/img/closemodalwhite.svg'></a>
        <div class="modal-content__congratulation-plash">
            <div class="congratulation__first"><?= (int)$wins['win'] ? $fs['Your photo has been sold'] : 'Your image has not been sold' ?></div>
            <div class="congratulation__second"><?= (int)$wins['win'] ? $fs['Your profit amounted to dollar'] : '' ?><span id="winner-popap" class="winner-popap-profit"> <?= (int)$wins['win'] ? $wins['profit'] : '' ?> </span><?= (int)$wins['win'] ? $fs['main_currency'] : '' ?></div>
            <div class="congratulation__third"><?= $fs['Share with friends'] ?>
                <div class="share-button-block-trade" id="shareButtonBlock">
                    <button class="gallery__Twitter_button" title="Twitter" onclick="shareTwitter()"></button>
                    <button class="gallery__Facebook_button" title="Facebook" onclick="shareFacebook()"></button>
                    <button class="gallery__Telegram_button" title="Telegram" onclick="shareTelegram()"></button>
                    <button class="gallery__Whatsapp_button" title="Whatsapp" onclick="shareWhatsApp()" data-action="share/whatsapp/share"></button>
                    <button class="gallery__copylinkphoto_button" title="<?= $fs['copy link photo'] ?>" onclick="copySharedLink()"></button>
                    <input type="hidden" id="shared_link" value="<?= $actual_link . '?shared_link=' . $wins['bg'] ?>">
                </div>
            </div>
        </div>
    </div>
</section>