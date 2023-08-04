<?php
if((int) $_SESSION['trade_image']) {
    $trade_image = (int) $_SESSION['trade_image'];
    $_SESSION['trade_image'] = null;
}
$page = ($update_all || !$page) ? 1 : $page;
$sets_amount = (int) getSetsAmount(null, null, null, 'all', 0)[0]['amount'];
$sets = getSets(null, null, null, $page, 25, 'all', 0);
$total_sets_amount = (int) getSetsAmount(null, null, null, 'active', 0)[0]['amount'];
?>
<div id="trade-sets__content-cover">
    <div style="display:flex; justify-content: space-between;">
        <p class="active-sets__title-trade-nopadding">
            <?= $fs['Active sets'] ?>
        </p>
        <p class="active-sets__title-trade-nopadding">
            <?= $fs['The total number of sets on the platform'] . ": $total_sets_amount" ?>
        </p>
    </div>
    <section id="trade-sets__content" class="active-sets__trade">

        <div style="background-color: #ffffff00;">
            <div class="trade-sets__table_wrapper-set-thead table-head-mobile">
                <table id="trade-sets__table_sets" class="table-mysets">
                    <thead class="fl-table-sets-head">
                        <tr class="table-mysets-tr table-mobile">
                            <th class="fl-table__first"><?= $fs['Placement cost'] ?></th>
                            <th><?= $fs['Total photos in set'] ?></th>
                            <th><?= $fs['Total purchasable photos'] ?></th>
                            <th><?= $fs['Income'] ?> <?= $fs['main_currency'] ?></th>
                            <th><?= $fs['Profit'] ?> %</th>
                            <th><?= $fs['Profit'] ?> <?= $fs['main_currency'] ?></th>
                            <th><?= $fs['Set fullness'] ?></th>
                            <th class="fl-table__last"><?= $fs['Time until the end of the set'] ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="trade-sets__table_wrapper-set trade-set-block-scroll" onscroll="checkScroll(this)">
                <table id="trade-sets__table_sets" class="table-mysets">
                    <tbody id="main_tbody">
                        <?php foreach ($sets as $set) {
                            $set['profit_usdt'] = ECommerceLogic::getProfitUSDT($set);
                            $set['profit_percent'] = ECommerceLogic::getProfitPercent($set);
                            $images = getImages(null, null, null, null, null, getSetImagesIdString($set));
                            ?>
                            <tr title="<?= $fs['TableTitle'] ?>" class="trade-sets__table_data table-mobile">
                                <td>
                                    <?= $set['cost'] ?>
                                </td>
                                <td>
                                    <?= $set['total_photos'] ?>
                                </td>
                                <td>
                                    <?= $set['pur_photos'] ?>
                                </td>
                                <td class="table__usdt">+<?= (int) $set['cost'] + (float) $set['profit_usdt'] ?>
                                </td>
                                <td class="table__percent">+<?= $set['profit_percent'] ?>%
                                </td>
                                <td class="table__usdt">+<?= $set['profit_usdt'] ?>
                                </td>
                                <td>
                                    <?= usersInSet($set) ?>/<?= $set['total_photos'] ?>
                                </td>
                                <td class="table__time">
                                    <?= ECommerceLogic::getDaysHours($set['time'], $fs) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8">
                                    <div class="trade-sets-images-cover">
                                        <?php for ($i = 0; $i < $set['total_photos'] - usersInSet($set); $i++) { ?>
                                            <div onclick='<?= $trade_image ? "showModalChosenImage(this, $trade_image)" : "showModalUserImages(this)" ?>' class="trade_gallery__empty_section"
                                                data-set="<?= $set['id'] ?>" style="<?= $trade_image ? 'border: 2px solid red' : '' ?>">
                                                <div class="trade_gallery__empty_section-ellipse">
                                                    <img class="trade_gallery__empty_section-ellipse-add"
                                                        src="./inc/assets/img/trade_slider-add.png">
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php foreach ($images as $image) { ?>
                                            <div style="display: flex; flex-direction: column;">
                                                <img style="width: 100px;height: 200px;object-fit: contain;"
                                                    src="inc/assets/img/<?= $image['name'] ?>">
                                                <?php if (isFullSet($set)) { ?>
                                                    <div style="display:flex; gap: 10px;margin-top: 5px;">
                                                        <img onclick="likeImage(this)" data-set="<?= $set['id'] ?>" data-image="<?= $image['id'] ?>" style="cursor: pointer; width: 30px; height: 30px;" src="inc/assets/img/like.svg"> 
                                                        <span style="line-height: 30px;" class="image-likes"><?= getImagesLikes($set, $image['id']) ?></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="more-block">
            <img class="more-img">
        </div>

    </section>
    <div class="more-block-mobile">
        <img class="more-img">
    </div>
</div>