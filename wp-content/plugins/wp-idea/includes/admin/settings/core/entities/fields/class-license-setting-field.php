<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\helpers\Translator_Static_Helper;

class License_Setting_Field extends Text_Setting_Field
{
    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        return $this->get_field_wrapper_start() .
            "<input
                    type='text'
                    name='" . $this->get_name() . "'
                    id='" . $this->get_name() . "'
                    value='" . $this->get_value() . "'
                    data-initial-value='" . $this->get_value() . "'
                    class='single-field'
                    " . $this->get_disabled_html_attr() . "
            />" . $this->get_field_wrapper_end();
    }


    public function get_license_field_additional_html()
    {
        $html = '';

        $license_data['license'] = get_option('bpmj_eddcm_license_status');
        $license_data['expires'] = get_option('bpmj_wpidea_license_expires');
        $license_data['error'] = get_option('bpmj_wpidea_license_connection_error');

        if ($license_data['error']) {
            $html .= '<span class="' . $this->prefix . '" style="color: #800;">' . __('Error while connecting to the license server!',
                    BPMJ_EDDCM_DOMAIN) . '</span>';
        }

        if ('valid' === $license_data['license']) {
            $html .= '<span class="' . $this->prefix . 'description-field' . '" style="color: #080;">';
        } else {
            $html .= '<span class="' . $this->prefix . 'description-field' . '" style="color: #800;">';
        }

        switch ($license_data['license']) {
            case 'site_inactive':
            case 'inactive':
                $license_status_translated = __('inactive', BPMJ_EDDCM_DOMAIN);
                break;
            case 'valid':
                $license_status_translated = __('active', BPMJ_EDDCM_DOMAIN);
                break;
            case 'expired':
                $license_status_translated = __('expired', BPMJ_EDDCM_DOMAIN);
                if ('lifetime' === $license_data['expires']) {
                    $license_data['expires'] = '';
                }
                break;
            default:
                $license_data['license'] = 'inactive';
                $license_status_translated = __('n/a', BPMJ_EDDCM_DOMAIN);
                $license_data['expires'] = __('n/a', BPMJ_EDDCM_DOMAIN);
                break;
        }

        if (empty($license_data['expires'])) {
            $license_data['expires'] = __('n/a', BPMJ_EDDCM_DOMAIN);
        }

        $html .= sprintf(__('License status: %1$s. Expiration date: %2$s.', BPMJ_EDDCM_DOMAIN),
            '<strong>' . $license_status_translated . '</strong>', substr($license_data['expires'], 0, 10));
        $html .= '</span>';

        return $html;
    }

    protected function get_field_wrapper_end(bool $hidden_save_fields = false): string
    {
        return "<span id='license_status'>".$this->get_license_field_additional_html()."</span>
            <span class='hint'>" . $this->get_description() . "</span>
            <span class='validation-errors' style='display: none;'></span>
        </div>

        <div class='field-buttons'>
            <button class='single-field-save-button' data-related-field='" . $this->get_name() . "' style='display: none;'>
                " . Translator_Static_Helper::translate('settings.field.button.save') . "
            </button>
            <button class='single-field-cancel-button' data-related-field='" . $this->get_name() . "' style='display: none;'>
                " . Translator_Static_Helper::translate('settings.field.button.cancel') . "
            </button>
        
            <span class='saving' style='display: none;'>" . Translator_Static_Helper::translate('settings.field.button.saving') . "</span>
            <span class='saved' style='display: none;'>" . Translator_Static_Helper::translate('settings.field.button.saved') . "</span>
        </div>
    </div>";
    }
}