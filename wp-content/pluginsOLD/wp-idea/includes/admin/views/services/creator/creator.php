<?php
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

/** @var Interface_View_Provider $view */
/** @var Interface_Translator $translator */
/** @var string $services_page_url */
/** @var array $integrations */

?>

<form class='edd-courses-manager-creator-steps-form'
    data-mode='service'
    data-see-item-list-button-url="<?= $services_page_url ?>"
    data-edit-item-button-label="<?= $translator->translate('services.edit_service') ?>"
    data-saving-in-progress-message="<h3><?= $translator->translate('services.creator.saving_in_progress') ?></h3>"
    data-saving-in-progress-popup-title="<?= $translator->translate('services.creator.saving_in_progress.title') ?>"
    data-saving-complete-message="<h3><?= $translator->translate('services.creator.save.success') ?></h3>"
>

    <?= $view->get('steps/step-one', [
        'next_step_label' => !empty($integrations) ? $translator->translate('services.creator.step_button.configure_integration') : $translator->translate('services.creator.step_button.save_service'),
        'translator' => $translator,
    ]) ?>

    <?= $view->get('steps/step-two', [
        'has_integrations' => !empty($integrations)
    ]) ?>

</form>