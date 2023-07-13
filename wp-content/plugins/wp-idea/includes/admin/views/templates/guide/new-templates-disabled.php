<?php

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Info_Box;

$info_box = Info_Box::create(__('Poprzedni system szablonów został przywrócony :)', BPMJ_EDDCM_DOMAIN));

$info_box->add_class('templates-guide-info-box');
$info_box->set_size(Info_Box::SIZE_SMALL);

$info_box->add_paragraph(__('Przykro nam, że pojawił się problem. Na pewno znajdziemy jakieś rozwiązanie i pomożemy Ci w tej sytuacji. Pamiętaj, żeby wysłać nam wiadomość z informacjami o napotkanych błędach. Przeanalizujemy całą sytuację i zaproponujemy najlepsze rozwiązanie, abyś mógł/mogła ponownie przetestować nowy system szablonów.', BPMJ_EDDCM_DOMAIN));

$info_box->add_paragraph(__('Gdy wszystko będzie już w porządku, wystarczy, że klikniesz poniższy przycisk.', BPMJ_EDDCM_DOMAIN));

$info_box->add_button(
    Button::create(
        __('Przejdź na nowe szablony', BPMJ_EDDCM_DOMAIN),
        Button::TYPE_MAIN
    )
        ->add_data('loading', __('Włączam nowe szablony', BPMJ_EDDCM_DOMAIN) . '...')
        ->add_class('enable-new-templates-system')
);

$info_box->print_html();
