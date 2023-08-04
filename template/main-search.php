<section class="main_search">
    <div class="main_search_cover">
        <form method="POST" id="main_search_form">
            <input name="search" list="trade_modal__popular_hashtags_filters" autocomplete="off" id="trade_filter__search"
                class="search-header-input" oninput="loadPopularHashtagsFilters(this)"
                onchange='setTimeout(() => {$("#"+this.id).trigger("input")}, 500);' placeholder="Search hashtag">
            <div class="hesh__seacrh-block-datalist">
                <datalist id="trade_modal__popular_hashtags_filters">
                </datalist>
            </div>
        </form>
    </div>
</section>