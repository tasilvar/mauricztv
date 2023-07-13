<?php
/** @var string $id */
/** @var string $classes */
/** @var string $content */
/** @var string $timeout */
/** @var bool $auto_open */
/** @var bool $show_close_button */
?>

<div id="<?= $id ?>" <?= ($timeout) ? 'data-timeout="'.$timeout.'"' : '' ?> data-type="standard-popup" <?= ($auto_open) ? '' : 'style="display: none;"' ?> class="wpi-popup wpi-popup--standard <?= $classes ?> <?= ($auto_open) ? 'open' : '' ?>">
    <div class="wpi-popup__back_overlay"></div>
    <div class="wpi-popup__content">
        <?php if($show_close_button) { ?>
            <div class="wpi-popup__close" data-close-popup-on-click><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>
        <?php } ?>
        <?= $content ?>
    </div>
</div>
