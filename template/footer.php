<?php
include_once('./inc/template/cookie-modal.php');
?>
<script>
    <?php 
        // $js_fs = [];
        // foreach ($fs as $k => $v) {
        //     $js_fs[$k] = str_replace(['\'', '"'], ['&#039', '&quot'], $v);
        // } 
    ?>
    var fs = <?php echo json_encode($fs); ?>;
</script>
<footer class="footer">
<!--    <div class="footer-logo">
        <a href="/">
            <div class="foter-logo-img"></div>
        </a>
    </div> -->
    <nav class="footer__nav">
        <ul class="footer__nav-ul">
            <li class="footer__nav-ul-li">
                <a href="/inc/documents/<?= $fs['doc_Terms_of_use'] ?>" target="_blank" class="footer__nav-ul-li-a"><?= $fs['Terms'] ?></a>
            </li>
            <li class="footer__nav-ul-li">
                <a href="/inc/documents/<?= $fs['doc_Privacy_Policy'] ?>" target="_blank" class="footer__nav-ul-li-a"><?= $fs['Privacy Policy'] ?></a>
            </li>
            <li class="footer__nav-ul-li">
                <a href="/inc/documents/<?= $fs['doc_cookie'] ?>" target="_blank" class="footer__nav-ul-li-a"><?= $fs['cookies'] ?></a>
            </li>
            <li class="footer__nav-ul-li">
                <a href="/inc/documents/<?= $fs['doc_Public_license_Agreement'] ?>" target="_blank" class="footer__nav-ul-li-a"><?= $fs['Supplier Agreement'] ?></a>
            </li>
            <li class="footer__nav-ul-li">
                <a href="/inc/documents/<?= $fs['doc_File_Sale'] ?>" target="_blank" class="footer__nav-ul-li-a"><?= $fs['File Sale'] ?></a>
            </li>
            <li class="footer__nav-ul-li">
                <a href="contact.php" class="footer__nav-ul-li-a"><?= $fs['Contact information'] ?></a>
            </li>
        </ul>
    </nav>
    <div class="footer__socials">
        <a href="https://www.instagram.com/picstrader_com/" target="_blank"><img src="./inc/assets/img/instagram-footer.svg" alt="instagram" class="footer__social"></a>
        <a href="https://t.me/picstrader" target="_blank"><img src="./inc/assets/img/telegram-footer.svg" alt="telegram" class="footer__social"></a>
        <a href="https://www.youtube.com/@picstrader" target="_blank"><img src="./inc/assets/img/youtube-footer.svg" alt="youtube" class="footer__social"></a>
        <a href="https://www.facebook.com/profile.php?id=100089981705933&mibextid=LQQJ4d" target="_blank"><img src="./inc/assets/img/facebook-footer.svg" alt="facebook" class="footer__social"></a>
    </div>
    <div class="footer__rights_reserved" id="footer__rights_reserved">
        2023 © <?= $fs['All rights reserved'] ?>
    </div>
</footer><!-- Footer End-->
<!-- Java Script
   ================================================== -->
<script src="inc/js/jquery-3.6.2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.0/js/intlTelInput.min.js"></script>
<script src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
<script src="inc/js/country-phone.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.0/css/intlTelInput.css" />
<script src="inc/js/modal-manager.js"></script>
<script src="inc/js/show-hide-pass.js"></script>
<script src="inc/js/trade-error-message.js"></script>
<script src="inc/js/ajax-functions.js"></script>
<script src="inc/js/registration.js"></script>
<script src="inc/js/trade-modal.js"></script>
<script src="inc/js/active-sets.js"></script>
<script src="inc/js/how-it-works-faqs.js"></script>
<script src="inc/js/rate.js"></script>
<script src="inc/js/main-page.js"></script>
<script src="inc/js/popup_winner.js"></script>
<script src="inc/js/background-update-sets.js"></script>
<script src="inc/js/datalist-css.min.js"></script>
<script src="inc/js/personal-account.js"></script>
<script src="inc/js/chosen.jquery.min.js"></script>
<script src="inc/js/chosen-init.js"></script>
<script src="inc/js/guide.js"></script>
<script defer src="inc/js/carousel.js"></script>
<script src="inc/js/main-slider.js"></script>
<script src="inc/js/coockies.js"></script>
<?php if (str_contains($routes[1], 'personal-account.php')) { ?>
    <script src="https://unpkg.com/bootstrap@4/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
	<script src="inc/js/cropper.js"></script>
	<script>
		window.addEventListener('DOMContentLoaded', function () {
			var image = document.getElementById('image');
			var input = document.getElementById('fileToUpload');
			var $modal = $('#modal');
			var cropper;

			$('[data-toggle="tooltip"]').tooltip();

			input.addEventListener('change', function (e) {
				var files = e.target.files;
				var done = function (url) {
					input.value = '';
					image.src = url;
					$modal.modal('show');
				};
				var reader;
				var file;
				var url;

				if (files && files.length > 0) {
					file = files[0];

					if (URL) {
						done(URL.createObjectURL(file));
					} else if (FileReader) {
						reader = new FileReader();
						reader.onload = function (e) {
							done(reader.result);
						};
						reader.readAsDataURL(file);
					}
				}
			});

			$modal.on('shown.bs.modal', function () {
                document.getElementById('hashtag-error').style.display = 'none';
				cropper = new Cropper(image, {
					aspectRatio: 7 / 15,
					viewMode: 1,
					autoCropArea: 1
				});
			}).on('hidden.bs.modal', function () {
				cropper.destroy();
				cropper = null;
			});

			document.getElementById('crop').addEventListener('click', function () {
                var hashtags = $('.chosen-select').val();
                if(!hashtags.length) {
                    document.getElementById('hashtag-error').style.display = 'block';
                    return;
                }
				var initialAvatarURL;
				var canvas;

				$modal.modal('hide');

				if (cropper) {
					canvas = cropper.getCroppedCanvas();
					canvas.toBlob(function (blob) {
						var formData = new FormData();

						formData.append('fileToUpload', blob, 'wall.jpg');
                        formData.append('hashtags', JSON.stringify(hashtags));
						formData.append('action', 'download');
						$.ajax('/personal-account-ajax.php', {
							method: 'POST',
							data: formData,
							processData: false,
							contentType: false,

							xhr: function () {
								var xhr = new XMLHttpRequest();

								xhr.upload.onprogress = function (e) {
								};

								return xhr;
							},

							success: function (dd) {
								reloadCurrentPage();
							},

							error: function () {
							},

							complete: function () {
							},
						});
					});
				}
			});
		});
	</script>
<?php } ?>
</body>

</html>