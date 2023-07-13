<?php

use \bpmj\wpidea\admin\helpers\html\Button;

/** @var Interface_Translator $translator */
?>



<div class="media-warning-content">
    <h2><?= $translator->translate('video_uploader.storage_not_enough_space.popup.title') ?></h2>
    <div class="media-warning-content-replace"></div>
</div>

<div class="wpi-popup__footer">
    <?php
    Button::create($translator->translate('media.video_format_blocker.understand'), Button::TYPE_SECONDARY)
        ->add_class('media-limit-cancel-button')
        ->close_popup_on_click()
        ->print_html();
    ?>
</div>
