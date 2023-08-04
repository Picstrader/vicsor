<section style="margin-top: 30px;">
    <section class="personal-account-purchased-breadcrumbs">
        <div class="breadcrumbs">
            <div class="breadcrumbs-home" onClick="location.href='/'"></div>
            <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
            <div class="breadcrumbs-page">Personal Account</div>
        </div>
    </section>
    <form style="display: flex; justify-content: center;" action="#" method="POST" enctype="multipart/form-data">
        <!-- <div style="margin:10px;">
            <label for="fileToUpload">Выберите фото:</label>
            <input type="file" id="fileToUpload" name="fileToUpload">
        </div> -->
        <label class="personal-account-label">
            <input type="file" id="fileToUpload" name="fileToUpload">
            <span>Download an image</span>
        </label>
    </form>
</section>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="margin-top: 80px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Crop the image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
                </div>
                <div style="margin:10px;">
                    <label for="fileToUpload">
                        <?= $fs['Hashtags'] ?>
                    </label>
                    <div style="display:flex;">
                        <span style="font-size:20px;padding-top:8px;">#</span>
                        <select data-placeholder=" " multiple class="chosen-select" name="hashtags[]">
                            <option value=""></option>
                        </select>
                    </div>
                    <div id="hashtag-error" style="display: none; text-align: center; color: red;">
                        <span>Enter at least one hashtag</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop">Crop</button>
            </div>
        </div>
    </div>
</div>