<?php
/** @var string $currency */
/** @var bool $has_integrations */
?>

<?php if ( !$has_integrations ) { return; } ?>

<section class="step-four integrations" style="display: none">
    <div class="row">
        <div class="container">

			<?php if ( WPI()->diagnostic->invoice_integration() ) { ?>
                <div class="panel" style="margin-bottom: 20px;">
                    <div class="panel-heading">
                        <?php _e( 'Invoices', BPMJ_EDDCM_DOMAIN ); ?>
                    </div>

                    <div class="panel-body">
                        <?php $editor->metabox_invoice_settings(); ?>
                    </div>
                </div>
            <?php } ?>


            <?php if ( WPI()->diagnostic->mailer_integration() ) { ?>
                <div class="panel">
                    <div class="panel-heading" style="margin-bottom: 0px;">
                        <?php _e( 'Mailers', BPMJ_EDDCM_DOMAIN ); ?>
                    </div>

                    <div class="panel-body" style="padding-top: 0px;">
                        <?php $editor->metabox_mailer_settings(); ?>
                    </div>
                </div>
            <?php } ?>


        </div>
    </div>
</section>