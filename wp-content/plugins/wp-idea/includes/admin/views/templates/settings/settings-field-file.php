<?php
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field_File;

/** @var Template_Group_Settings_Field_File $field */
?>

<label for="<?= $field->get_name() ?>">
    <?= $field->get_label() ?>
</label>

<div class="file-inputs-wrapper">
    <input type="text"
           class="regular-text wp_idea-layout-template-settings-scarlet-url"
           id="<?= $field->get_name() ?>"
           name="<?= $field->get_name() ?>"
           value="<?= $field->get_value() ?>">

    <input type="button"
           class="button wpi-browse-media-library-button"
           data-input-name="<?= $field->get_name() ?>"
           value="<?= __('Choose File', BPMJ_EDDCM_DOMAIN) ?>">
</div>