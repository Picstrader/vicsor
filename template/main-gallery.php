<?php
$hashtag = $_POST['search'] ? $_POST['search'] : null;
$images_amount = (int) getImagesAmount('gallery', $hashtag)[0]['amount'];
$images = getImages($page ? $page : 1, GALLERY_PER_PAGE, 'gallery', null, null, '', true, $hashtag);
?>
<?php if(!count($images)) { ?>
    <section class="gallery-noimage">
        <div class="gallery-images__header">
            <?= $hashtag ? 'No images with this hashtag were found' : 'The gallery is currently empty' ?>
        </div>
    </section>
<?php } else { ?>
<section id="gallery-images__section" class="gallery-images__section-foto" data-page="<?= $page ? $page : 1 ?>">
    <div class="block-image">
            <div id="my-images-cover" class="gallery-images__images-cover" data-page="<?= $page ? $page : 1 ?>">
                <?php foreach ($images as $image) { ?>
                    <div class="gallery-images__image-data">
                        <div class="gallery-images__image"
                            style="background-image: url(./inc/assets/img/<?= $image['name'] ?>);"
                            onclick="openBuyImage(this)" data-image="<?= $image['id'] ?>" data-watermark="<?= $image['name'] ?>">
                            <div style="position: relative;" class="zoom-galery-block-first">
                               <div class="zoom-galery-block">
                                    <!-- <img style="cursor: pointer;" class="img-zoom-galery" src="/inc/assets/img/zoom.svg"> -->
                                    <button style="cursor: pointer; position:absolute; top: 221px; left: 65px; width: 80px; display:none;"  class="img-zoom-galery personal_account__purchased_button_set_price">Buy</button>
                                </div>
                            </div>
                        </div>

                        <div class="gallery-images__profit-block">
                            <div class="gallery-images__profit">
                                <span>
                                    <?= 'Price' ?>
                                </span>
                                <span class='gallery-images__price'>
                                    <?= $image['price'] ?>
                                </span>
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>
</section>
<?php } ?>