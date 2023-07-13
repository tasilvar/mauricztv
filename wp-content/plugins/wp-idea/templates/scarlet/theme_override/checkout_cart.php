<?php
/**
 *  This template is used to display the Checkout page when items are in the cart
 */
use bpmj\wpidea\sales\product\Invoice_Tax_Payer_Helper;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\View_Hooks;
use bpmj\wpidea\modules\increasing_sales\api\Increasing_Sales_API_Static_Helper;

global $post;

$cart_items = edd_get_cart_contents();
$is_vat_payer = Invoice_Tax_Payer_Helper::is_enabled();
?>
<table
        id="edd_checkout_cart" class="podsumowanie_koszyk<?php if ( ! edd_is_ajax_disabled() ) { echo ' ajaxed'; } ?>" >
	<thead>
		<tr class="edd_cart_header_row">
            <th><?= Translator_Static_Helper::translate('templates.checkout_cart.products_in_cart') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'edd_cart_items_before' ); ?>
		<?php if ( $cart_items ) : ?>
			<?php foreach ( $cart_items as $key => $item ) :
                    $item_price = edd_cart_item_price( $item['id'], $item['options'] );
                ?>
				<tr class="edd_cart_item" id="edd_cart_item_<?php echo esc_attr( $key ) . '_' . esc_attr( $item['id'] ); ?>" data-download-id="<?php echo esc_attr( $item['id'] ); ?>">
					<?php do_action( 'edd_checkout_table_body_first', $item ); ?>
					<td>
                        <div class="checkout_cart_img">
                           <?php
							if ( current_theme_supports( 'post-thumbnails' ) ) {
								$thumb = get_the_post_thumbnail( $item['id'], apply_filters( 'edd_checkout_image_size', array( 80, 80 ) ) );
								if( empty( $thumb ) ) {
									$thumb = '<img width="80" height="80" src="' . bpmj_eddcm_template_get_file( 'assets/img/box1-200x197.jpg' ) . '" class="attachment-80x80 size-80x80 wp-post-image" alt="">';
								}
								echo $thumb;
							}
                            ?>
                        </div>
                        <div class="checkout_cart_title">
                            <?php
                            $item_title = edd_get_cart_item_name( $item );
                            echo esc_html( $item_title );
                            do_action( 'edd_checkout_cart_item_title_after', $item );
                            ?>
                        </div>
                        <div class="checkout_cart_price_button">
                            <div class="cart_price">
                                <?php
                                echo $item_price;
                                do_action( 'edd_checkout_cart_item_price_after', $item );
                                ?>
                            </div>
                            <div class="cart_button_remove">
                                <?php if( edd_item_quantities_enabled() ) : ?>
                                    <input type="number" min="1" step="1" name="edd-cart-download-<?php echo $key; ?>-quantity" data-key="<?php echo $key; ?>" class="edd-input edd-item-quantity" value="<?php echo edd_get_cart_item_quantity( $item['id'], $item['options'] ); ?>"/>
                                    <input type="hidden" name="edd-cart-downloads[]" value="<?php echo $item['id']; ?>"/>
                                    <input type="hidden" name="edd-cart-download-<?php echo $key; ?>-options" value="<?php echo esc_attr( json_encode( $item['options'] ) ); ?>"/>
                                <?php endif; ?>
                                <?php do_action( 'edd_cart_actions', $item, $key ); ?>
                                <a class="remove_from_cart" <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_REMOVE_FROM_CART_HYPERLINK, $item['id']); ?> href="<?php echo esc_url( edd_remove_item_url( $key ) ); ?>"><i class="fas fa-trash-alt"></i> &nbsp; <?= Translator_Static_Helper::translate('templates.checkout_cart.remove_from_cart') ?></a>

                            </div>
                        </div>
                    </td>
					<?php do_action( 'edd_checkout_table_body_last', $item ); ?>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php do_action( 'edd_cart_items_middle' ); ?>

        <?= Increasing_Sales_API_Static_Helper::render_offers() ?>

		<!-- Show any cart fees, both positive and negative fees -->
		<?php if( edd_cart_has_fees() ) : ?>
			<?php foreach( edd_get_cart_fees() as $fee_id => $fee ) : ?>
				<tr class="edd_cart_fee" id="edd_cart_fee_<?php echo $fee_id; ?>">

					<?php do_action( 'edd_cart_fee_rows_before', $fee_id, $fee ); ?>

                    <td>
                        <div class="checkout_cart_title">
                            <?php echo esc_html($fee['label']); ?>
                        </div>
                        <div class="checkout_fee_amount">
                            <?php echo esc_html(edd_currency_filter(edd_format_amount($fee['amount']))); ?>
                        </div>
                    </td>
                    <td>
						<?php if( ! empty( $fee['type'] ) && 'item' == $fee['type'] ) : ?>
							<a href="<?php echo esc_url( edd_remove_cart_fee_url( $fee_id ) ); ?>"><?php _e( 'Remove', 'easy-digital-downloads' ); ?></a>
						<?php endif; ?>

					</td>

					<?php do_action( 'edd_cart_fee_rows_after', $fee_id, $fee ); ?>

				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php do_action( 'edd_cart_items_after' ); ?>
	</tbody>
	<tfoot>

		<?php if( has_action( 'edd_cart_footer_buttons' ) ) : ?>
			<tr class="edd_cart_footer_row<?php if ( edd_is_cart_saving_disabled() ) { echo ' edd-no-js'; } ?>">
				<th>
					<?php do_action( 'edd_cart_footer_buttons' ); ?>
				</th>
			</tr>
		<?php endif; ?>

		<?php if( edd_use_taxes() && ! edd_prices_include_tax() ) : ?>
			<tr class="edd_cart_footer_row edd_cart_subtotal_row"<?php if ( ! edd_is_cart_taxed() ) echo ' style="display:none;"'; ?>>
				<?php do_action( 'edd_checkout_table_subtotal_first' ); ?>
				<th class="edd_cart_subtotal">
					<?php _e( 'Subtotal', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_subtotal_amount"><?php echo edd_cart_subtotal(); ?></span>
				</th>
				<?php do_action( 'edd_checkout_table_subtotal_last' ); ?>
			</tr>
		<?php endif; ?>

		<tr class="edd_cart_footer_row edd_cart_discount_row" <?php if( ! edd_cart_has_discounts() )  echo ' style="display:none;"'; ?>>
			<?php do_action( 'edd_checkout_table_discount_first' ); ?>
			<th class="edd_cart_discount">
				<?php edd_cart_discounts_html(); ?>
			</th>
			<?php do_action( 'edd_checkout_table_discount_last' ); ?>
		</tr>

		<?php if( edd_use_taxes() ) : ?>
			<tr class="edd_cart_footer_row edd_cart_tax_row"<?php if( ! edd_is_cart_taxed() ) echo ' style="display:none;"'; ?>>
				<?php do_action( 'edd_checkout_table_tax_first' ); ?>
				<th class="edd_cart_tax">
					<?php _e( 'Tax', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_tax_amount" data-tax="<?php echo edd_get_cart_tax( false ); ?>"><?php echo esc_html( edd_cart_tax() ); ?></span>
				</th>
				<?php do_action( 'edd_checkout_table_tax_last' ); ?>
			</tr>

		<?php endif; ?>

        <?php if( edd_has_active_discounts() ) : ?>
		<tr class="edd_cart_footer_row">
			<th class="edd_cart_total">
				<div class="podsumowanie_contenter">
                    <?php bpmj_eddcm_scarlet_edd_discount_field() ?>
                </div>
			</th>
		</tr>
        <?php endif; ?>
        <tr class="edd_cart_footer_row">
            <?php do_action( 'edd_checkout_table_footer_first' ); ?>
            <th class="edd_cart_total">
                <div class="podsumowanie_contenter">
                    <div class="cena_netto_brutto">
                        <?php if ( $is_vat_payer ) : ?>
                            <div class="podsumowanie_netto_vat">
                                <?php
                                do_action( 'get_cart_net_vat_total_price');
                                ?>
                            </div><br>
                        <?php endif; ?>
                        <div class="podsumowanie">
                            <div class="title"><?php _e( 'Total', 'easy-digital-downloads' ); ?>:</div>
                            <div class="price edd_cart_amount" data-subtotal="<?php echo edd_get_cart_total(); ?>" data-total="<?php echo edd_get_cart_total(); ?>"><?php edd_cart_total(); ?></div>
                        </div>
                    </div>
                </div>
            </th>
            <?php do_action( 'edd_checkout_table_footer_last' ); ?>
        </tr>
	</tfoot>
</table>
