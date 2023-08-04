<section id="myModal2" class="modal" style="display:none;">
    <div class="modal-content-align">
        <div class="modal-content__congratulation" id="section__foto-src-desc">
        <a class="close" onclick="hideModalFinish()"><img class="img__del" src='/inc/assets/img/closemodalwhite.svg'></a>
            <div class="modal-content__congratulation-plash">
                <div class="congratulation__second"><?= $fs['Your photo has been published!'] ?></div>
                <div class="section__cong">
                    <div class="section__cong-block">
                        <div>
                            <table id="trade-sets__table_my_sets" class="congratulation__table">
                                <tbody>
                                    <tr>
                                    <td class="congratulation__table_info"><?= $fs['Set info:'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="congratulation__table_name"><?= $fs['Placement cost'] ?>:</td>
                                        <td id="trade_modal_finish__cost" class="congratulation__table_value">100</td>
                                    </tr>
                                    <tr>
                                        <td class="congratulation__table_name"><?= $fs['Total photos in set'] ?>:</td>
                                        <td id="trade_modal_finish__photos" class="congratulation__table_value">100</td>
                                    </tr>
                                    <tr>
                                        <td class="congratulation__table_name"><?= $fs['Total purchasable photos'] ?>:</td>
                                        <td id="trade_modal_finish__purchasable" class="congratulation__table_value">100</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="section__profit">
                            <table id="trade-sets__table_my_sets" class="congratulation__table">
                                <tbody>
                                    <tr>
                                        <td class="congratulation__table_name mobile-finish-trade"><?= $fs['Profit'] ?>:</td>
                                        <td id="trade_modal_finish__profit" class="congratulation__table_value mobile-finish-trade">100</td>
                                    </tr>
                                    <tr>
                                        <td class="congratulation__table_name mobile-finish-trade"><?= $fs['Profit'] ?> <?= $fs['main_currency'] ?>:</td>
                                        <td id="trade_modal_finish__profit_usdt" class="congratulation__table_value mobile-finish-trade">100</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>