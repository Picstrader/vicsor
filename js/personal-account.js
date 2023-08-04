
let personal_account_modal = document.getElementById("modalPersonalAccount");
let personal_account_modal_confirm = document.getElementById("modalConfirmEmail");
let personal_account_modal_subscribe = document.getElementById("modalSubscription");
let personal_account_modal_image = document.getElementById("modalGallery");
let load_new_my_logs = false;
let my_activity_current_page = 1;
let purchased_current_page = 1;
let last_opened_image = null;
if (personal_account_modal != null) {
    window.onclick = function (event) {
        if (event.target == personal_account_modal) {
            personal_account_modal.style.display = "none";
        } else if (event.target == personal_account_modal_confirm) {
            personal_account_modal_confirm.style.display = "none";
        } else if (event.target == personal_account_modal_subscribe) {
            personal_account_modal_subscribe.style.display = "none";
        } else if (event.target == personal_account_modal_image) {
            personal_account_modal_image.style.display = "none";
        }
    }
}

switchConfirmModalButtonSrc();

function showPersonalAccountModal(message = '') {
    let txt = document.getElementById('personal-account-modal__text');
    txt.innerText = message;
    personal_account_modal.style.display = "block";
}

function hidePersonalAccountModal() {
    personal_account_modal.style.display = "none";
}

function showPersonalAccountModalConfirm() {
    personal_account_modal_confirm.style.display = "block";
}

function hidePersonalAccountModalConfirm() {
    personal_account_modal_confirm.style.display = "none";
}

function hidePersonalAccountModalConfirmRedirect() {
    personal_account_modal_confirm.style.display = "none";
    window.location.href = '/trade.php';
}

function showModalSubscription() {
    personal_account_modal_subscribe.style.display = "block";
}

function hideModalSubscription() {
    personal_account_modal_subscribe.style.display = "none";
}

function avatarFileSelected() {
    document.getElementById('avatar_send_submit').click();
}

function button_change(data) {
    console.log(data);
    if (data == 'profile') {
        document.getElementById('profile').style.backgroundColor = ('#D0DCE8');
        document.getElementById('change-password').style.backgroundColor = ('');
        document.getElementById('my-activity').style.backgroundColor = ('');
        document.getElementById('purchased-images').style.backgroundColor = ('');
        document.getElementById('balance').style.backgroundColor = ('');
    } else if (data == 'change-password') {
        document.getElementById('profile').style.backgroundColor = ('');
        document.getElementById('change-password').style.backgroundColor = ('#D0DCE8');
        document.getElementById('my-activity').style.backgroundColor = ('');
        document.getElementById('purchased-images').style.backgroundColor = ('');
        document.getElementById('balance').style.backgroundColor = ('');
    } else if (data == 'my-activity') {
        document.getElementById('profile').style.backgroundColor = ('');
        document.getElementById('change-password').style.backgroundColor = ('');
        document.getElementById('my-activity').style.backgroundColor = ('#D0DCE8');
        document.getElementById('purchased-images').style.backgroundColor = ('');
        document.getElementById('balance').style.backgroundColor = ('');
    } else if (data == 'purchased-images') {
        document.getElementById('profile').style.backgroundColor = ('');
        document.getElementById('change-password').style.backgroundColor = ('');
        document.getElementById('my-activity').style.backgroundColor = ('');
        document.getElementById('purchased-images').style.backgroundColor = ('#D0DCE8');
        document.getElementById('balance').style.backgroundColor = ('');
    } else if (data == 'balance') {
        document.getElementById('profile').style.backgroundColor = ('');
        document.getElementById('change-password').style.backgroundColor = ('');
        document.getElementById('my-activity').style.backgroundColor = ('');
        document.getElementById('purchased-images').style.backgroundColor = ('');
        document.getElementById('balance').style.backgroundColor = ('#D0DCE8');
    }
}




function pers_acc_pass_show_hide(type, act) {
    var ar0 = document.querySelectorAll(`[data-type = '${type}']`);
    for (var i = 0; i < ar0.length; i++) {
        if (ar0[i].getAttribute('data-act') == act) {
            ar0[i].style.display = 'none';
        } else if ((ar0[i].getAttribute('data-act') != act) && (ar0[i].getAttribute('data-act') != '')) {
            ar0[i].style.display = 'inline-block';
        }
    }
    var inps = document.querySelectorAll(`input[data-type = '${type}']`);
    for (var i = 0; i < inps.length; i++) {
        inps[i]
        if (act == 'show') {
            inps[i].setAttribute('type', 'text');
        } else {
            inps[i].setAttribute('type', 'password');
        }
    }
}

function sellInGallery(button, image_id) {
    let parent_div = button.parentNode;
    let input = parent_div.querySelector('input');
    let text = document.getElementById('change-price-text');
    text.classList.remove('error-element');
    if (Number(input.value) < 0.1) {
        //showPersonalAccountModal(fs["The minimum allowable price is 0.1"] + " " + fs['main_currency']);
        text.classList.add('error-element');
        text.innerHTML = 'Price cannot be less than 0.1';
        return;
    }
    if (text) {
        text.innerHTML = 'Price change ...';
    }
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('price', input.value);
    form_data.append('action', 'set_price');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                if (last_opened_image !== null) {
                    let price_span = last_opened_image.parentNode.querySelector('.gallery-images__price');
                    if (price_span) {
                        price_span.innerText = json.price + ' ' + fs['main_currency'];
                    }
                }
                if (text) {
                    text.innerHTML = 'The image was placed in the gallery';
                }
            } else {
                if (text) {
                    text.innerHTML = 'The image was placed in the gallery';
                }
            }
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            let json = xhr.responseText;
            if (text) {
                text.classList.add('error-element');
                text.innerHTML = 'The price has not been changed';
            }
        }
    };
    xhr.send(form_data);
}

function sellWallpapers(image_id) {
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('action', 'trade_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            window.location.href = "/trade.php";
        }
    };
    xhr.send(form_data);
}

function changeBalanceInput(elem) {
    let input = document.getElementById('amount_value');
    let amount_value = Number(input.value);
    if (input.value == '') {
        input.value = '';
        $('#amount_value_real').text(fs["Amount must be an integer"]);
        $('#amount_value').removeClass("trade_modal__error");
    } else if (Number.isInteger(amount_value) && amount_value < 10) {
        $('#amount_value_real').text('Amount must be more or equal 10' + ' ' + fs['main_currency']);
        $('#amount_value').addClass("trade_modal__error");
    } else if (Number.isInteger(amount_value) && amount_value > 0) {
        real_value = amount_value;
        // let real_value = (amount_value * 100) / 97;
        // real_value = real_value.toFixed(2);
        $('#amount_value_real').html(fs["Final price"] + " <br>" + real_value + ' ' + fs['main_currency']);
        $('#amount_value').removeClass("trade_modal__error");
    } else {
        input.value = '';
        $('#amount_value_real').text(fs["Amount must be an integer"]);
        $('#amount_value').addClass("trade_modal__error");
    }
}

function changeWithdrawInput(elem) {
    $('#amount_value').removeClass("trade_modal__error");
    let input = document.getElementById('amount_value');
    let amount_value = Number(input.value);
    if (input.value == '') {
        input.value = '';
        $('#amount_value_real').text(fs["Enter an integer USD"] + ' ' + fs['main_currency']);
        $('#amount_value').removeClass("trade_modal__error");
    } else if (Number.isInteger(amount_value) && amount_value > 0) {
    } else {
        input.value = '';
        $('#amount_value_real').text(fs["Amount must be an integer"]);
        $('#amount_value').addClass("trade_modal__error");
    }
}

function preparePayment(button) {
    let input = document.getElementById('amount_value');
    if (Number(input.value) <= 0 || !Number.isInteger(Number(input.value))) {
        addErrorMessage(input, 'wrong input value');
        $('#amount_value').addClass("trade_modal__error");
        return;
    }
    let onclick = button.onclick;
    button.onclick = '';
    let form_data = new FormData();
    form_data.append('amount', input.value);
    form_data.append('action', 'create_order');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            console.log(json);
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                window.location.href = json.pay_url;
            }
            button.onclick = onclick;
            forceRemoveBalanceModal();
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            addErrorMessage(input, json.message, true);
            $('#amount_value').addClass("trade_modal__error");
            button.onclick = onclick;
        }
    };
    xhr.send(form_data);
}

function prepareWithdrawFunds(button) {
    let input = document.getElementById('amount_value');
    if (Number(input.value) <= 0 || !Number.isInteger(Number(input.value))) {
        addErrorMessage(input, 'wrong input value');
        $('#amount_value').addClass("trade_modal__error");
        return;
    }
    let onclick = button.onclick;
    button.onclick = '';
    let form_data = new FormData();
    form_data.append('amount', input.value);
    form_data.append('action', 'withdraw_funds');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            console.log(json);
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                showPersonalAccountModalWithdraw(json.message);
                updateBalanceInHeader();
                updateBalanceOnBalancePage();
            }
            button.onclick = onclick;
            forceRemoveBalanceModal();
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            addErrorMessage(input, json.message, true);
            $('#amount_value').addClass("trade_modal__error");
            button.onclick = onclick;
        } else if (xhr.readyState === 4 && xhr.status === 403) {
            forceRemoveBalanceModal();
            let json = xhr.responseText;
            console.log(json);
            json = JSON.parse(json);
            if (!Boolean(json.status)) {
                showPersonalAccountModalWithdraw(json.message, false);
            }
            button.onclick = onclick;
        }
    };
    xhr.send(form_data);
}

function buySubcsription(button) {
    let onclick = button.onclick;
    button.onclick = '';
    let form_data = new FormData();
    // form_data.append('id', sub_id);
    form_data.append('action', 'subscription');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            console.log(json);
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                updateBalanceInHeader();
                updateBalanceOnBalancePage();
                // updateSubOnBalancePage();
                if (document.getElementById('modalError')) {
                    hideModalErrorBig();
                    showModalSuccessSubscription(json.message);
                } else if (document.getElementById('modalGalleryResult')) {
                    hideGalleryModelResult();
                    showGallerySuccessSubscription(json.message);
                } else if (document.getElementById('modalSubscription')) {
                    hideModalSubscription();
                    showPersonalAccountModalSubscribeRespond(json.message);
                }
            }
            button.onclick = onclick;
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (document.getElementById('modalError')) {
                hideModalErrorBig();
                showModalSuccessSubscription(json.message);
            } else if (document.getElementById('modalGalleryResult')) {
                hideGalleryModelResult();
                showGallerySuccessSubscription(json.message);
            } else if (document.getElementById('modalSubscription')) {
                hideModalSubscription();
                showPersonalAccountModalSubscribeRespond(json.message);
            }
            button.onclick = onclick;
        } else if (xhr.readyState === 4 && xhr.status === 403) {
            let json = xhr.responseText;
            console.log(json);
            json = JSON.parse(json);
            if (!Boolean(json.status)) {
                hideModalErrorBig();
            }
            button.onclick = onclick;
        }
    };
    xhr.send(form_data);
}

function updateBalanceOnBalancePage() {
    let check_exist = document.getElementById('balance-balance-span');
    if (!check_exist) {
        return;
    }
    let form_data = new FormData();
    form_data.append('action', 'get_balance');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                let balance = document.getElementById('balance-balance-span');
                if (balance) {
                    balance.innerText = json.balance;
                }
            }
        }
    };
    xhr.send(form_data);
}

function updateSubOnBalancePage() {
    let form_data = new FormData();
    form_data.append('action', 'update_sub_amount');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                let sub = document.getElementById('sub-balance-span');
                if (sub) {
                    sub.innerText = json.amount;
                }
                let type = document.getElementById('sub-balance-span-type');
                if (type) {
                    type.innerText = fs[json.type];
                }
            }
        }
    };
    xhr.send(form_data);
}

function updateLogsList(page = 1) {
    let form_data = new FormData();
    form_data.append('page', page);
    form_data.append('action', 'update_logs');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let div = document.createElement('div');
            div.innerHTML = json;
            let container_logs = div.querySelector('.main_tbody1');
            if (container_logs.getElementsByTagName('tr').length <= 0) {
                my_activity_current_page--;
            }
            $(container_logs).contents().appendTo('.main_tbody1');
            load_new_my_logs = false;
        }
    };
    xhr.send(form_data);
}

function updateBalanceLogsList(page = 1) {
    let form_data = new FormData();
    form_data.append('page', page);
    form_data.append('action', 'update_balance_logs');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let div = document.createElement('div');
            div.innerHTML = json;
            let container_logs = div.querySelector('.main_tbody1');
            if (container_logs.getElementsByTagName('tr').length <= 0) {
                my_activity_current_page--;
            }
            $(container_logs).contents().appendTo('.main_tbody1');
            load_new_my_logs = false;
        }
    };
    xhr.send(form_data);
}

function scrollUpdateMyActivity(elem) {
    if (elem.offsetHeight + elem.scrollTop >= elem.scrollHeight) {
        if (load_new_my_logs) {
            return;
        } else {
            load_new_my_logs = true;
        }
        my_activity_current_page++;
        console.log(my_activity_current_page);
        updateLogsList(my_activity_current_page);
    }
}

function scrollUpdateBalance(elem) {
    if (elem.offsetHeight + elem.scrollTop >= elem.scrollHeight) {
        if (load_new_my_logs) {
            return;
        } else {
            load_new_my_logs = true;
        }
        my_activity_current_page++;
        console.log(my_activity_current_page);
        updateBalanceLogsList(my_activity_current_page);
    }
}

if (window.location.href.includes('personal-account.php')) {
    document.addEventListener('scroll', scrollUpdateProfileImage);
}

function scrollUpdateProfileImage(e) {
    let limit = Math.max(document.body.scrollHeight, document.body.offsetHeight,
        document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight);
    if (document.body.offsetHeight + window.scrollY >= limit) {
        document.removeEventListener('scroll', scrollUpdateProfileImage);
        let page = Number(document.querySelector('#my-images-cover').getAttribute('data-page')) + 1;
        updateProfileImagesList(page);
    }
}

function updateProfileImagesList(page = 1) {
    let form_data = new FormData();
    form_data.append('page', page);
    form_data.append('action', 'update_profile_images');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.addEventListener('scroll', scrollUpdateProfileImage);
            let div = document.createElement('div');
            div.innerHTML = xhr.responseText;
            let container_gallery = div.querySelector('#my-images-cover');
            if (!container_gallery || container_gallery.getElementsByClassName('gallery-images__image-data').length <= 0) {
                document.querySelector('#my-images-cover').setAttribute('data-page', page - 1);
            } else {
                document.querySelector('#my-images-cover').setAttribute('data-page', page);
                $(container_gallery).contents().appendTo('#my-images-cover');
            }
        }
    };
    xhr.send(form_data);
}

let topUpBlock = document.querySelector('.top-up-block');
function topUp() {
    topUpBlock.classList.toggle('active');
}

// function openTopUpModal(open_button) {
//     addBalanceModal(open_button, 'changeBalanceInput()', 'pay()', 'Top-up');
// }

function openWithdrawModal(open_button) {
    //addBalanceModal(open_button, 'changeWithdrawInput()', 'prepareWithdrawFunds(this)', 'Withdraw');
}

let check_create_balance = false;

function addBalanceModal(error_element, input_action, button_action, title, not_click = false) {
    forceRemoveBalanceModal();
    check_create_balance = not_click ? true : false;
    let div = document.createElement('div');
    div.className = 'personal_account_balance_popup';

    let div_header = document.createElement('div');
    div_header.innerText = fs[title];
    div_header.className = 'personal_account_balance_header';

    let input = document.createElement('input');
    input.id = 'amount_value';
    input.className = 'pers_acc__balance_input';
    input.setAttribute('type', 'number');
    input.setAttribute('oninput', input_action);

    let span = document.createElement('span');
    span.id = 'amount_value_real';
    span.innerText = fs['Enter an integer USD'] + fs['main_currency'];
    span.className = 'personal_account_balance_span_cover';

    let div_span_cover = document.createElement('div');
    div_span_cover.className = 'personal_account_balance_span_cover';

    let button = document.createElement('button');
    button.className = 'inbalance_button';
    button.innerText = fs['Submit'];
    button.setAttribute('onclick', button_action);

    div_span_cover.append(span);
    div.append(div_header);
    div.append(input);
    div.append(div_span_cover);
    div.append(button);
    let section = document.body;
    section.append(div);
    document.addEventListener("click", removeBalanceModal);
}

function addPhoneVerificationModal(not_click = true) {
    forceRemovePhoneVerificationModal();
    check_create_balance = not_click ? true : false;
    let div = document.createElement('div');
    div.className = 'personal_account_balance_popup';

    let div_cover = document.createElement('div');
    div_cover.className = 'personal_account_balance_popup_cover';

    let div_header = document.createElement('div');
    div_header.innerText = fs['Enter code from SMS'];
    div_header.className = 'personal_account_balance_header';

    let input = document.createElement('input');
    input.id = 'verification_code';
    input.className = 'pers_acc__balance_input';
    input.setAttribute('type', 'number');
    input.setAttribute('oninput', 'updateCodeInput(this)');

    let button = document.createElement('button');
    button.className = 'inbalance_button';
    button.innerText = fs['Submit'];
    button.setAttribute('onclick', 'confirmPhoneVerification()');

    div_cover.append(div_header);
    div_cover.append(input);
    div_cover.append(button);
    div.append(div_cover);
    let section = document.body;
    section.append(div);
    document.addEventListener("click", removePhoneVerificationModal);
}

function removeBalanceModal(e) {
    if (!check_create_balance) {
        check_create_balance = true;
    } else {
        let balance_modal = $('.personal_account_balance_popup');
        let check_balance_modal = document.querySelector('.personal_account_balance_popup');
        if (!check_balance_modal) {
            return;
        }
        if (balance_modal && e.target != check_balance_modal && !check_balance_modal.contains(e.target)) {
            balance_modal.remove();
            document.removeEventListener("click", removeBalanceModal);
            check_create_balance = false;
        }
    }
}

function removePhoneVerificationModal(e) {
    if (!check_create_balance) {
        check_create_balance = true;
    } else {
        let balance_modal = $('.personal_account_balance_popup');
        let check_balance_modal = document.querySelector('.personal_account_balance_popup');
        if (!check_balance_modal) {
            return;
        }
        if (balance_modal && e.target != check_balance_modal && !check_balance_modal.contains(e.target)) {
            balance_modal.remove();
            document.removeEventListener("click", removePhoneVerificationModal);
            check_create_balance = false;
        }
    }
}

function forceRemoveBalanceModal() {
    let balance_modal = $('.personal_account_balance_popup');
    if (balance_modal) {
        balance_modal.remove();
        document.removeEventListener("click", removeBalanceModal);
    }
}

function forceRemovePhoneVerificationModal() {
    let balance_modal = $('.personal_account_balance_popup');
    if (balance_modal) {
        balance_modal.remove();
        document.removeEventListener("click", removePhoneVerificationModal);
    }
}

function sendConfirmEmail() {
    let form_data = new FormData();
    form_data.append('action', 'send_confirm_email');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            try {
                json = JSON.parse(json);
                if (Boolean(json.status)) {
                    showSuccessEmailSend();
                } else {
                    showErrorEmailSend();
                }
            } catch (error) {
                showErrorEmailSend();
            }
        }
    };
    xhr.send(form_data);
}

function sendConfirmPhone(button, phone = null) {
    if (!phone) {
        return;
    }
    let balance_modal = document.querySelector('.personal_account_balance_popup');// $('.personal_account_balance_popup');
    if (balance_modal) {
        return;
    }
    let save_onclick = button.onclick;
    button.onclick = '';
    let form_data = new FormData();
    form_data.append('action', 'send_confirm_phone');
    form_data.append('phone', phone);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            try {
                json = JSON.parse(json);
                if (Boolean(json.status)) {
                    showSuccessPhoneSendSMS()
                    addPhoneVerificationModal();
                } else {
                    showErrorPhoneSendSMS();
                }
            } catch (error) {
            }
            button.onclick = save_onclick;
        }
    };
    xhr.send(form_data);
}

function startPhoneConfirmation(button) {
    let amount = document.getElementById('withdraw_amount');
    let wallet = document.getElementById('withdraw_wallet');
    let prompt = document.getElementById('transaction-fee');
    // if (Number(amount.value) < 50) {
    //     prompt.innerHTML = fs['The amount must be at least'] + ' 50 ' + fs['main_currency'];
    //     return;
    // }
    if (amount.value == '' || wallet.value == '') {
        prompt.innerHTML = '';
        return;
    }
    let save_onclick = button.onclick;
    button.onclick = '';
    let form_data = new FormData();
    form_data.append('action', 'start_phone_confirmation');
    form_data.append('amount', amount.value);
    form_data.append('wallet', wallet.value);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            try {
                json = JSON.parse(json);
                if (Boolean(json.status)) {
                    hidePersonalAccountModalWithdrawAction();
                    showPersonalAccountModalWithdrawPhone();
                } else {
                    hidePersonalAccountModalWithdrawAction();
                    showPersonalAccountModalWithdraw(json.message, false);
                }
            } catch (error) {
            }
            button.onclick = save_onclick;
        } else if (xhr.readyState === 4 && xhr.status === 203) {
            let json = xhr.responseText;
            try {
                json = JSON.parse(json);
                if (Boolean(json.status)) {
                    hidePersonalAccountModalWithdrawAction();
                    showPersonalAccountModalWithdrawPhone();
                } else {
                    hidePersonalAccountModalWithdrawAction();
                    showPersonalAccountModalWithdraw(json.message);
                }
            } catch (error) {
            }
            button.onclick = save_onclick;
        }
    };
    xhr.send(form_data);
}

function finishPhoneConfirmation() {
    let code = document.getElementById('withdraw_code').value;
    if (!code) {
        return;
    }
    let form_data = new FormData();
    form_data.append('action', 'finish_phone_confirmation');
    form_data.append('code', code);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            console.log(json);
            try {
                json = JSON.parse(json);
                if (Boolean(json.status)) {
                    hidePersonalAccountModalWithdrawPhone();
                    showPersonalAccountModalWithdraw('The PayPal payment system processes transactions in up to three days. Wait for confirmation by mail.');
                    updateBalanceInHeader();
                    updateBalanceOnBalancePage();
                } else {
                    addErrorMessage(document.getElementById('withdraw_code'), fs["Wrong code"], true);
                    $('#withdraw_code').addClass("trade_modal__error");
                }
            } catch (error) {
            }
        } else if (xhr.readyState === 4 && xhr.status === 203) {
            try {
                json = JSON.parse(json);
                //if (!Boolean(json.status)) {
                hidePersonalAccountModalWithdrawPhone();
                showPersonalAccountModalWithdraw(json.message);
                // updateBalanceInHeader();
                // updateBalanceOnBalancePage();
                //}
            } catch (error) {
                hidePersonalAccountModalWithdrawPhone();
                showPersonalAccountModalWithdraw(fs['Operation failed']);
            }
        }
    };
    xhr.send(form_data);
}

function confirmPhoneVerification() {
    let code = document.getElementById('verification_code').value;
    if (!code) {
        return;
    }
    let form_data = new FormData();
    form_data.append('action', 'confirm_phone_verification');
    form_data.append('code', code);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            try {
                json = JSON.parse(json);
                if (Boolean(json.status)) {
                    forceRemovePhoneVerificationModal();
                    showSuccessPhoneVerification();
                    let button = document.getElementById('phone-confirm-button');
                    if (button) {
                        button.style.display = 'none';
                    }
                } else {
                    addErrorMessage(document.getElementById('verification_code'), fs["Wrong code"], true);
                    $('#verification_code').addClass("trade_modal__error");
                }
            } catch (error) {
            }
        }
    };
    xhr.send(form_data);
}

function updateCodeInput(el) {
    $(el).removeClass("trade_modal__error");
}

function showSuccessPhoneVerification() {
    let text_node = document.getElementById('phone-confirm-notice');
    if (text_node) {
        $(text_node).removeClass('personal-account-email-confirm-notice');
        $(text_node).addClass('personal-account-email-confirm-notice-success');
        text_node.innerText = fs["Phone confirmed"];
    }
}

function showSuccessPhoneSendSMS() {
    let text_node = document.getElementById('phone-confirm-notice');
    if (text_node) {
        $(text_node).removeClass('personal-account-email-confirm-notice');
        $(text_node).addClass('personal-account-email-confirm-notice-success');
        text_node.innerText = fs["SMS was sent"];
    }
}

function showErrorPhoneSendSMS() {
    let text_node = document.getElementById('phone-confirm-notice');
    if (text_node) {
        $(text_node).addClass('personal-account-email-confirm-notice');
        $(text_node).removeClass('personal-account-email-confirm-notice-success');
        text_node.innerText = fs["SMS was not sent"];
    }
}

function showSuccessEmailSend() {
    let text_node = document.getElementById('email-confirm-notice');
    if (text_node) {
        $(text_node).removeClass('personal-account-email-confirm-notice');
        $(text_node).addClass('personal-account-email-confirm-notice-success');
        text_node.innerText = fs["Email has been sent"];
    }
}

function showErrorEmailSend() {
    let text_node = document.getElementById('email-confirm-notice');
    if (text_node) {
        $(text_node).addClass('personal-account-email-confirm-notice');
        $(text_node).removeClass('personal-account-email-confirm-notice-success');
        text_node.innerText = fs["Email has not been sent"];
    }
}

function switchConfirmModalButtonSrc() {
    let themeHeader = document.getElementById("header");
    if (themeHeader.getAttribute("href") == "inc/assets/css/header.css") {
        let close_button = document.getElementById('modal-confirm-close');
        if (close_button) {
            close_button.src = '/inc/assets/img/closemodal.svg';
        }
    } else {
        let close_button = document.getElementById('modal-confirm-close');
        if (close_button) {
            close_button.src = '/inc/assets/img/closemodalwhite.svg';
        }
    }
}

function copyReferralLink() {
    let copyText = document.getElementById("referral_link");
    copyText.select();
    navigator.clipboard.writeText(copyText.value);
}

function openUserImage(img) {
    last_opened_image = img;
    img_data = JSON.parse(img.getAttribute('data-image'));
    console.log(img_data.id);
    let form_data = new FormData();
    form_data.append('image_id', img_data.id);
    form_data.append('action', 'open_user_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let div = document.createElement('div');
            div.innerHTML = xhr.responseText;
            document.getElementById('modalGallery').replaceWith(div.firstChild);
            personal_account_modal_image = document.getElementById("modalGallery");
            showUserImageModal();
        }
    };
    xhr.send(form_data);
}

function openUserPurchasedImage(img) {
    last_opened_image = img;
    img_data = JSON.parse(img.getAttribute('data-image'));
    console.log(img_data.id);
    let form_data = new FormData();
    form_data.append('image_id', img_data.id);
    form_data.append('action', 'open_purchased_user_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let div = document.createElement('div');
            div.innerHTML = xhr.responseText;
            document.getElementById('modalGallery').replaceWith(div.firstChild);
            personal_account_modal_image = document.getElementById("modalGallery");
            showUserImageModal();
        }
    };
    xhr.send(form_data);
}

function showUserImageModal() {
    personal_account_modal_image.style.display = "block";
}

function hideUserImageModal() {
    personal_account_modal_image.style.display = "none";
}

let interval = null;
let animation_count = 0;
let attempt = 10;
function pay(elem) {
    let hash = document.querySelector('.top-up-input');
    if (hash.value == '') {
        return;
    }
    let action = elem.getAttribute('onclick');
    elem.setAttribute('onclick', '');
    searchAnimation();
    let form_data = new FormData();
    form_data.append('hash', hash.value);
    form_data.append('action', 'check_transaction');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                hidePersonalAccountModalTopup(null);
                showPersonalAccountModalTopupResult('Transaction found, funds will arrive shortly');
                updateBalanceInHeader();
                updateBalanceOnBalancePage();
            } else {
                hidePersonalAccountModalTopup(null);
                showPersonalAccountModalTopupResult('The transaction has already been paid');
            }
            elem.setAttribute('onclick', action);
            clearInterval(interval);
            interval = null;
            attempt = 10;
            animation_count = 0;
            document.querySelector('.top-up-button').innerHTML = fs['I paid'];
        }
        if (xhr.readyState === 4 && xhr.status === 204) {
            elem.setAttribute('onclick', action);
            let topup_modal = document.getElementById('personalAccountBalanceTopup');
            if (topup_modal.style.display != 'none' && attempt) {
                attempt--;
                pay(elem);
            } else {
                clearInterval(interval);
                interval = null;
                attempt = 10;
                document.querySelector('.top-up-button').innerHTML = fs['I paid'];
            }
        }
    };
    xhr.send(form_data);
}

function searchAnimation() {
    if (interval) return;
    interval = setInterval(function () {
        animation_count++;
        var dots = new Array(animation_count % 5).join('.');
        document.querySelector('.top-up-button').innerHTML = "Searching" + dots;
    }, 500);
}

function saveTransaction(data) {
    let form_data = new FormData();
    form_data.append('data', JSON.stringify(data));
    form_data.append('action', 'create_transaction');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
        }
    };
    xhr.send(form_data);
}

let search_interval = null;

//checkTransactions();
// const http = require('http');
// const url = require('url');
// const Web3 = require('web3');
// const axios = require('axios');

function checkTransactions() {
    let form_data = new FormData();
    form_data.append('action', 'check_transactions');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            //document.body.innerHTML = json;
        }
    };
    xhr.send(form_data);
}

async function calculateSum() {
    let wallet = document.getElementById('withdraw_wallet');
    let amount = document.getElementById('withdraw_amount');
    let prompt = document.getElementById('transaction-fee');
    let balance = document.getElementById('header-balance-span');
    let withdraw_button = document.querySelector('.withdrow-button');
    if (amount.value == '') {
        prompt.innerHTML = '';
        withdraw_button.style.background = "#007AFF50";
        withdraw_button.disabled = true;
        return;
    }
    // if (Number(amount.value) < 50) {
    //     prompt.innerHTML = fs['The amount must be at least'] + ' 50 ' + fs['main_currency'];
    //     withdraw_button.style.background = "#007AFF50";
    //     withdraw_button.disabled = true;
    //     return;
    // }
    if (Number(amount.value) > Number(balance.innerText)) {
        prompt.innerHTML = fs['The amount cannot exceed your balance'];
        withdraw_button.style.background = "#007AFF50";
        withdraw_button.disabled = true;
        return;
    }
    prompt.innerHTML = '';
    if (wallet.value == '') {
        withdraw_button.style.background = "#007AFF50";
        withdraw_button.disabled = true;
        return;
    }
    withdraw_button.style.background = "#007AFF";
    withdraw_button.disabled = false;
}

function calculateTopupFee(cost = null) {
    let amount = document.getElementById('amount');
    let prompt = document.getElementById('topup-fee');
    if (cost) {
        amount.value = cost;
    }
    if (amount.value == '') {
        prompt.innerHTML = '';
        return;
    }
    // let commision = (((Number(amount.value) - 0.3) * 0.034) + 0.3);
    // let receive = Number(amount.value) - commision;
    // prompt.innerHTML = fs['Commission'] + ' ' + commision.toFixed(2) + ' ' + fs['main_currency'] + ', ' + fs['you will receive'] + ' ' + receive.toFixed(2) + ' ' + fs['main_currency'];
    prompt.innerHTML = fs["Top-up amount"] + ": " + Number(amount.value) + ' ' + fs['main_currency']/* + " " + fs['minus'] + " " + fs["payment system commission"]*/;
}

function deleteGalleryImage(id) {
    showPersonalAccountModalDeleteImage(id);
    // let form_data = new FormData();
    // form_data.append('action', 'delete_gallery_image');
    // form_data.append('image_id', id);
    // let xhr = new XMLHttpRequest();
    // xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    // xhr.onreadystatechange = function () {
    //     if (xhr.readyState === 4 && xhr.status === 200) {
    //         showPersonalAccountModalDeleteImage('Image was deleted');
    //     } else {
    //         showPersonalAccountModalDeleteImage('error');
    //     }
    // };
    // xhr.send(form_data);
}

function confirmDeleteGalleryImage(id) {
    let form_data = new FormData();
    form_data.append('action', 'delete_gallery_image');
    form_data.append('image_id', id);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            reloadCurrentPage();
        } else {
            reloadCurrentPage();
        }
    };
    xhr.send(form_data);
}

function setPurchasedPrice(button, image_id) {
    let parent_div = button.parentNode;
    let input = parent_div.querySelector('input');
    let text = document.getElementById('change-price-text');
    text.classList.remove('error-element');
    if (Number(input.value) < 0.1) {
        //showPersonalAccountModal(fs["The minimum allowable price is 0.1"] + " " + fs['main_currency']);
        text.classList.add('error-element');
        text.innerHTML = 'Price cannot be less than 0.1';
        return;
    }
    if (text) {
        text.innerHTML = 'Price change ...';
    }
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('price', input.value);
    form_data.append('action', 'set_purchased_price');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (text) {
                text.innerHTML = 'Your price was setted';
            }
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            let json = xhr.responseText;
            if (text) {
                text.classList.add('error-element');
                text.innerHTML = 'The price has not been setted';
            }
        }
    };
    xhr.send(form_data);
}

function sellPurchasedImage(button, image_id) {
    let parent_div = button.parentNode;
    let input = parent_div.querySelector('input');
    let text = document.getElementById('change-price-text');
    text.classList.remove('error-element');
    if (Number(input.value) < 0.1) {
        //showPersonalAccountModal(fs["The minimum allowable price is 0.1"] + " " + fs['main_currency']);
        text.classList.add('error-element');
        text.innerHTML = 'Price cannot be less than 0.1';
        return;
    }
    if (text) {
        text.innerHTML = 'Price change ...';
    }
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('action', 'sell_purchased_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (text) {
                text.innerHTML = 'Your price was setted';
            }
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            let json = xhr.responseText;
            if (text) {
                text.classList.add('error-element');
                text.innerHTML = 'The price has not been setted';
            }
        }
    };
    xhr.send(form_data);
}

function makePromotion() {
    let promotion = document.querySelector('#type_promotion').value;
    let form_data = new FormData();
    form_data.append('image_id', document.querySelector('#image_promotion').value);
    form_data.append('promotion', document.querySelector('#promotion').value);
    form_data.append('type_promotion', document.querySelector('#type_promotion').value);
    form_data.append('action', 'promotion');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let data = JSON.parse(xhr.responseText);
            if(data.status) {
                if(document.querySelector('#modalGallery')) {
                    document.querySelector('#modalGallery').style.display = 'none';
                }

                if(document.querySelector('#imagePromotionModal')) {
                    document.querySelector('#imagePromotionModal').style.display = 'none';
                }
                showModalResult(promotion == 'lift_up' ? 'Image will be lifted up soon' : 'Image will be pinned to top soon');
            }
        }
    };
    xhr.send(form_data);   
}