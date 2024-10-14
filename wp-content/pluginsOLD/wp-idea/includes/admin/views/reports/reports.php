<?php
use bpmj\wpidea\View;

$display = $graphing['dates']['range'] == 'other' ? '' : 'style="display:none;"';
?>
<div class="tablenav top">
    <div class="alignleft actions">
        <?= View::get_admin('/reports/reports-filter', [
                'reports_views' => $reports_views,
                'active_view' => $active_view,
        ]); ?>
    </div>
</div>
<div id="edd-dashboard-widgets-wrap">
    <div class="metabox-holder" style="padding-top: 0;">
        <div class="postbox">
            <h3><span><?php _e('Earnings Over Time',BPMJ_EDDCM_DOMAIN ); ?></span></h3>
            <div class="inside">
                <form id="edd-graphs-filter" method="get">
                    <div class="tablenav top">
                        <div class="alignleft actions">
                            <?php
                            $date_options = apply_filters( 'edd_report_date_options', array(
                                'today'        => __( 'Today', BPMJ_EDDCM_DOMAIN ),
                                'yesterday'    => __( 'Yesterday', BPMJ_EDDCM_DOMAIN ),
                                'this_week'    => __( 'This Week', BPMJ_EDDCM_DOMAIN ),
                                'last_week'    => __( 'Last Week', BPMJ_EDDCM_DOMAIN ),
                                'this_month'   => __( 'This Month', BPMJ_EDDCM_DOMAIN ),
                                'last_month'   => __( 'Last Month', BPMJ_EDDCM_DOMAIN ),
                                'this_quarter' => __( 'This Quarter', BPMJ_EDDCM_DOMAIN ),
                                'last_quarter' => __( 'Last Quarter', BPMJ_EDDCM_DOMAIN ),
                                'this_year'    => __( 'This Year', BPMJ_EDDCM_DOMAIN ),
                                'last_year'    => __( 'Last Year', BPMJ_EDDCM_DOMAIN ),
                                'other'        => __( 'Custom', BPMJ_EDDCM_DOMAIN )
                            ) );
                            $dates = edd_get_report_dates();
                            ?>
                            <input type="hidden" name="page" value="wp-idea-reports"/>
                            <input type="hidden" name="view" value="<?php echo esc_attr( $active_view ); ?>"/>

                            <select id="edd-graphs-date-options" name="range">
                                <?php foreach ( $date_options as $key => $option ) : ?>
                                    <option value="<?php echo esc_attr( $key ); ?>"<?php selected( $key, $dates['range'] ); ?>><?php echo esc_html( $option ); ?></option>
                                <?php endforeach; ?>
                            </select>

                            <div id="edd-date-range-options" <?php echo $display; ?>>
                                <span><?php _e( 'From', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;</span>
                                <select id="edd-graphs-month-start" name="m_start">
                                    <?php for ( $i = 1; $i <= 12; $i++ ) : ?>
                                        <option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['m_start'] ); ?>><?php echo edd_month_num_to_name( $i ); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select id="edd-graphs-day-start" name="day">
                                    <?php for ( $i = 1; $i <= 31; $i++ ) : ?>
                                        <option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['day'] ); ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select id="edd-graphs-year-start" name="year">
                                    <?php for ( $i = 2007; $i <= date( 'Y' ); $i++ ) : ?>
                                        <option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['year'] ); ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <span><?php _e( 'To', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;</span>
                                <select id="edd-graphs-month-end" name="m_end">
                                    <?php for ( $i = 1; $i <= 12; $i++ ) : ?>
                                        <option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['m_end'] ); ?>><?php echo edd_month_num_to_name( $i ); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select id="edd-graphs-day-end" name="day_end">
                                    <?php for ( $i = 1; $i <= 31; $i++ ) : ?>
                                        <option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['day_end'] ); ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select id="edd-graphs-year-end" name="year_end">
                                    <?php for ( $i = 2007; $i <= date( 'Y' ); $i++ ) : ?>
                                        <option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['year_end'] ); ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="edd-graph-filter-submit graph-option-section">
                                <input type="submit" class="button-secondary" value="<?php _e( 'Filter', BPMJ_EDDCM_DOMAIN ); ?>"/>
                            </div>
                        </div>
                    </div>
                </form>
                <?php
                $graph = new EDD_Graph( $graphing['data'] );
                $graph->set( 'x_mode', 'time' );
                $graph->set( 'multiple_y_axes', true );
                $graph->display();

                if( 'this_month' == $graphing['dates']['range'] ) {
                    $estimated = edd_estimated_monthly_stats( $graphing['include_taxes'] );
                }
                ?>

                <p class="edd_graph_totals">
                    <strong>
                        <?php
                        _e( 'Total earnings for period shown: ', BPMJ_EDDCM_DOMAIN );
                        echo edd_currency_filter( edd_format_amount( $graphing['earnings_totals'] ) );
                        ?>
                    </strong>
                    <?php if ( ! $graphing['include_taxes'] ) : ?>
                        <sup>&dagger;</sup>
                    <?php endif; ?>
                </p>
                <p class="edd_graph_totals"><strong><?php _e( 'Total sales for period shown: ', BPMJ_EDDCM_DOMAIN ); echo edd_format_amount( $graphing['sales_totals'], false ); ?></strong></p>

                <?php if( 'this_month' == $graphing['dates']['range'] ) : ?>
                    <p class="edd_graph_totals">
                        <strong>
                            <?php
                            _e( 'Estimated monthly earnings: ', BPMJ_EDDCM_DOMAIN );
                            echo edd_currency_filter( edd_format_amount( $estimated['earnings'] ) );
                            ?>
                        </strong>
                        <?php if ( ! $graphing['include_taxes'] ) : ?>
                            <sup>&dagger;</sup>
                        <?php endif; ?>
                    </p>
                    <p class="edd_graph_totals"><strong><?php _e( 'Estimated monthly sales: ', BPMJ_EDDCM_DOMAIN ); echo edd_format_amount( $estimated['sales'], false ); ?></strong></p>
                <?php endif; ?>

                <p class="edd_graph_notes">
                    <?php if ( false === $graphing['include_taxes'] ) : ?>
                        <em><sup>&dagger;</sup> <?php _e( 'Excludes sales tax.', BPMJ_EDDCM_DOMAIN ); ?></em>
                    <?php endif; ?>
                </p>

            </div>
        </div>
    </div>
</div>
