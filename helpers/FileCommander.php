<?php
class FileCommander
{
    public static function upload_image($is_admin = false)
    {
        if ($is_admin) {
            $target_dir = "../inc/assets/img/";
        } else {
            $target_dir = "inc/assets/img/";
        }
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $image_name = $_FILES["fileToUpload"]["name"];
        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = $_FILES["fileToUpload"]["tmp_name"] ? getimagesize($_FILES["fileToUpload"]["tmp_name"]) : false;
            if ($check === false) {
                $_SESSION['error_type'] = "not_image";
                return false;
            }
        }

        // Check if file already exists
        //if (file_exists($target_file)) {
        $uniquesavename = time() . uniqid(rand());
        $image_name = $uniquesavename . '.' . $imageFileType;
        $target_file = $target_dir . $uniquesavename . '.' . $imageFileType;
        //}

        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 20000000) {
            $_SESSION['error_type'] = "large_file";
            return false;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" &&
            $imageFileType != "jpeg" && $imageFileType != "gif"
        ) {
            $_SESSION['error_type'] = "not_image_type";
            return false;
        }

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            return htmlspecialchars($image_name);
        } else {
            $_SESSION['error_type'] = "file_upload_error";
            return false;
        }
        return false;
    }

    public static function multi_image($file_name, $file_tmp_name, $file_size)
    {
        $target_dir = "../inc/assets/img/";
    $target_file = $target_dir . basename(/*$_FILES["fileToUpload"]["name"]*/$file_name);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $image_name = /*$_FILES["fileToUpload"]["name"]*/$file_name;
        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = /*$_FILES["fileToUpload"]["tmp_name"]*/$file_tmp_name ? getimagesize(/*$_FILES["fileToUpload"]["tmp_name"]*/$file_tmp_name) : false;
            if ($check === false) {
                $_SESSION['error_type'] = "not_image";
                return false;
            }
        }

        // Check if file already exists
        //if (file_exists($target_file)) {
        $uniquesavename = time() . uniqid(rand());
        $image_name = $uniquesavename . '.' . $imageFileType;
        $target_file = $target_dir . $uniquesavename . '.' . $imageFileType;
        //}

        // Check file size
        if (/*$_FILES["fileToUpload"]["size"]*/$file_size > 20000000) {
            $_SESSION['error_type'] = "large_file";
            return false;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" &&
            $imageFileType != "jpeg" && $imageFileType != "gif"
        ) {
            $_SESSION['error_type'] = "not_image_type";
            return false;
        }

        if (move_uploaded_file(/*$_FILES["fileToUpload"]["tmp_name"]*/$file_tmp_name, $target_file)) {
            return htmlspecialchars($image_name);
        } else {
            $_SESSION['error_type'] = "file_upload_error";
            return false;
        }
        return false;
    }

    public static function is_image($full_path)
    {
        try {
            $check = file_exists($full_path) ? getimagesize($full_path) : false;
            if ($check === false) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public static function create_watermark_image($image_name, $is_admin = false)
    {
        $watermark = 'PicsTrader';
        if ($is_admin) {
            $directory = "../inc/assets/img/";
        } else {
            $directory = "inc/assets/img/";
        }

        $image_type = strtolower(pathinfo($directory . '/' . $image_name, PATHINFO_EXTENSION));
        $uniquesavename = time() . uniqid(rand());
        $image_watermark_name = 'w' . $uniquesavename . '.' . $image_type;
        copy($directory . '/' . $image_name, $directory . '/' . $image_watermark_name);
        $image_watermark = glob($directory . '/' . $image_watermark_name);
        $image_watermark = $image_watermark[0];
        // Получение информации об изображении
        $data = getimagesize($image_watermark);

        // Координаты наносимого текста - примерно в центре изображения
        // $width = $data[0];
        // $height = $data[1];
        // $diagonal = (sqrt(pow($width, 2) + pow($height, 2))) / 15;
        // $x = ($width / 2) - $diagonal * 4;
        // $y = ($height / 2) + $diagonal * 2.8;

        // Создание изображения из оригинального на основе его типа
        $mime = $data['mime'];
        if ($mime == 'image/jpeg') {
            $type = 'jpeg';
            $i = imagecreatefromjpeg($image_watermark);
        } else if ($mime == 'image/png') {
            $type = 'png';
            $i = imagecreatefrompng($image_watermark);
            imagesavealpha($i, true);
        }
        $exif = exif_read_data($image_watermark);
        $orientation = $exif['Orientation'];
        # Manipulate image
        switch ($orientation) {
            case 2:
                imageflip($i, IMG_FLIP_HORIZONTAL);
                break;
            case 3:
                $i = imagerotate($i, 180, 0);
                break;
            case 4:
                imageflip($i, IMG_FLIP_VERTICAL);
                break;
            case 5:
                $i = imagerotate($i, -90, 0);
                imageflip($i, IMG_FLIP_HORIZONTAL);
                break;
            case 6:
                $i = imagerotate($i, -90, 0);
                break;
            case 7:
                $i = imagerotate($i, 90, 0);
                imageflip($i, IMG_FLIP_HORIZONTAL);
                break;
            case 8:
                $i = imagerotate($i, 90, 0);
                break;
        }
        // Координаты наносимого текста - примерно в центре изображения
        $width = imagesx($i) /*$data[0]*/;
        $height = imagesy($i) /*$data[1]*/;
        $diagonal = (sqrt(pow($width, 2) + pow($height, 2))) / 15;
        // Задание цвета и шрифта для текста (файл со шрифтом можно скопировать, например, из ОС Windows)
        $grey = imagecolorallocatealpha($i, 230, 230, 230, 100);
        if ($is_admin) {
            $font = "../inc/assets/fonts/manrope-v13-latin_cyrillic-800.woff";  
        } else {
            $font = "inc/assets/fonts/manrope-v13-latin_cyrillic-800.woff";  
        }
        // Get center coordinates of image
        $centerX = $width / 2;
        $centerY = $height / 2;
        // Get size of text
        list($left, $bottom, $right, , , $top) = imageftbbox($diagonal, 45, $font, $watermark);
        // Determine offset of text
        $left_offset = ($right - $left) / 2;
        $top_offset = ($bottom - $top) / 2;
        // Generate coordinates
        $x = $centerX - $left_offset;
        $y = $centerY + $top_offset;
        // Add text to image
        // Добавление водяного знака на изображение - текста
        imagettftext($i, $diagonal, 45, $x, $y, $grey, $font, $watermark);
        // Перезапись оригинального изображения новым изображением со знаком
        // Сохранение в зависимости от типа - jpg или png
        $type == 'png' ? imagepng($i, $image_watermark) : imagejpeg($i, $image_watermark);
        // Уничтожение временного изображения в оперативной памяти
        imagedestroy($i);
        return $image_watermark_name;
    }

    public static function get_image_file_data($image_name)
    {
        $directory = "inc/assets/img/";
        $image_path = $directory . '/' . $image_name;
        $image_data = getimagesize($image_path);
        $image_size = filesize($image_path);
        $result = [];
        $result['width'] = $image_data[0];
        $result['height'] = $image_data[1];
        $result['size'] = $image_size;
        return $result;
    }

    public static function create_thumbnail_image($image_name, $is_admin = false)
    {
        if ($is_admin) {
            $directory = "../inc/assets/img/";
        } else {
            $directory = "inc/assets/img/";
        }
        $image_type = strtolower(pathinfo($directory . '/' . $image_name, PATHINFO_EXTENSION));
        $uniquesavename = time() . uniqid(rand());
        $image_thumbnail_name = 'th' . $uniquesavename . '.' . $image_type;
        copy($directory . '/' . $image_name, $directory . '/' . $image_thumbnail_name);
        $image_thumbnail = glob($directory . '/' . $image_thumbnail_name);
        $image_thumbnail = $image_thumbnail[0];
        // Получение информации об изображении
        $data = getimagesize($image_thumbnail);

        // Создание изображения из оригинального на основе его типа
        $mime = $data['mime'];
        if ($mime == 'image/jpeg') {
            $type = 'jpeg';
            $i = imagecreatefromjpeg($image_thumbnail);
        } else if ($mime == 'image/png') {
            $type = 'png';
            $i = imagecreatefrompng($image_thumbnail);
            imagesavealpha($i, true);
        }

        $exif = exif_read_data($image_thumbnail);
        $orientation = $exif['Orientation'];
        # Manipulate image
        switch ($orientation) {
            case 2:
                imageflip($i, IMG_FLIP_HORIZONTAL);
                break;
            case 3:
                $i = imagerotate($i, 180, 0);
                break;
            case 4:
                imageflip($i, IMG_FLIP_VERTICAL);
                break;
            case 5:
                $i = imagerotate($i, -90, 0);
                imageflip($i, IMG_FLIP_HORIZONTAL);
                break;
            case 6:
                $i = imagerotate($i, -90, 0);
                break;
            case 7:
                $i = imagerotate($i, 90, 0);
                imageflip($i, IMG_FLIP_HORIZONTAL);
                break;
            case 8:
                $i = imagerotate($i, 90, 0);
                break;
        }

        /* read the source image */
        $width = imagesx($i);
        $height = imagesy($i);

        /* find the “desired height” of this thumbnail, relative to the desired width  */
        $desired_width = 207;
        $desired_height = floor($height * ($desired_width / $width));
        /* create a new, “virtual” image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
        if ($type == 'png') {
            imagealphablending($virtual_image, false);
            imagesavealpha($virtual_image, true);
        }
        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $i, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
        /* create the physical thumbnail image to its destination */
        $type == 'png' ? imagepng($virtual_image, $image_thumbnail) : imagejpeg($virtual_image, $image_thumbnail);

        // Уничтожение временного изображения в оперативной памяти
        imagedestroy($i);

        return $image_thumbnail_name;
    }

    public static function downloadUserImage($user_id, $status) {
        try {
            $original = FileCommander::upload_image();
            $name = FileCommander::create_watermark_image($original);
            $thumbnail = FileCommander::create_thumbnail_image($name);
            $image_id = addImage($name, $original, $thumbnail, $user_id, $status);
            return $image_id;
        } catch (\Throwable $th) {
            return false;
        }
    }
}