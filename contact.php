<?php
include_once('./inc/template/header.php');
?>
<section class="trade__heading_section new-margin">
    <div class="breadcrumbs">
        <div class="breadcrumbs-home" onClick="location.href='/'"></div>
        <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
        <div class="breadcrumbs-page"><?= $fs['Contact information'] ?></div>
    </div>
</section>

<section class="contact">
    <div class="contact-title"><?= $fs['contact-text'] ?></div>
    <table>
        <!--<tr><td><b>Company Director:<br>Vitalii Naumenko</b></td><td><img src="./inc/assets/img/director.jpg" class="contact-image"></td></tr>-->
        <tr><td><b>Company Name:</b></td><td>PICSTRADER LTD</td></tr>
        <tr><td><b>Registered at:</b></td><td>63 - 66 HATTON GARDEN</td></tr>
        <!--<tr><td></td><td>5TH FLOOR, SUITE 23</td></tr>-->
        <tr><td></td><td>LONDON</td></tr>
        <tr><td></td><td>ENGLAND EC1N 8LE</td></tr>
        <tr><td><b>Registration number:</b></td><td>14702335</td></tr>
        <tr><td><b>Support e-mail:</b></td><td>support@picstrader.com</td></tr>
    </table>
</section>

<?php
include_once('./inc/template/footer.php');
?>