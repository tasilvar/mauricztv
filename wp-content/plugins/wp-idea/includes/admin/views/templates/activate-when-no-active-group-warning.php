<?php
use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Link;
use bpmj\wpidea\templates_system\admin\renderers\Template_Groups_Page_Renderer;
use bpmj\wpidea\View;

/** @var string $proceed_action */
/** @var View $view */
/** @var string $no_active_group_warning */
?>

<div class="wpi-popup__core activate-template-warning-content">
    <h1><?= __('Are you sure you want to activate this template?', BPMJ_EDDCM_DOMAIN) ?></h1>
    {no_active_group_warning}
</div>

<div class="wpi-popup__footer">
    <?php
    Button::create(__('Cancel', BPMJ_EDDCM_DOMAIN), Button::TYPE_SECONDARY)
        ->add_class('activate-cancel-button')
        ->close_popup_on_click()
        ->print_html();

    Link::create(__('Activate', BPMJ_EDDCM_DOMAIN), $proceed_action)
        ->add_class('activate-template-confirm')
        ->add_class('wpi-button')
        ->add_data('loading', __('Activating', BPMJ_EDDCM_DOMAIN) . '...')
        ->print_html();
    ?>
</div>