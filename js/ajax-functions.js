let current_pagination_page = 1;
let current_pagination_demo_page = 1;
let load_new_lots = false;
let load_new_demo_sets = false;
let prepare_data_set = null;
let slot_to_load_image = null;
let last_clicked_slot = null;
function loadTopics(param = '') {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", location.origin + "/main/topics/" + param, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            document.getElementById('content').innerHTML = json;
        }
    };
    xhr.send();
}

function loadPersonalAccountContent(param = '') {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", location.origin + "/personal-account-ajax.php/" + param, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            document.getElementById('personal_account_content').innerHTML = json;
        }
    };
    xhr.send();
}

function updateSetsList(page = 1, bg_update = false, full_replace = true, demo = false) {
    if (!bg_update) {
        prepare_data_set = null;
    }
    let filter_cost = document.getElementById('trade_filter__cost');
    let filter_photos = document.getElementById('trade_filter__photos');
    let filter_purchasable = document.getElementById('trade_filter__purchasable');
    let filter_search = document.getElementById('trade_filter__search');
    if (!filter_cost || !filter_photos || !filter_purchasable || !filter_search) {
        return;
    }
    if(demo) {
        current_pagination_demo_page = page;
    } else {
        current_pagination_page = page;
    }
    validateFilters();
    let form_data = new FormData();
    form_data.append('cost', filter_cost.value);
    form_data.append('photos', filter_photos.value);
    form_data.append('purchasable', filter_purchasable.value);
    form_data.append('hashtag', filter_search.value);
    form_data.append('page', page);
    form_data.append('update_all', Number(bg_update));
    form_data.append('demo', demo ? 1 : 0);
    form_data.append('action', 'update_sets');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = json.trim();
            let div = document.createElement('div');
            div.innerHTML = json;
            if(full_replace) {
                if(demo) {
                    document.getElementById('trade-demo-sets__content-cover').replaceWith(div.firstChild);
                } else {
                    document.getElementById('trade-sets__content-cover').replaceWith(div.firstChild);
                }
            } else {
                let container_lots = demo ? div.querySelector('#demo-main_tbody') : div.querySelector('#main_tbody');
                if(container_lots.getElementsByTagName('tr').length <= 0) {
                    if(demo) {
                        current_pagination_demo_page--;
                    } else {
                        current_pagination_page--;
                    }
                }
                if(demo) {
                    $(container_lots).contents().appendTo('#demo-main_tbody');
                    load_new_demo_sets = false;
                } else {
                    $(container_lots).contents().appendTo('#main_tbody');
                    load_new_lots = false;
                }
            }
        }
    };
    xhr.send(form_data);
}

function updateCreateSetButton() {
    let filter_cost = document.getElementById('trade_filter__cost');
    let filter_photos = document.getElementById('trade_filter__photos');
    let filter_purchasable = document.getElementById('trade_filter__purchasable');
    let filter_search = document.getElementById('trade_filter__search');
    let cost = filter_cost.value;
    let photos = filter_photos.value;
    let purchasable = filter_purchasable.value;
    let hashtag = filter_search.value;
    let form_data = new FormData();
    form_data.append('cost', cost);
    form_data.append('photos', photos);
    form_data.append('purchasable', purchasable);
    form_data.append('hashtag', hashtag);
    form_data.append('action', 'update_create_button');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let data = JSON.parse(json);
            let btn = document.getElementById('create-set__button');
            if(Boolean(data.is_all_params)) {
                $('#create-set__button').removeClass('create-set__button');
                $('#create-set__button').addClass('create-set__button-ready');
            } else {
                $('#create-set__button').removeClass('create-set__button-ready');
                $('#create-set__button').addClass('create-set__button');
            }
            if(Boolean(data.is_user_in_set)) {
                addErrorMessage(btn.parentNode, data.lab, true);
            }
            let params = json;
            if(btn) {
                btn.setAttribute('onclick', 'showSet(' + params + ')');
            }
            let income = document.getElementById('profit-block-count-span-income');
            income.innerText = Number(data.profit_usdt) != 0 ? (Number(data.cost) + Number(data.profit_usdt)) + " " + fs['main_currency'] : 0 + " " + fs['main_currency'];
            let profit = document.getElementById('profit-block-count-span-usdt');
            profit.innerText = data.profit_usdt + " " + fs['main_currency'];
            let profitperc = document.getElementById('profit-block-count-span-perc');
            profitperc.innerText = data.profit_percent + " %";
            let incomeMobile = document.getElementById('profit-block-count-span-income-mobile');
            incomeMobile.innerText = Number(data.profit_usdt) != 0 ? (Number(data.cost) + Number(data.profit_usdt)) + " " + fs['main_currency'] : 0 + " " + fs['main_currency'];
            let profitMobile = document.getElementById('profit-block-count-span-usdt-mobile');
            profitMobile.innerText = data.profit_usdt + " " + fs['main_currency'];
            let profitpercMobile = document.getElementById('profit-block-count-span-perc-mobile');
            profitpercMobile.innerText = data.profit_percent + " %";
        }
    };
    xhr.send(form_data);
}

function checkSet(set, elem) {
    let form_data = new FormData();
    form_data.append('cost', set.cost);
    form_data.append('photos', set.total_photos);
    form_data.append('purchasable', set.pur_photos);
    form_data.append('hashtag', '');
    form_data.append('action', 'update_create_button');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let data = JSON.parse(json);
            console.log('data.is_user_in_set=',data.is_user_in_set);
            if(Boolean(data.is_user_in_set)) {
                addErrorMessage(elem, data.lab, true);
            } else {
                set.click_on_set = true;
                showSet(set);
            }

        }
    };
    xhr.send(form_data);
}

function checkValidPhotos(field_input) {
    if (field_input.value === '') {
        $('#' + field_input.id).removeClass("trade_modal__error");
        return;
    }
    if (Number(field_input.value) < 2) {
        $('#' + field_input.id).addClass("trade_modal__error");
        field_input.value = '';
        addErrorMessage(field_input.parentNode, fs['must be more than 1']);
    } else {
        $('#' + field_input.id).removeClass("trade_modal__error");
    }
}

function checkValidTime(field_input) {
    if (Number(field_input.value) < 1 || Number(field_input.value) > 168) {
        $('#' + field_input.id).removeClass("trade_modal__error");
        field_input.value = '1';
        addErrorMessage(field_input.parentNode, fs['time must be from to']);
    }
}

function validateCost(field_input) {
    if (field_input.value === '') {
        $('#' + field_input.id).removeClass("trade_modal__error");
        return;
    }
    if (Number(field_input.value) < 1) {
        $('#' + field_input.id).addClass("trade_modal__error");
        field_input.value = '';
        addErrorMessage(field_input.parentNode, fs['must be more than 0'], true);
    } else {
        $('#' + field_input.id).removeClass("trade_modal__error");
    }
}

function validatePhotos(field_input) {
    if (field_input.value === '') {
        $('#' + field_input.id).removeClass("trade_modal__error");
        return;
    }
    if (Number(field_input.value) < 1) {
        $('#' + field_input.id).addClass("trade_modal__error");
        field_input.value = '';
        addErrorMessage(field_input.parentNode, fs['must be more than 0'], true);
    } else {
        $('#' + field_input.id).removeClass("trade_modal__error");
    }
}

function validatePurchasable(field_input, filter_photos) {
    if (field_input.value === '') {
        $('#' + field_input.id).removeClass("trade_modal__error");
        return;
    }
    if (Number(field_input.value) < 1) {
        $('#' + field_input.id).addClass("trade_modal__error");
        field_input.value = '';
        addErrorMessage(field_input.parentNode, fs['must be more than 0'], true);
    } else if (filter_photos.value !== '' && Number(field_input.value) >= Number(filter_photos.value)) {
        $('#' + field_input.id).addClass("trade_modal__error");
        field_input.value = '';
        addErrorMessage(field_input.parentNode, fs['must be less than total photos'] + ' - ' + filter_photos.value, true);
    } else {
        $('#' + field_input.id).removeClass("trade_modal__error");
    }
}

function validateTime(field_input) {
    if (field_input.value === '') {
        $('#' + field_input.id).removeClass("trade_modal__error");
        return;
    }
    if (Number(field_input.value) < 1 || Number(field_input.value) > 168) {
        $('#' + field_input.id).addClass("trade_modal__error");
        addErrorMessage(field_input.parentNode, fs[''], true);
    } else {
        $('#' + field_input.id).removeClass("trade_modal__error");
    }
}

function validateFilters() {
    let filter_cost = document.getElementById('trade_filter__cost');
    let filter_photos = document.getElementById('trade_filter__photos');
    let filter_purchasable = document.getElementById('trade_filter__purchasable');
    validateCost(filter_cost);
    validatePhotos(filter_photos);
    validatePurchasable(filter_purchasable, filter_photos);
}


function showUploadImage(slot) {
    if(slot_to_load_image != null) {
        console.log('disable');
        return;
    }
    last_clicked_slot = slot;
    $('#trade_slider__upload_file').show();
    $('#trade_slider__upload_file').focus();
    $('#trade_slider__upload_file').click();
    $('#trade_slider__upload_file').hide();
}

function notLogin() {
    document.location.href = '/login.php';
}

function updateBalanceInHeader() {
    let form_data = new FormData();
    form_data.append('action', 'get_balance');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                let balance = document.getElementById('header-balance-span');
                balance.innerText = json.balance;
            }
        }
    };
    xhr.send(form_data);
}