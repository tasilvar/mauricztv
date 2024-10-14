<?php
use bpmj\wpidea\admin\helpers\html\Button;

/** @var Interface_Translator $translator */
?>

<div class="media-warning-content">
    <h2><?= $translator->translate('media.limit_checker.title') ?></h2>
    <?= $translator->translate('media.limit_checker.error') ?>
</div>

<div class="wpi-popup__footer">
    <?php
    Button::create($translator->translate('understand'), Button::TYPE_SECONDARY)
        ->add_class('media-limit-cancel-button')
        ->close_popup_on_click()
        ->print_html();
    ?>
</div>