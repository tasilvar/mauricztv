<?php

/**
 * Usefull functions.
 */

// Exit if accessed directly
namespace bpmj\wpidea;

use bpmj\wpidea\options\Interface_Options;

if (!defined('ABSPATH')) {
    exit;
}

class Helper
{
    private const DEV_MODE_OPTION_NAME = 'bpmj_eddcm_dev';

    private $options;

    public function __construct(
        Interface_Options $options
    ) {
        $this->options = $options;
    }

    public function is_dev_mode_enabled(): bool
    {
        return $this->options->get(self::DEV_MODE_OPTION_NAME) === '1';
    }

    public static function is_dev()
    {
        return get_option(self::DEV_MODE_OPTION_NAME);
    }

    public static function turn_on_dev()
    {
        return update_option(self::DEV_MODE_OPTION_NAME, true);
    }

    public static function turn_off_dev()
    {
        return update_option(self::DEV_MODE_OPTION_NAME, false);
    }

    public static function is_voucher_page()
    {
        global $post;

        if (empty($post)) return false;
        $wpidea_settings = get_option('wp_idea');

        if (empty($wpidea_settings['voucher_page'])) return false;

        $post_id = $post->ID;
        $voucher_page_id = (int)$wpidea_settings['voucher_page'];

        return $post_id === $voucher_page_id;
    }
}
