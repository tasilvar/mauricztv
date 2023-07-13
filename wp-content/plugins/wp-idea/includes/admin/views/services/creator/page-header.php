<?php

use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\translator\Interface_Translator;

/** @var Interface_Translator $translator */
?>

<div class='row'>
    <div class='heading animated fadeInDown'>
        <?= $translator->translate('services.creator.title') ?>
        <span class="settings-page"><?php _e(Software_Variant::get_name(), BPMJ_EDDCM_DOMAIN); ?></span>
    </div>
</div>