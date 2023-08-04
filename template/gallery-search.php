<section class="trade__heading_section">
    <div style="display:flex;justify-content:space-between;">
        <div class="breadcrumbs">
            <div class="breadcrumbs-home" onClick="location.href='/'"></div>
            <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
            <div class="breadcrumbs-page">
                <?= $fs['Gallery'] ?>
            </div>
        </div>
        <form method="POST">
            <input class="search-header-input" id="search-header-global" name="search-header-global" type="text"
                placeholder="<?= $fs['Search hashtag'] ?>"
                value="<?= isset($input_rate_search_value) ? $input_rate_search_value : '' ?>">
            <input type="hidden" name="action" value="search_hash">
        </form>
    </div>
</section>