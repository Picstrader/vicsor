<section class="trade__heading_section">
    <div class="breadcrumbs">
        <div class="breadcrumbs-home" onClick="location.href='/'"></div>
        <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
        <div class="breadcrumbs-page"><?= $fs['Trading'] ?></div>
    </div>
</section>
<script>
    let go_tutorial = <?= isset($_GET['tutorial']) ? 'true' : 'false' ?>;
</script>