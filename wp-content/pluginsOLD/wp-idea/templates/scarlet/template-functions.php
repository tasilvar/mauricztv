<?php

use bpmj\wpidea\sales\product\Custom_Purchase_Links_Helper;
use bpmj\wpidea\View_Hooks;

function bpmj_eddcm_get_course_cats_and_tags()
{
    echo '<div class="box_glowna_kategorie_wrapper">';
    do_action( 'edd_download_after_title' );
    echo '</div>';
}

function bpmj_eddcm_get_course_excerpt($atts){
    if ( $atts[ 'excerpt' ] == 'yes' ) {
        add_filter('excerpt_length', function(){ return 9; });
        edd_get_template_part( 'shortcode', 'content-excerpt' );
        do_action( 'edd_download_after_content' );
    }
}

function bpmj_eddcm_get_course_prices($atts, $download){
    $product_id        = $download->id;
    $sale_price       = get_post_meta( $product_id, 'edd_sale_price', true );
    $variable_pricing = $download->has_variable_prices();
    $edd_item_in_cart = edd_item_in_cart( $download->ID, array() ) && ( ! $variable_pricing || ! $download->is_single_price_mode() );
    $custom_purchase_link = Custom_Purchase_Links_Helper::get_custom_purchase_link_as_string($product_id);

    $course            = WPI()->courses->get_course_by_product( $product_id );
    $show_open_padlock = false;
    $sales_disabled    = false;
    if ( false === $course ) {
        $product_type = get_post_meta( $product_id, '_edd_product_type', true );
        $sales_status = WPI()->courses->get_sales_status( $product_id, $product_id );

        if ( 'bundle' == $product_type && 'disabled' === $sales_status[ 'status' ] ) {
            $sales_disabled = true;
        }
    } else {
        $course_page_id = get_post_meta( $course->ID, 'course_id', true );
        $restricted_to  = array( array( 'download' => $product_id ) );
		$user_id = get_current_user_id();
        $access         = bpmj_eddpc_user_can_access( $user_id, $restricted_to, $course_page_id );
        if ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] ) {
            $show_open_padlock = true;
        }
        $sales_status = WPI()->courses->get_sales_status( $course->ID, $product_id );
        if ( 'disabled' === $sales_status[ 'status' ] ) {
            $sales_disabled = true;
        }
    }
    ?>
    <div class="col-sm-3
    <?php if ( ! $edd_item_in_cart && ! $show_open_padlock && edd_has_variable_prices( $product_id ) ): echo 'warianty'; endif; ?>
    <?php if ( ! $edd_item_in_cart && ! $show_open_padlock && ! empty( $sale_price ) ): echo 'promocja'; endif; ?>">
        <form class="edd_download_purchase_form edd_purchase_<?php echo absint( $product_id ); ?> <?php if ( $sales_disabled && ! $show_open_padlock ): ?>edd-sales-disabled<?php endif; ?>"

            <?php if ( $sales_disabled ): ?>
                data-eddcm-sales-disabled-reason="<?php echo esc_attr( $sales_status[ 'reason' ] ) ?>" data-eddcm-sales-disabled-reason-long="<?php echo esc_attr( $sales_status[ 'reason_long' ] ) ?>"
            <?php endif; ?>
                method="post">
            <?php

            if ( $show_open_padlock ):
                $links = get_post_meta( $product_id, 'edd_download_files', true );
                $link = is_array( $links ) ? array_shift( $links ) : array();
                ?>
                <div class="glowna_box_cena glowna_box_cena_dostepny">
                    <p class="glowna_box_cena_dostepny_opis">
                        <span>
                            <?php _e( 'AVAILABLE', BPMJ_EDDCM_DOMAIN ) ?>
                        </span>
                        <i class="icon-unlocked-inverted"></i>
                </div>
                <?php if ( ! empty( $link[ 'file' ] ) ): ?>
                <div class="box_glowna_add_to_cart">
                    <a href="<?php echo $link[ 'file' ]; ?>"><i
                                class="fa fa-arrow-right"></i><?php _e( 'GO TO COURSE', BPMJ_EDDCM_DOMAIN ) ?>
                    </a>
                </div>
            <?php endif; ?>
            <?php
            else:
                if ( $atts[ 'buy_button' ] == 'yes' ):
                    bpmj_eddcm_scarlet_variable_prices( $product_id );
                endif;

                if ( $atts[ 'price' ] == 'yes' ) {
                    edd_price( $product_id );
                    do_action( 'edd_download_after_price' );
                }

                if ( $atts[ 'buy_button' ] == 'yes' ):
                    $data_variable = $variable_pricing ? ' data-variable-price="yes"' : 'data-variable-price="no"';
                    $type = $download->is_single_price_mode() ? 'data-price-mode=multi' : 'data-price-mode=single';

                    if ( $edd_item_in_cart ) {
                        $button_display   = 'style="display:none;"';
                        $checkout_display = '';
                    } else {
                        $button_display   = '';
                        $checkout_display = 'style="display:none;"';
                    }

                    ?>

                    <div class="edd_purchase_submit_wrapper box_glowna_add_to_cart">
                        <?php
                        $button_text = edd_get_option( 'add_to_cart_text', __( 'Purchase', 'easy-digital-downloads' ) );
                        if ( ! edd_is_ajax_disabled() ): ?>
                            <a href="<?= ($custom_purchase_link)  ? $custom_purchase_link : '#' ?>" <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_HYPERLINK, $product_id) ?>
                               <?= ($custom_purchase_link) ? 'target="_blank" rel="noindex nofollow"' : 'class="edd-add-to-cart"' ?>
                               data-action="edd_add_to_cart" data-download-id="<?php echo esc_attr( $download->ID ) . '" ' . $data_variable . ' ' . $type . ' ' . $button_display . '>
                            <span class="edd-add-to-cart-label">
                                <i class="' . ($sales_disabled ? 'fas fa-times' : 'icon-cart') . '"></i>'
                                . $button_text . '
                            </span>
                            <span class="edd-loading"><i class="icon-hourglass icon-spin"></i></span></a>';
                        endif;
                        echo '<button type="submit"  class="edd-add-to-cart edd-no-js " name="edd_purchase_download" data-action="edd_add_to_cart" data-download-id="' . esc_attr( $download->ID ) . '" ' . $data_variable . ' ' . $type . ' ' . $button_display . '>' . $button_text . '</button>';
                        echo '<a href="' . esc_url( edd_get_checkout_uri() ) . '" class="edd_go_to_checkout" ' . $checkout_display . '>' . __( 'Checkout', 'easy-digital-downloads' ) . '</a>';
                        ?>

                        <?php if ( ! edd_is_ajax_disabled() ) : ?>
                            <span class="edd-cart-ajax-alert" aria-live="assertive">
                                <span class="edd-cart-added-alert" style="display: none;">
                                    <?php echo '<i class="edd-icon-ok" aria-hidden="true"></i> ' . __( 'Added to cart', 'easy-digital-downloads' ); ?>
                                </span>
                            </span>
                        <?php endif; ?>
                    </div><!--end .edd_purchase_submit_wrapper-->

                    <input type="hidden" name="prod$product_id"
                            value="<?php echo esc_attr( $download->ID ); ?>">
                    <?php if ( $variable_pricing && isset( $price_id ) && isset( $prices[ $price_id ] ) ): ?>
                    <input type="hidden" name="edd_options[price_id][]"
                            id="edd_price_option_<?php echo $download->ID; ?>_1"
                            class="edd_price_option_<?php echo $download->ID; ?>"
                            value="<?php echo $price_id; ?>">
                <?php endif; ?>
                    <input type="hidden" name="edd_action" class="edd_action_input"
                            value="add_to_cart">

                    <?php if ( apply_filters( 'edd_download_redirect_to_checkout', edd_straight_to_checkout(), $download->ID, array() ) ) : ?>
                    <input type="hidden" name="edd_redirect_to_checkout"
                            id="edd_redirect_to_checkout" value="1">
                <?php endif; ?>

                    <?php do_action( 'edd_purchase_link_end', $download->ID, array() ); ?>

                <?php
                endif;
            endif;

            do_action( 'edd_download_after' );

            ?>
        </form>
    </div>
<?php
}

function bpmj_eddcm_get_course_page_prices( $download, $is_from_home_page_slider = false ){
    $product_id        = $download->id;
    $sale_price       = get_post_meta( $product_id, 'edd_sale_price', true );
    $variable_pricing = $download->has_variable_prices();
    $variable_prices = edd_get_variable_prices( $download->id );
    $edd_item_in_cart = edd_item_in_cart( $download->ID, array() ) && ( ! $variable_pricing || ! $download->is_single_price_mode() );
    $custom_purchase_link = Custom_Purchase_Links_Helper::get_custom_purchase_link_as_string($product_id);

    $course            = WPI()->courses->get_course_by_product( $product_id );
    $show_open_padlock = false;
    $sales_disabled    = false;
    if ( false === $course ) {
        $product_type = get_post_meta( $product_id, '_edd_product_type', true );
        $is_digital_product = get_post_meta($product_id, 'wpi_resource_type', true) === 'digital_product';
        $is_service = get_post_meta($product_id, 'wpi_resource_type', true) === 'service';
        
        $sales_status = WPI()->courses->get_sales_status( $product_id, $product_id );

        if ( ('bundle' == $product_type || $is_digital_product || $is_service) && 'disabled' === $sales_status[ 'status' ] ) {
            $sales_disabled = true;
        }
    } else {
        $course_page_id = get_post_meta( $course->ID, 'course_id', true );
        $restricted_to  = array( array( 'download' => $product_id ) );
        $access         = bpmj_eddpc_user_can_access( get_current_user_id(), $restricted_to, $course_page_id );
        if ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] ) {
            $show_open_padlock = true;
        }
        $sales_status = WPI()->courses->get_sales_status( $course->ID, $product_id );
        if ( 'disabled' === $sales_status[ 'status' ] ) {
            $sales_disabled = true;
        }
    }

    ?>
    <div class="
    <?php if ( ! $edd_item_in_cart && ! $show_open_padlock && edd_has_variable_prices( $product_id ) ): echo 'warianty'; endif; ?>
    <?php if ( ! $edd_item_in_cart && ! $show_open_padlock && ! empty( $sale_price ) ): echo 'promocja'; endif; ?>">
        <form class="edd_download_purchase_form edd_purchase_<?php echo absint( $product_id ); ?> <?php if ( $sales_disabled && ! $show_open_padlock ): ?>edd-sales-disabled<?php endif; ?>"
            <?php if ( $sales_disabled ): ?>
                data-eddcm-sales-disabled-reason="<?php echo esc_attr( $sales_status[ 'reason' ] ) ?>" data-eddcm-sales-disabled-reason-long="<?php echo esc_attr( $sales_status[ 'reason_long' ] ) ?>"
            <?php endif; ?>
                method="post">
            <?php

            if ( $show_open_padlock ):
                $links = get_post_meta( $product_id, 'edd_download_files', true );
                $link = is_array( $links ) ? array_shift( $links ) : array();
                ?>
                <div class="glowna_box_cena glowna_box_cena_dostepny">
                    <p class="glowna_box_cena_dostepny_opis">
                        <span>
                            <?php _e( 'AVAILABLE', BPMJ_EDDCM_DOMAIN ) ?>
                        </span>
                        <i class="icon-unlocked-inverted"></i>
                </div>
                <?php if ( ! empty( $link[ 'file' ] ) ): ?>
                <div class="box_glowna_add_to_cart">
                    <a href="<?php echo $link[ 'file' ]; ?>"><i
                                class="fa fa-arrow-right"></i><?php _e( 'GO TO COURSE', BPMJ_EDDCM_DOMAIN ) ?>
                    </a>
                </div>
                <?php endif; ?>
            <?php
            else:
                if($variable_prices){
                    bpmj_eddcm_scarlet_variable_prices( $product_id, $is_from_home_page_slider );
                }
                bpmj_render_lowest_price_information($product_id);

                $no_variable_prices = ($variable_pricing && !$variable_prices);

                if ( $is_from_home_page_slider )
                    if(!$no_variable_prices) {
                        edd_price($product_id, true, edd_get_default_variable_price($download->id));
                    }else{
                        echo '';
                    }
                else

                    if(!$no_variable_prices) {
                        edd_price( $product_id );
                    }

                    do_action( 'edd_download_after_price' );

                    $data_variable = $variable_pricing ? ' data-variable-price="yes"' : 'data-variable-price="no"';
                    $type = $download->is_single_price_mode() ? 'data-price-mode=multi' : 'data-price-mode=single';

                    if ( $edd_item_in_cart ) {
                        $button_display   = 'style="display:none;"';
                        $checkout_display = '';
                    } else {
                        $button_display   = '';
                        $checkout_display = 'style="display:none;"';
                    }

                    ?>

                    <div class="edd_purchase_submit_wrapper box_glowna_add_to_cart">
                        <?php
                        $button_text = edd_get_option( 'add_to_cart_text', __( 'Purchase', 'easy-digital-downloads' ) );
                        if ( ! edd_is_ajax_disabled() ) { ?>
                            <a href="<?= ($custom_purchase_link) ? $custom_purchase_link : '#' ?>" <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_HYPERLINK, $product_id); ?>
                               <?= ($custom_purchase_link) ? 'target="_blank" rel="noindex nofollow"' : 'class="edd-add-to-cart"' ?>
                               data-action="edd_add_to_cart" data-download-id="<?= esc_attr( $download->ID )  ?>" <?= $data_variable ?> <?= $type ?> <?= $button_display ?>>
                                <span class="edd-add-to-cart-label">
                                    <i class="<?= ($sales_disabled ? 'fas fa-times' : 'icon-cart') ?>"></i>
                                    <?= $button_text ?>
                                </span> <span class="edd-loading"><i class="icon-hourglass icon-spin"></i></span></a>
                        <?php } ?>

                        <button type="submit" <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_HYPERLINK, $product_id); ?> class="edd-add-to-cart edd-no-js " name="edd_purchase_download" data-action="edd_add_to_cart" data-download-id="<?= esc_attr( $download->ID ) ?>" <?= $data_variable  ?> <?= $type ?> <?= $button_display ?>><?= $button_text ?></button>
                        <?php echo '<a href="' . esc_url( edd_get_checkout_uri() ) . '" class="edd_go_to_checkout" ' . $checkout_display . '>' . __( 'Checkout', 'easy-digital-downloads' ) . '</a>';
                        ?>

                        <?php if ( ! edd_is_ajax_disabled() ) : ?>
                            <span class="edd-cart-ajax-alert" aria-live="assertive">
                                <span class="edd-cart-added-alert" style="display: none;">
                                    <?php echo '<i class="edd-icon-ok" aria-hidden="true"></i> ' . __( 'Added to cart', 'easy-digital-downloads' ); ?>
                                </span>
                            </span>
                        <?php endif; ?>
                    </div><!--end .edd_purchase_submit_wrapper-->

                    <input type="hidden" name="prod$product_id"
                            value="<?php echo esc_attr( $download->ID ); ?>">
                    <?php if ( $variable_pricing && isset( $price_id ) && isset( $prices[ $price_id ] ) ): ?>
                    <input type="hidden" name="edd_options[price_id][]"
                            id="edd_price_option_<?php echo $download->ID; ?>_1"
                            class="edd_price_option_<?php echo $download->ID; ?>"
                            value="<?php echo $price_id; ?>">
                    <input type="hidden" name="edd_action" class="edd_action_input"
                            value="add_to_cart">

                    <?php if ( apply_filters( 'edd_download_redirect_to_checkout', edd_straight_to_checkout(), $download->ID, array() ) ) : ?>
                    <input type="hidden" name="edd_redirect_to_checkout"
                            id="edd_redirect_to_checkout" value="1">
            <?php endif; ?>

                <?php do_action( 'edd_purchase_link_end', $download->ID, array() ); ?>

                <?php
            endif;
        endif;

        do_action( 'edd_download_after' );

            ?>
        </form>
    </div>
<?php
}

/*
 * @param r string - a = days, h - hours, i - minutes, s - seconds
 */

function bpmj_eddcm_seconds_to_time( $seconds, $r ) {
	$seconds = (int)$seconds;
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%' . $r);
}
