<?php
include_once('./inc/template/header.php');
?>
<section class="trade__heading_section new-margin">
    <div class="breadcrumbs">
        <div class="breadcrumbs-home" onClick="location.href='/'"></div>
        <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
        <div class="breadcrumbs-page"><?= $fs['Supplier Agreement'] ?></div>
    </div>
</section>
<section class="section-terms new-margin">
    <div class="div-terms">
    <br>
        <?= $fs['supplier-agreement_text'] ?>
    </div>
</section>

<?php
include_once('./inc/template/footer.php');
?>
