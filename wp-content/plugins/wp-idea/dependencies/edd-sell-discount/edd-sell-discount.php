<?php

/*
Plugin Name: EDD Sell Discount
Description: Plugin pozwalający sprzedawać kody rabatowe
Author: upSell.pl & Better Profits
Author URI: http://upsell.pl
Version: 0.9.3
*/

if (!defined('ABSPATH'))
    exit;


class BPMJ_EDD_Sell_Discount
{

    /**
     * Class instance variable
     * @var $instance
     */
    private static $instance;


    /**
     * Function returns created instance of class
     * @return BPMJ_EDD_Sell_Discount
     */
    public static function instance()
    {
        if (!isset(self::$instance) && !(self::$instance instanceof BPMJ_EDD_Sell_Discount)) {
            self::$instance = new BPMJ_EDD_Sell_Discount;
        }

        return self::$instance;
    }


    /**
     * BPMJ_EDD_Sell_Discount constructor.
     */
    public function __construct()
    {
        $this->constants();
        $this->includes();
        $this->version = BPMJ_EDD_SELL_DISCOUNT_VERSION;
    }


    /*
     * Setup plugin constants
     */
    private function constants()
    {
        $this->define('BPMJ_EDD_SELL_DISCOUNT_VERSION', '0.9.3');
        $this->define('BPMJ_EDD_SELL_DISCOUNT_NAME', 'EDD Sell Discount');
        $this->define('BPMJ_EDD_SELL_DISCOUNT_DIR', plugin_dir_path(__FILE__));
        $this->define('BPMJ_EDD_SELL_DISCOUNT_URL', plugin_dir_url(__FILE__));
        $this->define('BPMJ_EDD_SELL_DISCOUNT_FILE', __FILE__);
        $this->define('BPMJ_EDD_SELL_DISCOUNT_DOMAIN', 'edd-sell-discount');
    }


    /*
     * Define constant if not already set
     * @param  string $name
     * @param  string|bool $value
     */
    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }


    /**
     * Include all files
     */
    public function includes()
    {
        require_once BPMJ_EDD_SELL_DISCOUNT_DIR . 'includes/class.new-order.php';
        require_once BPMJ_EDD_SELL_DISCOUNT_DIR . 'admin/class.custom-email-tags.php';

        if (is_admin()) {
            require_once BPMJ_EDD_SELL_DISCOUNT_DIR . 'admin/class.product-metabox.php';
        }
    }


    /**
     * Log important things
     * @param $content
     */
    public function log($content)
    {
        date_default_timezone_set('Europe/Warsaw');
        $file_name = 'logs.txt';

        $log = date('H:i:s [m.d.Y]') . PHP_EOL;
        $log .= $content . PHP_EOL;
        $log .= '--------' . PHP_EOL;

        file_put_contents(BPMJ_EDD_SELL_DISCOUNT_DIR . '/' . $file_name, $log, FILE_APPEND);
    }


    /**
     * Check if a string is serialized
     * @param string $string
     */
    public static function is_serial($string)
    {
        return (@unserialize($string) !== false);
    }
}

function EDD_Sell_Discount()
{
    return BPMJ_EDD_Sell_Discount::instance();
}

EDD_Sell_Discount();