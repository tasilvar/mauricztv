<?php
use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\Software_Purchase;

/** @var int $uid */
?>
<div class="wpi-popup__core subscription-exceeded-popup-core">
    <div class="subscription-exceeded-popup-core__image"></div>
    <div class="subscription-exceeded-popup-core__content-wrap">
        <h2 class="subscription-exceeded-popup-core__title">Oj, oj... chyba mamy mały problem...</h2>
        <p>Niezmiernie nam przykro, ale czas próbny dla Twojej platformy dobiegł końca i wkrótce zostanie ona usunięta :(</p>
        <p style="font-weight: bold;">Jeśli chcesz uchronić ją przed tym losem, niezwłocznie dokonaj płatności, korzystając z przycisku poniżej:</p>

        <div class="subscription-exceeded-popup-core__buttons-wrap">
            <?php
            Button::create(__('Opłać wersję CLOUD', BPMJ_EDDCM_DOMAIN), Button::TYPE_MAIN)
                ->add_class('subscription-exceeded-popup-core__cta')
                ->add_data('modal-title',__('WP Idea GO Prices', BPMJ_EDDCM_DOMAIN))
                ->add_data('buy-url',Software_Purchase::GO_PRICING_URL)
                ->add_data('uid',  $uid)
                ->add_data('domain',get_home_url())
                ->close_popup_on_click()
                ->print_html();
            ?>
            <span>lub</span>
            <?php
            Button::create(__('Wykup wersję BOX', BPMJ_EDDCM_DOMAIN), Button::TYPE_MAIN)
                ->add_class('subscription-exceeded-popup-core__cta')
                ->add_data('modal-title',__('WP Idea BOX Prices', BPMJ_EDDCM_DOMAIN))
                ->add_data('buy-url',Software_Purchase::BOX_PRICING_URL)
                ->add_data('uid',  $uid)
                ->add_data('domain',get_home_url())
                ->close_popup_on_click()
                ->print_html();
            ?>
        </div>

        <a href="https://poznaj.publigo.pl/articles/219968-co-si-stanie-jeli-nie-opac-wersji-trial" target="_blank" class="subscription-exceeded-popup-core__how-it-works">Dowiedz się więcej</a>
    </div>
</div>

<script>
jQuery(document).ready(function ($){
    let cta = $('.subscription-exceeded-popup-core__cta');
    let img = $('.subscription-exceeded-popup-core__image');

    cta.on('mouseout', function(){
        img.removeClass('cta-hover');
    })

    cta.on('mouseenter', function(){
        img.addClass('cta-hover');
    })
});
</script>
