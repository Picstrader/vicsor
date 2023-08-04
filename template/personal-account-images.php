<?php
$images = getImages($page ? $page : 1, PROFILE_IMAGE_PER_PAGE, null, getLoginUserId());
?>
<?php if(!count($images)) { ?>
    <section class="gallery-noimage">
        <div class="gallery-images__header">
            You don't have images, upload them
        </div>
    </section>
<?php } else { ?>
<section id="gallery-images__section" class="gallery-images__section-foto">
    <div id="my-images-cover" class="gallery-images__images-cover" data-page="<?= $page ? $page : 1 ?>">
        <?php foreach ($images as $image) {
            ?>
            <div class="gallery-images__image-data">
                <div class="gallery-images__image"
                    style="background-image: url(./inc/assets/img/<?= $image['name_original'] ?>);"
                    onclick="openUserImage(this)" data-image='<?= json_encode($image) ?>'>
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
                            <?= round((float) $image['price'], 4) . ' ' . $fs['main_currency'] ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<?php } ?>