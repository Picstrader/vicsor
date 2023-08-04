<?php
$sub_options = /*getSubscriptionOptions()*/[];
?>
<section id="modalError" class="modal" style="display:none;">
    <div class="modal-content-gallery-error">
        <div class="gallery-images__modal-close">
            <a class="close" onclick="hideModalErrorBig()"><img class="img__del"
                    src='/inc/assets/img/closemodalwhite.svg'></a>
        </div>
        <div class="gallery-images__modal-result-text">
            <span id="trade-modal-error-big__text" class="gallery-modal-result__text"></span>
        </div>
        <div class="gallery-images__modal-sub-container" style="display:none">
            <?php foreach ($sub_options as $option) { ?>
            <div class="sub-block">
                <div class="sub-block-inner" style="background-color:<?= $option['color'] ?>;">
                    <div class="sub-block-inner-title">
                        <div class="gallery-images__modal-sub-item-amount"><?= $option['amount'] . ' ' . $fs['Months'] ?></div>
                        <div class="gallery-images__modal-sub-item-price"><?= $option['price'] ?> <?= strtolower($fs['main_currency']) ?></div>
                        <?php if($option['amount']!=1){?>
                            <div class="gallery-images__modal-sub-item-price-mounth"><?= round($option['price']/$option['amount'], 2) ?> <?= strtolower($fs['main_currency']) ?>/<?= $fs['Months'] ?></div>
                            <div class="gallery-images__modal-sub-item-discount"><?= ECommerceLogic::getSubDiscount($sub_options[0], $option) ?>% <?= $fs['discount'] ?></div>
                        <?php } ?>
                    </div>
                    <div class="sub-block-inner-button">
                        <button class="button-subscribe" style="background-color:<?= $option['color'] ?>;" onclick="buySubcsription(<?= $option['id'] ?>, this)"><?= $fs['subscribe'] ?></button>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>