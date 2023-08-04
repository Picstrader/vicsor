<?php
$images = getImages(1, 5, 'gallery');
?>
<section class="main_heroes">
    <div class="main_heroes__top_section">
        <div class="main_heroes__top_section-info">
            <div class="heroes-img-title">
            </div>
            <div class="header__authorization-login-button">
                <?php
                if (isLogin()) {
                    ?>
                    <a class="main_heroes__top_section-info-button" href="trade.php">
                        <?= $fs['Start'] ?>
                    </a>
                    <?php
                } else {
                    ?>
                    <a class="main_heroes__top_section-info-button" href="trade.php">
                        <?= $fs['Start'] ?>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="main_heroes__top_section-slider">
            <div class="main-page-slider-container">
                <div class="main-page-slider-control main-page-left main-page-inactive"></div>
                <div class="main-page-slider-control main-page-right"></div>
                <ul class="main-page-slider-pagi"></ul>
                <div class="main-page-slider">
                    <?php foreach ($images as $key => $image) { ?>
                        <div style="left:<?= $key * 100 ?>%"
                            class="main-page-slide main-page-slide-<?= $key ?> <?= $key == 0 ? 'main-page-active' : '' ?>">
                            <div style='left:<?= $key * 50 * (-1) ?>%;background-image: url(./inc/assets/img/<?= $image['name_original'] /*'16831392529193415926452aab491eed.jpg'*/?>);'
                                class="main-page-slide__bg"></div>
                            <div class="main-page-slide__content" style="display:none;">
                                <svg class="main-page-slide__overlay" viewBox="0 0 720 405"
                                    preserveAspectRatio="xMaxYMax slice">
                                    <path class="main-page-slide__overlay-path" d="M0,0 150,0 500,405 0,405" />
                                </svg>
                                <div class="main-page-slide__text">
                                    <h2 class="main-page-slide__text-heading"></h2>
                                    <p class="main-page-slide__text-desc"></p>
                                    <a class="main-page-slide__text-link"></a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="header__authorization-login-button-mobile">
            <?php
            if (isLogin()) {
                ?>
                <a class="main_heroes__top_section-info-button" href="trade.php">
                    <?= $fs['Start'] ?>
                </a>
                <?php
            } else {
                ?>
                <a class="main_heroes__top_section-info-button" href="trade.php">
                    <?= $fs['Start'] ?>
                </a>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<!-- 
<section class="main-video">
    <div>
        <iframe width="100%" height="500" src="https://www.youtube.com/embed/hoJS5uWzwsg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    </div>
    <div class="header__authorization-more-button">
        <a class="main_heroes__top_section-more-button" href="how-it-works.php"><?= $fs['More'] ?></a>
    </div>
</section> -->
