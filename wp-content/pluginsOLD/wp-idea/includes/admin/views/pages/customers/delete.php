<?php

use bpmj\wpidea\admin\pages\customers\Customers;
use bpmj\wpidea\View;

/** @var EDD_Customer $customer */
/** @var array $customer_tabs */
/** @var string $actual_view */
/** @var array $default_views */
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
                <?php $active = $key === Customers::DETAILS_VIEW_DELETE ? true : false; ?>
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

        <div class="info-wrapper customer-section">

            <form id="delete-customer" method="post" action="<?php echo admin_url( 'admin.php?page=' . Customers::PAGE . '&view=' . Customers::DETAILS_VIEW_DELETE . '&id=' . $customer->id ); ?>">

                <div class="edd-item-notes-header">
                    <?php echo get_avatar( $customer_email, 30 ); ?> <span><?php echo $customer_name; ?></span>
                </div>


                <div class="customer-info delete-customer">

            <span class="delete-customer-options">
                <p>
                    <?php echo EDD()->html->checkbox( array( 'name' => 'edd-customer-delete-confirm' ) ); ?>
                    <label for="edd-customer-delete-confirm"><?php _e( 'Are you sure you want to delete this customer?', BPMJ_EDDCM_DOMAIN ); ?></label>
                </p>

                <p>
                    <?php echo EDD()->html->checkbox( array( 'name' => 'edd-customer-delete-records', 'options' => array( 'disabled' => true ) ) ); ?>
                    <label for="edd-customer-delete-records"><?php _e( 'Delete all associated payments and records?', BPMJ_EDDCM_DOMAIN ); ?></label>
                </p>

            </span>
            <span id="customer-edit-actions">
                <input type="hidden" name="customer_id" value="<?php echo $customer->id; ?>" />
                <?php wp_nonce_field( 'delete-customer', '_wpnonce', false, true ); ?>
                <input type="hidden" name="edd_action" value="delete-customer" />
                <input type="submit" disabled="disabled" id="edd-delete-customer" class="button-primary" value="<?php _e( 'Delete Customer', BPMJ_EDDCM_DOMAIN ); ?>" />
                <a id="edd-delete-customer-cancel" href="<?php echo admin_url( 'admin.php?page=' . Customers::PAGE . '&view=' . Customers::DETAILS_VIEW_OVERVIEW . '&id=' . $customer->id ); ?>" class="delete"><?php _e( 'Cancel', BPMJ_EDDCM_DOMAIN ); ?></a>
            </span>

                </div>

            </form>
        </div>

    </div>
    <div class="clear"></div>
</div>