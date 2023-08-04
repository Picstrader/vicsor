<?php

function checkFavoritedImage($image, $favorits)
{
    foreach ($favorits as $favorite) {
        if ((int) $favorite['gallery_id'] == (int) $image['id']) {
            return true;
        }
    }
    return false;
}

$active_favorited = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'search_hash':
            $filters = [];
            $filters['hash'] = $_POST['search-header-global'];
            $filters['favorite'] = false;
            $filters['cost_min'] = false;
            $filters['cost_max'] = false;
            $filters['search_mask'] = '1';
            break;
        case 'favorite':
            $active_favorited = true;
            $filters = [];
            $filters['hash'] = '';
            $filters['favorite'] = !((bool) $_POST['favorite']);
            $filters['user_id'] = isLogin() ? getLoginUserId() : 0;
            $filters['cost_min'] = false;
            $filters['cost_max'] = false;
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['searched_hash'])) {
        $filters = [];
        $filters['hash'] = $_GET['searched_hash'];
        $filters['favorite'] = false;
        $filters['cost_min'] = false;
        $filters['cost_max'] = false;
        $filters['search_mask'] = '1';
    } else if (isset($_GET['favorite'])) {
        $active_favorited = true;
        $filters = [];
        $filters['hash'] = '';
        $filters['favorite'] = !((bool) $_GET['favorite']);
        $filters['user_id'] = isLogin() ? getLoginUserId() : 0;
        $filters['cost_min'] = false;
        $filters['cost_max'] = false;
    } else if (isset($_GET['shared_link'])) {
        $shared_link = (int) $_GET['shared_link'];
        echo '<script>let shared_link =' . $shared_link . '</script>';
    } else {
        echo '<script>let shared_link = null</script>';
    }
}
if (!isset($page)) {
    $page = 1;
}

$images_amount = getGalleryAmount();
$images_amount = (int) $images_amount[0]['amount'];
$on_page = 35;
$pages = (intdiv($images_amount, $on_page)) + ($images_amount % $on_page !== 0 ? 1 : 0);
if ($page > $pages) {
    //$page = 1;
}
if(!$ajax) {
    $_SESSION['rand'] = mt_rand(1, 1000);
}
//var_dump($_SESSION['rand']);
if (isset($filters)) {
    $filters['page'] = $page;
    if ($filters['hash'] != '') {
        $images = (bool) $filters['favorite'] ? getGalleryByHashFavorite($filters) : getGalleryByHash($filters);
    } else {
        $images = (bool) $filters['favorite'] ? getGalleryImagesFavorite($filters) : getGalleryImages($_SESSION['rand'], $page);
    }
} else {
    $images = getGalleryImages($_SESSION['rand'], $page);
    $active_favorited = false;
}

$favorited_images = getFavoriteImages(['user_id' => getLoginUserId()]);
?>

<?php if ($active_favorited && count($images) <= 0) { ?>
    <section class="gallery-noimage">
        <div class="gallery-images__header">
            <?= $fs["You do not have favorite images"] ?>
        </div>
        <a class="main_heroes__top_section-info-button" href="gallery.php">
            <?= $fs['back to gallery'] ?>
        </a>
    </section>
<?php } ?>

<?php if ($filters['search_mask'] == 1 && count($images) <= 0) { ?>
    <section class="gallery-noimage">
        <?php
        $title_log = $fs["Nothing found for your request"];
        $title_log = str_replace("{{search}}", $filters['hash'], $title_log);
        ?>
        <div class="gallery-images__header">
            <?= $title_log ?>
        </div>
        <a class="main_heroes__top_section-info-button" href="gallery.php">
            <?= $fs['back to gallery'] ?>
        </a>
    </section>
<?php } ?>

<section id="gallery-images__section" class="gallery-images__section-foto">

    <div class="gallery-images__images-cover">
        <?php foreach ($images as $image) {
            $image['profit_usdt'] = ((float) $image['profit'] == 0) ? ECommerceLogic::getProfitUSDT($image) : (float) $image['profit'];
            $image['profit_percent'] = ((float) $image['percent'] == 0) ? ECommerceLogic::getProfitPercent($image) : (float) $image['percent'];
            $price = 0;
            $fields = [];
            $fields['image_id'] = $image['image_id'];
            $fields['user_id'] = isLogin() ? getLoginUserId() : 0;
            $purchased_image_data = getAllPartsOfPurchasedImage($fields);
            $parts_amount = count($purchased_image_data);
            $owner_login = getUserNickname($purchased_image_data[0]['user_id']);
            $owner_login = $owner_login[0]['nickname'];
            $logged_owner = null;
            if (count($purchased_image_data) > 0) {
                foreach ($purchased_image_data as $owner) {
                    if ((int) $owner['user_id'] == (int) $fields['user_id']) {
                        // $parts_amount--;
                        // $logged_owner = $owner;
                        $price += (float) $owner['price'];
                    } else {
                        $price += (float) $owner['price'];
                    }
                }
                if ($parts_amount <= 0) {
                    // $price = (float) $logged_owner['price'];
                    $price = $price / $parts_amount;
                } else {
                    $price = $price / $parts_amount;
                }
                $price = round($price, 2);
                if (isset($filters)) {
                    if ($filters['cost_min'] != '') {
                        if ($price == 0) {
                            continue;
                        }
                        if ((float) $filters['cost_min'] > $price) {
                            continue;
                        }
                    }
                    if ($filters['cost_max'] != '') {
                        if ($price == 0) {
                            continue;
                        }
                        if ((float) $filters['cost_max'] < $price) {
                            continue;
                        }
                    }
                }
            }
            ?>
            <div class="gallery-images__image-data">
                <div class="gallery-images__image"
                    style="background-image: url(./inc/assets/img/<?= $image['name_thumbnail'] ?>);"
                    onclick="openImage(this)" data-image='<?= json_encode($image) ?>'>
                    <div class="zoom-galery-block-first">
                        <div class="zoom-galery-block">
                            <img class="img-zoom-galery" src="/inc/assets/img/zoomgallery.png">
                        </div>
                    </div>
                </div>
                <div class="gallery-images__profit-block">
                    <div class="gallery-images__profit">
                        <span>
                            <?= $fs['Likes'] ?>
                        </span>
                        <span class='gallery-images__price'>
                            <?= $image['likes'] ?>
                        </span>
                        <?php if (checkFavoritedImage($image, $favorited_images)) { ?>
                            <img class="favorite-icon-small">
                        <?php } ?>
                    </div>
                    <div class="gallery-images__profit">
                    <span>
                        <?= $fs['Profit'] ?>
                    </span>
                    <span class='gallery-images__price'>
                        <?= $image['profit_usdt'] . ' ' . $fs['main_currency'] ?>
                    </span>
                    <span class='gallery-images__price'>
                        <?= $image['profit_percent'] . ' ' . '%' ?>
                    </span>
                </div>
                </div>
                
            </div>
        <?php } ?>
    </div>
</section>