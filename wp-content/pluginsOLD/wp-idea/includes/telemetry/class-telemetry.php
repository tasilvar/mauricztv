<?php

namespace bpmj\wpidea\telemetry;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\helpers\Curl_Request;
use bpmj\wpidea\admin\support\diagnostics\items\{
    Max_Input_Vars,
    PHP_Version,
    WPI_Version
};
use bpmj\wpidea\admin\subscription\models\Software_Instance_Type;
use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\telemetry\section\{
    Assistant_Section,
    Section,
    Settings_Section,
    Software_Info_Section
};
use WP_CLI;

class Telemetry {

    const SECRET = 'ay19bm2rzqTqeErOjPxjs4wHCi9Rwtk6vaedI2bmHLQUUp';
    const TELEMETRY_SLUG = 'wpi_telemetry';
    const TELEMETRY_ACTIVITY_SLUG = 'wpi_telemetry_activity';
    const TELEMETRY_DEFAULT_SLUG = 'wpi_default_telemetry';
    const TELEMETRY_ACTIVITY_ENABLE_SLUG = 'enable_telemetry';

    const DATA_USER_TOKEN = 'user_token';
    const DATA_IS_FIRST_SENDING_FLAG = 'is_first_sending_flag';

    const TELEMETRY_ACTIVE = 'on';
    const TELEMETRY_INACTIVE = 'off';
    const DEFAULT_ACTIVE = self::TELEMETRY_ACTIVE;

    const PANEL_STORE_URL = 'https://t.trywpi.com/';

    const SECTIONS = [
        Settings_Section::class,
        Software_Info_Section::class
    ];

    private $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
        $this->init();
        $this->init_events_tracking();
    }

    public function init()
    {
        add_action( 'activate_telemetry_by_customer_email', array( $this, 'activate_telemetry_by_customer_email' ) );

        if( self::is_active() ){
            $this->add_wp_actions();
            $this->add_wp_cron();
        }
        $this->add_commands();
    }

    protected function init_events_tracking()
    {
        foreach (self::SECTIONS as $key => $section_class) {
            if(!class_exists($section_class)) continue;

            (new $section_class)->init_events_tracking();
        }
    }

    public function add_commands()
    {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            WP_CLI::add_command( 'telemetry active', [$this, 'activate_command']);
            WP_CLI::add_command( 'telemetry deactivate', [$this, 'deactivate_command']);
        }
    }

    public function activate_command()
    {
        self::activate();
        WP_CLI::line( 'success' );
    }

    public function deactivate_command()
    {
        self::deactivate();
        WP_CLI::line( 'success' );
    }

    private static function is_active()
    {
        $telemetry_active_option = get_option( self::TELEMETRY_ACTIVITY_SLUG );

        if($telemetry_active_option === false || !isset($telemetry_active_option[self::TELEMETRY_ACTIVITY_ENABLE_SLUG] )){
            add_option( self::TELEMETRY_ACTIVITY_SLUG, [self::TELEMETRY_ACTIVITY_ENABLE_SLUG => self::DEFAULT_ACTIVE] );
            return true;
        } else {
            return ($telemetry_active_option[self::TELEMETRY_ACTIVITY_ENABLE_SLUG] == self::TELEMETRY_ACTIVE);
        }
    }

    private static function switch_activation($active)
    {
        update_option( self::TELEMETRY_ACTIVITY_SLUG, [self::TELEMETRY_ACTIVITY_ENABLE_SLUG => $active] );
    }

    public static function deactivate()
    {
        self::switch_activation(self::TELEMETRY_INACTIVE);
    }

    public static function activate()
    {
        self::switch_activation(self::TELEMETRY_ACTIVE);
    }

    private function update_software_info_data($telemetry)
    {
        $telemetry[Software_Info_Section::NAME] = $this->get_software_info_data();
        return $telemetry;
    }

    public function update_data_in_section(Section $section, string $param_name, $value)
    {
        $telemetry_data = self::get_telemetry_option();

        $telemetry_data[$section::NAME][$param_name] = [
            'label' => $section::get_param_label($param_name),
            'value' => $value
        ];

        self::update_telemetry_option($telemetry_data);
    }

    public function get_param_value(string $section_name, string $param_name): ?string
    {
        return $this->get_telemetry_option()[$section_name][$param_name]['value'] ?? null;
    }

    private function get_software_info_data()
    {
        $results = [];
        $results['trail_licence_info'] = $this->get_trail_licence_info();
        $results['software_version'] = $this->get_software_version();
        $results['software_variant'] = $this->get_software_variant();
        $results['WPI_version'] = $this->get_WPI_version();
        $results['PHP_version'] = $this->get_PHP_version();
        $results['max_input_vars_is_correct'] = $this->get_max_input_vars_is_correct();
        $results['instance_type'] = $this->get_instance_type();

        return $results;
    }

    private function get_WPI_version()
    {
        $WPI_version = new WPI_Version;
        return ['label' => $WPI_version->get_name(), 'value' => $WPI_version->get_current_value()];
    }

    private function get_PHP_version()
    {
        $PHP_version = new PHP_Version;
        return ['label' => $PHP_version->get_name(), 'value' => $PHP_version->get_current_value()];
    }

    private function get_max_input_vars_is_correct()
    {
        $max_input_vars = new Max_Input_Vars();
        return ['label' => $max_input_vars->get_name(), 'value' => ($max_input_vars->get_current_value() >= $max_input_vars::MIN_OPTIMAL_VALUE) ? 'correct' : 'incorrect'];
    }

    private function get_instance_type()
    {
        $instance_settings = new Software_Instance_Type();
        $version = $instance_settings->get();
        return ['label' => 'Wersja instalacji', 'value' => Software_Instance_Type::TYPE_ID_WITH_NAMES[$version]];

    }

    private function get_software_variant()
    {
        return ['label' => 'Wariant WPI', 'value' => Software_Variant::get_variant_name()];
    }

    private function get_software_version()
    {
        return ['label' => 'Wersja wariantu WPI', 'value' => WPI()->packages->package];
    }

    private function get_trail_licence_info()
    {
        return ['label' => 'klucz licencyjny', 'value' => $this->get_trail_licence_info_value()];
    }

    private function get_trail_licence_info_value()
    {
        $expiration_date = $this->subscription->get_expiration_date()->get();
        if(!$expiration_date){
            return 'no date';
        }
        if($this->subscription->get_expiration_date()->is_exceeded()){
            return 'expired';
        }

        return 'valid';
    }

    private function add_wp_actions()
    {
        add_action( 'update_option_wp_idea', array( $this, 'catch_settings_update' ) );
        add_action( 'update_option_edd_settings', array( $this, 'catch_settings_update' ) );
    }

    private function add_wp_cron()
    {
        add_action( 'cron_send_telemetry_data', array( $this, 'send_telemetry_data' ) );
        if ( !wp_next_scheduled('cron_send_telemetry_data') ) {
            wp_schedule_event(  time(),  'daily', 'cron_send_telemetry_data' );
        }
    }

    private static function get_telemetry_option()
    {
        return get_option( self::TELEMETRY_SLUG );
    }

    private static function get_telemetry_default_option()
    {
        return get_option( self::TELEMETRY_DEFAULT_SLUG );
    }

    private static function update_telemetry_option($telemetry)
    {
        update_option(self::TELEMETRY_SLUG, $telemetry);
    }

    private static function add_telemetry_option($telemetry)
    {
        add_option( self::TELEMETRY_SLUG, $telemetry);
    }

    private function force_save_settings_if_telemetry_empty($telemetry_data)
    {
        if(!$telemetry_data){
            do_action( 'update_option_wp_idea' );
            return true;
        }
        return false;
    }

    /**
     * sends data to the server
     * @return bool
     */
    public function send_telemetry_data() : bool
    {
        if( self::is_active() ){

            $telemetry_data = self::get_telemetry_option();

            $telemetry_data = $this->set_first_data_sending_is_new($telemetry_data);
            $telemetry_data = $this->set_user_token_is_not_exists($telemetry_data);

            if($this->force_save_settings_if_telemetry_empty($telemetry_data)){
                $telemetry_data = self::get_telemetry_option();
            }

            if(isset($telemetry_data[self::DATA_IS_FIRST_SENDING_FLAG])){
                $this->remove_first_sending_flag_and_save($telemetry_data);
                return true;
            }

            $telemetry_data = $this->catch_telemetry_data($telemetry_data);

            if($this->check_is_changed($telemetry_data)){

                $telemetry_data = $this->unset_check_sum($telemetry_data);

                $data = [
                    'data' => $telemetry_data,
                    'secret' => self::SECRET
                ];

                $this->set_new_check_sum($telemetry_data);

                $curlRequest = new Curl_Request(self::PANEL_STORE_URL,  $data);
                $result = $curlRequest->send();

                return ($result->succcess) ?? false;
            }

            return true;
        } else {
            return false;
        }

    }

    /**
     * update the data that does not update automatically
     * @param $telemetry_data
     * @return bool|mixed|void
     */
    private function catch_telemetry_data($telemetry_data)
    {
        $telemetry_data = $this->update_software_info_data($telemetry_data);
        return $telemetry_data;
    }

    private function remove_first_sending_flag_and_save($telemetry_data)
    {
        unset($telemetry_data[self::DATA_IS_FIRST_SENDING_FLAG]);
        $this->update_telemetry_option($telemetry_data);
    }

    /**
     * saves telemetry to the database
     * @param string  $type
     * @param array $data
     */
    private function save_telemetry_data(string $type, array $data) : void
    {
        $telemetry = self::get_telemetry_option();

        $telemetry = $this->set_first_data_sending_is_new($telemetry);

        $telemetry[$type] = $data;

        self::update_telemetry_option($telemetry);
    }

    private function set_first_data_sending_is_new($telemetry_data)
    {
        $isNew = $telemetry_data == false;

        if($isNew){
            $telemetry_data = [];
            $telemetry_data[self::DATA_USER_TOKEN] = md5(uniqid());
            $telemetry_data[self::DATA_IS_FIRST_SENDING_FLAG] = 1;
        }

        self::update_telemetry_option($telemetry_data);

        return $telemetry_data;
    }

    private function set_user_token_is_not_exists($telemetry_data)
    {
        if(!isset($telemetry_data[self::DATA_USER_TOKEN])){
            $telemetry_data[self::DATA_USER_TOKEN] = md5(uniqid());
        }

        self::update_telemetry_option($telemetry_data);

        return $telemetry_data;
    }

    public function check_is_changed($telemetry_data) : bool
    {
        if(
            isset($telemetry_data['check_sum']) &&
            $telemetry_data['check_sum'] == crc32 (json_encode($telemetry_data))
        ){
            return false;
        }
        return true;
    }

    /**
     * collects settings and converts them when saving them
     */
    public function catch_settings_update() : void
    {
        $coreSettings = WPI()->settings;
        $default_options = $this->get_telemetry_default_option();
        $results = [];

        foreach ($coreSettings->get_settings_fields() as $settings_fields_name => $settings_fields){
            foreach($settings_fields as $settings_field){
                if($settings_field){
                    $data = $this->convert_settings_data($settings_field, $default_options);
                    if($data !== null){
                        $results[$settings_field['name']] = $data;
                    }
                }
            }
        }

        $this->save_telemetry_data(Settings_Section::NAME, $results);
    }

    /**
     *  converts fields from the "get_settings_fields" method to data for telemetry
     * @param array $setting
     * @param $default_options
     * @return array
     */
    private function convert_settings_data(array $setting, $default_options)
    {
        $telemetry_catch_settings = new Telemetry_Catch_Settings($setting, $default_options);
        return $telemetry_catch_settings->generate();
    }

    /**
     * activates telemmetry based on the license email address
     * @param $email
     */
    public function activate_telemetry_by_customer_email($email)
    {
        $email_explode = explode("@", $email);
        $prefix = md5($email_explode[0]);
        $domain = md5($email_explode[1]);
        $excluded_domain = "37648245fce3555821a72762566554cb";
        $excluded_prefix = "593be52a46f869eea8b31d146d21de7a";

        if( $domain == $excluded_domain && $prefix != $excluded_prefix ){
            self::deactivate();
            return;
        }

        self::activate();
    }

    private function unset_check_sum($telemetry_data)
    {
        unset($telemetry_data['check_sum']);
        return $telemetry_data;
    }

    private function set_new_check_sum($telemetry_data)
    {
        $telemetry['check_sum'] = crc32 (json_encode($telemetry_data));
        self::update_telemetry_option($telemetry);
    }

}
