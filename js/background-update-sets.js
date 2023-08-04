setInterval(() => { backgroundUpdateSets(); updateBalanceTemplate(); updateBackgoundImages(); }, 60000);
function backgroundUpdateSets() {
    console.log('back update');
    updateSetsList(current_pagination_page, true);
    updateSetsList(current_pagination_demo_page, true, true, true);
    updateMySets(true);
}
function updateBalanceTemplate() {
    updateBalanceInHeader();
    updateBalanceOnBalancePage();
}

function updateFavoriteTemplate() {
    let form_data = new FormData();
    form_data.append('action', 'update_favorite');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/gallery-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (Boolean(json.status)) {
                if (Number(json.amount)) {
                    $("#favorite-header").addClass('favorite-header-not-empty');
                    $("#favorite-header-count").text(json.amount);
                    $("#favorite-header-count").show();

                } else {
                    $("#favorite-header").removeClass('favorite-header-not-empty');
                    $("#favorite-header-count").text(json.amount);
                    $("#favorite-header-count").hide();
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

function reloadCurrentPage() {
    window.location.href = document.URL;
}

function updateBackgoundImages() {
    let form_data = new FormData();
    form_data.append('action', 'background_update_images');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            json = JSON.parse(json);
            if (json.status && json.images.length > 0) {
                let images_id = [];
                json.images.forEach(image => {
                    images_id.push(Number(image.id));
                    let image_data = $('.selected' + image.id).data('image');
                    if (image_data) {
                        if (image_data.status != image.status) {
                            image_data.status = image.status;
                            $('.selected' + image.id).data('image', image_data);
                            let status_element = document.querySelector('.selected' + image.id).parentNode.querySelector('.trade_gallery__section-img_description');
                            status_element.innerText = fs[image_data.status];
                        }
                    }
                });
                let slot_nodes = document.getElementsByClassName('trade_gallery__section_foto');
                for (let i = 0; i < slot_nodes.length; i++) {
                    let slot_data = $(slot_nodes[i]).data('image');
                    let check = images_id.indexOf(Number(slot_data.id));
                    if (check < 0) {
                        slot_nodes[i].parentNode.replaceWith(creatyEmptyTradeImageSlot());
                    }
                }
            }
        }
    };
    xhr.send(form_data);
}

function creatyEmptyTradeImageSlot() {
    let div_main = document.createElement('DIV');
    div_main.className = 'trade_gallery__empty_section_cover';
    div_main.setAttribute('onclick', 'showUploadImage(this)');

    let div_ellipse_cover = document.createElement('DIV');
    div_ellipse_cover.className = 'trade_gallery__empty_section';

    let div_ellipse = document.createElement('DIV');
    div_ellipse.className = 'trade_gallery__empty_section-ellipse';

    let image_add = document.createElement('IMG');
    image_add.className = 'trade_gallery__empty_section-ellipse-add';
    image_add.src = './inc/assets/img/trade_slider-add.png';

    let div_noimage = document.createElement('DIV');
    div_noimage.className = 'trade_gallery__no-image';
    let span_upload = document.createElement('SPAN');
    span_upload.innerHTML = fs['Upload a photo'];

    div_ellipse.append(image_add);
    div_ellipse_cover.append(div_ellipse);
    div_main.append(div_ellipse_cover);
    div_noimage.append(span_upload);
    div_main.append(div_noimage);
    return div_main;
}


document.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        if(document.querySelector('#main_search_form')) {
            document.querySelector('#main_search_form').submit();
        }
    }
}, false);