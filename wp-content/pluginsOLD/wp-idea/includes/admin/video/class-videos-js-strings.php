<?php

namespace bpmj\wpidea\admin\video;


class Videos_Js_Strings {

    private const BPMJ_WPI_VIDEOS_I18N_JS_VAR = 'BPMJ_WPI_VIDEOS_I18N';
    
    public function __construct() {
        add_action( 'admin_print_footer_scripts', array( $this, 'print_transalation_strings_as_js_variable') );
    }

    public function print_transalation_strings_as_js_variable(): void
    {
        echo "<script>var " . self::BPMJ_WPI_VIDEOS_I18N_JS_VAR . "=" . $this->_get_json_translations() . "</script>";
    }

    private function _get_json_translations(): string
    {
        return json_encode($this->_get_translations()) ?: '[]';
    }

    private function _get_translations(): array
    {
        return [
            'upload_to_vimeo' => __('Upload to Vimeo', BPMJ_EDDCM_DOMAIN),
            'upload_complete' => __('Upload to Vimeo complete', BPMJ_EDDCM_DOMAIN),
            'upload_in_progress' => __('Upload to Vimeo in progress', BPMJ_EDDCM_DOMAIN)
        ];
    }

}