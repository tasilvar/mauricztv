<?php
/** @var ?int $module_id */
/** @var ?int $lesson_id */
/* @var \bpmj\wpidea\translator\Interface_Translator $translator */

$active = $active ?? false;
$display = $active ? 'block' : 'none';
?>

<template id="spinner-template">
    <div class='loader'></div>
</template>

<template id="before-delete-message-template"><?= $translator->translate('blocks.notes.delete_note_prompt') ?></template>

<div id='tab_cont_notatki' class='tab_cont tab_notatki lesson-notes-tab' style='display: <?= $display ?>;'>
    <div class="notes-tab-loader">
        <h3><?= $translator->translate('blocks.notes.note_content') ?></h3>
        <div class='loader'></div>
    </div>

    <div class="edit-note-form" style="display: none">
        <form class="add-note">
            <label for="lesson_note"><?= $translator->translate('blocks.notes.note_content') ?></label>
            <textarea required name="lesson_note" id="lesson_note" rows="10" data-lesson-id="<?= $lesson_id ?>" data-module-id="<?= $module_id ?>"></textarea>

            <div class='buttons-row'>
                <button type="submit"><?= $translator->translate('blocks.notes.save_note') ?></button>
            </div>
        </form>
    </div>

    <div class='note-preview' style='display: none'>
        <h3><?= $translator->translate('blocks.notes.note_content') ?></h3>
        <div class='note-content'></div>

        <div class="buttons-row">
            <button type='button' class='edit-note-button'><?= $translator->translate('blocks.notes.edit_note') ?></button>
            <button type='button' class='delete-note-button'><?= $translator->translate('blocks.notes.delete_note') ?></button>
        </div>
    </div>
</div>
