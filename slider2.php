<?php
session_start();
include_once 'config.php';
include_once 'helpers/Validation.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';
include_once('./inc/template/header.php');
$images = getGalleryForSlider();
?>
<div class="main-page-slider-container">
  <div class="main-page-slider-control main-page-left main-page-inactive"></div>
  <div class="main-page-slider-control main-page-right"></div>
  <ul class="main-page-slider-pagi"></ul>
  <div class="main-page-slider">
    <?php foreach($images as $key=>$image) { ?>
    <div class="main-page-slide main-page-slide-<?= $key ?> <?= $key == 0 ? 'main-page-active' : '' ?>">
      <div style='background-image: url(./inc/assets/img/<?= $image['name_original'] ?>);' class="main-page-slide__bg"></div>
      <div class="main-page-slide__content" style="display:none;">
        <svg class="main-page-slide__overlay" viewBox="0 0 720 405" preserveAspectRatio="xMaxYMax slice">
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
<?php
include_once('./inc/template/footer.php');
?>