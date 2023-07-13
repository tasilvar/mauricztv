<?php

namespace bpmj\wpidea\admin\reports;

use bpmj\wpidea\admin\reports\Gateways_Table;
use bpmj\wpidea\Caps;
use bpmj\wpidea\View;
use bpmj\wpidea\wolverine\user\User;

class Reports_View
{
    protected $reports_views = [];

    protected $active_view;

    protected $graphing = [];

    const EARNINGS_VIEW = 'earnings';

    const GATEWAYS_VIEW = 'gateways';

    public function __construct()
    {
        $this->reports_views = [
            self::EARNINGS_VIEW   => __( 'Earnings', BPMJ_EDDCM_DOMAIN ),
            self::GATEWAYS_VIEW   => __( 'Payment Methods', BPMJ_EDDCM_DOMAIN ),
        ];

        $this->active_view = $_GET['view'] ?? self::EARNINGS_VIEW;
    }

    public function render(): void
    {
        if ( ! User::getCurrent()->canViewReports() ) {
            _e( 'You do not have permission to access reports.', BPMJ_EDDCM_DOMAIN );
            return;
        }

        if ( self::EARNINGS_VIEW === $this->active_view ) {
            $this->make_graphing();
            echo View::get_admin('/reports/reports', [
                'graphing' => $this->graphing,
                'active_view' => $this->active_view,
                'reports_views' => $this->reports_views,
            ]);
        } else if ( self::GATEWAYS_VIEW === $this->active_view ) {
            $downloads_table = new Gateways_Table();
            $downloads_table->prepare_items();
            $downloads_table->display();
        }
    }

    protected function make_graphing()
    {
        // Retrieve the queried dates
        $this->graphing['dates'] = edd_get_report_dates();

        // Determine graph options
        switch ( $this->graphing['dates']['range'] ) :
            case 'today' :
            case 'yesterday' :
                $day_by_day	= true;
                break;
            case 'last_year' :
            case 'this_year' :
            case 'last_quarter' :
            case 'this_quarter' :
                $day_by_day = false;
                break;
            case 'other' :
                if( $this->graphing['dates']['m_end'] - $this->graphing['dates']['m_start'] >= 2 || ( $this->graphing['dates']['year_end'] > $this->graphing['dates']['year'] && ( $this->graphing['dates']['m_start'] - $this->graphing['dates']['m_end'] ) != 11 ) ) {
                    $day_by_day = false;
                } else {
                    $day_by_day = true;
                }
                break;
            default:
                $day_by_day = true;
                break;
        endswitch;

        $this->graphing['earnings_totals'] = 0.00; // Total earnings for time period shown
        $this->graphing['sales_totals']    = 0;    // Total sales for time period shown

        $this->graphing['include_taxes'] = true;
        $earnings_data = array();
        $sales_data    = array();

        if( $this->graphing['dates']['range'] == 'today' || $this->graphing['dates']['range'] == 'yesterday' ) {
            // Hour by hour
            $hour  = 1;
            $month = $this->graphing['dates']['m_start'];
            while ( $hour <= 23 ) {

                $sales    = edd_get_sales_by_date( $this->graphing['dates']['day'], $month, $this->graphing['dates']['year'], $hour );
                $earnings = edd_get_earnings_by_date( $this->graphing['dates']['day'], $month, $this->graphing['dates']['year'], $hour, $this->graphing['include_taxes'] );

                $this->graphing['sales_totals']    += $sales;
                $this->graphing['earnings_totals'] += $earnings;

                $date            = mktime( $hour, 0, 0, $month, $this->graphing['dates']['day'], $this->graphing['dates']['year'] ) * 1000;
                $sales_data[]    = array( $date, $sales );
                $earnings_data[] = array( $date, $earnings );

                $hour++;
            }

        } elseif ( $this->graphing['dates']['range'] == 'this_week' || $this->graphing['dates']['range'] == 'last_week' ) {

            $num_of_days = cal_days_in_month( CAL_GREGORIAN, $this->graphing['dates']['m_start'], $this->graphing['dates']['year'] );

            $report_dates = array();
            $i = 0;
            while ( $i <= 6 ) {

                if ( ( $this->graphing['dates']['day'] + $i ) <= $num_of_days ) {
                    $report_dates[ $i ] = array(
                        'day'   => (string) $this->graphing['dates']['day'] + $i,
                        'month' => $this->graphing['dates']['m_start'],
                        'year'  => $this->graphing['dates']['year'],
                    );
                } else {
                    $report_dates[ $i ] = array(
                        'day'   => (string) $i,
                        'month' => $this->graphing['dates']['m_end'],
                        'year'  => $this->graphing['dates']['year_end'],
                    );
                }

                $i++;
            }

            foreach ( $report_dates as $report_date ) {
                $sales = edd_get_sales_by_date( $report_date['day'], $report_date['month'], $report_date['year'] );
                $this->graphing['sales_totals'] += $sales;

                $earnings        = edd_get_earnings_by_date( $report_date['day'], $report_date['month'], $report_date['year'] , null, $this->graphing['include_taxes'] );
                $this->graphing['earnings_totals'] += $earnings;

                $date            = mktime( 0, 0, 0,  $report_date['month'], $report_date['day'], $report_date['year']  ) * 1000;
                $sales_data[]    = array( $date, $sales );
                $earnings_data[] = array( $date, $earnings );
            }

        } else {

            $y = $this->graphing['dates']['year'];

            while( $y <= $this->graphing['dates']['year_end'] ) {

                $last_year = false;

                if( $this->graphing['dates']['year'] == $this->graphing['dates']['year_end'] ) {
                    $month_start = $this->graphing['dates']['m_start'];
                    $month_end   = $this->graphing['dates']['m_end'];
                    $last_year   = true;
                } elseif( $y == $this->graphing['dates']['year'] ) {
                    $month_start = $this->graphing['dates']['m_start'];
                    $month_end   = 12;
                } elseif ( $y == $this->graphing['dates']['year_end'] ) {
                    $month_start = 1;
                    $month_end   = $this->graphing['dates']['m_end'];
                } else {
                    $month_start = 1;
                    $month_end   = 12;
                }

                $i = $month_start;
                while ( $i <= $month_end ) {

                    if ( $day_by_day ) {

                        $d = $this->graphing['dates']['day'];

                        if( $i == $month_end ) {

                            $num_of_days = $this->graphing['dates']['day_end'];

                            if ( $month_start < $month_end ) {

                                $d = 1;

                            }

                        } else {

                            $num_of_days = cal_days_in_month( CAL_GREGORIAN, $i, $y );

                        }




                        while ( $d <= $num_of_days ) {

                            $sales = edd_get_sales_by_date( $d, $i, $y );
                            $this->graphing['sales_totals'] += $sales;

                            $earnings = edd_get_earnings_by_date( $d, $i, $y, null, $this->graphing['include_taxes'] );
                            $this->graphing['earnings_totals'] += $earnings;

                            $date = mktime( 0, 0, 0, $i, $d, $y ) * 1000;
                            $sales_data[] = array( $date, $sales );
                            $earnings_data[] = array( $date, $earnings );
                            $d++;

                        }

                    } else {

                        $sales = edd_get_sales_by_date( null, $i, $y );
                        $this->graphing['sales_totals'] += $sales;

                        $earnings = edd_get_earnings_by_date( null, $i, $y, null, $this->graphing['include_taxes'] );
                        $this->graphing['earnings_totals'] += $earnings;

                        if( $i == $month_end && $last_year ) {

                            $num_of_days = cal_days_in_month( CAL_GREGORIAN, $i, $y );

                        } else {

                            $num_of_days = 1;

                        }

                        $date = mktime( 0, 0, 0, $i, $num_of_days, $y ) * 1000;
                        $sales_data[] = array( $date, $sales );
                        $earnings_data[] = array( $date, $earnings );

                    }

                    $i++;

                }

                $y++;
            }

        }

        $this->graphing['data'] = array(
            __( 'Earnings', BPMJ_EDDCM_DOMAIN ) => $earnings_data,
            __( 'Sales', BPMJ_EDDCM_DOMAIN )    => $sales_data
        );
    }

    public function get_active_view(): string
    {
        return $this->active_view;
    }

    public function get_reports_views(): array
    {
        return $this->reports_views;
    }
}
