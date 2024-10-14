<?php
use bpmj\wpidea\admin\helpers\html\Link;

/** @var string $renew_url */
?>

<div class="wpi-popup__core subscription-exceeded-popup-core">
    <div class="subscription-exceeded-popup-core__image"></div>
    <div class="subscription-exceeded-popup-core__content-wrap">
        <h2 class="subscription-exceeded-popup-core__title">Oj, oj... chyba mamy mały problem...</h2>
        <p>Chyba coś poszło nie tak, gdyż nie odnotowaliśmy wpłaty za kolejny okres subskrypcji platformy WP Idea GO. Tym samym, nasz bezduszny system oznaczył ją jako nieopłaconą i wkrótce zostanie usunięta :(</p>
        <p style="font-weight: bold;">Jeśli chcesz uchronić ją przed tym losem, niezwłocznie dokonaj płatności za kolejny okres subskrypcji.</p>


        <div class='subscription-exceeded-popup-core__buttons-wrap'>
            <?php
            Link::create(__('Przedłuż dostęp do usługi', BPMJ_EDDCM_DOMAIN), $renew_url)
                ->add_class('subscription-exceeded-popup-core__renew-link')
                ->open_in_new_tab()
                ->print_html();
            ?>
        </div>
        <a href="https://poznaj.publigo.pl/articles/219966-co-si-stanie-jeli-nie-opac-kolejnego-miesica-go" target="_blank" class="subscription-exceeded-popup-core__how-it-works">Dowiedz się więcej</a>
    </div>
</div>

<script>
    jQuery(document).ready(function ($){
        let cta = $('.subscription-exceeded-popup-core__renew-link');
        let img = $('.subscription-exceeded-popup-core__image');

        cta.on('mouseout', function(){
            img.removeClass('cta-hover');
        })

        cta.on('mouseenter', function(){
            img.addClass('cta-hover');
        })
    });
</script>
