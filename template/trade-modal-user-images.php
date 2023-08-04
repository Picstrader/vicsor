<?php
$page = $page ? $page : 1;
$images_amount = (int) getImagesAmount('ready', null, getLoginUserId())[0]['amount'];
$on_page = 3;
$pages = (intdiv($images_amount, $on_page)) + ($images_amount % $on_page !== 0 ? 1 : 0);
$images = getLoginUserId() ? getImages($page ? $page : 1, 3, 'ready', getLoginUserId(), null, null) : [];
if(isset($_POST['type']) && !count($images)) {
    if($_POST['type'] == 'next') {
        $page = (int) $page - 1;
    } else if($_POST['type'] == 'back') {
        $page = (int) $page + 1;
    }
}
?>
<section id="modalUserImages" class="modal" style="display:none;" data-p="<?= $images_amount ?>" data-pp="<?= $pages ?>">
    <div class="modal-content-custom">
        <a class="close-sets" onclick="document.querySelector('#modalUserImages').remove()"><img class="img__del" src='/inc/assets/img/closemodal.svg'></a>
        <div id="user-images-cover" data-page="<?= $page ? $page : 1 ?>" style="display:flex; justify-content: center; gap: 30px; margin-top: 30px;">
            <div class="trade_gallery__empty_section" style="cursor: pointer;" onclick="window.location.href = '/personal-account.php'">
                <div class="trade_gallery__empty_section-ellipse">
                    <img class="trade_gallery__empty_section-ellipse-add" src="./inc/assets/img/trade_slider-add.png">
                </div>
            </div>
            <?php foreach ($images as $image) { ?>
                <img class="ready-user-image" onclick="addToLot(this)" data-image="<?= $image['id'] ?>" data-set="<?= $_POST['set_id'] ?>" style="cursor: pointer;width: 100px;height: 200px;object-fit: contain;" src="inc/assets/img/<?= $image['name'] ?>">
            <?php } ?>
        </div>
        <div id="user-images-buttons" style="display: flex; justify-content: center; margin-top: 35px; gap: 60px;">
        <?php if((int) $page > 1) { ?>
            <button style="width: 100px;" class="personal_account__purchased_button_set_price" onclick="loadOtherUserImages('back')">Back</button>
        <?php } ?>
        <?php if((int) $page < $pages) { ?>
            <button style="width: 100px;" class="personal_account__purchased_button_set_price" onclick="loadOtherUserImages('next')">Next</button>
        <?php } ?>
        </div>
    </div>
</section>