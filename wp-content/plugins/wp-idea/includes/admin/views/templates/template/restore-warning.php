<?php
use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Link;
use bpmj\wpidea\View;

/** @var string $proceed_action */
/** @var View $view */

?>

<div class="wpi-popup__core restore-warning-content">
    <h1><?= __('Are you sure you want to restore the template?', BPMJ_EDDCM_DOMAIN) ?></h1>
</div>

<div class="wpi-popup__footer">
    <?php
    Button::create(__('Cancel', BPMJ_EDDCM_DOMAIN), Button::TYPE_SECONDARY)
        ->add_class('restore-cancel-button')
        ->close_popup_on_click()
        ->print_html();

    Link::create(__('Restore', BPMJ_EDDCM_DOMAIN), $proceed_action)
        ->add_class('restore-template-confirm')
        ->add_class('wpi-button')
        ->add_data('loading', __('Restoring', BPMJ_EDDCM_DOMAIN) . '...')
        ->print_html();
    ?>
</div>