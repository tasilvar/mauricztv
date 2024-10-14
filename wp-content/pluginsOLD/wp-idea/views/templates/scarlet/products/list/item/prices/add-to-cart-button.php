<?php

use bpmj\wpidea\sales\product\Custom_Purchase_Links_Helper;
use bpmj\wpidea\View_Hooks;
use bpmj\wpidea\wolverine\product\Product;

/** @var Product $product */
/** @var bool $isAjaxPurchaseEnabled */

    $data_variable = $product->hasVariants() ? ' data-variable-price="yes"' : 'data-variable-price="no"';
    $type = $product->hasPriceModeMulti() ? 'data-price-mode=multi' : 'data-price-mode=single';

    if ($product->getIsInCart()) {
        $button_display   = 'style="display:none;"';
        $checkout_display = '';
    } else {
        $button_display   = '';
        $checkout_display = 'style="display:none;"';
    }

$custom_purchase_link = Custom_Purchase_Links_Helper::get_custom_purchase_link_as_string($product->getId());
?>
    <div class="edd_purchase_submit_wrapper box_glowna_add_to_cart">
        <?php
        $button_text = edd_get_option( 'add_to_cart_text', __( 'Purchase', 'easy-digital-downloads' ) ); //@todo: wyciagnac stad edd_get_option
        if ( $isAjaxPurchaseEnabled ): ?>
            <a href="<?= ($custom_purchase_link) ?: '#' ?>" <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_HYPERLINK, $product->getId()) ?>
               <?= !$custom_purchase_link ? 'class="edd-add-to-cart"' : 'target="_blank" rel="noindex nofollow"' ?>
               data-action="edd_add_to_cart" data-download-id="<?= esc_attr( $product->getId() ) ?>" <?= $data_variable . ' ' . $type . ' ' . $button_display ?>>
                <span class="edd-add-to-cart-label">
                    <i class="<?= $product->salesDisabled() ? 'hidden' : 'icon-cart' ?>"></i>
                    <?= $product->salesDisabled() ? __('Sales disabled', BPMJ_EDDCM_DOMAIN) : $button_text ?>
                </span> <span class="edd-loading"><i class="icon-hourglass icon-spin"></i></span></a>

        <?php
        endif; ?>

        <button type="submit" <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_HYPERLINK, $product->getId()); ?>  class="edd-add-to-cart edd-no-js " name="edd_purchase_download" data-action="edd_add_to_cart" data-download-id="<?= esc_attr( $product->getId() ) ?>" <?= $data_variable ?> <?= $type ?> <?= $button_display ?>><?= $button_text ?></button>
        <?php echo '<a href="' . esc_url( edd_get_checkout_uri() ) . '" class="edd_go_to_checkout" ' . $checkout_display . '>' . __( 'Checkout', 'easy-digital-downloads' ) . '</a>';
        ?>

        <?php if ( $isAjaxPurchaseEnabled ) : ?>
            <span class="edd-cart-ajax-alert" aria-live="assertive">
                <span class="edd-cart-added-alert" style="display: none;">
                    <?= '<i class="edd-icon-ok" aria-hidden="true"></i> ' . __( 'Added to cart', 'easy-digital-downloads' ); ?>
                </span>
            </span>
        <?php endif; ?>
    </div><!--end .edd_purchase_submit_wrapper-->

    <input type="hidden" name="prod$productId" value="<?= esc_attr( $product->getId() ); ?>">

    <?php if ( $product->hasVariants() && isset( $price_id ) && isset( $prices[ $price_id ] ) ): /* @todo: where these $prices and $price_id variables come from? */?>
        <input type="hidden" name="edd_options[price_id][]"
                id="edd_price_option_<?= $product->getId(); ?>_1"
                class="edd_price_option_<?= $product->getId(); ?>"
                value="<?= $price_id; ?>">
    <?php endif; ?>

    <input type="hidden" name="edd_action" class="edd_action_input" value="add_to_cart">

    <?php if ($product->getGoStraightToCheckoutModeEnabled()) : ?>
        <input type="hidden" name="edd_redirect_to_checkout"
            id="edd_redirect_to_checkout" value="1">
    <?php endif; ?>
