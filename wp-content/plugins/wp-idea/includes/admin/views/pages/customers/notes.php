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
                <?php $active = $key === Customers::DETAILS_VIEW_NOTES ? true : false; ?>
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
        <div id="edd-item-notes-wrapper">
            <div class="edd-item-notes-header">
                <?php echo get_avatar( $customer_email, 30 ); ?> <span><?php echo $customer_name; ?></span>
            </div>
            <h3><?php _e( 'Notes', BPMJ_EDDCM_DOMAIN ); ?></h3>

            <?php if ( 1 == $paged ) : ?>
                <div style="display: block; margin-bottom: 35px;">
                    <form id="edd-add-customer-note" method="post" action="<?php echo admin_url( 'admin.php?page=' . Customers::PAGE . '&view=' . Customers::DETAILS_VIEW_NOTES . '&id=' . $customer->id ); ?>">
                        <textarea id="customer-note" name="customer_note" class="customer-note-input" rows="10"></textarea>
                        <br />
                        <input type="hidden" id="customer-id" name="customer_id" value="<?php echo $customer->id; ?>" />
                        <input type="hidden" name="edd_action" value="add-customer-note" />
                        <?php wp_nonce_field( 'add-customer-note', 'add_customer_note_nonce', true, true ); ?>
                        <input id="add-customer-note" class="right button-primary" type="submit" value="<?php _e( 'Add Note', BPMJ_EDDCM_DOMAIN ); ?>" />
                    </form>
                </div>
            <?php endif; ?>

            <?php
            $pagination_args = array(
                'base'     => '%_%',
                'format'   => '?paged=%#%',
                'total'    => $total_pages,
                'current'  => $paged,
                'show_all' => true
            );

            echo paginate_links( $pagination_args );
            ?>

            <div id="edd-customer-notes">
                <?php if ( count( $customer_notes ) > 0 ) : ?>
                    <?php foreach( $customer_notes as $key => $note ) : ?>
                        <div class="customer-note-wrapper dashboard-comment-wrap comment-item">
                <span class="note-content-wrap">
                    <?php echo stripslashes( $note ); ?>
                </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="edd-no-customer-notes">
                        <?php _e( 'No Customer Notes', BPMJ_EDDCM_DOMAIN ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php echo paginate_links( $pagination_args ); ?>

        </div>
    </div>
    <div class="clear"></div>
</div>
