<section id="modalComplain" class="modal" style="display:none;">
    <div class="modal-content-align">
        <div class="modal-content__complain">
            
            <div class="modal-rate-block">
                <div class="rate-search__info"><p class="rate-modal-title"><?= $fs['Complain about the image'] ?>:</p><a class="close-modal-rate" onclick="hideModalComplain()"><img class="img__del-modal-rate" src='/inc/assets/img/closemodal.svg'></a></div>
                <div class="rate-search__info-button-block">
                    <button class="rate-search__info-button" onclick="sendComplain(1)"><?= $fs['Offensive photo'] ?></button>
                    <button class="rate-search__info-button" onclick="sendComplain(2)"><?= $fs['Violence'] ?></button>
                    <button class="rate-search__info-button" onclick="sendComplain(3)"><?= $fs['adult content'] ?></button>
                </div>
            </div>
        </div>
    </div>
</section>