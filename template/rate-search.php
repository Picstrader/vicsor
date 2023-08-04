<section class="trade__heading_section">
    <div style="display:flex;justify-content:space-between;">
        <div class="breadcrumbs">
            <div class="breadcrumbs-home" onClick="location.href='/'"></div>
            <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
            <div class="breadcrumbs-page">
                <?= $fs['Rate photo'] ?>
            </div>
        </div>
        <form method="POST">
            <input class="search-header-input" id="rate-search" name="search-header-global" type="text"
                placeholder="<?= $fs['Search hashtag'] ?>"
                value="<?= isset($input_rate_search_value) ? $input_rate_search_value : '' ?>">
            <input type="hidden" name="action" value="search_hash_rate">
        </form>
    </div>
    <div><img class="rate-slider-info" style="z-index:3;cursor: pointer;" onclick='info()' src="./inc/assets/img/info.svg"></div>
</section>