<div id="personalAccountBalanceWithdrawPhone" class="personal_account_balance_popup" style="display:none;">
    <div class="personal_account_balance_popup_cover">
        <div class="personal_account_balance_header">Enter code from SMS to confirm operation</div>
        <input id="withdraw_code"
            class="pers_acc__balance_input" type="number" oninput="updateCodeInput(this)"><button
            class="inbalance_button" onclick="finishPhoneConfirmation()">Submit</button>
    </div>
</div>