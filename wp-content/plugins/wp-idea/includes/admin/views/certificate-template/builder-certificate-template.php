<?php

use bpmj\wpidea\certificates\Certificate_Template;
use bpmj\wpidea\nonce\Nonce_Handler;

/** @var string $url */
/** @var string $certificate */
/** @var string $certificate_template_action */


?>
<div class="certificate-builder-wrapper">
    <form id="certificate-form" action="<?= $url ?>" method="post">
    <div class="certificate-builder-wrapper__top-bar">
        <div class="certificate-builder-wrapper__top-bar__column certificate-builder-wrapper__top-bar__column--left">
            <label style="font-weight: 700; font-size: 15px;" for="certificate-name"><?= __( 'Certificate template name', BPMJ_EDDCM_DOMAIN); ?></label>
            <input name="name"  id="certificate-name" class="bold"
                   type="text" value="<?= ($certificate) ? $certificate->get_name() : __( 'My certificate template', BPMJ_EDDCM_DOMAIN); ?>"/>
        </div>
        <div class="certificate-builder-wrapper__top-bar__column certificate-builder-wrapper__top-bar__column--right">

        </div>
    </div>
        <?php if($certificate){ ?>
            <input type="hidden" name="id" value="<?= $certificate->get_id() ?>">
        <?php } ?>

        <input type="hidden" name="action" value="wpi_handler">
        <input type="hidden" name="certificate_template_action" value="<?= $certificate_template_action ?>">
        <input type="hidden" name="<?= Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME ?>" value="<?= Nonce_Handler::create() ?>">

        <textarea style="display: none" id="certificate-builder" name="page"><?= ($certificate) ? esc_html( $certificate->get_page()) : '' ?></textarea>
    </form>
</div>
