<?php
$image = getImages(null, null, 'ready', getLoginUserId(), $_POST['image_id'], null)[0];
?>
<section id="modalChosenImage" class="modal" style="display:none;">
    <div class="modal-content-custom">
        <a class="close-sets" onclick="document.querySelector('#modalChosenImage').remove()"><img class="img__del" src='/inc/assets/img/closemodal.svg'></a>
        <div style="display:flex; justify-content: center;">
            <img onclick="addToLot(this)" style="width: 200px;height: 400px;object-fit: contain;" src="inc/assets/img/<?= $image['name'] ?>">
        </div>
        <div style="display:flex; justify-content: center; margin-top: 20px;">
            <button style="width:188px;" onclick="addToLot(this)" data-image="<?= $image['id'] ?>" data-set="<?= $_POST['set_id'] ?>" class="personal_account__purchased_button_download">Submit</button>
        </div>
    </div>
</section>