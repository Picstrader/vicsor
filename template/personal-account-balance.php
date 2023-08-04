<?php
include_once 'config.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/Validation.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/PersonalAccountFunctions.php';
if (!isset($page)) {
    $page = 1;
}
if (!isset($filters)) {
    $filters = [];
}
if (!isset($minutes)) {
    $minutes = 0;
}
$filters['user_id'] = getLoginUserId();
$filters['type'] = 'balance';
$filters['page'] = $page;
if (isset($filters['period'])) {
    $logs = getUserLogsByPeriodFilters($filters);
    $amount = getUserLogsAmountByPeriodFilters($filters);
} else if (isset($filters['day'])) {
    $logs = getUserLogsByDayFilters($filters);
    $amount = getUserLogsAmountByDayFilters($filters);
} else {
    $logs = getUserLogsByFilters($filters);
    $amount = getUserLogsAmountByFilters($filters);
}
$amount = (int) $amount[0]['amount'];
$on_page = 20;
$pages = (intdiv($amount, $on_page)) + ($amount % $on_page !== 0 ? 1 : 0);
if ($page > $pages) {
    //$page = 1;
}
$sub_data = ECommerceLogic::getSubscriptionLeft();
?>
<section class="trade__heading_section">
    <div class="breadcrumbs">
        <div class="breadcrumbs-home" onClick="location.href='/'"></div>
        <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
        <div class="breadcrumbs-page"><?= $fs['Cash flow statement'] ?></div>
    </div>
</section>

<section class="balance-account-block">
    <div id="personal_account_change_password__container">
        <div class="myactivity-block-main">
            <div class="myactivity-block-inner">
            <div class="personal_account_balance-add-block">
                <div class="personal_account_balance-sub-block">
                    <div class="personal_account_subscription-add-mobile">
                        <button style="display:none;" class="inbalance_button-add-sub" onclick="showModalSubscription()"><?= $fs['Subscription title'] ?></button>
                    </div>
                    <div class="personal_account_balance-info">
                        <?php if(isset($_GET['topup'])) { ?>
                            <div class="balance-amount"><span class="balance-amount-title" style="color:red;"><?= $fs['The operation was not performed'] ?></span></div>
                        <?php } ?>
                        <div class="balance-amount"><span class="balance-amount-title"><?= $fs['balance'] ?>:</span><span class="balance-amount-count"> <span id='balance-balance-span'><?= $_SESSION['user_data']['balance'] ?></span> <?= $fs['main_currency'] ?></span></div>
                        <div style="display:none;" class="subscribe-amount"><span class="subscribe-amount-title"><?= $fs['Subscribe expiration'] ?>:</span><span class="subscribe-amount-count"> <span id='sub-balance-span'><?= $sub_data['amount'] ?></span> <span id='sub-balance-span-type'> <?= $fs[$sub_data['type']] ?></span></span></div>
                    </div>
                </div>
                <div class="block-button-balance">
                    <div></div>
                    <div class="personal_account_balance-add">
                        <button class="inbalance_button-add" onclick="showPersonalAccountModalTopup()"><?= $fs['Top-up'] ?></button>
                        <button class="inbalance_button-add" onclick="showPersonalAccountModalWithdrawAction()"><?= $fs['Withdraw'] ?></button>
                    </div>
                    <div class="personal_account_subscription-add">
                        <button style="display:none;" class="inbalance_button-add-sub" onclick="showModalSubscription()"><?= $fs['Subscription title'] ?></button>
                    </div>
                </div>
            </div>
            <div class="balance-block-scroll" onscroll="scrollUpdateBalance(this)">
                <table class="fl-table1 myactivity">
                    <thead class="myactivity-thead">
                        <tr class="myactivity-tr-thead">
                            <th class="balance-th-description">
                                <?= $fs['Description'] ?><p class="date-time-mobile"><?= $fs['Date and time'] ?>
                            </th>
                            <th class="balance-th-datetime">
                                <?= $fs['Date and time'] ?>
                            </th>
                            <th class="balance-th-amountusdt">
                                <?= $fs['Operation amount USDT'] ?> <?= $fs['main_currency'] ?>
                            </th>
                            <th class="balance-th-balance">
                                <?= $fs['Balance after this operation'] . " " .  $fs['main_currency'] ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="main_tbody1">
                        <?php foreach ($logs as $log) { ?>
                            <tr class="myactivity-tr-tbody">
                                <td class="myactivity-td-tbody">
                                    <p class="date-time-mobile-table"><?= date("d/m/Y H:i:s", strtotime($log['cur_time'])) ?></p>
                                    <?= ECommerceLogic::prepareBalanceEmailLog($log) ?>
                                </td>
                                <td class="myactivity-td-tbody">
                                    <?= date("d/m/Y H:i:s", strtotime($log['cur_time'])) ?>
                                </td>
                                <?php $dif = (float) $log['balance_new'] - (float) $log['balance_old']; ?>
                                <td class="myactivity-td-tbody <?= $dif > 0 ? 'myactivity-positive-number' : '' ?>">
                                    <?= $dif > 0 ? '+' . number_format((float)$dif, 2, ',', '') : number_format((float)$dif, 2, ',', '') ?>
                                </td>
                                <td class="myactivity-td-tbody">
                                    <?= number_format((float)$log['balance_new'], 2, ',', '') ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="more-block">
                <img class="more-img">
            </div>
        </div>
        </div>
    </div>
</section>
<!-- <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js/dist/web3.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script> -->