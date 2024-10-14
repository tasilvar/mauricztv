<?php

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Info_Box;

$info_box = Info_Box::create(__('Nowy system szablonów', BPMJ_EDDCM_DOMAIN));

$info_box->add_class('templates-guide-info-box');
$info_box->set_size(Info_Box::SIZE_SMALL);

$info_box->add_paragraph(__('Klikając w poniższy przycisk, możesz przełączyć się na nowy system szablonów WP Idea. Już niebawem stanie się on podstawowym systemem zarządzania układem elementów na platformie. <b>Przetestuj go już teraz</b>.', BPMJ_EDDCM_DOMAIN));
$info_box->add_paragraph(__('Pamiętaj, że w razie problemów możesz cofnąć się do obecnych ustawień. Zapoznaj się również ze specjalnie na tę okazję przygotowanym artykułem (<a href="https://wpidea.pl/nowy-system-szablonow-wp-idea" target="_blank">link</a>).', BPMJ_EDDCM_DOMAIN));
$info_box->add_paragraph(__('Do dzieła!', BPMJ_EDDCM_DOMAIN));

$info_box->add_button(
    Button::create(
        __('Przejdź na nowe szablony', BPMJ_EDDCM_DOMAIN),
        Button::TYPE_MAIN
    )
    ->add_data('loading', __('Włączam nowe szablony', BPMJ_EDDCM_DOMAIN) . '...')
    ->add_class('enable-new-templates-system')
);

$info_box->print_html();
