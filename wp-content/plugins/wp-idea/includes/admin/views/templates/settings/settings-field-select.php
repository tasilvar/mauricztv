<?php
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field_Select;

/** @var Template_Group_Settings_Field_Select $field */
?>

<label for="<?= $field->get_name() ?>">
    <?= $field->get_label() ?>
    <?php if($field->get_hint() !== null): ?>
        <span class="hint"><?= $field->get_hint() ?></span>
    <?php endif; ?>
</label>

<select name="<?= $field->get_name() ?>" id="<?= $field->get_name() ?>">
    <?php foreach ($field->get_options() as $value => $label): ?>
        <option value="<?= $value ?>" <?= (string)$value === (string)$field->get_value() ? 'selected' : '' ?>><?= $label ?></option>
    <?php endforeach ?>
</select>


