<?php

/** @var EDD_Customer $customer */
/** @var array $customer_tabs */
/** @var array $default_views */

use bpmj\wpidea\admin\pages\customers\Customers;

?>
<div class='wrap customers-page'>
    <hr class="wp-header-end">

    <h1 class='wp-heading-inline'><?php _e( 'Customer Details', BPMJ_EDDCM_DOMAIN );?></h1>
    <p class="clear"></p>
    <?php if ( edd_get_errors() ) :?>
        <div class="error settings-error">
            <?php edd_print_errors(); ?>
        </div>
    <?php endif; ?>

    <div id="edd-item-tab-wrapper" class="customer-tab-wrapper">
        <ul id="edd-item-tab-wrapper-list" class="ustomer-tab-wrapper-list">
            <?php
            foreach ( $customer_tabs as $key => $tab ) : ?>
                <?php $active = $key === Customers::DETAILS_VIEW_OVERVIEW ? true : false; ?>
                <?php $class  = $active ? 'active' : 'inactive'; ?>

                <?php if ( ! $active ) : ?>
                    <a title="<?php echo esc_attr( $tab['title'] ); ?>" aria-label="<?php echo esc_attr( $tab['title'] ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . Customers::PAGE . '&view=' . $key . '&id=' . $customer->id ) ); ?>">
                <?php endif; ?>

                <li class="<?php echo sanitize_html_class( $class ); ?>"><span class="dashicons <?php echo sanitize_html_class( $tab['dashicon'] ); ?>"></span></li>

                <?php if ( ! $active ) : ?>
                    </a>
                <?php endif; ?>

            <?php endforeach; ?>
        </ul>
    </div>

    <div id="edd-item-card-wrapper" class="edd-customer-card-wrapper" style="float: left">
        <?php do_action( 'edd_customer_card_top', $customer ); ?>

        <div class="info-wrapper customer-section">

            <form id="edit-customer-info" method="post" action="<?php echo admin_url( 'admin.php?page=' . Customers::PAGE . '&view=' . Customers::DETAILS_VIEW_OVERVIEW . '&id=' . $customer->id ); ?>">

                <div class="edd-item-info customer-info">

                    <div class="avatar-wrap left" id="customer-avatar">
                        <?php echo get_avatar( $customer_email ); ?><br />
                        <?php if ( current_user_can( $customer_edit_role ) && !$no_edit ): ?>
                            <span class="info-item editable customer-edit-link"><a title="<?php _e( 'Edit Customer', BPMJ_EDDCM_DOMAIN ); ?>" href="#" id="edit-customer"><?php _e( 'Edit Customer', BPMJ_EDDCM_DOMAIN ); ?></a></span>
                        <?php endif; ?>
                    </div>

                    <div class="customer-id right">
                        #<?php echo $customer->id; ?>
                    </div>

                    <div class="customer-address-wrapper right">
                        <?php if ( isset( $customer->user_id ) && $customer->user_id > 0 ) : ?>

                            <?php
                            $address = get_user_meta( $customer->user_id, '_edd_user_address', true );
                            $defaults = array(
                                'line1'   => '',
                                'line2'   => '',
                                'city'    => '',
                                'state'   => '',
                                'country' => '',
                                'zip'     => ''
                            );

                            $address = wp_parse_args( $address, $defaults );
                            ?>

                            <?php if ( ! empty( $address ) ) : ?>
                                <strong><?php _e( 'Customer Address', BPMJ_EDDCM_DOMAIN ); ?></strong>
                                <?php
                            $address = apply_filters( 'lms_filter_sensitive__customer_address', $address, $customer->user_id, true )
                                ?>
                                <span class="customer-address info-item editable">
                    <span class="info-item" data-key="line1"><?php echo $address['line1']; ?></span>
                    <span class="info-item" data-key="line2"><?php echo $address['line2']; ?></span>
                    <span class="info-item" data-key="city"><?php echo $address['city']; ?></span>
                    <span class="info-item" data-key="state"><?php echo $address['state']; ?></span>
                    <span class="info-item" data-key="country"><?php echo $address['country']; ?></span>
                    <span class="info-item" data-key="zip"><?php echo $address['zip']; ?></span>
                </span>
                            <?php endif; ?>

                            <?php if( !$no_edit ): ?>
                                <span class="customer-address info-item edit-item">
                    <input class="info-item" type="text" data-key="line1" name="customerinfo[line1]" placeholder="<?php _e( 'Address 1', BPMJ_EDDCM_DOMAIN ); ?>" value="<?php echo $address['line1']; ?>" />
                    <input class="info-item" type="text" data-key="line2" name="customerinfo[line2]" placeholder="<?php _e( 'Address 2', BPMJ_EDDCM_DOMAIN ); ?>" value="<?php echo $address['line2']; ?>" />
                    <input class="info-item" type="text" data-key="city" name="customerinfo[city]" placeholder="<?php _e( 'City', BPMJ_EDDCM_DOMAIN ); ?>" value="<?php echo $address['city']; ?>" />
                    <select data-key="country" name="customerinfo[country]" id="billing_country" class="billing_country edd-select edit-item">
                        <?php

                        $selected_country = $address['country'];

                        $countries = edd_get_country_list();
                        foreach( $countries as $country_code => $country ) {
                            echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
                        }
                        ?>
                    </select>
                    <?php
                    $selected_state = edd_get_shop_state();
                    $states         = edd_get_shop_states( $selected_country );

                    $selected_state = isset( $address['state'] ) ? $address['state'] : $selected_state;

                    if( ! empty( $states ) ) : ?>
                        <select data-key="state" name="customerinfo[state]" id="card_state" class="card_state edd-select info-item">
                        <?php
                        foreach( $states as $state_code => $state ) {
                            echo '<option value="' . $state_code . '"' . selected( $state_code, $selected_state, false ) . '>' . $state . '</option>';
                        }
                        ?>
                    </select>
                    <?php else : ?>
                        <input type="text" size="6" data-key="state" name="customerinfo[state]" id="card_state" class="card_state edd-input info-item" placeholder="<?php _e( 'State / Province', BPMJ_EDDCM_DOMAIN ); ?>"/>
                    <?php endif; ?>
                    <input class="info-item" type="text" data-key="zip" name="customerinfo[zip]" placeholder="<?php _e( 'Postal', BPMJ_EDDCM_DOMAIN ); ?>" value="<?php echo $address['zip']; ?>" />
                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="customer-main-wrapper left">

                        <?php if(!$no_edit): ?>
                            <span class="customer-name info-item edit-item"><input size="15" data-key="name" name="customerinfo[name]" type="text" value="<?php echo esc_attr( $customer_name ); ?>" placeholder="<?php _e( 'Customer Name', BPMJ_EDDCM_DOMAIN ); ?>" /></span>
                        <?php endif; ?>
                        <span class="customer-name info-item editable"><span data-key="name"><?php echo $customer_name; ?></span></span>
                        <?php if(!$no_edit): ?>
                            <span class="customer-name info-item edit-item"><input size="20" data-key="email" name="customerinfo[email]" type="text" value="<?php echo $customer_email; ?>" placeholder="<?php _e( 'Customer Email', BPMJ_EDDCM_DOMAIN ); ?>" /></span>
                        <?php endif; ?>
                        <span class="customer-email info-item editable" data-key="email"><?php echo $customer_email; ?></span>
                        <span class="customer-since info-item">
                    <?php _e( 'Customer since', BPMJ_EDDCM_DOMAIN ); ?>
                    <?php echo date_i18n( get_option( 'date_format' ), strtotime( $customer->date_created ) ) ?>
                </span>

                        <?php if(!$no_edit): ?>
                            <span class="customer-user-id info-item edit-item">
                    <?php

                    $user_id    = $customer->user_id > 0 ? $customer->user_id : '';
                    $data_atts  = array( 'key' => 'user_login', 'exclude' => $user_id );
                    $user_args  = array(
                        'name'  => 'customerinfo[user_login]',
                        'class' => 'edd-user-dropdown',
                        'data'  => $data_atts,
                    );

                    if( ! empty( $user_id ) ) {
                        $userdata = get_userdata( $user_id );
                        $user_args['value'] = $userdata->user_login;
                    }

                    echo EDD()->html->ajax_user_search( $user_args );
                    ?>
                    <input type="hidden" name="customerinfo[user_id]" data-key="user_id" value="<?php echo $customer->user_id; ?>" />
                </span>
                        <?php endif; ?>

                        <span class="customer-user-id info-item editable">
                    <?php _e( 'User ID', BPMJ_EDDCM_DOMAIN ); ?>:&nbsp;
                    <?php if( intval( $customer->user_id ) > 0 ) : ?>
                        <span data-key="user_id"><a href="<?php echo admin_url( 'user-edit.php?user_id=' . $customer->user_id ); ?>"><?php echo $customer->user_id; ?></a></span>
                    <?php else : ?>
                        <span data-key="user_id"><?php _e( 'none', BPMJ_EDDCM_DOMAIN ); ?></span>
                    <?php endif; ?>
                            <?php if ( current_user_can( $customer_edit_role ) && intval( $customer->user_id ) > 0 ) : ?>
                                <span class="disconnect-user"> - <a id="disconnect-customer" href="#disconnect" title="<?php _e( 'Disconnects the current user ID from this customer record', BPMJ_EDDCM_DOMAIN ); ?>"><?php _e( 'Disconnect User', BPMJ_EDDCM_DOMAIN ); ?></a></span>
                            <?php endif; ?>
                </span>

                    </div>

                </div>

                <span id="customer-edit-actions" class="edit-item">
            <input type="hidden" data-key="id" name="customerinfo[id]" value="<?php echo $customer->id; ?>" />
            <?php wp_nonce_field( 'edit-customer', '_wpnonce', false, true ); ?>
            <input type="hidden" name="edd_action" value="edit-customer" />
            <input type="submit" id="edd-edit-customer-save" class="button-secondary" value="<?php _e( 'Update Customer', BPMJ_EDDCM_DOMAIN ); ?>" />
            <a id="edd-edit-customer-cancel" href="" class="delete"><?php _e( 'Cancel', BPMJ_EDDCM_DOMAIN ); ?></a>
        </span>

            </form>
        </div>

        <div id="edd-item-stats-wrapper" class="customer-stats-wrapper customer-section">
            <ul>
                <li>
                    <?php if(!$no_edit): ?>
                        <a title="<?php _e( 'View All Purchases', BPMJ_EDDCM_DOMAIN ); ?>" href="<?php echo admin_url( 'admin.php?page=wp-idea-payment-history&user=' . urlencode( $customer->email ) ); ?>">
                            <span class="dashicons dashicons-cart"></span>
                            <?php printf( _n( '%d Completed Sale', '%d Completed Sales', $customer->purchase_count, BPMJ_EDDCM_DOMAIN ), $customer->purchase_count ); ?>
                        </a>
                    <?php else: ?>
                        <span class="dashicons dashicons-cart"></span>
                        <?php printf( _n( '%d Completed Sale', '%d Completed Sales', $customer->purchase_count, BPMJ_EDDCM_DOMAIN ), $customer->purchase_count ); ?>
                    <?php endif; ?>
                </li>
                <li>
                    <span class="dashicons dashicons-chart-area"></span>
                    <?php echo edd_currency_filter( edd_format_amount( $customer->purchase_value ) ); ?> <?php _e( 'Lifetime Value', BPMJ_EDDCM_DOMAIN ); ?>
                </li>
            </ul>
        </div>

        <div id="edd-item-tables-wrapper" class="customer-tables-wrapper customer-section">

            <h3><?php _e( 'Recent Payments', BPMJ_EDDCM_DOMAIN ); ?></h3>
            <?php
            $payment_ids = explode( ',', $customer->payment_ids );
            $payments    = edd_get_payments( array( 'post__in' => $payment_ids ) );
            $payments    = array_slice( $payments, 0, 10 );
            ?>
            <table class="wp-list-table widefat striped payments">
                <thead>
                <tr>
                    <th><?php _e( 'ID', BPMJ_EDDCM_DOMAIN ); ?></th>
                    <th><?php _e( 'Amount', BPMJ_EDDCM_DOMAIN ); ?></th>
                    <th><?php _e( 'Date', BPMJ_EDDCM_DOMAIN ); ?></th>
                    <th><?php _e( 'Status', BPMJ_EDDCM_DOMAIN ); ?></th>
                    <th><?php _e( 'Actions', BPMJ_EDDCM_DOMAIN ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ( ! empty( $payments ) ) : ?>
                    <?php foreach ( $payments as $payment ) : ?>
                        <tr>
                            <td><?php echo $payment->ID; ?></td>
                            <td><?php echo edd_payment_amount( $payment->ID ); ?></td>
                            <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $payment->post_date ) ); ?></td>
                            <td><?php echo edd_get_payment_status( $payment, true ); ?></td>
                            <td>
                                <a title="<?php _e( 'View Details for Payment', BPMJ_EDDCM_DOMAIN ); echo ' ' . $payment->ID; ?>" href="<?php echo admin_url( 'admin.php?page=wp-idea-payment-history&view=order-details&id=' . $payment->ID ); ?>">
                                    <?php _e( 'View Details', BPMJ_EDDCM_DOMAIN ); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5"><?php _e( 'No Payments Found', BPMJ_EDDCM_DOMAIN ); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <h3><?php printf( __( 'Purchased %s', BPMJ_EDDCM_DOMAIN ), edd_get_label_plural() ); ?></h3>
            <?php
            $downloads = edd_get_users_purchased_products( $customer->email );
            ?>
            <table class="wp-list-table widefat striped downloads">
                <thead>
                <tr>
                    <th><?php echo edd_get_label_singular(); ?></th>
                    <th width="120px"><?php _e( 'Actions', BPMJ_EDDCM_DOMAIN ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ( ! empty( $downloads ) ) : ?>
                    <?php foreach ( $downloads as $download ) : ?>
                        <tr>
                            <td><?php echo $download->post_title; ?></td>
                            <td>
                                <a title="<?php echo esc_attr( sprintf( __( 'View %s', BPMJ_EDDCM_DOMAIN ), $download->post_title ) ); ?>" href="<?php echo esc_url( admin_url( 'post.php?action=edit&post=' . $download->ID ) ); ?>">
                                    <?php printf( __( 'View %s', BPMJ_EDDCM_DOMAIN ), edd_get_label_singular() ); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2"><?php printf( __( 'No %s Found', BPMJ_EDDCM_DOMAIN ), edd_get_label_plural() ); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>

        </div>

    </div>
    <div class="clear"></div>
</div>
