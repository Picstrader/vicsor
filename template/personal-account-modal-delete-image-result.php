<section id="modalPersonalAccountDeleteImage" class="modal" style="display:none;">
    <div class="personal-account-balance-modal">
        <div class="personal-account-balance-close">
            <a class="close" onclick="hidePersonalAccountModalDeleteImage()"><img class="img__del"
                    src='/inc/assets/img/closemodalwhite.svg'></a>
        </div>
        <div class="personal-account-balance-result-text-cover">
            <span id="personal-account-modal-delete-text" class="personal-account-balance-result-text">Are you sure you want to delete this image?</span>
        </div>
        <div style="display:flex;justify-content: space-between;">
            <button class="pers_acc__profile-data-submit" id="delete_button" onclick="">Delete</button>
            <button class="pers_acc__profile-data-submit" style="background-color:red;" onclick="hidePersonalAccountModalDeleteImage()">Back</button>
        </div>
    </div>
</section>