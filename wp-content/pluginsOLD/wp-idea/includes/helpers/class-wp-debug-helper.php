<?php namespace bpmj\wpidea\helpers;

use bpmj\wpidea\options\Interface_Options;

class Wp_Debug_Helper implements Interface_Debug_Helper
{
    private const DEV_MODE_OPTION_NAME = 'bpmj_eddcm_dev';
    
    private $options;

    public function __construct(Interface_Options $options)
    {
        $this->options = $options;
    }

    public function is_dev_mode_enabled(): bool
    {
        return $this->options->get(self::DEV_MODE_OPTION_NAME) === '1';
    }

    public function in_debug_mode(): bool
    {
        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            return true;
        }

        return false;
    }
}
