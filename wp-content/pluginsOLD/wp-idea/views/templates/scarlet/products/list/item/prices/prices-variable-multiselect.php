<?php
use bpmj\wpidea\infrastructure\system\System;
?>

<div class="box_glowna_wariant">
    <div class="fake-select">
        <?php _e( 'Selected variants', BPMJ_EDDCM_DOMAIN ); ?>:&nbsp;<span>2</span>
    </div>
    <select class="edd_options_price_id_multi" name="edd_options[price_id]" title="<?php esc_attr_e( 'Select a variant', BPMJ_EDDCM_DOMAIN ); ?>" multiple="multiple" data-download="<?= $productId ?>">
        <?php
        $checked_key = isset( $_GET[ 'price_option' ] ) ? absint( $_GET[ 'price_option' ] ) : $defaultPriceId; //@todo: variant id == price key??
            foreach ( $variants as $key => $variant ) :
                if ($variant->purchaseLimitExhausted())
                    $checked_key = null;
                ?>
                <option id="edd_price_option_<?= $productId . '_' . sanitize_key( $variant->getName() ); ?>"
                        value="<?= esc_attr( $variant->getId() ); ?>"
                        <?php selected( $variant->getId(), $checked_key ); ?>
                        <?= $variant->purchaseLimitExhausted() ? ' disabled' : ''; ?>>
                    <?= $variant->getName() ?> <?= $variant->getPrice() ?> <?= System::get_currency() ?>
                </option><?php

            endforeach;
        ?>
    </select>
    <div class="edd_options_price_id_multi_hidden_checkboxes">
        <?php foreach ( $variants as $key => $variant ) : ?>
            <input <?php checked( $variant->getId(), $checked_key ); ?> type="checkbox" name="" class="edd_price_option_<?= $productId ?> edd_price_option_hidden_checkbox_<?= $productId ?>_<?= $variant->getId(); ?> edd_price_option_hidden_checkbox" value="<?= esc_attr( $variant->getId() ); ?>">
        <?php endforeach; ?>
    </div>
</div>