<?php
use bpmj\wpidea\admin\helpers\html\Button;

/** @var Interface_Translator $translator */
/** @var string $video_page_url */
?>



<div class="media-warning-content">
    <h2><?= $translator->translate('media.video_format_blocker.title') ?></h2>
    <?= sprintf($translator->translate('media.video_format_blocker.error'), '<a href="'.$video_page_url.'">', '</a>') ?>
</div>

<div class="wpi-popup__footer">
    <?php
    Button::create($translator->translate('media.video_format_blocker.understand'), Button::TYPE_SECONDARY)
        ->add_class('media-limit-cancel-button')
        ->close_popup_on_click()
        ->print_html();
    ?>
</div>