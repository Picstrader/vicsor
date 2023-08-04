<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Фотогалерея</title>
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
    <div style="display:flex;justify-content:center;">
        <form class="f1" action="#" method="POST" enctype="multipart/form-data">
            <div>
                <label for="fileToUpload">Выберите фото:</label>
                <input type="file" id="fileToUpload" name="fileToUpload">
            </div>
        </form>
    </div>
    <!-- Модальное окно -->
    <!-- <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div>
                <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
            </div>
            <div>
            <button type="button" class="btn btn-primary" id="crop">Crop</button>
            </div>
        </div>
    </div> -->

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Crop the image</h5>
                    <button type="button" onclick="closeModal()" class="close" data-dismiss="modal" aria-label="Close">
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
    <script src="https://unpkg.com/jquery@3/dist/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap@4/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="inc/js/cropper.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            // Открыть модальное окно
            function showModal(img) {
                cropper = new Cropper(image, {
                    aspectRatio: 7 / 15,
                    viewMode: 1,
                    autoCropArea: 1
                });
                document.getElementById("myModal").style.display = "block";
            }

            // Закрыть модальное окно
            function closeModal() {
                cropper.destroy();
                cropper = null;
                document.getElementById("myModal").style.display = "none";
            }

            var image = document.getElementById('image');
            var input = document.getElementById('fileToUpload');
            var $modal = $('#myModal');
            var cropper;

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

            document.getElementById('crop').addEventListener('click', function () {
                var canvas;
                closeModal();
                if (cropper) {
                    canvas = cropper.getCroppedCanvas({
                        width: 160,
                        height: 160,
                    });
                    canvas.toBlob(function (blob) {
                        var formData = new FormData();

                        formData.append('avatar', blob, 'avatar.jpg');
                        $.ajax('/test2.php', {
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

                            success: function () {
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