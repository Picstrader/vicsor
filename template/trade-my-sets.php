<?php
$sets = getSets($filters['cost'], $filters['photos'], $filters['purchasable'], 1, 25, 'my', getLoginUserId());
?>
<section id="trade-sets__my-content" class="active-sets__trade">
    <?php if (isLogin()) { ?>
        <p class="active-sets__title-trade">
            <?= $fs['My sets'] ?>
        </p>
        <div class="trade-sets__table_wrapper">
            <table id="trade-sets__table_my_sets" class="table-mysets">
                <thead class="fl-table-sets-head">
                    <tr class="table-mysets-tr table-mobile">
                        <th class="fl-table__first"><?= $fs['Placement cost'] ?></th>
                        <th><?= $fs['Total photos in set'] ?></th>
                        <th><?= $fs['Total purchasable photos'] ?></th>
                        <th><?= $fs['Profit'] ?> %</th>
                        <th><?= $fs['Profit'] ?> <?= $fs['main_currency'] ?></th>
                        <th><?= $fs['Set fullness'] ?></th>
                        <th class="fl-table__last"><?= $fs['Time until the end of the set'] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sets as $set) {
                        $set['profit_usdt'] = ECommerceLogic::getProfitUSDT($set);
                        $set['profit_percent'] = ECommerceLogic::getProfitPercent($set);
                        ?>
                        <tr class="trade-sets__table_data table-mobile">
                            <td>
                                <?= $set['cost'] ?>
                            </td>
                            <td>
                                <?= $set['total_photos'] ?>
                            </td>
                            <td>
                                <?= $set['pur_photos'] ?>
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
                            <td class="<?= usersInSet($set) < $set['total_photos'] ? '' : 'trade-sets__in_progress' ?>"><?= usersInSet($set) < $set['total_photos'] ? ECommerceLogic::getDaysHours($set['time'], $fs) : $fs['In progress'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</section>