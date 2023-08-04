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
<section id="myModal" class="modal" style="display:none;">
    <div class="modal-content-custom">
        <a class="close-sets" onclick="hideModal()"><img class="img__del" src='/inc/assets/img/closemodalwhite.svg'></a>
        <div class="trade_modal__section_foto">
            <div id="trade_modal__foto" class="trade_modal__section_foto_inner"></div>
        </div>
        <div class="active-sets__filter-sets">
            <div class="active-sets__filter-inner">
                <div class="main_form__sect main_form__sect-mobile">
                    <div class="main_form__sect-title-modal"><?= $fs['Placement cost'] ?></div>
                    <div class="main-form__sect-input-field-trade">
                            <input type="number" id="trade_modal__cost" class="modal-inp-trade placeholder-mobile-usdt"
                                oninput="updateParams(this)" placeholder="<?= $fs['Enter amount'] ?>" title="<?= $fs['Cost of one place in set'] ?>">
                    </div>
                    <div class="main_form__sect-title-modal main_form__sect-title-mobile"><?= $fs['main_currency'] ?></div>
                </div>
                <div class="main_form__sect main_form__sect-mobile">
                    <div class="main_form__sect-title-modal"><?= $fs['Total photos in set'] ?></div>
                    <div class="main-form__sect-input-field-trade">
                            <input type="number" id="trade_modal__photos" class="modal-inp-trade placeholder-mobile-pics"
                                oninput="updateParams(this)" onfocusout="checkValidPhotos(this)" placeholder="<?= $fs['Enter quantity'] ?>" title="<?= $fs['Amount of images which must be in set'] ?>">
                    </div>
                    <div class="main_form__sect-title-modal main_form__sect-title-mobile"><?= $fs['pics'] ?></div>
                </div>
                <div class="main_form__sect main_form__sect-mobile">
                    <div class="main_form__sect-title-modal"><?= $fs['Total purchasable photos'] ?></div>
                    <div class="main-form__sect-input-field-trade">
                            <input type="number" id="trade_modal__purchasable" class="modal-inp-trade placeholder-mobile-pics"
                                oninput="updateParams(this)" placeholder="<?= $fs['Enter quantity'] ?>" title="<?= $fs['Amount of images which can win'] ?>">
                    </div>
                    <div class="main_form__sect-title-modal main_form__sect-title-mobile"><?= $fs['pics'] ?></div>
                </div>
                <div style="display:none;" class="main_form__sect_time">
                    <div class="main_form__sect-title-modal"><?= $fs['Set duration'] ?></div>
                    <div class="main-form__sect-input-field-trade">
                            <input type="number" id="trade_modal__time" class="modal-inp-trade"
                                oninput="updateParams(this)" onfocusout="checkValidTime(this)" placeholder="<?= $fs['Enter time'] ?>" title="<?= $fs['Lifetime of the set'] ?>">
                    </div>
                    <div id="trade_modal__time_title" class="main_form__sect-title-modal"><?= $fs['Hours'] ?></div>
                </div>
            </div>
            <div>
                <div class="trade_modal__sect">
                    <div class="trade_modal__sect_profit">
                        <div class="trade_modal__sect-title"><?= $fs['Profit'] ?>:</div>
                        <div class="trade_modal__sect-input-field-trade" id="trade_modal__profit">
                        </div>
                    </div>
                    <div class="trade_modal__sect_usdt">
                        <div class="trade_modal__sect-title"><?= $fs['Profit'] ?>:</div>
                        <div class="trade_modal__sect-input-field-trade" id="trade_modal__profit_usdt">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="trade_modal__sect_wallet">
            <div>
                <div class="main_form__sect-title-modal"><?= $fs['Hashtags'] ?></div>
                <select data-placeholder=" " multiple class="chosen-select" name="test">
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="trade-button__block">
            <button onclick="hideModal()" class="button__close"><?= $fs['Close'] ?></button>
            <button onclick="confirmSet()" id="trade_modal__confirm" class="button__confirm"><?= $fs['Confirm'] ?></button>
        </div>
    </div>
</section>