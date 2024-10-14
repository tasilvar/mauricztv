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
                <?php $active = $key === Customers::DETAILS_VIEW_TOOLS ? true : false; ?>
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

            <div class="customer-notes-header">
                <?php echo get_avatar( $customer_email, 30 ); ?> <span><?php echo $customer_name; ?></span>
            </div>
            <h3><?php _e( 'Tools', BPMJ_EDDCM_DOMAIN ); ?></h3>

            <div class="edd-item-info customer-info">
                <h4><?php _e( 'Recount Customer Stats', BPMJ_EDDCM_DOMAIN ); ?></h4>
                <p class="edd-item-description"><?php _e( 'Use this tool to recalculate the purchase count and total value of the customer.', BPMJ_EDDCM_DOMAIN ); ?></p>
                <form method="post" id="edd-tools-recount-form" class="edd-export-form">
            <span>
                <?php wp_nonce_field( 'edd_ajax_export', 'edd_ajax_export' ); ?>

                <input type="hidden" name="edd-export-class" data-type="recount-single-customer-stats" value="EDD_Tools_Recount_Single_Customer_Stats" />
                <input type="hidden" name="customer_id" value="<?php echo $customer->id; ?>" />
                <input type="submit" id="recount-stats-submit" value="<?php _e( 'Recount Stats', BPMJ_EDDCM_DOMAIN ); ?>" class="button-secondary"/>
                <span class="spinner"></span>

            </span>
                </form>

            </div>

        </div>

    </div>
    <div class="clear"></div>
</div>