let modal_data = {
    image: {},
    set: {}
}
var splide;
let global_this = this;
let modal = document.getElementById("myModal");
let modal_finish = document.getElementById("myModal2");
let modal_error = document.getElementById("modalError");
let modal_view = document.getElementById("modalView");
let modal_success_subscription = document.getElementById("modalSuccessSubscription");
if (modal != null && modal_finish != null) {
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        } else if (event.target == modal_finish) {
            hideModalFinish();
        } else if (event.target == modal_error) {
            hideModalErrorBig();
            //modal_error.style.display = "none";
        } else if (event.target == modal_view) {
            modal_view.style.display = "none";
        } else if (event.target == modal_success_subscription) {
            hideModalSuccessSubscription();
        }
    }
    selectImageEvent();
}
function sliderInitView() {
    if (splide != null) {
        splide.destroy();
        splide = null;
    }
    /*slider*/
    try {
        splide = new Splide('.splide', {
            pagination: false,
            perPage: 6,
            perMove: 1,
            rewind: true,
            gap: 133,
            type: 'slide',
            breakpoints: {
                680: { perPage: 2 },
                440: { perPage: 1 },
            }
        });
        var Components = splide.Components;
        var bar = splide.root.querySelector('.my-carousel-progress-bar');

        // Updates the bar width whenever the carousel moves:
        splide.on('mounted move', function () {
            var end = splide.Components.Controller.getEnd() + 1;
            var rate = Math.min((splide.index + 1) / end, 1);
            bar.style.width = String(100 * rate) + '%';
        });
        splide.on('resized', function () {
            var isOverflow = Components.Layout.isOverflow();
            var list = Components.Elements.list;
            var lastSlide = Components.Slides.getAt(splide.length - 1);

            if (lastSlide && list.childNodes.length < 6) {
                // Toggles `justify-content: center`
                list.style.justifyContent = isOverflow ? '' : 'center';
            }
        });

        splide.mount();
    } catch (error) {
        console.log('no slider on page');
    }
}

function uploadImage() {
    slot_to_load_image = last_clicked_slot;
    let interval = setInterval(function () { rotateAnimation(slot_to_load_image.querySelector('.trade_gallery__empty_section-ellipse-add')) }, 200);
    let load_text = slot_to_load_image.querySelector('.trade_gallery__no-image span');
    if (load_text) {
        load_text.innerHTML = 'Uploading...'
    }
    let selected_file = document.querySelector('#trade_slider__upload_file');
    let file = selected_file.files[0];
    let form_data = new FormData();
    form_data.append('fileToUpload', document.querySelector('#trade_slider__upload_file').files[0]);
    form_data.append('action', 'upload');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let div = document.createElement('div');
            div.innerHTML = json;
            document.getElementById('trade_gallery').replaceWith(div.firstChild);
            modal_data.image = {};
            selectImageEvent();
            clearInterval(interval);
            slot_to_load_image = null;
            try {
                if (go_tutorial) {
                    tour.hide();
                    tour.show();
                }
            } catch (error) {

            }
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            addErrorMessage(slot_to_load_image.querySelector('.trade_gallery__empty_section'), json.message, true);
            clearInterval(interval);
            slot_to_load_image = null;
            try {
                if (go_tutorial) {
                    tour.hide();
                    tour.show();
                }
            } catch (error) {

            }
        }
    };
    xhr.send(form_data);
}

function deleteImage(id, status) {
    if (status === 'trading') return;
    let form_data = new FormData();
    form_data.append('id', id);
    form_data.append('status', status);
    form_data.append('action', 'delete');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let div = document.createElement('div');
            div.innerHTML = json;
            document.getElementById('trade_gallery').replaceWith(div.firstChild);
            modal_data.image = {};
            selectImageEvent();
        }
    };
    xhr.send(form_data);
}

function isMobile() {
    // let touchDevice = (navigator.maxTouchPoints || 'ontouchend' in document.documentElement);
    // return Boolean(touchDevice);
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    const isTouchScreen = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0);
    if (isMobile && isTouchScreen) {
        return true;
    } else if (!isMobile && isTouchScreen) {
        return false;
    } else if (!isMobile && !isTouchScreen) {
        return false;
    }
    return false;
}

function iOS() {
    return [
        'iPad Simulator',
        'iPhone Simulator',
        'iPod Simulator',
        'iPad',
        'iPhone',
        'iPod'
    ].includes(navigator.platform)
        // iPad on iOS 13 detection
        || (navigator.userAgent.includes("Mac") && "ontouchend" in document)
}

let touchStart = false;
touchTime = new Date();
function selectImageEvent() {
    let touchDevice = (navigator.maxTouchPoints || 'ontouchend' in document.documentElement);
    if (Boolean(touchDevice)) {
        $(window).on('touchstart', function (e) {
            touchStart = true;
            touchTime = new Date();
        });
        if (!iOS()) {
            $(window).on('touchend', function (e) {
                touchStart = false;
            });
        }
    }
    $(".trade_gallery__section_foto").on(iOS() ? 'touchend' : 'mouseup', function (event) {
        if (iOS()) {
            if (Boolean(touchDevice)) {
                var diff = new Date() - touchTime;
                if (diff > 100) {
                    return;
                }
            }
        } else {
            if (/*Boolean(touchDevice)*/touchStart) {
                var diff = new Date() - touchTime;
                if (diff > 100) {
                    return;
                }
            }
        }
        if (event.target.tagName != 'IMG' && event.target.tagName != 'A') {
            let image = $(event.currentTarget).data('image');
            if (image.status != 'ready') {
                switch (image.status) {
                    case 'trading':
                        showModalError(fs['image is already in set'], $('.selected' + image.id).first());
                        break;
                    case 'moderating':
                        showModalError(fs['image is currently being moderated'], $('.selected' + image.id).first());
                        break;
                    default:
                        showModalError(fs['image is not available now'], $('.selected' + image.id).first());
                        break;
                }
                return;
            }
            $('.trade_gallery__selected').removeClass('trade_gallery__selected');
            $('.trade_gallery__section_foto').removeClass("trade_modal__error");
            $('.trade_gallery__empty_section').removeClass("trade_modal__error");
            if (modal_data.image.id == image.id) {
                modal_data.image = {};
            } else {
                try {
                    if (go_tutorial) {
                        tour.next();
                    }
                } catch (error) {

                }
                $('.selected' + image.id).addClass('trade_gallery__selected');
                modal_data.image = image;
                if (prepare_data_set !== null) {
                    showSet(prepare_data_set.set);
                    prepare_data_set = null;
                }
            }
        }
    });
}

function showSet(set = null) {
    forceRemove();
    if (set === null) return;
    if (Boolean(set.is_user_in_set)) {
        let btn = document.getElementById('create-set__button');
        addErrorMessage(btn.parentNode, set.lab);
        return;
    }
    modal_data.set = set;
    if (SetValidation()) {
        fillParams();
        showModal();
        try {
            if (go_tutorial) {
                tour.hide();
                tour.show(5);
            }
        } catch (error) {

        }
    }
}

function viewSet(set) {
    if (set.images_name.length >= set.total_photos) {
        modal_data.set = set;
        fillParamsView();
        showModalView();
    }
}

function triggerViewSet(set_id) {
    let trigged_button = document.querySelector('.triggerViewSet' + set_id);
    if (trigged_button) {
        trigged_button.click();
    }
}

function getReadyImages() {
    let image = null;
    let images = $('.trade_gallery__section_foto');
    for (let i = 0; i < images.length; i++) {
        let attr = $(images[i]).attr("data-image");
        attr = JSON.parse(attr);
        console.log('attr ', attr.status);
        if (attr.status == 'ready') {
            $(images[i]).addClass("trade_modal__error");
            image = images[i];
            addErrorMessage(image, fs['image not selected'], modal_data.set.click_on_set ? true : false);
        }
    }
    if (image !== null) {
        document.getElementById("trade_gallery").scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    images = $('.trade_gallery__empty_section');
    for (let i = 0; i < images.length; i++) {
        image = images[i];
        addErrorMessage(image, fs['download new photo'], modal_data.set.click_on_set ? true : false);
    }
    if (image !== null) {
        $('.trade_gallery__empty_section').addClass("trade_modal__error");
        document.getElementById("trade_gallery").scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    showModalErrorBig(fs["There are no ready-made images"], true);
}

function SetValidation() {
    if (!modal_data.image.id) {
        getReadyImages();
        prepare_data_set = Object.assign(modal_data);
        return false;
    }
    if (Number(modal_data.set.cost) < 1) {
        console.log('cost error');
        $('#trade_filter__cost').addClass("trade_modal__error");
        document.getElementById("trade_filter__cost").scrollIntoView({ behavior: 'smooth', block: 'center' });
        addErrorMessage($('#trade_filter__cost').parent(), fs['must be more than 0'], modal_data.set.click_on_set ? true : false);
        return false;
    }
    if (Number(modal_data.set.total_photos) < 2) {
        $('#trade_filter__photos').addClass("trade_modal__error");
        document.getElementById("trade_filter__photos").scrollIntoView({ behavior: 'smooth', block: 'center' });
        addErrorMessage($('#trade_filter__photos').parent(), fs['must be more than 1'], modal_data.set.click_on_set ? true : false);
        return false;
    }
    if (Number(modal_data.set.pur_photos) < 1) {
        $('#trade_filter__purchasable').addClass("trade_modal__error");
        document.getElementById("trade_filter__purchasable").scrollIntoView({ behavior: 'smooth', block: 'center' });
        addErrorMessage($('#trade_filter__purchasable').parent(), fs['must be more than 0'], modal_data.set.click_on_set ? true : false);
        return false;
    }
    if (Number(modal_data.set.pur_photos) >= Number(modal_data.set.total_photos)) {
        $('#trade_filter__purchasable').addClass("trade_modal__error");
        document.getElementById("trade_filter__purchasable").scrollIntoView({ behavior: 'smooth', block: 'center' });
        addErrorMessage($('#trade_filter__purchasable').parent(), fs['must be less than total photos'] + ' - ' + modal_data.set.total_photos, modal_data.set.click_on_set ? true : false);
        return false;
    }
    return true;
}

function showModal() {
    $(".chosen-select").chosen("destroy");
    $(".chosen-select").html('');
    chosenInit();
    $('#trade_modal__cost').removeClass("trade_modal__error");
    $('#trade_modal__photos').removeClass("trade_modal__error");
    $('#trade_modal__purchasable').removeClass("trade_modal__error");
    $('#trade_modal__time').removeClass("trade_modal__error");
    modal.style.display = "block";
}

function showModalAlreadyConfirm() {
    modal.style.display = "block";
}

function showModalView() {
    modal_view.style.display = "block";
}

function showModalErrorBig(message, not_show_subscription = false) {
    let txt = document.getElementById('trade-modal-error-big__text');
    txt.innerText = message;
    if (not_show_subscription) {
        let sub_block = document.querySelector('.gallery-images__modal-sub-container');
        sub_block.style.display = 'none';
    } else {
        let sub_block = document.querySelector('.gallery-images__modal-sub-container');
        sub_block.style.display = /*'flex'*/'none';
    }
    modal_error.style.display = "block";
}

function hideModalErrorBig() {
    modal_error.style.display = "none";
    showModal();
}

function showModalSuccessSubscription(message = '') {
    let txt = document.getElementById('trade-modal-success-subscription__text');
    txt.innerText = message;
    modal_success_subscription.style.display = "block";
}

function hideModalSuccessSubscription() {
    modal_success_subscription.style.display = "none";
    showModalAlreadyConfirm();
}

function showModalFinish() {
    modal_finish.style.display = "block";
}

function showModalError(error_message, jquery_element, not_click = false) {
    addErrorMessage(jquery_element, error_message, not_click = false);
}

function hideModal() {
    modal.style.display = "none";
}

function hideModalView() {
    modal_view.style.display = "none";
}

function hideModalFinish() {
    modal_data.image = {};
    modal_finish.style.display = "none";
    reloadCurrentPage();
}

function getOffset(el) {
    const rect = el.getBoundingClientRect();
    return {
        left: rect.left + window.scrollX,
        top: rect.top + window.scrollY
    };
}

function getTimeTemplate(seconds_amount) {
    let hours_amount = Math.trunc(Number(seconds_amount) / (60 * 60));
    let minutes_amount = Math.trunc(Number(seconds_amount) / 60);
    let days = Math.trunc(hours_amount / 24);
    if (days >= 1) {
        let time_template = {
            input_value: days,
            title_value: 'Days'
        }
        return time_template;
    }
    let hours = hours_amount % 24;
    if (hours >= 1) {
        let time_template = {
            input_value: hours,
            title_value: 'Hours'
        }
        return time_template;
    }
    let minutes = minutes_amount;
    if (minutes >= 1) {
        let time_template = {
            input_value: minutes,
            title_value: 'Minutes'
        }
        return time_template;
    }
    let seconds = seconds_amount;
    if (seconds >= 1) {
        let time_template = {
            input_value: seconds,
            title_value: 'Seconds'
        }
        return time_template;
    }
    let time_template = {
        input_value: 0,
        title_value: ''
    }
    return time_template;
}

function fillParams() {
    let cost = document.getElementById('trade_modal__cost');
    let photos = document.getElementById('trade_modal__photos');
    let purchasable = document.getElementById('trade_modal__purchasable');
    let time = document.getElementById('trade_modal__time');
    let time_title = document.getElementById('trade_modal__time_title');
    let image = document.getElementById('trade_modal__foto');
    let profit = document.getElementById('trade_modal__profit');
    let profit_usdt = document.getElementById('trade_modal__profit_usdt');
    purchasable.max = Number(photos.value) > 0 ? photos.value : '';
    cost.value = modal_data.set.cost;
    photos.value = modal_data.set.total_photos;
    purchasable.value = modal_data.set.pur_photos;
    let time_template = getTimeTemplate(modal_data.set.time);
    modal_data.set.time = Number(modal_data.set.time) == 0 ? 1 : Number(modal_data.set.time) / (60 * 60);
    time.value = Number(modal_data.set.time) == 0 ? 1 : modal_data.set.time;
    if (modal_data.set.id !== undefined) {
        time.value = time_template.input_value;
        time_title.innerText = time_template.title_value;
        cost.readOnly = true;
        photos.readOnly = true;
        purchasable.readOnly = true;
        time.disabled = true;
    } else {
        cost.readOnly = false;
        photos.readOnly = false;
        purchasable.readOnly = false;
        time.disabled = false;
    }
    profit.innerHTML = Number(modal_data.set.profit_percent).toFixed(2) + '%';
    profit_usdt.innerHTML = '+' + Number(modal_data.set.profit_usdt).toFixed(2) + ' ' + fs['main_currency'];
    image.style = "background-image: url(./inc/assets/img/" + modal_data.image.name + ");";
}

function fillParamsView() {
    let cost = document.getElementById('trade_modal__cost_view');
    let photos = document.getElementById('trade_modal__photos_view');
    let purchasable = document.getElementById('trade_modal__purchasable_view');
    let time = document.getElementById('trade_modal__time_view');
    let profit = document.getElementById('trade_modal__profit_view');
    let profit_usdt = document.getElementById('trade_modal__profit_usdt_view');
    let set_id = document.getElementById('modal-view-set-id');
    set_id.innerText = "Set ID: " + modal_data.set.id;
    cost.value = modal_data.set.cost;
    photos.value = modal_data.set.total_photos;
    purchasable.value = modal_data.set.pur_photos;
    time.value = Number(modal_data.set.time) == 0 ? 1 : modal_data.set.time;
    if (modal_data.set.id !== undefined) {
        cost.readOnly = true;
        photos.readOnly = true;
        purchasable.readOnly = true;
        time.disabled = true;
    } else {
        cost.readOnly = false;
        photos.readOnly = false;
        purchasable.readOnly = false;
        time.disabled = false;
    }
    profit.innerHTML = Number(modal_data.set.profit_percent).toFixed(2) + '%';
    profit_usdt.innerHTML = '+' + Number(modal_data.set.profit_usdt).toFixed(2) + ' ' + fs['main_currency'];
    document.querySelector('.splide__list').innerHTML = '';
    modal_data.set.images_name.forEach((image) => {
        let li = document.createElement('li');
        li.className = 'splide__slide';
        let div = document.createElement('div');
        div.className = 'trade_modal__section_foto_inner';
        div.style = "background-image: url(./inc/assets/img/" + image.name + ");";
        li.append(div);
        document.querySelector('.splide__list').append(li);
    });
    sliderInitView();
}

function validateFiltersModal() {
    let cost = document.getElementById('trade_modal__cost');
    let photos = document.getElementById('trade_modal__photos');
    let purchasable = document.getElementById('trade_modal__purchasable');
    let time = document.getElementById('trade_modal__time');
    validateCost(cost);
    validatePhotos(photos);
    validatePurchasable(purchasable, photos);
    validateTime(time);
}

function updateParams(input) {
    let cost = document.getElementById('trade_modal__cost');
    let photos = document.getElementById('trade_modal__photos');
    let purchasable = document.getElementById('trade_modal__purchasable');
    let time = document.getElementById('trade_modal__time');
    let profit = document.getElementById('trade_modal__profit');
    let profit_usdt = document.getElementById('trade_modal__profit_usdt');
    validateFiltersModal();
    modal_data.set.cost = cost.value;
    modal_data.set.total_photos = photos.value;
    modal_data.set.pur_photos = purchasable.value;
    modal_data.set.time = time.value;
    modal_data.set.profit_usdt = getProfitUSDT();
    modal_data.set.profit_percent = getProfitPercent();
    profit.innerHTML = modal_data.set.profit_percent + '%';
    profit_usdt.innerHTML = '+' + Number(modal_data.set.profit_usdt).toFixed(2) + ' ' + fs['main_currency'];
    console.log(modal_data);
}

function fillParamsFinish() {
    let cost = document.getElementById('trade_modal_finish__cost');
    let photos = document.getElementById('trade_modal_finish__photos');
    let purchasable = document.getElementById('trade_modal_finish__purchasable');
    let image = document.getElementById('section__foto-src');
    let imageDesc = document.getElementById('section__foto-src-desc');
    let profit = document.getElementById('trade_modal_finish__profit');
    let profit_usdt = document.getElementById('trade_modal_finish__profit_usdt');
    cost.innerHTML = modal_data.set.cost + ' ' + fs['main_currency'];
    photos.innerHTML = modal_data.set.total_photos + ' pics';
    purchasable.innerHTML = modal_data.set.pur_photos + ' pics';
    profit.innerHTML = Number(modal_data.set.profit_percent).toFixed(2) + '%';
    profit_usdt.innerHTML = '+' + Number(modal_data.set.profit_usdt).toFixed(2) + ' ' + fs['main_currency'];
    imageDesc.style.backgroundImage = "url('./inc/assets/img/" + modal_data.image.name + "')";
}

function checkTerms() {
    let checkbox = document.getElementById('trade_modal__privacy_policy');
    let confirm = document.getElementById('trade_modal__confirm');
    if (checkbox.checked) {
        confirm.disabled = false;
    } else {
        confirm.disabled = true;
    }
}

function confirmSet() {
    try {
        if (go_tutorial) {
            tour.hide();
            tour.show(8);
        }
    } catch (error) {

    }
    $('#trade_modal__cost').removeClass("trade_modal__error");
    $('#trade_modal__photos').removeClass("trade_modal__error");
    $('#trade_modal__purchasable').removeClass("trade_modal__error");
    $('#trade_modal__time').removeClass("trade_modal__error");
    modal_data.image.hashtags = $('.chosen-select').val();

    if (!confirmSetValidation()) return;
    let form_data = new FormData();
    console.log(modal_data);
    form_data.append('cost', modal_data.set.cost);
    form_data.append('photos', modal_data.set.total_photos);
    form_data.append('purchasable', modal_data.set.pur_photos);
    form_data.append('image_id', modal_data.image.id);
    form_data.append('hashtags', JSON.stringify(modal_data.image.hashtags));
    form_data.append('time', 24);
    form_data.append('action', 'create_set');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let div = document.createElement('div');
            div.innerHTML = json;
            updateBalanceInHeader();
            fillParamsFinish();
            showModalFinish();
            hideModal();
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            let json = xhr.responseText;
            try {
                json = JSON.parse(json);
                if (!Boolean(json.status)) {
                    showModalErrorBig(json.message);
                }
            } catch (error) {
                console.log(error);
            }
            hideModal();
        } else if (xhr.readyState === 4 && xhr.status === 409) {
            let json = xhr.responseText;
            try {
                json = JSON.parse(json);
                if (!Boolean(json.status)) {
                    //showModalErrorBig(json.message, true);
                    showModalNoMoney();
                    localStorage.setItem('needed_amount', json.message);
                }
            } catch (error) {
                console.log(error);
            }
            hideModal();
        }
    };
    xhr.send(form_data);
}

function updateMySets(background_update = false) {
    if (!document.getElementById('trade-sets__my-content')) {
        return;
    }
    let form_data = new FormData();
    form_data.append('action', 'my_set');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            if (json != '') {
                let div = document.createElement('div');
                div.innerHTML = json;
                document.getElementById('trade-sets__my-content').replaceWith(div.firstChild);
                if (!background_update) updateImages();
            }
        }
    };
    xhr.send(form_data);
}

function updateImages() {
    let form_data = new FormData();
    form_data.append('action', 'images');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let div = document.createElement('div');
            div.innerHTML = json;
            document.getElementById('trade_gallery').replaceWith(div.firstChild);
            selectImageEvent();
            let filter_cost = document.getElementById('trade_filter__cost');
            let filter_photos = document.getElementById('trade_filter__photos');
            let filter_purchasable = document.getElementById('trade_filter__purchasable');
            filter_cost.value = filter_photos.value = filter_purchasable.value = '';
            hideModal();
            fillParamsFinish();
            showModalFinish();
        }
    };
    xhr.send(form_data);
}

function datalistModalManager(datalist, input) {
    datalist.remove();
    input.parentNode.insertBefore(datalist, input.nextSibling);
}

function loadPopularHashtagsFilters(hashtag_field) {
    let search_hash = hashtag_field.value;
    if (search_hash == '') {
        document.querySelector('#trade_modal__popular_hashtags_filters').innerHTML = '';
        return;
    }
    let form_data = new FormData();
    form_data.append('search', search_hash);
    form_data.append('action', 'popular_hashtags');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            hashtags = JSON.parse(json);
            document.querySelector('#trade_modal__popular_hashtags_filters').innerHTML = '';
            hashtags.forEach((hash) => {
                let option = document.createElement('option');
                option.innerText = hash.name;
                document.querySelector('#trade_modal__popular_hashtags_filters').append(option);
            });
        }
    };
    xhr.send(form_data);
}

function confirmSetValidation() {
    if (!Number(modal_data.set.cost) || Number(modal_data.set.cost) < 1) {
        showModalError(fs['must be more than 0'], $('#trade_modal__cost').parent(), true);
        $('#trade_modal__cost').addClass("trade_modal__error");
        return false;
    }
    if (!Number(modal_data.set.total_photos) || Number(modal_data.set.total_photos) < 2) {
        showModalError(fs['must be more than 1'], $('#trade_modal__photos').parent(), true);
        $('#trade_modal__photos').addClass("trade_modal__error");
        return false;
    }
    if (!Number(modal_data.set.pur_photos) || Number(modal_data.set.pur_photos) < 1) {
        showModalError(fs['must be more than 0'], $('#trade_modal__purchasable').parent(), true);
        $('#trade_modal__purchasable').addClass("trade_modal__error");
        return false;
    }
    if (Number(modal_data.set.pur_photos) >= Number(modal_data.set.total_photos)) {
        showModalError(fs['must be less than total photos'] + ' - ' + modal_data.set.total_photos, $('#trade_modal__purchasable').parent(), true);
        $('#trade_modal__purchasable').addClass("trade_modal__error");
        return false;
    }
    return true;
}

function getProfitUSDT() {
    let cost = Math.abs(Number(modal_data.set.cost));
    let total_photos = Math.abs(Number(modal_data.set.total_photos));
    let purchasable_photos = Math.abs(Number(modal_data.set.pur_photos));
    let profit_usdt = 0;
    try {
        if (purchasable_photos != 0) {
            profit_usdt = ((cost * (total_photos - purchasable_photos)) / purchasable_photos);
        } else {
            profit_usdt = 0;
        }
    } catch (e) {
        profit_usdt = 0;
    }
    return profit_usdt.toFixed(2);
}

function getProfitPercent() {
    let cost = Math.abs(Number(modal_data.set.cost));
    let profit_usdt = Math.abs(Number(modal_data.set.profit_usdt));
    let profit_percent = 0;
    try {
        if (cost != 0) {
            profit_percent = (profit_usdt / cost) * 100;
        } else {
            profit_percent = 0;
        }
    } catch (e) {
        profit_percent = 0;
    }
    return profit_percent.toFixed(2);
}


function checkScroll(elem) {
    var gap = 20;
    if (elem.offsetHeight + elem.scrollTop + gap >= elem.scrollHeight) {
        if (load_new_lots) {
            return;
        } else {
            load_new_lots = true;
        }
        current_pagination_page++;
        updateSetsList(current_pagination_page, false, false);
    }
}

function checkDemoScroll(elem) {
    var gap = 20;
    if (elem.offsetHeight + elem.scrollTop + gap >= elem.scrollHeight) {
        if (load_new_demo_sets) {
            return;
        } else {
            load_new_demo_sets = true;
        }
        current_pagination_demo_page++;
        updateSetsList(current_pagination_demo_page, false, false, true);
    }
}

let current_rotation = 0;

function rotateAnimation(elem) {
    if (elem) {
        current_rotation += 20;
        elem.style.transform = 'rotate(' + current_rotation + 'deg)';
    }
}

function showModalUserImages(elem) {
    let form_data = new FormData();
    form_data.append('set_id', elem.getAttribute('data-set'));
    form_data.append('action', 'user_images');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if(document.querySelector('#modalUserImages')) {
                document.querySelector('#modalUserImages').remove();
            }
            document.body.insertAdjacentHTML("beforeend", xhr.responseText);
            document.querySelector('#modalUserImages').style.display = "block";
        }
    };
    xhr.send(form_data);
}

function addToLot(elem) {
    let form_data = new FormData();
    form_data.append('set_id', elem.getAttribute('data-set'));
    form_data.append('image_id', elem.getAttribute('data-image'));
    form_data.append('action', 'add_to_lot');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let data = JSON.parse(xhr.responseText);
            if(data.status) {
                reloadCurrentPage();
            } else if(data.error_type = 'no_money') {
                console.log('no_money');
                openWallpapersTopup(elem.getAttribute('data-set'));
            } else {
                window.location.href = '/login';
            }
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            console.log(xhr.responseText);
        }
    };
    xhr.send(form_data);
}

function likeImage(elem) {
    let image_id = elem.getAttribute('data-image');
    let set_id = elem.getAttribute('data-set');
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('set_id', set_id);
    form_data.append('action', 'like_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        console.log(xhr.responseText);
        let data = JSON.parse(xhr.responseText);
        if(Boolean(data.status)) {
            elem.parentNode.querySelector('.image-likes').innerHTML = data.likes;
        }
      }
    };
    xhr.send(form_data);
}

function showModalChosenImage(elem, image_id) {
    let form_data = new FormData();
    form_data.append('set_id', elem.getAttribute('data-set'));
    form_data.append('image_id', image_id);
    form_data.append('action', 'chosen_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if(document.querySelector('#modalChosenImage')) {
                document.querySelector('#modalChosenImage').remove();
            }
            document.body.insertAdjacentHTML("beforeend", xhr.responseText);
            document.querySelector('#modalChosenImage').style.display = "block";
        }
    };
    xhr.send(form_data);
}

function loadOtherUserImages(type='next') {
    let form_data = new FormData();
    form_data.append('page', document.querySelector('#user-images-cover').getAttribute('data-page'));
    form_data.append('type', type);
    form_data.append('action', 'previous_user_images');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText);
            let div = document.createElement('div');
            div.innerHTML = xhr.responseText.trim();
            if(div.querySelector('.ready-user-image')) {
                document.querySelector('#user-images-cover').replaceWith(div.querySelector('#user-images-cover'));
                document.querySelector('#user-images-buttons').replaceWith(div.querySelector('#user-images-buttons'));
            }
        }
    };
    xhr.send(form_data);
}
//setInterval(function () { rotateAnimation(document.querySelector('.trade_gallery__empty_section-ellipse-add')) }, 200);

//rotateAnimation(document.querySelector('.trade_gallery__empty_section-ellipse-add'));