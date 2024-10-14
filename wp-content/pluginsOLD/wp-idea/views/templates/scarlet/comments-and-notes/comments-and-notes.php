<?php
/* @var Interface_View_Provider $view */

/* @var Interface_Translator $translator */
/** @var bool $display_comments */
/** @var bool $display_notes_tab */
/** @var ?int $module_id */

/** @var ?int $lesson_id */

use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

$notes_tab_active = !$display_comments;
$notes_class = $notes_tab_active ? 'active' : '';
?>

<div class='tabs'>
    <div class='tab'>
        <ul>
            <?php
            if ($display_comments) : ?>
                <li id='tab_komentarze' class='active'><p><?php
                        _e('Comments', BPMJ_EDDCM_DOMAIN); ?></p></li>
            <?php
            endif;

            if ($display_notes_tab) : ?>
                <li id='tab_notatki' class="<?= $notes_class ?>"><p><?= $translator->translate('blocks.notes.tab_title') ?></p></li>
            <?php
            endif;
            ?>
        </ul>
    </div>
</div>

<?php
if ($display_comments) : ?>
    <?php comments_template(); ?>
<?php
endif;

if ($display_notes_tab) : ?>
    <?= $view->get('notes-tab', [
        'active' => $notes_tab_active,
        'module_id' => $module_id,
        'lesson_id' => $lesson_id,
        'translator' => $translator
    ]) ?>
<?php
endif;
?>
