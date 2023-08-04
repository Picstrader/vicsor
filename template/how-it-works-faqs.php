<?php
$faqs = getFaqs();
?>
<section class="trade__heading_section">
    <div class="breadcrumbs">
        <div class="breadcrumbs-home" onClick="location.href='/'"></div>
        <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
        <div class="breadcrumbs-page">
            <?= $fs['faqs'] ?>
        </div>
    </div>
</section>

<section class="how_it_works_faqs">

    <?php
    // for ($i=1; $i <= 19; $i++) { 
//     $fnq = 'faq_sale_q_' . $i;
//     $fna = 'faq_sale_a_' . $i;
    
    //     if($fs[$fnq]!=''){
    foreach ($faqs as $faq) {
        ?>
        <div class="how_it_works_faqs__item">
            <p class="how_it_works_faqs__item-title">
                <img class="how_it_works_faqs__item-img">
                <?= /*$faq['question']*/$fs['faq_question_' . $faq['id']] ?>
            </p>
            <div class="how_it_works_faqs__item-description">
                <p class="how_it_works_faqs__item-description-p">
                    <?= /*$faq['answer']*/$fs['faq_answer_' . $faq['id']] ?>
                </p>
            </div>
        </div>

        <?php
        // }
    }
    ?>


</section>