<?php
if (!isset($filters)) {
    $filters = [];
    $filters['cost'] = '';
    $filters['photos'] = '';
    $filters['purchasable'] = '';
}
$is_new_set = false;
$is_all_params = false;
$is_user_in_set = false;
$existing_set_data = getSet($filters);
$existing_set = [];
foreach ($existing_set_data as $item_set) {
    if (isFullSet($item_set)) {
        continue;
    } else {
        array_push($existing_set, $item_set);
        break;
    }
}
$existing_set_data = $existing_set;
if (count($existing_set_data) > 0) {
    $existing_set = $existing_set_data[0];
}
if (isLogin()) {
    if (count($existing_set_data) > 0) {
        //$existing_set = $existing_set[0];
        $players = $existing_set['users_photos'] === '' ? [] : explode(';', $existing_set['users_photos']);
        foreach ($players as $player) {
            $player_data = explode(':', $player);
            if ((int) $player_data[0] == (int) getLoginUserId()) {
                $is_user_in_set = true;
                break; // if user in set
            }
        }
    }
}
if (count($existing_set_data) <= 0) {
    $new_set = [
        'users_photos' => '',
        'cost' => (float) $filters['cost'],
        'pur_photos' => (int) $filters['purchasable'],
        'total_photos' => (int) $filters['photos'],
        'time' => 0
    ];
    $existing_set = $new_set;
    $is_new_set = true;
}
$existing_set['profit_usdt'] = ECommerceLogic::getProfitUSDT($existing_set);
$existing_set['profit_percent'] = ECommerceLogic::getProfitPercent($existing_set);
if (
    ($filters['cost'] !== '') && ($filters['purchasable'] !== '') && ($filters['photos'] !== '') &&
    ((int) $filters['cost'] > 0) && ((int) $filters['purchasable'] > 0) && ((int) $filters['photos'] > 0)
) {
    $is_all_params = true;
} else {
    $is_all_params = false;
}
?>
<section class="active-sets__trade">
<?php if (isLogin()) {echo "<div class='trading-balance'><p id='header-balance' class='balance'>" . $fs['balance'] . ": <span id='header-balance-span'>" . $_SESSION['user_data']['balance'] . "</span> ". $fs['main_currency'] ."</p></div>";} ?>
    <div class="active-sets__filter">
        <div class="profit-block-filter">
            <div class="profit-block-count">
            <div><span class="table__usdt-title"><?= $fs['Income'] ?>: </span><span id="profit-block-count-span-income" class="table__usdt">0 <?= $fs['main_currency'] ?></span></div>
                <div><span class="table__usdt-title"><?= $fs['Profit'] ?>: </span><span id="profit-block-count-span-perc" class="table__usdt">0 %</span></div>
                <div><span class="table__usdt-title"><?= $fs['Profit'] ?>: </span><span id="profit-block-count-span-usdt" class="table__usdt">0 <?= $fs['main_currency'] ?></span></div>
            </div>
        </div>
        <div class="active-sets__filter-inner">
            <div class="main_form__sect">
                <div class="main_form__sect-title">
                    <?= $fs['Placement cost'] ?>
                </div>
                <div class="main-form__sect-input-field-trade">
                        <input type="number" id="trade_filter__cost" class="main_form__sect-inp placeholder-mobile-usdt"
                            oninput="updateSetsList();updateSetsList(1, false, true, true);updateCreateSetButton();"
                            placeholder="<?= $fs['Enter amount'] ?>" title="<?= $fs['Cost of one place in set'] ?>">
                </div>
                <div class="main_form__sect-title main_form__sect-title-mobile"><?= $fs['main_currency'] ?></div>
                <div class="trade_description"><?= $fs['description_a'] ?></div>
            </div>
            <div class="main_form__sect">
                <div class="main_form__sect-title">
                <?= $fs['Total photos in set'] ?>
                </div>
                <div class="main-form__sect-input-field-trade">
                        <input type="number" id="trade_filter__photos" class="main_form__sect-inp placeholder-mobile-pics"
                            oninput="updateSetsList();updateSetsList(1, false, true, true);updateCreateSetButton();" onfocusout="checkValidPhotos(this)"
                            placeholder="<?= $fs['Enter quantity'] ?>" title="<?= $fs['Amount of images which must be in set'] ?>">
                </div>
                <div class="main_form__sect-title main_form__sect-title-mobile">
                    <?= $fs['pics'] ?>
                </div>
                <div class="trade_description"><?= $fs['description_b'] ?></div>
            </div>
            <div class="main_form__sect">
                <div class="main_form__sect-title">
                <?= $fs['Total purchasable photos'] ?>
                </div>
                <div class="main-form__sect-input-field-trade">
                        <input type="number" id="trade_filter__purchasable" class="main_form__sect-inp placeholder-mobile-pics"
                            oninput="updateSetsList();updateSetsList(1, false, true, true);updateCreateSetButton();"
                            placeholder="<?= $fs['Enter quantity'] ?>" title="<?= $fs['Amount of images which can win'] ?>">
                </div>
                <div class="main_form__sect-title main_form__sect-title-mobile">
                    <?= $fs['pics'] ?>
                </div>
                <div class="trade_description"><?= $fs['description_c'] ?></div>
            </div>
        </div>
        <div class="input-create-block">
            <div>
                <div class="hesh__seacrh-block">
                </div>
            </div>
        </div>
        <div class="profit-block-filter-mobile">
            <div class="profit-block-count">
                <div class="mobile-profit-block"><span class="table__usdt-title"><?= $fs['Income'] ?>: </span><span id="profit-block-count-span-income-mobile" class="table__usdt">0 <?= $fs['main_currency'] ?></span></div>
                <div class="mobile-profit-block"><span class="table__usdt-title"><?= $fs['Profit'] ?>: </span><span id="profit-block-count-span-perc-mobile" class="table__usdt">0%</span></div>
                <div class="mobile-profit-block"><span class="table__usdt-title"><?= $fs['Profit'] ?>: </span><span id="profit-block-count-span-usdt-mobile" class="table__usdt">0 <?= $fs['main_currency'] ?></span></div>
            </div>
        </div>
        <div class="create-button-block">
            <?php if (isLogin()) { ?>
                <a id="create-set__button" href="javascript:void(0)" class="create-set__button"
                    onclick='showSet(<?= $is_user_in_set ? null : json_encode($existing_set) ?>)'>
                    <?= $fs['Create or join set'] ?>
                </a>
            <?php } else { ?>
                <a href="./login.php" class="create-set__button">
                    <?= $fs['Create or join set'] ?>
                </a>
            <?php } ?>
        </div>
    </div>
</section>