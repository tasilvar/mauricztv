<?php
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\View_Hooks;

/** @var array $variants */
/** @var int $defaultPriceId */
/** @var int $productId */
?>

<div class="box_glowna_wariant">
    <select name="edd_options[price_id]" title="<?php esc_attr_e( 'Select a variant', BPMJ_EDDCM_DOMAIN ); ?>">
        <?php
        $checked_key = isset( $_GET[ 'price_option' ] ) ? absint( $_GET[ 'price_option' ] ) : $defaultPriceId;
        $next_checked_key = false;
        foreach ( $variants as $key => $variant ) :
            if ( $next_checked_key ) {
                $checked_key = $variant->getId();
                $next_checked_key = false;
            }

            if ( $variant->purchaseLimitExhausted() ) {
                $checked_key = null;
                $next_checked_key = true;
            }
            ?>
            <option id="edd_price_option_<?= $productId . '_' . sanitize_key( $variant->getName() ); ?>"

                <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_VARIANT_OPTIONS, ['product_id' => $productId, 'variant' => esc_attr( $variant->getId() )]) ?>
                    value="<?= esc_attr( $variant->getId() ); ?>"
                    <?php selected( $variant->getId(), $checked_key ); ?>
                    <?= $variant->purchaseLimitExhausted() ? ' disabled' : ''; ?>>

                    <?= $variant->getName() ?> <?= $variant->getPrice() ?> <?= System::get_currency() ?>
            </option><?php
        endforeach;
        ?>
    </select>
    <input type="hidden" name="" class="edd_price_option_<?= $productId; ?>"
            value="<?= esc_attr( $checked_key ); ?>"/>
</div><!--end .edd_price_options-->
