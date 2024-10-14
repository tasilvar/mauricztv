<?php
use bpmj\wpidea\admin\reports\Reports_View;
?>
<form id="edd-reports-filter" method="get">
    <select id="edd-reports-view" name="view">
        <option value="<?= Reports_View::EARNINGS_VIEW; ?>"><?php _e( 'Report Type', BPMJ_EDDCM_DOMAIN ); ?></option>
        <?php foreach ( $reports_views as $view_id => $label ) : ?>
            <option value="<?php echo esc_attr( $view_id ); ?>" <?php selected( $view_id, $active_view ); ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
    </select>

    <?php do_action( 'edd_report_view_actions' ); ?>

    <input type="hidden" name="page" value="wp-idea-reports"/>
    <?php submit_button( __( 'Show', BPMJ_EDDCM_DOMAIN ), 'secondary', 'submit', false ); ?>
</form>
