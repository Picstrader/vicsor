<?php
$page_demo = ($update_all || !$page_demo) ? 1 : $page_demo;
$demo_sets_amount = (int) getSetsAmount($filters['cost'], $filters['photos'], $filters['purchasable'], 'demo', getLoginUserId())[0]['amount'];
$demo_sets = getSets($filters['cost'], $filters['photos'], $filters['purchasable'], $page_demo, 25, 'demo', getLoginUserId());
if ((count($demo_sets) === 0) && $page_demo == 1) {
    $new_set = [
        'users_photos' => '',
        'cost' => (float) $filters['cost'],
        'pur_photos' => (int) $filters['purchasable'],
        'total_photos' => (int) $filters['photos'],
        'time' => 0
    ];
    array_push($demo_sets, $new_set);
}
$all_demo_sets_on_platform = (int) getSetsAmount(null, null, null, 'demo', 0)[0]['amount'];
?>
<div id="trade-demo-sets__content-cover">
    <div style="display:flex; justify-content: space-between;">
        <p class="active-sets__title-trade-nopadding">
            <?= /*$fs['Active sets']*/'Demo sets' ?>
        </p>
        <p class="active-sets__title-trade-nopadding">
            <?= /*$fs['The total number of sets on the platform']*/'The total number of demo sets on the platform' . ": $all_demo_sets_on_platform" ?>
        </p>
    </div>
    <section id="trade-demo-sets__content" class="active-sets__trade">
        <div style="background-color: #ffffff00;">
            <div class="trade-sets__table_wrapper-set-thead table-head-mobile">
                <table id="trade-demo-sets__table_sets" class="table-mysets">
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
            <div class="trade-sets__table_wrapper-set trade-set-block-scroll" onscroll="checkDemoScroll(this)">
                <table id="trade-demo-sets__table_sets" class="table-mysets">
                    <tbody id="demo-main_tbody">
                        <?php foreach ($demo_sets as $set) {
                            $set['profit_usdt'] = ECommerceLogic::getProfitUSDT($set);
                            $set['profit_percent'] = ECommerceLogic::getProfitPercent($set);
                            ?>
                            <tr title="<?= $fs['TableTitle'] ?>" onclick='checkSet(<?= json_encode($set) ?>, this)'
                                style="cursor: pointer;" class="trade-sets__table_data table-mobile">
                                <td>
                                    <?= $set['cost'] ?>
                                </td>
                                <td>
                                    <?= $set['total_photos'] ?>
                                </td>
                                <td>
                                    <?= $set['pur_photos'] ?>
                                </td>
                                <td class="table__usdt">+
                                    <?= (int) $set['cost'] + (float) $set['profit_usdt'] ?>
                                </td>
                                <td class="table__percent">+
                                    <?= $set['profit_percent'] ?>%
                                </td>
                                <td class="table__usdt">+
                                    <?= $set['profit_usdt'] ?>
                                </td>
                                <td>
                                    <?= usersInSet($set) ?>/
                                    <?= $set['total_photos'] ?>
                                </td>
                                <td class="table__time">
                                    <?= ECommerceLogic::getDaysHours($set['time'], $fs) ?>
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