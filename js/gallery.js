let modal_gallery = document.getElementById("modalGallery");
let modal_gallery_result = document.getElementById("modalGalleryResult");
let modal_gallery_success_subscription = document.getElementById("modalGallerySuccessSubscription");
let opened_image = null;
if (modal_gallery != null) {
    window.onclick = function (event) {
        if (event.target == modal_gallery) {
            modal_gallery.style.display = "none";
        }
        if (event.target == modal_gallery_result) {
            hideGalleryModelResult();
            //modal_gallery_result.style.display = "none";
        }
        if (event.target == modal_gallery_success_subscription) {
            hideGallerySuccessSubscription();
        }
    }
}
try {
    if (shared_link) {
        openImageLink(shared_link);
        shared_link = null;
    }
} catch (error) {

}
function openImage(img) {
    opened_image = img;
    img_data = JSON.parse(img.getAttribute('data-image'));
    let form_data = new FormData();
    form_data.append('image_id', img_data.image_id);
    form_data.append('action', 'open_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/gallery-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            console.log(json);
            let div = document.createElement('div');
            div.innerHTML = json;
            document.getElementById('modalGallery').replaceWith(div.firstChild);
            modal_gallery = document.getElementById("modalGallery");
            showGalleryModal();
        }
    };
    xhr.send(form_data);
}

function openImageLink(image_id) {
    if (!Number(image_id)) {
        return;
    }
    let gallery_images = document.getElementsByClassName('gallery-images__image');
    gallery_images = [...gallery_images];
    gallery_images.find((image_item) => {
        img_data = JSON.parse(image_item.getAttribute('data-image'));
        if (img_data.image_id == image_id) {
            opened_image = img_data;
            return true;
        } else {
            return false;
        }
    });
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('action', 'open_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/gallery-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let div = document.createElement('div');
            div.innerHTML = json;
            document.getElementById('modalGallery').replaceWith(div.firstChild);
            modal_gallery = document.getElementById("modalGallery");
            showGalleryModal();
        }
    };
    xhr.send(form_data);
}

function showGalleryModal() {
    modal_gallery.style.display = "block";
}

function hideGalleryModel() {
    modal_gallery.style.display = "none";
}

function showGallerySuccessSubscription(message = '') {
    let txt = document.getElementById('gallery-modal-success-subscription__text');
    txt.innerText = message;
    modal_gallery_success_subscription.style.display = "block";
}

function hideGallerySuccessSubscription() {
    modal_gallery_success_subscription.style.display = "none";
    showGalleryModal();
}

function showGalleryModalResult(message = '', success = true) {
    let txt = document.getElementById('gallery-modal-result__text');
    txt.innerText = message;
    let sub_block = document.querySelector('.gallery-images__modal-sub-container');
    if (success) {
        sub_block.style.display = 'none';
    } else {
        sub_block.style.display = /*'flex'*/'none';
    }
    modal_gallery_result.style.display = "block";
}

function hideGalleryModelResult() {
    modal_gallery_result.style.display = "none";
    hideModalGalleryAlertBuyImage();
    showGalleryModal();
}

function buyImage(image_id) {
    if (image_id === null) {
        window.location.href = '/login.php';
        return;
    }
    let form_data = new FormData();
    form_data.append('image_id', image_id);
    form_data.append('action', 'buy_image');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/gallery-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('bought');
            let json = xhr.responseText;
            console.log(json);
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                let download_image = document.getElementById('download_image');
                download_image.href = './inc/assets/img/' + json.image;
                download_image.click();
                download_image.href = '';
                hideModalGalleryAlertBuyImage();

                updateBalanceInHeader();
            } else {
                hideGalleryModel();
                showGalleryModalResult(json.message, false);
            }
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            console.log('error');
            let json = xhr.responseText;
            console.log(json);
            hideGalleryModel();
            showGalleryModalResult(fs['not enough money'], true);
        }
    };
    xhr.send(form_data);
}

function showConfirmBuyImage(element) {
    let buy_button = document.getElementById('confirm_buy_image');
    buy_button.setAttribute('onclick', 'buyImage(' + element.getAttribute('data-image') + ')');
    let buy_text = document.getElementById('confirm_buy_image_text');
    buy_text.innerHTML = fs["You sublicense this photo for"] + ' ' + element.getAttribute('data-price') + ' ' + fs['main_currency'];
    showModalGalleryAlertBuyImage();
}

function addToFavorite(gallery_image_id) {
    console.log('add f');
    let form_data = new FormData();
    form_data.append('gallery_image_id', gallery_image_id);
    form_data.append('action', 'add_favorite');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/gallery-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                updateFavoriteTemplate();
                let button = document.getElementById('favorite_button');
                button.style = json.style;
                button.setAttribute('onclick', "removeFromFavorite(" + gallery_image_id + ")");
                if (opened_image !== null) {
                    let favorite_icon_container = opened_image.parentNode.querySelector('.gallery-images__profit');
                    if (favorite_icon_container) {
                        let favorite_icon = document.createElement('img');
                        favorite_icon.className = 'favorite-icon-small';
                        favorite_icon_container.append(favorite_icon);
                    }
                }
            }
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            console.log('error');
            let json = xhr.responseText;
            console.log(json);
        }
    };
    xhr.send(form_data);
}

function removeFromFavorite(gallery_image_id) {
    console.log('remove f');
    let form_data = new FormData();
    form_data.append('gallery_image_id', gallery_image_id);
    form_data.append('action', 'remove_favorite');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/gallery-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                updateFavoriteTemplate();
                let button = document.getElementById('favorite_button');
                button.style = json.style;
                button.setAttribute('onclick', "addToFavorite(" + gallery_image_id + ")");
                if (opened_image !== null) {
                    let favorite_icon = opened_image.parentNode.querySelector('.favorite-icon-small');
                    if (favorite_icon) {
                        favorite_icon.remove();
                    }
                }
            }
        } else if (xhr.readyState === 4 && xhr.status === 400) {
            console.log('error');
            let json = xhr.responseText;
            console.log(json);
        }
    };
    xhr.send(form_data);
}

function shareButton() {
    let shareButtonBlock = document.getElementById("shareButtonBlock");
    console.log(shareButtonBlock.style);
    if (shareButtonBlock.style.display == "none") {
        shareButtonBlock.style.display = "flex";
    } else if (shareButtonBlock.style.display == "") {
        shareButtonBlock.style.display = "flex";
    } else {
        shareButtonBlock.style.display = "none";
    }

}

function copySharedLink() {
    let copyText = document.getElementById("shared_link");
    copyText.select();
    navigator.clipboard.writeText(copyText.value);
}

function shareTwitter() {
    let copyText = document.getElementById("shared_link");
    window.open("https://twitter.com/intent/tweet?url=" + copyText.value, '_blank');
}

function shareFacebook() {
    let copyText = document.getElementById("shared_link");
    window.open("https://www.facebook.com/sharer.php?u=" + copyText.value, '_blank');
}

function shareTelegram() {
    let copyText = document.getElementById("shared_link");
    window.open("https://t.me/share/url?url=" + copyText.value, '_blank');
}

function shareWhatsApp() {
    let copyText = document.getElementById("shared_link");
    window.open("https://wa.me/?text=" + copyText.value, '_blank');
}
