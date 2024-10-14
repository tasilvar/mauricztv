<div class="wrap">
    <h2><?php _e( 'Discount Code Generator', BPMJ_EDDCM_DOMAIN ); ?></h2>
    <form id="edd-add-discount" action="" method="POST">
        <?php do_action( 'edd_dcg_add_discount_form_top' ); ?>
        <table class="form-table">
            <tbody>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-number-codes"><?php _e( 'Number of Codes', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <input type="number" id="edd-number-codes" name="number-codes" value="" style="width: 80px;"/>
                    <p class="description"><?php _e( 'The number of codes to generate', BPMJ_EDDCM_DOMAIN ); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-name"><?php _ex( 'Name', 'for discount', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <input name="name" id="edd-name" type="text" value="" style="width: 300px;"/>
                    <p class="description"><?php _e( 'The name of this discount. This will have a number appended to it, e.g. Name-1', BPMJ_EDDCM_DOMAIN ); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-code-limit"><?php _e( 'Code', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <select name="code-type" id="edd-code-type">
                        <option value="hash"><?php _e( 'Hash', BPMJ_EDDCM_DOMAIN ); ?></option>
                        <option value="letters"><?php _e( 'Letters', BPMJ_EDDCM_DOMAIN ); ?></option>
                        <option value="number"><?php _e( 'Numbers', BPMJ_EDDCM_DOMAIN ); ?></option>
                    </select>
                    <input type="number" id="edd-code-limit" name="code-limit" value="10" style="width: 80px;"/>
                    <p class="description"><?php _e( 'Enter a type of code and code length limit', BPMJ_EDDCM_DOMAIN ); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-type"><?php _e( 'Type', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <select name="type" id="edd-type">
                        <option value="percent"><?php _ex( 'Percentage', 'for discount', BPMJ_EDDCM_DOMAIN ); ?></option>
                        <option value="flat"><?php _e( 'Flat amount', BPMJ_EDDCM_DOMAIN ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'The kind of discount to apply for this discount.', BPMJ_EDDCM_DOMAIN ); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-amount"><?php _e( 'Amount', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <input type="text" id="edd-amount" name="amount" value="" style="width: 40px;"/>
                    <p class="description"><?php _e( 'The amount of this discount code.', BPMJ_EDDCM_DOMAIN ); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-products"><?php printf( __( '%s Requirements', BPMJ_EDDCM_DOMAIN ), edd_get_label_plural() ); ?></label>
                </th>
                <td>
                    <p>
                        <select id="edd-product-condition" name="product_condition">
                            <option value="all"><?php printf( __( 'All Selected %s', BPMJ_EDDCM_DOMAIN ), edd_get_label_plural() ); ?></option>
                            <option value="any"><?php printf( __( 'Any Selected %s', BPMJ_EDDCM_DOMAIN ), edd_get_label_singular() ); ?></option>
                        </select>
                        <label for="edd-product-condition"><?php _e( 'Condition', BPMJ_EDDCM_DOMAIN ); ?></label>
                    </p>
                    <select multiple id="edd-products" name="products[]" class="edd-select-chosen" data-placeholder="<?php printf( __( 'Choose one or more %s', BPMJ_EDDCM_DOMAIN ), edd_get_label_plural() ); ?>">
                        <?php
                        $downloads = get_posts( array( 'post_type' => 'download', 'nopaging' => true ) );
                        if( $downloads ) :
                            foreach( $downloads as $download ) :
                                echo '<option value="' . esc_attr( $download->ID ) . '">' . esc_html( get_the_title( $download->ID ) ) . '</option>';
                            endforeach;
                        endif;
                        ?>
                    </select>
                    <p class="description"><?php printf( __( '%s required to be purchased for this discount.', BPMJ_EDDCM_DOMAIN ), edd_get_label_plural() ); ?></p>

                    <p>
                        <label for="edd-non-global-discount">
                            <input type="checkbox" id="edd-non-global-discount" name="not_global" value="1"/>
                            <?php printf( __( 'Apply discount only to selected %s?', BPMJ_EDDCM_DOMAIN ), edd_get_label_plural() ); ?>
                        </label>
                    </p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-start"><?php _ex( 'Start date', 'for discount', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <input name="start" id="edd-start" type="text" value="" style="width: 120px;" class="edd_datepicker"/>
                    <p class="description"><?php _e( 'Enter the start date for this discount code in the format of mm/dd/yyyy. For no start date, leave blank. If entered, the discount can only be used after or on this date.', BPMJ_EDDCM_DOMAIN ); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-expiration"><?php _ex( 'Expiration date', 'for discount', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <input name="expiration" id="edd-expiration" type="text" style="width: 120px;" class="edd_datepicker"/>
                    <p class="description"><?php _e( 'Enter the expiration date for this discount code in the format of mm/dd/yyyy. For no expiration, leave blank', BPMJ_EDDCM_DOMAIN ); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-min-cart-amount"><?php _e( 'Minimum Amount', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <input type="text" id="edd-min-cart-amount" name="min_price" value="" style="width: 40px;"/>
                    <p class="description"><?php _e( 'The minimum amount that must be purchased before this discount can be used. Leave blank for no minimum.', BPMJ_EDDCM_DOMAIN ); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-max-uses"><?php _e( 'Max Uses', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <input type="text" id="edd-max-uses" name="max" value="" style="width: 40px;"/>
                    <p class="description"><?php _e( 'The maximum number of times this discount can be used. Leave blank for unlimited.', BPMJ_EDDCM_DOMAIN ); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="edd-use-once"><?php _e( 'Use Once Per Customer', BPMJ_EDDCM_DOMAIN ); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="edd-use-once" name="use_once" value="1"/>
                    <span class="description"><?php _e( 'Limit this discount to a single-use per customer?', BPMJ_EDDCM_DOMAIN ); ?></span>
                </td>
            </tr>
            </tbody>
        </table>
        <?php do_action( 'edd_dcg_add_discount_form_bottom' ); ?>
        <p class="submit">
            <input type="hidden" name="wp-idea-action" value="add_discount"/>
            <input type="hidden" name="wp-idea-redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=wp-idea-discounts' ) ); ?>"/>
            <input type="hidden" name="wp-idea-dcg-discount-nonce" value="<?php echo wp_create_nonce( 'edd_dcg_discount_nonce' ); ?>"/>
            <input type="submit" value="<?php _e( 'Create Codes', BPMJ_EDDCM_DOMAIN ); ?>" class="button-primary"/>
        </p>
    </form>
</div>
