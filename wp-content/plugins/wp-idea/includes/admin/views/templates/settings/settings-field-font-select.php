<?php

use bpmj\wpidea\admin\helpers\fonts\Fonts_Helper;
use bpmj\wpidea\templates_system\admin\ajax\Group_Settings_Ajax_Handler;
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field_Font_Select;

/** @var Template_Group_Settings_Field_Font_Select $field */
?>
<label for="<?= $field->get_name() ?>" class="wpi-label wpi-label--for-select">
    <?= $field->get_label() ?>
</label>
<select
        style="width: 300px;"
        name="<?= $field->get_name() ?>"
        id="<?= $field->get_name() ?>"
        class="wpi-select font_select <?= $field->get_name() ?>"
        data-get-fonts-ajax-action="<?= $field->get_ajax_get_options_action() ?? '' ?>"
        data-nonce-field="<?= Group_Settings_Ajax_Handler::get_nonce_for_field_options() ?>">

    <?php if($field->get_value()): ?>
        <option value="<?= $field->get_value() ?>" selected="selected"><?= Fonts_Helper::get_google_font_name_by_slug($field->get_value()) ?></option>
    <?php endif; ?>
</select>

