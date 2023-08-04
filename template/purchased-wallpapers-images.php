<?php
//$images_amount = (int) getImagesAmount('gallery')[0]['amount'];
$images = getPurchasedImages(getLoginUserId());
?>
<section style="margin-top: 30px;">
    <section class="personal-account-purchased-breadcrumbs">
        <div class="breadcrumbs">
            <div class="breadcrumbs-home" onClick="location.href='/'"></div>
            <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
            <div class="breadcrumbs-page">Purchased Wallpapers</div>
        </div>
    </section>
</section>
<?php if(!count($images)) { ?>
    <section class="gallery-noimage">
        <div class="gallery-images__header">
            You don't have purchased images
        </div>
    </section>
<?php } else { ?>
<section id="gallery-images__section" class="gallery-images__section-foto">
    <div id="my-images-cover" class="gallery-images__images-cover" data-page="<?= $page ? $page : 1 ?>">
        <?php foreach ($images as $image) {
            foreach (explode(';', $image['owners']) as $owner) {
                if (getLoginUserId() == (int) explode(':', $owner)[0]) {
                    $user_price = (int) explode(':', $owner)[1];
                    break;
                }
            }
            ?>
            <div class="gallery-images__image-data">
                <div class="gallery-images__image"
                    style="background-image: url(./inc/assets/img/<?= $image['name_original'] ?>);"
                    onclick="openUserPurchasedImage(this)" data-image='<?= json_encode($image) ?>'>
                    <div class="zoom-galery-block-first">
                        <div class="zoom-galery-block">
                            <img style="display: none;" class="img-zoom-galery" src="/inc/assets/img/zoomgallery.png">
                        </div>
                    </div>
                </div>
                <div class="gallery-images__profit-block">
                    <div class="gallery-images__profit">
                        <span>
                            <?= $fs['Price'] ?>
                        </span>
                        <span class='gallery-images__price'>
                            <?= round((float) $user_price, 2) . ' ' . $fs['main_currency'] ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<?php } ?>