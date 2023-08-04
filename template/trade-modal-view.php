<?php
$fields = [];
if (isLogin()) {
    $fields['user_id'] = getLoginUserId();
    $wallets = getUserWallets($fields);
} else {
    $wallets = [];
}
if (count($wallets) != 0) {
    $wallets = $wallets[0];
}
?>
<section id="modalView" class="modal" style="display:none;">
    <div class="modal-content-custom">
        <a class="close" onclick="hideModalView()"><img class="img__del" src='/inc/assets/img/closemodal.svg'></a>
        <div id="modal-view-set-id" class="main_form__sect-title"></div>
        <div class="splide" aria-label="Splide Basic HTML Example">
            <div class="splide__track">
                <ul class="splide__list">
                    <!-- <li class="splide__slide"><img src="./inc/assets/img/slider1.png" /></li>
                    <li class="splide__slide"><img src="./inc/assets/img/slider9.png" /></li> -->
                </ul>
            </div>

            <div class="my-carousel-progress">
                <div class="my-carousel-progress-bar"></div>
            </div>
        </div>
        <div class="active-sets__filter">
            <div class="active-sets__filter-inner">
                <div class="main_form__sect">
                    <div class="main_form__sect-title"><?= $fs['Placement cost'] ?></div>
                    <div class="main-form__sect-input-field-trade">
                        <label for="trade_modal__cost_view" title="<?= $fs['Cost of one place in set'] ?>">
                            <input type="number" id="trade_modal__cost_view" class="trade_modal__sect-inp"
                                oninput="updateParams()" placeholder="<?= $fs['Enter amount'] ?>">
                        </label>
                    </div>
                    <div class="main_form__sect-title"><?= $fs['main_currency'] ?></div>
                </div>
                <div class="main_form__sect">
                    <div class="main_form__sect-title"><?= $fs['Total photos in set'] ?></div>
                    <div class="main-form__sect-input-field-trade">
                        <label for="trade_modal__photos_view" title="<?= $fs['Amount of images which must be in set'] ?>">
                            <input type="number" id="trade_modal__photos_view" class="trade_modal__sect-inp"
                                oninput="updateParams()" placeholder="<?= $fs['Enter quantity'] ?>">
                        </label>
                    </div>
                    <div class="main_form__sect-title"><?= $fs['Pics'] ?></div>
                </div>
                <div class="main_form__sect">
                    <div class="main_form__sect-title"><?= $fs['Total purchasable photos'] ?></div>
                    <div class="main-form__sect-input-field-trade">
                        <label for="trade_modal__purchasable_view" title="<?= $fs['Amount of images which can win'] ?>">
                            <input type="number" id="trade_modal__purchasable_view" class="trade_modal__sect-inp"
                                oninput="updateParams()" placeholder="<?= $fs['Enter quantity'] ?>">
                        </label>
                    </div>
                    <div class="main_form__sect-title"><?= $fs['Pics'] ?></div>
                </div>
                <div style="display:none;" class="main_form__sect_time">
                    <div class="main_form__sect-title"><?= $fs['Set duration'] ?></div>
                    <div class="main-form__sect-input-field-trade">
                        <label for="trade_modal__time_view" title="<?= $fs['Lifetime of the set'] ?>">
                            <input type="number" id="trade_modal__time_view" class="trade_modal__sect-inp" min="1" max="168"
                                oninput="updateParams(this)" placeholder="<?= $fs['Enter time'] ?>" required>
                        </label>
                    </div>
                </div>
            </div>
            <div>
                <div class="trade_modal__sect">
                    <div class="trade_modal__sect_profit">
                        <div class="trade_modal__sect-title"><?= $fs['Profit'] ?>:</div>
                        <div class="trade_modal__sect-input-field-trade" id="trade_modal__profit_view">
                        </div>
                    </div>
                    <div class="trade_modal__sect_usdt">
                        <div class="trade_modal__sect-title"><?= $fs['Profit'] ?> <?= $fs['main_currency'] ?>:</div>
                        <div class="trade_modal__sect-input-field-trade" id="trade_modal__profit_usdt_view">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>