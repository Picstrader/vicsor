<div id="personalAccountBalanceWithdraw" class="personal_account_balance_popup" style="display:none;">
    <div class="personal_account_balance_header">
        <?= $fs['Withdraw'] ?>
    </div>
    <div class="withdrow-inputs-block">
        <input type="number" id="withdraw_amount" class="withdrow-input" oninput="calculateSum()" placeholder="<?= $fs['Enter withdraw amount'] ?><?= ' ' . $fs['main_currency'] ?>">
        <input id="withdraw_wallet" class="withdrow-input" oninput="calculateSum()" placeholder="<?= $fs['Enter your PayPal account'] ?>">
    </div>
    <div class="">
        <span class="" id="transaction-fee" style="color:red;"></span>
    </div>
    <button class="withdrow-button" onclick="startPhoneConfirmation(this)" style="background:#007AFF50;" disabled>
        <?= $fs['Submit'] ?>
    </button>
</div>