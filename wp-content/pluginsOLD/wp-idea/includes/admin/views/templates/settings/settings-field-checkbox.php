<?php
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field_Checkbox;

/** @var Template_Group_Settings_Field_Checkbox $field */
?>

<label for="<?= $field->get_name() ?>">
    <?= $field->get_label() ?>
    <?php if($field->get_hint() !== null): ?>
        <span class="hint"><?= $field->get_hint() ?></span>
    <?php endif; ?>
</label>
<input type="checkbox" name="<?= $field->get_name() ?>" id="<?= $field->get_name() ?>"
       <?= $field->is_checked() ? ' checked' : '' ?>>


