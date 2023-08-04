let modal_cookie = document.getElementById("modalCookieModal");
let personal_account_modal_subscribe_respond = document.getElementById("modalPersonalAccountSubscriveRespond");
let personal_account_modal_withdraw = document.getElementById("modalPersonalAccountWithdraw");
let personal_account_modal_topup = document.getElementById('personalAccountBalanceTopup');
let personal_account_modal_topup_result = document.getElementById('modalPersonalAccountTopupResult');
let personal_account_modal_withdraw_action = document.getElementById('personalAccountBalanceWithdraw');
let personal_account_modal_withdraw_phone = document.getElementById('personalAccountBalanceWithdrawPhone');
let gallery_modal_alert_buy_image = document.getElementById("modalGalleryAlertBuyImage");
let personal_account_modal_delete_image = document.getElementById('modalPersonalAccountDeleteImage');
let modal_no_money = document.getElementById('modalNoMoney');
let personal_account_modal_topup_trade = document.getElementById('personalAccountBalanceTopupTrade');

window.onclick = function (event) {
    if (event.target == personal_account_modal_subscribe_respond) {
        hidePersonalAccountModalSubscribeRespond();
    }
}

checkCookieAlert();

function showModalCookie() {
    if (modal_cookie) {
        modal_cookie.style.display = "block";
    }
}

function hideModalCookie() {
    if (modal_cookie) {
        modal_cookie.style.display = "none";
    }
}

function checkCookieAlert() {
    let confirm_cookie = localStorage.getItem('confirm_cookie');
    if (!Boolean(Number(confirm_cookie))) {
        showModalCookie();
    }
}

function confirmCookie() {
    localStorage.setItem('confirm_cookie', '1');
    hideModalCookie();
}

function showPersonalAccountModalSubscribeRespond(message = '') {
    let txt = document.getElementById('personal-account-modal__text_sub_respond');
    txt.innerText = message;
    personal_account_modal_subscribe_respond.style.display = "block";
}

function hidePersonalAccountModalSubscribeRespond() {
    personal_account_modal_subscribe_respond.style.display = "none";
    reloadCurrentPage();
}

function showPersonalAccountModalTopup() {
    // let block = document.getElementById('smart-button-container');
    // if (block) {
    //     block.style.display = 'block';
    // }
    let input = document.querySelector('#amount');
    if (input) {
        input.value = '';
    }
    let fee = document.getElementById('topup-fee');
    if (fee) {
        fee.innerHTML = '';
    }
    let success = document.querySelector('.paypal-success');
    if (success) {
        $(success).remove();
    }
    personal_account_modal_topup.style.display = "block";
    document.addEventListener("mouseup", hidePersonalAccountModalTopup);
}

function hidePersonalAccountModalTopup(e) {
    if (e) {
        if (e.target != personal_account_modal_topup && !personal_account_modal_topup.contains(e.target)) {
            personal_account_modal_topup.style.display = "none";
            document.removeEventListener("mouseup", hidePersonalAccountModalTopup);
        }
    } else {
        personal_account_modal_topup.style.display = "none";
        document.removeEventListener("mouseup", hidePersonalAccountModalTopup);
    }
    let success = document.querySelector('.paypal-success');
    if (success) {
        reloadCurrentPage();
    }
}

// let check_init = false;
function openTopupModal() {
    hideModalSuccess();
    let container = document.getElementById('paypal-button-container');
    if(container) {
        container.innerHTML = '';
    }
    //let input = document.querySelector('#amount');
    //if (input) {
        let base_amount = Number(localStorage.getItem('needed_amount'));
        //let commision = Number(((((base_amount - 0.3) * 0.034) + 0.3)).toFixed(2));
        //y = (x + 0.3 - 0.3*0.034) / ((1 - 0.034))
        let amount = Number(base_amount) + Number(base_amount)*0.08 + 0.3;
        //input.value = amount;
        initPayPalButton(amount.toFixed(2));
        localStorage.setItem('needed_amount', '');
    //}
    let fee = document.getElementById('topup-fee');
    if (fee) {
        //fee.innerHTML = '';
        calculateTopupFee();
    }
    // if(!check_init) {
    //     initPayPalButtonTrade();
    //     check_init = true;
    // }
    personal_account_modal_topup_trade.style.display = "block";
    document.addEventListener("mouseup", closeTopupModal);
}

function closeTopupModal(e) {
    if (e) {
        if (e.target != personal_account_modal_topup_trade && !personal_account_modal_topup_trade.contains(e.target)) {
            personal_account_modal_topup_trade.style.display = "none";
            document.removeEventListener("mouseup", closeTopupModal);
        }
    } else {
        personal_account_modal_topup_trade.style.display = "none";
        document.removeEventListener("mouseup", closeTopupModal);
    }
    let success = document.querySelector('.paypal-success');
    if (success) {
        //reloadCurrentPage();
    }
}

function openWallpapersTopup(set_id) {
    if(document.querySelector('#modalUserImages')) {
        document.querySelector('#modalUserImages').remove();
    }
    if(document.querySelector('#modalChosenImage')) {
        document.querySelector('#modalChosenImage').remove();
    }
    let form_data = new FormData();
    form_data.append('set_id', set_id);
    form_data.append('action', 'check_set');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = JSON.parse(xhr.responseText);
            if (json.status) {
                let container = document.getElementById('paypal-button-container');
                if (container) {
                    container.innerHTML = '';
                }
                let base_amount = Number(json.price);
                let amount = Number(base_amount) + Number(base_amount) * 0.08 + 0.3;
                initPayPalButton(amount.toFixed(2), set_id, prepareCheck);
                let fee = document.getElementById('topup-fee');
                if (fee) {
                    calculateTopupFee();
                }
                document.querySelector('#wallpapersTopup').style.display = "block";
                document.addEventListener("mouseup", closeWallpapersTopup);
            }
        }
    };
    xhr.send(form_data);
}

function closeWallpapersTopup(e) {
    let modal = document.querySelector('#wallpapersTopup');
    if (e) {
        if (e.target != modal && !modal.contains(e.target)) {
            modal.style.display = "none";
            document.removeEventListener("mouseup", closeWallpapersTopup);
        }
    } else {
        modal.style.display = "none";
        document.removeEventListener("mouseup", closeWallpapersTopup);
    }
    let success = document.querySelector('.paypal-success');
    if (success) {
        //reloadCurrentPage();
    }
}

function showPersonalAccountModalTopupResult(message = '') {
    let txt = document.getElementById('personal-account-modal-topup-text');
    txt.innerText = message;
    personal_account_modal_topup_result.style.display = "block";
}

function hidePersonalAccountModalTopupResult() {
    personal_account_modal_topup_result.style.display = "none";
    reloadCurrentPage();
}

function showPersonalAccountModalDeleteImage(id) {
    // let txt = document.getElementById('personal-account-modal-delete-text');
    // txt.innerText = message;
    let button = document.getElementById('delete_button');
    if(button) {
        button.setAttribute('onclick', 'confirmDeleteGalleryImage('+id+')');
    }
    personal_account_modal_delete_image.style.display = "block";
}

function hidePersonalAccountModalDeleteImage() {
    personal_account_modal_delete_image.style.display = "none";
    //reloadCurrentPage();
}

function showPersonalAccountModalWithdrawAction() {
    let wallet = document.getElementById('withdraw_wallet');
    let amount = document.getElementById('withdraw_amount');
    let prompt = document.getElementById('transaction-fee');
    let withdraw_button = document.querySelector('.withdrow-button');
    if(wallet) {
        wallet.value = '';
    }
    if(amount) {
        amount.value = '';
    }
    if(prompt) {
        prompt.innerHTML = '';
    }
    if(withdraw_button) {
        withdraw_button.disabled = true;
    }
    personal_account_modal_withdraw_action.style.display = "block";
    document.addEventListener("mouseup", hidePersonalAccountModalWithdrawAction);
}

function hidePersonalAccountModalWithdrawAction(e = null) {
    if (e) {
        if (e.target != personal_account_modal_withdraw_action && !personal_account_modal_withdraw_action.contains(e.target)) {
            personal_account_modal_withdraw_action.style.display = "none";
            document.removeEventListener("mouseup", hidePersonalAccountModalWithdrawAction);
        }
    } else {
        personal_account_modal_withdraw_action.style.display = "none";
        document.removeEventListener("mouseup", hidePersonalAccountModalWithdrawAction);
    }
}

function showPersonalAccountModalWithdrawPhone() {
    personal_account_modal_withdraw_phone.style.display = "block";
    document.addEventListener("mouseup", hidePersonalAccountModalWithdrawPhone);
}

function hidePersonalAccountModalWithdrawPhone(e = null) {
    if (e) {
        if (e.target != personal_account_modal_withdraw_phone && !personal_account_modal_withdraw_phone.contains(e.target)) {
            personal_account_modal_withdraw_phone.style.display = "none";
            document.removeEventListener("mouseup", hidePersonalAccountModalWithdrawPhone);
        }
    } else {
        personal_account_modal_withdraw_phone.style.display = "none";
        document.removeEventListener("mouseup", hidePersonalAccountModalWithdrawPhone);
    }
}

function showPersonalAccountModalWithdraw(message, hide_button = true) {
    let txt = document.getElementById('personal-account-modal__text_sub_withdraw');
    txt.innerText = message;
    if (hide_button) {
        let button_container = document.getElementById('personal-account-balance-button-cover');
        button_container.style.display = 'none';
    }
    personal_account_modal_withdraw.style.display = "block";
}

function hidePersonalAccountModalWithdraw() {
    personal_account_modal_withdraw.style.display = "none";
    reloadCurrentPage();
}

function showModalGalleryAlertBuyImage() {
    gallery_modal_alert_buy_image.style.display = "block";
}

function hideModalGalleryAlertBuyImage() {
    gallery_modal_alert_buy_image.style.display = "none";
}

function showModalSuccess() {
    modal_no_money.style.display = "block";
}

function hideModalSuccess() {
    modal_no_money.style.display = "none";
}

function openModalPurchasedImage(elem) {
    document.querySelector('#price').value = Number(elem.getAttribute('data-price'));
    document.querySelector('#image').value = Number(elem.getAttribute('data-image'));
    document.querySelector('#purchasedWallpapersModal').style.display = 'block';
    document.addEventListener("mouseup", hideModalPurchasedImage);
}

function hideModalPurchasedImage(e = null) {
    let modal = document.querySelector('#purchasedWallpapersModal');
    if (e) {
        if (e.target != modal && !modal.contains(e.target)) {
            modal.style.display = "none";
            document.removeEventListener("mouseup", hideModalPurchasedImage);
        }
    } else {
        modal.style.display = "none";
        document.removeEventListener("mouseup", hideModalPurchasedImage);
    }
}

function openPromotionModal(image_id, type) {
    document.querySelector('#promotion').innerHTML = '';
    let form_data = new FormData();
    form_data.append('type', type);
    form_data.append('action', 'promotion_info');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = JSON.parse(xhr.responseText);
            if (json.status) {
                json.options.split(';').forEach(option => {
                    option = option.split(':');
                    let opt = document.createElement('option');
                    opt.value= option[0];
                    opt.innerHTML = `${option[0]} days - ${option[1]} USD`;
                    document.querySelector('#promotion').append(opt);
                });
                document.querySelector('#type_promotion').value = type;
                document.querySelector('#image_promotion').value = image_id;
                document.querySelector('#imagePromotionModal').style.display = 'block';
                document.addEventListener("mouseup", hidePromotionModal);
            }
        }
    };
    xhr.send(form_data);
}

function hidePromotionModal(e) {
    let modal = document.querySelector('#imagePromotionModal');
    if (e) {
        if (e.target != modal && !modal.contains(e.target)) {
            modal.style.display = "none";
            document.removeEventListener("mouseup", hidePromotionModal);
        }
    } else {
        modal.style.display = "none";
        document.removeEventListener("mouseup", hidePromotionModal);
    }
}

function showModalResult(text = '') {
    let text_container = document.querySelector('#modal-result-text');
    if(text_container) {
        text_container.innerHTML = text;
    }
    document.querySelector('#modalResult').style.display = 'block';
}