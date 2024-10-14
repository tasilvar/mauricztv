<?php
use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\templates_system\admin\ajax\Group_Settings_Ajax_Handler;
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field;
use bpmj\wpidea\templates_system\groups\settings\Template_Group_Settings_Fields;
use bpmj\wpidea\View;

/** @var Template_Group_Settings_Fields $fields */
/** @var string $title */
/** @var string $group_id */
/** @var View $view */

$group_id_param_name = Group_Settings_Ajax_Handler::GROUP_ID_PARAM_NAME;
$form_id = 'bpmj_template_group_settings_' . $group_id;
?>
<div class="group-settings-popup">
    <h1>{title}</h1>

    <div class="wpi-template-group-settings-fields">
        <form id="<?= $form_id ?>" class="wpi_group_settings_form">
            <input type="hidden" name="<?= $group_id_param_name ?>" id="<?= $group_id_param_name ?>" value="{group_id}">
            <?php Group_Settings_Ajax_Handler::echo_nonce_field_for_group($group_id) ?>

            <?php /** @var Template_Group_Settings_Field $field */
            foreach ($fields as $field): ?>
                <div class="wpi-template-group-settings-fields__field">
                    <?= $field->get_html() ?>
                </div>
            <?php endforeach; ?>

            <input type="submit" hidden>
        </form>
    </div>

    <div class="wpi-popup__footer">
        <?php
        Button::create(__('Cancel', BPMJ_EDDCM_DOMAIN), Button::TYPE_SECONDARY)
            ->add_class('template-group-settings-cancel-button')
            ->print_html();

        Button::create(__('Save settings', BPMJ_EDDCM_DOMAIN), Button::TYPE_MAIN)
            ->add_class('template-group-settings-save-button')
            ->add_data('form-id', $form_id)
            ->add_data('loading', __('Saving', BPMJ_EDDCM_DOMAIN) . '...')
            ->add_data('success', __('Settings successfully saved!', BPMJ_EDDCM_DOMAIN))
            ->print_html();
        ?>
    </div>
</div>