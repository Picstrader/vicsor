let check_create = false;
function addErrorMessage(error_element, message, not_click = false) {
    //forceRemove();
    check_create = not_click ? true : false;
    let div = document.createElement('div');
    let error_message = document.createElement('span');
    error_message.className = 'trade-error-message';
    error_message.innerHTML = message;
    div.append(error_message);
    div.className = 'block-message';
    error_element.after(div);
    document.addEventListener("click",removeErrorMessage);
}

function removeErrorMessage() {
    if (!check_create) {
        check_create = true;
    } else {
        let error_message = $('.block-message');
        if (error_message) {
            error_message.remove();
            document.removeEventListener("click", removeErrorMessage);
            check_create = false;
            removeOtherErrorElements();
        }
    }
}

function forceRemove() {
    let error_message = $('.block-message');
        if (error_message) {
            error_message.remove();
        }
}

function removeOtherErrorElements() {
    $('.trade_gallery__section_foto').removeClass("trade_modal__error");
    $('.trade_gallery__empty_section').removeClass("trade_modal__error");

    $('#trade_filter__cost').removeClass("trade_modal__error");
    $('#trade_filter__photos').removeClass("trade_modal__error");
    $('#trade_filter__purchasable').removeClass("trade_modal__error");
    $('#trade_modal__cost').removeClass("trade_modal__error");
    $('#trade_modal__photos').removeClass("trade_modal__error");
    $('#trade_modal__purchasable').removeClass("trade_modal__error");
}