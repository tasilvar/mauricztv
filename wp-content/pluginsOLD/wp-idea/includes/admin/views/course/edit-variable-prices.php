<?php
use bpmj\wpidea\translator\Interface_Translator;

/** @var string $product_id */
/** @var Interface_Translator $translator */
?>

<script>
    jQuery(document).ready(function ($) {
        $('.edd_add_repeatable').html('<?= $translator->translate('settings.popup.button.add_new_variant') ?>');
    });
</script>

<?php
edd_render_price_field( $product_id );
?>

<p class="bpmj-eddcm-save-variable-prices-div footer_variable_prices">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>

    <button type="button" class="wpi-button wpi-button--secondary" data-action="close-modal" data-id="bpmj-eddcm-variable-prices-modal">
        <?= $translator->translate('settings.popup.button.cancel') ?>
    </button>

    <button type="button" class="wpi-button wpi-button--main" data-action="save-variable-prices">
        <?= $translator->translate('settings.popup.button.save') ?>
    </button>

</p>


