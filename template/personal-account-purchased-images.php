<?php
if (!isset($page)) {
    $page = 1;
}
$fields = [];
$fields['user_id'] = getLoginUserId();
$fields['page'] = $page;
$images = getUserPurchasedImages($fields);
$images_amount = getUserPurchasedImagesAmount($fields);
$amount = (int) $images_amount[0]['amount'];
$on_page = 35;
$pages = (intdiv($amount, $on_page)) + ($amount % $on_page !== 0 ? 1 : 0);
if ($page > $pages) {
    //$page = 1;
}
?>

<section id="gallery-images__section" class="gallery-images__section-foto">
    <section class="personal-account-purchased-breadcrumbs">
        <div class="breadcrumbs">
            <div class="breadcrumbs-home" onClick="location.href='/'"></div>
            <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
            <div class="breadcrumbs-page">
                <?= $fs['Purchased images'] ?>
            </div>
        </div>
    </section>
    <?php if (count($images) <= 0) { ?>
        <div class="purchased-noimage">
            <p class="gallery-images__header">
                <?= $fs['no_purchase_image'] ?>
            </p><a href="trade.php"><button class="create-set__button">
                    <?= $fs['Go to trading'] ?>
                </button></a>
        </div>
    <?php } ?>

    <div class="gallery-images__images-cover">
        <?php foreach ($images as $image) {
            ?>
            <div class="gallery-images__image-data">
                <div class="gallery-images__image"
                    style="background-image: url(./inc/assets/img/<?= $image['name_original'] ?>);"
                    onclick="openPurchasedImage(this)" data-image='<?= json_encode($image) ?>'>
                    <div class="zoom-galery-block-first">
                        <div class="zoom-galery-block">
                            <img class="img-zoom-galery" src="/inc/assets/img/zoomgallery.png">
                        </div>
                    </div>
                </div>
                <div class="gallery-images__profit-block">
                    <div class="gallery-images__profit">
                        <span>
                            <?= $fs['Price'] ?>
                        </span>
                        <span class='gallery-images__price'>
                            <?= round((float) $image['price'], 4) . ' ' . $fs['main_currency'] ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>