<?php
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field;

/** @var Template_Group_Settings_Field $field */
?>

<label for="<?= $field->get_name() ?>">
    <?= $field->get_label() ?>
</label>

<div class="textarea-wrapper">
    <textarea name="<?= $field->get_name() ?>" id="<?= $field->get_name() ?>" rows="6"><?= $field->get_value() ?? $field->get_default_value() ?></textarea>
</div>

