<?php
session_start();
include_once 'config.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/Validation.php';
if (!isset($page)) {
    $page = 1;
}

$fields = [];
$fields['user_id'] = getLoginUserId();
$fields['page'] = $page;
$logs = getUserLogs($fields);
$amount = getUserLogsAmount($fields);
$amount = (int) $amount[0]['amount'];
$on_page = 20;
$pages = (intdiv($amount, $on_page)) + ($amount % $on_page !== 0 ? 1 : 0);
if ($page > $pages) {
    //$page = 1;
}
?>
<section class="trade__heading_section">
    <ul class="breadcrumbs">
        <div class="breadcrumbs-home" onClick="location.href='/'"></div>
        <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
        <div class="breadcrumbs-page">
            <?= $fs['My activity'] ?>
        </div>
    </ul>
</section>
<section class="myactivity-block">
    <div class="myactivity-block-main">
        <div class="myactivity-block-inner">
            <div class="myactivity-block-scroll" onscroll="scrollUpdateMyActivity(this)">
                <table class="fl-table1 myactivity">
                    <thead class="myactivity-thead">
                        <tr class="myactivity-tr-thead">
                            <th class="myactivity-th-description">
                                <?= $fs['Description'] ?>
                                <p class="date-time-mobile">
                                    <?= $fs['Date and time'] ?>
                                </p>
                            </th>
                            <th class="myactivity-th-datetime">
                                <?= $fs['Date and time'] ?>
                            </th>
                            <th class="myactivity-th-amountusdt">
                                <?= $fs['Operation amount USDT'] ?> <?= $fs['main_currency'] ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="main_tbody1">
                        <?php foreach ($logs as $log) { ?>
                            <?php if ($log['type'] === 'balance') { ?>
                                <tr class="myactivity-tr-tbody">
                                    <td class="myactivity-td-tbody">
                                        <p class="date-time-mobile-table">
                                            <?= date("d/m/Y H:i:s", strtotime($log['cur_time'])) ?>
                                        </p>
                                        <?= ECommerceLogic::prepareBalanceEmailLog($log) ?>
                                    </td>
                                    <td class="myactivity-td-tbody">
                                        <?= date("d/m/Y H:i:s", strtotime($log['cur_time'])) ?>
                                    </td>
                                    <?php $dif = (float) $log['balance_new'] - (float) $log['balance_old']; ?>
                                    <td class="myactivity-td-tbody <?= $dif > 0 ? 'myactivity-positive-number' : '' ?>">
                                    <?= $dif > 0 ? '+' . number_format((float)$dif, 2, ',', '') : number_format((float)$dif, 2, ',', '') ?>
                                </td>
                            </tr>
                    <?php } else if ($log['type'] === 'set') { ?>
                                    <tr class=" myactivity-tr-tbody">
                                        <td class="myactivity-td-tbody">
                                            <p class="date-time-mobile-table">
                                            <?= date("d/m/Y H:i:s", strtotime($log['cur_time'])) ?>
                                            </p>
                                            <?= ECommerceLogic::prepareSetEmailLog($log) ?>
                                        </td>
                                        <td class="myactivity-td-tbody">
                                        <?= date("d/m/Y H:i:s", strtotime($log['cur_time'])) ?>
                                        </td>
                                        <td class="myactivity-td-tbody">
                                            <div class="pers_acc_act__tb-body-row-data-line"></div>
                                        </td>
                                    </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="more-block">
                <img class="more-img">
            </div>
        </div>
    </div>
</section>