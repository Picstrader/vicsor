<?php
$_SESSION['move_back'] = false;
?>
<section id="empty_slider" class="gallery-noimage" style="display:none;">
</section>
<section class="rate-slider__section">
        <div style="display:flex;">
                <div style="display: none;align-items: center;"><img src="./inc/assets/img/back.png"
                                class="rate-carousel-control rate-carousel-control-back" data-name="back"></div>
                <div class="rate-carousel"></div>
                <div style="display: none;align-items: center;"><img src="./inc/assets/img/dislike.png"
                                class="rate-carousel-control rate-carousel-control-next" data-name="next"></div>
        </div>
        <div class="rate-carousel-controls">
                <div class="rate-range-cover" style="display: none;align-items: center;gap: 5vh;">
                        <img src="./inc/assets/img/dislike.svg"
                                class="rate-carousel-control rate-carousel-control-dislike" data-name="dislike">
                        <img src="./inc/assets/img/like.svg" class="rate-carousel-control rate-carousel-control-like"
                                data-name="like">
                </div>
        </div>
        <div class="slider-nav"></div>
</section>