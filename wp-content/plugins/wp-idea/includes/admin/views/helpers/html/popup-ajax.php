<?php
/** @var string $id */
/** @var string $action */
/** @var string $timeout */
/** @var array $ajax_params */
/** @var array $classes */
/** @var bool $auto_open */
/** @var bool $show_close_button */
?>

<div id="<?= $id ?>" <?= ($timeout) ? 'data-timeout="'.$timeout.'"' : '' ?> data-action="<?= $action ?>" data-loading="<?= __('Loading', BPMJ_EDDCM_DOMAIN) ?>..." data-params='<?= json_encode($ajax_params) ?>' data-type="ajax-popup" <?= ($auto_open) ? '' : 'style="display: none;"' ?> class="wpi-popup wpi-popup--ajax <?= $classes ?> <?= ($auto_open) ? 'open' : '' ?>">
    <div class="wpi-popup__back_overlay"></div>
    <div class="wpi-popup__content">
        <?php if($show_close_button) { ?>
            <div class="wpi-popup__close dashicons dashicons-no" data-close-popup-on-click></div>
        <?php } ?>
    </div>
</div>
