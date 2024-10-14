<?php

use bpmj\wpidea\certificates\Certificate_Template;

?>
<div class="wpi-popup__core">
    <h1><?= __('Are you sure?', BPMJ_EDDCM_DOMAIN) ?></h1>

    <p><?= __('Are you sure you want to switch to the new certificate templates? There will be no going back.', BPMJ_EDDCM_DOMAIN) ?></p>
</div>

<div class="wpi-popup__footer">
    <a class="wpi-button wpi-button--main enable-new-certyficates-button enable-new-certyficates-button--enable-now" href="<?= Certificate_Template::get_enable_new_version_url() ?>" ><?= __('Yes, enable new certificates ', BPMJ_EDDCM_DOMAIN) ?></a>
    <a class="wpi-button wpi-button--secondary enable-new-certyficates-button enable-new-certyficates-button--not-now" href="#" data-close-popup-on-click><?= __('Not now', BPMJ_EDDCM_DOMAIN) ?></a>
</div>