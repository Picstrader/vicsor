<?php
if (!isset($filters) && !isset($page)) {
    $_SESSION['remember_preview'] = [];
}
if (!isset($page)) {
    $page = 1;
}
if (isset($filters)) {
    //$sets = getAllSetsByFilter($filters);
    $sets = getAllSets();
} else {
    $filters = [];
    $filters['search'] = '';
    $sets = getAllSets();
}

$sets = array_filter($sets, function ($set) use ($filters) {
    $players = $set['users_photos'] === '' ? [] : explode(';', $set['users_photos']);
    if (count($players) != (int) $set['total_photos']) {
        return false;
    }
    if ($filters['search'] !== '') {
        $filters['search'] = strtolower($filters['search']);
        $hash_id = false;
        $has_hashtag = false;
        if (str_starts_with($set['id'], $filters['search'])) {
            $hash_id = true;
        } else {
            foreach ($players as $player) {
                $player_data = explode(':', $player);
                $hashtags = getImageHashtags($player_data[1]);
                foreach ($hashtags as $hashtag) {
                    if ($hashtag['name'] == $filters['search']) {
                        $has_hashtag = true;
                        break 2;
                    }
                }
            }
        }
        if (!$has_hashtag && !$hash_id) {
            return false; //if images have not hashtag
        }
    }
    return true;
});
$sets = array_values($sets);
$sets_amount = count($sets);
$sets_on_page = 14;
$pages = (intdiv($sets_amount, $sets_on_page)) + ($sets_amount % $sets_on_page !== 0 ? 1 : 0);
if ($page > $pages) {
    $page = 1;
}
$page = (int) $page;
$row = ($page - 1) * $sets_on_page;
$sets = array_slice($sets, $row, $sets_on_page);

$pagination_dots_left = false;
$pagination_dots_right = false;
$start_page = $page - 3;
$range = 6;
if ($pages < 6) {
    $start_page = 1;
    $range = $pages;
} else {
    if ($page > 5) {
        $pagination_dots_left = true;
    } else {
        $start_page = 1;
    }
    if ($page < $pages - 4) {
        $pagination_dots_right = true;
    } else {
        $start_page = $pages - 5;
    }
}
?>
<section id="rate-sets__section" class="rate-sets__section">
    <div class="rate-sets__title"><?= $fs['Another Sets'] ?></div>
    <div class="rate-sets__sets">
        <?php foreach ($sets as $set) {
        $players = explode(';', $set['users_photos']);
        if (isset($_SESSION['remember_preview'][$set['id']])) {
            $preview_index = $_SESSION['remember_preview'][$set['id']];
        } else {
            $preview_index = array_rand($players);
            $_SESSION['remember_preview'][$set['id']] = $preview_index;
        }
        $player_data = explode(':', $players[$preview_index]);
        $fields = [];
        $fields['user_id'] = $player_data[0];
        $fields['image_id'] = $player_data[1];
        $preview_name = getImage($fields);
        $preview_name = isset($preview_name[0]) ? $preview_name[0]['name'] : '';
        ?>
            <div class="rate-sets__set">
                <div class="rate-sets__set_image" onclick="openSet(this)" data-id="<?= $set['id'] ?>" style="background-image: url('./inc/assets/img/<?= $preview_name ?>')">
                </div>
                <div class="rate-sets__set_id">
                    <span><?= $fs['Set id'] ?></span>
                    <span>
                        <?= $set['id'] ?>
                    </span>
                </div>
            </div>
            <?php } ?>
    </div>
    <div class="trade-sets__pagination">
        <a class="trade-sets__pagination-arrow" href="javascript:void(0)"
            onclick="updateRateSetsList(<?= $page === 1 ? 1 : $page - 1 ?>)">
            <?='<img src="/inc/assets/img/arrowleft.svg">' ?>
        </a>
        <?php if ($pagination_dots_left) { ?>
            <a class="trade-sets__pagination-page" href="javascript:void(0)" onclick="updateRateSetsList(1)">1 ... </a>
            <?php } ?>
        <?php for ($i = $start_page; $i < ($start_page + $range); $i++) { ?>
            <a class="<?= $i === $page ? 'trade-sets__pagination-current-page' : 'trade-sets__pagination-page' ?>"
                href="javascript:void(0)" onclick="updateRateSetsList(<?= $i ?>)">
                <?= $i ?>
            </a>
            <?php } ?>
        <?php if ($pagination_dots_right) { ?>
            <a class="trade-sets__pagination-page" href="javascript:void(0)" onclick="updateRateSetsList(<?= $pages ?>)">
                ... <?= $pages ?></a>
            <?php } ?>
        <a class="trade-sets__pagination-arrow" href="javascript:void(0)"
            onclick="updateRateSetsList(<?= $page === $pages ? $page : $page + 1 ?>)"><img
                src="/inc/assets/img/arrowright.svg"></a>
    </div>
</section>