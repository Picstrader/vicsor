
<?php 
$show = false;
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if(isset($_GET['success_confirm'])) {
        $show = true;
    } else {
        $show = false;
    }
}
?>
<section id="modalConfirmEmail" style="display:<?= $show ? 'block' : 'none' ?>" class="modal" style="display:none;">
    <div class="modal-confirm-content-align">
        <div class="modal-confirm-content__congratulation">
            <div class="modal-confirm-block">
                <div><a class="modal-confirm-close" onclick="hidePersonalAccountModalConfirm()"><img id="modal-confirm-close" class="img__del-modal-rate" src='/inc/assets/img/closemodal.svg'></a></div>
                <div class="modal-confirm-block-item">
                    <img src='/inc/assets/img/confirm-email.png'>
                </div>
                <div class="modal-confirm-block-item-header">
                    <?= $fs['Congratulations']?>!
                </div>
                <div class="modal-confirm-block-item-text">
                    <?= $fs['Your email has been confirmed'] ?>!
                </div>
                <div class="modal-confirm-block-item-button">
                    <button onclick="hidePersonalAccountModalConfirmRedirect()" class="modal-confirm-button"><?= strtoupper($fs['Get Started']) ?></button>
                </div>
            </div>
        </div>
    </div>
</section>