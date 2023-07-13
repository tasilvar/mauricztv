<?php

namespace bpmj\wpidea\admin\video;

use bpmj\wpidea\admin\video\settings\Videos_Settings;
use bpmj\wpidea\Caps;

class Upload_Limit_Changer
{
    public function __construct() {        
        add_filter('upload_size_limit', [$this, 'filter_site_upload_size_limit'], 20);
    }
    
    /**
     * Filter the upload size limit when Vimeo upload is enabled
     *
     * @param string $size Upload size limit (in bytes).
     * @return int (maybe) Filtered size limit.
     */
    public function filter_site_upload_size_limit($size) {
        if ($this->upload_limit_change_conditions_met()) return $this->get_max_upload_size();

        return $size;
    }

    protected function get_max_upload_size()
    {
        return $this->get_server_upload_limit();
    }

    protected function get_server_upload_limit()
    {
        $server_settings = [
            $this->server_upload_size_to_bytes(ini_get('upload_max_filesize')),
            $this->server_upload_size_to_bytes(ini_get('post_max_size'))
        ];

        return min($server_settings);
    }

    protected function server_upload_size_to_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            case 'g':
                $val = (int)$val * (1024 * 1024 * 1024); //1073741824
                break;
            case 'm':
                $val = (int)$val * (1024 * 1024); //1048576
                break;
            case 'k':
                $val = (int)$val * 1024;
                break;
        }
    
        return $val;
    }

    protected function upload_limit_change_conditions_met()
    {
        $user_can_manage_settings = current_user_can( Caps::CAP_MANAGE_SETTINGS );
        $vimeo_upload_is_enabled = Videos_Settings::is_vimeo_upload_enabled();

        return $user_can_manage_settings && $vimeo_upload_is_enabled;
    }
}