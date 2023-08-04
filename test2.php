<?php
var_dump('POST');
var_dump($_POST);
var_dump('FILES');
var_dump($_FILES);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Cropper.js</title>
	<link rel="stylesheet" href="https://unpkg.com/bootstrap@4/dist/css/bootstrap.min.css" crossorigin="anonymous">
	<link rel="stylesheet" href="inc/assets/css/cropper.css">
	<style>
		.label {
			cursor: pointer;
		}

		.progress {
			display: none;
			margin-bottom: 1rem;
		}

		.alert {
			display: none;
		}

		.img-container img {
			max-width: 100%;
		}
	</style>
</head>

<body>
	<div class="container">
		<h1>Upload cropped image to server</h1>
		<p>Please note that the image will be uploaded to a <a href="https://jsonplaceholder.typicode.com/">third-party
				fake API server</a>, which means that the upload process will sometimes fail.</p>
		<label class="label" data-toggle="tooltip" title="Change your avatar">
			<img class="rounded" id="avatar" src="https://avatars0.githubusercontent.com/u/3456749?s=160" alt="avatar">
			<input type="file" class="sr-only" id="input" name="image" accept="image/*">
		</label>
		<div class="progress">
			<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0"
				aria-valuemin="0" aria-valuemax="100">0%</div>
		</div>
		<div class="alert" role="alert"></div>
		<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
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
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-primary" id="crop">Crop</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form id="go" method="POST">
		<input type="hidden" id="vvv" name="vvv" value="">
	</form>
	<script src="https://unpkg.com/jquery@3/dist/jquery.min.js" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/bootstrap@4/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
	<script src="inc/js/cropper.js"></script>
	<script>
		window.addEventListener('DOMContentLoaded', function () {
			var avatar = document.getElementById('avatar');
			var image = document.getElementById('image');
			var input = document.getElementById('input');
			var $progress = $('.progress');
			var $progressBar = $('.progress-bar');
			var $alert = $('.alert');
			var $modal = $('#modal');
			var cropper;

			$('[data-toggle="tooltip"]').tooltip();

			input.addEventListener('change', function (e) {
				var files = e.target.files;
				var done = function (url) {
					input.value = '';
					image.src = url;
					$alert.hide();
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
				var initialAvatarURL;
				var canvas;

				$modal.modal('hide');

				if (cropper) {
					canvas = cropper.getCroppedCanvas();
					// var ff = document.getElementById('go');
					// var input1 = document.getElementById('vvv');
					// input1.value = cropper.getCroppedCanvas().toDataURL('image/jpeg');
					// ff.submit();
					canvas.toBlob(function (blob) {
						var formData = new FormData();

						formData.append('fileToUpload', blob, 'wall.jpg');
						formData.append('action', 'test');
						$.ajax('/test3.php', {
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
								console.log(dd);
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
</body>

</html>