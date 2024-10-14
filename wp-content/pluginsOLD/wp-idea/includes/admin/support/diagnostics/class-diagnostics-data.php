<?php

namespace bpmj\wpidea\admin\support\diagnostics;

use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\Software_Variant;

class Diagnostics_Data
{
    private $data = [];

    protected function load_data()
    {
        foreach (WPI()->support->get_diagnostics()->get_items() as $key => $item) {
            $this->data[] = new Diagnostic_Data_Item(
                $item->get_name(),
                $item->get_current_value() . " ({$item->get_status()})"
            );
        }
        
        $this->data[] = new Diagnostic_Data_Item(
            __( 'WPI Package', BPMJ_EDDCM_DOMAIN ),
            $this->get_diagnostic_item_wpi_package()
        );

        $this->data[] = new Diagnostic_Data_Item(
            __( 'WPI Variant', BPMJ_EDDCM_DOMAIN ),
            $this->get_diagnostic_item_wpi_variant()
        );

        $this->data[] = new Diagnostic_Data_Item(
            __( 'WPI Theme', BPMJ_EDDCM_DOMAIN ),
            $this->get_diagnostic_item_wpi_theme()
        );

        $this->data[] = new Diagnostic_Data_Item(
            __( 'WP Active Plugins', BPMJ_EDDCM_DOMAIN ),
            $this->get_diagnostic_item_wp_active_plugins()
        );

        $this->data[] = new Diagnostic_Data_Item(
            __( 'WP Version', BPMJ_EDDCM_DOMAIN ),
            $this->get_diagnostic_item_wp_version()
        );
    }

    public function get_all()
    {
        if ( empty( $this->data ) )
            $this->load_data();

        return $this->data;
    }

    public function get_diagnostic_item_wpi_package()
    {
        return WPI()->packages->get_package();
    }

    public function get_diagnostic_item_wpi_variant()
    {
        return Software_Variant::get_variant_name();
    }

    public function get_diagnostic_item_wpi_theme()
    {
        return LMS_Settings::get_option('template' );
    }

    public function get_diagnostic_item_wp_active_plugins()
    {
        $all_plugins = get_plugins();
        $active_plugins = [];
        foreach ( get_option('active_plugins') as $active_plugin )
            $active_plugins[] = $all_plugins[ $active_plugin ]['Name'];

        return implode( ', ', $active_plugins );
    }

    public function get_diagnostic_item_wp_version()
    {
        return get_bloginfo('version');
    }
}