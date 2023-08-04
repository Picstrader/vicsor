if (window.location.href.includes('/?') || window.location.href.includes('index.php')) {
    document.addEventListener('scroll', scrollUpdateGallery);
}

function scrollUpdateGallery(e) {
    let limit = Math.max(document.body.scrollHeight, document.body.offsetHeight,
        document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight);
    if (document.body.offsetHeight + window.scrollY >= limit*0.75) {
        document.removeEventListener('scroll', scrollUpdateGallery);
        let page = Number(document.querySelector('#gallery-images__section').getAttribute('data-page')) + 1;
        updateGalleryList(page);
    }
}

function updateGalleryList(page = 1) {
    let hash = document.getElementById('gallery-search');
    let form_data = new FormData();
    form_data.append('page', page);
    form_data.append('action', 'update_gallery');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/index-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.addEventListener('scroll', scrollUpdateGallery);
            let div = document.createElement('div');
            div.innerHTML = xhr.responseText;
            let container_gallery = div.querySelector('.gallery-images__images-cover');
            if (!container_gallery || container_gallery.getElementsByClassName('gallery-images__image-data').length <= 0) {
                document.querySelector('#gallery-images__section').setAttribute('data-page', page - 1);
            } else {
                document.querySelector('#gallery-images__section').setAttribute('data-page', page);
                $(container_gallery).contents().appendTo('.gallery-images__images-cover');
            }
        }
    };
    xhr.send(form_data);
}

function openBuyImage(elem) {
    let image_id = elem.getAttribute('data-image');
    let watermark = elem.getAttribute('data-watermark');
    document.querySelector('#paypal_method').setAttribute('onclick', `tryBuyImage(${image_id})`);
    //document.querySelector('#wayforpay_method').setAttribute('onclick', `openWayforpay(${image_id})`);
    //document.querySelector('.img-zoom-galery-modal').setAttribute('onclick', `addToCart(${image_id})`);
    document.querySelector(".modal-content-gallery-error").style.backgroundImage = `url(/inc/assets/img/${watermark})`;
    document.querySelector('#modalMainModalBuyImage').style.display = 'block';
    // let form_data = new FormData();
    // form_data.append('image_id', elem.getAttribute('data-image'));
    // form_data.append('action', 'image_hashtags');
    // let xhr = new XMLHttpRequest();
    // xhr.open("POST", location.origin + "/gallery-ajax.php", true);
    // xhr.onreadystatechange = function () {
    //     if (xhr.readyState === 4 && xhr.status === 200) {
    //         let data = JSON.parse(xhr.responseText);
    //         if (image_id) {
    //             document.querySelector('#paypal_method').setAttribute('onclick', `openTopupModal(${image_id})`);
    //             document.querySelector('#wayforpay_method').setAttribute('onclick', `openWayforpay(${image_id})`);
    //             document.querySelector('.img-zoom-galery-modal').setAttribute('onclick', `addToCart(${image_id})`);
    //             document.querySelector(".modal-content-gallery-error").style.backgroundImage = `url(/inc/assets/img/${watermark})`;
    //             data.hashtags.forEach((hashtag, index, arr) => {
    //                 arr[index] = `<a style='color:#fff' href='/index.php?hashtag=${encodeURIComponent(hashtag)}'>${hashtag}</a>`
    //             });
    //             document.querySelector('.modal-hashtag').innerHTML = data.hashtags.join(' , ');
    //         }
    //         gallery_modal_alert_buy_image.style.display = "block";
    //     }
    // };
    // xhr.send(form_data);
}

function openTopupModal(image_id) {
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('action', 'check_image');
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
                initPayPalButton(amount.toFixed(2), image_id, prepareCheck);
                let fee = document.getElementById('topup-fee');
                if (fee) {
                    calculateTopupFee();
                }
                let email = document.querySelector('#email');
                if (email) {
                    email.value = '';
                    email.readOnly = false;
                }
                personal_account_modal_topup_trade.style.display = "block";
                document.addEventListener("mouseup", closeTopupModal);
            }
        }
    };
    xhr.send(form_data);
}

let prepareCheck = function (transaction_id) {
    document.cookie = "cart=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
    // searchAnimation();
    // search_interval = setInterval(() => { checkSuccessPayPal(transaction_id) }, 7000);
    //gtag('event', 'conversion', { 'send_to': 'AW-11226460940/DYrVCJXN-60YEIzmmOkp', 'transaction_id': '' }); 
}

function tryBuyImage(image_id) {
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('action', 'try_buy_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/index-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText);
            let json = JSON.parse(xhr.responseText);
            if (json.status) {
                showModalSuccess();
            } else {
                if(json.error_type == 'not_login') {
                    window.location.href = '/login.php';
                } else {
                    openTopupModal(image_id);
                }
            }
            document.querySelector('#modalMainModalBuyImage').style.display = 'none';
        }
    };
    xhr.send(form_data);
}