<?php

namespace bpmj\wpidea\telemetry;

class Telemetry_Catch_Settings {

    const DEFAULT_EMPTY_VALUE = 'no_value';
    const DEFAULT_VALUE = '_DEFAULT';

    const TYPE_DEFAULT = 'text';
    const SAVE_TO_DEFAULT = 'wp_idea';

    private $default_options;
    private $type;
    private $name;
    private $label;
    private $default;
    private $save_to;
    private $options;
    private $groups_type;

    private $helper_check_substring_default = false;

    public function __construct($setting, $default_options)
    {

        $this->default_options = $default_options;
        $this->name = $setting['name'] ?? null;
        $this->type = $setting['type'] ?? self::TYPE_DEFAULT;
        $this->label = $setting['label'] ?? null;
        $this->save_to = $setting['save_to'] ?? self::SAVE_TO_DEFAULT;
        $this->default = $setting['default'] ?? self::DEFAULT_EMPTY_VALUE;
        $this->options = $setting['options'] ?? null;
        $this->groups_type = $setting['groups_type'] ?? null;
    }

    public function generate()
    {
        if($this->check_is_default()){
            return ['label' => $this->label, 'value' => self::DEFAULT_VALUE];

        } elseif($value = $this->get_value_by_standard_input()) {

            return ['label' => $this->label, 'value' => $value];
        } else {
            return $this->get_value_by_not_standard_input();
        }
    }

    private function get_data_by_name()
    {
        return $_POST[$this->save_to][$this->name] ?? null;
    }

    private function get_data_by_type()
    {
        return $_POST[$this->save_to][$this->type] ?? null;
    }

    public function get_value_by_not_standard_input()
    {
        $data = $this->get_data_by_name();
        $result = null;

        switch ($this->type) {
            case 'gateways':
                $result[] = [
                    'label' => 'Domyslny sposób płatności',
                    'value' => $_POST[$this->save_to]['default_gateway'] ?? 'no default'
                ];
                break;
            case 'date_offset':
                if (isset($data) && is_array($data)) {
                    foreach ($data as $key => $value) {
                        $result[] = $this->get_date_offset_result($key, $value);
                    }
                }
                break;
            case 'renewal_times':
                $start = $_POST[$this->save_to]["bpmj_renewals_start"] ?? null;
                $end = $_POST[$this->save_to]["bpmj_renewals_end"] ?? null;
                $result[] = $this->get_renewal_times_start_result($start);
                $result[] = $this->get_renewal_times_end_result($end);
                break;
            case 'renewal':
                $renewal_value = $_POST[$this->save_to]["bpmj_renewal_discount_value"] ?? null;
                $renewal_type = $_POST[$this->save_to]["bpmj_renewal_discount_type"] ?? null;
                $result[] = $this->get_renewal_value_result($renewal_value);
                $result[] = $this->get_renewal_discount_type_result($renewal_type);
                break;
            case 'bpmj_groups':
                $result = $this->get_bpmj_groups();
                break;
        }

        return $result;
    }

    private function get_renewal_times_start_result($start)
    {
        if($start == '14'){
            return [
                'label' => $this->label . ' value',
                'value' => self::DEFAULT_VALUE
            ];
        } else{
            return [
                'label' => $this->label . ' value',
                'value' => (string)$start
            ];
        }
    }

    private function get_renewal_discount_type_result($renewal_type)
    {
        if($renewal_type == 'percent'){
            return [
                'label' => $this->label . ' type',
                'value' => self::DEFAULT_VALUE
            ];
        } else{
            return [
                'label' => $this->label . ' type',
                'value' => (string)$renewal_type
            ];
        }
    }

    private function get_renewal_value_result($renewal_value)
    {
        if($renewal_value == ''){
            return [
                'label' => $this->label . ' value',
                'value' => self::DEFAULT_VALUE
            ];
        } else{
            return [
                'label' => $this->label . ' value',
                'value' => (string)$renewal_value
            ];
        }
    }

    private function get_renewal_times_end_result($end)
    {
        if($end == '19'){
            return [
                'label' => $this->label . ' value',
                'value' => self::DEFAULT_VALUE
            ];
        } else{
            return [
                'label' => $this->label . ' value',
                'value' => (string)$end
            ];
        }
    }

    private function get_date_offset_result($key, $value)
    {
        if($key == 'number' && (string)$value == '1'){
            return [
                'label' => $this->label . ' ' . $key,
                'value' => self::DEFAULT_VALUE
            ];
        } elseif($key == 'unit' && (string)$value == 'days'){
            return [
                'label' => $this->label . ' ' . $key,
                'value' => self::DEFAULT_VALUE
            ];
        } else {
            return [
                'label' => $this->label . ' ' . $key,
                'value' => (string)$value
            ];
        }
    }

    private function get_value_by_standard_input()
    {
        $data = $this->get_data_by_name();

        switch ($this->type){
            case 'file':
            case 'text':
            case 'wysiwyg':
            case 'textarea':
            case 'html':
                return ($this->get_data_by_name() == '') ? 'unfilled' : 'filled';
                break;
            case 'select':
            case 'radio_with_other':
            case 'radio':
                return ($this->get_data_by_name() == '') ? 'empty' : $data;
                break;
            case 'checkbox':
            case 'renewal_discount':
            case 'checkbox_one_empty':
                return ($this->get_data_by_name() == 'on' || $this->get_data_by_name() == '1') ? 'checked' : 'unchecked';
                break;
            case 'checkbox_yes_no':
                return ($this->get_data_by_name() == 'no') ? 'checked' : 'unchecked';
                break;
            case 'override_all':
                return $this->get_data_by_type();
                break;
        }
    }

    private function check_option_default()
    {
        if(!$this->default_options){
            return false;
        }

        foreach ($this->default_options as $default_option_name => $default_option_value){
            if(
                $this->name == $default_option_name &&
                $this->get_data_by_name() == $default_option_value
            ){
                return true;
            }
        }

        return false;
    }

    private function check_is_default() : bool
    {
        $default = null;

        if($this->check_option_default()){
            return true;
        }

        if($this->default != self::DEFAULT_EMPTY_VALUE){
            $is_default_by_form = $this->compare_default_with_data($this->default);
            if($is_default_by_form){
                return $this->compare_default_with_data($this->default);
            }
        }

        $default = $this->get_nonstandard_default();
        if($default != null){
            return $this->compare_default_with_data($default);
        }

        return $this->check_standard_default();
    }

    private function compare_default_with_data($default) : bool
    {
        $convertedDefault = htmlspecialchars(sanitize_text_field($default));
        $convertedData = htmlspecialchars(sanitize_text_field($this->get_data_by_name()));

        if($convertedData == $convertedDefault){
            return true;
        } elseif (
            $this->helper_check_substring_default &&
            !empty($convertedData) &&
            !empty($convertedDefault) &&
            strpos($convertedData, $convertedDefault) !== false
        ) {

            return true;
        }

        return false;
    }

    private function check_standard_default() : bool
    {
        $standard_inputs_empty_string = ['file', 'text', 'wysiwyg','textarea','html','select','radio', 'checkbox_one_empty'];
        $standard_inputs_empty_string_or_off = ['checkbox'];

        if (in_array($this->type, $standard_inputs_empty_string)) {
            return ($this->get_data_by_name() == '');
        } elseif (in_array($this->type, $standard_inputs_empty_string_or_off)) {
            return ($this->get_data_by_name() == '' || $this->get_data_by_name() == 'off');
        } else {
            return false;
        }
    }

    private function get_nonstandard_default()
    {
        $default = null;

        foreach (self::nonstandard_default_array() as $nonstandard_default_value){
            if (in_array($this->name, $nonstandard_default_value['names'])) {
                if(isset($nonstandard_default_value['helper_check_substring_default'])){
                    $this->helper_check_substring_default = true;
                }

                $default = $nonstandard_default_value['default'];
            }
        }

        return $default;
    }

    private function get_bpmj_groups()
    {
        $result = null;
        foreach ($this->options as $option_key => $option){

            $converted_group = $this->convert_by_groups_type($this->groups_type, $option_key, $option);
            if($converted_group){
                $result[] = $converted_group;
            }

            if(isset($option['settings'])){
                foreach ($option['settings'] as $option_settings_key => $option_setting){
                    $settings_name = $this->name;
                    $option_name = $option['name'] ?? $option_key;
                    $option_settings_name = $option_setting['label'] ?? $option_setting['name'] ?? null;
                    $label = $settings_name.' || '.$option_name .' || ' .$option_settings_name;

                    $settings = $option_setting;
                    $settings['name'] = $option_settings_key ?? null;
                    $settings['label'] = $label;

                    $telemetry_catch_settings = new Telemetry_Catch_Settings($settings, $this->default_options);
                    $generate_result = $telemetry_catch_settings->generate();

                    if($generate_result ){
                        $result[] = $generate_result;
                    }
                }
            }
        }
        return $result;
    }

    private function convert_by_groups_type($group_type, $option_key, $option)
    {
        switch ($group_type){
            case 'gateways':
                $option_key = str_replace("edd-", "", $option_key);
                return [
                    'label' => 'Sposób płatności '.$option['name'],
                    'value' => isset($_POST[$option['save_to']]['gateways'][$option_key]) ? 'activated' : self::DEFAULT_VALUE
                ];
                break;
            case 'invoices':
                return [
                    'label' => 'Integracje faktury '.$option['name'],
                    'value' => isset($_POST["wp_idea"]['integrations'][$option_key]) ? 'activated' : self::DEFAULT_VALUE
                ];
                break;
            case 'mailers':
                return [
                    'label' => 'Integracje email '.$option['name'],
                    'value' => isset($_POST["wp_idea"]['integrations'][$option_key]) ? 'activated' : self::DEFAULT_VALUE
                ];
                break;
        }

    }

    private static function nonstandard_default_array()
    {
        return [
            [
                'names' => ['tpay_id'],
                'default' => '1010'
            ],
            [
                'names' => ['tpay_pin'],
                'default' => 'demo'
            ],
            [
                'names' => ['override_all','enable_responsive_videos','scarlet_cart_secure_payments_cb'],
                'default' => 'on'
            ],
            [
                'names' => ['edd_id_person'],
                'default' => 1
            ],
            [
                'names' => ['edd_id_hide_fname','test_mode','edd_id_force','edd_id_disable_taxid_verification','edd_id_hide_lname','bpmj_renewal_discount', 'edd_id_enable_vat_moss'],
                'default' => '-1'
            ],
            [
                'names' => ['decimal_separator'],
                'default' => '.'
            ],
            [
                'names' => ['scarlet_cart_additional_info_1_desc'],
                'default' => ''
            ],
            [
                'names' => ['currency'],
                'default' => 'PLN'
            ],
            [
                'names' => ['agree_label'],
                'default' => 'Akceptuję <a href="/regulamin/" target="_blank">regulamin zakupów</a> (konieczne do złożenia zamówienia)',
                'helper_check_substring_default' => true
            ],
            [
                'names' => ['bpmj_renewal_discount_time'],
                'default' => '+1day'
            ],
            [
                'names' => ['invoice_type'],
                'default' => 'faktura-vat'
            ],
            [
                'names' => ['from_name'],
                'default' => 'WP Idea'
            ],
            [
                'names' => ['purchase_subject','purchase_heading'],
                'default' => 'Potwierdzenie zamówienia',
                'helper_check_substring_default' => true
            ],
            [
                'names' => ['purchase_receipt'],
                'default' => 'Dzień dobry {name}, Dziękujemy za opłacenie Twojego zamówienia. Poniżej znajdziesz listę zakupionych produktów: {download_list} {sitename}',
                'helper_check_substring_default' => true
            ],
            [
                'names' => ['bpmj_edd_arc_subject'],
                'default' => 'Twoje dane logowania do WP Idea',
                'helper_check_substring_default' => true
            ],
            [
                'names' => ['bpmj_edd_arc_content'],
                'default' => 'Dzień dobry {firstname}, Twoje zamówienie zostało przyjęte. Utworzyliśmy dla Ciebie konto na platformie WP Idea. Oto Twoje dane logowania: Login: {login} Hasło: {password} -- Wiadomość wygenerowana automatycznie.',
                'helper_check_substring_default' => true
            ],
            [
                'names' => ['certificate_template'],
                'default' => 'Certyfikat Kurs: {course_name} - {course_price} Kursant: {student_name} Data: {certificate_date}'
            ],
            [
                'names' => ['footer_html'],
                'default' => 'Copyright © 2020 WP Idea. Szkolenia napędza platforma',
                'helper_check_substring_default' => true
            ],
            [
                'names' => ['scarlet_cart_additional_info_1_desc'],
                'default' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo'
            ],
            [
                'names' => ['scarlet_cart_additional_info_1_title'],
                'default' => 'Gwarancja satysfakcji'
            ],
            [
                'names' => ['scarlet_cart_additional_info_2_title'],
                'default' => 'Bezpieczne połączenie'
            ],
            [
                'names' => ['scarlet_cart_additional_info_2_desc'],
                'default' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo'
            ],
            [
                'names' => ['page_to_redirect_to_after_login'],
                'default' => '15'
            ],
            [
                'names' => ['profile_editor_page'],
                'default' => '16'
            ],
        ];
    }

}
