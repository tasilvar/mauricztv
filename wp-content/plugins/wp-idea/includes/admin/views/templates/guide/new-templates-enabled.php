<?php

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Info_Box;

$info_box = Info_Box::create(__('Witaj w Nowym systemie szablonów!', BPMJ_EDDCM_DOMAIN));

$info_box->add_class('templates-guide-info-box');
$info_box->add_class('new-templates-enabled-info');
$info_box->add_data('message-your-are-redy-to-go', __('Excellent! You are now ready to go!', BPMJ_EDDCM_DOMAIN));
$info_box->set_size(Info_Box::SIZE_SMALL);

$info_box->add_paragraph(__('Sprawdź, czy wszystkie elementy graficzne zachowują się poprawnie (również z perspektywy kursanta). Mamy nadzieję, że wszystko się udało. Jeśli jednak napotkasz jakieś problemy, wróć do poprzednich ustawień i skontaktuj się z nami.', BPMJ_EDDCM_DOMAIN));

$info_box->add_paragraph(__('<b>Ważne!</b> Zanim wrócisz do poprzednich ustawień, wykonaj zrzuty ekranu, przedstawiające problem, opisz je szczegółowo i wyślij do nas. Nie martw się! Na pewno znajdziemy rozwiązanie i pomożemy Ci w tej sytuacji.', BPMJ_EDDCM_DOMAIN));

$info_box->add_button(
    Button::create(
        __('Wszystko jest w porządku', BPMJ_EDDCM_DOMAIN),
        Button::TYPE_MAIN
    )
        ->add_data('loading', __('Działam', BPMJ_EDDCM_DOMAIN) . '...')
        ->add_class('hide-new-templates-info')
);
$info_box->add_button(
    Button::create(
        __('Przywróć poprzedni system szablonów', BPMJ_EDDCM_DOMAIN),
        Button::TYPE_SECONDARY
    )
        ->add_data('loading', __('Przywracam', BPMJ_EDDCM_DOMAIN) . '...')
        ->add_class('disable-new-templates-system')
);

$info_box->print_html();
