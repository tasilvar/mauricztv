<template id="diagnostics_data__table">
<?php foreach ( $support->get_diagnostics_data()->get_all() as $item ) : ?>
<?= $item->get_label(); ?>: <?= $item->get_value(); ?><br>
<?php endforeach; ?>
</template>

<div class="copy-diagnostic-button-wrapper">
    <p><?= __( 'Copy diagnostic and configuration data to clipboard', BPMJ_EDDCM_DOMAIN ) ?></p>
    <a href="#" type="button" class="btn-eddcm btn-eddcm-primary btn-eddcm-big animated fadeIn">
        <?= __('Copy data', BPMJ_EDDCM_DOMAIN ) ?>
    </a>
    <p class="diagnostic-data-copied"><?= __( 'Copied!', BPMJ_EDDCM_DOMAIN ); ?></p>
</div>