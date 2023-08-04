<?php
$sub = getSubParam()[0]['amount'];
?>
<section id="modalSubscription" class="modal" style="display:none;">
    <div class="modal-content-gallery-sub">
        <div class="gallery-images__modal-close">
            <a class="close" onclick="hideModalSubscription()"><img src='/inc/assets/img/closemodalwhite.svg' class="img__del-subscribe"></a>
        </div>
        <div class="gallery-images__modal-result-text">
            <span id="trade-modal-error-big__text" class="personal-account-modal-result__text">
                <?= /*$fs['Сhoose a subscription']*/'You subscribe for 30 days for ' . $sub . ' USD' ?>
            </span>
        </div>
        <div style="display:flex; justify-content:center">
            <button class="inbalance_button" onclick="buySubcsription(this)">Confirm</button>
        </div>
    </div>
</section>